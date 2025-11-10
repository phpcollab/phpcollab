<?php

namespace phpCollab\Members;

use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use phpCollab\AppConfig;
use phpCollab\Notification;
use phpCollab\Util;

/**
 * Class Members
 *
 * Service layer for member management.
 * Handles business logic, validation, and notifications.
 * All data access is delegated to MembersRepositoryInterface.
 *
 * @package phpCollab\Members
 */
class Members
{
    protected MembersRepositoryInterface $repository;
    protected AppConfig $appConfig;
    protected Logger $logger;
    protected Notification $notification;

    /**
     * Members constructor.
     *
     * Uses pure constructor injection with Repository Pattern.
     * Now depends on MembersRepositoryInterface instead of Gateway directly.
     *
     * @param MembersRepositoryInterface $repository Repository for member data access
     * @param Logger $logger Logger for recording member operations
     * @param Notification $notification Service for sending member-related notifications
     * @param AppConfig $appConfig Application configuration
     */
    public function __construct(
        MembersRepositoryInterface $repository,
        Logger $logger,
        Notification $notification,
        AppConfig $appConfig
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->notification = $notification;
        $this->appConfig = $appConfig;
    }

    /**
     * @param $memberLogin
     * @return mixed
     */
    public function getMemberByLogin($memberLogin)
    {
        $this->logger->info('Members', ['Method' => 'getMemberByLogin', 'memberLogin' => $memberLogin]);
        return $this->repository->findByLogin($memberLogin);
    }

    /**
     * @param $memberLogin
     * @param null $memberLoginOld
     * @return bool
     */
    public function checkIfMemberExists($memberLogin, $memberLoginOld = null): bool
    {
        return $this->repository->exists($memberLogin, $memberLoginOld);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMemberById($memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return $this->repository->findById($memberId);
    }

    /**
     * @param mixed $memberIds single or comma separated list of member IDs
     * @return mixed
     */
    public function getNonClientMembersExcept($memberIds)
    {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        if (empty($memberIds)) {
            throw new InvalidArgumentException('No member ID(s) provided.');
        }
        return $this->repository->findNonClientMembersExcept($memberIds);

    }

    /**
     * @param $memberIds
     * @param null $sorting
     * @return mixed
     */
    public function getMembersByIdIn($memberIds, $sorting = null)
    {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        return $this->repository->findByIds($memberIds, $sorting);
    }

    /**
     * @param $memberIds
     * @param null $excludeId
     * @param null $sorting
     * @return mixed
     */
    public function getMembersByProfileIn($memberIds, $excludeId = null, $sorting = null)
    {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        return $this->repository->findByProfiles($memberIds, $excludeId, $sorting);
    }

    /**
     * @param $orgId
     * @param $sorting
     * @return mixed
     */
    public function getMembersByOrg($orgId, $sorting)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->repository->findByOrganization($orgId, $sorting);
    }

    /**
     * @param $orgId
     * @param null $membersTeam
     * @param null $sorting
     * @return mixed
     */
    public function getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam = null, $sorting = null)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $membersTeam = filter_var($membersTeam, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);

        return $this->repository->findClientMembersByOrgNotInTeam($orgId, $membersTeam, $sorting);
    }

    /**
     * @param string $login
     * @param string $name
     * @param string $emailWork
     * @param string $password
     * @param int $profile
     * @param string|null $title
     * @param int|null $organization
     * @param string|null $phoneWork
     * @param string|null $phoneHome
     * @param string|null $phoneMobile
     * @param string|null $fax
     * @param string|null $comments
     * @param string|null $created
     * @param int $timezone
     * @return mixed
     * @throws Exception
     */
    public function addMember(
        string $login,
        string $name,
        string $emailWork,
        string $password,
        int $profile,
        string $title = null,
        int $organization = null,
        string $phoneWork = null,
        string $phoneHome = null,
        string $phoneMobile = null,
        string $fax = null,
        string $comments = null,
        string $created = null,
        int $timezone = 0
    ) {
        if (empty($login) || empty($name) || empty($emailWork) || empty($password)) {
            throw new Exception('Invalid member id, login, name, or email');
        }

        $data = [
            'login' => filter_var($login, FILTER_SANITIZE_STRING),
            'name' => filter_var($name, FILTER_SANITIZE_STRING),
            'email_work' => filter_var($emailWork, FILTER_SANITIZE_STRING),
            'password' => filter_var($password, FILTER_SANITIZE_STRING),
            'profil' => filter_var($profile, FILTER_SANITIZE_STRING),
            'created' => filter_var($created, FILTER_SANITIZE_STRING),
            'organization' => filter_var($organization, FILTER_SANITIZE_STRING),
            'title' => filter_var($title, FILTER_SANITIZE_STRING),
            'phone_work' => filter_var($phoneWork, FILTER_SANITIZE_STRING),
            'phone_home' => filter_var($phoneHome, FILTER_SANITIZE_STRING),
            'mobile' => filter_var($phoneMobile, FILTER_SANITIZE_STRING),
            'fax' => filter_var($fax, FILTER_SANITIZE_STRING),
            'comments' => filter_var($comments, FILTER_SANITIZE_STRING),
            'timezone' => filter_var($timezone, FILTER_SANITIZE_STRING),
        ];

        return $this->repository->create($data);
    }

    /**
     * @param int $memberId
     * @param string $login
     * @param string $name
     * @param string $emailWork
     * @param string|null $title
     * @param int $organization
     * @param string|null $phoneWork
     * @param string|null $phoneHome
     * @param string|null $phoneMobile
     * @param string|null $fax
     * @param string|null $lastPage
     * @param string|null $comments
     * @param int|null $profile
     * @return bool|mixed
     * @throws Exception
     */
    public function updateMember(
        int $memberId,
        string $login,
        string $name,
        string $emailWork,
        string $title = null,
        int $organization = 1,
        string $phoneWork = null,
        string $phoneHome = null,
        string $phoneMobile = null,
        string $fax = null,
        string $lastPage = null,
        string $comments = null,
        int $profile = null
    ) {
        if (empty($memberId) || empty($login) || empty($name) || empty($emailWork)) {
            throw new Exception('Invalid member id, login, name, or email');
        }

        $data = [
            'login' => filter_var($login, FILTER_SANITIZE_STRING),
            'name' => filter_var($name, FILTER_SANITIZE_STRING),
            'organization' => filter_var($organization, FILTER_SANITIZE_STRING),
            'title' => filter_var($title, FILTER_SANITIZE_STRING),
            'email_work' => filter_var($emailWork, FILTER_SANITIZE_STRING),
            'phone_work' => filter_var($phoneWork, FILTER_SANITIZE_STRING),
            'phone_home' => filter_var($phoneHome, FILTER_SANITIZE_STRING),
            'mobile' => filter_var($phoneMobile, FILTER_SANITIZE_STRING),
            'fax' => filter_var($fax, FILTER_SANITIZE_STRING),
            'comments' => filter_var($comments, FILTER_SANITIZE_STRING),
            'last_page' => filter_var($lastPage, FILTER_SANITIZE_STRING),
            'profil' => $profile,
        ];

        $this->repository->update($memberId, $data);
        return true;
    }

    /**
     * @param $memberId
     * @param $password
     * @return mixed
     * @throws Exception
     */
    public function setPassword($memberId, $password)
    {
        if (!isset($memberId) || !isset($password)) {
            throw new Exception('Invalid member id, password');
        } else {
            $memberId = filter_var((int)$memberId, FILTER_VALIDATE_INT);
            $password = Util::getPassword($password);
            return $this->repository->updatePassword($memberId, $password);
        }
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllMembers($sorting = null)
    {
        return $this->repository->findAll($sorting);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getNonClientMembers($sorting = null)
    {
        return $this->repository->findNonClientMembers($sorting);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getNonManagementMembers($sorting = null)
    {
        return $this->repository->findNonManagementMembers($sorting);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function deleteMemberByOrgId($orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);
        return $this->repository->deleteByOrganization($orgId);
    }

    /**
     * Delete from the members table by member_id(s)
     * @param $memberIds
     * @return mixed
     */
    public function deleteMemberByIdIn($memberIds)
    {
        return $this->repository->deleteByIds($memberIds);
    }

    /**
     * @param $userId
     * @param $page
     * @return mixed
     */
    public function setLastPageVisited($userId, $page)
    {
        return $this->repository->updateLastPageVisited($userId, $page);
    }

    /**
     * @param $userName
     * @param $page
     * @return mixed
     */
    public function setLastPageVisitedByLogin($userName, $page)
    {
        return $this->repository->updateLastPageVisited($userName, $page);
    }

    /**
     * @param $query
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function getSearchMembers($query, $sorting = null, $limit = null, $rowLimit = null)
    {
        return $this->repository->search($query, $sorting, $limit, $rowLimit);
    }

    /**
     * @param $toEmail
     * @param $toName
     * @param $subject
     * @param $message
     * @param null $fromEmail
     * @param null $fromName
     * @param null $signature
     * @throws Exception
     */
    public function sendEmail(
        $toEmail,
        $toName,
        $subject,
        $message,
        $fromEmail = null,
        $fromName = null,
        $signature = null
    ) {
        if ($toEmail && $toName && $subject && $message) {
            $mail = $this->notification;

            try {
                if (!is_null($signature)) {
                    $mail->setSignature($signature);
                }

                if (empty($fromEmail)) {
                    $fromEmail = $this->appConfig->getSupportEmail();
                }

                if (empty($fromName)) {
                    $fromName = $this->appConfig->getSetTitle();
                }

                $mail->setFrom($fromEmail, $fromName);

                $mail->setFooter("---\n" . $this->appConfig->getString("noti_foot1"));

                $body = $message;

                $body .= "\n\n" . $mail->getSignature();

                $body .= "\n\n" . $mail->getFooter();

                $mail->Subject = $subject;
                $mail->Priority = "3";
                $mail->Body = $body;
                $mail->AddAddress($toEmail, $toName);
                $mail->Send();
                $mail->ClearAddresses();

            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            $this->logger->critical('Error sending mail');
            throw new Exception('Error sending mail');
        }
    }
}
