<?php

namespace phpCollab\Members;

/**
 * Interface MembersRepositoryInterface
 *
 * Defines the contract for member data access.
 * Following the Repository Pattern, this interface abstracts data access operations,
 * allowing for:
 * - Easy mocking in tests
 * - Multiple implementations (SQL, NoSQL, API, etc.)
 * - Clear separation between data access and business logic
 *
 * @package phpCollab\Members
 */
interface MembersRepositoryInterface
{
    /**
     * Find a member by their login username
     *
     * @param string|array $memberLogin Login username or array with login data
     * @return array|null Member data or null if not found
     */
    public function findByLogin($memberLogin): ?array;

    /**
     * Find a member by their ID
     *
     * @param int $memberId Member ID
     * @return array|null Member data or null if not found
     */
    public function findById(int $memberId): ?array;

    /**
     * Check if a member exists with given login
     *
     * @param string $memberLogin Login to check
     * @param string|null $memberLoginOld Old login to exclude from check
     * @return bool True if member exists
     */
    public function exists(string $memberLogin, ?string $memberLoginOld = null): bool;

    /**
     * Find all non-client members except specified IDs
     *
     * @param string $memberIds Comma-separated member IDs to exclude
     * @return array Array of member records
     */
    public function findNonClientMembersExcept(string $memberIds): array;

    /**
     * Find members by multiple IDs
     *
     * @param string $memberIds Comma-separated member IDs
     * @param string|null $sorting Sorting clause
     * @return array Array of member records
     */
    public function findByIds(string $memberIds, ?string $sorting = null): array;

    /**
     * Find members by profile IDs
     *
     * @param string $profileIds Comma-separated profile IDs
     * @param string|null $excludeId Member ID to exclude
     * @param string|null $sorting Sorting clause
     * @return array Array of member records
     */
    public function findByProfiles(string $profileIds, ?string $excludeId = null, ?string $sorting = null): array;

    /**
     * Find members by organization ID
     *
     * @param int $orgId Organization ID
     * @param string|null $sorting Sorting clause
     * @return array Array of member records
     */
    public function findByOrganization(int $orgId, ?string $sorting = null): array;

    /**
     * Find client members by organization who are not in specified team
     *
     * @param int $orgId Organization ID
     * @param array|null $membersTeam Array of team member IDs
     * @param string|null $sorting Sorting clause
     * @return array Array of member records
     */
    public function findClientMembersByOrgNotInTeam(int $orgId, ?array $membersTeam = null, ?string $sorting = null): array;

    /**
     * Get all members
     *
     * @param string|null $sorting Sorting clause
     * @return array Array of all member records
     */
    public function findAll(?string $sorting = null): array;

    /**
     * Get all non-client members
     *
     * @param string|null $sorting Sorting clause
     * @return array Array of non-client member records
     */
    public function findNonClientMembers(?string $sorting = null): array;

    /**
     * Get all non-management members
     *
     * @param string|null $sorting Sorting clause
     * @return array Array of non-management member records
     */
    public function findNonManagementMembers(?string $sorting = null): array;

    /**
     * Search members by query
     *
     * @param string $query Search query
     * @param string|null $sorting Sorting clause
     * @param int|null $limit Offset for pagination
     * @param int|null $rowLimit Number of rows per page
     * @return array Array of matching member records
     */
    public function search(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array;

    /**
     * Create a new member
     *
     * @param array $data Member data
     * @return string ID of created member
     */
    public function create(array $data): string;

    /**
     * Update an existing member
     *
     * @param int $memberId Member ID
     * @param array $data Member data to update
     * @return void
     */
    public function update(int $memberId, array $data): void;

    /**
     * Update member's password
     *
     * @param int $memberId Member ID
     * @param string $password Encrypted password
     * @return void
     */
    public function updatePassword(int $memberId, string $password): void;

    /**
     * Update last page visited for a member
     *
     * @param int $userId User ID
     * @param string $page Page URL/identifier
     * @return void
     */
    public function updateLastPageVisited(int $userId, string $page): void;

    /**
     * Update last page visited by login username
     *
     * @param string $userName Login username
     * @param string $page Page URL/identifier
     * @return void
     */
    public function updateLastPageVisitedByLogin(string $userName, string $page): void;

    /**
     * Delete members by organization ID
     *
     * @param int $orgId Organization ID
     * @return void
     */
    public function deleteByOrganization(int $orgId): void;

    /**
     * Delete members by multiple IDs
     *
     * @param string $memberIds Comma-separated member IDs
     * @return void
     */
    public function deleteByIds(string $memberIds): void;
}
