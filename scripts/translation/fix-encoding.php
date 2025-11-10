#!/usr/bin/env php
<?php
/**
 * Fix Character Encoding in .po Files
 *
 * Fixes character encoding issues in translation files:
 * - Converts to proper UTF-8
 * - Fixes corrupted accented characters (é, è, à, ô, ü, ñ, etc.)
 * - Ensures proper encoding declarations
 *
 * Usage:
 *   php fix-encoding.php [OPTIONS]
 *
 * Options:
 *   --file=FILE         Fix specific file
 *   --lang=LANG         Fix specific language only
 *   --domain=DOMAIN     Fix specific domain (messages, help, custom, all)
 *   --dry-run           Show what would be fixed without modifying
 *   --backup            Create backups (default: true)
 */

class EncodingFixer
{
    private $translationsPath;
    private $languages = [
        'ar', 'az', 'pt-br', 'bg', 'ca', 'zh', 'zh-tw', 'cs-iso',
        'cs-win1250', 'da', 'nl', 'en', 'et', 'fr', 'de', 'hu',
        'is', 'in', 'it', 'ja', 'ko', 'lv', 'no', 'pl', 'pt',
        'ro', 'ru', 'sk-win1250', 'es', 'tr', 'uk'
    ];
    private $domains = ['messages', 'help', 'custom'];
    private $options = [];
    private $stats = [
        'files_processed' => 0,
        'files_fixed' => 0,
        'chars_fixed' => 0,
        'errors' => 0
    ];

    public function __construct(array $options = [])
    {
        $this->translationsPath = dirname(__DIR__, 2) . '/translations';
        $this->options = array_merge([
            'file' => null,
            'lang' => null,
            'domain' => 'all',
            'dry-run' => false,
            'backup' => true
        ], $options);
    }

    public function fix()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║         Character Encoding Fixer                         ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        if ($this->options['dry-run']) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        }

        // Fix specific file
        if ($this->options['file']) {
            $this->fixFile($this->options['file']);
        }
        // Fix specific language
        elseif ($this->options['lang']) {
            $this->fixLanguage($this->options['lang']);
        }
        // Fix all
        else {
            $this->fixAll();
        }

        $this->displaySummary();
    }

    private function fixAll()
    {
        $domains = $this->options['domain'] === 'all'
            ? $this->domains
            : [$this->options['domain']];

        foreach ($domains as $domain) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "  Domain: " . strtoupper($domain) . "\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

            foreach ($this->languages as $lang) {
                $file = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";
                if (file_exists($file)) {
                    $this->fixFile($file);
                }
            }

            echo "\n";
        }
    }

    private function fixLanguage(string $lang)
    {
        echo "Fixing language: {$lang}\n\n";

        $domains = $this->options['domain'] === 'all'
            ? $this->domains
            : [$this->options['domain']];

        foreach ($domains as $domain) {
            $file = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";
            if (file_exists($file)) {
                $this->fixFile($file);
            } else {
                echo "  ⚠️  File not found: {$file}\n";
            }
        }
    }

    private function fixFile(string $file)
    {
        $this->stats['files_processed']++;
        $filename = basename($file);

        // Read file
        $content = file_get_contents($file);
        $originalContent = $content;

        // Detect and fix encoding issues
        $fixed = $this->fixEncoding($content);
        $charsFix = $fixed['chars_fixed'];

        if ($charsFix > 0) {
            echo "  🔧 {$filename}: Fixed {$charsFix} characters\n";
            $this->stats['files_fixed']++;
            $this->stats['chars_fixed'] += $charsFix;

            // Create backup
            if ($this->options['backup'] && !$this->options['dry-run']) {
                $backupFile = $file . '.bak.' . date('Y-m-d_His');
                copy($file, $backupFile);
            }

            // Write fixed content
            if (!$this->options['dry-run']) {
                file_put_contents($file, $fixed['content']);
            }
        } else {
            echo "  ✓ {$filename}: Already correct\n";
        }
    }

    private function fixEncoding(string $content): array
    {
        $charsFix = 0;
        $original = $content;

        // Common encoding issues: corrupted UTF-8 from Latin-1/Windows-1252
        $replacements = [
            // French accents
            'Ã©' => 'é',  // Connectez-vous
            'Ã¨' => 'è',
            'Ãª' => 'ê',
            'Ã ' => 'à',
            'Ã¢' => 'â',
            'Ã´' => 'ô',
            'Ã»' => 'û',
            'Ã§' => 'ç',
            'Ã¯' => 'ï',
            'Ã¼' => 'ü',

            // Uppercase
            'Ã‰' => 'É',
            'Ãˆ' => 'È',
            'ÃŠ' => 'Ê',
            'Ã€' => 'À',
            'Ã‚' => 'Â',
            'Ã"' => 'Ô',
            'Ã›' => 'Û',
            'Ã‡' => 'Ç',

            // Spanish
            'Ã±' => 'ñ',
            'Ã³' => 'ó',
            'Ãº' => 'ú',
            'Ã­' => 'í',
            'Ã¡' => 'á',
            'Ã' => 'Ñ',
            'Ã"' => 'Ó',
            'Ãš' => 'Ú',
            'Ã' => 'Í',
            'Ã' => 'Á',

            // German
            'Ã¤' => 'ä',
            'Ã¶' => 'ö',
            'ÃŸ' => 'ß',
            'Ã„' => 'Ä',
            'Ã–' => 'Ö',
            'Ãœ' => 'Ü',

            // Portuguese
            'Ã£' => 'ã',
            'Ãµ' => 'õ',
            'Ã' => 'Ã',
            'Ãµ' => 'Õ',

            // Generic replacement character
            'ï¿½' => '',  // Often shows as �
            '�' => '',     // Replacement character
        ];

        // Apply replacements
        foreach ($replacements as $bad => $good) {
            $count = 0;
            $content = str_replace($bad, $good, $content, $count);
            $charsFix += $count;
        }

        // Fix common Windows-1252 to UTF-8 issues
        $windows1252Map = [
            "\xC3\xA9" => 'é',  // Already UTF-8 é
            "\xE9" => 'é',       // Windows-1252 é
            "\xE8" => 'è',
            "\xE0" => 'à',
            "\xE2" => 'â',
            "\xEA" => 'ê',
            "\xF4" => 'ô',
            "\xFB" => 'û',
            "\xE7" => 'ç',
            "\xEF" => 'ï',
            "\xFC" => 'ü',
            "\xF1" => 'ñ',
            "\xF3" => 'ó',
            "\xFA" => 'ú',
            "\xED" => 'í',
            "\xE1" => 'á',
            "\xE4" => 'ä',
            "\xF6" => 'ö',
            "\xDF" => 'ß',
            "\xE3" => 'ã',
            "\xF5" => 'õ',
        ];

        // Only apply if content looks like it has Windows-1252 bytes
        if (!mb_check_encoding($content, 'UTF-8')) {
            foreach ($windows1252Map as $bad => $good) {
                $count = 0;
                $content = str_replace($bad, $good, $content, $count);
                $charsFix += $count;
            }
        }

        // Ensure content is valid UTF-8
        if (!mb_check_encoding($content, 'UTF-8')) {
            // Try to convert from common encodings
            $encodings = ['ISO-8859-1', 'Windows-1252', 'ISO-8859-15'];
            foreach ($encodings as $encoding) {
                $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
                if ($converted && mb_check_encoding($converted, 'UTF-8')) {
                    $content = $converted;
                    if ($content !== $original) {
                        $charsFix += 10; // Approximate
                    }
                    break;
                }
            }
        }

        // Ensure header declares UTF-8
        if (strpos($content, 'charset=') !== false) {
            $content = preg_replace(
                '/charset=[^\\\\]+/',
                'charset=UTF-8',
                $content,
                -1,
                $count
            );
            if ($count > 0 && strpos($original, 'charset=UTF-8') === false) {
                $charsFix += $count;
            }
        }

        return [
            'content' => $content,
            'chars_fixed' => $charsFix
        ];
    }

    private function displaySummary()
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Summary\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        echo "  Files processed: {$this->stats['files_processed']}\n";
        echo "  Files fixed: {$this->stats['files_fixed']}\n";
        echo "  Characters fixed: {$this->stats['chars_fixed']}\n";

        if ($this->stats['errors'] > 0) {
            echo "  Errors: {$this->stats['errors']}\n";
        }

        if ($this->options['dry-run']) {
            echo "\n  ⚠️  DRY RUN - No files were actually modified\n";
            echo "     Run without --dry-run to apply changes\n";
        } elseif ($this->stats['files_fixed'] > 0) {
            echo "\n  ✅ Successfully fixed {$this->stats['files_fixed']} files!\n";
            if ($this->options['backup']) {
                echo "  📦 Backup files created (.bak.* files)\n";
            }
            echo "\n  Next steps:\n";
            echo "    1. Review changes: git diff translations/\n";
            echo "    2. Test in browser: http://your-site/general/translation-demo.php\n";
            echo "    3. Commit: git add translations/ && git commit -m \"fix: Character encoding in translations\"\n";
        } else {
            echo "\n  ℹ️  No encoding issues found - all files are already correct!\n";
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
Character Encoding Fixer for .po Files

Fixes character encoding issues in translation files.

Usage:
  php fix-encoding.php [OPTIONS]

Options:
  --file=FILE         Fix specific file only
  --lang=LANG         Fix specific language only (e.g., fr, es, de)
  --domain=DOMAIN     Fix specific domain: messages, help, custom, or all (default: all)
  --dry-run           Preview changes without modifying files
  --backup            Create backup files (default: true)
  --no-backup         Don't create backup files
  --help              Show this help message

Common Issues Fixed:
  - Corrupted UTF-8 characters (� replacement character)
  - French: é è ê à â ô û ç ï ü
  - Spanish: ñ ó ú í á
  - German: ä ö ü ß
  - Portuguese: ã õ

Examples:
  # Fix all translation files
  php fix-encoding.php

  # Preview changes without modifying
  php fix-encoding.php --dry-run

  # Fix only French translations
  php fix-encoding.php --lang=fr

  # Fix only messages domain
  php fix-encoding.php --domain=messages

  # Fix specific file
  php fix-encoding.php --file=translations/messages/messages.fr.po

  # Fix without creating backups
  php fix-encoding.php --no-backup

How it works:
  1. Reads each .po file
  2. Detects encoding issues (Windows-1252, ISO-8859-1, etc.)
  3. Converts to proper UTF-8
  4. Fixes corrupted accented characters
  5. Updates charset declaration
  6. Creates backup files (optional)

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
        $fixer = new EncodingFixer($options);
        $fixer->fix();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
