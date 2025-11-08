# Password Hashing Migration Plan

**Critical Security Fix - CVSS 9.1**
**Status:** PLANNING PHASE - DO NOT IMPLEMENT YET
**Date:** November 8, 2025

---

## ⚠️ CRITICAL WARNING

Changing password hashing in a live system is **extremely delicate**. A mistake could:
- Lock out all existing users
- Corrupt password database
- Require manual password resets for all users
- Cause production outage

**This plan MUST be reviewed, tested, and approved before implementation.**

---

## Current Situation Analysis

### Current Password Hashing Methods

**Configuration:** `includes/settings.php`
```php
$loginMethod = "crypt";  // Options: crypt | md5 | plain
```

**Problems Identified:**

1. **MD5 Hashing** (Broken)
   - No salt
   - Cryptographically broken since 2004
   - Rainbow tables crack in milliseconds
   - **Risk:** Complete password compromise

2. **Weak CRYPT** (Inadequate)
   - Only 2-character salt: `substr($password, 0, 2)`
   - Salt derived from password itself (predictable)
   - Uses DES algorithm (56-bit key)
   - **Risk:** GPU cracking in hours

3. **Plain Text** (Catastrophic)
   - Passwords stored unencrypted
   - Database breach = instant compromise
   - **Risk:** Complete credential exposure

4. **Global Setting** (Inflexible)
   - All users use same method
   - Can't migrate gradually
   - Changing setting affects everyone immediately

### Current Code Locations

**Password Functions:**
- `/classes/Util.php:187-212` - `passwordMatch()`
- `/classes/Util.php:224-242` - `doesPasswordMatch()`
- `/classes/Util.php:252-268` - `getPassword()`

**Login Handler:**
- `/general/login.php:106-112` - Password verification
- `/general/login.php:118-119` - Session password storage (also weak)

**Password Changes:**
- `/preferences/updatepassword.php`
- `/users/edituser.php`
- `/teams/addclientuser.php`

### Database Structure

**Table:** `members`
**Password Column:** `password` (VARCHAR)
**No Hash Type Tracking:** All passwords assumed to use global `$loginMethod`

---

## Migration Strategy Overview

### Goals

1. ✅ **No User Lockouts** - All existing users can still log in
2. ✅ **Gradual Migration** - Users upgraded opportunistically
3. ✅ **Backward Compatibility** - Support old hashes during transition
4. ✅ **Admin Tools** - Force migration when needed
5. ✅ **Modern Security** - Use bcrypt or Argon2id
6. ✅ **Eventual Deprecation** - Remove old methods after migration

### Approach: Hybrid Hash System

**Strategy:** Support multiple hash algorithms simultaneously with per-user tracking.

**Key Principles:**
- Detect hash type from stored hash format
- Verify against appropriate algorithm
- Upgrade on successful login (opportunistic)
- Never break existing logins

---

## Implementation Plan

### Phase 1: Preparation (Week 1)

#### 1.1 Database Schema Changes

**Add hash type tracking column:**

```sql
ALTER TABLE members ADD COLUMN password_hash_type VARCHAR(20) DEFAULT NULL;
```

**Possible values:**
- `NULL` - Legacy (use global $loginMethod)
- `md5` - Legacy MD5 hash
- `crypt` - Legacy crypt() hash
- `bcrypt` - Modern bcrypt hash
- `argon2id` - Modern Argon2id hash (recommended)

#### 1.2 Detect Existing Hash Types

Run migration script to populate `password_hash_type`:

```php
// For each existing user:
// - If password matches MD5 format (32 hex chars): Set 'md5'
// - If password matches bcrypt format ($2y$): Set 'bcrypt'
// - If password matches argon2id format ($argon2id$): Set 'argon2id'
// - Otherwise: Set to current $loginMethod value
```

#### 1.3 Backup

**CRITICAL:** Full database backup before any changes!

```bash
mysqldump phpcollab > phpcollab_pre_password_migration.sql
# Store securely with timestamp
```

---

### Phase 2: Code Implementation (Week 2)

#### 2.1 New Password Utility Class

Create `/classes/Security/PasswordHasher.php`:

```php
<?php
namespace phpCollab\Security;

class PasswordHasher
{
    const HASH_BCRYPT = PASSWORD_BCRYPT;
    const HASH_ARGON2ID = PASSWORD_ARGON2ID;
    const DEFAULT_ALGORITHM = self::HASH_ARGON2ID;

    /**
     * Hash password with modern algorithm
     */
    public static function hash(string $password): string
    {
        return password_hash($password, self::DEFAULT_ALGORITHM);
    }

    /**
     * Verify password against any supported hash type
     */
    public static function verify(string $password, string $hash, ?string $hashType = null): bool
    {
        // Auto-detect if not specified
        if ($hashType === null) {
            $hashType = self::detectHashType($hash);
        }

        switch ($hashType) {
            case 'bcrypt':
            case 'argon2id':
            case 'argon2i':
                return password_verify($password, $hash);

            case 'md5':
                // DEPRECATED - for migration only
                return hash_equals(md5($password), $hash);

            case 'crypt':
                // DEPRECATED - for migration only
                $salt = substr($hash, 0, 2);
                return hash_equals(crypt($password, $salt), $hash);

            case 'plain':
                // DEPRECATED - for migration only
                return hash_equals($password, $hash);

            default:
                return false;
        }
    }

    /**
     * Detect hash type from hash string
     */
    public static function detectHashType(string $hash): string
    {
        // Bcrypt: $2y$10$... (60 chars)
        if (preg_match('/^\$2[ayb]\$.{56}$/', $hash)) {
            return 'bcrypt';
        }

        // Argon2id: $argon2id$...
        if (strpos($hash, '$argon2id$') === 0) {
            return 'argon2id';
        }

        // Argon2i: $argon2i$...
        if (strpos($hash, '$argon2i$') === 0) {
            return 'argon2i';
        }

        // MD5: 32 hexadecimal characters
        if (preg_match('/^[a-f0-9]{32}$/', $hash)) {
            return 'md5';
        }

        // Plain text: No specific format (risky detection)
        // Crypt: Everything else (13 chars typically)
        if (strlen($hash) <= 13) {
            return 'crypt';
        }

        return 'plain';
    }

    /**
     * Check if password needs rehashing
     */
    public static function needsRehash(string $hash, ?string $hashType = null): bool
    {
        if ($hashType === null) {
            $hashType = self::detectHashType($hash);
        }

        // Any legacy hash needs upgrade
        if (in_array($hashType, ['md5', 'crypt', 'plain'])) {
            return true;
        }

        // Modern hashes may need rehash if algorithm changed
        if ($hashType === 'bcrypt' || $hashType === 'argon2id') {
            return password_needs_rehash($hash, self::DEFAULT_ALGORITHM);
        }

        return false;
    }
}
```

#### 2.2 Update Login Process

**File:** `/general/login.php`

**Current:**
```php
if (!phpCollab\Util::doesPasswordMatch($username, $password,
    $member['mem_password'], $loginMethod)) {
    $error = $strings["invalid_login"];
}
```

**New:**
```php
use phpCollab\Security\PasswordHasher;

$hashType = $member['mem_password_hash_type'] ?? $loginMethod;
$passwordValid = PasswordHasher::verify($password,
    $member['mem_password'], $hashType);

if (!$passwordValid) {
    $logger->notice('Invalid password', ['username' => $username]);
    $error = $strings["invalid_login"];
} else {
    // OPPORTUNISTIC UPGRADE: Rehash if using legacy algorithm
    if (PasswordHasher::needsRehash($member['mem_password'], $hashType)) {
        $newHash = PasswordHasher::hash($password);
        $members->updatePassword($member['mem_id'], $newHash, 'argon2id');

        $logger->info('Password upgraded to modern hash', [
            'user_id' => $member['mem_id'],
            'old_type' => $hashType,
            'new_type' => 'argon2id'
        ]);
    }

    // Proceed with login...
}
```

**Key Feature: OPPORTUNISTIC UPGRADE**
- User logs in with old password
- System verifies against old hash (works!)
- System immediately rehashes with modern algorithm
- Next login uses new secure hash
- **No user interruption!**

#### 2.3 Update Password Change Functions

**Files to Update:**
- `/preferences/updatepassword.php`
- `/users/edituser.php`
- `/users/addclientuser.php`
- `/teams/addclientuser.php`

**Change:**
```php
// OLD:
$password = phpCollab\Util::getPassword($newPassword, $loginMethod);

// NEW:
use phpCollab\Security\PasswordHasher;
$password = PasswordHasher::hash($newPassword);
$hashType = 'argon2id';
// Store both $password and $hashType in database
```

---

### Phase 3: Admin Migration Tools (Week 3)

#### 3.1 Password Migration Status Page

**Location:** `/administration/password_migration_status.php`

**Features:**
- Show migration progress (% of users on modern hashing)
- List users still on legacy hashing
- Statistics by hash type
- Estimated time to full migration

**Example Display:**
```
Password Security Migration Status
====================================

Total Users: 150

Hash Type Distribution:
- Argon2id (Secure):    75 users (50%) ✓
- Bcrypt (Secure):      10 users (6%)  ✓
- Crypt (Legacy):       50 users (33%) ⚠️
- MD5 (Legacy):         15 users (10%) ❌
- Plain (Legacy):        0 users (0%)  ❌

Migration Progress: 56% Complete

Users still on legacy hashing:
- Last login > 90 days: 30 users (recommend force reset)
- Last login 30-90 days: 20 users (will migrate on next login)
- Last login < 30 days: 15 users (will migrate soon)
```

#### 3.2 Force Migration Tool

**Location:** `/administration/force_password_migration.php`

**Options:**

**Option 1: Force Upgrade Specific Users**
- Select users to force upgrade
- Sends "Password Reset Required" email
- User must reset password via email link
- New password uses modern hashing

**Option 2: Mass Migration (Risky)**
- NOT RECOMMENDED for MD5/plain
- Only for crypt → bcrypt
- Rehashes all passwords in database
- **WARNING:** Could lock users out if something fails

**Option 3: Graceful Deprecation**
- After 6 months, block logins with legacy hashes
- Force password reset via email
- Ensures all users eventually migrate

---

### Phase 4: Testing (Week 3-4)

#### 4.1 Test Scenarios

**Test 1: Existing MD5 User Login**
1. User with MD5 password logs in
2. System verifies against MD5 (works)
3. System upgrades to Argon2id
4. User logs out and back in
5. System now uses Argon2id (faster, more secure)

**Test 2: Existing Crypt User Login**
1. User with crypt password logs in
2. System verifies against crypt (works)
3. System upgrades to Argon2id
4. Verify new hash in database

**Test 3: New User Creation**
1. Create new user
2. Verify password uses Argon2id
3. Verify `password_hash_type` = 'argon2id'

**Test 4: Password Change**
1. User changes password
2. New password uses Argon2id
3. Old sessions invalidated

**Test 5: Failed Login**
1. Wrong password with legacy hash
2. System does NOT upgrade
3. User remains on old hash until successful login

**Test 6: Rollback**
1. Restore database backup
2. Revert code changes
3. Verify all users can still log in

#### 4.2 Load Testing

- Test with 1000+ user database
- Verify performance impact
- Monitor login times (Argon2id is slower - by design)

---

### Phase 5: Deployment (Week 5)

#### 5.1 Pre-Deployment Checklist

- [ ] Full database backup completed
- [ ] Backup tested and verified restorable
- [ ] Code reviewed by security team
- [ ] Unit tests passing (100% coverage on PasswordHasher)
- [ ] Integration tests passing
- [ ] Staging environment tested
- [ ] Rollback plan documented and tested
- [ ] User communication prepared
- [ ] Support team briefed

#### 5.2 Deployment Steps

**Step 1: Database Migration (Maintenance Window)**
```sql
-- Add column
ALTER TABLE members ADD COLUMN password_hash_type VARCHAR(20) DEFAULT NULL;

-- Populate hash types for existing users
UPDATE members SET password_hash_type = 'md5'
WHERE LENGTH(password) = 32 AND password REGEXP '^[a-f0-9]{32}$';

UPDATE members SET password_hash_type = 'crypt'
WHERE password_hash_type IS NULL;
```

**Step 2: Deploy Code**
- Deploy new `PasswordHasher` class
- Deploy updated `login.php`
- Deploy updated password change handlers
- Deploy admin migration tools

**Step 3: Monitor**
- Watch error logs for authentication failures
- Monitor migration progress
- Track user support tickets

**Step 4: User Communication**
```
Subject: phpCollab Security Upgrade

We've upgraded our password security to protect your account better.

What this means for you:
- No action required!
- Next time you log in, your password will be automatically upgraded
- Your login credentials remain the same
- This change makes your account significantly more secure

If you have any issues logging in, please contact support.
```

---

### Phase 6: Deprecation (Month 6)

#### 6.1 Migration Deadline

After 6 months of opportunistic migration:

**Check Progress:**
```sql
SELECT password_hash_type, COUNT(*) as count
FROM members
GROUP BY password_hash_type;
```

**If >90% migrated:**
- Set deadline for legacy hash deprecation
- Email remaining users
- Force password reset after deadline

**Email Template:**
```
Subject: Action Required: Update Your Password

Your phpCollab account is using an outdated security method.

ACTION REQUIRED BY [DATE]:
1. Log in to phpCollab
2. Your password will be automatically upgraded
3. No other action needed

After [DATE], you will need to reset your password if you haven't logged in.

This is a one-time security upgrade to better protect your account.
```

#### 6.2 Final Cutover

**After deadline:**
```php
// In login.php
if (in_array($hashType, ['md5', 'crypt', 'plain'])) {
    $error = $strings["password_reset_required"];
    // Send password reset email
    // Redirect to password reset page
    return;
}
```

---

## Risk Mitigation

### Risk 1: Mass User Lockout

**Likelihood:** Medium
**Impact:** Critical

**Mitigation:**
- Thorough testing on staging with production data copy
- Gradual rollout (start with 10%, then 50%, then 100%)
- Rollback plan ready
- 24/7 support during deployment
- Admin override to reset passwords

### Risk 2: Performance Degradation

**Likelihood:** Low
**Impact:** Medium

**Mitigation:**
- Argon2id is intentionally slow (good for security)
- Test with production-scale database
- Monitor login times
- Consider caching authenticated sessions longer

### Risk 3: Database Corruption

**Likelihood:** Low
**Impact:** Critical

**Mitigation:**
- Multiple backups before migration
- Test restore procedure
- Backup before AND after migration
- Point-in-time recovery capability

### Risk 4: Incomplete Migration

**Likelihood:** Medium
**Impact:** Low

**Mitigation:**
- Admin tools to track progress
- Automated emails to inactive users
- Force migration tool for stragglers
- Keep legacy support for 6-12 months

---

## Rollback Plan

**If things go wrong:**

### Emergency Rollback (< 1 hour)

```bash
# 1. Restore database backup
mysql phpcollab < phpcollab_pre_password_migration.sql

# 2. Revert code changes
git revert <commit-hash>
git push

# 3. Verify users can log in
# Test with known credentials

# 4. Communicate to users
```

### Partial Rollback

If only some users affected:
```sql
-- Restore specific user passwords from backup
UPDATE members m1
JOIN backup_members m2 ON m1.id = m2.id
SET m1.password = m2.password,
    m1.password_hash_type = NULL
WHERE m1.id IN (affected_user_ids);
```

---

## Success Criteria

Migration is successful when:

1. ✅ **Zero user lockouts** - All existing users can still log in
2. ✅ **90%+ migrated** - At least 90% of active users on modern hashing within 3 months
3. ✅ **100% migrated** - All users on modern hashing within 6 months
4. ✅ **No security incidents** - No password-related breaches during transition
5. ✅ **Performance acceptable** - Login times < 2 seconds
6. ✅ **Legacy deprecated** - MD5/crypt/plain support removed after 6 months

---

## Configuration Options

### Recommended Settings

**Production:**
```php
// includes/settings.php
$passwordHashAlgorithm = PASSWORD_ARGON2ID;  // NEW setting
$loginMethod = "crypt";  // Legacy support during migration
$passwordMigrationMode = true;  // Enable opportunistic upgrade
```

**Security-Critical Environments:**
```php
$passwordHashAlgorithm = PASSWORD_ARGON2ID;
$argon2Options = [
    'memory_cost' => 65536,  // 64MB
    'time_cost' => 4,
    'threads' => 2
];
```

---

## Timeline Summary

| Week | Phase | Activity |
|------|-------|----------|
| 1 | Preparation | Database backup, schema changes, hash detection |
| 2 | Implementation | Code changes, PasswordHasher class, login updates |
| 3 | Admin Tools | Migration status page, force migration tool |
| 3-4 | Testing | Unit tests, integration tests, staging deployment |
| 5 | Deployment | Production deployment, monitoring |
| 5-26 | Migration | Opportunistic upgrade as users log in |
| 26 | Deprecation | Force remaining users to upgrade |

**Total Duration:** 6 months for complete migration

---

## Alternative Approaches Considered

### Alternative 1: Force Reset All Passwords

**Pros:** Immediate security improvement
**Cons:** Massive user disruption, support burden
**Decision:** ❌ Rejected - too disruptive

### Alternative 2: Maintain Dual Hashing

**Pros:** No migration needed
**Cons:** Continued security risk, code complexity
**Decision:** ❌ Rejected - doesn't fix vulnerability

### Alternative 3: Opportunistic Migration (CHOSEN)

**Pros:** Zero user disruption, gradual improvement
**Cons:** Slower migration
**Decision:** ✅ Selected - best balance of security and UX

---

## Questions for Review

Before implementing, please answer:

1. **User Base:** How many active users? How many inactive users?
2. **Current Hash:** What is the actual `$loginMethod` in production?
3. **Maintenance Window:** Is there a maintenance window available?
4. **Rollback Tolerance:** How quickly must we rollback if issues occur?
5. **Support Capacity:** Can support handle password reset requests?
6. **Testing Environment:** Is there a staging server with production data copy?
7. **Backup Strategy:** What is current backup/restore procedure?

---

## Approval Required

**Before proceeding, this plan must be approved by:**

- [ ] Security Team Lead
- [ ] Database Administrator
- [ ] Development Team Lead
- [ ] Product Manager
- [ ] Operations/DevOps Lead

**Approved By:** _________________
**Date:** _________________

---

## References

- PHP password_hash() documentation: https://www.php.net/manual/en/function.password-hash.php
- OWASP Password Storage Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html
- Argon2 RFC: https://datatracker.ietf.org/doc/html/rfc9106
- Migration Best Practices: https://paragonie.com/blog/2016/02/how-safely-store-password-in-2016

---

**Document Version:** 1.0 (DRAFT)
**Status:** AWAITING APPROVAL
**Next Review:** [DATE]
**Author:** Security Team
