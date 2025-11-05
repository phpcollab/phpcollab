# PHP Array to .po Files Migration Plan

## Executive Summary

This document outlines the complete migration plan for converting phpCollab's custom PHP array-based translation system to industry-standard `.po` (Portable Object) files using Symfony Translation component.

**Current State**: 31 languages, ~93 PHP files, custom array-based system
**Target State**: .po file-based translation system with Symfony Translation
**Estimated Effort**: 65-95 hours over 7-8 weeks
**Risk Level**: Medium (extensive codebase changes, multi-language testing required)

---

## Table of Contents

1. [Overview](#overview)
2. [Goals & Benefits](#goals--benefits)
3. [Technical Architecture](#technical-architecture)
4. [Migration Phases](#migration-phases)
5. [Implementation Steps](#implementation-steps)
6. [File Structure](#file-structure)
7. [Code Changes](#code-changes)
8. [Testing Strategy](#testing-strategy)
9. [Rollback Plan](#rollback-plan)
10. [Success Criteria](#success-criteria)

---

## 1. Overview

### Current System

**File Structure:**
```
/languages/
├── lang_*.php      (31 files, 770 lines each)
├── help_*.php      (31 files)
└── custom_*.php    (13 files)
```

**Translation Loading:**
- Files loaded in `includes/library.php` (lines 143-151)
- Arrays exposed via `$GLOBALS`
- Fallback: English always loaded first
- Usage: `$strings["key"]`, `$status[0]`, etc.

**Supported Languages:** 31 total
```
ar, az, pt-br, bg, ca, zh, zh-tw, cs-iso, cs-win1250, da, nl, en,
et, fr, de, hu, is, in, it, ja, ko, lv, no, pl, pt, ro, ru,
sk-win1250, es, tr, uk
```

### Target System

**File Structure:**
```
/translations/
├── messages/
│   ├── messages.en.po
│   ├── messages.fr.po
│   └── ... (31 languages)
├── help/
│   ├── help.en.po
│   ├── help.fr.po
│   └── ... (31 languages)
├── enums/
│   ├── enums.en.po
│   └── ... (31 languages)
└── custom/
    ├── custom.en.po
    └── ... (13 languages)
```

**Translation Loading:**
- Symfony Translation component
- Helper functions: `trans()`, `transChoice()`
- Centralized translator in Container
- Same fallback mechanism (English default)

---

## 2. Goals & Benefits

### Primary Goals

1. **Standardization**: Use industry-standard .po file format
2. **Tooling**: Enable professional translation tools (Poedit, Crowdin, Weblate)
3. **Maintainability**: Improve translator workflow and code maintainability
4. **Features**: Add support for pluralization, context, and comments

### Benefits

| Benefit | Impact | Priority |
|---------|--------|----------|
| Professional translation tools | High | High |
| Better translator experience | High | High |
| Plural form support | Medium | Medium |
| Context for ambiguous strings | Medium | Medium |
| Version control friendly | Medium | Low |
| Performance (compiled .mo files) | Low | Low |
| Industry standard format | High | High |

### Non-Goals

- ❌ Complete rewrite of the application
- ❌ Changing supported languages (keep all 31)
- ❌ Modifying UI/UX
- ❌ Database schema changes

---

## 3. Technical Architecture

### Technology Stack

**Translation Library**: Symfony Translation Component v6.0+
- **Why**: Modern, PSR-compatible, well-maintained, flexible
- **Alternative considered**: Native PHP gettext (rejected - less flexible)

**File Format**: `.po` (Portable Object) files
- **Compilation**: Optional `.mo` files for production (performance)
- **Fallback**: Keep compiled PHP arrays as emergency fallback

### Component Diagram

```
┌─────────────────────────────────────────────────────┐
│                  Application Layer                   │
│  (Controllers, Views, Templates)                    │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│              Translation Helper Layer                │
│  trans($key, $params, $domain, $locale)            │
│  transChoice($key, $count, $params, $domain)       │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│            Symfony Translator Service                │
│  - Translation loading                              │
│  - Locale management                                │
│  - Fallback handling                                │
│  - Caching                                          │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│                  .po File Loaders                    │
│  PoFileLoader + MoFileLoader (optional)            │
└────────────────┬────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────┐
│              Translation Files                       │
│  /translations/messages.*.po                        │
│  /translations/help.*.po                            │
│  /translations/enums.*.po                           │
│  /translations/custom.*.po                          │
└─────────────────────────────────────────────────────┘
```

### Translation Domains

To organize translations, we'll use multiple **domains**:

| Domain | Purpose | Example Keys |
|--------|---------|--------------|
| `messages` | Main UI strings | `please_login`, `preferences`, `logout` |
| `help` | Help text/tooltips | `setup_mkdirMethod`, `task_scope_creep` |
| `enums` | Status/priority enums | `status.completed`, `priority.high` |
| `custom` | Custom application values | `topicNote.phone_conversation` |

---

## 4. Migration Phases

### Phase 1: Foundation (Week 1) - 8-12 hours

**Goal**: Set up infrastructure and tooling

**Tasks**:
1. Install Symfony Translation component
2. Create translation helper functions
3. Set up directory structure
4. Create conversion scripts (PHP → .po)
5. Create test harness

**Deliverables**:
- Working Symfony Translation integration
- Conversion scripts ready
- Helper functions created
- Documentation

### Phase 2: File Conversion (Week 2-3) - 15-20 hours

**Goal**: Convert all language files to .po format

**Tasks**:
1. Convert `lang_en.php` → `messages.en.po` (master reference)
2. Auto-convert all 30 other language files
3. Convert help files → `help.*.po`
4. Convert custom files → `custom.*.po`
5. Handle enums → `enums.*.po`
6. Validation and quality checks

**Deliverables**:
- Complete .po file set (31 languages × 4 domains)
- Validation report
- Conversion logs

### Phase 3: Core Integration (Week 4) - 12-16 hours

**Goal**: Update core system files

**Tasks**:
1. Update `includes/library.php` - translation loading
2. Update `classes/Container.php` - add translator
3. Update `includes/customvalues.php` - custom translations
4. Create backward compatibility layer
5. Update session/language detection

**Deliverables**:
- Modernized core files
- Backward compatibility maintained
- Integration tests passing

### Phase 4: Application Updates (Week 5-6) - 25-35 hours

**Goal**: Replace all `$strings` usage across application

**Tasks**:
1. Update all view/template files (~50 files)
2. Update all controller files (~30 files)
3. Update all class files (~20 files)
4. Replace enum array usage
5. Update JavaScript (if translations used)

**Deliverables**:
- All files updated to use `trans()` helper
- No more direct `$strings` references
- Code review completed

### Phase 5: Testing (Week 7) - 10-15 hours

**Goal**: Comprehensive testing across all languages

**Tasks**:
1. Functional testing (English)
2. Visual testing (English)
3. Smoke test all 31 languages
4. Test edge cases (plurals, special chars)
5. Performance testing
6. Browser/device testing

**Deliverables**:
- Test report
- Bug fixes completed
- Performance baseline

### Phase 6: Documentation & Deployment (Week 8) - 5-8 hours

**Goal**: Document and deploy

**Tasks**:
1. Update developer documentation
2. Create translator guide
3. Update README
4. Create deployment guide
5. Deploy to staging
6. Deploy to production

**Deliverables**:
- Complete documentation
- Deployment scripts
- Translator guide
- Production deployment

---

## 5. Implementation Steps

### Step 1: Install Dependencies

**File**: `composer.json`

```json
{
    "require": {
        "symfony/translation": "^6.0",
        "symfony/config": "^6.0",
        "symfony/yaml": "^6.0"
    }
}
```

**Command**:
```bash
composer require symfony/translation symfony/config symfony/yaml
```

### Step 2: Create Directory Structure

```bash
mkdir -p translations/{messages,help,enums,custom}
mkdir -p scripts/translation
mkdir -p var/cache/translations
```

### Step 3: Create Helper Functions

**File**: `/includes/translation.php`

```php
<?php
/**
 * Translation helper functions
 */

use Symfony\Component\Translation\Translator;

/**
 * Get the translator instance from container
 *
 * @return Translator
 */
function getTranslator(): Translator
{
    global $container;
    return $container->getTranslator();
}

/**
 * Translate a message
 *
 * @param string $key Translation key
 * @param array $parameters Replacement parameters
 * @param string $domain Translation domain (messages, help, enums, custom)
 * @param string|null $locale Override locale
 * @return string Translated message
 */
function trans(string $key, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
{
    return getTranslator()->trans($key, $parameters, $domain, $locale);
}

/**
 * Translate with pluralization
 *
 * @param string $key Translation key
 * @param int $count Count for plural selection
 * @param array $parameters Replacement parameters
 * @param string $domain Translation domain
 * @param string|null $locale Override locale
 * @return string Translated message
 */
function transChoice(string $key, int $count, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
{
    $parameters['%count%'] = $count;
    return getTranslator()->trans($key, $parameters, $domain, $locale);
}

/**
 * Get enum value (status, priority, etc.)
 *
 * @param string $enumType Enum type (status, priority, profil)
 * @param int $value Enum numeric value
 * @param string|null $locale Override locale
 * @return string Translated enum value
 */
function getEnum(string $enumType, int $value, ?string $locale = null): string
{
    return trans("{$enumType}.{$value}", [], 'enums', $locale);
}

/**
 * Get all values for an enum as array
 *
 * @param string $enumType Enum type
 * @param array $keys Array of numeric keys
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
 * DEPRECATED - Use trans() instead
 *
 * @return array
 */
function getLegacyStringsArray(): array
{
    // This is a temporary bridge for gradual migration
    // Will be removed in future version
    static $strings = null;

    if ($strings !== null) {
        return $strings;
    }

    // Load all translations into array format
    $translator = getTranslator();
    $catalogue = $translator->getCatalogue();
    $strings = $catalogue->all('messages');

    return $strings;
}
```

### Step 4: Update Container Class

**File**: `/classes/Container.php`

Add translator property and methods:

```php
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Loader\MoFileLoader;

class Container {
    private $language;
    private $translator;

    // ... existing code ...

    /**
     * Get translator instance
     */
    public function getTranslator(): Translator
    {
        if ($this->translator === null) {
            $this->initializeTranslator();
        }
        return $this->translator;
    }

    /**
     * Set translator instance
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Initialize translator with all language resources
     */
    private function initializeTranslator(): void
    {
        $locale = $this->getLanguage();
        $this->translator = new Translator($locale);

        // Add loaders
        $this->translator->addLoader('po', new PoFileLoader());
        $this->translator->addLoader('mo', new MoFileLoader());

        // Set fallback locales
        $this->translator->setFallbackLocales(['en']);

        // Register all translation resources
        $this->registerTranslationResources();
    }

    /**
     * Register all translation resources
     */
    private function registerTranslationResources(): void
    {
        $translationsPath = APP_ROOT . '/translations';
        $languages = $this->getSupportedLanguages();
        $domains = ['messages', 'help', 'enums', 'custom'];

        foreach ($languages as $lang) {
            foreach ($domains as $domain) {
                $poFile = "{$translationsPath}/{$domain}/{$domain}.{$lang}.po";
                $moFile = "{$translationsPath}/{$domain}/{$domain}.{$lang}.mo";

                // Prefer .mo files (faster) if available, fallback to .po
                if (file_exists($moFile)) {
                    $this->translator->addResource('mo', $moFile, $lang, $domain);
                } elseif (file_exists($poFile)) {
                    $this->translator->addResource('po', $poFile, $lang, $domain);
                }
            }
        }
    }

    /**
     * Get supported languages
     */
    private function getSupportedLanguages(): array
    {
        return [
            'ar', 'az', 'pt-br', 'bg', 'ca', 'zh', 'zh-tw', 'cs-iso',
            'cs-win1250', 'da', 'nl', 'en', 'et', 'fr', 'de', 'hu',
            'is', 'in', 'it', 'ja', 'ko', 'lv', 'no', 'pl', 'pt',
            'ro', 'ru', 'sk-win1250', 'es', 'tr', 'uk'
        ];
    }
}
```

### Step 5: Update library.php

**File**: `/includes/library.php`

Replace language file loading (lines 143-151):

```php
// OLD CODE (to be replaced):
// require_once APP_ROOT . '/languages/lang_en.php';
// require_once APP_ROOT . '/languages/help_en.php';
// if ($session->get("language") !== 'en') {
//     require_once APP_ROOT . '/languages/lang_' . $session->get("language") . '.php';
//     require_once APP_ROOT . '/languages/help_' . $session->get("language") . '.php';
// }

// NEW CODE:
// Include translation helpers
require_once APP_ROOT . '/includes/translation.php';

// Set the language in the Container (will initialize translator)
$container->setLanguage($session->get('language') ?? $langDefault ?? 'en');

// Get translator instance (triggers initialization)
$translator = $container->getTranslator();

// BACKWARD COMPATIBILITY: Create legacy $strings array
// TODO: Remove this once all code is migrated to trans()
$strings = getLegacyStringsArray();

// BACKWARD COMPATIBILITY: Create legacy enum arrays
// TODO: Remove this once all code is migrated to getEnum()
$status = getEnumArray('status', [0, 1, 2, 3, 4]);
$profil = getEnumArray('profil', [0, 1, 2, 3, 4, 5]);
$priority = getEnumArray('priority', [0, 1, 2, 3, 4, 5]);
$statusTopic = getEnumArray('statusTopic', [0, 1]);
$statusPublish = getEnumArray('statusPublish', [0, 1]);
$statusFile = getEnumArray('statusFile', [0, 1, 2, 3, 4]);
$phaseStatus = getEnumArray('phaseStatus', [0, 1, 2, 3]);
$invoiceStatus = getEnumArray('invoiceStatus', [0, 1, 2]);
$requestStatus = getEnumArray('requestStatus', [0, 1, 2]);

// Other arrays
$dayNameArray = [];
for ($i = 1; $i <= 7; $i++) {
    $dayNameArray[$i] = trans("day.{$i}", [], 'messages');
}

$monthNameArray = [];
for ($i = 1; $i <= 12; $i++) {
    $monthNameArray[$i] = trans("month.{$i}", [], 'messages');
}

$byteUnits = [
    trans('byte_units.bytes'),
    trans('byte_units.kb'),
    trans('byte_units.mb'),
    trans('byte_units.gb')
];
```

### Step 6: Create Conversion Scripts

**File**: `/scripts/translation/convert-to-po.php`

See detailed script in Section 7 (will be created as separate file).

### Step 7: Run Conversion

```bash
php scripts/translation/convert-to-po.php --input=languages/lang_en.php --output=translations/messages/messages.en.po --domain=messages

# Convert all languages
for lang in ar az pt-br bg ca zh zh-tw cs-iso cs-win1250 da nl en et fr de hu is in it ja ko lv no pl pt ro ru sk-win1250 es tr uk; do
    php scripts/translation/convert-to-po.php --input=languages/lang_${lang}.php --output=translations/messages/messages.${lang}.po --domain=messages
    php scripts/translation/convert-to-po.php --input=languages/help_${lang}.php --output=translations/help/help.${lang}.po --domain=help
done
```

### Step 8: Update Application Code

**Pattern 1: Simple string replacement**

Before:
```php
echo $strings["please_login"];
```

After:
```php
echo trans('please_login');
```

**Pattern 2: With parameters**

Before:
```php
$strings["welcome_user"] = "Welcome, " . $userName;
```

After:
```php
echo trans('welcome_user', ['%username%' => $userName]);
```

.po file:
```po
msgid "welcome_user"
msgstr "Welcome, %username%"
```

**Pattern 3: Enum values**

Before:
```php
$status = $GLOBALS["status"];
echo $status[0]; // "Client Completed"
```

After:
```php
echo getEnum('status', 0); // "Client Completed"
```

**Pattern 4: Pluralization**

Before:
```php
echo ($count == 1) ? "1 task" : "$count tasks";
```

After:
```php
echo transChoice('task_count', $count, ['%count%' => $count]);
```

.po file:
```po
msgid "task_count"
msgid_plural "task_count"
msgstr[0] "%count% task"
msgstr[1] "%count% tasks"
```

---

## 6. File Structure

### Proposed Directory Layout

```
phpcollab/
├── translations/                      # NEW: Translation files
│   ├── messages/
│   │   ├── messages.en.po            # English UI strings (master)
│   │   ├── messages.fr.po            # French UI strings
│   │   ├── messages.es.po            # Spanish UI strings
│   │   └── ... (31 total)
│   ├── help/
│   │   ├── help.en.po                # English help text
│   │   ├── help.fr.po                # French help text
│   │   └── ... (31 total)
│   ├── enums/
│   │   ├── enums.en.po               # Status, priority, etc.
│   │   ├── enums.fr.po
│   │   └── ... (31 total)
│   └── custom/
│       ├── custom.en.po              # Custom application values
│       ├── custom.fr.po
│       └── ... (13 total)
│
├── languages/                         # OLD: Keep for rollback
│   ├── lang_*.php                    # DEPRECATED (keep temporarily)
│   ├── help_*.php                    # DEPRECATED (keep temporarily)
│   └── custom_*.php                  # DEPRECATED (keep temporarily)
│
├── includes/
│   ├── library.php                   # MODIFIED: Use translator
│   ├── translation.php               # NEW: Helper functions
│   └── customvalues.php              # MODIFIED: Use translator
│
├── classes/
│   └── Container.php                 # MODIFIED: Add translator
│
├── scripts/
│   └── translation/                  # NEW: Conversion scripts
│       ├── convert-to-po.php         # PHP array → .po converter
│       ├── validate-translations.php # Validation script
│       ├── compile-mo.php            # .po → .mo compiler
│       └── extract-strings.php       # Extract translatable strings
│
├── var/
│   └── cache/
│       └── translations/             # NEW: Compiled translations cache
│
└── tests/
    └── translation/                  # NEW: Translation tests
        ├── TranslationTest.php
        └── fixtures/
```

### .po File Structure Example

**File**: `translations/messages/messages.en.po`

```po
# phpCollab - Main UI Strings
# English Translation
msgid ""
msgstr ""
"Language: en\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"

#: general/login.php:45
msgid "please_login"
msgstr "Please log in"

#: general/login.php:67
msgid "login"
msgstr "Log In"

#: includes/header.php:23
msgid "preferences"
msgstr "Preferences"

#: includes/header.php:24
msgid "logout"
msgstr "Log Out"

# Error messages
msgid "error_messages.too_many_attempts"
msgstr "You have tried too many times. Please try again later."

msgid "error_messages.tasks.blank_task_name"
msgstr "Please enter a task name"

# Pluralization example
msgid "task_count"
msgid_plural "task_count"
msgstr[0] "%count% task"
msgstr[1] "%count% tasks"
```

**File**: `translations/enums/enums.en.po`

```po
# phpCollab - Enums (Status, Priority, etc.)
# English Translation
msgid ""
msgstr ""
"Language: en\n"
"Content-Type: text/plain; charset=UTF-8\n"

# Status values
msgid "status.0"
msgstr "Client Completed"

msgid "status.1"
msgstr "Completed"

msgid "status.2"
msgstr "Not Started"

msgid "status.3"
msgstr "Open"

msgid "status.4"
msgstr "Suspended"

# Priority values
msgid "priority.0"
msgstr "None"

msgid "priority.1"
msgstr "Very low"

msgid "priority.2"
msgstr "Low"

msgid "priority.3"
msgstr "Medium"

msgid "priority.4"
msgstr "High"

msgid "priority.5"
msgstr "Very high"

# Profile/Role values
msgid "profil.0"
msgstr "Administrator"

msgid "profil.1"
msgstr "Project Manager"

msgid "profil.2"
msgstr "User"

msgid "profil.3"
msgstr "Client User"

msgid "profil.4"
msgstr "Disabled"

msgid "profil.5"
msgstr "Project Manager Administrator"
```

---

## 7. Code Changes

### Summary of Files to Modify

| Category | Files | Effort | Strategy |
|----------|-------|--------|----------|
| **Core System** | 5 files | High | Manual, careful |
| **Controllers** | ~30 files | Medium | Semi-automated |
| **Views/Templates** | ~50 files | Medium | Semi-automated |
| **Classes** | ~20 files | Medium | Semi-automated |
| **JavaScript** | ~5 files | Low | Manual |
| **Total** | ~110 files | - | Gradual migration |

### High-Priority Files (Core System)

1. **includes/library.php** - Translation loading
2. **classes/Container.php** - Translator integration
3. **includes/customvalues.php** - Custom translations
4. **includes/translation.php** - NEW: Helper functions
5. **general/login.php** - Language selection

### Medium-Priority Files (Controllers)

Examples:
- `projects/listprojects.php`
- `tasks/listtasks.php`
- `users/listusers.php`
- `calendar/viewcalendar.php`
- `reports/projectreport.php`

### Low-Priority Files (Less Frequently Used)

Examples:
- `administration/admin.php`
- `includes/help.php`
- `export/export.php`

### Automated Refactoring Script

**File**: `scripts/translation/refactor-code.php`

```php
<?php
/**
 * Automated code refactoring: Replace $strings usage with trans()
 *
 * Usage: php refactor-code.php --file=path/to/file.php [--dry-run]
 */

// Will be created as separate script
```

---

## 8. Testing Strategy

### Testing Phases

#### Phase 1: Unit Tests

**File**: `tests/translation/TranslationTest.php`

```php
<?php

use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testTranslatorLoads()
    {
        $translator = getTranslator();
        $this->assertInstanceOf(Translator::class, $translator);
    }

    public function testSimpleTranslation()
    {
        $result = trans('please_login', [], 'messages', 'en');
        $this->assertEquals('Please log in', $result);
    }

    public function testFallbackToEnglish()
    {
        $result = trans('please_login', [], 'messages', 'invalid-lang');
        $this->assertEquals('Please log in', $result);
    }

    public function testEnumTranslation()
    {
        $result = getEnum('status', 0, 'en');
        $this->assertEquals('Client Completed', $result);
    }

    public function testPluralization()
    {
        $result = transChoice('task_count', 1, ['%count%' => 1], 'messages', 'en');
        $this->assertStringContains('1 task', $result);

        $result = transChoice('task_count', 5, ['%count%' => 5], 'messages', 'en');
        $this->assertStringContains('5 tasks', $result);
    }
}
```

#### Phase 2: Integration Tests

Test full workflow:
1. Login with different languages
2. Navigate through all major pages
3. Verify translations display correctly
4. Test language switching

#### Phase 3: Visual Testing

Create screenshot comparison:
```bash
# Before migration
php scripts/screenshot.php --lang=en --output=screenshots/before/

# After migration
php scripts/screenshot.php --lang=en --output=screenshots/after/

# Compare
diff -r screenshots/before/ screenshots/after/
```

#### Phase 4: Language Coverage

Test each of 31 languages:
- Login page renders
- Main dashboard renders
- No PHP errors
- Special characters display correctly

#### Phase 5: Performance Testing

Benchmark translation loading:
```php
// Before (PHP arrays)
$start = microtime(true);
require_once 'languages/lang_en.php';
$time1 = microtime(true) - $start;

// After (.po files)
$start = microtime(true);
$translator = getTranslator();
$time2 = microtime(true) - $start;

// After (.mo files - compiled)
$start = microtime(true);
$translator = getTranslator(); // with .mo files
$time3 = microtime(true) - $start;

echo "PHP arrays: {$time1}s\n";
echo ".po files: {$time2}s\n";
echo ".mo files: {$time3}s\n";
```

### Test Matrix

| Language | Login | Dashboard | Projects | Tasks | Reports | Status |
|----------|-------|-----------|----------|-------|---------|--------|
| en (English) | ✓ | ✓ | ✓ | ✓ | ✓ | Pass |
| fr (French) | ✓ | ✓ | ✓ | ✓ | ✓ | Pass |
| es (Spanish) | ✓ | ✓ | ✓ | ✓ | ✓ | Pass |
| de (German) | ✓ | ✓ | ✓ | ✓ | ✓ | Pass |
| ... (27 more) | ... | ... | ... | ... | ... | ... |

---

## 9. Rollback Plan

### Pre-Migration Backup

```bash
# Backup entire codebase
git checkout -b backup-before-po-migration
git commit -am "Backup before .po migration"
git push origin backup-before-po-migration

# Backup database
mysqldump -u user -p phpcollab > backups/phpcollab_$(date +%Y%m%d).sql
```

### Rollback Scenarios

#### Scenario 1: Critical Bug in Production

**Trigger**: Major translation errors, broken UI, performance issues

**Action**:
```bash
# Revert to previous commit
git revert HEAD
git push origin main

# Or full rollback
git reset --hard backup-before-po-migration
git push --force origin main
```

#### Scenario 2: Incomplete Migration

**Trigger**: Too many files still need updating, project stalled

**Action**:
- Keep both systems running in parallel
- Use feature flag to toggle between old/new system
- Complete migration incrementally

**Implementation**:
```php
// In library.php
if (defined('USE_PO_TRANSLATIONS') && USE_PO_TRANSLATIONS === true) {
    // New .po system
    require_once APP_ROOT . '/includes/translation.php';
    $translator = $container->getTranslator();
} else {
    // Old PHP array system
    require_once APP_ROOT . '/languages/lang_en.php';
    // ...
}
```

#### Scenario 3: Translation Quality Issues

**Trigger**: Many translations are incorrect or missing

**Action**:
- Revert to original PHP files
- Fix conversion scripts
- Re-run conversion
- Deploy again

### Rollback Checklist

- [ ] Database backup created
- [ ] Git backup branch created
- [ ] Old language files NOT deleted
- [ ] Feature flag implemented (optional)
- [ ] Monitoring/alerts configured
- [ ] Team notified of rollback plan

---

## 10. Success Criteria

### Functional Requirements

- [ ] All 31 languages working correctly
- [ ] All UI strings translated properly
- [ ] Help text displays correctly
- [ ] Enums (status, priority, etc.) work
- [ ] Custom values translate properly
- [ ] Language switching works
- [ ] No PHP errors in any language
- [ ] No missing translations (fallback to English)

### Performance Requirements

- [ ] Page load time ≤ previous system + 10%
- [ ] Translation loading time ≤ 100ms
- [ ] Memory usage ≤ previous system + 15%

### Code Quality Requirements

- [ ] All `$strings` references replaced with `trans()`
- [ ] All enum arrays replaced with `getEnum()`
- [ ] No `$GLOBALS` usage for translations
- [ ] PHPStan level 5 passes
- [ ] All tests passing
- [ ] Code coverage ≥ 80% for translation code

### Documentation Requirements

- [ ] Migration plan documented (this document)
- [ ] Developer guide updated
- [ ] Translator guide created
- [ ] API documentation for helper functions
- [ ] Code examples provided
- [ ] Troubleshooting guide

### Translator Experience

- [ ] Can edit .po files with Poedit
- [ ] Can see context/comments for strings
- [ ] Can validate translations
- [ ] Can see missing translations
- [ ] Can export/import translations

---

## Appendix A: Language Support Matrix

| Code | Language | lang | help | custom | Status |
|------|----------|------|------|--------|--------|
| en | English | ✓ | ✓ | ✓ | Complete |
| fr | French | ✓ | ✓ | ✓ | Complete |
| es | Spanish | ✓ | ✓ | ✓ | Complete |
| de | German | ✓ | ✓ | ✓ | Complete |
| it | Italian | ✓ | ✓ | ✓ | Complete |
| pt | Portuguese | ✓ | ✓ | ✓ | Complete |
| pt-br | Brazilian Portuguese | ✓ | ✓ | ✓ | Complete |
| nl | Dutch | ✓ | ✓ | ✓ | Complete |
| ru | Russian | ✓ | ✓ | ✓ | Complete |
| pl | Polish | ✓ | ✓ | ✓ | Complete |
| ja | Japanese | ✓ | ✓ | ✓ | Complete |
| zh | Chinese (Simplified) | ✓ | ✓ | ✓ | Complete |
| zh-tw | Chinese (Traditional) | ✓ | ✓ | ✓ | Complete |
| ... | ... | ... | ... | ... | ... |

---

## Appendix B: Estimated Timeline

```gantt
title phpCollab .po Migration Timeline

section Phase 1: Foundation
Setup infrastructure           :2024-01-01, 2d
Create conversion scripts      :2024-01-03, 3d
Create helper functions        :2024-01-04, 2d

section Phase 2: Conversion
Convert English master         :2024-01-08, 2d
Convert all languages          :2024-01-10, 4d
Validate conversions           :2024-01-14, 2d

section Phase 3: Core Integration
Update library.php             :2024-01-16, 2d
Update Container.php           :2024-01-18, 1d
Update customvalues.php        :2024-01-19, 1d
Test core integration          :2024-01-20, 1d

section Phase 4: App Updates
Update view files              :2024-01-23, 5d
Update controller files        :2024-01-28, 4d
Update class files             :2024-02-01, 3d
Code review                    :2024-02-04, 2d

section Phase 5: Testing
Functional testing             :2024-02-06, 3d
Language coverage testing      :2024-02-09, 2d
Performance testing            :2024-02-11, 1d
Bug fixes                      :2024-02-12, 3d

section Phase 6: Deployment
Documentation                  :2024-02-15, 2d
Staging deployment             :2024-02-17, 1d
Production deployment          :2024-02-18, 1d
Post-deployment monitoring     :2024-02-19, 2d
```

**Total Duration**: ~7-8 weeks (40 working days)

---

## Appendix C: Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Incomplete translation coverage | Medium | High | Keep English fallback, validate all conversions |
| Performance degradation | Low | Medium | Use .mo compiled files, implement caching |
| Breaking existing functionality | Medium | High | Comprehensive testing, gradual rollout |
| Developer resistance | Low | Low | Good documentation, training sessions |
| Translation quality issues | Medium | Medium | Review conversions, translator feedback |
| Timeline overrun | Medium | Medium | Buffer time, phased approach |

---

## Appendix D: Resources

### Tools

- **Poedit**: https://poedit.net/ - Professional .po file editor
- **Symfony Translation**: https://symfony.com/doc/current/translation.html
- **GNU gettext**: https://www.gnu.org/software/gettext/

### Documentation

- Symfony Translation Component: https://symfony.com/doc/current/components/translation.html
- .po file format spec: https://www.gnu.org/software/gettext/manual/html_node/PO-Files.html
- Plural forms: http://docs.translatehouse.org/projects/localization-guide/en/latest/l10n/pluralforms.html

### Team Contacts

- **Technical Lead**: [Name]
- **Translators**: [Names]
- **QA Team**: [Names]
- **DevOps**: [Name]

---

## Document Version History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0 | 2025-11-05 | Claude | Initial migration plan created |

---

**End of Migration Plan**
