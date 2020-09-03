<?php

namespace phpCollab\Members;

use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;
use phpCollab\Util;

/**
 * Class Members
 * @package phpCollab\Members
 */
class Members
{
    protected $members_gateway;
    protected $db;
    protected $strings;
    protected $logger;
    /**
     * @var Container
     */
    protected $container;

    /**
     * Members constructor.
     * @param Database $database
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct(Database $database, Logger $logger, Container $container)
    {
        $this->logger = $logger;

        $this->db = $database;

        $this->container = $container;

        $this->members_gateway = new MembersGateway($this->db);

        $this->strings = $GLOBALS["strings"];
    }

    /**
     * @param $memberLogin
     * @return mixed
     */
    public function getMemberByLogin($memberLogin)
    {
        return $this->members_gateway->getMemberByLogin($memberLogin);
    }

    /**
     * @param $memberLogin
     * @param null $memberLoginOld
     * @return bool
     */
    public function checkIfMemberExists($memberLogin, $memberLoginOld = null)
    {
        $memberLoginOld = (is_null($memberLoginOld)) ? '' : $memberLoginOld;

        $data = $this->members_gateway->checkMemberExists($memberLogin, $memberLoginOld);

        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMemberById($memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);

        return $this->members_gateway->getMemberById($memberId);
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
        return $this->members_gateway->getNonClientMembersNotIn($memberIds);

    }

    /**
     * @param $memberIds
     * @param null $sorting
     * @return mixed
     */
    public function getMembersByIdIn($memberIds, $sorting = null)
    {
        $memberIds = filter_var($memberIds, FILTER_SANITIZE_STRING);
        return $this->members_gateway->getMembersIn($memberIds, $sorting);
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
        return $this->members_gateway->getMembersByProfileIn($memberIds, $excludeId, $sorting);
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

        return $this->members_gateway->getAllByOrg($orgId, $sorting);
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

        return $this->members_gateway->getClientMembersByOrgIdAndNotInTeam($orgId, $membersTeam, $sorting);
    }

    /**
     * @param $username
     * @param $name
     * @param $emailWork
     * @param $password
     * @param $profile
     * @param null $title
     * @param null $organization
     * @param null $phoneWork
     * @param null $phoneHome
     * @param null $phoneMobile
     * @param null $fax
     * @param null $comments
     * @param null $created
     * @param int $timezone
     * @return mixed
     * @throws Exception
     */
    public function addMember(
        $username,
        $name,
        $emailWork,
        $password,
        $profile,
        $title = null,
        $organization = null,
        $phoneWork = null,
        $phoneHome = null,
        $phoneMobile = null,
        $fax = null,
        $comments = null,
        $created = null,
        $timezone = 0
    ) {
        if (empty($username) || empty($name) || empty($emailWork) || empty($password)) {
            throw new Exception('Invalid member id, login, name, or email');
        } else {
            $username = filter_var($username, FILTER_SANITIZE_STRING);
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $emailWork = filter_var($emailWork, FILTER_SANITIZE_STRING);
            $password = filter_var($password, FILTER_SANITIZE_STRING);
            $profile = filter_var($profile, FILTER_SANITIZE_STRING);
            $created = filter_var($created, FILTER_SANITIZE_STRING);
            $organization = filter_var($organization, FILTER_SANITIZE_STRING);
            $title = filter_var($title, FILTER_SANITIZE_STRING);
            $phoneWork = filter_var($phoneWork, FILTER_SANITIZE_STRING);
            $phoneHome = filter_var($phoneHome, FILTER_SANITIZE_STRING);
            $phoneMobile = filter_var($phoneMobile, FILTER_SANITIZE_STRING);
            $fax = filter_var($fax, FILTER_SANITIZE_STRING);
            $comments = filter_var($comments, FILTER_SANITIZE_STRING);
            $timezone = filter_var($timezone, FILTER_SANITIZE_STRING);

            return $this->members_gateway->addMember($username, $name, $title, $organization, $emailWork, $phoneWork,
                $phoneHome, $phoneMobile, $fax, $comments, $password, $profile, $created, $timezone);
        }
    }

    /**
     * @param $memberId
     * @param $login
     * @param $name
     * @param $emailWork
     * @param null $title
     * @param null $organization
     * @param null $phoneWork
     * @param null $phoneHome
     * @param null $phoneMobile
     * @param null $fax
     * @param null $lastPage
     * @param null $comments
     * @param null $profile
     * @return bool|mixed
     * @throws Exception
     */
    public function updateMember(
        $memberId,
        $login,
        $name,
        $emailWork,
        $title = null,
        $organization = null,
        $phoneWork = null,
        $phoneHome = null,
        $phoneMobile = null,
        $fax = null,
        $lastPage = null,
        $comments = null,
        $profile = null
    ) {
        if (empty($memberId) || empty($login) || empty($name) || empty($emailWork)) {
            throw new Exception('Invalid member id, login, name, or email');
        } else {

            $login = filter_var($login, FILTER_SANITIZE_STRING);
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $organization = filter_var($organization, FILTER_SANITIZE_STRING);
            $title = filter_var($title, FILTER_SANITIZE_STRING);
            $emailWork = filter_var($emailWork, FILTER_SANITIZE_STRING);
            $phoneWork = filter_var($phoneWork, FILTER_SANITIZE_STRING);
            $phoneHome = filter_var($phoneHome, FILTER_SANITIZE_STRING);
            $phoneMobile = filter_var($phoneMobile, FILTER_SANITIZE_STRING);
            $fax = filter_var($fax, FILTER_SANITIZE_STRING);
            $comments = filter_var($comments, FILTER_SANITIZE_STRING);
            $lastPage = filter_var($lastPage, FILTER_SANITIZE_STRING);

            return $this->members_gateway->updateMember($memberId, $login, $name, $title, $organization, $emailWork,
                $phoneWork, $phoneHome, $phoneMobile, $fax, $lastPage, $comments, $profile);
        }
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
            return $this->members_gateway->setPassword($memberId, $password);
        }
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllMembers($sorting = null)
    {
        return $this->members_gateway->getAllMembers($sorting);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getNonClientMembers($sorting = null)
    {
        return $this->members_gateway->getNonClientMembers($sorting);
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getNonManagementMembers($sorting = null)
    {
        return $this->members_gateway->getNonManagementMembers($sorting);
    }

    /**
     * @param $orgId
     * @return mixed
     */
    public function deleteMemberByOrgId($orgId)
    {
        $orgId = filter_var($orgId, FILTER_SANITIZE_STRING);
        return $this->members_gateway->deleteMember($orgId);
    }

    /**
     * Delete from the members table by member_id(s)
     * @param $memberIds
     * @return mixed
     */
    public function deleteMemberByIdIn($memberIds)
    {
        return $this->members_gateway->deleteMemberByIdIn($memberIds);
    }

    /**
     * @param $userId
     * @param $page
     * @return mixed
     */
    public function setLastPageVisited($userId, $page)
    {
        return $this->members_gateway->setLastPageVisited($userId, $page);
    }

    /**
     * @param $userName
     * @param $page
     * @return mixed
     */
    public function setLastPageVisitedByLogin($userName, $page)
    {
        return $this->members_gateway->setLastPageVisited($userName, $page);
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
        return $this->members_gateway->searchResultsUsers($query, $sorting, $limit, $rowLimit);
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
            $mail = $this->container->getNotification();

            try {
                if (!is_null($signature)) {
                    $mail->setSignature($signature);
                }

                if (empty($fromEmail)) {
                    $fromEmail = $GLOBALS["supportEmail"];
                }

                if (empty($fromName)) {
                    $fromName = $GLOBALS["setTitle"];
                }

                $mail->setFrom($fromEmail, $fromName);

                $mail->setFooter("---\n" . $this->strings["noti_foot1"]);

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
