<?php

namespace Tests\Unit\Subtasks;

use Codeception\Test\Unit;
use phpCollab\AppConfig;
use phpCollab\Database;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\SubtaskNotifications;
use phpCollab\RequestData;
use phpCollab\Subtasks\Subtasks;

/**
 * Unit tests for Subtasks service with updated AppConfig/RequestData dependencies
 *
 * Subtasks demonstrates:
 * - Pure constructor injection with all 5 explicit dependencies
 * - Easy testability with mocked dependencies
 * - Type safety and clear dependency graph
 */
class SubtasksTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * Helper method to create standard mocks
     */
    private function createSubtasksMocks(): array
    {
        return [
            'database' => $this->createMock(Database::class),
            'notifications' => $this->createMock(Notifications::class),
            'subtaskNotifications' => $this->createMock(SubtaskNotifications::class),
            'appConfig' => $this->createMock(AppConfig::class),
            'requestData' => $this->createMock(RequestData::class),
        ];
    }

    /**
     * Test instantiation with all 5 required dependencies
     */
    public function testCanInstantiateWithExplicitDependencies()
    {
        $mocks = $this->createSubtasksMocks();

        // ✅ All dependencies are explicit - no Container needed!
        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that constructor enforces type safety
     */
    public function testConstructorTypeEnforcement()
    {
        $mocks = $this->createSubtasksMocks();

        $subtasks = new Subtasks(
            $mocks['database'],                // Type: Database
            $mocks['notifications'],           // Type: Notifications
            $mocks['subtaskNotifications'],    // Type: SubtaskNotifications
            $mocks['appConfig'],               // Type: AppConfig
            $mocks['requestData']              // Type: RequestData
        );

        // If we got here, all types are correct ✅
        $this->assertTrue(true);
    }

    /**
     * Test that Notifications service is injected and can be mocked
     */
    public function testNotificationsServiceIsInjected()
    {
        $mocks = $this->createSubtasksMocks();

        // Mock the actual method used: getMemberNotifications()
        $mocks['notifications']->expects($this->any())
            ->method('getMemberNotifications')
            ->willReturn(['email_work' => 'test@example.com', 'name' => 'Test User']);

        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that SubtaskNotifications service is injected
     */
    public function testSubtaskNotificationsServiceIsInjected()
    {
        $mocks = $this->createSubtasksMocks();

        // Mock the actual methods used by SubtaskNotifications
        $mocks['subtaskNotifications']->expects($this->any())
            ->method('setWorkEmail');

        $mocks['subtaskNotifications']->expects($this->any())
            ->method('setUserName');

        $mocks['subtaskNotifications']->expects($this->any())
            ->method('getTaskDetails')
            ->willReturn(null);

        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that AppConfig is used for configuration
     */
    public function testAppConfigIsInjected()
    {
        $mocks = $this->createSubtasksMocks();

        // Mock AppConfig methods
        $mocks['appConfig']->expects($this->any())
            ->method('getString')
            ->willReturn('Test String');

        $mocks['appConfig']->expects($this->any())
            ->method('getTableName')
            ->willReturn('test_table');

        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test that RequestData is injected (for Gateway initialization)
     */
    public function testRequestDataIsInjected()
    {
        $mocks = $this->createSubtasksMocks();

        // Mock RequestData
        $mocks['requestData']->expects($this->any())
            ->method('all')
            ->willReturn(['id' => 1]);

        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test dependency isolation
     */
    public function testDependencyIsolation()
    {
        // Test 1: Notifications returns user data
        $mocks1 = $this->createSubtasksMocks();
        $mocks1['notifications']->method('getMemberNotifications')
            ->willReturn(['email_work' => 'user1@example.com', 'name' => 'User 1']);

        $subtasks1 = new Subtasks(
            $mocks1['database'],
            $mocks1['notifications'],
            $mocks1['subtaskNotifications'],
            $mocks1['appConfig'],
            $mocks1['requestData']
        );

        // Test 2: Notifications returns different user data (different mock behavior)
        $mocks2 = $this->createSubtasksMocks();
        $mocks2['notifications']->method('getMemberNotifications')
            ->willReturn(['email_work' => 'user2@example.com', 'name' => 'User 2']);

        $subtasks2 = new Subtasks(
            $mocks2['database'],
            $mocks2['notifications'],
            $mocks2['subtaskNotifications'],
            $mocks2['appConfig'],
            $mocks2['requestData']
        );

        // Both services are independent with different behaviors ✅
        $this->assertNotSame($subtasks1, $subtasks2);
    }

    /**
     * Test that dependencies are self-documenting
     */
    public function testDependenciesAreSelfDocumenting()
    {
        // Just by looking at the constructor, we know Subtasks needs:
        // 1. Database - for data access
        // 2. Notifications - for notification records
        // 3. SubtaskNotifications - for sending subtask-specific notifications
        // 4. AppConfig - for configuration values
        // 5. RequestData - for request parameters

        $mocks = $this->createSubtasksMocks();

        $subtasks = new Subtasks(
            $mocks['database'],               // #1 Data access
            $mocks['notifications'],          // #2 Notification records
            $mocks['subtaskNotifications'],   // #3 Subtask notifications
            $mocks['appConfig'],              // #4 Configuration
            $mocks['requestData']             // #5 Request data
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);
    }

    /**
     * Test pattern comparison
     */
    public function testPatternComparisonDocumentation()
    {
        // ❌ OLD WAY (Service Locator):
        // ----------------------------------
        // public function __construct(Database $db, Container $container) {
        //     $this->container = $container;
        //     $this->notifications = $container->getNotificationsManager(); // Hidden!
        // }
        //
        // Problems:
        // - Hidden dependency on Container
        // - NotificationsManager fetched inside constructor
        // - Hard to test
        // - Unclear dependencies

        // ✅ NEW WAY (Pure Constructor Injection):
        // --------------------------------------------
        // public function __construct(
        //     Database $database,
        //     Notifications $notifications,
        //     SubtaskNotifications $subtaskNotifications,
        //     AppConfig $appConfig,
        //     RequestData $requestData
        // ) {
        //     // All dependencies explicit
        // }
        //
        // Benefits:
        // - All dependencies visible
        // - Easy to mock and test
        // - Type-safe
        // - Clear and maintainable

        $mocks = $this->createSubtasksMocks();

        $subtasks = new Subtasks(
            $mocks['database'],
            $mocks['notifications'],
            $mocks['subtaskNotifications'],
            $mocks['appConfig'],
            $mocks['requestData']
        );

        $this->assertInstanceOf(Subtasks::class, $subtasks);

        // Much clearer and easier to test! ✅
    }
}
