<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: index.php


/**
 * PHPCollab Front Controller
 *
 * This file serves as the main entry point for the application.
 * It handles:
 * 1. Setup verification
 * 2. URL routing (convention-based with optional explicit routes)
 * 3. Session management and authentication
 * 4. Backward compatibility with legacy direct file access
 *
 * URL Formats Supported:
 * - Pretty URLs: /tasks/edit/123 (requires mod_rewrite in .htaccess)
 * - PATH_INFO: /index.php/tasks/edit/123 (works without mod_rewrite)
 * - Legacy: /tasks/edittask.php?id=123 (direct file access, always works)
 */

try {
    // Check if setup has been completed
    if (!file_exists("includes/settings.php")) {
        header('Location: installation/setup.php');
        exit;
    }

    // Determine if this is a routed request or root access
    $isRoutedRequest = !empty($_SERVER['PATH_INFO']) ||
                       (isset($_SERVER['REQUEST_URI']) &&
                        $_SERVER['REQUEST_URI'] !== '/' &&
                        $_SERVER['REQUEST_URI'] !== '/index.php' &&
                        strpos($_SERVER['REQUEST_URI'], '/index.php?') !== 0);

    // For root access, use existing redirect logic
    if (!$isRoutedRequest) {
        $checkSession = "false";
        $indexRedirect = "true";

        require_once 'includes/library.php';

        // Case: session fails or auth is false
        if ($session == "false" || !$session->get('auth') || $session->get('auth') === false) {
            phpCollab\Util::headerFunction("general/login.php");
        }

        if ($session->get('auth') === true) {
            phpCollab\Util::headerFunction("general/home.php");
        }

        // Default case just in case the above falls through
        phpCollab\Util::headerFunction("general/login.php");
        exit;
    }

    // For routed requests, initialize and dispatch
    $checkSession = "true"; // Most routed pages require session
    require_once 'includes/library.php';

    // Initialize router
    $router = new phpCollab\Router(__DIR__, $GLOBALS['logLevel'] <= 200); // Debug mode if log level is DEBUG/INFO

    // Load explicit route definitions if they exist
    if (file_exists(__DIR__ . '/routes.php')) {
        $router->loadRoutes(__DIR__ . '/routes.php');
    }

    // Load module-specific routes if directory exists
    if (is_dir(__DIR__ . '/routes')) {
        $router->loadRoutesFromDirectory(__DIR__ . '/routes');
    }

    // Dispatch the request
    $router->dispatch();

} catch (Exception $exception) {
    require_once dirname(__FILE__) . "/views/fatal_error.php";
    $date = new DateTime();
    $now = $date->format("[Y-m-d\TH:i:s.uP]");
    error_log($now . " FATAL ERROR: " . $exception . "\n");
    error_log($now . " FATAL ERROR: " . $exception . "\n", 3, "./logs/phpcollab.log");
}
