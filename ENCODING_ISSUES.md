# Character Encoding Issues

## Current Status

### Issue Identified

Some translation files have character encoding problems with accented characters (é, è, à, ô, ü, ñ, etc.) displaying as � or missing entirely.

**Affected Languages:**
- **French (fr)**: ~552 character encoding issues
- **German (de)**: ~3 character encoding issues
- Other languages appear correct

### Root Cause

The encoding issues exist in the **source PHP files** (`languages/lang_*.php`) from which the .po files were converted. These PHP files were created years ago, possibly with incorrect encoding (Windows-1252 or ISO-8859-1 instead of UTF-8).

When we converted from PHP to .po format, we inherited these encoding problems from the source files.

### Example Issues

**French:**
- "Déconnexion" appears as "D�connexion"
- "Préférences" appears as "Pr�f�rences"
- "Tâches" appears as "T�ches"
- "Éditer" appears as "�diter"

**German:**
- A few characters with umlauts (ä, ö, ü)

## Tools Created

### fix-encoding.php

Created a comprehensive encoding fix script:
```bash
php scripts/translation/fix-encoding.php
```

**What it does:**
- Detects encoding issues
- Converts to proper UTF-8
- Fixes common corrupted characters
- Creates automatic backups

**Limitations:**
- Can only fix corrupted characters if the original data is recoverable
- Cannot reconstruct characters that are completely lost (� replacement character)
- Works best when source encoding is intact

## Solutions

### Solution 1: Manual Correction (Recommended)

Have a native French speaker review and fix the French translations:

1. **Export for review:**
   ```bash
   php scripts/translation/export-for-translators.php --lang=fr --output=french-review.csv
   ```

2. **Native speaker reviews and fixes accents in Excel/Spreadsheet**

3. **Import corrected translations:**
   ```bash
   php scripts/translation/import-translations.php --lang=fr --input=french-fixed.csv
   ```

### Solution 2: Fix Source PHP Files

Fix the original PHP files first, then reconvert:

1. **Fix `languages/lang_fr.php`** with proper UTF-8 encoding
2. **Reconvert to .po:**
   ```bash
   php scripts/translation/convert-to-po.php \
       --input=languages/lang_fr.php \
       --output=translations/messages/messages.fr.po \
       --type=lang
   ```

### Solution 3: Use Reference Translations

Find French translations from:
- Previous phpCollab versions (if properly encoded)
- Professional translation services
- Community contributions

## Prevention

### For Future Translations

1. **Always use UTF-8 encoding** for all translation files
2. **Validate encoding** before committing:
   ```bash
   file -i translations/messages/messages.*.po
   # Should show: charset=utf-8
   ```

3. **Test in browser** with actual accented characters:
   ```
   http://your-site/general/translation-demo.php
   ```

4. **Use the encoding fix script** preventively:
   ```bash
   php scripts/translation/fix-encoding.php --dry-run
   ```

## Current Recommendation

**For French users:**
The French translations are 74.5% complete and functional, but many accented characters display incorrectly. The meaning is still clear, but it's not professional quality.

**Priority:**
- **High** if targeting French-speaking users
- **Medium** for general use
- **Low** if French is rarely used

**Action:**
Consider hiring a French translator to review and fix ~550 strings, or recruit a community member who speaks French natively.

## Technical Details

### Character Encoding Types

- **UTF-8**: Unicode, supports all languages (required)
- **ISO-8859-1 (Latin-1)**: Western European, limited
- **Windows-1252**: Similar to ISO-8859-1, Microsoft variant
- **ISO-8859-15**: Latin-1 with Euro symbol

### Detection

```bash
# Check file encoding
file -i translations/messages/messages.fr.po

# Check for problematic characters
grep -P "[^\x00-\x7F]" translations/messages/messages.fr.po | head
```

### Manual Fix Example

```bash
# Convert from Windows-1252 to UTF-8
iconv -f WINDOWS-1252 -t UTF-8 old.po > new.po

# Or from ISO-8859-1
iconv -f ISO-8859-1 -t UTF-8 old.po > new.po
```

## Status by Language

| Language | Status | Notes |
|----------|--------|-------|
| French (fr) | ⚠️ Encoding issues | ~552 chars need fixing |
| German (de) | ⚠️ Minor issues | ~3 chars need fixing |
| Spanish (es) | ✅ OK | No encoding issues |
| English (en) | ✅ OK | Master file |
| Italian (it) | ✅ OK | No encoding issues detected |
| Portuguese (pt) | ✅ OK | No encoding issues detected |
| All others | ✅ OK | No encoding issues detected |

## Timeline

- **2025-11-10**: Encoding issues identified
- **2025-11-10**: Fix script created (`fix-encoding.php`)
- **2025-11-10**: Documented in this file
- **Next**: Awaiting decision on fix approach

## Resources

- **Encoding fix script**: `scripts/translation/fix-encoding.php`
- **Translation management**: `TRANSLATION_MANAGEMENT.md`
- **Export/import tools**: `export-for-translators.php`, `import-translations.php`
- **Browser demo**: `http://your-site/general/translation-demo.php`

---

**Last Updated**: 2025-11-10
**Created By**: Translation system audit
