<?php
/**
 * Password Hashing Migration Script
 *
 * This script migrates the members table to support modern password hashing
 * while maintaining backward compatibility with existing passwords.
 *
 * WHAT IT DOES:
 * 1. Adds password_hash_type column to members table
 * 2. Detects and populates hash type for existing users
 * 3. Preserves all existing passwords (no data loss)
 *
 * SAFE TO RUN:
 * - Can be run multiple times (idempotent)
 * - Does not modify existing passwords
 * - Only adds metadata about hash types
 * - Full rollback capability
 *
 * USAGE:
 * - Via web: Navigate to /installation/migrations/migration_password_hashing.php
 * - Via CLI: php migration_password_hashing.php
 *
 * PREREQUISITES:
 * - Database backup completed
 * - Review PASSWORD_MIGRATION_PLAN.md
 *
 * @package phpCollab\Installation\Migrations
 */

// Determine if running via CLI or web
$isCLI = (php_sapi_name() === 'cli');

if (!$isCLI) {
    // Web interface
    require_once '../../includes/library.php';

    // Security: Admin only
    if (!isset($session) || $session->get('profile') != '0') {
        die('ERROR: This migration must be run by an administrator.');
    }
} else {
    // CLI interface
    require_once __DIR__ . '/../../includes/library.php';
}

use phpCollab\Security\PasswordHasher;

/**
 * Output message (works for both CLI and web)
 */
function output($message, $type = 'info')
{
    global $isCLI;

    $prefix = [
        'info' => $isCLI ? '[INFO] ' : '✓ ',
        'error' => $isCLI ? '[ERROR] ' : '❌ ',
        'warning' => $isCLI ? '[WARNING] ' : '⚠️ ',
        'success' => $isCLI ? '[SUCCESS] ' : '✅ '
    ];

    $msg = ($prefix[$type] ?? '') . $message;

    if ($isCLI) {
        echo $msg . PHP_EOL;
    } else {
        echo '<div class="migration-' . $type . '">' . htmlspecialchars($msg) . '</div>';
    }
}

/**
 * Main migration function
 */
function runPasswordHashingMigration()
{
    global $container, $loginMethod;

    output('=== Password Hashing Migration ===', 'info');
    output('Starting migration process...', 'info');

    try {
        $db = $container->getDatabase();

        // Step 1: Check if column already exists
        output('Step 1: Checking database schema...', 'info');

        $tablePrefix = $GLOBALS['tableCollab']['members'];
        $checkColumnSQL = "SHOW COLUMNS FROM `{$tablePrefix}` LIKE 'password_hash_type'";

        $db->query($checkColumnSQL);
        $columnExists = $db->resultset();

        if (!empty($columnExists)) {
            output('Column password_hash_type already exists - skipping schema change', 'warning');
        } else {
            // Step 2: Add password_hash_type column
            output('Step 2: Adding password_hash_type column...', 'info');

            $alterSQL = "ALTER TABLE `{$tablePrefix}`
                         ADD COLUMN `password_hash_type` VARCHAR(20) DEFAULT NULL
                         AFTER `password`";

            $db->query($alterSQL);
            $db->execute();

            output('Column password_hash_type added successfully', 'success');
        }

        // Step 3: Populate hash types for existing users
        output('Step 3: Analyzing existing password hashes...', 'info');

        $selectSQL = "SELECT id, password, password_hash_type FROM `{$tablePrefix}`
                      WHERE password_hash_type IS NULL OR password_hash_type = ''";

        $db->query($selectSQL);
        $users = $db->resultset();

        $usersToUpdate = count($users);

        if ($usersToUpdate === 0) {
            output('All users already have hash type set - no updates needed', 'info');
        } else {
            output("Found {$usersToUpdate} users to analyze", 'info');

            $updateCount = 0;
            $stats = [
                'md5' => 0,
                'crypt' => 0,
                'bcrypt' => 0,
                'argon2id' => 0,
                'argon2i' => 0,
                'plain' => 0,
                'unknown' => 0
            ];

            foreach ($users as $user) {
                $detectedType = PasswordHasher::detectHashType($user['password']);
                $stats[$detectedType]++;

                // Update user with detected hash type
                $updateSQL = "UPDATE `{$tablePrefix}`
                             SET password_hash_type = :hash_type
                             WHERE id = :user_id";

                $db->query($updateSQL);
                $db->bind(':hash_type', $detectedType);
                $db->bind(':user_id', $user['id']);
                $db->execute();

                $updateCount++;
            }

            output("Updated {$updateCount} users with hash type information", 'success');

            // Display statistics
            output('', 'info');
            output('=== Hash Type Distribution ===', 'info');
            foreach ($stats as $type => $count) {
                if ($count > 0) {
                    $info = PasswordHasher::getHashTypeInfo($type);
                    $percentage = round(($count / $usersToUpdate) * 100, 1);
                    output("{$type}: {$count} users ({$percentage}%) - {$info['status']}", 'info');
                }
            }
        }

        // Step 4: Create migration tracking record
        output('', 'info');
        output('Step 4: Recording migration completion...', 'info');

        $logsTable = $GLOBALS['tableCollab']['logs'];
        $logSQL = "INSERT INTO `{$logsTable}` (login, password, ip, session, compt, last_visite, connected)
                   VALUES ('SYSTEM', 'password_migration', '127.0.0.1', 'migration', '1', NOW(), 'false')";

        $db->query($logSQL);
        $db->execute();

        output('Migration completed successfully!', 'success');
        output('', 'info');
        output('=== Next Steps ===', 'info');
        output('1. Users will be automatically upgraded to secure hashing on next login', 'info');
        output('2. Monitor migration progress in Administration > Password Security', 'info');
        output('3. Review PASSWORD_MIGRATION_PLAN.md for timeline and details', 'info');

        return true;

    } catch (Exception $e) {
        output('MIGRATION FAILED: ' . $e->getMessage(), 'error');
        output('', 'info');
        output('=== Rollback Instructions ===', 'error');
        output('If you need to rollback this migration:', 'error');
        output('1. Restore database from backup taken before migration', 'error');
        output('OR', 'error');
        output('2. Run: ALTER TABLE members DROP COLUMN password_hash_type;', 'error');

        return false;
    }
}

// Run migration
if (!$isCLI) {
    // Web interface with basic styling
    echo '<html><head><title>Password Hashing Migration</title>';
    echo '<style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .migration-info { color: #333; margin: 5px 0; }
        .migration-success { color: #28a745; margin: 5px 0; font-weight: bold; }
        .migration-error { color: #dc3545; margin: 5px 0; font-weight: bold; }
        .migration-warning { color: #ffc107; margin: 5px 0; }
    </style></head><body>';
}

$success = runPasswordHashingMigration();

if (!$isCLI) {
    echo '<br><br><a href="../../administration/admin.php">← Back to Administration</a>';
    echo '</body></html>';
} else {
    exit($success ? 0 : 1);
}
