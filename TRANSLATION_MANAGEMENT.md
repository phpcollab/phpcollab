# Translation Management Guide

Complete guide for managing translations in phpCollab using the .po file system.

---

## Table of Contents

1. [Overview](#overview)
2. [Available Tools](#available-tools)
3. [Common Workflows](#common-workflows)
4. [Script Reference](#script-reference)
5. [Best Practices](#best-practices)
6. [Troubleshooting](#troubleshooting)

---

## Overview

phpCollab uses industry-standard `.po` (Portable Object) files for translations across 31 languages. This document explains how to manage these translations effectively.

### Quick Reference

```bash
# Check translation status
php scripts/translation/check-missing.php

# Add new strings to all languages
php scripts/translation/sync-translations.php

# Extract strings from code
php scripts/translation/extract-new-strings.php --update

# Export for translators
php scripts/translation/export-for-translators.php --lang=fr --output=french.csv

# Import completed translations
php scripts/translation/import-translations.php --lang=fr --input=french-completed.csv
```

---

## Available Tools

### 1. check-missing.php
**Purpose**: Generate translation completion reports

**What it does**:
- Compares all languages against English (master)
- Shows completion percentage
- Lists missing translations
- Exports reports in multiple formats

### 2. sync-translations.php
**Purpose**: Synchronize translation keys across languages

**What it does**:
- Adds missing keys from English to other languages
- Preserves existing translations
- Marks new entries as "fuzzy" (needs translation)
- Creates backup files

### 3. extract-new-strings.php
**Purpose**: Extract translatable strings from PHP code

**What it does**:
- Scans PHP files for trans() calls
- Finds new translation keys
- Adds them to English .po file
- Reports what was found

### 4. export-for-translators.php
**Purpose**: Export missing translations for translators

**What it does**:
- Exports missing translations to CSV/JSON
- Includes English reference text
- Creates translator-friendly format
- Can be opened in Excel/Spreadsheet apps

### 5. import-translations.php
**Purpose**: Import completed translations

**What it does**:
- Imports translations from CSV/JSON
- Updates .po files
- Removes "fuzzy" flags
- Creates backups

---

## Common Workflows

### Workflow 1: Adding a New Feature

**Scenario**: You're adding a new feature and need to add translation strings.

```bash
# Step 1: Write your code with trans() calls
# Example: echo trans('new_feature_title');

# Step 2: Extract new strings from code
php scripts/translation/extract-new-strings.php --scan=path/to/feature/ --update

# Step 3: Edit English .po file to add translations
nano translations/messages/messages.en.po
# Find the new keys and add English translations

# Step 4: Sync to all other languages
php scripts/translation/sync-translations.php

# Step 5: Check what needs translation
php scripts/translation/check-missing.php

# Step 6: Export for translators (optional)
php scripts/translation/export-for-translators.php --lang=fr --output=french-new.csv
```

### Workflow 2: Updating Existing Translations

**Scenario**: You need to update or fix translations for a specific language.

```bash
# Step 1: Check current status
php scripts/translation/check-missing.php --lang=fr

# Step 2: Export missing translations
php scripts/translation/export-for-translators.php --lang=fr --output=french-todo.csv

# Step 3: Edit the CSV file (in Excel or similar)
# Add translations in the "Translation" column

# Step 4: Import completed translations
php scripts/translation/import-translations.php --lang=fr --input=french-completed.csv

# Step 5: Verify import
php scripts/translation/check-missing.php --lang=fr

# Step 6: Test in browser
# Visit: http://your-site/general/translation-demo.php
```

### Workflow 3: Adding a New Language

**Scenario**: You want to add support for a new language (e.g., Swedish - sv).

```bash
# Step 1: Create .po files for new language
# Copy English files as template
for domain in messages help custom; do
    cp translations/${domain}/${domain}.en.po \
       translations/${domain}/${domain}.sv.po
done

# Step 2: Update Container.php
# Add 'sv' to $languages array in getSupportedLanguages() method

# Step 3: Edit header in new .po files
# Change Language: en to Language: sv

# Step 4: Export for translation
php scripts/translation/export-for-translators.php --lang=sv --output=swedish.csv

# Step 5: Send to Swedish translator

# Step 6: Import completed translations
php scripts/translation/import-translations.php --lang=sv --input=swedish-completed.csv

# Step 7: Test
# Visit translation-demo.php and add Swedish to language selector
```

### Workflow 4: Monthly Translation Audit

**Scenario**: Regular maintenance check of all translations.

```bash
# Step 1: Generate comprehensive report
php scripts/translation/check-missing.php --format=csv > translation-report-$(date +%Y-%m-%d).csv

# Step 2: Review the report
# Open CSV in Excel to see which languages need work

# Step 3: Prioritize languages
# Focus on languages with lowest completion %

# Step 4: For each priority language:
php scripts/translation/export-for-translators.php --lang=LANG --output=LANG-todo.csv

# Step 5: Send to translators or community

# Step 6: Import completed translations as they come back
php scripts/translation/import-translations.php --lang=LANG --input=LANG-completed.csv

# Step 7: Commit and push changes
git add translations/
git commit -m "Update translations for [languages]"
git push
```

### Workflow 5: Release Preparation

**Scenario**: Preparing for a new release, want all translations complete.

```bash
# Step 1: Sync all languages to ensure no missing keys
php scripts/translation/sync-translations.php

# Step 2: Generate full status report
php scripts/translation/check-missing.php --format=text > pre-release-translations.txt

# Step 3: Identify incomplete languages
# Look for languages with <95% completion

# Step 4: For each incomplete language:
php scripts/translation/export-for-translators.php \
    --lang=LANG \
    --only-missing \
    --output=LANG-urgent.csv

# Step 5: Rush translations for critical strings

# Step 6: Import rush translations
php scripts/translation/import-translations.php --lang=LANG --input=LANG-urgent.csv

# Step 7: Final verification
php scripts/translation/check-missing.php

# Step 8: Commit final translations
git add translations/
git commit -m "Final translation updates for v2.x release"
git push
```

---

## Script Reference

### check-missing.php

**Usage:**
```bash
php scripts/translation/check-missing.php [OPTIONS]
```

**Options:**
- `--domain=DOMAIN` - Check specific domain (messages, help, custom, all)
- `--lang=LANG` - Check specific language only
- `--verbose` - Show detailed missing keys
- `--format=FORMAT` - Output format (text, csv, json)

**Examples:**
```bash
# Check all languages, all domains
php scripts/translation/check-missing.php

# Check only French
php scripts/translation/check-missing.php --lang=fr

# Check messages domain with details
php scripts/translation/check-missing.php --domain=messages --verbose

# Export to CSV for analysis
php scripts/translation/check-missing.php --format=csv > report.csv

# Check help domain for Spanish
php scripts/translation/check-missing.php --domain=help --lang=es
```

**Output:**
```
╔════════════════════════════════════════════════════════════╗
║         Translation Completion Report                     ║
╚════════════════════════════════════════════════════════════╝

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Domain: MESSAGES
  Master Language: English (en)
  Total Keys: 726
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

  ✓ German (de):               [████████████████████]  100.0% (726/726) - 0 missing
  ● French (fr):               [███████████████████░]   93.8% (681/726) - 45 missing
  ● Spanish (es):              [████████████████████]   98.3% (714/726) - 12 missing
  ◐ Japanese (ja):             [████████████░░░░░░░░]   62.0% (450/726) - 276 missing
```

---

### sync-translations.php

**Usage:**
```bash
php scripts/translation/sync-translations.php [OPTIONS]
```

**Options:**
- `--domain=DOMAIN` - Sync specific domain or 'all'
- `--lang=LANG` - Sync specific language only
- `--dry-run` - Preview changes without modifying
- `--remove-obsolete` - Remove keys not in master
- `--backup` - Create backups (default: true)
- `--no-backup` - Don't create backups

**Examples:**
```bash
# Sync all languages (safe - creates backups)
php scripts/translation/sync-translations.php

# Preview changes first
php scripts/translation/sync-translations.php --dry-run

# Sync only messages domain
php scripts/translation/sync-translations.php --domain=messages

# Sync only French
php scripts/translation/sync-translations.php --lang=fr

# Sync and remove obsolete keys
php scripts/translation/sync-translations.php --remove-obsolete
```

**What it does:**
1. Reads English (master) .po file
2. For each other language:
   - Adds missing keys (marked as "fuzzy")
   - Preserves existing translations
   - Optionally removes obsolete keys
3. Creates `.bak.TIMESTAMP` backup files

---

### extract-new-strings.php

**Usage:**
```bash
php scripts/translation/extract-new-strings.php [OPTIONS]
```

**Options:**
- `--scan=PATH` - Directory or file to scan
- `--domain=DOMAIN` - Extract for specific domain
- `--dry-run` - Show without modifying
- `--update` - Update .po files with new keys

**Detected Patterns:**
- `trans('key')` → messages domain
- `getEnum('type', value)` → enums domain
- `help('key')` → help domain
- `custom('key')` → custom domain
- `$strings["key"]` → messages (legacy, marked)

**Examples:**
```bash
# Scan entire project
php scripts/translation/extract-new-strings.php

# Scan specific directory
php scripts/translation/extract-new-strings.php --scan=general/

# Extract and update .po files
php scripts/translation/extract-new-strings.php --update

# Preview without changes
php scripts/translation/extract-new-strings.php --dry-run
```

---

### export-for-translators.php

**Usage:**
```bash
php scripts/translation/export-for-translators.php --lang=LANG [OPTIONS]
```

**Options:**
- `--lang=LANG` - Language to export (required)
- `--domain=DOMAIN` - Export specific domain or 'all'
- `--format=FORMAT` - Output format (csv, json, text)
- `--output=FILE` - Output file (default: stdout)
- `--only-missing` - Exclude fuzzy translations
- `--with-english` - Include English reference (default: true)

**Examples:**
```bash
# Export French to CSV
php scripts/translation/export-for-translators.php --lang=fr --output=french.csv

# Export Spanish as JSON
php scripts/translation/export-for-translators.php --lang=es --format=json > spanish.json

# Export German as text worksheet
php scripts/translation/export-for-translators.php --lang=de --format=text > german.txt

# Export only messages domain
php scripts/translation/export-for-translators.php --lang=it --domain=messages --output=italian-messages.csv

# Preview to console
php scripts/translation/export-for-translators.php --lang=ja --format=text
```

**CSV Output Format:**
```csv
Domain,Key,Status,English,Translation,Context
messages,please_login,missing,"Please log in","",general/login.php
messages,logout,fuzzy,"Log Out","Déconnexion",includes/header.php
```

---

### import-translations.php

**Usage:**
```bash
php scripts/translation/import-translations.php --lang=LANG --input=FILE [OPTIONS]
```

**Options:**
- `--lang=LANG` - Language to import (required)
- `--input=FILE` - Input file (required)
- `--format=FORMAT` - Force format (csv/json, auto-detected)
- `--dry-run` - Preview without modifying
- `--backup` - Create backup (default: true)
- `--no-backup` - Don't create backup

**Examples:**
```bash
# Import French from CSV
php scripts/translation/import-translations.php --lang=fr --input=french-completed.csv

# Import Spanish from JSON
php scripts/translation/import-translations.php --lang=es --input=spanish.json

# Preview without changes
php scripts/translation/import-translations.php --lang=de --input=german.csv --dry-run

# Import without backup
php scripts/translation/import-translations.php --lang=it --input=italian.csv --no-backup
```

**Expected CSV Format:**
```csv
Domain,Key,Translation
messages,please_login,"Connectez-vous"
messages,logout,"Déconnexion"
```

---

## Best Practices

### For Developers

1. **Always use trans() for new strings**
   ```php
   // Good
   echo trans('new_feature_title');

   // Bad
   echo "New Feature Title";
   ```

2. **Extract and sync regularly**
   ```bash
   # After adding new features
   php scripts/translation/extract-new-strings.php --update
   php scripts/translation/sync-translations.php
   ```

3. **Use meaningful keys**
   ```php
   // Good
   trans('user_profile_updated_successfully')

   // Bad
   trans('msg1')
   ```

4. **Group related keys**
   ```php
   trans('error.invalid_email')
   trans('error.required_field')
   trans('error.password_too_short')
   ```

### For Translators

1. **Use professional tools**
   - Poedit (free, desktop app)
   - Excel/LibreOffice (for CSV exports)
   - Online: Crowdin, Weblate

2. **Check context**
   - Look at file references in CSV "Context" column
   - Check English reference for meaning
   - Test in browser when possible

3. **Maintain consistency**
   - Use same terms throughout
   - Match tone/formality of English
   - Keep placeholders (%name%, %count%) intact

4. **Report issues**
   - Ambiguous English strings
   - Missing context
   - Technical errors

### For Project Managers

1. **Regular audits**
   ```bash
   # Monthly check
   php scripts/translation/check-missing.php --format=csv > monthly-report.csv
   ```

2. **Prioritize high-traffic languages**
   - Focus on languages with most users
   - Aim for 95%+ completion before releases

3. **Track translator performance**
   - Monitor completion rates
   - Quality check random samples
   - Provide feedback

4. **Plan ahead for releases**
   - Freeze strings 2 weeks before release
   - Allow time for translation
   - Budget for professional translation if needed

---

## Troubleshooting

### Problem: Script shows "No changes needed"

**Cause**: Translations are already synchronized

**Solution**: This is normal! It means everything is up to date.

```bash
# Verify with:
php scripts/translation/check-missing.php
```

---

### Problem: Import fails with "Key not found"

**Cause**: The key exists in CSV but not in .po file

**Solution**: Sync first, then import

```bash
# Step 1: Sync to add missing keys
php scripts/translation/sync-translations.php

# Step 2: Try import again
php scripts/translation/import-translations.php --lang=fr --input=french.csv
```

---

### Problem: Translations not showing in browser

**Cause**: Cache or file permissions

**Solution**:
```bash
# Clear cache
rm -rf var/cache/translations/*

# Check file permissions
chmod 644 translations/*/*.po

# Restart web server
service apache2 restart  # or nginx
```

---

### Problem: Character encoding issues

**Cause**: CSV not saved as UTF-8

**Solution**:
- In Excel: Save As → CSV UTF-8
- In LibreOffice: Choose "Unicode (UTF-8)" when saving
- In text editor: Ensure file is saved as UTF-8

---

### Problem: "Fuzzy" translations not clearing

**Cause**: Translation might be identical to previous

**Solution**:
```bash
# Remove fuzzy flags manually
sed -i '/#, fuzzy/d' translations/messages/messages.fr.po

# Or sync again
php scripts/translation/sync-translations.php --lang=fr
```

---

## Additional Resources

### Documentation Files

- `MIGRATION_PLAN.md` - Detailed migration plan and architecture
- `TRANSLATION_POC_README.md` - Proof-of-concept documentation
- `TRANSLATION_COMPLETE.md` - Migration completion summary
- `TRANSLATION_MANAGEMENT.md` - This file

### Demo & Testing

- **Browser Demo**: `http://your-site/general/translation-demo.php`
- **CLI Test**: `php scripts/translation/test-translation-poc.php`

### External Tools

- **Poedit**: https://poedit.net/ - Free .po editor
- **Crowdin**: https://crowdin.com/ - Online translation platform (free for open source)
- **Weblate**: https://weblate.org/ - Self-hosted translation platform

### Getting Help

- Check this documentation first
- Review script help: `php script-name.php --help`
- Check git history for examples
- Visit phpCollab community forums

---

## Quick Reference Card

```
┌─────────────────────────────────────────────────────────────┐
│  Translation Management Quick Reference                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Check Status:                                              │
│  $ php scripts/translation/check-missing.php                │
│                                                             │
│  Add New Feature Strings:                                   │
│  $ php scripts/translation/extract-new-strings.php --update │
│  $ php scripts/translation/sync-translations.php            │
│                                                             │
│  For Translators:                                           │
│  $ php scripts/translation/export-for-translators.php \     │
│      --lang=fr --output=french.csv                          │
│  [Translator works on CSV]                                  │
│  $ php scripts/translation/import-translations.php \        │
│      --lang=fr --input=french-done.csv                      │
│                                                             │
│  Test in Browser:                                           │
│  http://your-site/general/translation-demo.php              │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

**Last Updated**: 2025-11-08
**Version**: 1.0
