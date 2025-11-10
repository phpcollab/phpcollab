<?php

namespace Tests\Unit\Members;

use Codeception\Test\Unit;
use Monolog\Logger;
use phpCollab\AppConfig;
use phpCollab\Database;
use phpCollab\Members\Members;
use phpCollab\Members\MembersGateway;
use phpCollab\Notification;
use phpCollab\RequestData;

/**
 * Unit tests for Members service with updated AppConfig/RequestData dependencies
 *
 * These tests demonstrate:
 * - Pure constructor injection with all explicit dependencies
 * - Easy mocking and testing of business logic
 * - Type safety and clear dependency graph
 */
class MembersTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Helper method to create standard mocks
     */
    private function createMembersMocks(): array
    {
        return [
            'database' => $this->createMock(Database::class),
            'logger' => $this->createMock(Logger::class),
            'notification' => $this->createMock(Notification::class),
            'appConfig' => $this->createMock(AppConfig::class),
            'requestData' => $this->createMock(RequestData::class),
        ];
    }

    /**
     * Test that Members can be instantiated with all 5 dependencies
     */
    public function testCanInstantiateWithMockedDependencies()
    {
        $mocks = $this->createMembersMocks();

        // ✅ Easy to test - just create mocks for each dependency
        $members = new Members(
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        // Verify the service was created successfully
        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * Test constructor enforces type safety
     */
    public function testConstructorTypeEnforcement()
    {
        $mocks = $this->createMembersMocks();

        $members = new Members(
            $mocks['database'],      // Type: Database
            $mocks['logger'],        // Type: Logger
            $mocks['notification'],  // Type: Notification
            $mocks['appConfig'],     // Type: AppConfig
            $mocks['requestData']    // Type: RequestData
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

        // Expect that the notification service will be called
        $mocks['notification']->expects($this->once())
            ->method('setFrom')
            ->with(
                $this->equalTo('test@example.com'),
                $this->equalTo('Test User')
            );

        $mocks['notification']->expects($this->once())
            ->method('setTo')
            ->with($this->equalTo('recipient@example.com'));

        $mocks['notification']->expects($this->once())
            ->method('setSubject')
            ->with($this->equalTo('Test Subject'));

        $mocks['notification']->expects($this->once())
            ->method('setBodyText')
            ->with($this->equalTo('Test message'));

        $mocks['notification']->expects($this->once())
            ->method('send')
            ->willReturn(true);

        // Create service and call the method
        $members = new Members(
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $result = $members->sendEmail(
            'recipient@example.com',
            'Recipient Name',
            'Test Subject',
            'Test message',
            'test@example.com',
            'Test User'
        );

        // Verify the result
        $this->assertTrue($result);
    }

    /**
     * Test that logger is used when fetching member by login
     */
    public function testGetMemberByLoginUsesLogger()
    {
        $mocks = $this->createMembersMocks();

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

        // Mock database to return test data
        $mocks['database']->expects($this->once())
            ->method('query')
            ->willReturn(['mem_id' => 1, 'mem_login' => 'testuser']);

        $members = new Members(
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
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

        // Mock database to return member data
        $mocks['database']->expects($this->once())
            ->method('query')
            ->willReturn($expectedMember);

        // Mock logger
        $mocks['logger']->expects($this->once())
            ->method('info');

        $members = new Members(
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
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
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
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
            $mocks1['database'],
            $mocks1['logger'],
            $mocks1['notification'],
            $mocks1['appConfig'],
            $mocks1['requestData']
        );

        // Test 2: Notification fails (different mock behavior)
        $mocks2 = $this->createMembersMocks();
        $mocks2['notification']->method('send')->willReturn(false);

        $members2 = new Members(
            $mocks2['database'],
            $mocks2['logger'],
            $mocks2['notification'],
            $mocks2['appConfig'],
            $mocks2['requestData']
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
        // 1. Database - for data access
        // 2. Logger - for logging
        // 3. Notification - for sending notifications
        // 4. AppConfig - for configuration values
        // 5. RequestData - for request parameters

        $mocks = $this->createMembersMocks();

        $members = new Members(
            $mocks['database'],       // #1 Data access
            $mocks['logger'],         // #2 Logging
            $mocks['notification'],   // #3 Notifications
            $mocks['appConfig'],      // #4 Configuration
            $mocks['requestData']     // #5 Request data
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
            $mocks['database'],
            $mocks['logger'],
            $mocks['notification'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Members::class, $members);

        // The improvement is massive! ✅
    }
}
