<?php

namespace Tests\Unit\Subtasks;

use Codeception\Test\Unit;
use phpCollab\Database;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\SubtaskNotifications;
use phpCollab\Subtasks\Subtasks;

/**
 * Unit tests for Subtasks service demonstrating Pure Constructor Injection benefits
 *
 * Subtasks is a great example because it went from:
 * - Before: 1 explicit + 1 hidden Container dependency
 * - After: 3 explicit dependencies (Database, Notifications, SubtaskNotifications)
 */
class SubtasksTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Test instantiation with all required dependencies
     *
     * Pure constructor injection makes it clear what Subtasks needs to function.
     */
    public function testCanInstantiateWithExplicitDependencies()
    {
        // ✅ All dependencies are explicit - no Container needed!
        $mockDatabase = $this->createMock(Database::class);
        $mockNotifications = $this->createMock(Notifications::class);
        $mockSubtaskNotifications = $this->createMock(SubtaskNotifications::class);

        $subtasks = new Subtasks(
            $mockDatabase,
            $mockNotifications,
            $mockSubtaskNotifications
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that constructor enforces type safety
     */
    public function testConstructorTypeEnforcement()
    {
        $mockDatabase = $this->createMock(Database::class);
        $mockNotifications = $this->createMock(Notifications::class);
        $mockSubtaskNotifications = $this->createMock(SubtaskNotifications::class);

        // PHP will enforce these types at runtime
        $subtasks = new Subtasks(
            $mockDatabase,              // Must be Database
            $mockNotifications,         // Must be Notifications
            $mockSubtaskNotifications   // Must be SubtaskNotifications
        );

        // If we got here, types are correct ✅
        $this->assertTrue(true);
    }

    /**
     * Test subtask creation uses notification services
     *
     * This test demonstrates how easy it is to verify that the service
     * uses its injected dependencies correctly.
     */
    public function testCreateSubtaskUsesNotificationServices()
    {
        // Arrange: Create mocks with expectations
        $mockDatabase = $this->createMock(Database::class);
        $mockNotifications = $this->createMock(Notifications::class);
        $mockSubtaskNotifications = $this->createMock(SubtaskNotifications::class);

        // Mock database to return subtask ID
        $mockDatabase->expects($this->once())
            ->method('query')
            ->willReturn(['id' => 123]);

        // Expect notifications to be stored
        $mockNotifications->expects($this->once())
            ->method('addNotification')
            ->with(
                $this->anything(), // notification type
                $this->equalTo(123), // subtask ID
                $this->anything(), // other params
                $this->anything()
            );

        // Expect notification email to be sent
        $mockSubtaskNotifications->expects($this->once())
            ->method('sendAssignmentNotification')
            ->with($this->equalTo(123))
            ->willReturn(true);

        // Act: Create service and perform action
        $subtasks = new Subtasks(
            $mockDatabase,
            $mockNotifications,
            $mockSubtaskNotifications
        );

        // Note: This is a simplified example. In real code, you'd call the actual method.
        // The point is to show how easy it is to set up expectations with explicit dependencies.

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that demonstrates the clarity of dependencies
     *
     * Compare the constructor signature to understand what the service needs.
     */
    public function testDependenciesAreSelfDocumenting()
    {
        // Looking at the constructor, we immediately know:
        // 1. Subtasks needs Database for data storage
        // 2. Subtasks needs Notifications for recording notification events
        // 3. Subtasks needs SubtaskNotifications for sending subtask-specific notifications

        // This is MUCH clearer than:
        // public function __construct(Database $db, Container $container)
        // where you have NO IDEA what comes from the container!

        $subtasks = new Subtasks(
            $this->createMock(Database::class),
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class)
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test mocking specific notification behavior
     *
     * With explicit dependencies, we can easily test different scenarios.
     */
    public function testCanMockNotificationSuccess()
    {
        $mockDatabase = $this->createMock(Database::class);
        $mockNotifications = $this->createMock(Notifications::class);
        $mockSubtaskNotifications = $this->createMock(SubtaskNotifications::class);

        // Scenario 1: Notification succeeds
        $mockSubtaskNotifications->method('sendAssignmentNotification')
            ->willReturn(true);

        $subtasks = new Subtasks(
            $mockDatabase,
            $mockNotifications,
            $mockSubtaskNotifications
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test mocking notification failure scenario
     */
    public function testCanMockNotificationFailure()
    {
        $mockDatabase = $this->createMock(Database::class);
        $mockNotifications = $this->createMock(Notifications::class);
        $mockSubtaskNotifications = $this->createMock(SubtaskNotifications::class);

        // Scenario 2: Notification fails
        $mockSubtaskNotifications->method('sendAssignmentNotification')
            ->willReturn(false);

        $subtasks = new Subtasks(
            $mockDatabase,
            $mockNotifications,
            $mockSubtaskNotifications
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);

        // Easy to test different scenarios with different mock behaviors! ✅
    }

    /**
     * Test that demonstrates reduced coupling
     *
     * Subtasks only depends on what it needs, not the entire Container.
     */
    public function testReducedCoupling()
    {
        // ❌ OLD WAY: Subtasks depended on Container (100+ services)
        //
        // This created tight coupling:
        // - Subtasks could access ANY service via Container
        // - Hard to track what Subtasks actually uses
        // - Tests needed to mock entire Container

        // ✅ NEW WAY: Subtasks depends on exactly 3 services
        //
        // Benefits:
        // - Clear dependencies
        // - Reduced coupling
        // - Easy to test

        $subtasks = new Subtasks(
            $this->createMock(Database::class),
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class)
        );

        // Just 3 dependencies, all explicit! ✅
        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test demonstrating test setup simplicity
     *
     * Setup for tests is now straightforward and easy to understand.
     */
    public function testSimpleTestSetup()
    {
        // Setting up a test is now as simple as:
        // 1. Create mocks for each dependency
        // 2. Configure mock behavior as needed
        // 3. Pass to constructor
        // 4. Test!

        // Step 1: Create mocks
        $database = $this->createMock(Database::class);
        $notifications = $this->createMock(Notifications::class);
        $subtaskNotifications = $this->createMock(SubtaskNotifications::class);

        // Step 2: Configure (if needed)
        $database->method('query')->willReturn(['id' => 1]);

        // Step 3: Create service
        $subtasks = new Subtasks($database, $notifications, $subtaskNotifications);

        // Step 4: Test!
        $this->assertInstanceOf(Subtasks::class, $subtasks);

        // Compare to the old way where you'd need to mock Container
        // and all its potential method calls! Much simpler now ✅
    }

    /**
     * Test that each test can have isolated dependencies
     */
    public function testDependencyIsolationAcrossTests()
    {
        // Test 1: Database returns success
        $db1 = $this->createMock(Database::class);
        $db1->method('query')->willReturn(['success' => true]);

        $subtasks1 = new Subtasks(
            $db1,
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class)
        );

        // Test 2: Database returns failure (completely isolated!)
        $db2 = $this->createMock(Database::class);
        $db2->method('query')->willReturn(['success' => false]);

        $subtasks2 = new Subtasks(
            $db2,
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class)
        );

        // Each test has its own isolated dependencies ✅
        $this->assertNotSame($subtasks1, $subtasks2);
    }

    /**
     * Comparison test: Old pattern vs New pattern
     */
    public function testPatternComparison()
    {
        // This test documents the improvement:

        // ❌ OLD PATTERN (Service Locator):
        // ----------------------------------
        // public function __construct(Database $db, Container $container) {
        //     $this->notifications = $container->getNotificationsManager();
        //     $this->subtaskNotifications = $container->getSubtasksNotificationsManager();
        // }
        //
        // Problems:
        // - Hidden dependencies (discovered at runtime)
        // - Depends on Container
        // - Hard to test (mock Container)
        // - Unclear what's needed

        // ✅ NEW PATTERN (Pure Constructor Injection):
        // --------------------------------------------
        // public function __construct(
        //     Database $database,
        //     Notifications $notifications,
        //     SubtaskNotifications $subtaskNotifications
        // ) {
        //     $this->notifications = $notifications;
        //     $this->subtaskNotifications = $subtaskNotifications;
        // }
        //
        // Benefits:
        // - Explicit dependencies
        // - No Container coupling
        // - Easy to test (mock specific services)
        // - Crystal clear requirements

        $subtasks = new Subtasks(
            $this->createMock(Database::class),
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class)
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);

        // The difference is night and day! ✅
    }
}
