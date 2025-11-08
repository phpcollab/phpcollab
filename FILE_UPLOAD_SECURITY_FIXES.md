# File Upload Security Fixes

**Date:** November 8, 2025
**Severity:** CRITICAL (CVSS 9.6)
**Status:** ✅ FIXED

---

## Overview

This document details the security fixes applied to resolve critical file upload vulnerabilities in phpCollab that could lead to **Remote Code Execution (RCE)** and complete server compromise.

---

## Vulnerabilities Fixed

### 1. Missing MIME Type Validation (CWE-434)
**Files Affected:** `linkedcontent/addfile.php`, `projects_site/uploadfile.php`

**Before:**
- Only validated file extension (easily spoofed)
- Checked only 3 dangerous extensions: `.php`, `.php3`, `.phtml`

**After:**
- Validates actual MIME type from file content
- Strict whitelist of allowed MIME types
- Verifies MIME type matches file extension

### 2. User-Controlled File Size Bypass
**Files Affected:** `linkedcontent/addfile.php`, `projects_site/uploadfile.php`

**Before:**
```php
if (!empty($request->request->get("maxCustom"))) {
    $maxFileSize = $request->request->get("maxCustom"); // VULNERABLE!
}
```
Attacker could send `maxCustom=999999999` to bypass limits.

**After:**
```php
$maxFileSize = !empty($projectDetail["pro_upload_max"])
    ? $projectDetail["pro_upload_max"]
    : SecureFileUploadValidator::MAX_FILE_SIZE;
```
Uses project-defined or system default limits only.

### 3. Path Traversal via Filename
**Files Affected:** `linkedcontent/addfile.php`, `projects_site/uploadfile.php`

**Before:**
```php
$filename = phpCollab\Util::checkFileName($_FILES['upload']['name']);
// Uses basename() only - insufficient
phpCollab\Util::uploadFile("files/$projectId/$taskId", $source, "$num--" . $filename);
```

**After:**
```php
$secureFilename = SecureFileUploadValidator::generateSecureFilename($extension);
// Cryptographically secure random filename: e.g., "a7f3d2e1b9c4f6a8...e5d7.pdf"
phpCollab\Util::uploadFile("files/$projectId/$taskId", $source, $secureFilename);
```

### 4. Weak Extension Blacklist
**Before:** Only blocked `.php`, `.php3`, `.phtml`

**After:** Comprehensive blacklist of 40+ dangerous extensions including:
- PHP variants: `.php4`, `.php5`, `.php7`, `.pht`, `.phar`, `.inc`
- Web config: `.htaccess`, `.htpasswd`, `.web.config`
- Executables: `.exe`, `.dll`, `.bat`, `.cmd`, `.sh`, `.bash`
- Scripts: `.asp`, `.aspx`, `.jsp`, `.cgi`, `.pl`, `.py`, `.rb`, `.vbs`

### 5. Double Extension Bypass
**Before:** `malicious.php.jpg` would be accepted and could execute as PHP

**After:**
- MIME type validation prevents this
- Extension matching ensures file content matches claimed type
- Multiple dot detection in filename sanitization

### 6. Null Byte Injection
**Before:** No protection against `malicious.php%00.jpg`

**After:** Explicit null byte detection:
```php
if (strpos($originalName, "\0") !== false) {
    $errors[] = 'Filename contains null byte';
}
```

### 7. No Virus/Malware Scanning
**Status:** Documented as future enhancement
**Recommendation:** Integrate ClamAV or similar for production use

---

## New Security Class

### SecureFileUploadValidator

**Location:** `/classes/Files/SecureFileUploadValidator.php`

**Features:**
- Comprehensive MIME type whitelist
- Extension validation and matching
- Path traversal detection (including URL-encoded variants)
- Null byte detection
- Filename character validation
- Cryptographically secure filename generation
- File size validation
- Upload error handling

**Allowed File Types:**
- **Images:** JPEG, PNG, GIF, WebP
- **Documents:** PDF, DOC/DOCX, XLS/XLSX, PPT/PPTX
- **Text:** TXT, CSV
- **Archives:** ZIP, RAR, 7Z (use with caution)

**Usage:**
```php
use phpCollab\Files\SecureFileUploadValidator;

// Validate file
$validation = SecureFileUploadValidator::validate($uploadedFile, $maxSize);

if (!$validation['valid']) {
    // Handle errors
    foreach ($validation['errors'] as $error) {
        echo $error;
    }
} else {
    // Generate secure filename
    $secureFilename = SecureFileUploadValidator::generateSecureFilename(
        $validation['extension']
    );

    // Upload file with secure name
    uploadFile($path, $uploadedFile->getPathName(), $secureFilename);
}
```

---

## Files Modified

### 1. `/classes/Files/SecureFileUploadValidator.php`
**Status:** NEW FILE
**Lines:** 380+
**Purpose:** Comprehensive file upload security validation

### 2. `/linkedcontent/addfile.php`
**Changes:**
- Added `SecureFileUploadValidator` import (Line 4)
- Replaced lines 52-98 with secure validation
- Replaced lines 124-145 with secure file upload
- **Impact:** All internal team file uploads now secure

### 3. `/projects_site/uploadfile.php`
**Changes:**
- Added `SecureFileUploadValidator` import (Line 30)
- Replaced lines 50-99 with secure validation
- Replaced lines 101-115 with secure file upload
- **Impact:** All client-facing file uploads now secure

---

## Security Improvements Summary

| Vulnerability | Before | After | Risk Eliminated |
|---------------|--------|-------|-----------------|
| MIME Type Validation | ❌ None | ✅ Strict whitelist | RCE via file type confusion |
| Extension Validation | ⚠️ 3 blocked | ✅ 40+ blocked | PHP/script execution |
| Path Traversal | ❌ basename() only | ✅ Comprehensive checks | Directory traversal |
| Filename Security | ⚠️ User-controlled | ✅ Random generation | All filename attacks |
| File Size Bypass | ❌ User-controlled | ✅ System-enforced | DoS via large files |
| Null Byte Injection | ❌ None | ✅ Explicit check | File type confusion |
| Double Extension | ❌ None | ✅ MIME matching | .php.jpg execution |
| Upload Error Handling | ⚠️ Basic | ✅ Comprehensive | Various upload attacks |

---

## Attack Scenarios Prevented

### Scenario 1: Remote Code Execution via .htaccess
**Before:**
```
1. Upload .htaccess: AddType application/x-httpd-php .jpg
2. Upload shell.jpg containing PHP code
3. Execute via /files/project/task/shell.jpg
4. Full server compromise ✓
```

**After:** ❌ **BLOCKED**
- .htaccess blocked by dangerous extension check
- MIME type validation prevents execution
- Random filename prevents predictable access

### Scenario 2: PHP Shell Upload
**Before:**
```
1. Upload malicious.php5 (not in blocklist)
2. Access /files/project/task/1--malicious.php5
3. Execute system commands ✓
```

**After:** ❌ **BLOCKED**
- .php5 in dangerous extensions list
- MIME type validation rejects PHP files
- Random filename: a7f3d2e1b9c4...pdf

### Scenario 3: Path Traversal
**Before:**
```
1. Upload file named: ../../../etc/passwd
2. Overwrite system files ✓
```

**After:** ❌ **BLOCKED**
- Path traversal sequences detected
- Random filename generation eliminates user control
- basename() usage replaced

### Scenario 4: File Size DoS
**Before:**
```
1. Set maxCustom=999999999 in POST request
2. Upload 10GB file
3. Fill disk space ✓
```

**After:** ❌ **BLOCKED**
- maxCustom parameter ignored
- Project-defined or system max enforced
- Validation before file copy

---

## Testing Recommendations

### Manual Testing

```bash
# Test 1: Try to upload PHP file
curl -F "upload=@shell.php" http://phpcollab/linkedcontent/addfile.php
# Expected: "File type not allowed: application/x-php"

# Test 2: Try double extension
curl -F "upload=@malicious.php.jpg" http://phpcollab/linkedcontent/addfile.php
# Expected: "File extension does not match file content"

# Test 3: Try path traversal
curl -F "upload=@../../../etc/passwd" http://phpcollab/linkedcontent/addfile.php
# Expected: "Filename contains path traversal sequences"

# Test 4: Try size bypass
curl -F "maxCustom=999999999" -F "upload=@huge.zip" http://phpcollab/linkedcontent/addfile.php
# Expected: "File size exceeds maximum allowed size"

# Test 5: Valid upload
curl -F "upload=@document.pdf" http://phpcollab/linkedcontent/addfile.php
# Expected: Success, file saved with random name
```

### Automated Testing

```php
// Unit test example
public function testSecureFileUploadValidator()
{
    $file = new UploadedFile('/tmp/test.pdf', 'test.pdf', 'application/pdf', null, true);
    $result = SecureFileUploadValidator::validate($file, 10485760);

    $this->assertTrue($result['valid']);
    $this->assertEmpty($result['errors']);
}

public function testRejectsPHPFile()
{
    $file = new UploadedFile('/tmp/shell.php', 'shell.php', 'application/x-php', null, true);
    $result = SecureFileUploadValidator::validate($file, 10485760);

    $this->assertFalse($result['valid']);
    $this->assertContains('File type not allowed', $result['errors']);
}
```

---

## Logging and Monitoring

All file upload validation failures are now logged:

```php
$logger->warning('File upload validation failed', [
    'file' => $uploadedFile->getClientOriginalName(),
    'user' => $session->get("login"),
    'errors' => $validation['errors']
]);
```

**Monitor for:**
- Multiple validation failures from same user (possible attack)
- Dangerous extension attempts (`.php`, `.phar`, etc.)
- Path traversal attempts (`../`, encoded variants)
- File size bypass attempts

---

## Configuration

### Allowed File Types

To modify allowed MIME types, edit `/classes/Files/SecureFileUploadValidator.php`:

```php
const ALLOWED_MIME_TYPES = [
    // Add your custom types here
    'application/custom' => ['custom'],
];
```

### Maximum File Size

Default: 10MB (`10485760` bytes)

**Per-Project:** Set in project settings (`pro_upload_max`)
**Global Default:** `SecureFileUploadValidator::MAX_FILE_SIZE`

---

## Future Enhancements

1. **Virus Scanning Integration**
   - Integrate ClamAV for malware detection
   - Scan files before storage
   - Quarantine suspicious files

2. **Original Filename Storage**
   - Add `original_filename` column to files table
   - Store for display purposes only
   - Never use in file paths

3. **Content Disarm and Reconstruction (CDR)**
   - Strip macros from Office documents
   - Sanitize PDF files
   - Remove EXIF data from images

4. **File Storage Outside Web Root**
   - Move `/files/` outside document root
   - Serve via PHP download script
   - Add access control checks

---

## Rollback Instructions

If issues arise, rollback by:

```bash
git revert <commit-hash>
```

**Note:** Rolling back removes all security protections. Only do this if absolutely necessary and implement alternative security measures immediately.

---

## Compliance Impact

These fixes address:
- **OWASP Top 10 2021:** A01 - Broken Access Control
- **OWASP Top 10 2021:** A03 - Injection
- **CWE-434:** Unrestricted Upload of File with Dangerous Type
- **CWE-22:** Path Traversal
- **PCI DSS 6.5.8:** Improper input validation

---

## References

- OWASP File Upload Cheat Sheet: https://cheatsheetseries.owasp.org/cheatsheets/File_Upload_Cheat_Sheet.html
- CWE-434: https://cwe.mitre.org/data/definitions/434.html
- PHP Security Guide: https://www.php.net/manual/en/security.filesystem.php

---

**Document Version:** 1.0
**Last Updated:** November 8, 2025
**Author:** Security Team
**Review Date:** December 8, 2025
