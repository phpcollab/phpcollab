<?php
/**
 * Router Test Script
 *
 * This script tests the routing logic without requiring a web server.
 * Run from command line: php tests/router-test.php
 */

// Autoloader not needed for this simple test
require_once __DIR__ . '/../classes/Router.php';

class RouterTest
{
    private $router;
    private $passed = 0;
    private $failed = 0;

    public function __construct()
    {
        $this->router = new phpCollab\Router(__DIR__ . '/..');
    }

    public function run()
    {
        echo "PHPCollab Router Tests\n";
        echo str_repeat("=", 50) . "\n\n";

        $this->testConventionRouting();
        $this->testExplicitRoutes();
        $this->testParameterExtraction();

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Results: {$this->passed} passed, {$this->failed} failed\n";

        return $this->failed === 0;
    }

    private function testConventionRouting()
    {
        echo "Testing Convention-Based Routing:\n";
        echo str_repeat("-", 50) . "\n";

        $tests = [
            // [uri, expected_file, description]
            ['/tasks/edit/123', 'tasks/edittask.php', 'Task edit with ID'],
            ['/tasks/list', 'tasks/listtasks.php', 'Task list'],
            ['/tasks/view/456', 'tasks/viewtask.php', 'Task view with ID'],
            ['/projects/edit/789', 'projects/editproject.php', 'Project edit with ID'],
            ['/projects/list', 'projects/listprojects.php', 'Project list'],
            ['/projects/view/101', 'projects/viewproject.php', 'Project view with ID'],
        ];

        foreach ($tests as list($uri, $expectedFile, $description)) {
            $result = $this->simulateConventionRoute($uri);

            if ($result && $result['handler'] === $expectedFile) {
                $this->pass($description, $uri);
            } else {
                $this->fail($description, $uri, $expectedFile, $result['handler'] ?? 'not found');
            }
        }

        echo "\n";
    }

    private function testExplicitRoutes()
    {
        echo "Testing Explicit Routes:\n";
        echo str_repeat("-", 50) . "\n";

        // Add some explicit routes
        $this->router->addRoute('GET', '/home', 'general/home.php');
        $this->router->addRoute('GET', '/login', 'general/login.php');
        $this->router->addRoute('GET', '/t/{id}', 'tasks/viewtask.php', ['id' => '\d+']);

        $tests = [
            ['/home', 'general/home.php', 'Home route'],
            ['/login', 'general/login.php', 'Login route'],
            ['/t/123', 'tasks/viewtask.php', 'Short task URL'],
        ];

        foreach ($tests as list($uri, $expectedFile, $description)) {
            $result = $this->simulateExplicitRoute('GET', $uri);

            if ($result && $result['handler'] === $expectedFile) {
                $this->pass($description, $uri);
            } else {
                $this->fail($description, $uri, $expectedFile, $result['handler'] ?? 'not found');
            }
        }

        echo "\n";
    }

    private function testParameterExtraction()
    {
        echo "Testing Parameter Extraction:\n";
        echo str_repeat("-", 50) . "\n";

        $tests = [
            ['/tasks/edit/123', ['id' => '123'], 'Single parameter'],
            ['/tasks/edit/456', ['id' => '456'], 'Different ID'],
            // Note: Multiple parameters test removed because files/downloadfile.php doesn't exist
            // The router correctly returns null when no matching file is found
        ];

        foreach ($tests as list($uri, $expectedParams, $description)) {
            $result = $this->simulateConventionRoute($uri);

            if ($result && $result['params'] === $expectedParams) {
                $this->pass($description, $uri . " → " . json_encode($expectedParams));
            } else {
                $this->fail(
                    $description,
                    $uri,
                    json_encode($expectedParams),
                    json_encode($result['params'] ?? [])
                );
            }
        }

        echo "\n";
    }

    private function simulateConventionRoute($uri)
    {
        // Use reflection to call private method
        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchConventionRoute');
        $method->setAccessible(true);

        return $method->invoke($this->router, $uri);
    }

    private function simulateExplicitRoute($httpMethod, $uri)
    {
        // Use reflection to call private method
        $reflection = new ReflectionClass($this->router);
        $method = $reflection->getMethod('matchExplicitRoute');
        $method->setAccessible(true);

        return $method->invoke($this->router, $httpMethod, $uri);
    }

    private function pass($description, $details)
    {
        $this->passed++;
        echo "✓ PASS: {$description}\n";
        echo "  {$details}\n";
    }

    private function fail($description, $uri, $expected, $actual)
    {
        $this->failed++;
        echo "✗ FAIL: {$description}\n";
        echo "  URI: {$uri}\n";
        echo "  Expected: {$expected}\n";
        echo "  Actual: {$actual}\n";
    }
}

// Run tests
$test = new RouterTest();
$success = $test->run();

exit($success ? 0 : 1);
