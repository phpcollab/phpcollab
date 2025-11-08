<?php

namespace phpCollab\Security;

use Exception;

/**
 * Secure Password Hashing and Migration Utility
 *
 * Provides secure password hashing with support for migrating from legacy methods:
 * - MD5 (deprecated, no salt)
 * - Crypt with weak salt (deprecated)
 * - Plain text (deprecated, catastrophic)
 *
 * Uses modern password_hash() with Argon2id or bcrypt for new passwords.
 *
 * @package phpCollab\Security
 */
class PasswordHasher
{
    /**
     * Default algorithm for new passwords
     * PASSWORD_ARGON2ID is the most secure option available
     * Falls back to PASSWORD_BCRYPT if Argon2id not available
     */
    const DEFAULT_ALGORITHM = PASSWORD_ARGON2ID;
    const FALLBACK_ALGORITHM = PASSWORD_BCRYPT;

    /**
     * Hash type identifiers for database storage
     */
    const HASH_TYPE_ARGON2ID = 'argon2id';
    const HASH_TYPE_ARGON2I = 'argon2i';
    const HASH_TYPE_BCRYPT = 'bcrypt';
    const HASH_TYPE_MD5 = 'md5';        // Legacy - for migration only
    const HASH_TYPE_CRYPT = 'crypt';    // Legacy - for migration only
    const HASH_TYPE_PLAIN = 'plain';    // Legacy - for migration only

    /**
     * Hash a password using the modern secure algorithm
     *
     * @param string $password Plain text password to hash
     * @return string Hashed password
     * @throws Exception If hashing fails
     */
    public static function hash(string $password): string
    {
        // Try Argon2id first (most secure)
        if (defined('PASSWORD_ARGON2ID')) {
            $hash = password_hash($password, PASSWORD_ARGON2ID);
        } else {
            // Fall back to bcrypt if Argon2id not available (PHP < 7.3)
            $hash = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($hash === false) {
            throw new Exception('Password hashing failed');
        }

        return $hash;
    }

    /**
     * Verify password against stored hash
     *
     * Supports both modern and legacy hash formats for migration compatibility
     *
     * @param string $password Plain text password to verify
     * @param string $storedHash Hash from database
     * @param string|null $hashType Optional hash type hint (auto-detected if null)
     * @return bool True if password matches, false otherwise
     */
    public static function verify(string $password, string $storedHash, ?string $hashType = null): bool
    {
        // Auto-detect hash type if not provided
        if ($hashType === null) {
            $hashType = self::detectHashType($storedHash);
        }

        switch ($hashType) {
            case self::HASH_TYPE_ARGON2ID:
            case self::HASH_TYPE_ARGON2I:
            case self::HASH_TYPE_BCRYPT:
                // Modern hashes - use password_verify()
                return password_verify($password, $storedHash);

            case self::HASH_TYPE_MD5:
                // DEPRECATED - Legacy MD5 support for migration
                // Uses timing-safe comparison
                return hash_equals(md5($password), $storedHash);

            case self::HASH_TYPE_CRYPT:
                // DEPRECATED - Legacy crypt() support for migration
                // Extract salt from stored hash (first 2 characters for DES-crypt)
                $salt = substr($storedHash, 0, 2);
                return hash_equals(crypt($password, $salt), $storedHash);

            case self::HASH_TYPE_PLAIN:
                // DEPRECATED - Plain text support for migration (catastrophic if used!)
                // Uses timing-safe comparison
                return hash_equals($password, $storedHash);

            default:
                // Unknown hash type - fail closed
                return false;
        }
    }

    /**
     * Detect hash type from hash string format
     *
     * @param string $hash Hash to analyze
     * @return string Hash type constant
     */
    public static function detectHashType(string $hash): string
    {
        // Argon2id: $argon2id$v=19$m=65536,t=4,p=1$...
        if (strpos($hash, '$argon2id$') === 0) {
            return self::HASH_TYPE_ARGON2ID;
        }

        // Argon2i: $argon2i$v=19$m=65536,t=4,p=1$...
        if (strpos($hash, '$argon2i$') === 0) {
            return self::HASH_TYPE_ARGON2I;
        }

        // Bcrypt: $2y$10$... or $2a$10$... or $2b$10$... (60 chars total)
        if (preg_match('/^\$2[ayb]\$.{56}$/', $hash)) {
            return self::HASH_TYPE_BCRYPT;
        }

        // MD5: 32 hexadecimal characters
        if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            return self::HASH_TYPE_MD5;
        }

        // DES-crypt: 13 characters (2 salt + 11 hash)
        // Extended DES: _... (starts with underscore)
        // Blowfish crypt would have been caught by bcrypt pattern above
        if (strlen($hash) == 13 || $hash[0] === '_') {
            return self::HASH_TYPE_CRYPT;
        }

        // If nothing else matches, assume plain text
        // This is dangerous but necessary for migration
        return self::HASH_TYPE_PLAIN;
    }

    /**
     * Check if a password hash needs to be upgraded to modern algorithm
     *
     * @param string $hash Hash to check
     * @param string|null $hashType Optional hash type hint
     * @return bool True if hash should be upgraded
     */
    public static function needsRehash(string $hash, ?string $hashType = null): bool
    {
        if ($hashType === null) {
            $hashType = self::detectHashType($hash);
        }

        // All legacy hashes need upgrade
        if (in_array($hashType, [
            self::HASH_TYPE_MD5,
            self::HASH_TYPE_CRYPT,
            self::HASH_TYPE_PLAIN
        ])) {
            return true;
        }

        // Modern hashes may need rehash if algorithm parameters changed
        if (in_array($hashType, [
            self::HASH_TYPE_BCRYPT,
            self::HASH_TYPE_ARGON2ID,
            self::HASH_TYPE_ARGON2I
        ])) {
            // Use PHP's built-in check for modern hashes
            if (defined('PASSWORD_ARGON2ID')) {
                return password_needs_rehash($hash, PASSWORD_ARGON2ID);
            } else {
                return password_needs_rehash($hash, PASSWORD_BCRYPT);
            }
        }

        return false;
    }

    /**
     * Get the hash type that will be used for new passwords
     *
     * @return string Hash type (argon2id or bcrypt)
     */
    public static function getDefaultHashType(): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return self::HASH_TYPE_ARGON2ID;
        }
        return self::HASH_TYPE_BCRYPT;
    }

    /**
     * Check if a hash type is considered secure (not legacy)
     *
     * @param string $hashType Hash type to check
     * @return bool True if secure, false if legacy
     */
    public static function isSecureHashType(string $hashType): bool
    {
        return in_array($hashType, [
            self::HASH_TYPE_ARGON2ID,
            self::HASH_TYPE_ARGON2I,
            self::HASH_TYPE_BCRYPT
        ]);
    }

    /**
     * Get human-readable security status for a hash type
     *
     * @param string $hashType Hash type to describe
     * @return array ['status' => string, 'description' => string, 'secure' => bool]
     */
    public static function getHashTypeInfo(string $hashType): array
    {
        switch ($hashType) {
            case self::HASH_TYPE_ARGON2ID:
                return [
                    'status' => 'Excellent',
                    'description' => 'Argon2id - Most secure algorithm available',
                    'secure' => true
                ];

            case self::HASH_TYPE_ARGON2I:
                return [
                    'status' => 'Very Good',
                    'description' => 'Argon2i - Very secure algorithm',
                    'secure' => true
                ];

            case self::HASH_TYPE_BCRYPT:
                return [
                    'status' => 'Good',
                    'description' => 'Bcrypt - Industry standard secure algorithm',
                    'secure' => true
                ];

            case self::HASH_TYPE_CRYPT:
                return [
                    'status' => 'Weak',
                    'description' => 'Legacy crypt() - Vulnerable to GPU cracking',
                    'secure' => false
                ];

            case self::HASH_TYPE_MD5:
                return [
                    'status' => 'Broken',
                    'description' => 'MD5 - Cryptographically broken, crackable in seconds',
                    'secure' => false
                ];

            case self::HASH_TYPE_PLAIN:
                return [
                    'status' => 'Catastrophic',
                    'description' => 'Plain text - No security whatsoever',
                    'secure' => false
                ];

            default:
                return [
                    'status' => 'Unknown',
                    'description' => 'Unknown hash type',
                    'secure' => false
                ];
        }
    }

    /**
     * Validate password meets minimum complexity requirements
     *
     * @param string $password Password to validate
     * @param int $minLength Minimum length (default: 8)
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePasswordComplexity(string $password, int $minLength = 8): array
    {
        $errors = [];

        // Check minimum length
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long";
        }

        // Check for at least one letter
        if (!preg_match('/[a-zA-Z]/', $password)) {
            $errors[] = "Password must contain at least one letter";
        }

        // Check for at least one number
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        // Check for common weak passwords
        $commonPasswords = [
            'password', 'password123', '12345678', 'qwerty', 'abc123',
            'admin', 'admin123', 'letmein', 'welcome', 'monkey'
        ];

        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = "Password is too common - please choose a stronger password";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate migration statistics for admin dashboard
     *
     * @param array $users Array of users with password_hash_type field
     * @return array Migration statistics
     */
    public static function getMigrationStats(array $users): array
    {
        $stats = [
            'total' => count($users),
            'by_type' => [],
            'secure_count' => 0,
            'legacy_count' => 0,
            'migration_percentage' => 0
        ];

        // Count by hash type
        foreach ($users as $user) {
            $hashType = $user['password_hash_type'] ?? 'unknown';

            if (!isset($stats['by_type'][$hashType])) {
                $stats['by_type'][$hashType] = 0;
            }
            $stats['by_type'][$hashType]++;

            if (self::isSecureHashType($hashType)) {
                $stats['secure_count']++;
            } else {
                $stats['legacy_count']++;
            }
        }

        // Calculate migration percentage
        if ($stats['total'] > 0) {
            $stats['migration_percentage'] = round(
                ($stats['secure_count'] / $stats['total']) * 100,
                2
            );
        }

        return $stats;
    }
}
