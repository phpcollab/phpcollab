#!/usr/bin/env php
<?php
/**
 * Simple German Encoding Fix
 *
 * Direct string replacement approach for German .po files
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line\n");
}

$options = getopt('', ['dry-run', 'help']);

if (isset($options['help'])) {
    echo "Simple German Encoding Fix\n";
    echo "===========================\n\n";
    echo "Usage: php simple-german-fix.php [--dry-run]\n\n";
    exit(0);
}

$dryRun = isset($options['dry-run']);
$filePath = __DIR__ . '/../../translations/messages/messages.de.po';

if (!file_exists($filePath)) {
    die("Error: File not found: {$filePath}\n");
}

echo "Simple German Encoding Fix\n";
echo "==========================\n\n";
echo "File: translations/messages/messages.de.po\n";
echo "Mode: " . ($dryRun ? "DRY RUN" : "LIVE") . "\n\n";

// Read entire file
$content = file_get_contents($filePath);
$originalContent = $content;

// Count original � characters
$originalCount = substr_count($content, '�');

// Apply replacements for German umlauts
$replacements = [
    'm�glich' => 'möglich',
    'M�glich' => 'Möglich',
    'best�tigen' => 'bestätigen',
    'Best�tigen' => 'Bestätigen',
    'l�sche' => 'lösche',
    'L�sche' => 'Lösche',
    'l�schen' => 'löschen',
    'L�schen' => 'Löschen',
];

foreach ($replacements as $search => $replace) {
    $content = str_replace($search, $replace, $content);
}

// Count remaining � characters
$remainingCount = substr_count($content, '�');
$fixedCount = $originalCount - $remainingCount;

echo "Original � characters: {$originalCount}\n";
echo "Fixed: {$fixedCount}\n";
echo "Remaining: {$remainingCount}\n\n";

if ($content !== $originalContent) {
    if (!$dryRun) {
        // Create backup
        $backup = $filePath . '.bak.' . date('Y-m-d_His');
        copy($filePath, $backup);

        // Write fixed content
        file_put_contents($filePath, $content);

        echo "✓ Changes applied\n";
        echo "✓ Backup created: " . basename($backup) . "\n";
    } else {
        echo "✓ DRY RUN - No changes made\n";
        echo "  Run without --dry-run to apply fixes\n";
    }
} else {
    echo "✓ No changes needed\n";
}

if ($remainingCount > 0) {
    echo "\nNote: {$remainingCount} � characters remain. These may need manual review.\n";
}
