<?php

namespace phpCollab;

/**
 * Class Router
 *
 * Convention-based router with support for explicit route definitions.
 * Maintains backward compatibility with direct file access.
 *
 * Features:
 * - Convention-based routing (zero config for standard patterns)
 * - Optional explicit route definitions
 * - PATH_INFO support (works without .htaccess)
 * - Backward compatible with legacy direct file access
 * - Self-hosted friendly (download, extract, access)
 *
 * @package phpCollab
 */
class Router
{
    /**
     * @var array Explicit route definitions
     */
    private array $routes = [];

    /**
     * @var string Application root directory
     */
    private string $baseDir;

    /**
     * @var bool Enable debug mode for troubleshooting
     */
    private bool $debug = false;

    /**
     * Router constructor.
     *
     * @param string $baseDir Application root directory
     * @param bool $debug Enable debug mode
     */
    public function __construct(string $baseDir, bool $debug = false)
    {
        $this->baseDir = rtrim($baseDir, '/');
        $this->debug = $debug;
    }

    /**
     * Add an explicit route definition
     *
     * @param string|array $methods HTTP method(s): 'GET', 'POST', or ['GET', 'POST']
     * @param string $pattern URL pattern (e.g., '/tasks/edit/{id}')
     * @param string $handler File to handle the request (e.g., 'tasks/edittask.php')
     * @param array $constraints Parameter constraints (e.g., ['id' => '\d+'])
     * @return void
     */
    public function addRoute($methods, string $pattern, string $handler, array $constraints = []): void
    {
        $methods = (array) $methods;

        // Convert pattern to regex
        $regex = $this->patternToRegex($pattern, $constraints);

        foreach ($methods as $method) {
            $this->routes[] = [
                'method' => strtoupper($method),
                'pattern' => $pattern,
                'regex' => $regex,
                'handler' => $handler,
            ];
        }
    }

    /**
     * Load routes from a file
     *
     * @param string $routeFile Path to route definition file
     * @return void
     */
    public function loadRoutes(string $routeFile): void
    {
        if (!file_exists($routeFile)) {
            return;
        }

        $router = $this;
        require $routeFile;
    }

    /**
     * Load all route files from a directory
     *
     * @param string $routeDir Directory containing route files
     * @return void
     */
    public function loadRoutesFromDirectory(string $routeDir): void
    {
        if (!is_dir($routeDir)) {
            return;
        }

        foreach (glob($routeDir . '/*.php') as $routeFile) {
            $this->loadRoutes($routeFile);
        }
    }

    /**
     * Match the current request and return route information
     *
     * @param string|null $method HTTP method (defaults to current request)
     * @param string|null $uri Request URI (defaults to current request)
     * @return array|null Route information array with 'handler' and 'params' keys, or null if not found
     */
    public function match(?string $method = null, ?string $uri = null): ?array
    {
        $method = $method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $uri ?? $this->getRequestUri();

        if ($this->debug) {
            error_log("Router: Method={$method}, URI={$uri}");
        }

        // 1. Try explicit routes first
        $result = $this->matchExplicitRoute($method, $uri);
        if ($result) {
            return $result;
        }

        // 2. Try convention-based routing
        $result = $this->matchConventionRoute($uri);
        if ($result) {
            return $result;
        }

        // 3. Not found
        return null;
    }

    /**
     * Dispatch the current request (convenience method)
     *
     * Note: This includes the handler file from within the Router class scope.
     * For better compatibility, use match() and include the handler at global scope.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $result = $this->match();

        if ($result) {
            $this->executeRoute($result['handler'], $result['params']);
            return;
        }

        // Try direct file access (legacy compatibility)
        $uri = $this->getRequestUri();
        $directFile = $this->tryDirectFileAccess($uri);
        if ($directFile) {
            require $directFile;
            return;
        }

        // 404 Not Found
        $this->handleNotFound($uri);
    }

    /**
     * Get the request URI, handling both standard and PATH_INFO requests
     *
     * @return string Request URI
     */
    public function getRequestUri(): string
    {
        // Check for PATH_INFO first (e.g., /index.php/tasks/edit/123)
        if (!empty($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }

        // Otherwise parse REQUEST_URI
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove script name if present (e.g., /index.php/tasks -> /tasks)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    /**
     * Match against explicit route definitions
     *
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|null Route match result or null
     */
    private function matchExplicitRoute(string $method, string $uri): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, function($key) {
                    return is_string($key);
                }, ARRAY_FILTER_USE_KEY);

                if ($this->debug) {
                    error_log("Router: Matched explicit route - {$route['pattern']} -> {$route['handler']}");
                }

                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Match using convention-based routing
     *
     * Convention patterns:
     * - /module/action -> module/actionmodule.php (e.g., /tasks/edit -> tasks/edittask.php)
     * - /module/action/id -> module/actionmodule.php with id param
     * - /module/list -> module/listmodules.php (e.g., /tasks/list -> tasks/listtasks.php)
     *
     * @param string $uri Request URI
     * @return array|null Route match result or null
     */
    private function matchConventionRoute(string $uri): ?array
    {
        // Handle root
        if ($uri === '/' || $uri === '') {
            return null; // Let index.php handle this
        }

        // Parse URI: /module/action or /module/action/id or /module/action/param1/param2
        if (!preg_match('#^/([a-zA-Z0-9_-]+)(?:/([a-zA-Z0-9_-]+))?(?:/(.+))?$#', $uri, $matches)) {
            return null;
        }

        $module = $matches[1];
        $action = $matches[2] ?? 'index';
        $params = [];

        // Parse additional params (e.g., /tasks/edit/123 or /files/download/456/report.pdf)
        if (isset($matches[3])) {
            $paramString = trim($matches[3], '/');
            $paramParts = explode('/', $paramString);

            // First param is typically 'id'
            if (isset($paramParts[0])) {
                $params['id'] = $paramParts[0];
            }

            // Additional params can be accessed via $_GET['param1'], $_GET['param2'], etc.
            for ($i = 1; $i < count($paramParts); $i++) {
                $params["param{$i}"] = $paramParts[$i];
            }
        }

        // Try various file naming conventions
        $candidates = $this->getConventionCandidates($module, $action);

        foreach ($candidates as $file) {
            $fullPath = $this->baseDir . '/' . $file;
            if (file_exists($fullPath) && is_file($fullPath)) {
                if ($this->debug) {
                    error_log("Router: Matched convention route - {$uri} -> {$file}");
                }

                return [
                    'handler' => $file,
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    /**
     * Get candidate file paths based on naming conventions
     *
     * @param string $module Module name
     * @param string $action Action name
     * @return array List of candidate file paths
     */
    private function getConventionCandidates(string $module, string $action): array
    {
        $candidates = [];

        // Get singular form of module (e.g., tasks -> task, projects -> project)
        $singular = rtrim($module, 's');
        $hasPlural = ($singular !== $module);

        // Pattern 1a: module/actionsingular.php (e.g., tasks/edittask.php)
        // This is tried FIRST as it's the most common pattern in PHPCollab
        if ($hasPlural) {
            $candidates[] = "{$module}/{$action}{$singular}.php";
        }

        // Pattern 1b: module/actionmodule.php (e.g., calendar/addcalendar.php for non-plural modules)
        $candidates[] = "{$module}/{$action}{$module}.php";

        // Pattern 2: module/action.php (e.g., tasks/edit.php)
        $candidates[] = "{$module}/{$action}.php";

        // Pattern 3: For 'list' action -> module/listmodules.php (e.g., tasks/listtasks.php)
        if ($action === 'list') {
            $candidates[] = "{$module}/list{$module}.php";
            if ($hasPlural) {
                $candidates[] = "{$module}/list{$singular}s.php";
            }
        }

        // Pattern 4: For 'view' action (special handling)
        if ($action === 'view') {
            if ($hasPlural) {
                $candidates[] = "{$module}/view{$singular}.php";
            }
            $candidates[] = "{$module}/view{$module}.php";
        }

        // Pattern 5: module/action_singular.php (e.g., tasks/edit_task.php)
        if ($hasPlural) {
            $candidates[] = "{$module}/{$action}_{$singular}.php";
        }

        // Pattern 6: module/action_module.php
        $candidates[] = "{$module}/{$action}_{$module}.php";

        // Pattern 7: For index action
        if ($action === 'index') {
            $candidates[] = "{$module}/index.php";
        }

        return $candidates;
    }

    /**
     * Try to access a file directly (legacy compatibility)
     *
     * @param string $uri Request URI
     * @return string|null File path if found, null otherwise
     */
    public function tryDirectFileAccess(string $uri): ?string
    {
        // Security: Don't allow directory traversal
        if (strpos($uri, '..') !== false) {
            return null;
        }

        // Try direct file access
        $file = $this->baseDir . $uri;

        // If URI doesn't end in .php, try adding it
        $filesToTry = [$file];
        if (!str_ends_with($file, '.php')) {
            $filesToTry[] = $file . '.php';
        }

        foreach ($filesToTry as $tryFile) {
            if (file_exists($tryFile) && is_file($tryFile)) {
                // Additional security: ensure file is within baseDir
                $realPath = realpath($tryFile);
                $realBase = realpath($this->baseDir);

                if ($realPath && $realBase && strpos($realPath, $realBase) === 0) {
                    if ($this->debug) {
                        error_log("Router: Direct file access - {$uri} -> {$tryFile}");
                    }

                    // Return the file path instead of executing
                    return $tryFile;
                }
            }
        }

        return null;
    }

    /**
     * Execute a route handler
     *
     * @param string $handler File path to handler
     * @param array $params Route parameters
     * @return void
     */
    private function executeRoute(string $handler, array $params = []): void
    {
        // Merge params into $_GET for legacy compatibility
        $_GET = array_merge($_GET, $params);

        // Also make params available in $_REQUEST
        $_REQUEST = array_merge($_REQUEST, $params);

        $file = $this->baseDir . '/' . $handler;

        if (!file_exists($file)) {
            $this->handleNotFound("Handler file not found: {$handler}");
            return;
        }

        require $file;
    }

    /**
     * Handle 404 Not Found
     *
     * @param string $uri Requested URI
     * @return void
     */
    public function handleNotFound(string $uri): void
    {
        http_response_code(404);

        // Try to load a custom 404 page
        $notFoundPages = [
            $this->baseDir . '/views/404.php',
            $this->baseDir . '/errors/404.php',
            $this->baseDir . '/general/404.php',
        ];

        foreach ($notFoundPages as $page) {
            if (file_exists($page)) {
                require $page;
                return;
            }
        }

        // Fallback to simple 404 message
        echo "<!DOCTYPE html>\n<html>\n<head>\n<title>404 Not Found</title>\n</head>\n<body>\n";
        echo "<h1>404 Not Found</h1>\n";
        echo "<p>The requested page could not be found.</p>\n";
        echo "<p>URI: " . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . "</p>\n";
        echo "</body>\n</html>";
    }

    /**
     * Convert URL pattern to regex
     *
     * @param string $pattern URL pattern (e.g., '/tasks/edit/{id}')
     * @param array $constraints Parameter constraints (e.g., ['id' => '\d+'])
     * @return string Regex pattern
     */
    private function patternToRegex(string $pattern, array $constraints = []): string
    {
        // Escape forward slashes
        $regex = str_replace('/', '\/', $pattern);

        // Replace {param} with named capture groups
        $regex = preg_replace_callback('/\{(\w+)\}/', function($matches) use ($constraints) {
            $param = $matches[1];
            $constraint = $constraints[$param] ?? '[^\/]+';
            return "(?P<{$param}>{$constraint})";
        }, $regex);

        return '#^' . $regex . '$#';
    }
}
