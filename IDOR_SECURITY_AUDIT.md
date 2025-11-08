# IDOR (Insecure Direct Object Reference) Vulnerability Report for phpCollab

## Executive Summary
A comprehensive security analysis of the phpCollab codebase has identified **CRITICAL and HIGH severity IDOR vulnerabilities** across multiple modules. These vulnerabilities allow authenticated users to bypass authorization checks and access, modify, or delete objects belonging to other users by directly manipulating object IDs in URL parameters.

## Vulnerability Categories Identified:
1. File Download/View Operations (Critical)
2. File Publishing/Unpublishing Operations (Critical)
3. File Deletion Operations (Critical)
4. Task Operations (High)
5. Bookmark Edit/Update Operations (High)
6. Notes Publishing Operations (Critical)
7. Project Content Publishing Operations (Critical)

---

## CRITICAL SEVERITY VULNERABILITIES

### 1. IDOR in File Download/View - Arbitrary File Access
**File**: `linkedcontent/accessfile.php`
**Lines**: 17, 36-40
**Severity**: CRITICAL

**Vulnerable Code**:
```php
// Line 17 - NO AUTHORIZATION CHECK
$fileDetail = $files->getFileById($request->query->get('id'));

if ($fileDetail) {
    // ...
    if ($request->query->get('mode') == "download") {
        $fileAction->downloadFile($filename);  // Line 37 - Downloads without auth
    } elseif ($request->query->get('mode') == "view") {
        $fileAction->viewFile($filename);      // Line 39 - Views without auth
    }
}
```

**IDOR Attack Scenario**:
An authenticated user (User A) can download or view ANY file in the system by incrementing the file ID parameter:
```
../linkedcontent/accessfile.php?id=1&mode=download
../linkedcontent/accessfile.php?id=2&mode=view
../linkedcontent/accessfile.php?id=100&mode=download
```

Even if User A is not a member of the project that owns the file, they can access it.

**Impact**:
- Unauthorized access to confidential project files
- Disclosure of sensitive information (contracts, source code, designs)
- No audit trail of who accessed what files

**Remediation**:
```php
$fileDetail = $files->getFileById($request->query->get('id'));

// ADD AUTHORIZATION CHECK
if (!$fileDetail) {
    phpCollab\Util::headerFunction("../general/permissiondenied.php");
}

// Check if user is a team member of the project that owns the file
$teams = $container->getTeams();
$teamMember = $teams->isTeamMember($fileDetail["fil_project"], $session->get("id"));

if (!$teamMember && $session->get("profile") != "5") { // 5 = admin
    phpCollab\Util::headerFunction("../general/permissiondenied.php");
}

if ($fileDetail) {
    // ... rest of code
}
```

---

### 2. IDOR in File Publish/Unpublish to Project Site
**File**: `linkedcontent/viewfile.php`
**Lines**: 29-42 (publish/unpublish operations)
**Severity**: CRITICAL

**Vulnerable Code**:
```php
if ($action == "publish") {
    $file = $request->query->get("file");
    if ($addToSiteFile == "true") {
        $files->publishFileByIdOrVcParent($file);  // Line 32 - NO AUTH CHECK
        $msg = "addToSite";
        $id = $file;
    }

    if ($removeToSiteFile == "true") {
        $files->unPublishFileByIdOrVcParent($file);  // Line 38 - NO AUTH CHECK
        $msg = "removeToSite";
        $id = $file;
    }
}

// Line 44 - File fetched AFTER publish/unpublish operations
$fileDetail = $files->getFileById($id);
// Line 49 - Team member check happens AFTER the operations
$teamMember = $teams->isTeamMember($fileDetail["fil_project"], $session->get("id"));
```

**IDOR Attack Scenario**:
An authenticated user can publish ANY file to the project site without being the file owner:
```
../linkedcontent/viewfile.php?action=publish&addToSiteFile=true&file=1
../linkedcontent/viewfile.php?action=publish&removeToSiteFile=true&file=5
```

**Impact**:
- Unauthorized publication of confidential information
- Potential defacement of project sites
- Data exposure

---

### 3. IDOR in File Deletion
**File**: `linkedcontent/deletefiles.php`
**Lines**: 34-49
**Severity**: CRITICAL

**Vulnerable Code**:
```php
if ($request->request->get("action") == "delete") {
    $id = str_replace("**", ",", $id);
    
    // NO AUTHORIZATION CHECK - Just gets and deletes
    $listFiles = $files->getFiles($id);
    
    foreach ($listFiles as $file) {
        // Delete file from filesystem
        if ($task != "0") {
            phpCollab\Util::deleteFile("files/" . $project . "/" . $task . "/" . $file["fil_name"]);
        } else {
            phpCollab\Util::deleteFile("files/" . $project . "/" . $file["fil_name"]);
        }
        // Delete from database
        $deleteFile = $files->deleteFile($file['fil_id']);
    }
}
```

**IDOR Attack Scenario**:
An authenticated user can delete ANY file by knowing its ID:
```
../linkedcontent/deletefiles.php?project=1&id=1&sendto=filedetails [POST] action=delete
../linkedcontent/deletefiles.php?project=1&id=2,3,4,5&sendto=filedetails [POST] action=delete
```

**Impact**:
- Destruction of critical project files
- Data loss across projects
- Disruption of project operations

---

### 4. IDOR in Project-Wide Publish/Unpublish Operations
**File**: `projects/viewproject.php`
**Lines**: 30-178
**Severity**: CRITICAL

**Vulnerable Code**:
```php
if ($action == "publish") {
    try {
        $closeTopic = $request->query->get("closeTopic");
        if ($closeTopic == "true") {
            $topics->closeTopic($id);  // Line 45 - NO AUTH CHECK
        }

        $addToSiteTask = $request->query->get("addToSiteTask");
        if ($addToSiteTask == "true") {
            $tasks->publishTasks($id);  // Line 60 - NO AUTH CHECK
        }

        $removeToSiteTask = $request->query->get("removeToSiteTask");
        if ($removeToSiteTask == "true") {
            $tasks->unPublishTasks($id);  // Line 74 - NO AUTH CHECK
        }

        $addToSiteTopic = $request->query->get("addToSiteTopic");
        if ($addToSiteTopic == "true") {
            $topics->publishTopic($id);  // Line 88 - NO AUTH CHECK
        }

        $removeToSiteTopic = $request->query->get("removeToSiteTopic");
        if ($removeToSiteTopic == "true") {
            $topics->unPublishTopic($id);  // Line 100 - NO AUTH CHECK
        }

        $addToSiteTeam = $request->query->get("addToSiteTeam");
        if ($addToSiteTeam == "true") {
            $teams->publishToSite($project, $id);  // Line 114 - NO AUTH CHECK
        }

        $removeToSiteTeam = $request->query->get("removeToSiteTeam");
        if ($removeToSiteTeam == "true") {
            $teams->unPublishToSite($project, $id);  // Line 127 - NO AUTH CHECK
        }

        $addToSiteFile = $request->query->get("addToSiteFile");
        if ($addToSiteFile == "true") {
            $files->publishFileByIdOrVcParent($id);  // Line 136 - NO AUTH CHECK
        }

        $removeToSiteFile = $request->query->get("removeToSiteFile");
        if ($removeToSiteFile == "true") {
            $files->unPublishFileByIdOrVcParent($id);  // Line 145 - NO AUTH CHECK
        }

        $addToSiteNote = $request->query->get("addToSiteNote");
        if ($addToSiteNote == "true") {
            $notes->publishToSite($id);  // Line 158 - NO AUTH CHECK
        }

        $removeToSiteNote = $request->query->get("removeToSiteNote");
        if ($removeToSiteNote == "true") {
            $notes->unPublishFromSite($id);  // Line 171 - NO AUTH CHECK
        }
    } catch (Exception $e) {
        // Generic error handling - no clear authorization denial
    }
}
```

**IDOR Attack Scenario**:
An authenticated user can manipulate ANY project's content publishing status without being a team member.

**Impact**:
- Bulk unauthorized publication of confidential information
- Defacement of project sites
- Breach of information governance policies

---

### 5. IDOR in Notes Publish/Unpublish
**File**: `notes/viewnote.php`
**Lines**: 51-60
**Severity**: CRITICAL

**Vulnerable Code**:
```php
if ($action == "publish") {
    if ($addToSite == "true") {
        $notes->publishToSite($id);  // Line 53 - NO AUTH CHECK
        $msg = "addToSite";
    }
    if ($removeToSite == "true") {
        $notes->unPublishFromSite($id);  // Line 57 - NO AUTH CHECK
        $msg = "removeToSite";
    }
}

// Authorization check happens AFTER operations (lines 68 onwards)
$noteDetail = $notes->getNoteById($id);
$teamMember = $teams->isTeamMember($noteDetail["note_project"], $session->get("id"));
```

**Impact**:
- Unauthorized publication of private notes
- Information disclosure

---

## HIGH SEVERITY VULNERABILITIES

### 6. IDOR in Task Deletion
**File**: `tasks/deletetasks.php`
**Lines**: 30-42
**Severity**: HIGH

**Vulnerable Code**:
```php
if ($request->request->get('action') == "delete") {
    $id = str_replace("**", ",", $id);
    
    $listTasks = $tasks->getTasksById($id);  // NO AUTH CHECK
    
    foreach ($listTasks as $listTask) {
        if ($fileManagement == "true") {
            phpCollab\Util::deleteDirectory("../files/" . $listTask["tas_project"] . "/" . $listTask["tas_id"]);
        }
    }
    $tasks->deleteTasks($id);  // Line 40 - DELETE WITHOUT AUTH
    $assignments->deleteAssignments($id);
    $tasks->deleteSubTasks($id);
}
```

**IDOR Attack Scenario**:
```
../tasks/deletetasks.php?project=1 [POST] id=1&action=delete
../tasks/deletetasks.php?project=1 [POST] id=1,2,3,4,5&action=delete
```

Any authenticated user can delete ANY task without being a team member.

**Impact**:
- Data destruction
- Project disruption
- Loss of task history

---

### 7. IDOR in Bookmark Edit/Update
**File**: `bookmarks/editbookmark.php`
**Lines**: 76-93
**Severity**: HIGH

**Vulnerable Code**:
```php
if ($request->query->get("id")) {
    $bookmark = new Bookmark($session->get('id'), $request->request->get('name'), $request->request->get('url'));
    $bookmark->setId($request->query->get("id"));  // Line 77 - SET ID FROM REQUEST
    
    // ... other setters ...
    
    $bookmarkService->update($bookmark);  // Line 93 - NO OWNERSHIP CHECK IN UPDATE
}
```

The update() method in `classes/Bookmarks/Bookmarks.php` (lines 119-154) has NO authorization check:
```php
public function update(Bookmark $bookmark)
{
    // NO AUTHORIZATION CHECK
    if (empty($bookmark->get('id'))) {
        return $this->bookmarks_gateway->addBookmark(...);
    }
    
    // UPDATE ANY BOOKMARK WITH MATCHING ID
    return $this->bookmarks_gateway->updateBookmark(
        $bookmark->get('id'),  // User-controlled ID
        // ...
    );
}
```

**IDOR Attack Scenario**:
```
../bookmarks/editbookmark.php?id=1&action=update [POST]
../bookmarks/editbookmark.php?id=5&action=update [POST]
../bookmarks/editbookmark.php?id=100&action=update [POST]
```

Any authenticated user can edit ANY bookmark, even those owned by other users.

**Impact**:
- Modification of other users' bookmarks
- Defacement of user content
- Data tampering

---

### 8. Suboptimal Authorization in Note Edit
**File**: `notes/editnote.php`
**Lines**: 48-55
**Severity**: MEDIUM

While there IS an ownership check, the code fetches the note BEFORE authorization:
```php
if ($id != "" && $action != "add") {
    $noteDetail = $notes->getNoteById($id);  // Line 49 - Fetch without auth
    $project = $noteDetail["note_project"];
    
    // Owner check is done BUT AFTER fetching
    if ($noteDetail["note_owner"] != $session->get("id")) {
        phpCollab\Util::headerFunction("../notes/listnotes.php?project=" . $project . "&msg=noteOwner");
    }
}
```

**Issue**: Fetching before authorization can leak information if an error occurs.

---

## MEDIUM SEVERITY VULNERABILITIES

### 9. Sequential ID Exposure - Predictable Object Identifiers
**Affected Throughout**: All modules use sequential numeric IDs

**Vulnerability**:
- Tasks: IDs are sequential (1, 2, 3, 4, ...)
- Files: IDs are sequential
- Notes: IDs are sequential
- Bookmarks: IDs are sequential
- Users: IDs are sequential

This allows attackers to:
- Enumerate all objects in the system
- Perform mass IDOR attacks by iterating through IDs
- Identify active projects, users, files without explicit access

---

## ROOT CAUSE ANALYSIS

The primary root causes of these IDOR vulnerabilities are:

1. **Missing Authorization Checks in Entry Points**: User-facing endpoints directly call gateway methods without verifying ownership or project membership
2. **No Authorization in Gateway Layer**: Gateway classes perform data access without any permission verification
3. **Authorization Checks Applied After Data Fetching**: When checks exist, they often occur too late (after sensitive data has been retrieved)
4. **Inconsistent Authorization Patterns**: Different modules implement authorization differently or not at all
5. **Sequential IDs Enable Enumeration**: Predictable IDs make IDOR attacks trivial

---

## RECOMMENDATIONS FOR REMEDIATION

### Priority 1 - CRITICAL (Fix Immediately):
1. Implement authorization checks BEFORE any data access in:
   - `linkedcontent/accessfile.php` (lines 17)
   - `linkedcontent/deletefiles.php` (lines 34-49)
   - `projects/viewproject.php` (lines 30-178)
   - `notes/viewnote.php` (lines 51-60)

2. Add ownership/team membership verification to:
   - All file download/view operations
   - All file publish/unpublish operations
   - All project-wide content management operations

### Priority 2 - HIGH (Fix Soon):
1. Add authorization checks to:
   - `tasks/deletetasks.php` (lines 30-42)
   - `bookmarks/editbookmark.php` and `classes/Bookmarks/Bookmarks.php` update methods

2. Implement proper checks in Gateway classes:
   - Add owner/project membership validation before DELETE operations
   - Add owner/project membership validation before UPDATE operations

### Priority 3 - MEDIUM (Long-term):
1. Replace sequential IDs with UUIDs
2. Implement centralized authorization middleware
3. Add comprehensive audit logging for all data access
4. Implement API-level authorization checks

### General Best Practices Pattern:
```php
// 1. Fetch data
$data = $service->getById($id);

// 2. Check if exists
if (!$data) {
    throw new NotFoundException();
}

// 3. Check authorization BEFORE operations
if (!isUserAuthorized($session->get("id"), $data)) {
    throw new AccessDeniedException();
}

// 4. Perform operation
$service->update($data);
```

---

## Testing Recommendations

### Test Case 1: File Download IDOR
```
1. Login as User A
2. Note a file ID that belongs to another project/user
3. Visit: ../linkedcontent/accessfile.php?id=X&mode=download
4. Verify file downloads successfully (confirms vulnerability)
```

### Test Case 2: File Deletion IDOR
```
1. Login as User A
2. POST to: ../linkedcontent/deletefiles.php
   - project=1&task=0&id=5&sendto=filedetails
   - action=delete
3. Verify file is deleted (confirms vulnerability)
```

### Test Case 3: Task Deletion IDOR
```
1. Login as User A (not project member)
2. POST to: ../tasks/deletetasks.php
   - id=1,2,3
   - action=delete
3. Verify tasks are deleted (confirms vulnerability)
```

---

## Impact Summary

| Vulnerability | Severity | Access Impact | Data Impact |
|---|---|---|---|
| File Download/View IDOR | CRITICAL | Any authenticated user can read ANY file | Disclosure of confidential data |
| File Deletion IDOR | CRITICAL | Any authenticated user can delete ANY file | Data destruction |
| File Publish/Unpublish IDOR | CRITICAL | Any authenticated user can expose ANY file | Information disclosure |
| Project Content Publish IDOR | CRITICAL | Any authenticated user can publish ANY content | Bulk information exposure |
| Notes Publish IDOR | CRITICAL | Any authenticated user can expose ANY note | Information disclosure |
| Task Deletion IDOR | HIGH | Any authenticated user can delete ANY task | Data loss, project disruption |
| Bookmark Edit IDOR | HIGH | Any authenticated user can modify ANY bookmark | Content tampering |
| Sequential ID Enumeration | MEDIUM | Attackers can enumerate all objects | Information enumeration |

---

## Conclusion

The phpCollab application contains multiple CRITICAL IDOR vulnerabilities that allow authenticated users to bypass authorization checks and access, modify, or delete objects belonging to other users. These vulnerabilities pose a significant security risk and should be remediated immediately, starting with the CRITICAL severity issues.

The root cause is the lack of consistent authorization checks before data access operations. Implementation of a comprehensive authorization framework and consistent authorization checks throughout the codebase is essential.

**Total Vulnerabilities Found**: 9
- Critical: 5
- High: 2
- Medium: 2

**Date of Audit**: 2025-11-08
**Audit Scope**: Full phpCollab codebase analysis
**Analysis Method**: Static code analysis focusing on IDOR patterns
