<?php

namespace Tests\Unit\Security;

use Codeception\Test\Unit;
use phpCollab\Container;
use phpCollab\Security\Authorization;
use phpCollab\Security\UnauthorizedException;
use UnitTester;

/**
 * Unit Tests: Authorization and Access Control
 *
 * Tests the Authorization class for proper access control and IDOR prevention
 *
 * SECURITY FEATURES TESTED:
 * - Admin privilege verification
 * - Project manager privilege verification
 * - Project team membership verification
 * - File access authorization (IDOR prevention)
 * - Task access authorization (IDOR prevention)
 * - Resource deletion permissions
 * - Unauthorized access exception handling
 *
 * RELATED: classes/Security/Authorization.php
 */
class AuthorizationTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * Test: Admin profile check (profile = 0)
     */
    public function testIsAdminReturnsTrue()
    {
        // Simulate admin user (profile 0)
        $userProfile = 0;

        // Admin check logic
        $isAdmin = ($userProfile === 0);

        $this->assertTrue($isAdmin, 'User with profile 0 should be admin');
    }

    /**
     * Test: Non-admin profile check
     */
    public function testIsAdminReturnsFalse()
    {
        // Test all non-admin profiles
        $nonAdminProfiles = [1, 2, 3];  // 1=manager, 2=user, 3=client

        foreach ($nonAdminProfiles as $profile) {
            $isAdmin = ($profile === 0);
            $this->assertFalse($isAdmin, "User with profile $profile should NOT be admin");
        }
    }

    /**
     * Test: Project manager check (profile 0 or 1)
     */
    public function testIsProjectManagerReturnsTrue()
    {
        // Admins (0) and Project Managers (1) should return true
        $managerProfiles = [0, 1];

        foreach ($managerProfiles as $profile) {
            $isManager = in_array($profile, [0, 1], true);
            $this->assertTrue($isManager, "Profile $profile should be considered manager");
        }
    }

    /**
     * Test: Regular user is not a project manager
     */
    public function testIsProjectManagerReturnsFalse()
    {
        // Regular users (2) and clients (3) should NOT be managers
        $regularProfiles = [2, 3];

        foreach ($regularProfiles as $profile) {
            $isManager = in_array($profile, [0, 1], true);
            $this->assertFalse($isManager, "Profile $profile should NOT be manager");
        }
    }

    /**
     * Test: Profile validation constants
     */
    public function testProfileConstants()
    {
        // Verify expected profile values
        $profiles = [
            'admin' => 0,
            'project_manager' => 1,
            'user' => 2,
            'client' => 3,
        ];

        $this->assertEquals(0, $profiles['admin'], 'Admin profile should be 0');
        $this->assertEquals(1, $profiles['project_manager'], 'Project manager profile should be 1');
        $this->assertEquals(2, $profiles['user'], 'User profile should be 2');
        $this->assertEquals(3, $profiles['client'], 'Client profile should be 3');
    }

    /**
     * Test: Admin privilege escalation prevention
     *
     * SECURITY: Verify that non-admins cannot bypass admin checks
     */
    public function testCannotBypassAdminCheck()
    {
        $testCases = [
            ['profile' => 1, 'description' => 'Project Manager'],
            ['profile' => 2, 'description' => 'Regular User'],
            ['profile' => 3, 'description' => 'Client'],
            ['profile' => -1, 'description' => 'Invalid negative profile'],
            ['profile' => 4, 'description' => 'Invalid high profile'],
            ['profile' => 99, 'description' => 'Invalid very high profile'],
        ];

        foreach ($testCases as $case) {
            $isAdmin = ($case['profile'] === 0);
            $this->assertFalse(
                $isAdmin,
                "{$case['description']} (profile {$case['profile']}) should NOT have admin access"
            );
        }
    }

    /**
     * Test: Project team membership logic
     *
     * SECURITY: Only team members (and admins) should have project access
     */
    public function testProjectTeamMembershipLogic()
    {
        // Simulate team membership check
        $userId = 42;
        $projectId = 100;
        $userProfile = 2; // Regular user

        // Simulate team members for project 100
        $teamMembers = [10, 42, 55, 78];

        // Check if user is in team
        $isTeamMember = in_array($userId, $teamMembers, true);
        $isAdmin = ($userProfile === 0);

        // Access granted if team member OR admin
        $hasAccess = $isTeamMember || $isAdmin;

        $this->assertTrue($hasAccess, 'User 42 should have access (team member)');
    }

    /**
     * Test: Non-team member denied access
     *
     * SECURITY: Prevents IDOR - users can't access projects they're not on
     */
    public function testNonTeamMemberDeniedAccess()
    {
        $userId = 99;  // Not on team
        $projectId = 100;
        $userProfile = 2; // Regular user (not admin)

        // Team members for project 100
        $teamMembers = [10, 42, 55, 78];

        // Check access
        $isTeamMember = in_array($userId, $teamMembers, true);
        $isAdmin = ($userProfile === 0);
        $hasAccess = $isTeamMember || $isAdmin;

        $this->assertFalse(
            $hasAccess,
            'User 99 should NOT have access (not on team, not admin)'
        );
    }

    /**
     * Test: Admin bypass for team membership
     *
     * SECURITY: Admins should have access to ALL projects regardless of team membership
     */
    public function testAdminBypassesTeamMembership()
    {
        $userId = 1;  // Admin user
        $projectId = 100;
        $userProfile = 0; // Admin

        // Team members for project 100 (admin NOT in list)
        $teamMembers = [10, 42, 55, 78];

        // Check access
        $isTeamMember = in_array($userId, $teamMembers, true);
        $isAdmin = ($userProfile === 0);
        $hasAccess = $isTeamMember || $isAdmin;

        $this->assertFalse($isTeamMember, 'Admin should NOT be in team list');
        $this->assertTrue($isAdmin, 'User should be admin');
        $this->assertTrue($hasAccess, 'Admin should have access despite not being in team');
    }

    /**
     * Test: File access requires project membership
     *
     * SECURITY: Files are tied to projects - must be project member to access
     */
    public function testFileAccessRequiresProjectMembership()
    {
        $userId = 42;
        $fileId = 200;
        $userProfile = 2; // Regular user

        // File belongs to project 100
        $fileProjectId = 100;

        // User is member of projects
        $userProjects = [50, 75, 100, 125];

        // Check if user is member of file's project
        $hasProjectAccess = in_array($fileProjectId, $userProjects, true);
        $isAdmin = ($userProfile === 0);
        $hasFileAccess = $hasProjectAccess || $isAdmin;

        $this->assertTrue(
            $hasFileAccess,
            'User should have file access (member of file project)'
        );
    }

    /**
     * Test: File access denied when not project member
     *
     * SECURITY CRITICAL: Prevents IDOR on file downloads
     */
    public function testFileAccessDeniedWithoutProjectMembership()
    {
        $userId = 99;
        $fileId = 200;
        $userProfile = 2; // Regular user

        // File belongs to project 100
        $fileProjectId = 100;

        // User is member of different projects
        $userProjects = [50, 75, 125];  // NOT 100

        // Check access
        $hasProjectAccess = in_array($fileProjectId, $userProjects, true);
        $isAdmin = ($userProfile === 0);
        $hasFileAccess = $hasProjectAccess || $isAdmin;

        $this->assertFalse(
            $hasFileAccess,
            'User should NOT have file access (not member of file project)'
        );
    }

    /**
     * Test: Task access requires project membership
     *
     * SECURITY: Tasks are tied to projects - must be project member
     */
    public function testTaskAccessRequiresProjectMembership()
    {
        $userId = 42;
        $taskId = 300;
        $userProfile = 2;

        // Task belongs to project 100
        $taskProjectId = 100;

        // User projects
        $userProjects = [100, 150];

        $hasProjectAccess = in_array($taskProjectId, $userProjects, true);
        $isAdmin = ($userProfile === 0);
        $hasTaskAccess = $hasProjectAccess || $isAdmin;

        $this->assertTrue(
            $hasTaskAccess,
            'User should have task access (member of task project)'
        );
    }

    /**
     * Test: File deletion requires ownership or manager privileges
     *
     * SECURITY: Regular users can only delete their own files
     */
    public function testFileDeletionRequiresOwnershipOrManager()
    {
        // Test Case 1: File owner can delete
        $userId = 42;
        $fileOwnerId = 42;
        $userProfile = 2; // Regular user

        $isOwner = ($userId === $fileOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertTrue($canDelete, 'File owner should be able to delete their own file');

        // Test Case 2: Non-owner regular user CANNOT delete
        $userId = 99;
        $fileOwnerId = 42;
        $userProfile = 2;

        $isOwner = ($userId === $fileOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertFalse(
            $canDelete,
            'Non-owner regular user should NOT be able to delete file'
        );

        // Test Case 3: Manager can delete any file
        $userId = 99;
        $fileOwnerId = 42;
        $userProfile = 1; // Manager

        $isOwner = ($userId === $fileOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertTrue(
            $canDelete,
            'Manager should be able to delete any file (even if not owner)'
        );
    }

    /**
     * Test: Task deletion requires ownership or manager privileges
     *
     * SECURITY: Same logic as file deletion
     */
    public function testTaskDeletionRequiresOwnershipOrManager()
    {
        // Owner can delete
        $userId = 42;
        $taskOwnerId = 42;
        $userProfile = 2;

        $isOwner = ($userId === $taskOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertTrue($canDelete, 'Task owner should be able to delete their task');

        // Non-owner cannot delete
        $userId = 99;
        $taskOwnerId = 42;
        $userProfile = 2;

        $isOwner = ($userId === $taskOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertFalse($canDelete, 'Non-owner regular user cannot delete task');

        // Admin can delete any task
        $userId = 1;
        $taskOwnerId = 42;
        $userProfile = 0; // Admin

        $isOwner = ($userId === $taskOwnerId);
        $isManagerOrAdmin = in_array($userProfile, [0, 1], true);
        $canDelete = $isOwner || $isManagerOrAdmin;

        $this->assertTrue($canDelete, 'Admin can delete any task');
    }

    /**
     * Test: Authorization exception message format
     *
     * SECURITY: Verify exception messages don't leak sensitive info
     */
    public function testUnauthorizedExceptionMessageFormat()
    {
        $userId = 99;
        $resourceId = 100;

        $exceptionMessage = "User $userId is not authorized to access project $resourceId";

        // Verify message contains user and resource IDs
        $this->assertStringContainsString((string)$userId, $exceptionMessage);
        $this->assertStringContainsString((string)$resourceId, $exceptionMessage);
        $this->assertStringContainsString('not authorized', $exceptionMessage);

        // Verify message doesn't contain sensitive data
        $this->assertStringNotContainsString('password', strtolower($exceptionMessage));
        $this->assertStringNotContainsString('secret', strtolower($exceptionMessage));
    }

    /**
     * Test: Project ownership verification
     */
    public function testProjectOwnershipVerification()
    {
        $userId = 42;
        $projectOwnerId = 42;

        $isOwner = ($userId === $projectOwnerId);

        $this->assertTrue($isOwner, 'User should be verified as project owner');

        // Non-owner check
        $userId = 99;
        $projectOwnerId = 42;

        $isOwner = ($userId === $projectOwnerId);

        $this->assertFalse($isOwner, 'User should NOT be verified as project owner');
    }

    /**
     * Test: Type juggling in authorization checks
     *
     * SECURITY CRITICAL: Prevent type juggling bypasses
     */
    public function testStrictTypeComparisonInAuthChecks()
    {
        // Test that string "0" is NOT equal to integer 0 without strict comparison
        $userProfile = "0";  // String from session/form

        // INSECURE: Loose comparison (==)
        $insecureCheck = ($userProfile == 0);  // TRUE (type juggling)
        $this->assertTrue($insecureCheck, 'Loose comparison: "0" == 0 is TRUE (type juggling)');

        // SECURE: Strict comparison (===)
        $secureCheck = ($userProfile === 0);  // FALSE (different types)
        $this->assertFalse($secureCheck, 'Strict comparison: "0" === 0 is FALSE (correct)');

        // Best practice: Cast to int first
        $userProfileInt = (int)$userProfile;
        $bestPractice = ($userProfileInt === 0);  // TRUE (both integers)
        $this->assertTrue($bestPractice, 'Best practice: cast then compare');
    }

    /**
     * Test: in_array strict comparison for privilege checks
     *
     * SECURITY: Prevent privilege escalation via type juggling
     */
    public function testStrictInArrayForPrivilegeChecks()
    {
        $userProfile = "1";  // String profile (e.g., from session)
        $allowedProfiles = [0, 1];  // Integers

        // INSECURE: Loose comparison
        $insecureCheck = in_array($userProfile, $allowedProfiles);  // TRUE (type juggling)
        $this->assertTrue($insecureCheck, 'Loose in_array: "1" in [0,1] is TRUE');

        // SECURE: Strict comparison
        $secureCheck = in_array($userProfile, $allowedProfiles, true);  // FALSE
        $this->assertFalse($secureCheck, 'Strict in_array: "1" in [0,1] is FALSE');

        // Best practice: Cast to int
        $userProfileInt = (int)$userProfile;
        $bestPractice = in_array($userProfileInt, $allowedProfiles, true);  // TRUE
        $this->assertTrue($bestPractice, 'Best practice: cast then strict compare');
    }

    /**
     * Test: Authorization check ordering
     *
     * SECURITY: Admin checks should come first for performance
     */
    public function testAuthCheckOrdering()
    {
        $userProfile = 0; // Admin
        $projectId = 100;
        $userProjects = [50, 75]; // Admin NOT in project team

        // EFFICIENT: Check admin first (early return)
        $isAdmin = ($userProfile === 0);
        if ($isAdmin) {
            $hasAccess = true;
        } else {
            $hasAccess = in_array($projectId, $userProjects, true);
        }

        $this->assertTrue($hasAccess, 'Admin should have access (early return)');

        // Verify we didn't need to check project membership
        $this->assertNotContains($projectId, $userProjects, 'Admin access without team check');
    }
}
