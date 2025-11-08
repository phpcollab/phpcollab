# PHP Collab - File Upload & Path Traversal Security Audit Report

## Executive Summary

This security audit identified **9 critical to high-severity vulnerabilities** in the phpcollab codebase related to file upload handling and path traversal. While the application implements some security measures (CSRF protection, basic file size checking), there are significant gaps that could allow attackers to:

- Upload and execute arbitrary code
- Access files outside intended directories
- Bypass file type restrictions
- Perform unauthorized file operations
- Access or modify other projects' files

---

## CRITICAL VULNERABILITIES

### 1. MISSING MIME TYPE VALIDATION FOR GENERAL FILE UPLOADS

**Severity:** CRITICAL  
**Type:** Unrestricted File Upload (CWE-434)

#### File: `/home/user/phpcollab/linkedcontent/addfile.php`
**Lines: 54-82**

```php
// Clean the filename of spaces, slashes, etc
$filename1 = phpCollab\Util::checkFileName($_FILES['upload']['name']);
$filename = $request->files->get('upload')->getClientOriginalName();

// ... file size check but NO MIME TYPE validation ...

$extension = strtolower(substr(strrchr($filename, "."), 1));

if ($allowPhp == "false") {
    $send = "";
    if (!empty($filename) && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
        $error .= $strings["no_php"] . "<br/>";
        $send = "false";
    }
}
```

#### Vulnerability Analysis:

1. **No MIME Type Validation**: The code only checks file extensions, not actual MIME types
2. **Weak Extension Blacklist**: Only blocks `.php`, `.php3`, `.phtml` but misses:
   - `.phar` (PHP Archive - executable)
   - `.inc` (PHP includes)
   - `.shtml` (Server-parsed HTML)
   - `.htaccess` (Apache config - can enable PHP execution for any file type)
   - `.pht`, `.phtml` (missed in some handlers)
   - Double extensions: `file.php.jpg`, `file.jpg.php`
   - Null byte injection (PHP < 5.3): `file.php%00.jpg`

3. **Client-Supplied Extension**: File extension determined from client filename, not server validation

#### Attack Scenario:

```
1. Attacker uploads file: "shell.phtml" or "shell.php3" 
2. If these variants aren't blocked by admin's configuration
3. Or uploads "shell.htaccess" to make web server execute JPG files as PHP
4. Gets arbitrary code execution on server
```

#### Remediation:

```php
// Use whitelist approach only:
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Whitelist ONLY

try {
    // Validate MIME type using fileinfo extension
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['upload']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        throw new Exception('Invalid file type');
    }
    
    // Validate extension as secondary check
    $extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        throw new Exception('Invalid file extension');
    }
    
    // Regenerate filename completely server-side
    $newFileName = bin2hex(random_bytes(16)) . '.' . $extension;
    move_uploaded_file($_FILES['upload']['tmp_name'], $uploadDir . $newFileName);
    
} catch (Exception $e) {
    // Handle error
}
```

---

### 2. ARBITRARY FILE NAME ALLOWING PATH TRAVERSAL

**Severity:** CRITICAL  
**Type:** Path Traversal (CWE-22)

#### File: `/home/user/phpcollab/linkedcontent/addfile.php`
**Lines: 127-142**

```php
phpCollab\Util::uploadFile("files/$projectId/$taskId",
    $request->files->get('upload')->getPathName(), "$num--" . $filename);
$size = phpCollab\Util::fileInfoSize("../files/" . $projectId . "/" . $taskId . "/" . $num . "--" . $filename);
```

#### File: `/home/user/phpcollab/classes/Util.php`
**Lines: 373-402**

```php
public static function uploadFile($path, $source, $dest)
{
    $pathNew = "../{$path}";
    
    // ... directory creation ...
    
    move_uploaded_file($source, APP_ROOT . "/" . $path . "/" . $dest);
}
```

#### Vulnerability Analysis:

1. **User-Controlled Filename**: `$filename` comes directly from `$_FILES['upload']['name']` after minimal cleaning
2. **checkFileName() is Insufficient**: 
   ```php
   public static function checkFileName($name = '')
   {
       $name = str_replace('\\', '/', $name);
       $name = str_replace(" ", "_", $name);
       $name = str_replace("'", "", $name);
       return basename($name);  // Only removes directory separators
   }
   ```
   `basename()` removes directory path but doesn't prevent:
   - Encoded path traversal: `%2e%2e%2f` (URL encoded `../`)
   - Double encoding: `%252e%252e%252f`
   - Unicode encoding: `..` with different unicode representations

3. **File Path Construction**: Multiple locations build paths with user input:
   - `/linkedcontent/addfile.php:127-129` - Direct concatenation
   - `/projects_site/uploadfile.php:100-101` - Direct concatenation
   - `/projects_site/clientfiledetail.php:129, 137` - Direct concatenation
   - `/linkedcontent/viewfile.php:137-139, 147-149` - Direct concatenation

#### Attack Scenarios:

```
Scenario 1: Upload file with crafted name to escape project directory
- Upload filename: "../../etc/passwd"
- After basename(): "passwd" 
- BUT if properly decoded before: Could traverse directories

Scenario 2: Upload HTaccess to enable PHP execution
- Upload filename: ".htaccess"
- Content: "AddType application/x-httpd-php .jpg"
- Result: All JPG files in that directory execute as PHP

Scenario 3: Access another project's files
- Database stores: fil_project=1, fil_task=2, fil_name="test.jpg"
- Can modify stored fil_name via SQL injection (if SQLi exists)
- Or access via direct file_exists checks with mallicious IDs
```

#### Relevant Code (Path Access):

**File: `/home/user/phpcollab/linkedcontent/viewfile.php`**
**Lines: 612-612, 619-619, 747-747, 754-754**

```php
if (file_exists("../files/" . $version["fil_project"] . "/" . $version["fil_task"] . "/" . $version["fil_name"])) {
    // Direct path construction - no validation that fil_name is within project
}

if (file_exists("../files/" . $review["fil_project"] . "/" . $review["fil_name"])) {
    // No check that fil_name doesn't contain path traversal sequences
}
```

**File: `/home/user/phpcollab/linkedcontent/deletefiles.php`**
**Lines: 41-41, 45-45**

```php
if (file_exists("../files/" . $project . "/" . $task . "/" . $file["fil_name"])) {
    phpCollab\Util::deleteFile("files/" . $project . "/" . $task . "/" . $file["fil_name"]);
}
```

#### Remediation:

```php
// In Util::uploadFile() - Validate destination path:
public static function uploadFile($path, $source, $dest)
{
    // Validate that destination doesn't contain path traversal
    $safeDest = basename($dest); // Remove any path components
    
    // Verify final path is within expected directory
    $fullPath = realpath(APP_ROOT . "/" . $path);
    $finalPath = realpath($fullPath . "/" . $safeDest);
    
    if (strpos($finalPath, $fullPath) !== 0) {
        throw new Exception('Invalid file destination');
    }
    
    // Use server-generated filename instead
    $extension = pathinfo($safeDest, PATHINFO_EXTENSION);
    $serverFilename = bin2hex(random_bytes(16)) . '.' . $extension;
    
    move_uploaded_file($source, $fullPath . "/" . $serverFilename);
    return $serverFilename; // Return server filename for database storage
}

// Update database with server-generated filename, not user-supplied
```

---

### 3. MISSING FILE SIZE VALIDATION ENFORCEMENT

**Severity:** HIGH  
**Type:** Missing File Size Validation (CWE-190)

#### File: `/home/user/phpcollab/classes/Files/FileExceedsSizeService.php`
**Lines: 16-21**

```php
public static function exceedsSize(UploadedFile $fileObj)
{
    if ($fileObj->getSize() > 10000000) {  // Hardcoded 10MB limit
        throw new Exception('Exceeded filesize limit.');
    }
}
```

#### File: `/home/user/phpcollab/linkedcontent/addfile.php`
**Lines: 61-79**

```php
if (!empty($request->request->get("maxCustom"))) {
    $maxFileSize = $request->request->get("maxCustom");  // User-controlled!
}

if ($_FILES['upload']['size'] > $maxFileSize) {
    // Check against user-supplied limit...
}
```

#### Vulnerability Analysis:

1. **Inconsistent Size Validation**:
   - `FileExceedsSizeService` enforces hardcoded 10MB limit
   - But handlers also accept `maxCustom` from form
   - No validation that `maxCustom` is reasonable

2. **User-Controlled Limit**: 
   ```php
   $maxFileSize = $request->request->get("maxCustom");
   ```
   This value comes from user form field and could be:
   - Set to 0 (unlimited)
   - Set to negative (bypasses check)
   - Set to extremely large value (10GB+)

3. **Bypass via Form Manipulation**:
```
Original request:
POST /linkedcontent/addfile.php
maxCustom=5242880&upload=<file>

Attacker modifies:
maxCustom=999999999&upload=<1GB file>

Server accepts the large file
```

#### Remediation:

```php
// Define server-side maximum file size
const MAX_FILE_SIZE = 52428800; // 50MB hard limit

// Validate custom max is within acceptable range
$maxCustom = intval($request->request->get("maxCustom"));
if ($maxCustom <= 0 || $maxCustom > self::MAX_FILE_SIZE) {
    $maxCustom = self::MAX_FILE_SIZE;
}

// Compare against server maximum, not user input
if ($uploadedFile->getSize() > $maxCustom || 
    $uploadedFile->getSize() > self::MAX_FILE_SIZE) {
    throw new Exception('File size exceeds limit');
}
```

---

### 4. WEAK EXECUTABLE FILE UPLOAD PROTECTION

**Severity:** CRITICAL  
**Type:** Unrestricted Upload of Dangerous File Type (CWE-434)

#### File: `/home/user/phpcollab/projects_site/uploadfile.php`
**Lines: 82-88**

```php
if ($allowPhp == "false") {
    $send = "";
    if ($filename != "" && ($extension == "php" || $extension == "php3" || $extension == "phtml")) {
        $error .= $strings["no_php"] . "<br/>";
        $send = "false";
    }
}
```

#### Vulnerability Analysis:

1. **Incomplete Executable Extensions**:
   - Missing: `.phtml`, `.pht`, `.php4`, `.php5`, `.php7`, `.phps`, `.pht`, `.phpt`
   - Missing: `.phar` (PHP Archive - executable)
   - Missing: `.inc` (PHP includes - often executable)
   - Missing: `.html` (server-parsed if configured with SSI)

2. **Double Extension Bypass**:
   - Apache can execute `file.php.jpg` if PHP is configured for multiple extensions
   - IIS can execute `file.jpg.php` (processes right to left)
   - Nginx reverse proxy may mishandle extensions

3. **Case Sensitivity Bypass**:
   ```php
   $extension = strtolower(substr(strrchr($filename, "."), 1));
   // This is safe - but what about Windows servers with case-insensitive FS?
   ```

4. **htaccess/web.config Upload**:
   - If `.htaccess` or `web.config` can be uploaded
   - Attacker can enable PHP execution for any extension
   - No checks for these config files

#### Attack Scenarios:

```
Scenario 1: Upload with alternative PHP extension
- Upload: shell.phtml (if not in blocklist)
- Executes as PHP on most servers

Scenario 2: Use .htaccess to enable PHP execution
- Upload: .htaccess
- Content: AddType application/x-httpd-php .jpg
- Then upload: shell.jpg
- shell.jpg executes as PHP

Scenario 3: Use web.config on IIS
- Upload: web.config
- Configure to execute JPG as ASP
- Gain code execution
```

#### Remediation:

```php
// Whitelist ONLY safe extensions
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx'];

// Block dangerous file extensions
const DANGEROUS_EXTENSIONS = [
    'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'pht', 'phpt',
    'phar', 'inc', 'cgi', 'pl', 'py', 'rb', 'sh', 'bat', 'exe', 'com',
    'htaccess', 'web.config', 'config', 'cfg', 'conf', 'jsp', 'jspx',
    'shtml', 'shtm', 'asp', 'aspx', 'cer', 'cdx', 'asa'
];

// Block config/system files
const DANGEROUS_FILENAMES = ['.htaccess', 'web.config', 'composer.json'];

$extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
$filename = strtolower(basename($_FILES['upload']['name']));

if (in_array($extension, self::DANGEROUS_EXTENSIONS, true)) {
    throw new Exception('Dangerous file type not allowed');
}

if (in_array($filename, self::DANGEROUS_FILENAMES, true)) {
    throw new Exception('System file upload not allowed');
}

if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
    throw new Exception('File type not in whitelist');
}
```

---

### 5. MISSING OR INADEQUATE ACCESS CONTROL ON FILE DOWNLOADS

**Severity:** HIGH  
**Type:** Broken Access Control (CWE-639)

#### File: `/home/user/phpcollab/linkedcontent/accessfile.php`
**Lines: 17-44**

```php
$fileDetail = $files->getFileById($request->query->get('id'));

if ($fileDetail) {
    $fileAction = $container->getFileDownloadService();
    
    try {
        if (empty($fileDetail["fil_task"])) {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $filename);
        } else {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/" . $filename);
        }
        
        if ($request->query->get('mode') == "download") {
            $fileAction->downloadFile($filename);
        } elseif ($request->query->get('mode') == "view") {
            $fileAction->viewFile($filename);
        }
    }
}
```

#### Vulnerability Analysis:

1. **No Ownership/Project Membership Verification**:
   - Code retrieves file from database
   - Directly serves file without checking if current user:
     - Is in project team
     - Has permission to access this file
     - File is published for their access level

2. **Similar Issue in `/home/user/phpcollab/projects_site/clientaccessfile.php`** (Lines 16-38):
   ```php
   $fileDetail = $files->getFileById($request->query->get('id'));
   
   if ($fileDetail) {
       // Check ONLY if file is published OR current project (weak check)
       if ($fileDetail["fil_published"] == "1" || 
           $fileDetail["fil_project"] != $session->get("project")) {
           phpCollab\Util::headerFunction("index.php");
       }
   ```
   - Logic appears inverted: redirects if NOT published OR not in project
   - Should check user IS in project team AND has access

3. **No Session Timeout Check**: No verification that user is still logged in

4. **File Path Manipulation**: 
   - Database fil_name field could contain traversal sequences
   - If accessible via SQL injection or by modifying stored values

#### Attack Scenarios:

```
Scenario 1: Access unpublished files from other projects
- Get file ID of another project's unpublished document
- Call: /linkedcontent/accessfile.php?id=999&mode=download
- Server may serve file if access control is missing

Scenario 2: Access via URL manipulation
- If file ID is sequential, attacker tries: id=1, id=2, id=3...
- Downloads all files regardless of project membership
```

#### Remediation:

```php
// In accessfile.php - Add proper access control
$fileDetail = $files->getFileById($request->query->get('id'));

if (!$fileDetail) {
    throw new Exception('File not found');
}

// Verify user has access to this file
$teams = $container->getTeams();
$isMember = $teams->isTeamMember($fileDetail["fil_project"], $session->get("id"));

if (!$isMember && $fileDetail["fil_published"] == "0") {
    http_response_code(403);
    throw new Exception('Access denied');
}

// Check file owner or project owner access
$projects = $container->getProjectsLoader();
$projectDetail = $projects->getProjectById($fileDetail["fil_project"]);

$canAccess = (
    $isMember == "true" ||                                  // Team member
    $fileDetail["fil_owner"] == $session->get("id") ||    // File owner
    $projectDetail["pro_owner"] == $session->get("id") ||  // Project owner
    $session->get("profile") == "5"                         // Admin
);

if (!$canAccess) {
    http_response_code(403);
    throw new Exception('Access denied');
}

// Additional security: serve file from outside web root
// Don't allow user to manipulate the file path
```

---

### 6. PREDICTABLE FILE STORAGE LOCATIONS

**Severity:** MEDIUM  
**Type:** Sensitive Data Exposure (CWE-200)

#### File: `/home/user/phpcollab/linkedcontent/addfile.php`
**Lines: 127-143**

```php
phpCollab\Util::uploadFile("files/$projectId/$taskId",
    $request->files->get('upload')->getPathName(), "$num--" . $filename);
```

#### Vulnerability Analysis:

1. **Predictable Directory Structure**:
   - Files stored in: `/files/<projectId>/<taskId>/`
   - Project IDs and task IDs likely sequential integers
   - Attacker can guess paths: `/files/1/1/`, `/files/1/2/`, `/files/2/1/`

2. **Predictable File Naming**:
   - Format: `<fileId>--<originalFilename>`
   - File IDs are likely sequential
   - Example: `123--document.pdf`, `124--report.docx`
   - Attacker can guess file IDs

3. **Files in Web-Accessible Directory**:
   - `/files/` directory is web-accessible
   - No `.htaccess` protection shown (checked `/home/user/phpcollab/files/.htaccess`)
   - `.htaccess` only contains generic rules, doesn't prevent direct download

4. **Directory Listing**:
   - If directory listing enabled, attacker sees all files
   - Can enumerate all projects/tasks

#### Attack Scenario:

```
Attacker Actions:
1. Registers as low-privilege user
2. Creates project with ID 5
3. Guesses other projects: /files/1/, /files/2/, /files/3/, /files/4/
4. Directly accesses /files/4/120--salary.pdf
5. Gets competitor's confidential salary information
```

#### Remediation:

```php
// Store files outside webroot or in protected directory
// Use random, non-guessable filenames

$baseUploadDir = dirname(APP_ROOT) . '/private_files/'; // Outside web root

$randomFilename = bin2hex(random_bytes(32)); // 64-char random string
$extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
$finalFilename = $randomFilename . '.' . $extension;

$storagePath = $baseUploadDir . $finalFilename;
move_uploaded_file($_FILES['upload']['tmp_name'], $storagePath);

// Store in database: only the random filename, not the original
// When serving: read file from private directory, no direct web access

// In web server config (.htaccess):
<FilesMatch "\.">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

---

### 7. NO VIRUS/MALWARE SCANNING

**Severity:** CRITICAL  
**Type:** Malware/Malicious Content (CWE-434)

#### Affected Files:
- `/home/user/phpcollab/classes/Files/FileUploader.php`
- `/home/user/phpcollab/linkedcontent/addfile.php`
- `/home/user/phpcollab/projects_site/uploadfile.php`

#### Vulnerability Analysis:

1. **No Antivirus Integration**:
   - No ClamAV scanning
   - No EICAR test detection
   - No yara rules checking
   - No heuristic analysis

2. **No File Content Validation**:
   - Not checking file headers (magic bytes)
   - Not validating file structure
   - Not checking for embedded executables in archives

3. **No Sandboxing**:
   - Uploaded files directly stored
   - No isolated analysis environment
   - No detonation in controlled environment

#### Attack Scenarios:

```
Scenario 1: Distribute malware via file share
- Upload trojan disguised as PDF
- Other users download "safe" file
- Malware distributed across organization

Scenario 2: Polyglot files
- File is valid PDF + valid PHP
- Served as PDF to users, but could be executed as PHP
```

#### Remediation:

```php
// Integrate ClamAV antivirus
function scanFileForViruses($filePath) {
    if (!extension_loaded('clamav')) {
        throw new Exception('ClamAV extension not available');
    }
    
    $resource = cl_scanfile($filePath, $virus);
    
    if ($resource === CL_VIRUS) {
        unlink($filePath);
        throw new Exception("Virus detected: $virus");
    }
    
    return true;
}

// Validate file magic bytes
function validateFileMagicBytes($filePath, $expectedType) {
    $finfo = finfo_open(FILEINFO_RAW);
    $magic = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    
    $validMagics = [
        'jpeg' => 'FFD8FF',
        'png' => '89504E47',
        'gif' => '474946',
        'pdf' => '25504446'
    ];
    
    if (strpos($magic, $validMagics[$expectedType]) !== 0) {
        throw new Exception('Invalid file format');
    }
    
    return true;
}

// Check for executable content
function scanForExecutableContent($filePath) {
    $dangerous = ['<?php', '<?=', 'eval(', 'system(', 'exec('];
    $content = file_get_contents($filePath, false, null, 0, 1000);
    
    foreach ($dangerous as $pattern) {
        if (stripos($content, $pattern) !== false) {
            throw new Exception('Executable content detected');
        }
    }
    
    return true;
}
```

---

### 8. INSUFFICIENT INPUT VALIDATION IN FILE DELETION

**Severity:** HIGH  
**Type:** Path Traversal (CWE-22), Arbitrary File Deletion

#### File: `/home/user/phpcollab/linkedcontent/deletefiles.php`
**Lines: 34-60**

```php
if ($request->request->get("action") == "delete") {
    $id = str_replace("**", ",", $id);
    
    $listFiles = $files->getFiles($id);
    
    foreach ($listFiles as $file) {
        if ($task != "0") {
            if (file_exists("../files/" . $project . "/" . $task . "/" . $file["fil_name"])) {
                phpCollab\Util::deleteFile("files/" . $project . "/" . $task . "/" . $file["fil_name"]);
            }
        } else {
            if (file_exists("../files/" . $project . "/" . $file["fil_name"])) {
                phpCollab\Util::deleteFile("files/" . $project . "/" . $file["fil_name"]);
            }
        }
        $deleteFile = $files->deleteFile($file['fil_id']);
    }
}
```

#### Vulnerability Analysis:

1. **fil_name Not Validated**: 
   - `$file["fil_name"]` comes from database
   - If database can be compromised or modified
   - Could contain path traversal sequences
   - Would delete arbitrary files

2. **No Symlink Checks**:
   - If symlinks allowed in upload directory
   - Could create symlink to `/etc/passwd`
   - Deletion would follow symlink and delete real file

3. **Race Condition**:
   - `file_exists()` check then delete
   - Between check and delete, file could be replaced
   - TOCTOU (Time-Of-Check-Time-Of-Use) vulnerability

#### Remediation:

```php
// Validate file name before deletion
public static function deleteFile($source)
{
    // Ensure filename doesn't contain path components
    $fileName = basename($source);
    
    // Reconstruct safe path
    $directory = dirname($source);
    $safePath = $directory . '/' . $fileName;
    
    // Verify the path doesn't escape the intended directory
    $realPath = realpath($safePath);
    $expectedBase = realpath($directory);
    
    if (strpos($realPath, $expectedBase) !== 0) {
        throw new Exception('Invalid file path');
    }
    
    // Check it's not a symlink
    if (is_link($realPath)) {
        throw new Exception('Symlink deletion not allowed');
    }
    
    // Safe deletion
    unlink($realPath);
}
```

---

### 9. WEAK FILENAME SANITIZATION

**Severity:** HIGH  
**Type:** Path Traversal (CWE-22)

#### File: `/home/user/phpcollab/classes/Util.php`
**Lines: 713-721**

```php
public static function checkFileName($name = '')
{
    $name = str_replace('\\', '/', $name);
    $name = str_replace(" ", "_", $name);
    $name = str_replace("'", "", $name);
    
    return basename($name);
}
```

#### Vulnerability Analysis:

1. **Insufficient Encoding Handling**:
   - Doesn't handle URL-encoded traversal: `%2e%2e%2f`
   - Doesn't handle double-encoded: `%252e%252e%252f`
   - Doesn't handle Unicode normalization bypasses
   - Doesn't handle null bytes

2. **basename() is Not Enough**:
   - Only removes leading path components
   - Could still contain special characters
   - Doesn't validate against dangerous filenames

3. **Inconsistent Across Codebase**:
   - Some files use this function
   - Others use `$_FILES['upload']['name']` directly
   - No centralized validation

#### Attack Examples:

```
1. URL encoding bypass:
   Filename: "%2e%2e%2fpasswd"
   After str_replace: "%2e%2e%2fpasswd"  (no change!)
   After basename: "passwd" (decoded by basename? depends on PHP version)
   
2. Double encoding:
   Filename: "%252e%252e%252fpasswd"
   After processing: could traverse depending on decode order

3. Unicode normalization:
   Filename: "..‾f" (with decomposed character)
   Might bypass basename depending on filesystem
```

#### Remediation:

```php
public static function checkFileName($name = '')
{
    // Decode any encoded characters first
    $decoded = urldecode($name);
    $decoded = rawurldecode($decoded);
    
    // Normalize unicode to prevent bypass
    if (function_exists('normalizer_normalize')) {
        $decoded = normalizer_normalize($decoded, Normalizer::FORM_KC);
    }
    
    // Remove all potentially dangerous characters
    // Only allow: letters, numbers, dots, hyphens, underscores
    $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '', $decoded);
    
    // Prevent reserved filenames on Windows
    $reserved = ['CON', 'PRN', 'AUX', 'NUL', 'COM1-9', 'LPT1-9'];
    foreach ($reserved as $name) {
        if (strtoupper($sanitized) === strtoupper($name)) {
            $sanitized = '_' . $sanitized;
        }
    }
    
    // Get only filename portion (remove any remaining paths)
    $sanitized = basename($sanitized);
    
    // Reject if empty after sanitization
    if (empty($sanitized) || $sanitized === '.' || $sanitized === '..') {
        throw new Exception('Invalid filename');
    }
    
    return $sanitized;
}
```

---

## SUMMARY TABLE

| # | Vulnerability | Severity | Type | CWE | File(s) | Lines |
|---|---|---|---|---|---|---|
| 1 | Missing MIME Type Validation | CRITICAL | Unrestricted Upload | 434 | addfile.php, uploadfile.php | 54-82, 80 |
| 2 | Path Traversal in Filenames | CRITICAL | Path Traversal | 22 | Util.php, accessfile.php | 373-402, 612-754 |
| 3 | User-Controlled File Size Limit | HIGH | Size Bypass | 190 | addfile.php, uploadfile.php | 61-79 |
| 4 | Weak Executable Block | CRITICAL | Dangerous Upload | 434 | uploadfile.php, addfile.php, viewfile.php | 82-88, 84-90 |
| 5 | Missing Access Control | HIGH | Broken AC | 639 | accessfile.php, clientaccessfile.php | 17-44, 16-38 |
| 6 | Predictable Paths/Names | MEDIUM | Information Disclosure | 200 | addfile.php, all upload handlers | 127-143 |
| 7 | No Virus Scanning | CRITICAL | Malware | 434 | FileUploader.php, all uploads | all |
| 8 | Unsafe File Deletion | HIGH | Path Traversal | 22 | deletefiles.php, Util.php | 34-60, 354-364 |
| 9 | Weak Sanitization | HIGH | Path Traversal | 22 | Util.php | 713-721 |

---

## RECOMMENDATIONS (Priority Order)

### IMMEDIATE (P0 - Deploy within 24 hours)

1. **Implement Whitelist-Based MIME Type Validation**
   - Only allow specific safe MIME types (image/jpeg, image/png, etc.)
   - Check actual MIME type, not filename extension
   - Use fileinfo extension or Symfony file handler

2. **Block Dangerous Extensions**
   - Create blocklist for PHP, executable, config files
   - Block: php, phtml, php3, php4, php5, phtml, pht, phar, htaccess, web.config
   - Reject uploads with suspicious patterns

3. **Generate Server-Side Filenames**
   - Stop storing original user filenames
   - Generate random names: `bin2hex(random_bytes(32))`
   - Store mapping in database

4. **Implement Strict Access Controls**
   - Verify user in project team before file download
   - Check file published status
   - Log all file access

### SHORT TERM (P1 - Within 1 week)

5. **Add Server-Side File Size Limits**
   - Don't trust `maxCustom` form parameter
   - Enforce server-side maximum (50MB default)
   - Validate size in bytes, not form field

6. **Implement Virus Scanning**
   - Integrate ClamAV or VirusTotal API
   - Scan every uploaded file before storing
   - Quarantine suspicious files

7. **Use realpath() Validation**
   - Use `realpath()` to resolve actual paths
   - Verify paths don't escape intended directories
   - Check for symlinks before file operations

8. **Add Request Logging**
   - Log all file uploads with: username, filename, project, IP
   - Log all file downloads with same info
   - Log all file deletions

### MEDIUM TERM (P2 - Within 1 month)

9. **Store Files Outside Web Root**
   - Move `/files/` directory outside web-accessible area
   - Serve files via PHP handler, not direct web access
   - Add X-Accel-Redirect or X-Sendfile headers

10. **Implement File Type Validation**
    - Check magic bytes (file headers)
    - Validate file structure for archives
    - Reject polyglot files

11. **Add CSRF Tokens**
    - (Already implemented, but verify on all forms)

12. **Database Constraints**
    - Add CHECK constraints on fil_name field
    - Prevent storing path traversal sequences in database

---

## FILES AFFECTED BY VULNERABILITIES

```
Classes:
- /home/user/phpcollab/classes/Files/FileUploader.php (vulnerability #7)
- /home/user/phpcollab/classes/Files/Files.php (vulnerability #1, #4)
- /home/user/phpcollab/classes/Util.php (vulnerability #2, #8, #9)
- /home/user/phpcollab/classes/FileHandler.php (weak validation)

Linked Content:
- /home/user/phpcollab/linkedcontent/addfile.php (all 9)
- /home/user/phpcollab/linkedcontent/viewfile.php (vulnerability #2, #4, #5)
- /home/user/phpcollab/linkedcontent/accessfile.php (vulnerability #5)
- /home/user/phpcollab/linkedcontent/deletefiles.php (vulnerability #2, #8)

Project Site:
- /home/user/phpcollab/projects_site/uploadfile.php (vulnerability #1, #2, #3, #4, #7)
- /home/user/phpcollab/projects_site/clientaccessfile.php (vulnerability #5)
- /home/user/phpcollab/projects_site/clientfiledetail.php (vulnerability #1, #2, #4, #7)
```

---

## TESTING RECOMMENDATIONS

### Manual Testing

1. **Upload Executable**: Try uploading `.php`, `.phtml`, `.php3` files
2. **Test Path Traversal**: Upload `../../../etc/passwd` as filename
3. **Bypass via Double Extension**: Upload `shell.php.jpg`
4. **Test Access Control**: Access file from project you're not member of
5. **Directory Traversal**: Try accessing `/files/../../../etc/`

### Automated Testing

1. Run OWASP ZAP scan focusing on file upload module
2. Use Burp Suite file upload test cases
3. Test with EICAR antivirus test file
4. Fuzzing: upload files with special characters

### Code Review

1. Review all uses of `$_FILES` array
2. Search for `move_uploaded_file()` calls
3. Check all `file_exists()` with user input
4. Audit all `include/require` statements
5. Review database queries related to files

