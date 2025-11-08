<?php

namespace phpCollab\Security;

use Exception;
use phpCollab\Container;
use phpCollab\Files\Files;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Authorization
 *
 * Centralized authorization layer to prevent IDOR (Insecure Direct Object Reference) vulnerabilities
 *
 * This class provides methods to verify that users have proper access to resources
 * before allowing operations like view, edit, delete.
 *
 * SECURITY: All resource access should go through this class to prevent unauthorized access
 *
 * @package phpCollab\Security
 */
class Authorization
{
    /** @var Session */
    private $session;

    /** @var Teams */
    private $teams;

    /** @var Projects */
    private $projects;

    /** @var Tasks */
    private $tasks;

    /** @var Files */
    private $files;

    /** @var int Current user ID */
    private $currentUserId;

    /** @var int Current user profile (0=admin, 1=project manager, 2=user, 3=client) */
    private $currentUserProfile;

    /**
     * Authorization constructor.
     *
     * @param Container $container Dependency injection container
     */
    public function __construct(Container $container)
    {
        $this->session = $container->getRequest()->getSession();
        $this->teams = $container->getTeams();
        $this->projects = $container->getProjectsLoader();
        $this->tasks = $container->getTasksLoader();
        $this->files = $container->getFilesLoader();

        // Get current user from session
        $this->currentUserId = (int)$this->session->get('id');
        $this->currentUserProfile = (int)$this->session->get('profile');
    }

    /**
     * Check if current user is an administrator
     *
     * @return bool True if user is admin (profile = 0)
     */
    public function isAdmin(): bool
    {
        return $this->currentUserProfile === 0;
    }

    /**
     * Check if current user is a project manager or admin
     *
     * @return bool True if user is admin or project manager
     */
    public function isProjectManager(): bool
    {
        return in_array($this->currentUserProfile, [0, 1], true);
    }

    /**
     * Check if user is a member of a project team
     *
     * Administrators always have access to all projects
     *
     * @param int $projectId Project ID
     * @param int|null $userId User ID (defaults to current user)
     * @return bool True if user is team member or admin
     */
    public function isProjectTeamMember(int $projectId, ?int $userId = null): bool
    {
        // Admins have access to all projects
        if ($this->isAdmin()) {
            return true;
        }

        $userId = $userId ?? $this->currentUserId;

        // Check if user is on project team
        $teamMembership = $this->teams->getTeamByProjectIdAndTeamMember($projectId, $userId);

        return !empty($teamMembership);
    }

    /**
     * Check if user is the owner/creator of a project
     *
     * @param int $projectId Project ID
     * @param int|null $userId User ID (defaults to current user)
     * @return bool True if user is project owner
     */
    public function isProjectOwner(int $projectId, ?int $userId = null): bool
    {
        $userId = $userId ?? $this->currentUserId;

        $project = $this->projects->getProjectById($projectId);

        if (!$project) {
            return false;
        }

        return (int)$project['pro_owner'] === $userId;
    }

    /**
     * Verify user has access to a project
     *
     * @param int $projectId Project ID
     * @param int|null $userId User ID (defaults to current user)
     * @throws UnauthorizedException If user doesn't have access
     * @return void
     */
    public function requireProjectAccess(int $projectId, ?int $userId = null): void
    {
        $userId = $userId ?? $this->currentUserId;

        if (!$this->isProjectTeamMember($projectId, $userId)) {
            throw new UnauthorizedException(
                "User $userId is not authorized to access project $projectId"
            );
        }
    }

    /**
     * Verify user has access to a file
     *
     * Files are accessed through their associated project or task.
     * User must be a team member of the project.
     *
     * @param int $fileId File ID
     * @param int|null $userId User ID (defaults to current user)
     * @throws UnauthorizedException If user doesn't have access
     * @return void
     */
    public function requireFileAccess(int $fileId, ?int $userId = null): void
    {
        $userId = $userId ?? $this->currentUserId;

        $file = $this->files->getFileById($fileId);

        if (!$file) {
            throw new UnauthorizedException("File $fileId not found");
        }

        $projectId = (int)$file['fil_project'];

        // Check if user is member of the file's project
        if (!$this->isProjectTeamMember($projectId, $userId)) {
            throw new UnauthorizedException(
                "User $userId is not authorized to access file $fileId (project $projectId)"
            );
        }
    }

    /**
     * Verify user has permission to delete a file
     *
     * Only admins, project managers, or the file owner can delete files
     *
     * @param int $fileId File ID
     * @param int|null $userId User ID (defaults to current user)
     * @throws UnauthorizedException If user doesn't have permission
     * @return void
     */
    public function requireFileDeletePermission(int $fileId, ?int $userId = null): void
    {
        $userId = $userId ?? $this->currentUserId;

        // Admins can delete any file
        if ($this->isAdmin()) {
            return;
        }

        $file = $this->files->getFileById($fileId);

        if (!$file) {
            throw new UnauthorizedException("File $fileId not found");
        }

        $projectId = (int)$file['fil_project'];
        $fileOwnerId = (int)$file['fil_owner'];

        // Must be team member
        if (!$this->isProjectTeamMember($projectId, $userId)) {
            throw new UnauthorizedException(
                "User $userId is not authorized to delete file $fileId (not a team member)"
            );
        }

        // Project managers can delete
        if ($this->isProjectManager()) {
            return;
        }

        // File owner can delete their own files
        if ($fileOwnerId === $userId) {
            return;
        }

        throw new UnauthorizedException(
            "User $userId is not authorized to delete file $fileId (not owner or manager)"
        );
    }

    /**
     * Verify user has access to a task
     *
     * Tasks are accessed through their associated project.
     * User must be a team member of the project.
     *
     * @param int $taskId Task ID
     * @param int|null $userId User ID (defaults to current user)
     * @throws UnauthorizedException If user doesn't have access
     * @return void
     */
    public function requireTaskAccess(int $taskId, ?int $userId = null): void
    {
        $userId = $userId ?? $this->currentUserId;

        $task = $this->tasks->getTaskById($taskId);

        if (!$task) {
            throw new UnauthorizedException("Task $taskId not found");
        }

        $projectId = (int)$task['tas_project'];

        // Check if user is member of the task's project
        if (!$this->isProjectTeamMember($projectId, $userId)) {
            throw new UnauthorizedException(
                "User $userId is not authorized to access task $taskId (project $projectId)"
            );
        }
    }

    /**
     * Verify user has permission to delete a task
     *
     * Only admins, project managers, or the task owner can delete tasks
     *
     * @param int $taskId Task ID
     * @param int|null $userId User ID (defaults to current user)
     * @throws UnauthorizedException If user doesn't have permission
     * @return void
     */
    public function requireTaskDeletePermission(int $taskId, ?int $userId = null): void
    {
        $userId = $userId ?? $this->currentUserId;

        // Admins can delete any task
        if ($this->isAdmin()) {
            return;
        }

        $task = $this->tasks->getTaskById($taskId);

        if (!$task) {
            throw new UnauthorizedException("Task $taskId not found");
        }

        $projectId = (int)$task['tas_project'];
        $taskOwnerId = (int)$task['tas_owner'];

        // Must be team member
        if (!$this->isProjectTeamMember($projectId, $userId)) {
            throw new UnauthorizedException(
                "User $userId is not authorized to delete task $taskId (not a team member)"
            );
        }

        // Project managers can delete
        if ($this->isProjectManager()) {
            return;
        }

        // Task owner can delete their own tasks
        if ($taskOwnerId === $userId) {
            return;
        }

        throw new UnauthorizedException(
            "User $userId is not authorized to delete task $taskId (not owner or manager)"
        );
    }

    /**
     * Verify user has admin privileges
     *
     * @throws UnauthorizedException If user is not an admin
     * @return void
     */
    public function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            throw new UnauthorizedException(
                "User {$this->currentUserId} requires admin privileges for this operation"
            );
        }
    }

    /**
     * Verify user has project manager or admin privileges
     *
     * @throws UnauthorizedException If user is not a manager or admin
     * @return void
     */
    public function requireProjectManager(): void
    {
        if (!$this->isProjectManager()) {
            throw new UnauthorizedException(
                "User {$this->currentUserId} requires project manager or admin privileges for this operation"
            );
        }
    }

    /**
     * Get current user ID
     *
     * @return int Current user ID
     */
    public function getCurrentUserId(): int
    {
        return $this->currentUserId;
    }

    /**
     * Get current user profile
     *
     * @return int Current user profile (0=admin, 1=manager, 2=user, 3=client)
     */
    public function getCurrentUserProfile(): int
    {
        return $this->currentUserProfile;
    }
}
