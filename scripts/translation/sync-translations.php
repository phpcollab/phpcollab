#!/usr/bin/env php
<?php
/**
 * Sync Translations Across Languages
 *
 * Synchronizes translation keys from English (master) to all other languages:
 * - Adds missing keys to language files
 * - Marks new entries as "fuzzy" (needs translation)
 * - Preserves existing translations
 * - Optionally removes obsolete keys
 *
 * Usage:
 *   php sync-translations.php [OPTIONS]
 *
 * Options:
 *   --domain=DOMAIN     Sync specific domain: messages, help, custom, or all (default: all)
 *   --lang=LANG         Sync specific language only
 *   --dry-run           Show what would be changed without modifying files
 *   --remove-obsolete   Remove keys that don't exist in master
 *   --backup            Create backup files before modifying
 */

class TranslationSyncer
{
    private $translationsPath;
    private $masterLanguage = 'en';
    private $domains = ['messages', 'help', 'custom'];
    private $languages = [
        'ar', 'az', 'pt-br', 'bg', 'ca', 'zh', 'zh-tw', 'cs-iso',
        'cs-win1250', 'da', 'nl', 'et', 'fr', 'de', 'hu',
        'is', 'in', 'it', 'ja', 'ko', 'lv', 'no', 'pl', 'pt',
        'ro', 'ru', 'sk-win1250', 'es', 'tr', 'uk'
    ];
    private $options = [];
    private $stats = [];

    public function __construct(array $options = [])
    {
        $this->translationsPath = dirname(__DIR__, 2) . '/translations';
        $this->options = array_merge([
            'domain' => 'all',
            'lang' => null,
            'dry-run' => false,
            'remove-obsolete' => false,
            'backup' => true
        ], $options);
    }

    public function sync()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║         Translation Synchronization                       ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        if ($this->options['dry-run']) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        }

        $domains = $this->options['domain'] === 'all'
            ? $this->domains
            : [$this->options['domain']];

        foreach ($domains as $domain) {
            $this->syncDomain($domain);
        }

        $this->displaySummary();
    }

    private function syncDomain(string $domain)
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Domain: " . strtoupper($domain) . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $masterFile = "{$this->translationsPath}/{$domain}/{$domain}.{$this->masterLanguage}.po";

        if (!file_exists($masterFile)) {
            echo "  ❌ Master file not found: {$masterFile}\n\n";
            return;
        }

        $masterData = $this->parsePoFile($masterFile);
        $masterKeys = array_keys($masterData['entries']);
        $totalMasterKeys = count($masterKeys);

        echo "  Master file: {$domain}.{$this->masterLanguage}.po\n";
        echo "  Total keys: {$totalMasterKeys}\n\n";

        $languagesToSync = $this->options['lang']
            ? [$this->options['lang']]
            : $this->languages;

        foreach ($languagesToSync as $lang) {
            if ($lang === $this->masterLanguage) {
                continue; // Skip master language
            }

            $this->syncLanguage($domain, $lang, $masterData);
        }

        echo "\n";
    }

    private function syncLanguage(string $domain, string $lang, array $masterData)
    {
        $langFile = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";
        $exists = file_exists($langFile);

        if (!$exists) {
            echo "  ⚠️  {$lang}: File doesn't exist - creating new file\n";
            $langData = [
                'header' => $this->generateHeader($lang, $domain),
                'entries' => []
            ];
        } else {
            $langData = $this->parsePoFile($langFile);
        }

        // Find missing and obsolete keys
        $masterKeys = array_keys($masterData['entries']);
        $langKeys = array_keys($langData['entries']);
        $missing = array_diff($masterKeys, $langKeys);
        $obsolete = array_diff($langKeys, $masterKeys);

        // Stats
        $added = 0;
        $removed = 0;
        $preserved = count(array_intersect($masterKeys, $langKeys));

        // Add missing keys
        foreach ($missing as $key) {
            $langData['entries'][$key] = [
                'msgid' => $key,
                'msgstr' => '', // Empty - needs translation
                'fuzzy' => true, // Mark as needing translation
                'comments' => ["# NEEDS TRANSLATION"]
            ];
            $added++;
        }

        // Handle obsolete keys
        if ($this->options['remove-obsolete']) {
            foreach ($obsolete as $key) {
                unset($langData['entries'][$key]);
                $removed++;
            }
        }

        // Report changes
        if ($added > 0 || $removed > 0) {
            $symbol = $added > 0 ? '⚡' : '✓';
            echo "  {$symbol} {$lang}: ";
            $changes = [];
            if ($added > 0) $changes[] = "+{$added} added";
            if ($removed > 0) $changes[] = "-{$removed} removed";
            if ($preserved > 0) $changes[] = "{$preserved} preserved";
            echo implode(', ', $changes) . "\n";

            // Update stats
            if (!isset($this->stats[$lang])) {
                $this->stats[$lang] = ['added' => 0, 'removed' => 0, 'preserved' => 0];
            }
            $this->stats[$lang]['added'] += $added;
            $this->stats[$lang]['removed'] += $removed;
            $this->stats[$lang]['preserved'] += $preserved;

            // Write changes (unless dry-run)
            if (!$this->options['dry-run']) {
                // Create backup if requested
                if ($this->options['backup'] && $exists) {
                    $backupFile = $langFile . '.bak.' . date('Y-m-d_His');
                    copy($langFile, $backupFile);
                }

                // Write updated file
                $this->writePoFile($langFile, $langData);
            }
        } else {
            echo "  ✓ {$lang}: Already in sync\n";
        }
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
        $inHeader = true;
        $currentComments = [];

        foreach ($lines as $line) {
            $line = rtrim($line);

            // Comments
            if (strpos($line, '#') === 0) {
                $currentComments[] = $line;
                continue;
            }

            // msgid line
            if (preg_match('/^msgid\s+"(.*)"$/', $line, $matches)) {
                $msgid = $this->unescapeString($matches[1]);

                if (empty($msgid)) {
                    // This is the header
                    $inHeader = true;
                    continue;
                }

                $inHeader = false;
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

        return $data;
    }

    private function writePoFile(string $file, array $data)
    {
        $content = [];

        // Add header
        if (!empty($data['header'])) {
            $content[] = $data['header'];
        } else {
            // Generate default header
            $lang = basename($file, '.po');
            $lang = substr($lang, strrpos($lang, '.') + 1);
            $domain = dirname($file);
            $domain = basename($domain);
            $content[] = $this->generateHeader($lang, $domain);
        }

        $content[] = '';

        // Add entries
        foreach ($data['entries'] as $entry) {
            // Add comments
            if (!empty($entry['comments'])) {
                foreach ($entry['comments'] as $comment) {
                    $content[] = $comment;
                }
            }

            // Add fuzzy marker if needed
            if ($entry['fuzzy'] && !in_array('#, fuzzy', $entry['comments'] ?? [])) {
                $content[] = '#, fuzzy';
            }

            // Add msgid and msgstr
            $content[] = 'msgid "' . $this->escapeString($entry['msgid']) . '"';
            $content[] = 'msgstr "' . $this->escapeString($entry['msgstr']) . '"';
            $content[] = '';
        }

        // Write to file
        file_put_contents($file, implode("\n", $content));
    }

    private function generateHeader(string $lang, string $domain): string
    {
        $date = date('Y-m-d H:i:s O');

        return <<<HEADER
# phpCollab Translation File
# Language: {$lang}
# Domain: {$domain}
# Synced: {$date}

msgid ""
msgstr ""
"Project-Id-Version: phpCollab 2.x\\n"
"Language: {$lang}\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
HEADER;
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
        echo "  Summary\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        if (empty($this->stats)) {
            echo "  ✓ All languages are already in sync!\n\n";
            return;
        }

        $totalAdded = 0;
        $totalRemoved = 0;
        $languagesAffected = 0;

        foreach ($this->stats as $lang => $stats) {
            $totalAdded += $stats['added'];
            $totalRemoved += $stats['removed'];
            if ($stats['added'] > 0 || $stats['removed'] > 0) {
                $languagesAffected++;
            }
        }

        echo "  Languages affected: {$languagesAffected}\n";
        echo "  Total keys added: {$totalAdded}\n";
        if ($this->options['remove-obsolete']) {
            echo "  Total keys removed: {$totalRemoved}\n";
        }

        if ($this->options['dry-run']) {
            echo "\n  ⚠️  DRY RUN - No files were actually modified\n";
            echo "     Run without --dry-run to apply changes\n";
        } else {
            echo "\n  ✅ Synchronization complete!\n";
            if ($this->options['backup']) {
                echo "  📦 Backup files created (.bak.* files)\n";
            }
        }

        echo "\n";
        echo "Next steps:\n";
        echo "  1. Review changes: git diff translations/\n";
        echo "  2. Check completion: php check-missing.php\n";
        echo "  3. Send to translators: php export-for-translators.php\n";
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
Translation Synchronization Tool

Syncs translation keys from English (master) to all other languages.

Usage:
  php sync-translations.php [OPTIONS]

Options:
  --domain=DOMAIN      Sync specific domain: messages, help, custom, or all (default: all)
  --lang=LANG          Sync specific language only (e.g., fr, es, de)
  --dry-run            Show what would change without modifying files
  --remove-obsolete    Remove keys that don't exist in master file
  --backup             Create backup files before modifying (default: true)
  --no-backup          Don't create backup files
  --help               Show this help message

Examples:
  # Sync all domains for all languages (safe - creates backups)
  php sync-translations.php

  # Preview changes without modifying files
  php sync-translations.php --dry-run

  # Sync only messages domain
  php sync-translations.php --domain=messages

  # Sync only French translations
  php sync-translations.php --lang=fr

  # Sync and remove obsolete keys
  php sync-translations.php --remove-obsolete

  # Sync without creating backups
  php sync-translations.php --no-backup

How it works:
  1. Reads English (master) .po file
  2. For each other language:
     - Adds missing keys (marked as "fuzzy" - needs translation)
     - Preserves existing translations
     - Optionally removes obsolete keys (--remove-obsolete)
  3. Creates backup files (.bak.TIMESTAMP) before modifying

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
        $syncer = new TranslationSyncer($options);
        $syncer->sync();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
