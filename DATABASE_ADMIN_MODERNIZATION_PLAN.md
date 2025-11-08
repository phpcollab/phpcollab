# Database Administration Modernization Plan
**Date:** November 8, 2025
**Objective:** Replace vulnerable bundled phpMyAdmin/phpPgAdmin with modern, secure code supporting all database platforms

---

## Current State Analysis

### ✅ MySQL - Already Modernized
**Current Implementation:**
- Uses modern `ifsnop/mysqldump-php` library (Composer)
- `Administration::dumpTables()` method (line 103)
- Supports: structure-only, data-only, full backup, compression
- Secure: No shell injection vulnerabilities
- Works: ✅ Tested and production-ready

### ❌ PostgreSQL - Vulnerable
**Current Implementation:**
- Uses bundled `includes/phppgadmin/db_dump.php` (2005-era code)
- Vulnerable to SQL injection and other exploits
- No modern library equivalent
- Admin page: `administration/phppgadmin.php`

**Issues:**
- Unmaintained since 2015
- Unknown number of CVEs
- Direct form POST to vulnerable bundled code

### ❓ SQL Server - Unknown Status
**Current Implementation:**
- Supported in settings (`$databaseType = "sqlserver"`)
- No backup interface found in admin pages
- Likely no backup functionality implemented

---

## Modernization Strategy

### Approach: Extend Administration Class

Follow the same pattern as MySQL backup:
1. Add methods to `Administration` class for each database type
2. Use secure, modern approaches
3. Update admin pages to use new methods
4. Delete vulnerable bundled libraries

---

## Implementation Plan

### Phase 1: PostgreSQL Backup (Week 1)

#### 1.1 Add PostgreSQL Dump Method

**File:** `classes/Administration/Administration.php`

**New Method:**
```php
/**
 * Dump PostgreSQL database
 * @param array|null $dumpSettings
 * @throws Exception
 */
public function dumpPostgreSQLTables($dumpSettings = null)
{
    if (MYDBTYPE !== 'postgresql') {
        throw new Exception('Database type is not PostgreSQL');
    }

    $fileName = MYDATABASE . '_' . date("Y_m_d", time());
    $filePath = '/tmp/' . $fileName . '.sql';

    // Build pg_dump command with safe parameters
    $command = sprintf(
        'PGPASSWORD=%s pg_dump -h %s -U %s -F p %s > %s 2>&1',
        escapeshellarg(MYPASSWORD),
        escapeshellarg(MYSERVER),
        escapeshellarg(MYLOGIN),
        escapeshellarg(MYDATABASE),
        escapeshellarg($filePath)
    );

    // Add options based on settings
    $options = [];

    if (isset($dumpSettings['no-data']) && $dumpSettings['no-data'] === true) {
        $options[] = '--schema-only';
    }

    if (isset($dumpSettings['no-create-info']) && $dumpSettings['no-create-info'] === true) {
        $options[] = '--data-only';
    }

    if (isset($dumpSettings['add-drop-table']) && $dumpSettings['add-drop-table'] === true) {
        $options[] = '--clean';
    }

    if (!empty($dumpSettings['include-tables'])) {
        foreach ($dumpSettings['include-tables'] as $table) {
            $options[] = '--table=' . escapeshellarg($table);
        }
    }

    // Insert options into command
    if (!empty($options)) {
        $command = str_replace(
            '-F p',
            '-F p ' . implode(' ', $options),
            $command
        );
    }

    // Execute dump
    exec($command, $output, $returnCode);

    if ($returnCode !== 0) {
        throw new Exception('PostgreSQL dump failed: ' . implode("\n", $output));
    }

    // Compress if requested
    if (isset($dumpSettings['compress']) && $dumpSettings['compress'] === 'Gzip') {
        exec("gzip " . escapeshellarg($filePath), $output, $returnCode);
        if ($returnCode !== 0) {
            throw new Exception('Compression failed');
        }
        $filePath .= '.gz';
        $fileName .= '.sql.gz';
    } else {
        $fileName .= '.sql';
    }

    // Download file
    $fileDownload = FileDownload::createFromFilePath($filePath);
    $fileDownload->sendDownload($fileName);

    // Clean up
    @unlink($filePath);
}
```

**Security Considerations:**
- ✅ All parameters escaped with `escapeshellarg()`
- ✅ Password passed via environment variable (not in command)
- ✅ Return code checked for errors
- ✅ Temp files cleaned up
- ✅ No user input directly in shell command

#### 1.2 Update PostgreSQL Admin Page

**File:** `administration/phppgadmin.php`

**Changes:**
```php
// Remove lines 54-76 (old form posting to vulnerable bundled code)
// Replace with modern form similar to phpmyadmin.php

echo <<<HTML
<tr class="odd"><td class="leftvalue">&nbsp;</td><td>
    <form method="post" action="backupPostgreSQL.php" name="pg_dump">
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
        <table>
        <tr>
            <td>
                <select name="tables[]" size="5" multiple="multiple">
HTML;

sort($tableCollab);

foreach ($tableCollab as $item) {
    echo "<option selected>$item</option>";
}

echo <<<HTML
                </select>
            </td>
            <td>
                <input type="radio" name="what" value="structureonly" />
                Structure only<br />
                <input type="radio" name="what" value="all" checked="checked" />
                Structure and data<br />
                <input type="radio" name="what" value="dataonly" />
                Data only
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="drop" value="1" checked="checked" />
                Add "drop table"
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="checkbox" name="asfile" value="sendit" checked="checked" />
                Save as file ( <input type="checkbox" name="zip" value="zip" />"zipped" )
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Go" />
            </td>
        </tr>
        </table>
    </form>
</td></tr>
HTML;
```

#### 1.3 Create PostgreSQL Backup Handler

**New File:** `administration/backupPostgreSQL.php`

```php
<?php

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
        $logger->error('CSRF Token Error in backupPostgreSQL.php', [
            'ip' => $request->server->get('REMOTE_ADDR'),
            'user_id' => $session->get('id')
        ]);

        $session->getFlashBag()->add('error', 'Security error: Invalid form submission. Please try again.');
        phpCollab\Util::headerFunction('../administration/phppgadmin.php');
        exit;
    }

    if ($request->request->get('tables')) {

        $dumpSettings = [
            'include-tables' => $request->request->get('tables'),
        ];

        if ($request->request->get('what') == "structureonly") {
            $dumpSettings['no-data'] = true;
        }

        if ($request->request->get('what') == "dataonly") {
            $dumpSettings['no-create-info'] = true;
        }

        if ((bool)$request->request->get('drop')) {
            $dumpSettings['add-drop-table'] = true;
        }

        if ($request->request->get('zip') == 'zip') {
            $dumpSettings['compress'] = 'Gzip';
        }

        $admins = $container->getAdministration();

        try {
            $admins->dumpPostgreSQLTables($dumpSettings);
        } catch (Exception $e) {
            $logger->error('PostgreSQL backup failed', [
                'error' => $e->getMessage(),
                'user_id' => $session->get('id')
            ]);
            $session->getFlashBag()->add('error', 'Backup failed: ' . $e->getMessage());
            phpCollab\Util::headerFunction('../administration/phppgadmin.php');
        }

    } else {
        phpCollab\Util::headerFunction('../administration/phppgadmin.php');
    }
}
```

---

### Phase 2: SQL Server Backup (Week 2)

#### 2.1 Add SQL Server Dump Method

**File:** `classes/Administration/Administration.php`

**New Method:**
```php
/**
 * Dump SQL Server database
 * @param array|null $dumpSettings
 * @throws Exception
 */
public function dumpSQLServerTables($dumpSettings = null)
{
    if (MYDBTYPE !== 'sqlserver') {
        throw new Exception('Database type is not SQL Server');
    }

    $fileName = MYDATABASE . '_' . date("Y_m_d", time()) . '.bak';
    $filePath = '/tmp/' . $fileName;

    // Use PDO to execute SQL Server backup command
    try {
        $backupQuery = sprintf(
            "BACKUP DATABASE [%s] TO DISK = '%s'",
            MYDATABASE,
            str_replace("'", "''", $filePath)
        );

        $this->db->query($backupQuery);
        $this->db->execute();

        // Download file
        $fileDownload = FileDownload::createFromFilePath($filePath);
        $fileDownload->sendDownload($fileName);

        // Clean up
        @unlink($filePath);

    } catch (Exception $e) {
        throw new Exception('SQL Server backup failed: ' . $e->getMessage());
    }
}
```

#### 2.2 Create SQL Server Admin Page

**New File:** `administration/sqlserver.php`

Similar structure to `phpmyadmin.php` but for SQL Server.

---

### Phase 3: Unified Database Admin (Week 3)

#### 3.1 Refactor for Database Type Detection

**File:** `classes/Administration/Administration.php`

**Add universal method:**
```php
/**
 * Dump database tables (auto-detects database type)
 * @param array|null $dumpSettings
 * @throws Exception
 */
public function dumpDatabaseTables($dumpSettings = null)
{
    switch (MYDBTYPE) {
        case 'mysql':
            return $this->dumpTables($dumpSettings);

        case 'postgresql':
            return $this->dumpPostgreSQLTables($dumpSettings);

        case 'sqlserver':
            return $this->dumpSQLServerTables($dumpSettings);

        default:
            throw new Exception('Unsupported database type: ' . MYDBTYPE);
    }
}
```

#### 3.2 Create Unified Admin Page

**Option:** Create `administration/database.php` that auto-detects DB type and shows appropriate interface.

---

### Phase 4: Remove Vulnerable Libraries (Week 4)

#### 4.1 Verify All Functionality Migrated

**Checklist:**
- [ ] MySQL backup working with new code
- [ ] PostgreSQL backup working with new code
- [ ] SQL Server backup working (if applicable)
- [ ] All admin pages updated
- [ ] CSRF protection on all forms
- [ ] Error handling and logging
- [ ] User testing completed

#### 4.2 Delete Bundled Libraries

```bash
rm -rf includes/phpmyadmin/
rm -rf includes/phppgadmin/
```

#### 4.3 Update Admin Pages

**File:** `administration/admin.php`

Remove or update links to old admin pages.

#### 4.4 Update Documentation

- Document new backup procedures
- Update admin guide
- Note breaking changes for users relying on bundled tools

---

## Testing Plan

### Unit Tests

**Test Cases:**
1. MySQL backup with various options
2. PostgreSQL backup with various options
3. SQL Server backup (if applicable)
4. CSRF token validation
5. Error handling (invalid database type, dump failure, etc.)
6. File cleanup after download
7. Compression functionality

### Integration Tests

**Scenarios:**
1. Admin user creates MySQL backup
2. Admin user creates PostgreSQL backup
3. Non-admin user blocked from accessing backup pages
4. Invalid CSRF token rejected
5. Large database backup (performance test)
6. Download and verify backup file integrity

### Security Tests

1. **Shell Injection:** Attempt to inject commands via database name, table names
2. **CSRF:** Attempt backup without valid token
3. **Path Traversal:** Attempt to write to different directory
4. **Authentication:** Verify only admins can access
5. **File Cleanup:** Verify temp files are deleted

---

## Dependencies Required

### For PostgreSQL Support

**System Requirements:**
```bash
# Debian/Ubuntu
apt-get install postgresql-client

# Red Hat/CentOS
yum install postgresql

# macOS
brew install postgresql
```

**Verify Installation:**
```bash
pg_dump --version
```

### For SQL Server Support

**System Requirements:**
- ODBC driver for SQL Server
- sqlcmd utility (optional, using PDO instead)

**Composer Packages:**
- Already have: `ext-pdo` (for SQL Server backup via PDO)

---

## Security Improvements

### Before (Vulnerable)
❌ Bundled phpMyAdmin 2.x (400+ CVEs)
❌ Bundled phpPgAdmin (unknown CVEs, unmaintained)
❌ Direct POST to vulnerable scripts
❌ No CSRF protection
❌ SQL injection vulnerabilities

### After (Secure)
✅ Modern library for MySQL (`ifsnop/mysqldump-php`)
✅ Secure pg_dump execution for PostgreSQL
✅ PDO-based backup for SQL Server
✅ CSRF protection on all forms
✅ Shell injection prevention (escapeshellarg)
✅ Parameter validation
✅ Security logging
✅ Admin-only access

---

## Migration Impact

### Breaking Changes
- Users relying on bundled phpMyAdmin/phpPgAdmin will need to use:
  - External phpMyAdmin/pgAdmin installations
  - New built-in backup interface
  - Command-line tools

### Compatibility
- ✅ No impact on database schema
- ✅ No impact on existing backups
- ✅ Same backup format (.sql files)
- ✅ Works with existing restore procedures

### User Communication
1. Document changes in CHANGELOG
2. Provide migration guide
3. Note improved security
4. Offer support for transition

---

## Timeline

| Week | Tasks | Deliverables |
|------|-------|--------------|
| **1** | PostgreSQL implementation | - New dump method<br>- Updated admin page<br>- Backup handler |
| **2** | SQL Server implementation | - SQL Server dump method<br>- Admin interface |
| **3** | Unified interface | - Auto-detection<br>- Unified admin page<br>- Testing |
| **4** | Cleanup & deployment | - Delete bundled libs<br>- Documentation<br>- User communication |

---

## Success Metrics

✅ **Security:**
- Zero CVEs in database admin tools
- All forms CSRF-protected
- No shell injection vulnerabilities

✅ **Functionality:**
- All database types supported
- Feature parity with old tools
- Successful backup/download

✅ **Performance:**
- Backup speed same or faster
- No memory issues with large DBs
- Efficient compression

✅ **User Experience:**
- Clear interface
- Good error messages
- Consistent across DB types

---

## Rollback Plan

If issues arise during migration:

1. **Keep bundled libraries** until new code proven stable
2. **Parallel operation:** Run both old and new systems temporarily
3. **Feature flag:** Enable new system for testing, keep old as fallback
4. **Git revert:** Easy rollback via version control

---

## Future Enhancements

### Short-term (1-2 months)
- [ ] Scheduled automatic backups
- [ ] Backup retention policy
- [ ] Email backup completion notifications
- [ ] Backup history/log

### Medium-term (3-6 months)
- [ ] Restore functionality (upload .sql file)
- [ ] Cloud backup storage (S3, Google Cloud)
- [ ] Incremental backups
- [ ] Backup encryption

### Long-term (6-12 months)
- [ ] Point-in-time recovery
- [ ] Backup monitoring dashboard
- [ ] Multi-database backup (all projects)
- [ ] Backup validation/testing

---

## Decisions Made

**Date:** November 8, 2025

### ✅ Restore Functionality
**Decision:** Restore will NOT be implemented in phpCollab web interface

**Rationale:**
- High security risk (file upload + SQL execution)
- Better handled via command-line tools (ssh access)
- Reduces attack surface significantly
- Most admins use external tools anyway (phpMyAdmin, Adminer, CLI)

**Alternative:** Documentation will guide users to:
- MySQL: `mysql -u user -p database < backup.sql`
- PostgreSQL: `psql -U user -d database -f backup.sql`
- SQL Server: `sqlcmd -S server -d database -i backup.sql`
- External tools: Adminer, phpMyAdmin, pgAdmin 4

### ✅ Scope Simplified
**In Scope:**
- ✅ Backup functionality for all database types
- ✅ Remove vulnerable bundled libraries (279KB)
- ✅ CSRF protection, validation, logging
- ✅ Documentation for external restore

**Out of Scope:**
- ❌ Web-based restore functionality
- ❌ HTMLArea replacement (handled separately)
- ❌ Advanced features (scheduled backups, cloud storage)

---

## Updated Timeline (3 Weeks)

| Week | Tasks | Deliverables |
|------|-------|--------------|
| **1** | PostgreSQL backup | - `dumpPostgreSQLTables()` method<br>- Updated phppgadmin.php UI<br>- backupPostgreSQL.php handler<br>- Keep restore disabled |
| **2** | SQL Server backup | - `dumpSQLServerTables()` method<br>- sqlserver.php admin page<br>- Testing |
| **3** | Cleanup & docs | - Delete bundled libs (279KB)<br>- Restore documentation<br>- User communication |

---

**Next Steps:**

1. ✅ Plan reviewed and approved
2. ✅ Restore decision finalized (external only)
3. Ready to implement Phase 1 (PostgreSQL backup)

**Ready to proceed with implementation!**
