# AppConfig Migration Guide

## Overview

AppConfig replaces the `$GLOBALS` anti-pattern with type-safe dependency injection. All service classes now use AppConfig instead of accessing `$GLOBALS` directly.

For script files (controllers/views), AppConfig is automatically available after including `library.php`.

## Quick Reference

### Available in All Scripts

After `require_once '../includes/library.php';`, you have access to:

**Modern Approach:**
- `$appConfig` - Full AppConfig object with type-safe methods

**Backward Compatible Variables:**
- `$strings` - Translation strings (sourced from AppConfig)
- `$priority` - Priority labels (sourced from AppConfig)
- `$status` - Status labels (sourced from AppConfig)
- `$requestStatus` - Support request status labels
- `$root` - Application root URL
- `$setTitle` - Site title
- `$byteUnits` - File size units

## Migration Examples

### Strings (Translation Keys)

**Before:**
```php
echo $GLOBALS["strings"]["welcome"];
echo $GLOBALS["strings"]["error"];
$message = $GLOBALS["strings"]["task_created"];
```

**After (Option 1 - Use helper variable):**
```php
echo $strings["welcome"];  // $strings already defined by library.php
echo $strings["error"];
$message = $strings["task_created"];
```

**After (Option 2 - Use AppConfig):**
```php
echo $appConfig->getString("welcome");
echo $appConfig->getString("error");
$message = $appConfig->getString("task_created");
```

### Priority Labels

**Before:**
```php
$priorityLabel = $GLOBALS["priority"][$taskDetail["tas_priority"]];
echo "Priority: " . $GLOBALS["priority"][1];
```

**After (Option 1 - Use helper variable):**
```php
$priorityLabel = $priority[$taskDetail["tas_priority"]];
echo "Priority: " . $priority[1];
```

**After (Option 2 - Use AppConfig):**
```php
$priorityLabel = $appConfig->getPriorityLabel($taskDetail["tas_priority"]);
echo "Priority: " . $appConfig->getPriorityLabel(1);
```

### Status Labels

**Before:**
```php
$statusLabel = $GLOBALS["status"][$taskDetail["tas_status"]];
if ($GLOBALS["status"][$status] == "Completed") { ... }
```

**After (Option 1 - Use helper variable):**
```php
$statusLabel = $status[$taskDetail["tas_status"]];
if ($status[$statusValue] == "Completed") { ... }
```

**After (Option 2 - Use AppConfig):**
```php
$statusLabel = $appConfig->getStatusLabel($taskDetail["tas_status"]);
if ($appConfig->getStatusLabel($statusValue) == "Completed") { ... }
```

### Request Status (Support Requests)

**Before:**
```php
$requestStatusLabel = $GLOBALS["requestStatus"][$request["sr_status"]];
```

**After (Option 1 - Use helper variable):**
```php
$requestStatusLabel = $requestStatus[$request["sr_status"]];
```

**After (Option 2 - Use AppConfig):**
```php
$requestStatusLabel = $appConfig->getRequestStatusLabel($request["sr_status"]);
```

### Application Root URL

**Before:**
```php
$link = $GLOBALS["root"] . "/tasks/viewtask.php?id=" . $taskId;
header("Location: " . $GLOBALS["root"] . "/home.php");
```

**After (Option 1 - Use helper variable):**
```php
$link = $root . "/tasks/viewtask.php?id=" . $taskId;
header("Location: " . $root . "/home.php");
```

**After (Option 2 - Use AppConfig):**
```php
$link = $appConfig->getRoot() . "/tasks/viewtask.php?id=" . $taskId;
header("Location: " . $appConfig->getRoot() . "/home.php");
```

### Site Title

**Before:**
```php
$pageTitle = $GLOBALS["setTitle"] . " - Tasks";
echo "Welcome to " . $GLOBALS["setTitle"];
```

**After (Option 1 - Use helper variable):**
```php
$pageTitle = $setTitle . " - Tasks";
echo "Welcome to " . $setTitle;
```

**After (Option 2 - Use AppConfig):**
```php
$pageTitle = $appConfig->getSetTitle() . " - Tasks";
echo "Welcome to " . $appConfig->getSetTitle();
```

### File Size Units

**Before:**
```php
$units = $GLOBALS["byteUnits"];
echo $size . " " . $GLOBALS["byteUnits"][$unitIndex];
```

**After (Option 1 - Use helper variable):**
```php
$units = $byteUnits;
echo $size . " " . $byteUnits[$unitIndex];
```

**After (Option 2 - Use AppConfig):**
```php
$units = $appConfig->getByteUnits();
echo $size . " " . $appConfig->getByteUnits()[$unitIndex];
```

## Complete AppConfig API

### Configuration Access

```php
// Core settings
$appConfig->getStrings()              // array - All translation strings
$appConfig->getString($key)           // string - Single translation string
$appConfig->getRoot()                 // string - Application root URL
$appConfig->getSetTitle()             // string - Site title
$appConfig->getSupportEmail()         // string - Support email address

// Status arrays
$appConfig->getPriority()             // array - All priority labels
$appConfig->getPriorityLabel($num)    // string - Single priority label
$appConfig->getStatus()               // array - All status labels
$appConfig->getStatusLabel($num)      // string - Single status label
$appConfig->getRequestStatus()        // array - All request status labels
$appConfig->getRequestStatusLabel($num) // string - Single request status label

// Language and localization
$appConfig->getLang()                 // string - Current language code (e.g., 'en')

// Notification settings
$appConfig->getNotificationMethod()   // string - 'mail' or 'smtp'

// Site settings
$appConfig->isSitePublished()         // bool - Is site published?

// Database settings
$appConfig->getDatabaseType()         // string - Database type (e.g., 'mysql')
$appConfig->getGmtTimezone()          // string - GMT timezone
$appConfig->getTableCollab()          // array - All table names
$appConfig->getTableName($name)       // string - Single table name

// File/FTP settings
$appConfig->getByteUnits()            // array - File size units ['B', 'KB', 'MB', etc.]
$appConfig->getMkdirMethod()          // string - Directory creation method
$appConfig->getFtpRoot()              // string - FTP root path

// LDAP settings
$appConfig->isUseLDAP()               // bool - Is LDAP enabled?
$appConfig->getConfigLDAP()           // array - LDAP configuration

// UI/Filtering settings
$appConfig->getProjectsFilter()       // mixed - Projects filter setting

// Sorting configuration
$appConfig->getSortingOrders()        // array - Sorting orders
$appConfig->getSortingFields()        // array - Sorting fields
$appConfig->getSortingArrows()        // array - Sorting arrows
$appConfig->getSortingStyles()        // array - Sorting styles
$appConfig->getExplode()              // mixed - Explode delimiter
$appConfig->getHelp()                 // array - All help text
$appConfig->getHelpItem($key)         // string - Single help text item
```

## Migration Strategy

### Recommended Approach

1. **Don't force changes** - Existing code works fine with helper variables
2. **Remove redundant assignments** - When you touch a file, remove lines like:
   ```php
   $strings = $GLOBALS["strings"];  // ❌ Remove - already defined by library.php
   ```
3. **Use AppConfig for new code** - Get type safety and IDE autocomplete
4. **Gradual migration** - Migrate files as you work on them naturally

### Example: Full File Migration

**Before:**
```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

$strings = $GLOBALS["strings"];      // ❌ Redundant
$priority = $GLOBALS["priority"];    // ❌ Redundant
$status = $GLOBALS["status"];        // ❌ Redundant
$root = $GLOBALS["root"];            // ❌ Redundant

echo "<h1>" . $strings["tasks"] . "</h1>";
echo "Priority: " . $priority[$taskPriority];
echo "Status: " . $status[$taskStatus];
$link = $root . "/tasks/viewtask.php?id=" . $id;
```

**After (Option 1 - Minimal changes):**
```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// ✅ Removed redundant assignments - variables already defined!

echo "<h1>" . $strings["tasks"] . "</h1>";
echo "Priority: " . $priority[$taskPriority];
echo "Status: " . $status[$taskStatus];
$link = $root . "/tasks/viewtask.php?id=" . $id;
```

**After (Option 2 - Modern approach):**
```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// ✅ Use AppConfig for type safety
echo "<h1>" . $appConfig->getString("tasks") . "</h1>";
echo "Priority: " . $appConfig->getPriorityLabel($taskPriority);
echo "Status: " . $appConfig->getStatusLabel($taskStatus);
$link = $appConfig->getRoot() . "/tasks/viewtask.php?id=" . $id;
```

## Common Patterns

### In Templates/Views

**Displaying translation strings:**
```php
<!-- Option 1: Helper variable -->
<h1><?= $strings["dashboard"] ?></h1>

<!-- Option 2: AppConfig -->
<h1><?= $appConfig->getString("dashboard") ?></h1>
```

**Displaying priority/status:**
```php
<!-- Option 1: Helper variable -->
<span class="priority"><?= $priority[$task["tas_priority"]] ?></span>

<!-- Option 2: AppConfig -->
<span class="priority"><?= $appConfig->getPriorityLabel($task["tas_priority"]) ?></span>
```

### In Controllers

**Building links:**
```php
// Option 1: Helper variable
$redirectUrl = $root . "/tasks/listtasks.php?project=" . $projectId;

// Option 2: AppConfig
$redirectUrl = $appConfig->getRoot() . "/tasks/listtasks.php?project=" . $projectId;
```

**Building email messages:**
```php
// Option 1: Helper variable
$subject = $setTitle . " - " . $strings["new_task"];
$body = $strings["task_notification"] . "\n\n";

// Option 2: AppConfig
$subject = $appConfig->getSetTitle() . " - " . $appConfig->getString("new_task");
$body = $appConfig->getString("task_notification") . "\n\n";
```

## Benefits of Using AppConfig

1. **Type Safety** - IDE autocomplete and type hints
2. **Explicit Dependencies** - Clear what configuration you're using
3. **Easier Testing** - Can mock AppConfig in tests
4. **Better Refactoring** - IDE can track all usages
5. **Documentation** - Method names are self-documenting

## FAQ

**Q: Do I need to change existing scripts?**
A: No! All existing scripts work without changes. Helper variables are automatically defined.

**Q: What about scripts that do `$strings = $GLOBALS["strings"]`?**
A: They still work, but the assignment is redundant (can be removed to clean up code).

**Q: Should I use helper variables or AppConfig methods?**
A: Both work! Helper variables are fine for simple cases. Use AppConfig methods for type safety and IDE support.

**Q: Can I mix both approaches?**
A: Yes! Use whatever makes sense for each situation.

**Q: Do I need to update old code immediately?**
A: No. Migrate naturally as you work on files. No rush!

## See Also

- `/classes/AppConfig.php` - Full AppConfig implementation
- `/includes/library.php` - Where AppConfig is initialized and exposed
- `/classes/Container.php` - Container::getAppConfig() method
