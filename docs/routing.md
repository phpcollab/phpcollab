# PHPCollab Routing System

## Overview

PHPCollab uses a **hybrid routing system** that combines:

1. **Convention-based routing** - Zero configuration for standard patterns
2. **Explicit route definitions** - Custom routes when needed
3. **Legacy compatibility** - Direct file access still works

This design maintains the "download, extract, access" simplicity of self-hosted applications while providing modern URL routing.

---

## URL Formats Supported

PHPCollab supports **three URL formats** simultaneously:

### 1. Pretty URLs (Requires mod_rewrite)
```
/tasks/edit/123
/projects/view/456
/calendar/list
```
**Requires:** `.htaccess` with `mod_rewrite` enabled (included in distribution)

### 2. PATH_INFO URLs (No server config required)
```
/index.php/tasks/edit/123
/index.php/projects/view/456
/index.php/calendar/list
```
**Requires:** Nothing - works on 99% of shared hosting

### 3. Legacy URLs (Always works)
```
/tasks/edittask.php?id=123
/projects/viewproject.php?id=456
/calendar/listcalendar.php
```
**Requires:** Nothing - direct file access (backward compatible)

---

## Convention-Based Routing

The router automatically discovers routes based on file naming conventions. **No route definitions needed!**

### How It Works

The router translates URLs to files using patterns:

| URL Pattern | File Pattern | Example |
|-------------|--------------|---------|
| `/module/action` | `module/actionmodule.php` | `/tasks/edit` → `tasks/edittask.php` |
| `/module/action/id` | `module/actionmodule.php` | `/tasks/edit/123` → `tasks/edittask.php` |
| `/module/list` | `module/listmodules.php` | `/tasks/list` → `tasks/listtasks.php` |
| `/module/view/id` | `module/viewmodule.php` | `/projects/view/456` → `projects/viewproject.php` |
| `/module/action` | `module/action.php` | `/calendar/list` → `calendar/list.php` |

### Convention Patterns Tried (in order)

For URL `/tasks/edit/123`, the router tries:

1. `tasks/edittask.php` ✅ (matches!)
2. `tasks/edit.php`
3. `tasks/edit_task.php`

For URL `/tasks/list`, the router tries:

1. `tasks/listtask.php`
2. `tasks/listtasks.php` ✅ (matches!)
3. `tasks/list.php`

### Parameters

URL parameters are automatically extracted and made available in `$_GET`:

```php
// URL: /tasks/edit/123
// $_GET['id'] = '123'

// URL: /files/download/456/report.pdf
// $_GET['id'] = '456'
// $_GET['param1'] = 'report.pdf'
```

---

## Explicit Route Definitions

For routes that don't follow conventions, define them explicitly.

### Main Routes File: `routes.php`

```php
// routes.php

// Home and auth routes
$router->addRoute('GET', '/home', 'general/home.php');
$router->addRoute(['GET', 'POST'], '/login', 'general/login.php');
$router->addRoute('GET', '/logout', 'general/logout.php');

// Short URLs
$router->addRoute('GET', '/t/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
$router->addRoute('GET', '/p/{id}', 'projects/viewproject.php', ['id' => '\d+']);

// Custom routes
$router->addRoute('GET', '/dashboard', 'general/home.php');
$router->addRoute('GET', '/settings', 'preferences/updateuser.php');
```

### Module-Specific Routes: `routes/` Directory

Organize routes by module in the `routes/` directory:

```
routes/
├── tasks.php           # Task module routes
├── projects.php        # Project module routes
├── calendar.php        # Calendar module routes
└── files.php           # File module routes
```

**Example: routes/tasks.php**

```php
<?php
// routes/tasks.php

$router->addRoute('GET', '/tasks', 'tasks/listtasks.php');
$router->addRoute(['GET', 'POST'], '/tasks/new', 'tasks/newtask.php');
$router->addRoute(['GET', 'POST'], '/tasks/{id}/edit', 'tasks/edittask.php', ['id' => '\d+']);
$router->addRoute('GET', '/tasks/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
$router->addRoute('GET', '/tasks/{id}/delete', 'tasks/deletetask.php', ['id' => '\d+']);
```

### Route Definition Syntax

```php
$router->addRoute($methods, $pattern, $handler, $constraints);
```

**Parameters:**

- `$methods` - HTTP method(s): `'GET'`, `'POST'`, or `['GET', 'POST']`
- `$pattern` - URL pattern with `{param}` placeholders
- `$handler` - File path relative to application root
- `$constraints` - (Optional) Array of regex patterns for parameters

**Examples:**

```php
// Single method
$router->addRoute('GET', '/tasks/{id}', 'tasks/viewtask.php', ['id' => '\d+']);

// Multiple methods
$router->addRoute(['GET', 'POST'], '/login', 'general/login.php');

// Multiple parameters
$router->addRoute('GET', '/files/{id}/{name}', 'files/download.php', [
    'id' => '\d+',
    'name' => '[a-zA-Z0-9._-]+'
]);

// No constraints (any value allowed)
$router->addRoute('GET', '/search/{query}', 'search/results.php');
```

---

## Routing Priority

The router tries to match requests in this order:

1. **Explicit routes** (defined in `routes.php` or `routes/*.php`)
2. **Convention-based routing** (auto-discovery)
3. **Direct file access** (legacy compatibility)
4. **404 Not Found**

This allows you to:
- Override convention routes with explicit definitions
- Maintain backward compatibility with legacy URLs
- Gradually migrate from legacy to routed URLs

---

## Migration Strategy

You can adopt the routing system incrementally without breaking existing code:

### Phase 1: Enable Routing (Zero Breaking Changes)

1. ✅ Router is already installed
2. ✅ All legacy URLs still work (`/tasks/edittask.php?id=123`)
3. ✅ New URLs also work (`/tasks/edit/123`)
4. ✅ No code changes needed

### Phase 2: Start Using New URLs

Update internal links gradually:

```php
// Old (still works)
<a href="/tasks/edittask.php?id=<?php echo $task['id']; ?>">Edit</a>

// New (also works)
<a href="/tasks/edit/<?php echo $task['id']; ?>">Edit</a>
```

### Phase 3: Create URL Helper (Optional)

```php
// classes/UrlHelper.php
class UrlHelper {
    public static function taskEdit($id) {
        return "/tasks/edit/{$id}";
    }

    public static function projectView($id) {
        return "/projects/view/{$id}";
    }
}

// Usage
<a href="<?php echo UrlHelper::taskEdit($task['id']); ?>">Edit</a>
```

---

## .htaccess Configuration

The included `.htaccess` file provides progressive enhancement:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Allow direct access to existing files
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    # Route everything else through index.php
    RewriteRule ^(.*)$ index.php/$1 [L,QSA]
</IfModule>
```

**What this does:**

- ✅ If `mod_rewrite` available → Pretty URLs work (`/tasks/edit/123`)
- ✅ If not available → PATH_INFO works (`/index.php/tasks/edit/123`)
- ✅ Existing files served directly (CSS, JS, images, legacy PHP files)
- ✅ No setup required - just works™

### Installing in a Subdirectory

If PHPCollab is installed in a subdirectory (e.g., `/phpcollab/`), update the RewriteBase:

```apache
RewriteBase /phpcollab/
```

---

## Working Without mod_rewrite

If your server doesn't have `mod_rewrite` enabled, the router still works using PATH_INFO:

**URLs will look like:**
```
/index.php/tasks/edit/123
/index.php/projects/view/456
/index.php/calendar/list
```

**No configuration needed** - it just works!

To make internal links work in both modes:

```php
// Helper function
function url($path) {
    // Detect if mod_rewrite is available
    if (isset($_SERVER['PATH_INFO']) ||
        (isset($_SERVER['REQUEST_URI']) && !str_contains($_SERVER['REQUEST_URI'], 'index.php'))) {
        return $path; // Pretty URLs
    }
    return '/index.php' . $path; // PATH_INFO
}

// Usage
<a href="<?php echo url('/tasks/edit/' . $task['id']); ?>">Edit</a>
```

---

## Debugging Routes

### Enable Debug Mode

Debug mode is automatically enabled when the log level is DEBUG or INFO (200 or lower).

**In `includes/settings.php`:**

```php
$logLevel = 200; // 200 = INFO, shows routing debug messages
```

### Debug Output

When debug mode is enabled, routing decisions are logged to `logs/phpcollab.log`:

```
Router: Method=GET, URI=/tasks/edit/123
Router: Matched convention route - /tasks/edit/123 -> tasks/edittask.php
```

### View All Routes

To see all defined explicit routes, add debug code to `index.php`:

```php
// After loading routes
if ($GLOBALS['logLevel'] <= 200) {
    error_log("Loaded routes: " . print_r($router, true));
}
```

---

## Troubleshooting

### Pretty URLs Don't Work

**Problem:** `/tasks/edit/123` returns 404, but `/index.php/tasks/edit/123` works

**Solution:** Your server doesn't have `mod_rewrite` enabled or `.htaccess` is not being read.

**Options:**
1. Use PATH_INFO URLs (`/index.php/tasks/edit/123`)
2. Enable `mod_rewrite` (contact hosting provider)
3. Ensure `AllowOverride All` in Apache config

### Route Not Found

**Problem:** URL returns 404 even though file exists

**Solutions:**

1. **Check file naming** - Ensure file follows convention:
   - `/tasks/edit` → `tasks/edittask.php` (not `tasks/edit.php`)

2. **Check file permissions** - Ensure file is readable

3. **Enable debug mode** - See what the router is trying:
   ```php
   $logLevel = 200; // in settings.php
   ```

4. **Define explicit route** - Override convention:
   ```php
   $router->addRoute('GET', '/tasks/edit/{id}', 'tasks/edit.php', ['id' => '\d+']);
   ```

### Legacy URLs Stop Working

**Problem:** `/tasks/edittask.php?id=123` returns 404 after enabling routing

**This shouldn't happen!** The `.htaccess` is configured to allow direct file access.

**Check:**
1. File exists and is readable
2. `.htaccess` has the correct `RewriteCond` rules (see above)
3. Apache has `AllowOverride All`

### Parameters Not Available

**Problem:** `$_GET['id']` is empty in routed requests

**Cause:** Route pattern doesn't capture the parameter

**Solution:** Ensure URL has the parameter:
```
/tasks/edit/123  ✅ (id = 123)
/tasks/edit      ❌ (no id)
```

Or make parameter optional in your code:
```php
$id = $_GET['id'] ?? null;
if (!$id) {
    // Handle missing ID
}
```

---

## API Reference

### Router Class

**Location:** `classes/Router.php`

#### Constructor

```php
$router = new phpCollab\Router(string $baseDir, bool $debug = false);
```

#### Methods

```php
// Add a route
$router->addRoute($methods, $pattern, $handler, $constraints = []);

// Load routes from a file
$router->loadRoutes($routeFile);

// Load all routes from directory
$router->loadRoutesFromDirectory($routeDir);

// Dispatch the current request
$router->dispatch();
```

---

## Examples

### Example 1: Simple Route

```php
// routes.php
$router->addRoute('GET', '/about', 'general/about.php');
```

**URL:** `/about`
**Handler:** `general/about.php`

### Example 2: Route with Parameter

```php
// routes.php
$router->addRoute('GET', '/tasks/{id}', 'tasks/viewtask.php', ['id' => '\d+']);
```

**URL:** `/tasks/123`
**Handler:** `tasks/viewtask.php`
**Parameters:** `$_GET['id'] = '123'`

### Example 3: Multiple Parameters

```php
// routes.php
$router->addRoute('GET', '/projects/{project_id}/tasks/{task_id}',
    'tasks/viewtask.php',
    ['project_id' => '\d+', 'task_id' => '\d+']
);
```

**URL:** `/projects/456/tasks/123`
**Handler:** `tasks/viewtask.php`
**Parameters:** `$_GET['project_id'] = '456'`, `$_GET['task_id'] = '123'`

### Example 4: Multiple HTTP Methods

```php
// routes.php
$router->addRoute(['GET', 'POST'], '/contact', 'general/contact.php');
```

**URL:** `/contact` (both GET and POST)
**Handler:** `general/contact.php`

### Example 5: API Routes

```php
// routes/api.php
$router->addRoute('GET', '/api/tasks', 'api/tasks/list.php');
$router->addRoute('POST', '/api/tasks', 'api/tasks/create.php');
$router->addRoute('GET', '/api/tasks/{id}', 'api/tasks/get.php', ['id' => '\d+']);
$router->addRoute('PUT', '/api/tasks/{id}', 'api/tasks/update.php', ['id' => '\d+']);
$router->addRoute('DELETE', '/api/tasks/{id}', 'api/tasks/delete.php', ['id' => '\d+']);
```

---

## Best Practices

### 1. Use Conventions When Possible

✅ **Good** - Let convention routing handle it:
```
File: tasks/edittask.php
URL: /tasks/edit/123
No route definition needed!
```

❌ **Unnecessary** - Don't define routes for standard patterns:
```php
$router->addRoute('GET', '/tasks/edit/{id}', 'tasks/edittask.php'); // Not needed!
```

### 2. Organize Routes by Module

✅ **Good** - Separate files:
```
routes/tasks.php      - Task routes
routes/projects.php   - Project routes
routes/calendar.php   - Calendar routes
```

❌ **Bad** - Everything in one file:
```
routes.php - 1000+ lines of routes for all modules
```

### 3. Use Consistent URL Patterns

✅ **Good** - RESTful patterns:
```
GET    /tasks          - List all tasks
GET    /tasks/{id}     - View task
POST   /tasks          - Create task
PUT    /tasks/{id}     - Update task
DELETE /tasks/{id}     - Delete task
```

❌ **Bad** - Inconsistent patterns:
```
GET /tasks/list
GET /task_view/{id}
POST /create-task
POST /tasks/update?id=123
```

### 4. Constrain Parameters

✅ **Good** - Validate in route:
```php
$router->addRoute('GET', '/tasks/{id}', 'tasks/view.php', ['id' => '\d+']);
```

❌ **Bad** - No validation:
```php
$router->addRoute('GET', '/tasks/{id}', 'tasks/view.php'); // Allows /tasks/abc
```

### 5. Maintain Backward Compatibility

When refactoring, keep old URLs working:

```php
// Old route (keep working)
// /tasks/edittask.php?id=123

// New route (add)
$router->addRoute('GET', '/tasks/edit/{id}', 'tasks/edittask.php', ['id' => '\d+']);
```

Both URLs now work! Remove the old one in a major version update.

---

## Performance Considerations

### Route Caching (Future Enhancement)

The router currently compiles regex patterns on each request. For better performance in production, routes could be cached:

```php
// Future enhancement
$router->setCacheDir('/path/to/cache');
$router->enableCache(true);
```

This would store compiled route patterns in a cache file.

### Number of Routes

- **Convention routing** - Near-zero overhead (file_exists checks)
- **Explicit routes** - Linear search (fast for <100 routes)
- **Recommendation** - Keep explicit routes under 100, use conventions for everything else

---

## Summary

The PHPCollab routing system provides:

✅ **Convention-based routing** - Zero config for standard patterns
✅ **Explicit routes** - Full control when needed
✅ **Progressive enhancement** - Works with or without mod_rewrite
✅ **Backward compatibility** - Legacy URLs still work
✅ **Self-hosted friendly** - "Download, extract, access" simplicity
✅ **No breaking changes** - Gradual adoption

**Next steps:**
1. Start using new URLs in your links
2. Define custom routes for special cases
3. Gradually migrate away from legacy URLs
4. Enjoy cleaner, more maintainable URLs!
