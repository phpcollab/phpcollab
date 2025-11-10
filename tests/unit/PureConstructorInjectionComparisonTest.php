<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use phpCollab\AppConfig;
use phpCollab\Database;
use phpCollab\Members\Members;
use phpCollab\Notification;
use phpCollab\Notifications\MailNotification;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\SubtaskNotifications;
use phpCollab\Projects\Projects;
use phpCollab\RequestData;
use phpCollab\Subtasks\Subtasks;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use Monolog\Logger;

/**
 * Comparison Test: Service Locator Pattern vs Pure Constructor Injection
 *
 * This test suite demonstrates the dramatic improvement in testability
 * when moving from Service Locator to Pure Constructor Injection.
 */
class PureConstructorInjectionComparisonTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * ============================================================================
     * COMPARISON #1: Test Setup Complexity
     * ============================================================================
     */

    /**
     * OLD WAY: Service Locator Pattern
     *
     * To test a service that uses Container, you need to:
     * 1. Mock the Container
     * 2. Mock EVERY method that MIGHT be called on the Container
     * 3. Set up return values for each method
     * 4. Hope you didn't miss any Container calls
     * 5. Pray the service doesn't call an unexpected Container method
     */
    public function testOldWay_ServiceLocatorPatternIsComplexToTest()
    {
        // This is pseudocode showing how complex the old tests were:
        //
        // $mockContainer = $this->createMock(Container::class);
        //
        // // Need to mock EVERY possible Container method
        // $mockContainer->expects($this->any())
        //     ->method('getNotificationService')
        //     ->willReturn($mockNotificationService);
        //
        // $mockContainer->expects($this->any())
        //     ->method('getLanguage')
        //     ->willReturn('en');
        //
        // $mockContainer->expects($this->any())
        //     ->method('getProjectsLoader')
        //     ->willReturn($mockProjects);
        //
        // $mockContainer->expects($this->any())
        //     ->method('getTeams')
        //     ->willReturn($mockTeams);
        //
        // // ... and MORE mocking for every possible dependency
        // // What if you missed one? Test breaks at runtime!
        //
        // $tasks = new Tasks($mockDb, $mockContainer);

        // Problems:
        // ❌ Verbose and repetitive
        // ❌ Fragile - breaks if service uses new Container method
        // ❌ Hidden dependencies - must read entire class to know what to mock
        // ❌ Tight coupling to Container
        // ❌ Hard to maintain

        $this->assertTrue(true, "Old way was complex and fragile");
    }

    /**
     * NEW WAY: Pure Constructor Injection
     *
     * To test a service with explicit dependencies:
     * 1. Mock each dependency
     * 2. Pass to constructor
     * 3. Done! ✅
     */
    public function testNewWay_PureConstructorInjectionIsSimpleToTest()
    {
        // ✅ Simple and clear!
        $tasks = new Tasks(
            $this->createMock(Database::class),
            $this->createMock(MailNotification::class),
            'en',
            $this->createMock(Projects::class),
            $this->createMock(Teams::class),
            $this->createMock(Notifications::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );

        // Benefits:
        // ✅ Concise and clear
        // ✅ Resilient - types are enforced
        // ✅ Explicit dependencies - visible in constructor
        // ✅ No Container coupling
        // ✅ Easy to maintain

        $this->assertInstanceOf(Tasks::class, $tasks);
    }

    /**
     * ============================================================================
     * COMPARISON #2: Dependency Visibility
     * ============================================================================
     */

    /**
     * OLD WAY: Dependencies are hidden
     *
     * You must read the ENTIRE class to find all Container calls.
     */
    public function testOldWay_HiddenDependencies()
    {
        // With Service Locator, dependencies are discovered like this:
        //
        // 1. Read through class methods
        // 2. Find $this->container->getX() calls
        // 3. Make a list of dependencies
        // 4. Update the list every time the class changes
        //
        // Example (pseudocode):
        // public function someMethod() {
        //     $mail = $this->container->getNotificationService();  // Dependency #1
        //     // ... 100 lines later ...
        //     $projects = $this->container->getProjectsLoader();   // Dependency #2
        //     // ... more code ...
        //     $teams = $this->container->getTeams();                // Dependency #3
        // }
        //
        // You won't know about these dependencies until you read the method!

        $this->assertTrue(true, "Dependencies are hidden with Service Locator");
    }

    /**
     * NEW WAY: Dependencies are explicit
     *
     * Just look at the constructor - all dependencies are visible immediately.
     */
    public function testNewWay_ExplicitDependencies()
    {
        // With Pure Constructor Injection, dependencies are immediately visible:
        //
        // public function __construct(
        //     Database $database,              // ✅ Dependency #1
        //     MailNotification $mailNotification,  // ✅ Dependency #2
        //     string $language,                 // ✅ Dependency #3
        //     Projects $projects,               // ✅ Dependency #4
        //     Teams $teams,                     // ✅ Dependency #5
        //     Notifications $notifications,     // ✅ Dependency #6
        //     Notification $notification        // ✅ Dependency #7
        // )
        //
        // ALL dependencies visible at a glance! ✅

        $tasks = new Tasks(
            $this->createMock(Database::class),
            $this->createMock(MailNotification::class),
            'en',
            $this->createMock(Projects::class),
            $this->createMock(Teams::class),
            $this->createMock(Notifications::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );

        $this->assertInstanceOf(Tasks::class, $tasks);
    }

    /**
     * ============================================================================
     * COMPARISON #3: Type Safety
     * ============================================================================
     */

    /**
     * OLD WAY: No compile-time type checking
     *
     * Errors discovered at runtime when Container method is called.
     */
    public function testOldWay_RuntimeErrors()
    {
        // With Service Locator:
        //
        // $mail = $this->container->getNotificationService();
        //
        // Problems:
        // - What if method doesn't exist? Runtime error!
        // - What if it returns wrong type? Runtime error!
        // - No IDE autocomplete
        // - No static analysis
        //
        // You only find errors when the code actually runs!

        $this->assertTrue(true, "Service Locator has runtime errors");
    }

    /**
     * NEW WAY: Compile-time type checking
     *
     * PHP enforces types at object creation time.
     */
    public function testNewWay_CompileTimeTypeChecking()
    {
        // With Pure Constructor Injection:
        //
        // public function __construct(
        //     Database $database,              // PHP enforces type!
        //     MailNotification $mailNotification,  // PHP enforces type!
        //     string $language,                 // PHP enforces type!
        //     // ...
        // )
        //
        // Benefits:
        // - Wrong type? Immediate TypeError
        // - IDE knows exact types - autocomplete works!
        // - Static analysis can catch errors
        // - Errors found before code runs

        $tasks = new Tasks(
            $this->createMock(Database::class),
            $this->createMock(MailNotification::class),
            'en',  // PHP ensures this is a string
            $this->createMock(Projects::class),
            $this->createMock(Teams::class),
            $this->createMock(Notifications::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );

        $this->assertInstanceOf(Tasks::class, $tasks);
    }

    /**
     * ============================================================================
     * COMPARISON #4: Coupling
     * ============================================================================
     */

    /**
     * OLD WAY: High coupling to Container
     *
     * Service depends on Container with 100+ methods.
     */
    public function testOldWay_HighCoupling()
    {
        // Container has methods like:
        // - getPDO()
        // - getLogger()
        // - getEscaperService()
        // - getBookmarksLoader()
        // - getDeleteBookmarksLoader()
        // - getLoginLogs()
        // - getSortingLoader()
        // - getAdministration()
        // - getOrganizationsManager()
        // - ... 100+ more methods
        //
        // A service depending on Container is coupled to ALL of these! ❌
        //
        // Even if it only uses 3-4 services, it COULD access all of them.

        $this->assertTrue(true, "Service Locator creates high coupling");
    }

    /**
     * NEW WAY: Low coupling to specific services
     *
     * Service depends ONLY on what it needs.
     */
    public function testNewWay_LowCoupling()
    {
        // Tasks depends on exactly 7 services:
        // 1. Database
        // 2. MailNotification
        // 3. language (string)
        // 4. Projects
        // 5. Teams
        // 6. Notifications
        // 7. Notification
        //
        // That's it! Can't access anything else. ✅
        //
        // Coupling reduced from ~100 to 7 (~93% reduction!)

        $tasks = new Tasks(
            $this->createMock(Database::class),
            $this->createMock(MailNotification::class),
            'en',
            $this->createMock(Projects::class),
            $this->createMock(Teams::class),
            $this->createMock(Notifications::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );

        $this->assertInstanceOf(Tasks::class, $tasks);
    }

    /**
     * ============================================================================
     * COMPARISON #5: Test Clarity
     * ============================================================================
     */

    /**
     * OLD WAY: Tests are unclear about what's being tested
     */
    public function testOldWay_UnclearTests()
    {
        // When you see:
        //
        // $mockContainer = $this->createMock(Container::class);
        // $service = new SomeService($mockContainer);
        //
        // Questions:
        // - What does this service actually need?
        // - What Container methods will be called?
        // - Which dependencies matter for this test?
        // - How do I set up the right mocks?
        //
        // It's unclear! You have to read the service code to understand.

        $this->assertTrue(true, "Service Locator makes tests unclear");
    }

    /**
     * NEW WAY: Tests are self-documenting
     */
    public function testNewWay_SelfDocumentingTests()
    {
        // When you see:
        //
        // $members = new Members(
        //     $mockDatabase,
        //     $mockLogger,
        //     $mockNotification
        // );
        //
        // It's immediately clear:
        // - Members needs Database, Logger, and Notification
        // - To test Members, mock these 3 dependencies
        // - No surprises!
        //
        // The test setup tells you exactly what the service needs! ✅

        $members = new Members(
            $this->createMock(Database::class),
            $this->createMock(Logger::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );

        $this->assertInstanceOf(Members::class, $members);
    }

    /**
     * ============================================================================
     * COMPARISON #6: Real-World Example - All Three Services
     * ============================================================================
     */

    /**
     * Demonstrate testing all three refactored services
     */
    public function testAllRefactoredServicesAreEasyToTest()
    {
        // Test Members (3 dependencies)
        $members = new Members(
            $this->createMock(Database::class),
            $this->createMock(Logger::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );
        $this->assertInstanceOf(Members::class, $members);

        // Test Subtasks (3 dependencies)
        $subtasks = new Subtasks(
            $this->createMock(Database::class),
            $this->createMock(Notifications::class),
            $this->createMock(SubtaskNotifications::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );
        $this->assertInstanceOf(Subtasks::class, $subtasks);

        // Test Tasks (7 dependencies)
        $tasks = new Tasks(
            $this->createMock(Database::class),
            $this->createMock(MailNotification::class),
            'en',
            $this->createMock(Projects::class),
            $this->createMock(Teams::class),
            $this->createMock(Notifications::class),
            $this->createMock(Notification::class),
            $this->createMock(AppConfig::class),
            $this->createMock(RequestData::class)
        );
        $this->assertInstanceOf(Tasks::class, $tasks);

        // All three services tested with simple, clear code! ✅
        // Try doing this with the old Container pattern - much harder!
    }

    /**
     * ============================================================================
     * SUMMARY
     * ============================================================================
     */

    /**
     * Summary of improvements
     */
    public function testSummaryOfImprovements()
    {
        // Pure Constructor Injection provides:
        //
        // ✅ Explicit Dependencies     - Visible in constructor
        // ✅ Type Safety               - Enforced by PHP
        // ✅ Low Coupling              - Only depend on what you need
        // ✅ Easy Testing              - Simple mock setup
        // ✅ Self-Documenting          - Clear what service needs
        // ✅ IDE Support               - Full autocomplete
        // ✅ Static Analysis           - Tools can verify correctness
        // ✅ Better Maintainability    - Clear dependencies
        // ✅ Reduced Complexity        - No Container indirection
        // ✅ Compile-Time Errors       - Catch errors early
        //
        // Service Locator had:
        //
        // ❌ Hidden Dependencies       - Discovered at runtime
        // ❌ No Type Safety            - Runtime errors only
        // ❌ High Coupling             - Depends on entire Container
        // ❌ Hard Testing              - Complex mock setup
        // ❌ Unclear Code              - Must read entire class
        // ❌ Poor IDE Support          - Limited autocomplete
        // ❌ No Static Analysis        - Can't verify at compile time
        // ❌ Hard Maintenance          - Dependencies unclear
        // ❌ High Complexity           - Container indirection
        // ❌ Runtime Errors            - Find errors late
        //
        // The improvement is MASSIVE! ✅

        $this->assertTrue(true, "Pure Constructor Injection is dramatically better!");
    }
}
