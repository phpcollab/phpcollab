#!/usr/bin/env php
<?php
/**
 * Check Missing Translations
 *
 * Analyzes all .po files and reports:
 * - Which translations are missing in each language
 * - Completion percentage per language
 * - Overall translation status
 *
 * Usage:
 *   php check-missing.php [--domain=messages] [--verbose] [--lang=fr]
 *
 * Options:
 *   --domain=DOMAIN   Check specific domain (messages, help, custom) or 'all' (default: all)
 *   --lang=LANG       Check specific language only
 *   --verbose         Show detailed missing keys
 *   --format=FORMAT   Output format: text, csv, json (default: text)
 */

class TranslationChecker
{
    private $translationsPath;
    private $masterLanguage = 'en';
    private $domains = ['messages', 'help', 'custom'];
    private $languages = [
        'ar', 'az', 'pt-br', 'bg', 'ca', 'zh', 'zh-tw', 'cs-iso',
        'cs-win1250', 'da', 'nl', 'en', 'et', 'fr', 'de', 'hu',
        'is', 'in', 'it', 'ja', 'ko', 'lv', 'no', 'pl', 'pt',
        'ro', 'ru', 'sk-win1250', 'es', 'tr', 'uk'
    ];
    private $languageNames = [
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

    private $options = [];

    public function __construct(array $options = [])
    {
        $this->translationsPath = dirname(__DIR__, 2) . '/translations';
        $this->options = array_merge([
            'domain' => 'all',
            'lang' => null,
            'verbose' => false,
            'format' => 'text'
        ], $options);
    }

    public function check()
    {
        $domains = $this->options['domain'] === 'all'
            ? $this->domains
            : [$this->options['domain']];

        $results = [];

        foreach ($domains as $domain) {
            $domainResults = $this->checkDomain($domain);
            $results[$domain] = $domainResults;
        }

        $this->displayResults($results);
    }

    private function checkDomain(string $domain): array
    {
        $masterFile = "{$this->translationsPath}/{$domain}/{$domain}.{$this->masterLanguage}.po";

        if (!file_exists($masterFile)) {
            return ['error' => "Master file not found: {$masterFile}"];
        }

        $masterEntries = $this->parsePoFile($masterFile);
        $totalKeys = count($masterEntries);

        $results = [
            'total_keys' => $totalKeys,
            'languages' => []
        ];

        $languagesToCheck = $this->options['lang']
            ? [$this->options['lang']]
            : $this->languages;

        foreach ($languagesToCheck as $lang) {
            if ($lang === $this->masterLanguage) {
                continue; // Skip master language
            }

            $langFile = "{$this->translationsPath}/{$domain}/{$domain}.{$lang}.po";

            if (!file_exists($langFile)) {
                $results['languages'][$lang] = [
                    'exists' => false,
                    'total' => 0,
                    'translated' => 0,
                    'missing' => $totalKeys,
                    'percentage' => 0,
                    'missing_keys' => array_keys($masterEntries)
                ];
                continue;
            }

            $langEntries = $this->parsePoFile($langFile);
            $missing = [];
            $translated = 0;

            foreach ($masterEntries as $key => $masterValue) {
                if (isset($langEntries[$key]) && !empty($langEntries[$key])) {
                    $translated++;
                } else {
                    $missing[] = $key;
                }
            }

            $percentage = $totalKeys > 0 ? ($translated / $totalKeys) * 100 : 0;

            $results['languages'][$lang] = [
                'exists' => true,
                'total' => $totalKeys,
                'translated' => $translated,
                'missing' => count($missing),
                'percentage' => $percentage,
                'missing_keys' => $missing
            ];
        }

        return $results;
    }

    private function parsePoFile(string $file): array
    {
        $entries = [];
        $content = file_get_contents($file);

        // Simple .po parser - extracts msgid and msgstr pairs
        preg_match_all('/msgid\s+"([^"]+)"\s+msgstr\s+"([^"]*)"/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $msgid = $match[1];
            $msgstr = $match[2];

            // Skip header entry
            if (empty($msgid)) {
                continue;
            }

            $entries[$msgid] = $msgstr;
        }

        return $entries;
    }

    private function displayResults(array $results)
    {
        switch ($this->options['format']) {
            case 'json':
                echo json_encode($results, JSON_PRETTY_PRINT) . "\n";
                break;
            case 'csv':
                $this->displayCsv($results);
                break;
            default:
                $this->displayText($results);
                break;
        }
    }

    private function displayText(array $results)
    {
        echo "\n";
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║         Translation Completion Report                     ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        foreach ($results as $domain => $data) {
            if (isset($data['error'])) {
                echo "❌ {$domain}: {$data['error']}\n\n";
                continue;
            }

            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "  Domain: " . strtoupper($domain) . "\n";
            echo "  Master Language: English ({$this->masterLanguage})\n";
            echo "  Total Keys: {$data['total_keys']}\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "\n";

            // Sort by percentage (highest first)
            $languages = $data['languages'];
            uasort($languages, function($a, $b) {
                return $b['percentage'] <=> $a['percentage'];
            });

            foreach ($languages as $lang => $info) {
                $langName = $this->languageNames[$lang] ?? ucfirst($lang);
                $status = $this->getStatus($info['percentage']);
                $bar = $this->getProgressBar($info['percentage']);

                printf(
                    "  %s %-25s %s %5.1f%% (%d/%d) - %d missing\n",
                    $status,
                    "{$langName} ({$lang}):",
                    $bar,
                    $info['percentage'],
                    $info['translated'],
                    $info['total'],
                    $info['missing']
                );

                // Show missing keys in verbose mode
                if ($this->options['verbose'] && !empty($info['missing_keys'])) {
                    echo "    Missing keys:\n";
                    $count = 0;
                    foreach ($info['missing_keys'] as $key) {
                        echo "      - {$key}\n";
                        $count++;
                        if ($count >= 10 && count($info['missing_keys']) > 10) {
                            $remaining = count($info['missing_keys']) - 10;
                            echo "      ... and {$remaining} more\n";
                            break;
                        }
                    }
                    echo "\n";
                }
            }

            echo "\n";

            // Summary statistics
            $totalLanguages = count($languages);
            $complete = count(array_filter($languages, fn($l) => $l['percentage'] == 100));
            $partial = count(array_filter($languages, fn($l) => $l['percentage'] > 0 && $l['percentage'] < 100));
            $empty = count(array_filter($languages, fn($l) => $l['percentage'] == 0));
            $avgCompletion = array_sum(array_column($languages, 'percentage')) / max($totalLanguages, 1);

            echo "  Summary:\n";
            echo "  ─────────────────────────────────────────────────────────\n";
            echo "  Total Languages:      {$totalLanguages}\n";
            echo "  ✓ Complete (100%):    {$complete}\n";
            echo "  ⚠ Partial:             {$partial}\n";
            echo "  ✗ Empty (0%):          {$empty}\n";
            echo "  Average Completion:   " . number_format($avgCompletion, 1) . "%\n";
            echo "\n";
        }

        // Overall summary across all domains
        if (count($results) > 1) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "  Overall Summary Across All Domains\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "\n";

            $overallStats = $this->calculateOverallStats($results);
            foreach ($overallStats as $lang => $stats) {
                $langName = $this->languageNames[$lang] ?? ucfirst($lang);
                $status = $this->getStatus($stats['percentage']);
                $bar = $this->getProgressBar($stats['percentage']);

                printf(
                    "  %s %-25s %s %5.1f%%\n",
                    $status,
                    "{$langName} ({$lang}):",
                    $bar,
                    $stats['percentage']
                );
            }
            echo "\n";
        }

        echo "Legend:\n";
        echo "  ✓ = 100% complete\n";
        echo "  ● = 75-99% complete\n";
        echo "  ◐ = 50-74% complete\n";
        echo "  ◔ = 25-49% complete\n";
        echo "  ○ = 1-24% complete\n";
        echo "  ✗ = 0% complete or file missing\n";
        echo "\n";
    }

    private function displayCsv(array $results)
    {
        echo "Domain,Language,Language Name,Total Keys,Translated,Missing,Percentage,Status\n";

        foreach ($results as $domain => $data) {
            if (isset($data['error'])) {
                continue;
            }

            foreach ($data['languages'] as $lang => $info) {
                $langName = $this->languageNames[$lang] ?? ucfirst($lang);
                $status = $info['exists'] ? 'Exists' : 'Missing';

                echo sprintf(
                    "%s,%s,\"%s\",%d,%d,%d,%.1f,%s\n",
                    $domain,
                    $lang,
                    $langName,
                    $info['total'],
                    $info['translated'],
                    $info['missing'],
                    $info['percentage'],
                    $status
                );
            }
        }
    }

    private function calculateOverallStats(array $results): array
    {
        $overallStats = [];

        foreach ($this->languages as $lang) {
            if ($lang === $this->masterLanguage) {
                continue;
            }

            $totalKeys = 0;
            $totalTranslated = 0;

            foreach ($results as $domain => $data) {
                if (isset($data['error']) || !isset($data['languages'][$lang])) {
                    continue;
                }

                $langInfo = $data['languages'][$lang];
                $totalKeys += $langInfo['total'];
                $totalTranslated += $langInfo['translated'];
            }

            $percentage = $totalKeys > 0 ? ($totalTranslated / $totalKeys) * 100 : 0;

            $overallStats[$lang] = [
                'total' => $totalKeys,
                'translated' => $totalTranslated,
                'percentage' => $percentage
            ];
        }

        // Sort by percentage
        uasort($overallStats, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        return $overallStats;
    }

    private function getStatus(float $percentage): string
    {
        if ($percentage == 100) return '✓';
        if ($percentage >= 75) return '●';
        if ($percentage >= 50) return '◐';
        if ($percentage >= 25) return '◔';
        if ($percentage > 0) return '○';
        return '✗';
    }

    private function getProgressBar(float $percentage, int $width = 20): string
    {
        $filled = (int)($width * ($percentage / 100));
        $empty = $width - $filled;

        return '[' . str_repeat('█', $filled) . str_repeat('░', $empty) . ']';
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
Translation Completion Checker

Usage:
  php check-missing.php [OPTIONS]

Options:
  --domain=DOMAIN   Check specific domain: messages, help, custom, or all (default: all)
  --lang=LANG       Check specific language only (e.g., fr, es, de)
  --verbose         Show detailed list of missing keys
  --format=FORMAT   Output format: text, csv, json (default: text)
  --help            Show this help message

Examples:
  # Check all domains for all languages
  php check-missing.php

  # Check only messages domain
  php check-missing.php --domain=messages

  # Check French translations with details
  php check-missing.php --lang=fr --verbose

  # Export to CSV
  php check-missing.php --format=csv > report.csv

  # Check help domain for Spanish
  php check-missing.php --domain=help --lang=es

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
        $checker = new TranslationChecker($options);
        $checker->check();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
