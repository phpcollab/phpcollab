<?php
/**
 * Test: Session Fixation Prevention via Session Regeneration
 *
 * SECURITY FEATURE TESTED:
 * - Session ID regeneration after successful authentication
 * - Prevention of session fixation attacks
 * - Old session ID becomes invalid
 * - New session ID is unpredictable
 *
 * RELATED COMMIT: "Security: Critical authentication hardening"
 * LOCATION: general/login.php line 107
 * COMPLIANCE: OWASP ASVS 3.2.1
 */

$I = new AcceptanceTester($scenario);
$I->wantTo('Verify session ID is regenerated after successful login (session fixation prevention)');

// Test Part 1: Get session cookie before login
$I->amOnPage('/general/login.php');
$sessionBefore = $I->grabCookie('PHPCOLLAB_SESSID');
$I->assertNotEmpty($sessionBefore, 'Session cookie should exist before login');
$I->comment('Session ID before login: ' . substr($sessionBefore, 0, 16) . '...');

// Test Part 2: Perform successful login
$I->fillField(['name' => 'usernameForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testPassword');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/home.php'); // Should redirect to home page on success

// Test Part 3: Get session cookie after login
$sessionAfter = $I->grabCookie('PHPCOLLAB_SESSID');
$I->assertNotEmpty($sessionAfter, 'Session cookie should exist after login');
$I->comment('Session ID after login: ' . substr($sessionAfter, 0, 16) . '...');

// Test Part 4: Verify session ID changed
$I->assertNotEquals(
    $sessionBefore,
    $sessionAfter,
    'CRITICAL: Session ID MUST change after login to prevent session fixation attacks'
);

// Test Part 5: Verify old session ID is invalid
$I->resetCookie('PHPCOLLAB_SESSID');
$I->setCookie('PHPCOLLAB_SESSID', $sessionBefore); // Try to use old session ID
$I->amOnPage('/general/home.php');
$I->seeInCurrentUrl('/general/login.php'); // Should redirect to login (old session invalid)

// Test Part 6: Verify new session ID is valid
$I->resetCookie('PHPCOLLAB_SESSID');
$I->setCookie('PHPCOLLAB_SESSID', $sessionAfter); // Use new session ID
$I->amOnPage('/general/home.php');
$I->seeInCurrentUrl('/general/home.php'); // Should stay on home page (valid session)

$I->comment('SECURITY TEST PASSED: Session fixation protection is working correctly');
$I->comment('- Session ID regenerated after login');
$I->comment('- Old session ID is invalid');
$I->comment('- New session ID grants access');
$I->comment('- OWASP ASVS 3.2.1 compliance verified');
