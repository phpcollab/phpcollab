<?php

namespace phpCollab\Members;

use InvalidArgumentException;
use phpCollab\Database;
use phpCollab\RequestData;

/**
 * Class MembersRepository
 *
 * Concrete implementation of MembersRepositoryInterface.
 * Handles all member data access operations using MembersGateway internally.
 *
 * This repository:
 * - Provides a clean, intention-revealing API for data access
 * - Encapsulates the Gateway pattern implementation details
 * - Uses domain-friendly method names (findById instead of getMemberById)
 * - Focuses purely on data access (no business logic)
 *
 * @package phpCollab\Members
 */
class MembersRepository implements MembersRepositoryInterface
{
    private MembersGateway $gateway;

    /**
     * MembersRepository constructor.
     *
     * @param Database $database Database connection
     * @param RequestData $requestData Request data for SQL query construction
     */
    public function __construct(Database $database, RequestData $requestData)
    {
        $this->gateway = new MembersGateway($database, $requestData);
    }

    /**
     * {@inheritdoc}
     */
    public function findByLogin($memberLogin): ?array
    {
        $result = $this->gateway->getMemberByLogin($memberLogin);
        return $result ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $memberId): ?array
    {
        $result = $this->gateway->getMemberById($memberId);
        return $result ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $memberLogin, ?string $memberLoginOld = null): bool
    {
        $result = $this->gateway->checkMemberExists($memberLogin, $memberLoginOld ?? '');
        return !empty($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findNonClientMembersExcept(string $memberIds): array
    {
        if (empty($memberIds)) {
            throw new InvalidArgumentException('No member ID(s) provided.');
        }
        return $this->gateway->getNonClientMembersNotIn($memberIds);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(string $memberIds, ?string $sorting = null): array
    {
        if (empty($memberIds)) {
            throw new InvalidArgumentException('No member ID(s) provided.');
        }
        return $this->gateway->getMembersByIdIn($memberIds, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findByProfiles(string $profileIds, ?string $excludeId = null, ?string $sorting = null): array
    {
        if (empty($profileIds)) {
            throw new InvalidArgumentException('No profile ID(s) provided.');
        }
        return $this->gateway->getMembersByProfileIn($profileIds, $excludeId, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findByOrganization(int $orgId, ?string $sorting = null): array
    {
        return $this->gateway->getMembersByOrg($orgId, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findClientMembersByOrgNotInTeam(int $orgId, ?array $membersTeam = null, ?string $sorting = null): array
    {
        return $this->gateway->getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam, $sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(?string $sorting = null): array
    {
        return $this->gateway->getAllMembers($sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findNonClientMembers(?string $sorting = null): array
    {
        return $this->gateway->getNonClientMembers($sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function findNonManagementMembers(?string $sorting = null): array
    {
        return $this->gateway->getNonManagementMembers($sorting);
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $query, ?string $sorting = null, ?int $limit = null, ?int $rowLimit = null): array
    {
        return $this->gateway->getSearchMembers($query, $sorting, $limit, $rowLimit);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): string
    {
        return $this->gateway->addMember($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $memberId, array $data): void
    {
        $this->gateway->updateMember($memberId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(int $memberId, string $password): void
    {
        $this->gateway->setPassword($memberId, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function updateLastPageVisited(int $userId, string $page): void
    {
        $this->gateway->setLastPageVisited($userId, $page);
    }

    /**
     * {@inheritdoc}
     */
    public function updateLastPageVisitedByLogin(string $userName, string $page): void
    {
        $this->gateway->setLastPageVisitedByLogin($userName, $page);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByOrganization(int $orgId): void
    {
        $this->gateway->deleteMemberByOrgId($orgId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIds(string $memberIds): void
    {
        if (empty($memberIds)) {
            throw new InvalidArgumentException('No member ID(s) provided.');
        }
        $this->gateway->deleteMemberByIdIn($memberIds);
    }
}
