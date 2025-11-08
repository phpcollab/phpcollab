#!/usr/bin/env php
<?php
/**
 * Import Translations from CSV/JSON
 *
 * Imports completed translations from translator-friendly formats back into .po files
 *
 * Usage:
 *   php import-translations.php --lang=fr --input=french.csv [OPTIONS]
 *
 * Options:
 *   --lang=LANG       Language to import (required)
 *   --input=FILE      Input file (required)
 *   --format=FORMAT   Input format: csv, json (auto-detected from extension)
 *   --dry-run         Preview changes without modifying files
 *   --backup          Create backup before importing (default: true)
 */

class TranslationImporter
{
    private $translationsPath;
    private $options = [];
    private $stats = [
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0
    ];

    public function __construct(array $options = [])
    {
        $this->translationsPath = dirname(__DIR__, 2) . '/translations';
        $this->options = array_merge([
            'lang' => null,
            'input' => null,
            'format' => null,
            'dry-run' => false,
            'backup' => true
        ], $options);

        if (empty($this->options['lang'])) {
            throw new Exception('--lang parameter is required');
        }

        if (empty($this->options['input'])) {
            throw new Exception('--input parameter is required');
        }

        if (!file_exists($this->options['input'])) {
            throw new Exception('Input file not found: ' . $this->options['input']);
        }

        // Auto-detect format if not specified
        if (empty($this->options['format'])) {
            $ext = pathinfo($this->options['input'], PATHINFO_EXTENSION);
            $this->options['format'] = $ext === 'json' ? 'json' : 'csv';
        }
    }

    public function import()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║         Translation Importer                              ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        if ($this->options['dry-run']) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        }

        $lang = $this->options['lang'];
        echo "Importing translations for: {$lang}\n";
        echo "From: {$this->options['input']}\n";
        echo "Format: {$this->options['format']}\n\n";

        // Load import data
        $importData = $this->loadImportData();

        if (empty($importData)) {
            echo "❌ No valid translation data found in input file\n\n";
            return;
        }

        echo "Loaded " . count($importData) . " translations\n\n";

        // Group by domain
        $byDomain = [];
        foreach ($importData as $item) {
            $domain = $item['domain'] ?? 'messages';
            if (!isset($byDomain[$domain])) {
                $byDomain[$domain] = [];
            }
            $byDomain[$domain][] = $item;
        }

        // Import to each domain
        foreach ($byDomain as $domain => $items) {
            $this->importDomain($domain, $lang, $items);
        }

        $this->displaySummary();
    }

    private function loadImportData(): array
    {
        if ($this->options['format'] === 'json') {
            return $this->loadJson();
        } else {
            return $this->loadCsv();
        }
    }

    private function loadJson(): array
    {
        $content = file_get_contents($this->options['input']);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    private function loadCsv(): array
    {
        $data = [];
        $file = fopen($this->options['input'], 'r');

        if (!$file) {
            throw new Exception('Could not open file: ' . $this->options['input']);
        }

        // Read header
        $header = fgetcsv($file);

        if (!$header) {
            fclose($file);
            throw new Exception('Empty CSV file or invalid format');
        }

        // Normalize header (case-insensitive)
        $header = array_map('strtolower', $header);

        // Read data rows
        while (($row = fgetcsv($file)) !== false) {
            $item = array_combine($header, $row);

            // Skip if translation is empty
            if (empty($item['translation'])) {
                continue;
            }

            $data[] = [
                'domain' => $item['domain'] ?? 'messages',
                'key' => $item['key'],
                'translation' => $item['translation']
            ];
        }

        fclose($file);

        return $data;
    }

    private function importDomain(string $domain, string $lang, array $items)
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Domain: {$domain}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $langFile = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";

        if (!file_exists($langFile)) {
            echo "  ❌ Language file not found: {$langFile}\n";
            echo "     Run sync-translations.php first to create it\n\n";
            return;
        }

        // Create backup
        if ($this->options['backup'] && !$this->options['dry-run']) {
            $backupFile = $langFile . '.bak.' . date('Y-m-d_His');
            copy($langFile, $backupFile);
            echo "  📦 Backup created: {$backupFile}\n\n";
        }

        // Parse existing .po file
        $poData = $this->parsePoFile($langFile);

        // Import translations
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $key = $item['key'];
            $translation = $item['translation'];

            if (!isset($poData['entries'][$key])) {
                echo "  ⚠️  Key not found: {$key}\n";
                $this->stats['errors']++;
                continue;
            }

            $existing = $poData['entries'][$key]['msgstr'];

            if (empty($existing)) {
                // New translation
                $poData['entries'][$key]['msgstr'] = $translation;
                $poData['entries'][$key]['fuzzy'] = false; // Remove fuzzy flag
                $imported++;
                $this->stats['imported']++;
            } elseif ($existing !== $translation) {
                // Update existing translation
                $poData['entries'][$key]['msgstr'] = $translation;
                $poData['entries'][$key]['fuzzy'] = false; // Remove fuzzy flag
                $updated++;
                $this->stats['updated']++;
            } else {
                // Same translation
                $skipped++;
                $this->stats['skipped']++;
            }
        }

        echo "  Results:\n";
        if ($imported > 0) echo "    ✅ Imported: {$imported} new translations\n";
        if ($updated > 0) echo "    🔄 Updated: {$updated} existing translations\n";
        if ($skipped > 0) echo "    ⏭️  Skipped: {$skipped} (already correct)\n";

        // Write updated file
        if (!$this->options['dry-run']) {
            $this->writePoFile($langFile, $poData);
            echo "  💾 File saved: {$langFile}\n";
        }

        echo "\n";
    }

    private function parsePoFile(string $file): array
    {
        $content = file_get_contents($file);
        $lines = explode("\n", $content);

        $data = [
            'header' => '',
            'entries' => []
        ];

        $currentEntry = null;
        $currentComments = [];
        $headerLines = [];
        $inHeader = true;

        foreach ($lines as $line) {
            $line = rtrim($line);

            // Collect header
            if ($inHeader && (strpos($line, '#') === 0 || strpos($line, 'msgid ""') !== false || strpos($line, 'msgstr') !== false || strpos($line, '"') === 0)) {
                $headerLines[] = $line;
                if (empty($line) && count($headerLines) > 5) {
                    $inHeader = false;
                }
                continue;
            }

            // Comments
            if (strpos($line, '#') === 0) {
                $currentComments[] = $line;
                continue;
            }

            // msgid line
            if (preg_match('/^msgid\s+"(.*)"$/', $line, $matches)) {
                $msgid = $this->unescapeString($matches[1]);

                if (empty($msgid)) {
                    continue; // Skip header entry
                }

                $currentEntry = [
                    'msgid' => $msgid,
                    'msgstr' => '',
                    'fuzzy' => in_array('#, fuzzy', $currentComments),
                    'comments' => $currentComments
                ];
                $currentComments = [];
                continue;
            }

            // msgstr line
            if (preg_match('/^msgstr\s+"(.*)"$/', $line, $matches)) {
                if ($currentEntry !== null) {
                    $currentEntry['msgstr'] = $this->unescapeString($matches[1]);
                }
                continue;
            }

            // Continuation line
            if (preg_match('/^"(.*)"$/', $line, $matches) && $currentEntry !== null) {
                $currentEntry['msgstr'] .= $this->unescapeString($matches[1]);
                continue;
            }

            // Empty line - end of entry
            if (empty($line) && $currentEntry !== null) {
                $data['entries'][$currentEntry['msgid']] = $currentEntry;
                $currentEntry = null;
            }
        }

        // Add last entry if exists
        if ($currentEntry !== null) {
            $data['entries'][$currentEntry['msgid']] = $currentEntry;
        }

        $data['header'] = implode("\n", $headerLines);

        return $data;
    }

    private function writePoFile(string $file, array $data)
    {
        $content = [];

        // Add header
        $content[] = $data['header'];
        $content[] = '';

        // Add entries
        foreach ($data['entries'] as $entry) {
            // Add comments (but not fuzzy if we're clearing it)
            if (!empty($entry['comments'])) {
                foreach ($entry['comments'] as $comment) {
                    // Skip fuzzy comment if entry is no longer fuzzy
                    if ($comment === '#, fuzzy' && !($entry['fuzzy'] ?? false)) {
                        continue;
                    }
                    $content[] = $comment;
                }
            }

            // Add msgid and msgstr
            $content[] = 'msgid "' . $this->escapeString($entry['msgid']) . '"';
            $content[] = 'msgstr "' . $this->escapeString($entry['msgstr']) . '"';
            $content[] = '';
        }

        file_put_contents($file, implode("\n", $content));
    }

    private function escapeString(string $str): string
    {
        $str = str_replace('\\', '\\\\', $str);
        $str = str_replace('"', '\\"', $str);
        $str = str_replace("\t", '\\t', $str);
        $str = str_replace("\r", '', $str);
        $str = str_replace("\n", '\\n', $str);
        return $str;
    }

    private function unescapeString(string $str): string
    {
        $str = str_replace('\\n', "\n", $str);
        $str = str_replace('\\t', "\t", $str);
        $str = str_replace('\\"', '"', $str);
        $str = str_replace('\\\\', '\\', $str);
        return $str;
    }

    private function displaySummary()
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Import Summary\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "  ✅ Imported (new):  {$this->stats['imported']}\n";
        echo "  🔄 Updated:         {$this->stats['updated']}\n";
        echo "  ⏭️  Skipped (same):  {$this->stats['skipped']}\n";
        echo "  ❌ Errors:          {$this->stats['errors']}\n\n";

        $total = $this->stats['imported'] + $this->stats['updated'];

        if ($this->options['dry-run']) {
            echo "  ⚠️  DRY RUN - No files were actually modified\n";
            echo "     Run without --dry-run to apply changes\n";
        } elseif ($total > 0) {
            echo "  ✅ Successfully imported {$total} translations!\n\n";
            echo "  Next steps:\n";
            echo "    1. Review changes: git diff translations/\n";
            echo "    2. Check completion: php check-missing.php --lang={$this->options['lang']}\n";
            echo "    3. Test in browser: http://your-site/general/translation-demo.php\n";
        } else {
            echo "  ℹ️  No changes made\n";
        }

        echo "\n";
    }
}

// Parse command line arguments
function parseArgs(array $argv): array
{
    $options = [];

    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', substr($arg, 2), 2);
            if (count($parts) === 2) {
                $options[$parts[0]] = $parts[1];
            } else {
                $options[$parts[0]] = true;
            }
        }
    }

    return $options;
}

// Show usage
function showUsage()
{
    echo <<<USAGE
Translation Importer

Imports completed translations from CSV or JSON back into .po files.

Usage:
  php import-translations.php --lang=LANG --input=FILE [OPTIONS]

Required:
  --lang=LANG       Language code to import (e.g., fr, es, de)
  --input=FILE      Input file (CSV or JSON)

Options:
  --format=FORMAT   Force format: csv or json (default: auto-detect)
  --dry-run         Preview changes without modifying files
  --backup          Create backup before importing (default: true)
  --no-backup       Don't create backup files
  --help            Show this help message

CSV Format:
  Expected columns: Domain, Key, Translation
  Optional columns: Status, English, Context

  Example:
    Domain,Key,Status,English,Translation,Context
    messages,please_login,missing,"Please log in","Connectez-vous",general/login.php

JSON Format:
  Expected structure: Array of objects with domain, key, translation

  Example:
    [
      {"domain": "messages", "key": "please_login", "translation": "Connectez-vous"},
      {"domain": "messages", "key": "logout", "translation": "Déconnexion"}
    ]

Examples:
  # Import French translations from CSV
  php import-translations.php --lang=fr --input=french-completed.csv

  # Import Spanish from JSON
  php import-translations.php --lang=es --input=spanish.json

  # Preview without changes
  php import-translations.php --lang=de --input=german.csv --dry-run

  # Import without backup
  php import-translations.php --lang=it --input=italian.csv --no-backup

Workflow:
  1. Export: php export-for-translators.php --lang=fr --output=french.csv
  2. Translator completes translations in Excel/Spreadsheet
  3. Import: php import-translations.php --lang=fr --input=french-completed.csv
  4. Verify: php check-missing.php --lang=fr
  5. Test: Visit translation-demo.php in browser

USAGE;
    exit(0);
}

// Main execution
if (php_sapi_name() === 'cli') {
    $options = parseArgs($argv);

    if (isset($options['help'])) {
        showUsage();
    }

    // Handle --no-backup flag
    if (isset($options['no-backup'])) {
        $options['backup'] = false;
        unset($options['no-backup']);
    }

    try {
        $importer = new TranslationImporter($options);
        $importer->import();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
        showUsage();
        exit(1);
    }
}
