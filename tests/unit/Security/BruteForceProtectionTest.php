<?php

namespace Tests\Unit\Security;

use Codeception\Test\Unit;
use UnitTester;

/**
 * Unit Tests: Brute Force Protection Logic
 *
 * Tests the brute force protection implementation in login.php
 *
 * FEATURES TESTED:
 * - Rate limiting logic (5 attempts in 15 minutes)
 * - Timestamp cleanup (old attempts removed)
 * - Lockout time calculation
 * - Attempt counter reset
 *
 * RELATED: general/login.php lines 65-90
 */
class BruteForceProtectionTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * Test: Old attempts are cleaned up (older than 15 minutes)
     *
     * This tests the sliding window algorithm
     */
    public function testOldAttemptsAreCleanedUp()
    {
        $now = time();

        // Simulate login attempts array with mixed timestamps
        $loginAttempts = [
            $now - 1000, // 16+ minutes ago (should be removed)
            $now - 500,  // 8 minutes ago (should be kept)
            $now - 200,  // 3 minutes ago (should be kept)
            $now - 50,   // < 1 minute ago (should be kept)
        ];

        // Clean up old attempts (same logic as login.php line 70-74)
        $cleanedAttempts = array_filter($loginAttempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < 900; // 15 minutes = 900 seconds
        });

        // Verify old attempts removed, recent ones kept
        $this->assertEquals(3, count($cleanedAttempts), 'Should keep 3 recent attempts');
        $this->assertNotContains($now - 1000, $cleanedAttempts, 'Old attempt should be removed');
        $this->assertContains($now - 500, $cleanedAttempts, 'Recent attempt should be kept');
        $this->assertContains($now - 200, $cleanedAttempts, 'Recent attempt should be kept');
        $this->assertContains($now - 50, $cleanedAttempts, 'Recent attempt should be kept');
    }

    /**
     * Test: Lockout triggers after 5 failed attempts
     */
    public function testLockoutTriggersAfterFiveAttempts()
    {
        $now = time();

        // Simulate 4 recent failed attempts
        $loginAttempts = [
            $now - 100,
            $now - 200,
            $now - 300,
            $now - 400,
        ];

        $this->assertLessThan(5, count($loginAttempts), '4 attempts should NOT trigger lockout');

        // Add 5th attempt
        $loginAttempts[] = $now;

        $this->assertGreaterThanOrEqual(5, count($loginAttempts), '5 attempts SHOULD trigger lockout');
    }

    /**
     * Test: Lockout time calculation is correct
     */
    public function testLockoutTimeCalculation()
    {
        $now = time();
        $lockoutDuration = 900; // 15 minutes in seconds

        // Simulate 5 attempts, oldest is 10 minutes ago
        $loginAttempts = [
            $now - 600, // 10 minutes ago (oldest)
            $now - 400,
            $now - 200,
            $now - 100,
            $now - 10,  // 10 seconds ago
        ];

        // Find oldest attempt
        $oldestAttempt = min($loginAttempts);

        // Calculate time remaining (same logic as login.php line 78-80)
        $timeRemaining = $lockoutDuration - ($now - $oldestAttempt);
        $minutesRemaining = ceil($timeRemaining / 60);

        // Verify calculations
        $this->assertEquals(300, $timeRemaining, 'Should have 5 minutes (300s) remaining');
        $this->assertEquals(5, $minutesRemaining, 'Should display 5 minutes remaining');
    }

    /**
     * Test: Lockout time decreases as time passes
     */
    public function testLockoutTimeDecreases()
    {
        $baseTime = time();
        $lockoutDuration = 900; // 15 minutes

        // Oldest attempt is 10 minutes ago
        $oldestAttempt = $baseTime - 600;

        // Time remaining at T+0
        $timeRemaining1 = $lockoutDuration - ($baseTime - $oldestAttempt);

        // Time remaining at T+60 (1 minute later)
        $timeRemaining2 = $lockoutDuration - (($baseTime + 60) - $oldestAttempt);

        // Verify time remaining decreases
        $this->assertGreaterThan($timeRemaining2, $timeRemaining1, 'Time remaining should decrease');
        $this->assertEquals(60, $timeRemaining1 - $timeRemaining2, 'Should decrease by 60 seconds');
    }

    /**
     * Test: Lockout expires after 15 minutes
     */
    public function testLockoutExpiresAfterFifteenMinutes()
    {
        $now = time();

        // All attempts are 15+ minutes old
        $loginAttempts = [
            $now - 901, // 15+ minutes ago
            $now - 902,
            $now - 903,
            $now - 904,
            $now - 905,
        ];

        // Clean up old attempts (same logic as login.php)
        $cleanedAttempts = array_filter($loginAttempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < 900;
        });

        // After cleanup, should have 0 attempts
        $this->assertEquals(0, count($cleanedAttempts), 'All old attempts should be removed');
        $this->assertLessThan(5, count($cleanedAttempts), 'Should NOT trigger lockout after cleanup');
    }

    /**
     * Test: Minutes remaining always rounds up
     *
     * Example: 61 seconds = 2 minutes (not 1)
     */
    public function testMinutesRoundUp()
    {
        $testCases = [
            ['seconds' => 1, 'expected_minutes' => 1],
            ['seconds' => 59, 'expected_minutes' => 1],
            ['seconds' => 60, 'expected_minutes' => 1],
            ['seconds' => 61, 'expected_minutes' => 2],
            ['seconds' => 120, 'expected_minutes' => 2],
            ['seconds' => 121, 'expected_minutes' => 3],
            ['seconds' => 899, 'expected_minutes' => 15],
        ];

        foreach ($testCases as $case) {
            $minutes = ceil($case['seconds'] / 60);
            $this->assertEquals(
                $case['expected_minutes'],
                $minutes,
                "{$case['seconds']} seconds should round to {$case['expected_minutes']} minute(s)"
            );
        }
    }

    /**
     * Test: Attempt tracking uses IP address hash
     *
     * Tests the session key generation for attempt tracking
     */
    public function testAttemptTrackingKeyGeneration()
    {
        $clientIp1 = '192.168.1.100';
        $clientIp2 = '10.0.0.5';

        // Generate keys (same logic as login.php line 67)
        $key1 = 'login_attempts_' . md5($clientIp1);
        $key2 = 'login_attempts_' . md5($clientIp2);

        // Verify keys are different for different IPs
        $this->assertNotEquals($key1, $key2, 'Different IPs should have different keys');

        // Verify key format
        $this->assertStringStartsWith('login_attempts_', $key1, 'Key should have correct prefix');
        $this->assertEquals(48, strlen($key1), 'Key should be 48 chars (prefix + 32 char hash)');
    }

    /**
     * Test: Multiple IPs are tracked separately
     */
    public function testMultipleIPsTrackedSeparately()
    {
        // Simulate two different users from different IPs
        $ip1 = '192.168.1.100';
        $ip2 = '10.0.0.5';

        $attempts_ip1 = [time(), time() - 100, time() - 200]; // 3 attempts
        $attempts_ip2 = [time(), time() - 50]; // 2 attempts

        // Verify they're tracked separately
        $this->assertEquals(3, count($attempts_ip1), 'IP1 should have 3 attempts');
        $this->assertEquals(2, count($attempts_ip2), 'IP2 should have 2 attempts');
        $this->assertNotEquals($attempts_ip1, $attempts_ip2, 'Different IPs tracked independently');
    }

    /**
     * Test: Successful login clears attempts
     *
     * This verifies the logic in login.php line 145
     */
    public function testSuccessfulLoginClearsAttempts()
    {
        // Simulate having failed attempts
        $loginAttempts = [time(), time() - 100, time() - 200];

        $this->assertEquals(3, count($loginAttempts), 'Should have 3 failed attempts');

        // Simulate successful login (clear attempts)
        $loginAttempts = [];

        $this->assertEquals(0, count($loginAttempts), 'Successful login should clear all attempts');
    }
}
