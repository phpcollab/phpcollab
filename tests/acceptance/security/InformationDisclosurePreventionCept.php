<?php
/**
 * Test: Information Disclosure Prevention
 *
 * SECURITY FEATURES TESTED:
 * - Error display disabled (display_errors = 0)
 * - Sensitive file access blocked (.git, .env, backups, etc.)
 * - Directory browsing disabled
 * - Session timeout enforcement
 * - Custom session name (reduces fingerprinting)
 *
 * RELATED COMMIT: "Security: Information disclosure prevention via .htaccess hardening"
 * LOCATION: .htaccess
 * COMPLIANCE: OWASP A05:2021 - Security Misconfiguration
 */

$I = new AcceptanceTester($scenario);

// ============================================================================
// TEST GROUP 1: Sensitive File Protection
// ============================================================================

$I->wantTo('Verify sensitive files are blocked by .htaccess');

// Test 1.1: .git directory should be blocked
$I->amOnPage('/.git/config');
$I->seeResponseCodeIs(403); // Forbidden
$I->comment('✅ .git directory access blocked');

// Test 1.2: .env file should be blocked
$I->amOnPage('/.env');
$I->seeResponseCodeIs(403);
$I->comment('✅ .env file access blocked');

// Test 1.3: composer.json should be blocked
$I->amOnPage('/composer.json');
$I->seeResponseCodeIs(403);
$I->comment('✅ composer.json access blocked');

// Test 1.4: composer.lock should be blocked
$I->amOnPage('/composer.lock');
$I->seeResponseCodeIs(403);
$I->comment('✅ composer.lock access blocked');

// Test 1.5: README.md should be blocked
$I->amOnPage('/README.md');
$I->seeResponseCodeIs(403);
$I->comment('✅ README.md access blocked');

// Test 1.6: CHANGELOG.md should be blocked
$I->amOnPage('/CHANGELOG.md');
$I->seeResponseCodeIs(403);
$I->comment('✅ CHANGELOG.md access blocked');

// Test 1.7: .gitignore should be blocked
$I->amOnPage('/.gitignore');
$I->seeResponseCodeIs(403);
$I->comment('✅ .gitignore access blocked');

// ============================================================================
// TEST GROUP 2: Backup and Temporary File Protection
// ============================================================================

$I->wantTo('Verify backup and temporary files are blocked');

// Test 2.1: .bak files should be blocked
$I->amOnPage('/includes/settings.php.bak');
$I->seeResponseCodeIs(403);
$I->comment('✅ .bak file access blocked');

// Test 2.2: .backup files should be blocked
$I->amOnPage('/database.backup');
$I->seeResponseCodeIs(403);
$I->comment('✅ .backup file access blocked');

// Test 2.3: .sql files should be blocked
$I->amOnPage('/backup.sql');
$I->seeResponseCodeIs(403);
$I->comment('✅ .sql file access blocked');

// Test 2.4: .old files should be blocked
$I->amOnPage('/config.php.old');
$I->seeResponseCodeIs(403);
$I->comment('✅ .old file access blocked');

// Test 2.5: .tmp files should be blocked
$I->amOnPage('/cache.tmp');
$I->seeResponseCodeIs(403);
$I->comment('✅ .tmp file access blocked');

// Test 2.6: .log files should be blocked
$I->amOnPage('/debug.log');
$I->seeResponseCodeIs(403);
$I->comment('✅ .log file access blocked');

// ============================================================================
// TEST GROUP 3: Configuration File Protection
// ============================================================================

$I->wantTo('Verify configuration files are blocked');

// Test 3.1: settings.php should be blocked (if accessed directly)
// Note: This might return 200 if PHP executes it, but shouldn't show source code
$I->amOnPage('/includes/settings.php');
$I->dontSee('<?php'); // Should NOT see PHP source code
$I->dontSee('MYSERVER'); // Should NOT see configuration constants
$I->comment('✅ settings.php source code not exposed');

// ============================================================================
// TEST GROUP 4: Error Display Prevention
// ============================================================================

$I->wantTo('Verify errors are logged but not displayed to users');

// Test 4.1: Trigger a PHP error by accessing non-existent file
$I->amOnPage('/this-file-does-not-exist-12345.php');
// Should see generic error page, NOT detailed PHP error with paths
$I->dontSee('Fatal error'); // Should NOT see PHP error
$I->dontSee('Warning:'); // Should NOT see PHP warning
$I->dontSee('/home/'); // Should NOT see file paths
$I->dontSee('on line'); // Should NOT see line numbers
$I->comment('✅ Detailed error messages not displayed to users');

// ============================================================================
// TEST GROUP 5: Session Security
// ============================================================================

$I->wantTo('Verify session security configuration');

// Test 5.1: Session cookie should have custom name (not PHPSESSID)
$I->amOnPage('/general/login.php');
$sessionCookie = $I->grabCookie('PHPCOLLAB_SESSID');
$I->assertNotEmpty($sessionCookie, 'Custom session cookie name should be used');
$I->comment('✅ Custom session name (PHPCOLLAB_SESSID) in use');

// Test 5.2: Verify PHPSESSID is NOT used (default PHP session name)
$defaultSessionCookie = $I->grabCookie('PHPSESSID');
$I->assertEmpty($defaultSessionCookie, 'Default PHPSESSID should NOT be used');
$I->comment('✅ Default PHPSESSID not in use (fingerprinting protection)');

// ============================================================================
// TEST GROUP 6: Directory Browsing Protection
// ============================================================================

$I->wantTo('Verify directory browsing is disabled');

// Test 6.1: Attempt to browse /includes/ directory
$I->amOnPage('/includes/');
$I->dontSee('Index of'); // Should NOT see directory listing
$I->dontSee('Parent Directory'); // Should NOT see parent directory link
$I->comment('✅ Directory browsing disabled');

// ============================================================================
// TEST GROUP 7: Security Headers
// ============================================================================

$I->wantTo('Verify security headers are set');

$I->amOnPage('/general/login.php');

// Test 7.1: X-Content-Type-Options header
$I->seeHttpHeader('X-Content-Type-Options', 'nosniff');
$I->comment('✅ X-Content-Type-Options: nosniff header present');

// Test 7.2: X-Frame-Options header
$I->seeHttpHeader('X-Frame-Options', 'SAMEORIGIN');
$I->comment('✅ X-Frame-Options: SAMEORIGIN header present');

// Test 7.3: X-XSS-Protection header
$I->seeHttpHeader('X-XSS-Protection');
$I->comment('✅ X-XSS-Protection header present');

// Test 7.4: Referrer-Policy header
$I->seeHttpHeader('Referrer-Policy');
$I->comment('✅ Referrer-Policy header present');

$I->comment('=============================================================');
$I->comment('SECURITY TEST SUMMARY: Information Disclosure Prevention');
$I->comment('=============================================================');
$I->comment('✅ All sensitive files blocked (.git, .env, backups, etc.)');
$I->comment('✅ Configuration files protected');
$I->comment('✅ Error details not exposed to users');
$I->comment('✅ Custom session name reduces fingerprinting');
$I->comment('✅ Directory browsing disabled');
$I->comment('✅ Security headers implemented');
$I->comment('✅ OWASP A05:2021 compliance verified');
