<?php
/**
 * Example Module-Specific Routes
 *
 * This is an example of how to organize routes by module.
 * Each module can have its own route file in the routes/ directory.
 *
 * Recommended file naming:
 * - routes/tasks.php - Task module routes
 * - routes/projects.php - Project module routes
 * - routes/calendar.php - Calendar module routes
 * - etc.
 *
 * This file is just an example and can be safely deleted.
 * Remove or rename this file to prevent these routes from loading.
 */

// Example: Custom task routes
// Uncomment to use these routes instead of convention-based routing for tasks

// $router->addRoute('GET', '/tasks', 'tasks/listtasks.php');
// $router->addRoute(['GET', 'POST'], '/tasks/new', 'tasks/newtask.php');
// $router->addRoute(['GET', 'POST'], '/tasks/{id}/edit', 'tasks/edittask.php', ['id' => '\d+']);
// $router->addRoute('GET', '/tasks/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
// $router->addRoute('GET', '/tasks/{id}/delete', 'tasks/deletetask.php', ['id' => '\d+']);

// Example: Project routes with nested resources
// $router->addRoute('GET', '/projects', 'projects/listprojects.php');
// $router->addRoute('GET', '/projects/{id}', 'projects/viewproject.php', ['id' => '\d+']);
// $router->addRoute('GET', '/projects/{id}/tasks', 'tasks/listtasks.php', ['id' => '\d+']);
// $router->addRoute('GET', '/projects/{id}/files', 'linkedfiles/listfiles.php', ['id' => '\d+']);

// Example: Calendar routes
// $router->addRoute('GET', '/calendar', 'calendar/viewcalendar.php');
// $router->addRoute(['GET', 'POST'], '/calendar/event/new', 'calendar/addevent.php');
// $router->addRoute(['GET', 'POST'], '/calendar/event/{id}', 'calendar/editevent.php', ['id' => '\d+']);

// Example: File download route with additional parameters
// $router->addRoute('GET', '/download/{id}/{name}', 'files/download.php', ['id' => '\d+', 'name' => '[a-zA-Z0-9._-]+']);

// Example: API-style routes
// $router->addRoute('GET', '/api/tasks/{id}', 'api/tasks/get.php', ['id' => '\d+']);
// $router->addRoute('POST', '/api/tasks', 'api/tasks/create.php');
// $router->addRoute('PUT', '/api/tasks/{id}', 'api/tasks/update.php', ['id' => '\d+']);
// $router->addRoute('DELETE', '/api/tasks/{id}', 'api/tasks/delete.php', ['id' => '\d+']);
