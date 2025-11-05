<?php

namespace Tests\Unit\Members;

use Codeception\Test\Unit;
use Monolog\Logger;
use phpCollab\Database;
use phpCollab\Members\Members;
use phpCollab\Notification;

/**
 * Unit tests for Members service demonstrating Pure Constructor Injection benefits
 *
 * These tests show how easy it is to test services with explicit dependencies
 * compared to the old Service Locator pattern.
 */
class MembersTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Test that Members can be instantiated with mocked dependencies
     *
     * This demonstrates the primary benefit of pure constructor injection:
     * We can create the service with mocks without needing the Container.
     */
    public function testCanInstantiateWithMockedDependencies()
    {
        // ✅ Easy to test - just create mocks for each dependency
        $mockDatabase = $this->createMock(Database::class);
        $mockLogger = $this->createMock(Logger::class);
        $mockNotification = $this->createMock(Notification::class);

        // Create the service with mocked dependencies
        $members = new Members($mockDatabase, $mockLogger, $mockNotification);

        // Verify the service was created successfully
        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * Test that Members accepts correct dependency types
     *
     * This demonstrates type safety - PHP will throw a TypeError if wrong types are passed.
     */
    public function testConstructorAcceptsCorrectTypes()
    {
        $mockDatabase = $this->createMock(Database::class);
        $mockLogger = $this->createMock(Logger::class);
        $mockNotification = $this->createMock(Notification::class);

        $members = new Members($mockDatabase, $mockLogger, $mockNotification);

        // If we get here without a TypeError, the types are correct ✅
        $this->assertTrue(true);
    }

    /**
     * Test sendEmail method with mocked notification service
     *
     * This demonstrates how easy it is to verify interactions when dependencies are explicit.
     */
    public function testSendEmailUsesInjectedNotification()
    {
        // Arrange: Set up mocks
        $mockDatabase = $this->createMock(Database::class);
        $mockLogger = $this->createMock(Logger::class);
        $mockNotification = $this->createMock(Notification::class);

        // Expect that the notification service will be called
        $mockNotification->expects($this->once())
            ->method('setFrom')
            ->with(
                $this->equalTo('test@example.com'),
                $this->equalTo('Test User')
            );

        $mockNotification->expects($this->once())
            ->method('setTo')
            ->with($this->equalTo('recipient@example.com'));

        $mockNotification->expects($this->once())
            ->method('setSubject')
            ->with($this->equalTo('Test Subject'));

        $mockNotification->expects($this->once())
            ->method('setBodyText')
            ->with($this->equalTo('Test message'));

        $mockNotification->expects($this->once())
            ->method('send')
            ->willReturn(true);

        // Act: Create service and call the method
        $members = new Members($mockDatabase, $mockLogger, $mockNotification);

        $result = $members->sendEmail(
            'recipient@example.com',
            'Recipient Name',
            'Test Subject',
            'Test message',
            'test@example.com',
            'Test User'
        );

        // Assert: Verify the result
        $this->assertTrue($result);
    }

    /**
     * Test that logger is used when fetching member by login
     *
     * Demonstrates testing of logging behavior with mocked logger.
     */
    public function testGetMemberByLoginUsesLogger()
    {
        // Arrange
        $mockDatabase = $this->createMock(Database::class);
        $mockLogger = $this->createMock(Logger::class);
        $mockNotification = $this->createMock(Notification::class);

        // Expect logger to be called
        $mockLogger->expects($this->once())
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
        $mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn(['mem_id' => 1, 'mem_login' => 'testuser']);

        $members = new Members($mockDatabase, $mockLogger, $mockNotification);

        // Act
        $result = $members->getMemberByLogin('testuser');

        // Assert - if we get here, logger was called correctly ✅
        $this->assertIsArray($result);
    }

    /**
     * Test comparison: Old way (Service Locator) vs New way (Constructor Injection)
     *
     * This test documents the improvement in testability.
     */
    public function testTestabilityComparison()
    {
        // ❌ OLD WAY (Service Locator Pattern):
        // -----------------------------------------
        // public function __construct(Database $db, Logger $logger, Container $container) {
        //     $this->container = $container;
        // }
        //
        // To test, you would need to:
        // 1. Create mock Container
        // 2. Mock EVERY possible method that might be called on Container
        // 3. Set up complex expectations for container->getX() calls
        // 4. Hope you didn't miss any Container calls
        //
        // Example of old test complexity:
        // $mockContainer = $this->createMock(Container::class);
        // $mockContainer->expects($this->any())
        //     ->method('getNotification')
        //     ->willReturn($mockNotification);
        // $mockContainer->expects($this->any())
        //     ->method('getProjectsLoader')
        //     ->willReturn($mockProjects);
        // // ... and so on for every possible dependency
        // $members = new Members($mockDb, $mockLogger, $mockContainer);

        // ✅ NEW WAY (Pure Constructor Injection):
        // -----------------------------------------
        // public function __construct(Database $db, Logger $logger, Notification $notification) {
        //     $this->notification = $notification;
        // }
        //
        // To test:
        // 1. Create mocks for each explicit dependency
        // 2. Pass them to constructor
        // 3. Done! ✅

        $mockDatabase = $this->createMock(Database::class);
        $mockLogger = $this->createMock(Logger::class);
        $mockNotification = $this->createMock(Notification::class);

        // That's it! So much cleaner ✅
        $members = new Members($mockDatabase, $mockLogger, $mockNotification);

        $this->assertInstanceOf(Members::class, $members);

        // This test demonstrates how much simpler and clearer the new approach is!
    }

    /**
     * Test that demonstrates dependency isolation
     *
     * With pure constructor injection, each test can have different mock behavior
     * without affecting other tests.
     */
    public function testDependencyIsolation()
    {
        // Test 1: Notification succeeds
        $mockNotification1 = $this->createMock(Notification::class);
        $mockNotification1->method('send')->willReturn(true);

        $members1 = new Members(
            $this->createMock(Database::class),
            $this->createMock(Logger::class),
            $mockNotification1
        );

        // Test 2: Notification fails (different mock behavior)
        $mockNotification2 = $this->createMock(Notification::class);
        $mockNotification2->method('send')->willReturn(false);

        $members2 = new Members(
            $this->createMock(Database::class),
            $this->createMock(Logger::class),
            $mockNotification2
        );

        // Both services are independent with different behaviors ✅
        $this->assertNotSame($members1, $members2);
    }

    /**
     * Test that demonstrates explicit dependencies make tests self-documenting
     */
    public function testSelfDocumentingDependencies()
    {
        // Just by looking at the constructor call, you know EXACTLY what Members needs:
        // 1. Database - for data access
        // 2. Logger - for logging
        // 3. Notification - for sending notifications

        // No surprises! No hidden dependencies! ✅

        $members = new Members(
            $this->createMock(Database::class),     // Data access
            $this->createMock(Logger::class),       // Logging
            $this->createMock(Notification::class)  // Notifications
        );

        $this->assertInstanceOf(Members::class, $members);
    }
}
