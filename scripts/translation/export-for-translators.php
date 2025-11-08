#!/usr/bin/env php
<?php
/**
 * Export Translations for Translators
 *
 * Exports missing or fuzzy translations in formats easy for translators:
 * - CSV (Excel-compatible)
 * - JSON
 * - Text table
 *
 * Usage:
 *   php export-for-translators.php --lang=fr [OPTIONS]
 *
 * Options:
 *   --lang=LANG         Language to export (required)
 *   --domain=DOMAIN     Export specific domain or 'all' (default: all)
 *   --format=FORMAT     Output format: csv, json, text (default: csv)
 *   --output=FILE       Output file (default: stdout)
 *   --only-missing      Export only missing translations (default: missing + fuzzy)
 *   --with-english      Include English translation as reference
 */

class TranslationExporter
{
    private $translationsPath;
    private $masterLanguage = 'en';
    private $domains = ['messages', 'help', 'custom'];
    private $options = [];

    public function __construct(array $options = [])
    {
        $this->translationsPath = dirname(__DIR__, 2) . '/translations';
        $this->options = array_merge([
            'lang' => null,
            'domain' => 'all',
            'format' => 'csv',
            'output' => null,
            'only-missing' => false,
            'with-english' => true
        ], $options);

        if (empty($this->options['lang'])) {
            throw new Exception('--lang parameter is required');
        }
    }

    public function export()
    {
        $lang = $this->options['lang'];
        $domains = $this->options['domain'] === 'all'
            ? $this->domains
            : [$this->options['domain']];

        $exportData = [];

        foreach ($domains as $domain) {
            $data = $this->extractDomainData($domain, $lang);
            if (!empty($data)) {
                $exportData = array_merge($exportData, $data);
            }
        }

        if (empty($exportData)) {
            echo "No missing or fuzzy translations found for language '{$lang}'\n";
            echo "This language might be 100% complete!\n\n";
            echo "Run: php check-missing.php --lang={$lang}\n";
            return;
        }

        // Export in requested format
        $output = $this->formatOutput($exportData);

        // Write to file or stdout
        if ($this->options['output']) {
            file_put_contents($this->options['output'], $output);
            echo "Exported " . count($exportData) . " translations to: {$this->options['output']}\n";
        } else {
            echo $output;
        }
    }

    private function extractDomainData(string $domain, string $lang): array
    {
        $masterFile = "{$this->translationsPath}/{$domain}/{$domain}.{$this->masterLanguage}.po";
        $langFile = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";

        if (!file_exists($masterFile)) {
            return [];
        }

        $masterEntries = $this->parsePoFile($masterFile);
        $langEntries = file_exists($langFile) ? $this->parsePoFile($langFile) : [];

        $exportData = [];

        foreach ($masterEntries as $key => $masterEntry) {
            $needsExport = false;
            $status = '';
            $currentTranslation = '';

            if (!isset($langEntries[$key])) {
                // Missing entirely
                $needsExport = true;
                $status = 'missing';
            } elseif (empty($langEntries[$key]['msgstr'])) {
                // Exists but empty
                $needsExport = true;
                $status = 'empty';
            } elseif ($langEntries[$key]['fuzzy'] ?? false) {
                // Marked as fuzzy (needs review)
                if (!$this->options['only-missing']) {
                    $needsExport = true;
                    $status = 'fuzzy';
                    $currentTranslation = $langEntries[$key]['msgstr'];
                }
            }

            if ($needsExport) {
                $exportData[] = [
                    'domain' => $domain,
                    'key' => $key,
                    'english' => $masterEntry['msgstr'],
                    'current' => $currentTranslation,
                    'status' => $status,
                    'context' => $masterEntry['comments'] ?? []
                ];
            }
        }

        return $exportData;
    }

    private function parsePoFile(string $file): array
    {
        $content = file_get_contents($file);
        $lines = explode("\n", $content);

        $entries = [];
        $currentEntry = null;
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
                    continue; // Skip header
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
                $entries[$currentEntry['msgid']] = $currentEntry;
                $currentEntry = null;
            }
        }

        // Add last entry if exists
        if ($currentEntry !== null) {
            $entries[$currentEntry['msgid']] = $currentEntry;
        }

        return $entries;
    }

    private function unescapeString(string $str): string
    {
        $str = str_replace('\\n', "\n", $str);
        $str = str_replace('\\t', "\t", $str);
        $str = str_replace('\\"', '"', $str);
        $str = str_replace('\\\\', '\\', $str);
        return $str;
    }

    private function formatOutput(array $data): string
    {
        switch ($this->options['format']) {
            case 'json':
                return $this->formatJson($data);
            case 'text':
                return $this->formatText($data);
            case 'csv':
            default:
                return $this->formatCsv($data);
        }
    }

    private function formatCsv(array $data): string
    {
        $csv = [];

        // Header
        $headers = ['Domain', 'Key', 'Status'];
        if ($this->options['with-english']) {
            $headers[] = 'English';
        }
        $headers[] = 'Translation';
        $headers[] = 'Context';

        $csv[] = $headers;

        // Data rows
        foreach ($data as $item) {
            $row = [
                $item['domain'],
                $item['key'],
                $item['status']
            ];

            if ($this->options['with-english']) {
                $row[] = $item['english'];
            }

            $row[] = $item['current'];

            // Context (file references from comments)
            $context = '';
            foreach ($item['context'] as $comment) {
                if (strpos($comment, '#:') === 0) {
                    $context = trim(substr($comment, 2));
                    break;
                }
            }
            $row[] = $context;

            $csv[] = $row;
        }

        // Convert to CSV string
        $output = '';
        foreach ($csv as $row) {
            $output .= implode(',', array_map(function($field) {
                // Escape fields containing commas, quotes, or newlines
                if (strpos($field, ',') !== false ||
                    strpos($field, '"') !== false ||
                    strpos($field, "\n") !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row)) . "\n";
        }

        return $output;
    }

    private function formatJson(array $data): string
    {
        $simplified = array_map(function($item) {
            $result = [
                'domain' => $item['domain'],
                'key' => $item['key'],
                'status' => $item['status'],
                'translation' => $item['current']
            ];

            if ($this->options['with-english']) {
                $result['english'] = $item['english'];
            }

            return $result;
        }, $data);

        return json_encode($simplified, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }

    private function formatText(array $data): string
    {
        $lang = $this->options['lang'];
        $output = "\n";
        $output .= "Translation Worksheet for: {$lang}\n";
        $output .= str_repeat("=", 60) . "\n";
        $output .= "Total items: " . count($data) . "\n\n";

        $byDomain = [];
        foreach ($data as $item) {
            $byDomain[$item['domain']][] = $item;
        }

        foreach ($byDomain as $domain => $items) {
            $output .= "\n" . strtoupper($domain) . " (" . count($items) . " items)\n";
            $output .= str_repeat("-", 60) . "\n\n";

            foreach ($items as $item) {
                $output .= "Key: {$item['key']}\n";
                $output .= "Status: {$item['status']}\n";

                if ($this->options['with-english']) {
                    $output .= "English: {$item['english']}\n";
                }

                if (!empty($item['current'])) {
                    $output .= "Current: {$item['current']}\n";
                }

                $output .= "Translation: _______________________________________________\n";
                $output .= "\n";
            }
        }

        return $output;
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
Translation Exporter for Translators

Exports missing or fuzzy translations in translator-friendly formats.

Usage:
  php export-for-translators.php --lang=LANG [OPTIONS]

Required:
  --lang=LANG         Language code to export (e.g., fr, es, de)

Options:
  --domain=DOMAIN     Export specific domain or 'all' (default: all)
  --format=FORMAT     Output format: csv, json, text (default: csv)
  --output=FILE       Output file (default: stdout)
  --only-missing      Export only missing translations (exclude fuzzy)
  --with-english      Include English reference (default: true)
  --no-english        Don't include English reference
  --help              Show this help message

Examples:
  # Export French missing translations to CSV
  php export-for-translators.php --lang=fr --output=french-todo.csv

  # Export Spanish as JSON
  php export-for-translators.php --lang=es --format=json > spanish.json

  # Export German as text worksheet
  php export-for-translators.php --lang=de --format=text > german-worksheet.txt

  # Export only messages domain
  php export-for-translators.php --lang=it --domain=messages --output=italian-messages.csv

  # Preview to console
  php export-for-translators.php --lang=ja --format=text

Workflow:
  1. Export missing translations: php export-for-translators.php --lang=fr --output=french.csv
  2. Send CSV to translator
  3. Receive completed CSV from translator
  4. Import: php import-translations.php --lang=fr --input=french-completed.csv

Output formats:
  csv   - Excel-compatible CSV (easy for translators)
  json  - JSON format (for programmatic use)
  text  - Human-readable text worksheet (for printing)

USAGE;
    exit(0);
}

// Main execution
if (php_sapi_name() === 'cli') {
    $options = parseArgs($argv);

    if (isset($options['help'])) {
        showUsage();
    }

    // Handle --no-english flag
    if (isset($options['no-english'])) {
        $options['with-english'] = false;
        unset($options['no-english']);
    }

    try {
        $exporter = new TranslationExporter($options);
        $exporter->export();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
        showUsage();
        exit(1);
    }
}
