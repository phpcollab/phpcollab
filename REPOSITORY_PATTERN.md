# Repository Pattern Guide

## Overview

The Repository Pattern provides an abstraction layer between business logic (services) and data access (gateways/database). This document explains how to implement repositories in phpCollab.

## Why Use the Repository Pattern?

### Benefits

1. **Separation of Concerns**
   - Services focus on business logic
   - Repositories handle data access
   - Clear boundaries between layers

2. **Easier Testing**
   - Mock the repository interface
   - No database or gateway mocking needed
   - Faster unit tests

3. **Flexibility**
   - Swap data sources (SQL → NoSQL, API, cache)
   - Multiple implementations of same interface
   - Easy to extend

4. **Better Encapsulation**
   - Data access implementation details hidden
   - Domain-friendly method names
   - Consistent API across repositories

5. **Type Safety**
   - Interface contract enforced by PHP
   - IDE auto-completion support
   - Compile-time type checking

## Architecture

```
┌─────────────────┐
│  Service Layer  │  ← Business Logic
│   (Members)     │
└────────┬────────┘
         │ depends on
         ▼
┌──────────────────────────────┐
│  Repository Interface        │  ← Contract
│  (MembersRepositoryInterface)│
└────────┬─────────────────────┘
         │ implemented by
         ▼
┌─────────────────────────┐
│  Repository Impl        │  ← Data Access
│  (MembersRepository)    │
└────────┬────────────────┘
         │ delegates to
         ▼
┌─────────────────┐
│  Gateway        │  ← Database Queries
│ (MembersGateway)│
└─────────────────┘
```

## Implementation Guide

### Step 1: Create the Repository Interface

The interface defines the contract for data operations.

```php
<?php

namespace phpCollab\YourModule;

interface YourRepositoryInterface
{
    /**
     * Find entity by ID
     *
     * @param int $id Entity ID
     * @return array|null Entity data or null if not found
     */
    public function findById(int $id): ?array;

    /**
     * Find all entities
     *
     * @param string|null $sorting Sorting clause
     * @return array Array of entities
     */
    public function findAll(?string $sorting = null): array;

    /**
     * Create a new entity
     *
     * @param array $data Entity data
     * @return string ID of created entity
     */
    public function create(array $data): string;

    /**
     * Update an existing entity
     *
     * @param int $id Entity ID
     * @param array $data Entity data to update
     * @return void
     */
    public function update(int $id, array $data): void;

    /**
     * Delete an entity
     *
     * @param int $id Entity ID
     * @return void
     */
    public function delete(int $id): void;
}
```

**Naming Conventions:**
- **Query methods**: `find*()`, `search()`, `exists()`
- **Command methods**: `create()`, `update()`, `delete()`
- **Domain-friendly names**: `findById()` not `getYourEntityById()`

### Step 2: Create the Repository Implementation

The concrete implementation wraps the existing Gateway.

```php
<?php

namespace phpCollab\YourModule;

use phpCollab\Database;
use phpCollab\RequestData;

class YourRepository implements YourRepositoryInterface
{
    private YourGateway $gateway;

    public function __construct(Database $database, RequestData $requestData)
    {
        $this->gateway = new YourGateway($database, $requestData);
    }

    public function findById(int $id): ?array
    {
        $result = $this->gateway->getYourEntityById($id);
        return $result ?: null;
    }

    public function findAll(?string $sorting = null): array
    {
        return $this->gateway->getAllYourEntities($sorting);
    }

    public function create(array $data): string
    {
        return $this->gateway->addYourEntity($data);
    }

    public function update(int $id, array $data): void
    {
        $this->gateway->updateYourEntity($id, $data);
    }

    public function delete(int $id): void
    {
        $this->gateway->deleteYourEntity($id);
    }
}
```

### Step 3: Update the Service

Update the service to depend on the repository interface.

**Before:**
```php
class YourService
{
    private YourGateway $gateway;

    public function __construct(
        Database $database,
        Logger $logger,
        RequestData $requestData
    ) {
        $this->gateway = new YourGateway($database, $requestData);
    }

    public function getById($id)
    {
        return $this->gateway->getYourEntityById($id);
    }
}
```

**After:**
```php
class YourService
{
    private YourRepositoryInterface $repository;
    private Logger $logger;

    public function __construct(
        YourRepositoryInterface $repository,
        Logger $logger
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function getById($id)
    {
        $this->logger->info('Fetching entity', ['id' => $id]);
        return $this->repository->findById($id);
    }
}
```

**Key Changes:**
- ✅ Inject `YourRepositoryInterface` (not Gateway)
- ✅ No more Database or RequestData in service constructor
- ✅ Service focuses on business logic
- ✅ All data access delegated to repository

### Step 4: Register in DI Container

Update `config/services.php` to register the repository.

```php
use phpCollab\YourModule\YourRepository;
use phpCollab\YourModule\YourRepositoryInterface;
use phpCollab\YourModule\YourService;

// Register the repository interface and implementation
$services->set(YourRepositoryInterface::class, YourRepository::class)
    ->args([
        service(Database::class),
        service(RequestData::class)
    ])
    ->public();

// Alias for easier access
$services->alias(YourRepository::class, YourRepositoryInterface::class)
    ->public();

// Update service to use repository
$services->set(YourService::class)
    ->args([
        service(YourRepositoryInterface::class),
        service(Logger::class)
    ])
    ->public();
```

### Step 5: Update Tests

Update tests to mock the repository interface.

**Before:**
```php
public function testServiceLogic()
{
    $mocks = [
        'database' => $this->createMock(Database::class),
        'logger' => $this->createMock(Logger::class),
        'requestData' => $this->createMock(RequestData::class),
    ];

    // Complex database mocking...
    $mocks['database']->expects($this->once())
        ->method('query');
    $mocks['database']->expects($this->once())
        ->method('bind');
    $mocks['database']->expects($this->once())
        ->method('single')
        ->willReturn(['id' => 1]);

    $service = new YourService(
        $mocks['database'],
        $mocks['logger'],
        $mocks['requestData']
    );

    $result = $service->getById(1);
}
```

**After:**
```php
public function testServiceLogic()
{
    $mocks = [
        'repository' => $this->createMock(YourRepositoryInterface::class),
        'logger' => $this->createMock(Logger::class),
    ];

    // Simple repository mocking!
    $mocks['repository']->expects($this->once())
        ->method('findById')
        ->with(1)
        ->willReturn(['id' => 1]);

    $service = new YourService(
        $mocks['repository'],
        $mocks['logger']
    );

    $result = $service->getById(1);
}
```

**Benefits:**
- ✅ Simpler test setup
- ✅ No database/requestData mocking
- ✅ Focuses on business logic
- ✅ Faster tests

## Real-World Example: Members Service

See the implementation in:
- `classes/Members/MembersRepositoryInterface.php` (interface)
- `classes/Members/MembersRepository.php` (implementation)
- `classes/Members/Members.php` (service using repository)
- `tests/unit/Members/MembersTest.php` (tests)

## Best Practices

### 1. Keep Repositories Focused

✅ **DO:**
```php
interface MembersRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function create(array $data): string;
}
```

❌ **DON'T:**
```php
interface MembersRepositoryInterface
{
    public function findById(int $id): ?array;
    public function sendWelcomeEmail(int $id): void; // ← Business logic!
    public function calculateMembershipFee(int $id): float; // ← Business logic!
}
```

**Rule:** Repositories = Data access only. Business logic belongs in services.

### 2. Use Domain-Friendly Names

✅ **DO:**
```php
findById()
findByEmail()
exists()
create()
update()
delete()
```

❌ **DON'T:**
```php
getMemberById()      // Too verbose
selectMemberByEmail() // SQL-ish
insertMember()       // SQL-ish
```

### 3. Return Appropriate Types

✅ **DO:**
```php
public function findById(int $id): ?array; // null if not found
public function exists(string $email): bool; // true/false
public function findAll(): array; // empty array if none
```

❌ **DON'T:**
```php
public function findById(int $id); // mixed? array? false?
public function exists(string $email); // 0/1? true/false?
```

### 4. Keep Interface Stable

Once an interface is published, avoid breaking changes. Add new methods instead:

✅ **DO:**
```php
// v1
interface MembersRepositoryInterface
{
    public function findById(int $id): ?array;
}

// v2 - add new method
interface MembersRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByIds(array $ids): array; // ← New method
}
```

❌ **DON'T:**
```php
// v1
interface MembersRepositoryInterface
{
    public function findById(int $id): ?array;
}

// v2 - breaking change!
interface MembersRepositoryInterface
{
    public function findById(int $id, bool $includeDeleted = false): ?array; // ← Changed signature!
}
```

### 5. Document with PHPDoc

Always document interface methods with clear PHPDoc:

```php
/**
 * Find a member by their unique ID
 *
 * @param int $id Member ID
 * @return array|null Member data array or null if not found
 * @throws InvalidArgumentException if ID is invalid
 */
public function findById(int $id): ?array;
```

## Testing Repositories

Repositories can have their own integration tests:

```php
class MembersRepositoryTest extends Unit
{
    private MembersRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        // Use real database for integration testing
        $this->repository = new MembersRepository(
            $this->getModule('Database'),
            new RequestData(/* test data */)
        );
    }

    public function testFindByIdReturnsCorrectData()
    {
        // Given: A member exists in test database
        $memberId = $this->createTestMember(['name' => 'John Doe']);

        // When: Finding by ID
        $result = $this->repository->findById($memberId);

        // Then: Correct data returned
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['mem_name']);
    }
}
```

## Migration Checklist

When migrating a service to Repository Pattern:

- [ ] Create `YourRepositoryInterface` with all data access methods
- [ ] Create `YourRepository` implementing the interface
- [ ] Update service constructor to inject interface
- [ ] Replace all gateway calls with repository calls
- [ ] Register repository in `config/services.php`
- [ ] Update unit tests to mock repository interface
- [ ] Ensure all tests pass (100%)
- [ ] Document any breaking changes (if needed)
- [ ] Update related documentation

## Future Repositories

Candidates for Repository Pattern implementation:

1. **TasksRepository** - Task data access
2. **ProjectsRepository** - Project data access
3. **SubtasksRepository** - Subtask data access
4. **TeamsRepository** - Team data access
5. **OrganizationsRepository** - Organization data access

Each repository should follow this same pattern for consistency!

## Questions?

For questions or clarifications about the Repository Pattern:
1. Review the `MembersRepository` implementation as a reference
2. Check tests in `tests/unit/Members/MembersTest.php`
3. Consult this guide
4. Ask the team!

---

**Remember:** Repository Pattern = Clean separation between business logic and data access! 🎯
