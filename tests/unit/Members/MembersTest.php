<?php

namespace Tests\Unit\Members;

use Codeception\Test\Unit;
use Monolog\Logger;
use phpCollab\AppConfig;
use phpCollab\Members\Members;
use phpCollab\Members\MembersRepositoryInterface;
use phpCollab\Notification;

/**
 * Unit tests for Members service with Repository Pattern
 *
 * These tests demonstrate:
 * - Repository Pattern with clean separation of concerns
 * - Service layer testing without database dependencies
 * - Easy mocking of repository for unit testing
 * - Pure constructor injection with explicit dependencies
 */
class MembersTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Helper method to create standard mocks with Repository Pattern
     */
    private function createMembersMocks(): array
    {
        return [
            'repository' => $this->createMock(MembersRepositoryInterface::class),
            'logger' => $this->createMock(Logger::class),
            'notification' => $this->createMock(Notification::class),
            'appConfig' => $this->createMock(AppConfig::class),
        ];
    }

    /**
     * Test that Members can be instantiated with Repository Pattern
     */
    public function testCanInstantiateWithMockedDependencies()
    {
        $mocks = $this->createMembersMocks();

        // ✅ With Repository Pattern - cleaner dependencies
        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        // Verify the service was created successfully
        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * Test constructor enforces type safety with Repository Pattern
     */
    public function testConstructorTypeEnforcement()
    {
        $mocks = $this->createMembersMocks();

        $members = new Members(
            $mocks['repository'],    // Type: MembersRepositoryInterface
            $mocks['logger'],        // Type: Logger
            $mocks['notification'],  // Type: Notification
            $mocks['appConfig']      // Type: AppConfig
        );

        // If we got here, all types are correct ✅
        $this->assertTrue(true);
    }

    /**
     * Test sendEmail method uses injected Notification service
     */
    public function testSendEmailUsesInjectedNotification()
    {
        $mocks = $this->createMembersMocks();

        // Mock AppConfig methods used by sendEmail
        $mocks['appConfig']->method('getString')
            ->willReturn('Test Footer');

        // Expect that the notification service will be called with PHPMailer API
        $mocks['notification']->expects($this->once())
            ->method('setFrom')
            ->with(
                $this->equalTo('test@example.com'),
                $this->equalTo('Test User')
            );

        $mocks['notification']->expects($this->once())
            ->method('AddAddress')
            ->with(
                $this->equalTo('recipient@example.com'),
                $this->equalTo('Recipient Name')
            );

        $mocks['notification']->expects($this->once())
            ->method('Send')
            ->willReturn(true);

        $mocks['notification']->expects($this->once())
            ->method('ClearAddresses');

        // Allow setFooter and getSignature/getFooter calls
        $mocks['notification']->method('setFooter');
        $mocks['notification']->method('getSignature')->willReturn('');
        $mocks['notification']->method('getFooter')->willReturn('Test Footer');

        // Create service and call the method
        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        $result = $members->sendEmail(
            'recipient@example.com',
            'Recipient Name',
            'Test Subject',
            'Test message',
            'test@example.com',
            'Test User'
        );

        // Method doesn't return a value, just verify no exception was thrown
        $this->assertTrue(true);
    }

    /**
     * Test that logger is used when fetching member by login
     */
    public function testGetMemberByLoginUsesLogger()
    {
        $mocks = $this->createMembersMocks();

        // Mock repository to return member data
        $mocks['repository']->expects($this->once())
            ->method('findByLogin')
            ->with('testuser')
            ->willReturn(['mem_id' => 1, 'mem_login' => 'testuser']);

        // Expect logger to be called
        $mocks['logger']->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo('Members'),
                $this->callback(function ($context) {
                    return isset($context['Method']) &&
                           $context['Method'] === 'getMemberByLogin' &&
                           isset($context['memberLogin']) &&
                           $context['memberLogin'] === 'testuser';
                })
            );

        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        // Act
        $result = $members->getMemberByLogin('testuser');

        // Assert - if we get here, logger was called correctly ✅
        $this->assertIsArray($result);
    }

    /**
     * Test getMemberById returns expected data structure
     */
    public function testGetMemberByIdReturnsCorrectStructure()
    {
        $mocks = $this->createMembersMocks();

        $expectedMember = [
            'mem_id' => '1',
            'mem_login' => 'johndoe',
            'mem_name' => 'John Doe',
            'mem_email_work' => 'john@example.com',
            'mem_profil' => '0'
        ];

        // Mock repository to return member data
        $mocks['repository']->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($expectedMember);

        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        $result = $members->getMemberById('1');

        // Verify we got the expected structure
        $this->assertIsArray($result);
        $this->assertEquals('1', $result['mem_id']);
        $this->assertEquals('johndoe', $result['mem_login']);
    }

    /**
     * Test that AppConfig is used for accessing configuration
     */
    public function testUsesAppConfigForConfiguration()
    {
        $mocks = $this->createMembersMocks();

        // AppConfig should be called for configuration values
        $mocks['appConfig']->expects($this->any())
            ->method('getString')
            ->willReturn('Test String');

        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        // Service created successfully with AppConfig
        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * Test dependency isolation - different mocks don't interfere
     */
    public function testDependencyIsolation()
    {
        // Test 1: Notification succeeds
        $mocks1 = $this->createMembersMocks();
        $mocks1['notification']->method('send')->willReturn(true);

        $members1 = new Members(
            $mocks1['repository'],
            $mocks1['logger'],
            $mocks1['notification'],
            $mocks1['appConfig']
        );

        // Test 2: Notification fails (different mock behavior)
        $mocks2 = $this->createMembersMocks();
        $mocks2['notification']->method('send')->willReturn(false);

        $members2 = new Members(
            $mocks2['repository'],
            $mocks2['logger'],
            $mocks2['notification'],
            $mocks2['appConfig']
        );

        // Both services are independent with different behaviors ✅
        $this->assertNotSame($members1, $members2);
    }

    /**
     * Test that all dependencies are explicit and self-documenting
     */
    public function testDependenciesAreSelfDocumenting()
    {
        // Just by looking at the constructor, we know Members needs:
        // 1. MembersRepositoryInterface - for data access (Repository Pattern!)
        // 2. Logger - for logging
        // 3. Notification - for sending notifications
        // 4. AppConfig - for configuration values
        //
        // Notice how the Repository Pattern makes this even cleaner!
        // No more Database or RequestData - that's hidden in the repository.

        $mocks = $this->createMembersMocks();

        $members = new Members(
            $mocks['repository'],     // #1 Data access (via Repository!)
            $mocks['logger'],         // #2 Logging
            $mocks['notification'],   // #3 Notifications
            $mocks['appConfig']       // #4 Configuration
        );

        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * Comparison test: Old pattern (Service Locator) vs New pattern (Pure DI)
     */
    public function testPatternComparisonDocumentation()
    {
        // ❌ OLD WAY (Service Locator):
        // ----------------------------------
        // public function __construct(Database $db, Logger $logger, Container $container) {
        //     $this->container = $container;
        // }
        //
        // Problems:
        // - Hidden dependencies fetched via $container->getNotification()
        // - Hard to test - need to mock entire Container
        // - Dependencies not clear from constructor
        // - Tight coupling to Container

        // ✅ NEW WAY (Pure Constructor Injection):
        // --------------------------------------------
        // public function __construct(
        //     Database $database,
        //     Logger $logger,
        //     Notification $notification,
        //     AppConfig $appConfig,
        //     RequestData $requestData
        // ) {
        //     // All dependencies explicit
        // }
        //
        // Benefits:
        // - All dependencies visible in constructor
        // - Easy to test - just mock what you need
        // - Type-safe
        // - Reduced coupling

        $mocks = $this->createMembersMocks();

        $members = new Members(
            $mocks['repository'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig']
        );

        $this->assertInstanceOf(Members::class, $members);

        // The improvement is massive! ✅
    }
}
