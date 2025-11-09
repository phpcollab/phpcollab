# Security Test Coverage

This document describes the automated security tests created to verify the security improvements implemented during the comprehensive security audit.

---

## Test Framework

**Codeception** - BDD-style PHP testing framework
- Acceptance tests: Browser-based functional tests
- Unit tests: Component-level tests

---

## Test Coverage Summary

| Security Feature | Test File | Test Type | Status |
|------------------|-----------|-----------|--------|
| Brute Force Protection | `LoginBruteForceProtectionCept.php` | Acceptance | ✅ Created |
| Session Fixation Prevention | `LoginSessionRegenerationCept.php` | Acceptance | ✅ Created |
| Information Disclosure | `InformationDisclosurePreventionCept.php` | Acceptance | ✅ Created |
| Database Backup Security | `DatabaseBackupTest.php` | Unit | ⚠️ Skeleton Created |

---

## Running the Tests

### Run All Tests
```bash
vendor/bin/codecept run
```

### Run Acceptance Tests Only
```bash
vendor/bin/codecept run acceptance
```

### Run Unit Tests Only
```bash
vendor/bin/codecept run unit
```

### Run Specific Security Tests
```bash
# Brute force protection
vendor/bin/codecept run tests/acceptance/public/LoginBruteForceProtectionCept.php

# Session regeneration
vendor/bin/codecept run tests/acceptance/public/LoginSessionRegenerationCept.php

# Information disclosure prevention
vendor/bin/codecept run tests/acceptance/security/InformationDisclosurePreventionCept.php

# Database backup security
vendor/bin/codecept run tests/unit/Administration/DatabaseBackupTest.php
```

### Run Tests with Detailed Output
```bash
vendor/bin/codecept run --steps --debug
```

---

## Test Descriptions

### 1. Login Brute Force Protection Test

**File:** `tests/acceptance/public/LoginBruteForceProtectionCept.php`

**Tests:**
- ✅ First 4 failed login attempts are allowed
- ✅ 5th failed attempt triggers 15-minute lockout
- ✅ Correct password is rejected during lockout period
- ✅ Lockout persists across multiple attempts
- ✅ Error message displays time remaining

**Security Coverage:**
- OWASP ASVS 2.2.1: Anti-automation
- Account takeover prevention
- Rate limiting effectiveness

**Related Code:** `general/login.php` lines 65-90

---

### 2. Session Fixation Prevention Test

**File:** `tests/acceptance/public/LoginSessionRegenerationCept.php`

**Tests:**
- ✅ Session ID exists before login
- ✅ Session ID changes after successful login
- ✅ Old session ID becomes invalid
- ✅ New session ID grants access
- ✅ Session IDs are different (not predictable)

**Security Coverage:**
- OWASP ASVS 3.2.1: Session binding
- Session fixation attack prevention
- Session hijacking mitigation

**Related Code:** `general/login.php` line 107

---

### 3. Information Disclosure Prevention Test

**File:** `tests/acceptance/security/InformationDisclosurePreventionCept.php`

**Tests:**

**Sensitive File Protection:**
- ✅ `.git` directory blocked (403 Forbidden)
- ✅ `.env` file blocked
- ✅ `composer.json` blocked
- ✅ `composer.lock` blocked
- ✅ `README.md` blocked
- ✅ `CHANGELOG.md` blocked
- ✅ `.gitignore` blocked

**Backup/Temp File Protection:**
- ✅ `.bak` files blocked
- ✅ `.backup` files blocked
- ✅ `.sql` files blocked
- ✅ `.old` files blocked
- ✅ `.tmp` files blocked
- ✅ `.log` files blocked

**Configuration Protection:**
- ✅ PHP source code not exposed
- ✅ Configuration constants not visible

**Error Display:**
- ✅ Detailed PHP errors not shown to users
- ✅ File paths not exposed
- ✅ Line numbers not exposed

**Session Security:**
- ✅ Custom session name (`PHPCOLLAB_SESSID`)
- ✅ Default `PHPSESSID` not used

**Directory Browsing:**
- ✅ Directory listing disabled

**Security Headers:**
- ✅ `X-Content-Type-Options: nosniff`
- ✅ `X-Frame-Options: SAMEORIGIN`
- ✅ `X-XSS-Protection` present
- ✅ `Referrer-Policy` present

**Security Coverage:**
- OWASP A05:2021: Security Misconfiguration
- OWASP ASVS 14.3.3: Error handling
- OWASP ASVS 14.3.4: Debug features disabled

**Related Code:** `.htaccess`

---

### 4. Database Backup Security Test (Unit Tests)

**File:** `tests/unit/Administration/DatabaseBackupTest.php`

**Status:** ⚠️ Skeleton created - requires database mocking

**Planned Tests:**

**PostgreSQL Backup:**
- Database type validation
- Shell parameter escaping (SQL injection prevention)
- PGPASSWORD environment variable usage
- Temp file cleanup
- Error handling

**SQL Server Backup:**
- Database type validation
- Table name validation (regex)
- SQL value escaping
- NULL/numeric/string handling
- Temp file cleanup

**General:**
- Admin permission requirement
- CSRF token validation
- Logging of security events

**Security Coverage:**
- Shell injection prevention
- SQL injection prevention
- Authorization checks
- CSRF protection

**Related Code:**
- `classes/Administration/Administration.php` lines 140-399
- `administration/backupPostgreSQL.php`
- `administration/backupSQLServer.php`

**Implementation Note:**
These tests require database mocking infrastructure. Each test is marked with `markTestSkipped()` and includes detailed comments about what should be tested. To implement:

1. Set up database mocking (PHPUnit or Mockery)
2. Mock the `Database` class
3. Mock the `FileDownload` service
4. Remove `markTestSkipped()` calls
5. Implement test logic per comments

---

## Test Environment Setup

### Prerequisites

```bash
# Install Codeception (if not already installed)
composer install --dev

# Build Codeception helpers
vendor/bin/codecept build
```

### Configuration

Tests use the Codeception configuration in:
- `codeception.yml` - Main configuration
- `tests/acceptance.suite.yml` - Acceptance test config
- `tests/unit.suite.yml` - Unit test config

### Test Database

For comprehensive testing, you may need:
- Test database with sample data
- Test user accounts
- Admin user account

**Example test user creation:**
```sql
INSERT INTO phpcollab_members (login, password, name, profil)
VALUES ('testUser', '[hashed_password]', 'Test User', 0);
```

---

## Continuous Integration

### GitHub Actions Example

```yaml
name: Security Tests

on: [push, pull_request]

jobs:
  security-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install dependencies
        run: composer install
      - name: Run security tests
        run: |
          vendor/bin/codecept run tests/acceptance/public/LoginBruteForceProtectionCept.php
          vendor/bin/codecept run tests/acceptance/public/LoginSessionRegenerationCept.php
          vendor/bin/codecept run tests/acceptance/security/InformationDisclosurePreventionCept.php
```

---

## Test Maintenance

### When to Update Tests

1. **When security features change:**
   - Update tests to match new behavior
   - Add tests for new security features

2. **When vulnerabilities are discovered:**
   - Add regression tests to prevent re-introduction

3. **When OWASP guidelines update:**
   - Review tests against new standards
   - Add tests for new requirements

### Test Review Schedule

- **After every security fix:** Run relevant tests
- **Before each release:** Run full security test suite
- **Quarterly:** Review test coverage and update

---

## Coverage Gaps (Future Work)

The following security features should have tests added:

### High Priority

1. **CSRF Token Validation**
   - Test token generation
   - Test token validation
   - Test token expiration
   - Test token rejection

2. **Password Reset Security**
   - Token generation randomness
   - Token expiration (60 minutes)
   - Rate limiting (already has test for "too many requests")
   - Email delivery

3. **File Upload Security**
   - File type validation
   - File size limits
   - Malicious file detection
   - Path traversal prevention

### Medium Priority

4. **Authorization Checks**
   - Admin-only page access
   - Project member-only access
   - File download authorization
   - IDOR prevention

5. **Input Validation**
   - SQL injection prevention (prepared statements)
   - XSS prevention (output encoding)
   - Command injection prevention

6. **Database Backup Integration Tests**
   - Complete PostgreSQL backup workflow
   - Complete SQL Server backup workflow
   - Compressed backup testing
   - Large database handling

### Low Priority

7. **Security Headers**
   - CSP policy enforcement
   - Test inline script/style blocking
   - Test nonce generation

8. **Session Timeout**
   - 30-minute idle timeout
   - Absolute timeout (if implemented)
   - Timeout warning (if implemented)

---

## Security Test Best Practices

### 1. Test Real Attack Vectors

Don't just test valid input - test actual attack patterns:

```php
// Good
$I->fillField('username', "admin' OR '1'='1"); // SQL injection attempt
$I->fillField('file', '../../../etc/passwd');  // Path traversal

// Not as useful
$I->fillField('username', 'validuser'); // Only tests happy path
```

### 2. Verify Security Headers

Always check that security is enforced, not just that features work:

```php
// Good
$I->seeResponseCodeIs(403);  // Verify blocked
$I->dontSee('sensitive data'); // Verify not exposed

// Less secure
$I->seeResponseCodeIs(200);  // Only tests success path
```

### 3. Test Edge Cases

```php
// Test lockout boundaries
for ($i = 1; $i <= 4; $i++) {
    // Should allow 4 attempts
}
// 5th attempt should trigger lockout
```

### 4. Clean Up Test Data

```php
// After each test
$I->resetCookie('PHPCOLLAB_SESSID');
$I->amOnPage('/general/logout.php');
```

---

## Related Security Documentation

- `SECURITY_AUDIT_ADDITIONAL_HARDENING.md` - Detailed vulnerability analysis
- `HISTORICAL_CVE_ANALYSIS.md` - Historical CVE fixes
- `DATABASE_ADMIN_MODERNIZATION_PLAN.md` - Database security improvements
- `DATABASE_RESTORE_GUIDE.md` - Secure restore procedures

---

## Contact & Support

For questions about security tests:
- Review commit messages for implementation details
- Check related code files listed in test comments
- Consult OWASP ASVS 4.0 for security requirements

---

**Last Updated:** November 9, 2025
**Test Suite Version:** 1.0
**Coverage:** 3 critical security features (brute force, session fixation, information disclosure)
