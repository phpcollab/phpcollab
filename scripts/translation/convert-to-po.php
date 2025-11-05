#!/usr/bin/env php
<?php
/**
 * PHP Array to .po File Converter
 *
 * Converts phpCollab's PHP array-based language files to .po (Portable Object) format
 *
 * Usage:
 *   php convert-to-po.php --input=languages/lang_en.php --output=translations/messages/messages.en.po --type=lang [--domain=messages]
 *   php convert-to-po.php --input=languages/help_en.php --output=translations/help/help.en.po --type=help
 *   php convert-to-po.php --input=languages/custom_en.php --output=translations/custom/custom.en.po --type=custom
 *
 * Options:
 *   --input    : Input PHP file path (required)
 *   --output   : Output .po file path (required)
 *   --type     : File type: lang, help, or custom (required)
 *   --domain   : Translation domain (optional, default: messages)
 *   --lang     : Language code (optional, auto-detected from filename)
 *   --verbose  : Show verbose output
 */

class PhpToPoConverter
{
    private $input;
    private $output;
    private $type;
    private $domain;
    private $lang;
    private $verbose;
    private $stats = [
        'strings' => 0,
        'enums' => 0,
        'arrays' => 0,
        'total' => 0
    ];

    public function __construct(array $options)
    {
        $this->input = $options['input'] ?? null;
        $this->output = $options['output'] ?? null;
        $this->type = $options['type'] ?? 'lang';
        $this->domain = $options['domain'] ?? 'messages';
        $this->lang = $options['lang'] ?? $this->detectLanguage($this->input);
        $this->verbose = isset($options['verbose']);

        $this->validate();
    }

    private function validate()
    {
        if (!$this->input) {
            $this->error("--input is required");
        }
        if (!$this->output) {
            $this->error("--output is required");
        }
        if (!file_exists($this->input)) {
            $this->error("Input file does not exist: {$this->input}");
        }
        if (!in_array($this->type, ['lang', 'help', 'custom'])) {
            $this->error("Invalid type. Must be: lang, help, or custom");
        }
    }

    private function detectLanguage(string $filename): string
    {
        // Extract language code from filename like "lang_en.php" or "help_fr.php"
        if (preg_match('/(lang|help|custom)_([a-z-]+)\.php$/i', $filename, $matches)) {
            return $matches[2];
        }
        return 'en';
    }

    public function convert()
    {
        $this->log("Converting {$this->input} to {$this->output}");
        $this->log("Type: {$this->type}, Domain: {$this->domain}, Language: {$this->lang}");

        // Load PHP file and extract variables
        $data = $this->loadPhpFile();

        // Generate .po content
        $poContent = $this->generatePoContent($data);

        // Ensure output directory exists
        $outputDir = dirname($this->output);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
            $this->log("Created directory: {$outputDir}");
        }

        // Write .po file
        file_put_contents($this->output, $poContent);

        $this->log("Conversion complete!");
        $this->log("Statistics:");
        $this->log("  - String translations: {$this->stats['strings']}");
        $this->log("  - Enum values: {$this->stats['enums']}");
        $this->log("  - Arrays: {$this->stats['arrays']}");
        $this->log("  - Total entries: {$this->stats['total']}");
    }

    private function loadPhpFile(): array
    {
        $this->log("Loading PHP file...");

        // Include the file to get variables
        ob_start();
        include $this->input;
        ob_end_clean();

        $data = [];

        // Capture all defined variables
        $vars = get_defined_vars();

        // Based on file type, extract relevant data
        switch ($this->type) {
            case 'lang':
                $data = $this->extractLangData($vars);
                break;
            case 'help':
                $data = $this->extractHelpData($vars);
                break;
            case 'custom':
                $data = $this->extractCustomData($vars);
                break;
        }

        return $data;
    }

    private function extractLangData(array $vars): array
    {
        $data = ['strings' => [], 'enums' => [], 'arrays' => []];

        // Extract $strings array
        if (isset($vars['strings']) && is_array($vars['strings'])) {
            $data['strings'] = $this->flattenArray($vars['strings'], 'strings');
            $this->stats['strings'] = count($data['strings']);
        }

        // Extract enum arrays (status, priority, profil, etc.)
        $enumTypes = [
            'status', 'profil', 'priority', 'statusTopic', 'statusTopicBis',
            'statusPublish', 'statusFile', 'phaseStatus', 'requestStatus',
            'invoiceStatus', 'logLevels'
        ];

        foreach ($enumTypes as $enumType) {
            if (isset($vars[$enumType]) && is_array($vars[$enumType])) {
                foreach ($vars[$enumType] as $key => $value) {
                    if (is_string($value)) {
                        $data['enums']["{$enumType}.{$key}"] = $value;
                        $this->stats['enums']++;
                    }
                }
            }
        }

        // Extract other arrays (byteUnits, dayNameArray, monthNameArray)
        $arrayTypes = ['byteUnits', 'dayNameArray', 'monthNameArray'];

        foreach ($arrayTypes as $arrayType) {
            if (isset($vars[$arrayType]) && is_array($vars[$arrayType])) {
                foreach ($vars[$arrayType] as $key => $value) {
                    if (is_string($value)) {
                        // Convert to dotted notation
                        $normalizedKey = $this->normalizeArrayKey($arrayType, $key);
                        $data['arrays'][$normalizedKey] = $value;
                        $this->stats['arrays']++;
                    }
                }
            }
        }

        return $data;
    }

    private function extractHelpData(array $vars): array
    {
        $data = ['help' => []];

        if (isset($vars['help']) && is_array($vars['help'])) {
            $data['help'] = $vars['help'];
        }

        return $data;
    }

    private function extractCustomData(array $vars): array
    {
        $data = ['custom' => []];

        // Extract custom variables (topicNote, phaseArraySets, etc.)
        $customTypes = ['topicNote', 'phaseArraySets', 'bookmarkType'];

        foreach ($customTypes as $customType) {
            if (isset($vars[$customType]) && is_array($vars[$customType])) {
                $flattened = $this->flattenArray($vars[$customType], $customType);
                $data['custom'] = array_merge($data['custom'], $flattened);
            }
        }

        return $data;
    }

    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                // Recursively flatten nested arrays
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } elseif (is_string($value)) {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    private function normalizeArrayKey(string $arrayType, $key): string
    {
        // Convert array type to lowercase, remove 'Array' suffix
        $prefix = strtolower(str_replace('Array', '', $arrayType));

        return "{$prefix}.{$key}";
    }

    private function generatePoContent(array $data): string
    {
        $po = [];

        // Add header
        $po[] = $this->generatePoHeader();
        $po[] = "";

        // Add entries based on file type
        switch ($this->type) {
            case 'lang':
                // Add string translations
                if (!empty($data['strings'])) {
                    $po[] = "# String translations";
                    $po[] = "";
                    foreach ($data['strings'] as $key => $value) {
                        $po[] = $this->generatePoEntry($key, $value);
                        $this->stats['total']++;
                    }
                }

                // Add enum values to separate domain
                if (!empty($data['enums'])) {
                    $po[] = "";
                    $po[] = "# Enum values (status, priority, etc.)";
                    $po[] = "# Note: These should be moved to enums.*.po domain";
                    $po[] = "";
                    foreach ($data['enums'] as $key => $value) {
                        $po[] = $this->generatePoEntry($key, $value, 'enums');
                        $this->stats['total']++;
                    }
                }

                // Add arrays (days, months, byte units)
                if (!empty($data['arrays'])) {
                    $po[] = "";
                    $po[] = "# Arrays (days, months, byte units)";
                    $po[] = "";
                    foreach ($data['arrays'] as $key => $value) {
                        $po[] = $this->generatePoEntry($key, $value);
                        $this->stats['total']++;
                    }
                }
                break;

            case 'help':
                if (!empty($data['help'])) {
                    $po[] = "# Help text";
                    $po[] = "";
                    foreach ($data['help'] as $key => $value) {
                        $po[] = $this->generatePoEntry($key, $value);
                        $this->stats['total']++;
                    }
                }
                break;

            case 'custom':
                if (!empty($data['custom'])) {
                    $po[] = "# Custom application values";
                    $po[] = "";
                    foreach ($data['custom'] as $key => $value) {
                        $po[] = $this->generatePoEntry($key, $value);
                        $this->stats['total']++;
                    }
                }
                break;
        }

        return implode("\n", $po) . "\n";
    }

    private function generatePoHeader(): string
    {
        $language = $this->lang;
        $languageName = $this->getLanguageName($language);
        $date = date('Y-m-d H:i:s O');
        $type = ucfirst($this->type);

        $header = <<<PO
# phpCollab Translation File
# Language: {$languageName} ({$language})
# Type: {$type}
# Domain: {$this->domain}
# Generated: {$date}

msgid ""
msgstr ""
"Project-Id-Version: phpCollab 2.x\\n"
"Language: {$language}\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
PO;

        return $header;
    }

    private function generatePoEntry(string $key, string $value, string $domain = null): string
    {
        $entry = [];

        // Add domain comment if different from current domain
        if ($domain && $domain !== $this->domain) {
            $entry[] = "#. Domain: {$domain}";
        }

        // Escape the value
        $escapedValue = $this->escapePoString($value);

        // Single-line format
        if (strlen($escapedValue) < 70 && strpos($escapedValue, "\n") === false) {
            $entry[] = "msgid \"{$key}\"";
            $entry[] = "msgstr \"{$escapedValue}\"";
        } else {
            // Multi-line format
            $entry[] = "msgid \"{$key}\"";
            $entry[] = "msgstr \"\"";
            $lines = $this->wrapPoString($escapedValue);
            foreach ($lines as $line) {
                $entry[] = "\"{$line}\"";
            }
        }

        $entry[] = "";

        return implode("\n", $entry);
    }

    private function escapePoString(string $str): string
    {
        // Escape special characters for .po format
        $str = str_replace('\\', '\\\\', $str);  // Escape backslashes
        $str = str_replace('"', '\\"', $str);    // Escape quotes
        $str = str_replace("\t", '\\t', $str);   // Escape tabs
        $str = str_replace("\r", '', $str);      // Remove carriage returns
        $str = str_replace("\n", '\\n', $str);   // Escape newlines

        return $str;
    }

    private function wrapPoString(string $str, int $width = 70): array
    {
        $lines = [];
        $parts = explode('\\n', $str);

        foreach ($parts as $i => $part) {
            if ($i > 0) {
                $lines[count($lines) - 1] .= '\\n';
            }

            if (strlen($part) <= $width) {
                $lines[] = $part;
            } else {
                // Wrap long lines
                $words = explode(' ', $part);
                $currentLine = '';

                foreach ($words as $word) {
                    if (strlen($currentLine) + strlen($word) + 1 <= $width) {
                        $currentLine .= ($currentLine ? ' ' : '') . $word;
                    } else {
                        if ($currentLine) {
                            $lines[] = $currentLine;
                        }
                        $currentLine = $word;
                    }
                }

                if ($currentLine) {
                    $lines[] = $currentLine;
                }
            }
        }

        return $lines;
    }

    private function getLanguageName(string $code): string
    {
        $languages = [
            'en' => 'English',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'pt-br' => 'Brazilian Portuguese',
            'nl' => 'Dutch',
            'ru' => 'Russian',
            'pl' => 'Polish',
            'ja' => 'Japanese',
            'zh' => 'Chinese (Simplified)',
            'zh-tw' => 'Chinese (Traditional)',
            'ar' => 'Arabic',
            'bg' => 'Bulgarian',
            'ca' => 'Catalan',
            'cs-iso' => 'Czech (ISO)',
            'cs-win1250' => 'Czech (Win1250)',
            'da' => 'Danish',
            'et' => 'Estonian',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'in' => 'Indonesian',
            'ko' => 'Korean',
            'lv' => 'Latvian',
            'no' => 'Norwegian',
            'ro' => 'Romanian',
            'sk-win1250' => 'Slovak (Win1250)',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'az' => 'Azerbaijani',
        ];

        return $languages[$code] ?? ucfirst($code);
    }

    private function log(string $message)
    {
        if ($this->verbose) {
            echo "[" . date('H:i:s') . "] {$message}\n";
        }
    }

    private function error(string $message)
    {
        echo "ERROR: {$message}\n";
        exit(1);
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
PHP Array to .po File Converter

Usage:
  php convert-to-po.php --input=FILE --output=FILE --type=TYPE [OPTIONS]

Required Arguments:
  --input=FILE    Input PHP file path
  --output=FILE   Output .po file path
  --type=TYPE     File type: lang, help, or custom

Optional Arguments:
  --domain=DOMAIN Translation domain (default: messages)
  --lang=CODE     Language code (default: auto-detect from filename)
  --verbose       Show verbose output

Examples:
  # Convert main language file
  php convert-to-po.php --input=languages/lang_en.php --output=translations/messages/messages.en.po --type=lang --verbose

  # Convert help file
  php convert-to-po.php --input=languages/help_fr.php --output=translations/help/help.fr.po --type=help

  # Convert custom file
  php convert-to-po.php --input=languages/custom_en.php --output=translations/custom/custom.en.po --type=custom

USAGE;
    exit(0);
}

// Main execution
if (php_sapi_name() === 'cli') {
    $options = parseArgs($argv);

    if (isset($options['help']) || empty($options)) {
        showUsage();
    }

    try {
        $converter = new PhpToPoConverter($options);
        $converter->convert();
        echo "✓ Conversion successful!\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
