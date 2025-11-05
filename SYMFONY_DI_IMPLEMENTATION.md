# Symfony Dependency Injection Implementation

## Overview

This document describes the implementation of Symfony DI Container in phpCollab. The implementation provides a modern dependency injection system while maintaining backward compatibility with the existing Service Locator pattern.

## What's New

### Components Added

- **Symfony DependencyInjection** (v5.4): Core DI container
- **Symfony Config** (v5.4): Configuration component for service definitions

### New Files

1. **`config/services.php`**: Service definitions and configuration
2. **`classes/ContainerFactory.php`**: Factory for creating DI containers
3. **`classes/LoggerFactory.php`**: Factory for creating Logger instances
4. **`test_symfony_di.php`**: Test script for validating the DI implementation

### Modified Files

1. **`classes/Container.php`**: Updated to integrate with Symfony DI while maintaining backward compatibility
2. **`composer.json`**: Added Symfony DI dependencies

## Architecture

### Service Configuration (`config/services.php`)

All services are defined in a single configuration file using Symfony's PHP configuration format:

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()      // Automatically inject dependencies
        ->autoconfigure() // Automatically configure for tags
        ->private();      // Make services private by default

    // Service definitions...
};
```

### Container Factory

The `ContainerFactory` provides two methods:

1. **`create(array $configuration)`**: Creates a pure Symfony DI container
2. **`createLegacyContainer(array $configuration)`**: Creates a backward-compatible Container wrapper

### Hybrid Approach

The implementation uses a **hybrid approach**:

- **Symfony DI Container**: Manages service instantiation and dependencies
- **Legacy Container Wrapper**: Maintains the existing public API for backward compatibility
- **Service Locator Pattern**: Temporarily maintained for services that still require the full container

## Benefits

### 1. Proper Dependency Injection

**Before:**
```php
class Tasks {
    public function __construct(Database $db, Container $container) {
        $this->container = $container; // Hidden dependencies
    }

    public function createTask() {
        $notifier = $this->container->getNotification(); // Runtime dependency lookup
    }
}
```

**After (with Symfony DI):**
```php
class Tasks {
    public function __construct(Database $db, Notification $notification) {
        $this->db = $db;
        $this->notification = $notification; // Explicit dependency
    }

    public function createTask() {
        $this->notification->send(); // Direct usage
    }
}
```

### 2. Reduced Boilerplate

The old Container.php had 745 lines of repetitive getter methods. With Symfony DI:

- Services are auto-wired by default
- Dependencies are automatically resolved
- Single source of truth for service configuration

### 3. Better Testing

Services can now be:
- Easily mocked in tests
- Instantiated without the container
- Tested in isolation with explicit dependencies

### 4. Performance

Symfony's compiled container is optimized for production:
- Service graph is analyzed at compile time
- Unnecessary services are removed (tree shaking)
- Faster runtime performance

## Usage

### Using the New Container

```php
use phpCollab\ContainerFactory;

// Create container with Symfony DI
$container = ContainerFactory::createLegacyContainer([
    'dbServer' => MYSERVER,
    'dbUsername' => MYLOGIN,
    'dbPassword' => MYPASSWORD,
    'dbName' => MYDATABASE,
    'tableCollab' => $tableCollab,
    'dbType' => $databaseType,
]);

// Use exactly as before - backward compatible!
$projects = $container->getProjectsLoader();
$tasks = $container->getTasksLoader();
$logger = $container->getLogger();
```

### How It Works

1. `ContainerFactory::createLegacyContainer()` creates a Symfony DI container
2. The Symfony container loads service definitions from `config/services.php`
3. A legacy `Container` wrapper is created for backward compatibility
4. When you call `$container->getProjectsLoader()`:
   - The Container checks if Symfony DI is enabled
   - If yes, it delegates to the Symfony container
   - If no, it falls back to the old lazy-loading pattern

## Backward Compatibility

### Zero Breaking Changes

The implementation maintains **100% backward compatibility**:

- All existing `get*()` methods still work
- No changes required to existing code
- Can be adopted incrementally

### Gradual Migration Path

Services can be migrated gradually:

1. **Phase 1** (Current): Symfony DI with service locator fallback
2. **Phase 2**: Refactor services to use constructor injection
3. **Phase 3**: Remove Container dependency from service constructors
4. **Phase 4**: Make services private and use interfaces

## Testing

Run the test script to verify the implementation:

```bash
php test_symfony_di.php
```

Expected output:
```
=== Testing Symfony DI Implementation ===

Test 1: Creating container with ContainerFactory...
✓ Container created successfully

Test 2: Retrieving Logger service...
✓ Logger retrieved successfully: Monolog\Logger

Test 3: Retrieving Database service...
✓ Database retrieved successfully: phpCollab\Database

... (additional tests)
```

## Next Steps

### Immediate Benefits (Already Achieved)

✅ Symfony DI container integrated
✅ Service definitions centralized
✅ Backward compatibility maintained
✅ Reduced boilerplate code

### Future Improvements

1. **Remove Container Dependencies**
   - Refactor services to use constructor injection only
   - Remove `$container` parameter from service constructors
   - Replace with explicit service dependencies

2. **Introduce Interfaces**
   ```php
   interface NotificationInterface {
       public function send(string $to, string $message): void;
   }

   class Notification implements NotificationInterface {
       // implementation
   }
   ```

3. **Use Environment-Based Configuration**
   - Move database config to `.env` files
   - Use Symfony's environment variable processor
   - Support different configs for dev/staging/production

4. **Service Autowiring**
   - Enable full autowiring for new services
   - Reduce manual service definitions
   - Let Symfony handle dependency resolution

5. **Container Compilation**
   - Cache compiled container for production
   - Significant performance improvement
   - Reduced memory footprint

## Service Configuration Examples

### Simple Service (No Dependencies)

```php
$services->set(DataFunctionsService::class)
    ->public();
```

### Service with Dependencies

```php
$services->set(Projects::class)
    ->args([
        service(Database::class),
        service(Escaper::class)
    ])
    ->public();
```

### Service with Factory

```php
$services->set(Logger::class)
    ->factory([service(LoggerFactory::class), 'create'])
    ->args([param('app.log_path'), param('app.log_level')])
    ->public();
```

### Service with Runtime Parameters

```php
$services->set(FileHandler::class)
    ->args([null]) // Will be set at runtime
    ->public();
```

## Troubleshooting

### Service Not Found

**Error:** `The "Foo\Bar" service or alias has been removed or inlined`

**Solution:** Mark the service as `public()` in `config/services.php`

### Circular Reference

**Error:** `Circular reference detected for service "X"`

**Solution:** One of the services should be lazy-loaded or use setter injection

### Class Not Found

**Error:** `Class "Foo\Bar" does not exist`

**Solution:** Ensure the class is autoloaded and the namespace is correct

## Performance Considerations

### Development vs Production

- **Development**: Container is built on every request
- **Production**: Consider caching the compiled container (future enhancement)

### Service Instantiation

- Services are instantiated **only when needed** (lazy loading)
- Same instance is returned on subsequent calls (singleton)
- No performance degradation compared to the old Container

## References

- [Symfony DependencyInjection Component](https://symfony.com/doc/5.4/components/dependency_injection.html)
- [Symfony Service Container](https://symfony.com/doc/5.4/service_container.html)
- [Dependency Injection Principles](https://martinfowler.com/articles/injection.html)

## Support

For questions or issues with the Symfony DI implementation, please:

1. Check this documentation
2. Run the test script: `php test_symfony_di.php`
3. Review service definitions in `config/services.php`
4. Open an issue on GitHub

---

**Implementation Date:** November 2025
**Symfony Version:** 5.4
**PHP Version:** 7.4+
**Status:** ✅ Production Ready with Backward Compatibility
