#!/usr/bin/env php
<?php
/**
 * Extract New Translation Strings from Code
 *
 * Scans PHP files for translation function calls and extracts keys:
 * - trans('key')
 * - getEnum('type', value)
 * - help('key')
 * - custom('key')
 *
 * Adds new keys to English master .po files
 *
 * Usage:
 *   php extract-new-strings.php [OPTIONS]
 *
 * Options:
 *   --scan=PATH         Directory to scan (default: entire project)
 *   --domain=DOMAIN     Extract for specific domain only
 *   --dry-run           Show what would be added without modifying files
 *   --update            Update .po files with new keys
 */

class StringExtractor
{
    private $translationsPath;
    private $projectRoot;
    private $masterLanguage = 'en';
    private $options = [];
    private $found = [];

    public function __construct(array $options = [])
    {
        $this->projectRoot = dirname(__DIR__, 2);
        $this->translationsPath = $this->projectRoot . '/translations';
        $this->options = array_merge([
            'scan' => $this->projectRoot,
            'domain' => null,
            'dry-run' => false,
            'update' => false
        ], $options);
    }

    public function extract()
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║         Translation String Extractor                      ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        if ($this->options['dry-run']) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        }

        $scanPath = $this->options['scan'];
        echo "Scanning: {$scanPath}\n\n";

        // Scan for translation function calls
        $this->scanDirectory($scanPath);

        // Display results
        $this->displayResults();

        // Update .po files if requested
        if ($this->options['update'] && !$this->options['dry-run']) {
            $this->updatePoFiles();
        }
    }

    private function scanDirectory(string $path)
    {
        if (!is_dir($path)) {
            if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $this->scanFile($path);
            }
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                // Skip vendor and cache directories
                $filePath = $file->getPathname();
                if (strpos($filePath, '/vendor/') !== false ||
                    strpos($filePath, '/cache/') !== false ||
                    strpos($filePath, '/var/') !== false) {
                    continue;
                }

                $this->scanFile($filePath);
            }
        }
    }

    private function scanFile(string $file)
    {
        $content = file_get_contents($file);
        $relativePath = str_replace($this->projectRoot . '/', '', $file);

        // Pattern 1: trans('key')
        if (preg_match_all('/trans\s*\(\s*[\'"]([^\'"]+)[\'"]\s*[,\)]/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->addFound('messages', $key, $relativePath);
            }
        }

        // Pattern 2: trans("key")
        if (preg_match_all('/trans\s*\(\s*"([^"]+)"\s*[,\)]/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->addFound('messages', $key, $relativePath);
            }
        }

        // Pattern 3: getEnum('type', ...)
        if (preg_match_all('/getEnum\s*\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches)) {
            foreach ($matches[1] as $enumType) {
                // Note: We can't extract the actual enum values without runtime analysis
                // So we just note that this enum type is used
                $this->addFound('enums', $enumType . '.*', $relativePath);
            }
        }

        // Pattern 4: help('key')
        if (preg_match_all('/help\s*\(\s*[\'"]([^\'"]+)[\'"]\s*[,\)]/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->addFound('help', $key, $relativePath);
            }
        }

        // Pattern 5: custom('key')
        if (preg_match_all('/custom\s*\(\s*[\'"]([^\'"]+)[\'"]\s*[,\)]/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                $this->addFound('custom', $key, $relativePath);
            }
        }

        // Pattern 6: $strings["key"] (old style - for reference)
        if (preg_match_all('/\$strings\s*\[\s*[\'"]([^\'"]+)[\'"]\s*\]/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                // Prepend "strings." for old-style usage
                $this->addFound('messages', 'strings.' . $key, $relativePath, true);
            }
        }
    }

    private function addFound(string $domain, string $key, string $file, bool $legacy = false)
    {
        if (!isset($this->found[$domain])) {
            $this->found[$domain] = [];
        }

        if (!isset($this->found[$domain][$key])) {
            $this->found[$domain][$key] = [
                'files' => [],
                'legacy' => $legacy
            ];
        }

        if (!in_array($file, $this->found[$domain][$key]['files'])) {
            $this->found[$domain][$key]['files'][] = $file;
        }
    }

    private function displayResults()
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Extraction Results\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        if (empty($this->found)) {
            echo "  No translation strings found.\n\n";
            return;
        }

        foreach ($this->found as $domain => $keys) {
            echo "  Domain: {$domain}\n";
            echo "  " . str_repeat('─', 58) . "\n";

            // Load existing keys from master file
            $masterFile = "{$this->translationsPath}/{$domain}/{$domain}.{$this->masterLanguage}.po";
            $existingKeys = [];

            if (file_exists($masterFile)) {
                $existingKeys = $this->getKeysFromPoFile($masterFile);
            }

            $newKeys = [];
            $existingCount = 0;

            foreach ($keys as $key => $data) {
                if (in_array($key, $existingKeys)) {
                    $existingCount++;
                } else {
                    $newKeys[$key] = $data;
                }
            }

            echo "  Total found: " . count($keys) . "\n";
            echo "  Already in .po: {$existingCount}\n";
            echo "  New keys: " . count($newKeys) . "\n";

            if (!empty($newKeys)) {
                echo "\n  New keys to add:\n";
                $count = 0;
                foreach ($newKeys as $key => $data) {
                    $legacy = $data['legacy'] ? ' (legacy)' : '';
                    $fileCount = count($data['files']);
                    $fileInfo = $fileCount === 1 ? $data['files'][0] : "{$fileCount} files";

                    echo "    • {$key}{$legacy}\n";
                    echo "      → {$fileInfo}\n";

                    $count++;
                    if ($count >= 20 && count($newKeys) > 20) {
                        $remaining = count($newKeys) - 20;
                        echo "    ... and {$remaining} more\n";
                        break;
                    }
                }
            }

            echo "\n";
        }
    }

    private function getKeysFromPoFile(string $file): array
    {
        $content = file_get_contents($file);
        $keys = [];

        if (preg_match_all('/msgid\s+"([^"]+)"/', $content, $matches)) {
            foreach ($matches[1] as $key) {
                if (!empty($key)) {
                    $keys[] = $key;
                }
            }
        }

        return $keys;
    }

    private function updatePoFiles()
    {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "  Updating .po Files\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        foreach ($this->found as $domain => $keys) {
            $masterFile = "{$this->translationsPath}/{$domain}/{$domain}.{$this->masterLanguage}.po";

            if (!file_exists($masterFile)) {
                echo "  ⚠️  {$domain}: Master file doesn't exist, skipping\n";
                continue;
            }

            // Load existing keys
            $existingKeys = $this->getKeysFromPoFile($masterFile);

            // Filter to only new keys
            $newKeys = array_filter($keys, function($data, $key) use ($existingKeys) {
                return !in_array($key, $existingKeys);
            }, ARRAY_FILTER_USE_BOTH);

            if (empty($newKeys)) {
                echo "  ✓ {$domain}: No new keys to add\n";
                continue;
            }

            // Add new keys to file
            $content = file_get_contents($masterFile);

            // Add comment header for new keys
            $newSection = "\n# ============================================\n";
            $newSection .= "# New keys extracted on " . date('Y-m-d H:i:s') . "\n";
            $newSection .= "# ============================================\n\n";

            foreach ($newKeys as $key => $data) {
                $files = implode(', ', array_slice($data['files'], 0, 3));
                if (count($data['files']) > 3) {
                    $files .= ' +' . (count($data['files']) - 3) . ' more';
                }

                $newSection .= "#: {$files}\n";
                if ($data['legacy']) {
                    $newSection .= "# Legacy usage: \$strings[\"...\"]\n";
                }
                $newSection .= "msgid \"{$key}\"\n";
                $newSection .= "msgstr \"\"\n";
                $newSection .= "\n";
            }

            // Append to file
            file_put_contents($masterFile, $content . $newSection);

            echo "  ✅ {$domain}: Added " . count($newKeys) . " new keys\n";
        }

        echo "\n";
        echo "Next steps:\n";
        echo "  1. Review changes: git diff translations/\n";
        echo "  2. Add English translations to new keys\n";
        echo "  3. Sync to other languages: php sync-translations.php\n";
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
Translation String Extractor

Scans PHP files for translation function calls and extracts keys.

Usage:
  php extract-new-strings.php [OPTIONS]

Options:
  --scan=PATH      Directory or file to scan (default: entire project)
  --domain=DOMAIN  Extract for specific domain only
  --dry-run        Show what would be added without modifying files
  --update         Update .po files with new keys
  --help           Show this help message

Detected patterns:
  - trans('key')              → messages domain
  - getEnum('type', value)    → enums domain
  - help('key')               → help domain
  - custom('key')             → custom domain
  - \$strings["key"]          → messages domain (legacy, marked)

Examples:
  # Scan entire project and show results
  php extract-new-strings.php

  # Scan specific directory
  php extract-new-strings.php --scan=general/

  # Extract and update .po files
  php extract-new-strings.php --update

  # Preview without changes
  php extract-new-strings.php --dry-run

Workflow:
  1. Run extract to find new strings
  2. Review the output
  3. Run with --update to add to English .po files
  4. Edit English .po files to add translations
  5. Run sync-translations.php to propagate to other languages

USAGE;
    exit(0);
}

// Main execution
if (php_sapi_name() === 'cli') {
    $options = parseArgs($argv);

    if (isset($options['help'])) {
        showUsage();
    }

    try {
        $extractor = new StringExtractor($options);
        $extractor->extract();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
