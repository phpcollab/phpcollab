# Password Security Migration - User Guide

**For phpCollab Administrators and End Users**

---

## What's Changing?

phpCollab is upgrading to modern, secure password hashing to better protect your account.

### The Old System (Insecure):
- MD5 hashing (broken since 2004)
- Weak crypt() with 2-character salt
- Passwords vulnerable to cracking

### The New System (Secure):
- Argon2id or bcrypt (industry standard)
- Cryptographically secure
- Passwords protected against modern attacks

---

## What Do I Need to Do?

### For End Users: **NOTHING!**

Your password migration happens automatically:

1. **Log in normally** with your current password
2. **System automatically upgrades** your password security
3. **You're done!** - No password change required

**Your password stays the same** - only the security improves.

---

## For Administrators

### Step 1: Run the Migration Script

**Before upgrading phpCollab**, run the database migration:

**Option A: Via Web Browser**
```
Navigate to: /installation/migrations/migration_password_hashing.php
```

**Option B: Via Command Line**
```bash
cd /path/to/phpcollab
php installation/migrations/migration_password_hashing.php
```

**What it does:**
- Adds `password_hash_type` column to members table
- Detects hash type for existing users
- **Does NOT change any passwords**
- Safe to run multiple times

### Step 2: Deploy Updated Code

Deploy the new phpCollab version with password migration support.

### Step 3: Monitor Migration Progress

**Check migration status:**
- Log in as administrator
- Navigate to: Administration → Password Security (coming soon)

**Or check manually:**
```sql
SELECT
    password_hash_type,
    COUNT(*) as user_count
FROM members
GROUP BY password_hash_type;
```

**Expected results over time:**
```
Week 1:  20% migrated
Month 1: 60% migrated
Month 3: 90% migrated
Month 6: 100% migrated
```

---

## Frequently Asked Questions

### Q: Will this change my password?
**A: No!** Your password remains the same. Only the security method changes.

### Q: Do I need to reset my password?
**A: No!** Migration happens automatically when you log in.

### Q: What if I haven't logged in for months?
**A: No problem!** When you log in next, your password will be upgraded automatically.

### Q: Will this lock me out?
**A: No!** The migration maintains backward compatibility with old passwords.

### Q: How long does migration take?
**A: Instant!** Your password is upgraded during your normal login (you won't even notice).

### Q: What if something goes wrong?
**A: We can rollback!** Administrators have full rollback capability (see below).

### Q: Can I keep using MD5/crypt?
**A: Temporarily, yes.** Old passwords work during migration period. After 6 months, you'll need to upgrade for security.

### Q: Will password requirements change?
**A: Not immediately.** Password complexity rules can be added later but aren't required for migration.

---

## Migration Timeline

### Week 1: Deployment
- Database migration completed
- Code deployed
- Users begin upgrading automatically

### Months 1-6: Gradual Migration
- Active users upgraded automatically
- Inactive users upgraded when they log in
- No user interruption

### Month 6+: Cleanup
- 90%+ users migrated
- Email reminders to remaining users
- Eventually deprecate legacy hashing

---

## Troubleshooting

### Problem: "Password upgrade failed" in logs

**Solution:** Check database permissions
```sql
GRANT UPDATE ON phpcollab.members TO 'phpcollab_user'@'localhost';
```

### Problem: User can't log in after migration

**Cause:** Rare - usually a configuration issue

**Solution:**
1. Check that migration script completed successfully
2. Verify password_hash_type column exists
3. Check error logs for details
4. Contact support if needed

### Problem: Want to force upgrade for specific users

**Solution:** Use admin tools (coming soon) or:
```php
// Force password reset email
// User clicks link and sets new password
// New password uses modern hashing
```

---

## Rollback Procedure

**If you need to undo the migration:**

### Step 1: Restore Database
```bash
mysql phpcollab < backup_before_migration.sql
```

### Step 2: Revert Code
```bash
git revert <migration_commit_hash>
```

### Step 3: Verify
- Test that users can log in
- Check that system works normally

**Note:** Rollback should only be needed in extreme cases.

---

## Security Benefits

After full migration:

| Metric | Before | After |
|--------|--------|-------|
| **Time to crack password** | Minutes-Hours | Centuries |
| **Rainbow table vulnerability** | Yes | No |
| **GPU cracking** | Vulnerable | Protected |
| **Salt security** | 2 chars | Full |
| **Modern algorithm** | No | Yes |
| **Compliance ready** | No | Yes |

---

## Technical Details

### Hash Types Supported

**Legacy (migration only):**
- `md5` - MD5 hash, no salt
- `crypt` - DES crypt with 2-char salt
- `plain` - Plain text (catastrophic)

**Modern (recommended):**
- `argon2id` - Argon2id (most secure)
- `bcrypt` - Bcrypt (very secure)

### Database Schema Change

```sql
ALTER TABLE members
ADD COLUMN password_hash_type VARCHAR(20) DEFAULT NULL
AFTER password;
```

### How Opportunistic Upgrade Works

```
1. User enters password
2. System verifies against current hash (MD5/crypt/etc.)
3. If match: Login successful
4. System checks: Is hash type legacy?
5. If yes: Rehash with Argon2id
6. Update database: New hash + hash type
7. Next login: Uses new secure hash
```

---

## Support

**Questions?** Review the full technical documentation:
- `PASSWORD_MIGRATION_PLAN.md` - Complete technical plan
- `PASSWORD_MIGRATION_SUMMARY.md` - Executive summary

**Issues?** Check:
- Error logs: `/logs/phpcollab.log`
- Migration logs: Check output from migration script
- Database: Verify `password_hash_type` column exists

**Need help?** Contact:
- phpCollab community forums
- GitHub issues
- Your system administrator

---

## Checklist for Administrators

Before migration:
- [ ] Database backup completed
- [ ] Backup tested and verified
- [ ] Review PASSWORD_MIGRATION_PLAN.md
- [ ] Test on staging environment
- [ ] Schedule maintenance window (optional)

During migration:
- [ ] Run migration script
- [ ] Verify migration completed successfully
- [ ] Deploy updated code
- [ ] Test that logins still work
- [ ] Monitor error logs

After migration:
- [ ] Track migration progress
- [ ] Monitor for issues
- [ ] Communicate to users (optional - seamless migration)
- [ ] Plan for eventual legacy deprecation

---

**Version:** 1.0
**Last Updated:** November 8, 2025
**Status:** Ready for Deployment
