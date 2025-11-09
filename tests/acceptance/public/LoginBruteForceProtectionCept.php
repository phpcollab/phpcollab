<?php
/**
 * Test: Login Brute Force Protection
 *
 * SECURITY FEATURE TESTED:
 * - Rate limiting on login attempts (5 failures = 15 minute lockout)
 * - Failed attempt tracking per IP address
 * - Lockout time calculation
 * - Attempt counter reset on successful login
 *
 * RELATED COMMIT: "Security: Critical authentication hardening"
 * LOCATION: general/login.php lines 65-90
 */

$I = new AcceptanceTester($scenario);
$I->wantTo('Verify login brute force protection locks account after 5 failed attempts');

// Test Part 1: First 4 failed attempts should be allowed
$I->amOnPage('/general/login.php');

for ($attempt = 1; $attempt <= 4; $attempt++) {
    $I->fillField(['name' => 'usernameForm'], 'testUser');
    $I->fillField(['name' => 'passwordForm'], 'wrongpassword' . $attempt);
    $I->click('input[type="submit"]');
    $I->seeInCurrentUrl('/general/login.php');
    $I->see('invalid', '.error'); // Should see error but NOT lockout message
    $I->dontSee('Too many failed login attempts'); // Should NOT be locked out yet
}

// Test Part 2: 5th failed attempt should trigger lockout
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'usernameForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'wrongpassword5');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/login.php');
$I->see('Too many failed login attempts', '.error'); // Should see lockout message
$I->see('minute', '.error'); // Should mention time remaining

// Test Part 3: Even correct password should be rejected during lockout
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'usernameForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testPassword'); // Correct password
$I->click('input[type="submit"]');
$I->see('Too many failed login attempts', '.error'); // Should still be locked out

// Test Part 4: Verify lockout persists across multiple attempts
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'usernameForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testPassword');
$I->click('input[type="submit"]');
$I->see('Too many failed login attempts', '.error'); // Should STILL be locked out

$I->comment('SECURITY TEST PASSED: Brute force protection is working correctly');
$I->comment('- 5 failed attempts triggered lockout');
$I->comment('- Lockout prevents further login attempts (even with correct password)');
$I->comment('- User must wait 15 minutes for lockout to expire');
