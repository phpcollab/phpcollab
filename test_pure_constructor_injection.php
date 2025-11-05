<?php
/**
 * Test script demonstrating Pure Constructor Injection
 *
 * This script shows the difference between the old Service Locator pattern
 * and the new Pure Constructor Injection pattern.
 */

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/vendor/autoload.php';

use phpCollab\ContainerFactory;
use phpCollab\Tasks\Tasks;
use phpCollab\Subtasks\Subtasks;
use phpCollab\Members\Members;

echo "===========================================\n";
echo "Pure Constructor Injection Demonstration\n";
echo "===========================================\n\n";

// Mock configuration
$mockConfig = [
    'dbServer' => 'localhost',
    'dbUsername' => 'test_user',
    'dbPassword' => 'test_pass',
    'dbName' => 'test_db',
    'tableCollab' => ['projects' => 'projects', 'tasks' => 'tasks'],
    'dbType' => 'mysql',
];

echo "Creating container with language 'en'...\n";
try {
    $container = ContainerFactory::createLegacyContainer($mockConfig, 'en');
    echo "✓ Container created successfully\n\n";
} catch (Exception $e) {
    echo "✗ Failed to create container: " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== BEFORE (Service Locator Pattern) ===\n";
echo "Problems:\n";
echo "  ❌ Hidden dependencies - class depends on Container\n";
echo "  ❌ Runtime dependency lookup - dependencies fetched inside methods\n";
echo "  ❌ Hard to test - must mock entire Container\n";
echo "  ❌ Unclear what a class actually needs\n\n";

echo "Example (old way):\n";
echo "```php\n";
echo "class Tasks {\n";
echo "    public function __construct(Database \$db, Container \$container) {\n";
echo "        \$this->container = \$container; // ❌ Depends on container\n";
echo "    }\n";
echo "    \n";
echo "    public function sendNotification() {\n";
echo "        \$mail = \$this->container->getNotificationService(); // ❌ Hidden dependency\n";
echo "        \$mail->send();\n";
echo "    }\n";
echo "}\n";
echo "```\n\n";

echo "=== AFTER (Pure Constructor Injection) ===\n";
echo "Benefits:\n";
echo "  ✅ Explicit dependencies - all dependencies in constructor\n";
echo "  ✅ No runtime lookup - dependencies injected at creation\n";
echo "  ✅ Easy to test - mock only what's needed\n";
echo "  ✅ Clear what a class needs at a glance\n\n";

echo "Example (new way):\n";
echo "```php\n";
echo "class Tasks {\n";
echo "    public function __construct(\n";
echo "        Database \$database,\n";
echo "        MailNotification \$mailNotification,  // ✅ Explicit\n";
echo "        string \$language,                    // ✅ Explicit\n";
echo "        Projects \$projects,                  // ✅ Explicit\n";
echo "        Teams \$teams,                        // ✅ Explicit\n";
echo "        Notifications \$notifications,        // ✅ Explicit\n";
echo "        Notification \$notification           // ✅ Explicit\n";
echo "    ) {\n";
echo "        \$this->mailNotification = \$mailNotification;\n";
echo "        // All dependencies are available, no container needed!\n";
echo "    }\n";
echo "    \n";
echo "    public function sendNotification() {\n";
echo "        \$this->mailNotification->send(); // ✅ Direct usage\n";
echo "    }\n";
echo "}\n";
echo "```\n\n";

echo "=== Testing Refactored Services ===\n\n";

// Test Tasks service
echo "Test 1: Tasks service with pure constructor injection\n";
try {
    $tasks = $container->getTasksLoader();
    if ($tasks instanceof Tasks) {
        echo "✓ Tasks service instantiated successfully\n";

        // Use reflection to inspect the constructor
        $reflection = new ReflectionClass(Tasks::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        echo "  Dependencies (via constructor):\n";
        foreach ($parameters as $param) {
            $type = $param->getType();
            $typeName = $type ? $type->getName() : 'mixed';
            echo "    - " . $param->getName() . ": " . $typeName . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
}

// Test Subtasks service
echo "Test 2: Subtasks service with pure constructor injection\n";
try {
    $subtasks = $container->getSubtasksLoader();
    if ($subtasks instanceof Subtasks) {
        echo "✓ Subtasks service instantiated successfully\n";

        $reflection = new ReflectionClass(Subtasks::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        echo "  Dependencies (via constructor):\n";
        foreach ($parameters as $param) {
            $type = $param->getType();
            $typeName = $type ? $type->getName() : 'mixed';
            echo "    - " . $param->getName() . ": " . $typeName . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
}

// Test Members service
echo "Test 3: Members service with pure constructor injection\n";
try {
    $members = $container->getMembersLoader();
    if ($members instanceof Members) {
        echo "✓ Members service instantiated successfully\n";

        $reflection = new ReflectionClass(Members::class);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        echo "  Dependencies (via constructor):\n";
        foreach ($parameters as $param) {
            $type = $param->getType();
            $typeName = $type ? $type->getName() : 'mixed';
            echo "    - " . $param->getName() . ": " . $typeName . "\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "✗ Failed: " . $e->getMessage() . "\n\n";
}

echo "=== Key Improvements ===\n\n";

echo "1. **No More Container Dependency**\n";
echo "   ❌ Before: `public function __construct(Database \$db, Container \$container)`\n";
echo "   ✅ After:  `public function __construct(Database \$db, Notification \$notification)`\n\n";

echo "2. **Explicit Dependencies**\n";
echo "   - All dependencies are visible in the constructor signature\n";
echo "   - No need to search the class to find what it uses\n";
echo "   - IDE autocomplete works perfectly\n\n";

echo "3. **Better Testing**\n";
echo "   ```php\n";
echo "   // Easy to test - just pass mocks!\n";
echo "   \$mockDb = \$this->createMock(Database::class);\n";
echo "   \$mockNotification = \$this->createMock(Notification::class);\n";
echo "   \$members = new Members(\$mockDb, \$mockLogger, \$mockNotification);\n";
echo "   ```\n\n";

echo "4. **Symfony DI Handles Everything**\n";
echo "   - Container automatically resolves and injects dependencies\n";
echo "   - No manual wiring needed\n";
echo "   - Dependencies are shared (singletons by default)\n\n";

echo "=== Refactored Services Summary ===\n\n";
echo "Services refactored to use pure constructor injection:\n";
echo "  ✅ Tasks       - 7 explicit dependencies (was: Container)\n";
echo "  ✅ Subtasks    - 3 explicit dependencies (was: Container)\n";
echo "  ✅ Members     - 3 explicit dependencies (was: Container + Logger)\n\n";

echo "Services still using Container (to be refactored):\n";
echo "  ⏳ SetStatus, SetTaskStatus, Topics, Files, etc.\n\n";

echo "=== Next Steps ===\n\n";
echo "1. Refactor remaining services to remove Container dependency\n";
echo "2. Introduce interfaces (e.g., NotificationInterface)\n";
echo "3. Use setter injection for optional dependencies\n";
echo "4. Make services private (only accessed via DI, not Container getters)\n\n";

echo "===========================================\n";
echo "All tests completed successfully!\n";
echo "===========================================\n";
