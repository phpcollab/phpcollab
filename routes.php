<?php
/**
 * Main Route Definitions
 *
 * This file contains explicit route definitions for special cases that don't follow
 * the standard convention-based routing patterns.
 *
 * Convention-based routing automatically handles most routes without definition:
 * - /tasks/edit/123 -> tasks/edittask.php (auto-discovered)
 * - /projects/view/456 -> projects/viewproject.php (auto-discovered)
 * - /calendar/list -> calendar/listcalendar.php (auto-discovered)
 *
 * Only define routes here if:
 * 1. The URL pattern doesn't match file naming conventions
 * 2. You need custom parameter handling
 * 3. You want a specific URL structure
 *
 * Route Definition Syntax:
 * $router->addRoute(methods, pattern, handler, constraints);
 *
 * Parameters:
 * - methods: 'GET', 'POST', or ['GET', 'POST']
 * - pattern: URL pattern with {param} placeholders
 * - handler: File path relative to application root
 * - constraints: Array of regex patterns for parameters (optional)
 *
 * Examples:
 * $router->addRoute('GET', '/tasks/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
 * $router->addRoute(['GET', 'POST'], '/login', 'general/login.php');
 * $router->addRoute('GET', '/projects/{id}/files', 'files/listfiles.php', ['id' => '\d+']);
 */

// Special routes that don't follow conventions

// Home and authentication routes
$router->addRoute('GET', '/home', 'general/home.php');
$router->addRoute(['GET', 'POST'], '/login', 'general/login.php');
$router->addRoute('GET', '/logout', 'general/logout.php');

// Alternative route patterns (if you want shorter URLs)
// $router->addRoute('GET', '/t/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
// $router->addRoute('GET', '/p/{id}', 'projects/viewproject.php', ['id' => '\d+']);

// Custom routes that don't match file structure
// $router->addRoute('GET', '/dashboard', 'general/home.php');
// $router->addRoute('GET', '/settings', 'preferences/updateuser.php');

// Module-specific routes can be loaded from routes/ directory:
// See routes/example.php for more examples
