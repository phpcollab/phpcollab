<?php
/**
 * Test script for Symfony DI implementation
 *
 * This script tests the new Symfony DI Container implementation
 * to ensure services are properly configured and can be retrieved.
 */

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/vendor/autoload.php';

use phpCollab\ContainerFactory;
use phpCollab\Database;
use Monolog\Logger;
use Laminas\Escaper\Escaper;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Members\Members;

echo "=== Testing Symfony DI Implementation ===\n\n";

// Mock configuration for testing
$mockConfig = [
    'dbServer' => 'localhost',
    'dbUsername' => 'test_user',
    'dbPassword' => 'test_pass',
    'dbName' => 'test_db',
    'tableCollab' => ['projects' => 'projects', 'tasks' => 'tasks'],
    'dbType' => 'mysql',
];

// Test 1: Create container using factory
echo "Test 1: Creating container with ContainerFactory...\n";
try {
    $container = ContainerFactory::createLegacyContainer($mockConfig);
    echo "✓ Container created successfully\n\n";
} catch (Exception $e) {
    echo "✗ Failed to create container: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Retrieve Logger service
echo "Test 2: Retrieving Logger service...\n";
try {
    $logger = $container->getLogger();
    if ($logger instanceof Logger) {
        echo "✓ Logger retrieved successfully: " . get_class($logger) . "\n\n";
    } else {
        echo "✗ Logger is not a Monolog\\Logger instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Logger: " . $e->getMessage() . "\n\n";
}

// Test 3: Retrieve Database service
echo "Test 3: Retrieving Database service...\n";
try {
    $database = $container->getPDO();
    if ($database instanceof Database) {
        echo "✓ Database retrieved successfully: " . get_class($database) . "\n\n";
    } else {
        echo "✗ Database is not a phpCollab\\Database instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Database: " . $e->getMessage() . "\n\n";
}

// Test 4: Retrieve Escaper service
echo "Test 4: Retrieving Escaper service...\n";
try {
    $escaper = $container->getEscaperService();
    if ($escaper instanceof Escaper) {
        echo "✓ Escaper retrieved successfully: " . get_class($escaper) . "\n\n";
    } else {
        echo "✗ Escaper is not a Laminas\\Escaper\\Escaper instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Escaper: " . $e->getMessage() . "\n\n";
}

// Test 5: Retrieve Projects service
echo "Test 5: Retrieving Projects service...\n";
try {
    $projects = $container->getProjectsLoader();
    if ($projects instanceof Projects) {
        echo "✓ Projects retrieved successfully: " . get_class($projects) . "\n\n";
    } else {
        echo "✗ Projects is not a phpCollab\\Projects\\Projects instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Projects: " . $e->getMessage() . "\n\n";
}

// Test 6: Retrieve Tasks service
echo "Test 6: Retrieving Tasks service...\n";
try {
    $tasks = $container->getTasksLoader();
    if ($tasks instanceof Tasks) {
        echo "✓ Tasks retrieved successfully: " . get_class($tasks) . "\n\n";
    } else {
        echo "✗ Tasks is not a phpCollab\\Tasks\\Tasks instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Tasks: " . $e->getMessage() . "\n\n";
}

// Test 7: Retrieve Members service
echo "Test 7: Retrieving Members service...\n";
try {
    $members = $container->getMembersLoader();
    if ($members instanceof Members) {
        echo "✓ Members retrieved successfully: " . get_class($members) . "\n\n";
    } else {
        echo "✗ Members is not a phpCollab\\Members\\Members instance\n";
    }
} catch (Exception $e) {
    echo "✗ Failed to retrieve Members: " . $e->getMessage() . "\n\n";
}

// Test 8: Verify singleton behavior
echo "Test 8: Verifying singleton behavior (same instance returned)...\n";
try {
    $database1 = $container->getPDO();
    $database2 = $container->getPDO();
    if ($database1 === $database2) {
        echo "✓ Singleton behavior verified - same instance returned\n\n";
    } else {
        echo "✗ Different instances returned - singleton pattern not working\n";
    }
} catch (Exception $e) {
    echo "✗ Failed singleton test: " . $e->getMessage() . "\n\n";
}

echo "=== All tests completed ===\n";
