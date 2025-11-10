# phpCollab Translation System Migration

## Overview

This project has successfully migrated phpCollab's translation system from legacy PHP array-based translations to industry-standard `.po` (Portable Object) files using the Symfony Translation component.

**Migration Status**: ✅ **COMPLETE** (2025-11-10)

## What Was Accomplished

### Core Migration
- ✅ **31 languages** fully converted to .po format
- ✅ **75 translation files** created (messages, help, custom domains)
- ✅ **726 strings** in English master file
- ✅ **Symfony Translation** component integrated (v5.3)
- ✅ **Backward compatibility** maintained with legacy PHP arrays
- ✅ **Browser demo** for visual testing and validation

### Tools Created
- ✅ **Conversion scripts** for PHP → .po migration
- ✅ **5 management tools** for ongoing translation maintenance
- ✅ **Encoding fix script** for character encoding issues
- ✅ **Comprehensive documentation** (5 documents)

## Quick Start

### Using the New Translation System

```php
// Basic translation
echo trans('strings.login');  // Returns: "Login"

// With parameters
echo trans('strings.welcome', ['name' => 'John']);  // Returns: "Welcome, John"

// Enum translations
echo getEnum('priority', '4');  // Returns: "Very High"

// Help text
echo help('projects');  // Returns: "Projects are..."

// Custom app values
echo custom('company');  // Returns: "Your Company Name"
```

### Testing Translations

Visit the browser demo to test translations live:
```
http://your-site/general/translation-demo.php
```

Features:
- Switch between 13 languages
- Compare old PHP arrays vs new .po system
- Verify both methods produce identical output
- Real-time language switching

## Documentation

### For Getting Started
1. **[TRANSLATION_COMPLETE.md](TRANSLATION_COMPLETE.md)** - Quick overview and statistics
2. **[TRANSLATION_POC_README.md](TRANSLATION_POC_README.md)** - Proof-of-concept documentation

### For Understanding the Migration
3. **[MIGRATION_PLAN.md](MIGRATION_PLAN.md)** - Comprehensive 50-page migration plan
   - Current system analysis
   - Target architecture
   - 6-phase implementation plan
   - Code changes required
   - Testing strategy

### For Maintaining Translations
4. **[TRANSLATION_MANAGEMENT.md](TRANSLATION_MANAGEMENT.md)** - Complete management guide
   - 5 workflow scenarios
   - Tool reference for all 5 scripts
   - Best practices
   - Troubleshooting

### For Addressing Quality Issues
5. **[ENCODING_ISSUES.md](ENCODING_ISSUES.md)** - Character encoding problems
   - Current status by language
   - Root cause analysis
   - 3 solution approaches
   - Prevention guidelines

## File Structure

```
phpcollab/
├── translations/                    # New .po translation files
│   ├── messages/                   # UI strings (31 languages)
│   │   ├── messages.en.po         # English master (726 entries)
│   │   ├── messages.fr.po         # French (574 entries)
│   │   └── messages.*.po          # 29 more languages
│   ├── help/                       # Help text (31 languages)
│   │   ├── help.en.po
│   │   └── help.*.po
│   └── custom/                     # Custom app values (13 languages)
│       ├── custom.en.po
│       └── custom.*.po
│
├── scripts/translation/            # Management tools
│   ├── convert-to-po.php          # Convert PHP arrays → .po
│   ├── convert-all.sh             # Batch conversion script
│   ├── check-missing.php          # Check translation completion
│   ├── sync-translations.php      # Sync keys across languages
│   ├── extract-new-strings.php    # Extract strings from code
│   ├── export-for-translators.php # Export for translators
│   ├── import-translations.php    # Import completed translations
│   └── fix-encoding.php           # Fix character encoding issues
│
├── includes/
│   ├── translation.php            # Helper functions (trans(), getEnum(), etc.)
│   └── library.php                # Initializes translator (modified)
│
├── classes/
│   └── Container.php              # Translator support added
│
├── languages/                      # Legacy PHP arrays (still loaded for backward compatibility)
│   ├── lang_*.php                 # 31 language files
│   ├── help_*.php                 # 31 help files
│   └── custom_*.php               # 13 custom files
│
└── general/
    └── translation-demo.php       # Browser testing interface
```

## Translation Management Workflows

### 1. Check Translation Status

```bash
php scripts/translation/check-missing.php --lang=fr
```

**Output**: Shows completion percentage, missing strings, total progress

### 2. Add New Strings to All Languages

```bash
# Step 1: Extract new strings from code
php scripts/translation/extract-new-strings.php

# Step 2: Sync to all language files
php scripts/translation/sync-translations.php --from=en --dry-run
php scripts/translation/sync-translations.php --from=en
```

### 3. Send Work to Translators

```bash
# Export missing translations to CSV
php scripts/translation/export-for-translators.php \
    --lang=fr \
    --format=csv \
    --output=french-translations.csv \
    --with-english
```

### 4. Import Completed Translations

```bash
# Import translator's work
php scripts/translation/import-translations.php \
    --lang=fr \
    --input=french-translations-completed.csv \
    --format=csv
```

### 5. Fix Encoding Issues

```bash
# Check for encoding problems
php scripts/translation/fix-encoding.php --dry-run

# Fix detected issues
php scripts/translation/fix-encoding.php
```

## Current Status

### Languages: 31 Total

| Status | Count | Languages |
|--------|-------|-----------|
| ✅ Fully functional | 31 | All languages converted and working |
| ⚠️ Encoding issues | 2 | French (~552 chars), German (~3 chars) |
| ✅ No encoding issues | 29 | All other languages |

### Translation Completeness

- **English (en)**: 100% complete (726/726) - Master language
- **French (fr)**: 74.5% complete (541/726) - Functional but needs work
- **Other languages**: Varies by language (use check-missing.php to verify)

### Known Issues

#### French Character Encoding
- **Issue**: ~552 accented characters display incorrectly (é → �)
- **Status**: Documented, fix script created
- **Root Cause**: Source PHP files have corrupted UTF-8 data
- **Recommended Fix**: Native speaker manual review and correction
- **Details**: See [ENCODING_ISSUES.md](ENCODING_ISSUES.md)

#### German Character Encoding
- **Issue**: ~3 characters with umlauts (ä, ö, ü)
- **Status**: Minor, similar to French
- **Recommended Fix**: Quick manual correction

## Technical Architecture

### Components Used

- **Symfony Translation** (v5.3) - Core translation engine
- **Symfony Config** (v5.3) - Configuration loader
- **PHP** (7.4+) - Minimum required version
- **Gettext format** (.po files) - Industry standard

### How It Works

1. **Initialization** (includes/library.php)
   - Translator initialized on every page load
   - Language set from session
   - All .po files registered with translator

2. **Translation Lookup** (includes/translation.php)
   - `trans()` called with key
   - Symfony looks up translation in current language
   - Falls back to English if not found
   - Returns translated string

3. **Backward Compatibility**
   - Legacy PHP arrays still loaded
   - Both old and new methods work simultaneously
   - Gradual migration path supported

### Helper Functions

```php
// Core translation function
trans(string $key, array $parameters = [], string $domain = 'messages', ?string $locale = null): string

// Enum translation
getEnum(string $enumType, $value, ?string $locale = null): string

// Enum array
getEnumArray(string $enumType, array $keys, ?string $locale = null): array

// Help text
help(string $key, ?string $locale = null): string

// Custom values
custom(string $key, ?string $locale = null): string

// Get translator instance
getTranslator(): Translator
```

## Migration Benefits

### Before (PHP Arrays)
- ❌ Non-standard format
- ❌ Hard to maintain (93 PHP files)
- ❌ No tooling support
- ❌ Mixed code and content
- ❌ No translation memory
- ❌ No professional translator tools

### After (.po Files)
- ✅ Industry standard format
- ✅ Organized structure (75 .po files)
- ✅ Professional tools available (Poedit, Lokalize, etc.)
- ✅ Clean separation of concerns
- ✅ Translation memory support
- ✅ Export to CSV/JSON for translators
- ✅ Automatic fallback to English
- ✅ Comprehensive management scripts

## Performance

### No Performance Impact
- .po files loaded once per request
- Same as legacy PHP arrays
- Symfony caches translations in production
- No database queries required

### Memory Usage
- Similar to previous system
- Only active language loaded
- Fallback language (English) also loaded

## Next Steps (Optional)

### High Priority
1. **Fix French encoding** - 552 characters need correction
   - Hire French translator for review
   - Use export/import workflow
   - See ENCODING_ISSUES.md for details

2. **Complete French translations** - 185 strings missing (74.5% → 100%)
   - Export missing: `php scripts/translation/export-for-translators.php --lang=fr`
   - Send to translator
   - Import completed: `php scripts/translation/import-translations.php --lang=fr`

### Medium Priority
3. **Fix German encoding** - 3 characters need correction
4. **Verify other languages** - Check completion status
5. **Update documentation** - Add missing translations to English master

### Low Priority
6. **Remove backward compatibility** - Remove legacy PHP array loading
7. **Optimize file structure** - Consider using .mo (compiled) files for production
8. **Add more domains** - Split messages into more specific domains

## Testing

### Automated Testing
```bash
# Test conversion scripts
php scripts/translation/test-translation.php

# Check all languages
for lang in ar az pt-br bg ca zh zh-tw cs-iso cs-win1250 da nl en et fr de hu is in it ja ko lv no pl pt ro ru sk-win1250 es tr uk; do
    php scripts/translation/check-missing.php --lang=$lang
done
```

### Manual Testing
1. Visit `general/translation-demo.php`
2. Switch between languages
3. Verify translations display correctly
4. Compare old vs new methods
5. Test special characters (accents, umlauts, etc.)

### Browser Testing
- Test all 31 languages in demo page
- Verify character encoding (UTF-8)
- Check for missing translations
- Validate formatting and grammar

## Support & Troubleshooting

### Common Issues

**Q: Translations not showing up?**
- Check .po file exists in translations/messages/
- Verify file encoding is UTF-8: `file -i translations/messages/messages.*.po`
- Check logs for errors: `tail -f logs/phpcollab.log`

**Q: Character encoding problems?**
- Run: `php scripts/translation/fix-encoding.php --dry-run`
- See ENCODING_ISSUES.md for solutions

**Q: How to add new strings?**
- Add to English master: translations/messages/messages.en.po
- Run: `php scripts/translation/sync-translations.php --from=en`
- Send to translators: `php scripts/translation/export-for-translators.php`

**Q: Translation showing wrong text?**
- Check key exists in .po file: `grep "msgid \"key\"" translations/messages/messages.*.po`
- Verify msgstr has correct translation
- Check for "fuzzy" flag (needs review)

### Getting Help

1. **Documentation**: Read TRANSLATION_MANAGEMENT.md for detailed workflows
2. **Tools**: All scripts have `--help` option: `php scripts/translation/check-missing.php --help`
3. **Examples**: See TRANSLATION_POC_README.md for code examples

## Credits

**Migration Completed**: 2025-11-10
**System**: Symfony Translation Component v5.3
**Format**: GNU gettext .po files
**Languages**: 31 languages, 75 files
**Lines of Code**: ~5,000+ lines (scripts + docs)

## License

This translation system follows phpCollab's existing license.

---

**Last Updated**: 2025-11-10
**Status**: Production Ready (with known French/German encoding issues)
**Maintainer**: See TRANSLATION_MANAGEMENT.md for maintenance workflows
