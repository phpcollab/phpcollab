# Translation System + AppConfig Integration Guide

## Overview

This guide shows how to integrate the Symfony Translation system (.po files) with AppConfig to create a unified, modern translation architecture while maintaining 100% backward compatibility.

## Current State

### Translation Branch (`claude/migrate-languages-po-files-011CUqPzgnhAyTpxq8Nv6psj`)

**Has:**
- Symfony Translation component
- .po files for 31 languages
- Helper functions: `trans()`, `getEnum()`, `help()`, `custom()`
- Translation domains: messages, help, enums, custom
- Management scripts for maintaining translations

**Pattern:**
```php
echo trans("welcome");                    // Strings
echo getEnum("priority", 1);              // Priority labels
echo getEnum("status", 2);                // Status labels
echo help("task_help");                   // Help text
```

### AppConfig Branch (`claude/implement-symfony-di-011CUqNvEdrXQ4STfGAq6cbE`)

**Has:**
- AppConfig class with type-safe configuration access
- Exposed via `$appConfig` in all scripts
- Helper variables: `$strings`, `$priority`, `$status`, etc.
- Pure dependency injection in service classes

**Pattern:**
```php
echo $appConfig->getString("welcome");
echo $appConfig->getPriorityLabel(1);
echo $appConfig->getStatusLabel(2);
```

## Integration Strategy

### Goal

Make AppConfig translation-aware so it delegates to the Symfony Translation system while maintaining all existing APIs.

### Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Script Files                         │
│  (controllers/views - tasks/*.php, projects/*.php)      │
└─────────────────────────────────────────────────────────┘
                           │
           ┌───────────────┴───────────────┐
           │                               │
           ▼                               ▼
    ┌─────────────┐              ┌─────────────────┐
    │   Helper    │              │   AppConfig     │
    │  Variables  │              │    Object       │
    │  ($strings) │              │  ($appConfig)   │
    └─────────────┘              └─────────────────┘
           │                               │
           └───────────────┬───────────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │   AppConfig     │
                  │     Class       │
                  │  (abstraction)  │
                  └─────────────────┘
                           │
                           ▼
                  ┌─────────────────┐
                  │    Symfony      │
                  │   Translator    │
                  │   (.po files)   │
                  └─────────────────┘
```

## Step-by-Step Integration

### Step 1: Enhance AppConfig Class

Update `classes/AppConfig.php` to be translation-aware:

```php
<?php

namespace phpCollab;

use Symfony\Component\Translation\Translator;

class AppConfig
{
    // ... existing properties ...

    private ?Translator $translator = null;

    /**
     * Inject translator after construction
     *
     * @param Translator $translator Symfony translator instance
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Check if translator is available
     *
     * @return bool
     */
    public function hasTranslator(): bool
    {
        return $this->translator !== null;
    }

    /**
     * Get translation string - delegates to translator if available
     *
     * @param string $key Translation key
     * @param array $parameters Optional parameters for interpolation
     * @return string Translated string
     */
    public function getString(string $key, array $parameters = []): string
    {
        if ($this->translator) {
            // Use Symfony Translation
            return trans($key, $parameters);
        }

        // Fallback to array-based (backward compatibility)
        $string = $this->strings[$key] ?? $key;

        // Simple parameter replacement for BC
        foreach ($parameters as $paramKey => $value) {
            $string = str_replace($paramKey, $value, $string);
        }

        return $string;
    }

    /**
     * Get all strings - for backward compatibility
     *
     * @return array
     */
    public function getStrings(): array
    {
        if ($this->translator) {
            // Get from translation system
            return getLegacyStringsArray();
        }

        // Fallback to stored array
        return $this->strings;
    }

    /**
     * Get priority label with translation support
     *
     * @param int $priority Priority value (0-5)
     * @return string Priority label
     */
    public function getPriorityLabel(int $priority): string
    {
        if ($this->translator) {
            return getEnum('priority', $priority);
        }

        return $this->priority[$priority] ?? '';
    }

    /**
     * Get all priority labels
     *
     * @return array
     */
    public function getPriority(): array
    {
        if ($this->translator) {
            // Get from translation system
            return getEnumArray('priority', range(0, 5));
        }

        return $this->priority;
    }

    /**
     * Get status label with translation support
     *
     * @param int $status Status value (0-4)
     * @return string Status label
     */
    public function getStatusLabel(int $status): string
    {
        if ($this->translator) {
            return getEnum('status', $status);
        }

        return $this->status[$status] ?? '';
    }

    /**
     * Get all status labels
     *
     * @return array
     */
    public function getStatus(): array
    {
        if ($this->translator) {
            // Get from translation system
            return getEnumArray('status', range(0, 4));
        }

        return $this->status;
    }

    /**
     * Get request status label with translation support
     *
     * @param int $status Request status value
     * @return string Status label
     */
    public function getRequestStatusLabel(int $status): string
    {
        if ($this->translator) {
            return getEnum('requestStatus', $status);
        }

        return $this->requestStatus[$status] ?? '';
    }

    /**
     * Get all request status labels
     *
     * @return array
     */
    public function getRequestStatus(): array
    {
        if ($this->translator) {
            // Get from translation system
            return getEnumArray('requestStatus', range(0, 2));
        }

        return $this->requestStatus;
    }

    /**
     * Get help text with translation support
     *
     * @param string $key Help key
     * @param array $parameters Optional parameters
     * @return string Help text
     */
    public function getHelpItem(string $key, array $parameters = []): string
    {
        if ($this->translator) {
            return help($key, $parameters);
        }

        $text = $this->help[$key] ?? '';

        // Simple parameter replacement
        foreach ($parameters as $paramKey => $value) {
            $text = str_replace($paramKey, $value, $text);
        }

        return $text;
    }

    /**
     * Get all help items
     *
     * @return array
     */
    public function getHelp(): array
    {
        if ($this->translator) {
            // Would need to load all help keys from .po files
            // For now, return empty array or implement full loading
            return [];
        }

        return $this->help;
    }
}
```

### Step 2: Update Container Class

Add method to get translator and inject into AppConfig:

```php
// classes/Container.php

/**
 * Get Symfony Translator instance
 *
 * @return \Symfony\Component\Translation\Translator
 */
public function getTranslator(): \Symfony\Component\Translation\Translator
{
    if ($this->isSymfonyDIEnabled()) {
        return $this->symfonyContainer->get(\Symfony\Component\Translation\Translator::class);
    }

    throw new \LogicException('Translator not available - Symfony DI not enabled');
}
```

### Step 3: Register Translator in DI Container

Update `config/services.php`:

```php
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\PoFileLoader;

// Translator Service
$services->set(Translator::class)
    ->args([param('app.language')])
    ->call('addLoader', ['po', service(PoFileLoader::class)])
    ->call('addResource', ['po', APP_ROOT . '/translations/messages/messages.en.po', 'en', 'messages'])
    ->call('addResource', ['po', APP_ROOT . '/translations/help/help.en.po', 'en', 'help'])
    // Add more languages as needed
    ->public();

$services->set(PoFileLoader::class);
```

### Step 4: Update library.php

Integrate translator with AppConfig:

```php
// includes/library.php

// After creating container...
$container = ContainerFactory::createLegacyContainer([...]);

// Include translation helper functions
require_once APP_ROOT . '/includes/translation.php';

// Get translator from container
$translator = $container->getTranslator();

// Get AppConfig and inject translator
$appConfig = $container->getAppConfig();
$appConfig->setTranslator($translator);

// ✅ Helper variables now come from AppConfig (which uses translator)
$strings = $appConfig->getStrings();        // Uses getLegacyStringsArray() → .po files
$priority = $appConfig->getPriority();      // Uses getEnumArray() → .po files
$status = $appConfig->getStatus();          // Uses getEnumArray() → .po files
$requestStatus = $appConfig->getRequestStatus();
$root = $appConfig->getRoot();
$setTitle = $appConfig->getSetTitle();
$byteUnits = $appConfig->getByteUnits();
```

### Step 5: Update Translation Helper Functions

Enhance `includes/translation.php` to work with AppConfig:

```php
/**
 * Get enum array - for backward compatibility with AppConfig
 *
 * @param string $enumType Enum type (status, priority, profil, etc.)
 * @param array $keys Array of numeric keys to retrieve
 * @param string|null $locale Override locale
 * @return array Associative array [key => translated value]
 */
function getEnumArray(string $enumType, array $keys, ?string $locale = null): array
{
    $result = [];
    foreach ($keys as $key) {
        $result[$key] = getEnum($enumType, $key, $locale);
    }
    return $result;
}

/**
 * Backward compatibility: Create $strings array from translations
 * DEPRECATED - Use trans() or $appConfig->getString() instead
 *
 * @return array
 */
function getLegacyStringsArray(): array
{
    static $strings = null;

    if ($strings !== null) {
        return $strings;
    }

    $strings = [];

    try {
        $translator = getTranslator();
        $catalogue = $translator->getCatalogue($translator->getLocale());

        // Get all messages from 'messages' domain
        $messages = $catalogue->all('messages');

        // Extract strings.* keys
        foreach ($messages as $key => $value) {
            if (strpos($key, 'strings.') === 0) {
                $shortKey = substr($key, 8); // Remove 'strings.' prefix
                $strings[$shortKey] = $value;
            }
        }

    } catch (Exception $e) {
        error_log("Failed to load legacy strings array: " . $e->getMessage());
    }

    return $strings;
}
```

## Migration Paths for Scripts

### Path 1: No Changes (Safest)

Existing scripts work unchanged:

```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// These still work - now powered by .po files via AppConfig!
echo $strings["welcome"];
echo $priority[$taskPriority];
echo $status[$taskStatus];
```

**Behind the scenes:**
- `$strings` comes from `$appConfig->getStrings()`
- `$appConfig` delegates to `getLegacyStringsArray()`
- `getLegacyStringsArray()` loads from .po files via Translator

### Path 2: Remove Redundant Assignments

Clean up redundant `$GLOBALS` assignments:

```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// ❌ REMOVE - these are already defined by library.php
// $strings = $GLOBALS["strings"];
// $priority = $GLOBALS["priority"];
// $status = $GLOBALS["status"];

// ✅ Just use the variables directly
echo $strings["welcome"];
echo $priority[$taskPriority];
```

### Path 3: Use AppConfig Methods

Use AppConfig for type safety:

```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// ✅ Type-safe with IDE autocomplete
echo $appConfig->getString("welcome");
echo $appConfig->getPriorityLabel($taskPriority);
echo $appConfig->getStatusLabel($taskStatus);
```

### Path 4: Use Translation Functions Directly

Use modern translation API:

```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// ✅ Direct translation system access
echo trans("strings.welcome");
echo trans("welcome");  // Auto-prefixes with 'strings.'
echo getEnum("priority", $taskPriority);
echo getEnum("status", $taskStatus);

// ✅ With parameters
echo trans("welcome_user", ['%name%' => $userName]);

// ✅ Help text
echo help("task_help");
```

### Path 5: Hybrid Approach (Recommended)

Mix approaches based on what makes sense:

```php
<?php
$checkSession = "true";
require_once '../includes/library.php';

// Simple display - use helper variable
$pageTitle = $strings["tasks"];

// Complex with parameters - use trans()
$welcomeMsg = trans("welcome_user", ['%name%' => $userName]);

// Enums - use AppConfig or getEnum()
$priorityLabel = $appConfig->getPriorityLabel($task["tas_priority"]);
// OR
$priorityLabel = getEnum("priority", $task["tas_priority"]);

// Type-safe config access - use AppConfig
$siteUrl = $appConfig->getRoot();
```

## Service Class Usage

Service classes should use AppConfig (already implemented):

```php
<?php

namespace phpCollab\Tasks;

use phpCollab\AppConfig;
use phpCollab\Database;

class Tasks
{
    private AppConfig $appConfig;

    public function __construct(Database $database, AppConfig $appConfig)
    {
        $this->database = $database;
        $this->appConfig = $appConfig;
    }

    public function getTaskStatusLabel(int $status): string
    {
        // AppConfig delegates to translator automatically
        return $this->appConfig->getStatusLabel($status);
    }

    public function formatNotification(array $task): string
    {
        // Use getString with parameters
        return $this->appConfig->getString("task_created", [
            '%task%' => $task['tas_name']
        ]);
    }
}
```

## Testing the Integration

### Test 1: Verify Helper Variables Work

```php
// test-integration.php
<?php
require_once 'includes/library.php';

// Test helper variables (backward compatibility)
assert(!empty($strings), '$strings should be defined');
assert(!empty($priority), '$priority should be defined');
assert(!empty($status), '$status should be defined');

echo "✅ Helper variables work\n";
echo "Welcome: " . $strings["welcome"] . "\n";
echo "Priority 1: " . $priority[1] . "\n";
echo "Status 0: " . $status[0] . "\n";
```

### Test 2: Verify AppConfig Works

```php
// Test AppConfig (modern approach)
assert($appConfig->hasTranslator(), 'AppConfig should have translator');
echo "✅ AppConfig has translator\n";

echo "AppConfig getString: " . $appConfig->getString("welcome") . "\n";
echo "AppConfig priority: " . $appConfig->getPriorityLabel(1) . "\n";
echo "AppConfig status: " . $appConfig->getStatusLabel(0) . "\n";
```

### Test 3: Verify Translation Functions Work

```php
// Test translation functions (direct access)
echo "✅ Translation functions work\n";
echo "trans(): " . trans("welcome") . "\n";
echo "getEnum(): " . getEnum("priority", 1) . "\n";
echo "help(): " . help("project_help") . "\n";
```

### Test 4: Verify Language Switching

```php
// Test language switching
$translator = $container->getTranslator();
$translator->setLocale('fr');

echo "✅ French translations:\n";
echo "Welcome (FR): " . trans("welcome") . "\n";
echo "Priority 1 (FR): " . getEnum("priority", 1) . "\n";
```

## Merging the Branches

### Step 1: Ensure Current Branch is Clean

```bash
git checkout claude/implement-symfony-di-011CUqNvEdrXQ4STfGAq6cbE
git status  # Should be clean
```

### Step 2: Create Integration Branch

```bash
git checkout -b claude/integrate-translation-appconfig
```

### Step 3: Merge Translation Branch

```bash
git merge claude/migrate-languages-po-files-011CUqPzgnhAyTpxq8Nv6psj
# Resolve any conflicts
```

### Step 4: Apply Integration Changes

1. Update `classes/AppConfig.php` with translator support (as shown above)
2. Update `classes/Container.php` with `getTranslator()` method
3. Update `config/services.php` to register Translator
4. Update `includes/library.php` to inject translator into AppConfig
5. Update `includes/translation.php` with `getEnumArray()` helper

### Step 5: Test Integration

```bash
php test-integration.php
```

### Step 6: Commit and Push

```bash
git add -A
git commit -m "feat: Integrate Symfony Translation with AppConfig

Merges translation system (.po files) with AppConfig to create
unified translation architecture.

- AppConfig delegates to Symfony Translator when available
- Helper variables ($strings, $priority, $status) now powered by .po files
- 100% backward compatibility maintained
- Scripts can use: helper vars, AppConfig methods, or trans() directly"

git push -u origin claude/integrate-translation-appconfig
```

## Benefits of This Integration

### 1. Unified Architecture
- Single source of truth for translations
- Consistent API across codebase
- Clear abstraction layers

### 2. Backward Compatibility
- All existing scripts work unchanged
- Helper variables continue to function
- Gradual migration path

### 3. Modern Translation Features
- Professional .po file management
- Parameter interpolation
- Pluralization support
- Language fallbacks
- Translation management tools

### 4. Type Safety
- AppConfig provides type hints
- IDE autocomplete support
- Clear method signatures

### 5. Maintainability
- Easy to add new languages
- Translation tools for managing strings
- Clear separation of concerns

## Best Practices

### For New Code

**DO:**
```php
// ✅ Use trans() for strings with parameters
echo trans("welcome_user", ['%name%' => $userName]);

// ✅ Use getEnum() for status/priority
echo getEnum("priority", $priority);

// ✅ Use AppConfig for type safety
$siteUrl = $appConfig->getRoot();
```

**DON'T:**
```php
// ❌ Don't access $GLOBALS directly
echo $GLOBALS["strings"]["welcome"];

// ❌ Don't hardcode strings
echo "Welcome, " . $userName;
```

### For Existing Code

**When touching a file:**
1. Remove redundant `$GLOBALS` assignments
2. Consider switching to `trans()` for complex strings
3. Keep using helper variables for simple cases

**Don't force changes:**
- No need to refactor working code
- Migrate naturally as you work on files
- Helper variables will continue to work

## Troubleshooting

### Issue: Translator not available

**Error:** `Translator not available - Symfony DI not enabled`

**Solution:** Ensure Symfony DI is properly initialized in library.php

### Issue: Missing translations

**Error:** Translation key returned unchanged

**Solution:**
1. Check .po files contain the key
2. Verify locale is set correctly
3. Run `scripts/translation/sync-translations.php`

### Issue: Helper variables empty

**Error:** `$strings` is empty array

**Solution:**
1. Verify `getLegacyStringsArray()` is working
2. Check .po files are loaded
3. Ensure translator is injected into AppConfig

## Next Steps

After integration is complete:

1. **Test thoroughly** - Run through all major features
2. **Update documentation** - Document the new patterns
3. **Train team** - Show developers both approaches
4. **Gradual migration** - Convert files as you touch them
5. **Monitor** - Watch for translation issues in logs

## See Also

- `/docs/APPCONFIG_MIGRATION.md` - AppConfig migration guide
- `/includes/translation.php` - Translation helper functions
- `/classes/AppConfig.php` - AppConfig implementation
- Translation branch docs (TRANSLATION_COMPLETE.md, etc.)
