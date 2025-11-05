# Pure Constructor Injection Refactoring

## Overview

This document describes the refactoring of phpCollab services from the **Service Locator anti-pattern** to **Pure Constructor Injection**. This is a major improvement in code quality, testability, and maintainability.

## What is Pure Constructor Injection?

Pure Constructor Injection is a design pattern where a class declares **all** its dependencies in its constructor parameters. The dependency injection container automatically resolves and injects these dependencies.

### Before: Service Locator Pattern (Anti-pattern)

```php
class Tasks {
    private Container $container;  // ❌ Depends on the entire container!

    public function __construct(Database $database, Container $container) {
        $this->db = $database;
        $this->container = $container;
    }

    public function sendNotification() {
        // ❌ Hidden dependency - discovered at runtime
        $mail = $this->container->getNotificationService();
        $mail->send();
    }
}
```

**Problems:**
- ❌ Hidden dependencies - you must read the entire class to see what it uses
- ❌ Depends on the entire Container (coupling)
- ❌ Hard to test - must mock the entire Container
- ❌ Runtime dependency lookup - slower and error-prone
- ❌ No IDE autocomplete for dependencies

### After: Pure Constructor Injection

```php
class Tasks {
    private MailNotification $mailNotification;  // ✅ Explicit dependency!
    private Projects $projects;
    private Teams $teams;
    // ... all dependencies declared as properties

    public function __construct(
        Database $database,
        MailNotification $mailNotification,  // ✅ Explicit!
        string $language,
        Projects $projects,
        Teams $teams,
        Notifications $notifications,
        Notification $notification
    ) {
        $this->db = $database;
        $this->mailNotification = $mailNotification;
        $this->language = $language;
        $this->projects = $projects;
        $this->teams = $teams;
        $this->notifications = $notifications;
        $this->notification = $notification;
    }

    public function sendNotification() {
        // ✅ Direct usage - dependency already injected
        $this->mailNotification->send();
    }
}
```

**Benefits:**
- ✅ All dependencies are explicit and visible
- ✅ Only depends on what it actually needs
- ✅ Easy to test - just pass mocks to constructor
- ✅ Dependencies injected at creation time
- ✅ Full IDE autocomplete support
- ✅ Type safety enforced by PHP type hints

## Refactored Services

### 1. Tasks Service

**Before:**
```php
public function __construct(Database $database, Container $container)
```

**After:**
```php
public function __construct(
    Database $database,
    MailNotification $mailNotification,
    string $language,
    Projects $projects,
    Teams $teams,
    Notifications $notifications,
    Notification $notification
)
```

**Dependencies extracted from Container:**
- `getNotificationService()` → `MailNotification $mailNotification`
- `getLanguage()` → `string $language`
- `getProjectsLoader()` → `Projects $projects`
- `getTeams()` → `Teams $teams`
- `getNotificationsManager()` → `Notifications $notifications`
- `getNotification()` → `Notification $notification`

**Changes:**
- Removed `Container` dependency
- Added 7 explicit dependencies
- Updated all `$this->container->getX()` calls to use injected dependencies

### 2. Subtasks Service

**Before:**
```php
public function __construct(Database $database, Container $container)
```

**After:**
```php
public function __construct(
    Database $database,
    Notifications $notifications,
    SubtaskNotifications $subtaskNotifications
)
```

**Dependencies extracted from Container:**
- `getNotificationsManager()` → `Notifications $notifications`
- `getSubtasksNotificationsManager()` → `SubtaskNotifications $subtaskNotifications`

**Changes:**
- Removed `Container` dependency
- Added 2 explicit dependencies
- Much simpler and clearer!

### 3. Members Service

**Before:**
```php
public function __construct(Database $database, Logger $logger, Container $container)
```

**After:**
```php
public function __construct(
    Database $database,
    Logger $logger,
    Notification $notification
)
```

**Dependencies extracted from Container:**
- `getNotification()` → `Notification $notification`

**Changes:**
- Removed `Container` dependency
- Added 1 explicit dependency
- Already had `Logger` as explicit dependency ✅

## Configuration Changes

### Service Definitions (`config/services.php`)

Services are now configured with explicit dependencies:

```php
// ✅ Pure constructor injection
$services->set(Tasks::class)
    ->args([
        service(Database::class),
        service(MailNotification::class),
        param('app.language'),
        service(Projects::class),
        service(Teams::class),
        service(Notifications::class),
        service(Notification::class)
    ])
    ->public();

$services->set(Subtasks::class)
    ->args([
        service(Database::class),
        service(Notifications::class),
        service(SubtaskNotifications::class)
    ])
    ->public();

$services->set(Members::class)
    ->args([
        service(Database::class),
        service(Logger::class),
        service(Notification::class)
    ])
    ->public();
```

## Testing Benefits

### Before: Hard to Test

```php
// ❌ Must mock the entire Container
$mockContainer = $this->createMock(Container::class);
$mockContainer->expects($this->any())
    ->method('getNotificationService')
    ->willReturn($mockNotification);
$mockContainer->expects($this->any())
    ->method('getLanguage')
    ->willReturn('en');
// ... mock every possible Container method 😱

$tasks = new Tasks($mockDb, $mockContainer);
```

### After: Easy to Test

```php
// ✅ Just pass the mocks you need
$mockDb = $this->createMock(Database::class);
$mockMailNotification = $this->createMock(MailNotification::class);
$mockProjects = $this->createMock(Projects::class);
$mockTeams = $this->createMock(Teams::class);
$mockNotifications = $this->createMock(Notifications::class);
$mockNotification = $this->createMock(Notification::class);

$tasks = new Tasks(
    $mockDb,
    $mockMailNotification,
    'en',
    $mockProjects,
    $mockTeams,
    $mockNotifications,
    $mockNotification
);

// Setup specific expectations
$mockMailNotification->expects($this->once())
    ->method('send')
    ->with($expectedRecipient, $expectedMessage);

$tasks->sendNotification();
```

## Architecture Principles

### 1. Dependency Inversion Principle (SOLID)

Classes now depend on abstractions (interfaces/types) rather than the concrete Container:

```php
// ✅ Depends on specific services (could be interfaces)
public function __construct(
    Database $database,
    MailNotification $mailNotification,
    ...
)
```

### 2. Single Responsibility Principle

Services no longer have the responsibility of locating their dependencies:

```php
// ❌ Before: Service locates dependencies
$mail = $this->container->getNotificationService();

// ✅ After: Dependencies are provided
$this->mailNotification->send();
```

### 3. Explicit is Better Than Implicit

All dependencies are declared upfront:

```php
// Constructor signature tells you EVERYTHING the class needs
public function __construct(
    Database $database,              // Needs database
    MailNotification $mailNotification,  // Needs email
    string $language,                // Needs language
    Projects $projects,              // Needs projects
    Teams $teams,                    // Needs teams
    ...
)
```

## Performance Impact

### Positive Impacts

1. **No runtime lookups** - Dependencies are injected once at creation
2. **Container optimization** - Symfony can optimize the dependency graph
3. **Better caching** - Container can be compiled and cached

### No Negative Impacts

- Service instantiation happens only once (singleton pattern maintained)
- Memory usage is identical
- No additional overhead

## Migration Strategy

### Services Already Refactored ✅

- `Tasks` - 7 explicit dependencies
- `Subtasks` - 3 explicit dependencies
- `Members` - 3 explicit dependencies

### Services To Be Refactored ⏳

Services still using Container that should be refactored:

- `SetStatus`
- `SetTaskStatus`
- `Topics`
- `Files`
- `UpdateFile`
- `PeerReview`
- `ApprovalTracking`
- `Support`
- `Invoices`
- `ResetPassword`
- `MailNotification`

### Refactoring Process

1. **Find Container usage**
   ```bash
   grep "container->" classes/ServiceName/ServiceName.php
   ```

2. **Identify dependencies**
   - List all `$this->container->getX()` calls
   - Determine what services are actually needed

3. **Update constructor**
   - Add parameters for each dependency
   - Add type hints
   - Remove Container parameter

4. **Update usages**
   - Replace `$this->container->getX()` with `$this->x`
   - Use injected dependencies directly

5. **Update configuration**
   - Update `config/services.php`
   - Add `service()` references for each dependency

6. **Test**
   - Verify service still works
   - Run test suite
   - Check for any errors

## Future Enhancements

### 1. Introduce Interfaces

```php
interface NotificationInterface {
    public function send(string $to, string $message): void;
}

class MailNotification implements NotificationInterface {
    // ...
}

// In services.php
$services->set(NotificationInterface::class)
    ->class(MailNotification::class);
```

### 2. Use Setter Injection for Optional Dependencies

```php
class Tasks {
    private ?Notification $notification = null;

    public function setNotification(Notification $notification): void {
        $this->notification = $notification;
    }
}
```

### 3. Method Injection for Context-Specific Dependencies

```php
public function sendNotification(Notification $notification, ...) {
    // Dependency provided per-method call
}
```

### 4. Property Injection (Use Sparingly)

```php
class Tasks {
    #[Inject]
    private Notification $notification;
}
```

## Comparison Table

| Aspect | Service Locator | Pure Constructor Injection |
|--------|----------------|----------------------------|
| **Dependency Visibility** | Hidden (runtime) | Explicit (constructor) |
| **Container Coupling** | High (depends on Container) | None |
| **Testability** | Hard (mock Container) | Easy (mock dependencies) |
| **IDE Support** | Poor | Excellent |
| **Type Safety** | Runtime errors | Compile-time checks |
| **Performance** | Runtime lookup | Direct access |
| **Code Clarity** | Must read entire class | See constructor |
| **Maintainability** | Lower | Higher |

## Running the Demo

Test the pure constructor injection implementation:

```bash
php test_pure_constructor_injection.php
```

This will show:
- Before/after comparison
- Refactored services
- Dependency inspection
- Key improvements

## References

- [Dependency Injection (Martin Fowler)](https://martinfowler.com/articles/injection.html)
- [Service Locator is an Anti-Pattern](https://blog.ploeh.dk/2010/02/03/ServiceLocatorisanAnti-Pattern/)
- [SOLID Principles](https://en.wikipedia.org/wiki/SOLID)
- [Symfony Service Container](https://symfony.com/doc/current/service_container.html)

## Conclusion

Pure Constructor Injection is a significant improvement over the Service Locator pattern:

✅ **Explicit** - Dependencies are clear and visible
✅ **Testable** - Easy to mock and test
✅ **Type-safe** - PHP enforces type hints
✅ **Maintainable** - Easy to understand and modify
✅ **Performant** - No runtime lookups

The refactoring improves code quality without breaking existing functionality.

---

**Next Steps:**
1. Refactor remaining services to use pure constructor injection
2. Introduce interfaces for better abstraction
3. Remove Container getter methods once all services are refactored
4. Make services private (accessed only via DI, not Container)

**Status:** ✅ 3 services refactored, ~15 services remaining
