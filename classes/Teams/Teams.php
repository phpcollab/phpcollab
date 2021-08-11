<?php


namespace phpCollab\Teams;

use Exception;
use Monolog\Logger;
use phpCollab\Database;
use phpCollab\Notification;
use phpCollab\Notifications;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Teams
 * @package phpCollab\Teams
 */
class Teams
{
    protected $teams_gateway;
    protected $db;
    protected $notification;
    protected $notifications;
    protected $strings;
    protected $root;

    /**
     * Teams constructor.
     * @param Database $database
     * @param Notification $notification
     * @param Notifications\Notifications $notifications
     */
    public function __construct(
        Database $database,
        Notification $notification,
        Notifications\Notifications $notifications
    ) {
        $this->db = $database;
        $this->notification = $notification;
        $this->notifications = $notifications;
        $this->teams_gateway = new TeamsGateway($this->db);
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
    }

    /**
     * @param int $projectId
     * @param int $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember(int $projectId, int $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByProjectIdAndTeamMember($projectId, $teamMember);
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function getOtherProjectTeamMembers(int $projectId, int $memberId)
    {
        return $this->teams_gateway->getOtherProjectTeamMembers($projectId, $memberId);
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getClientTeamMembersByProject(int $projectId, string $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getClientTeamMembersByProject($projectId, $sorting);
    }

    /**
     * @param int $projectId
     * @param int $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished(
        int $projectId,
        int $teamMember
    ) {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId,
            $teamMember);
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectSiteContacts(int $projectId, string $sorting = null)
    {
        return $this->teams_gateway->getProjectSiteContacts($projectId, $sorting);
    }

    /**
     * @param int $teamMember
     * @return mixed
     */
    public function getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished(int $teamMember)
    {
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($teamMember);
    }

    /**
     * @param int $teamMember
     * @param int $orgId
     * @return mixed
     */
    public function getTeamByTeamMemberAndOrgId(int $teamMember, int $orgId)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByTeamMemberAndOrgId($teamMember, $orgId);
    }

    /**
     * @param int $projectId
     * @param int|null $offset
     * @param int|null $limit
     * @param string|null $sorting
     * @return mixed
     */
    public function getTeamByProjectId(int $projectId, int $offset = null, int $limit = null, string $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }

        return $this->teams_gateway->getTeamByProjectId($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param int $projectId
     * @return int
     */
    public function getTopicCountByProject(int $projectId): int
    {
        $team = $this->getTeamByProjectId($projectId);
        return count($team);
    }

    /**
     * @param int $memberId
     * @param string|null $sorting
     * @return mixed
     */
    public function getTeamByMemberId(int $memberId, string $sorting = null)
    {
        return $this->teams_gateway->getTeamByMemberId($memberId, $sorting);
    }

    /**
     * @param int $memberId
     * @return mixed
     */
    public function getTeamsImAMemberOf(int $memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamsImAMemberOf($memberId);
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @return string
     */
    public function isTeamMember(int $projectId, int $memberId): string
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return (count($this->teams_gateway->isTeamMember($projectId, $memberId)) > 0) ? "true" : "false";
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @return mixed
     */
    public function deleteFromTeamsByProjectIdAndMemberId(int $projectId, int $memberId)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdEqualsAndMemberIdIn($projectId, $memberId);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteFromTeamsByProjectId(string $projectIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdIn($projectIds);
    }

    /**
     * @param string $memberIds
     * @return mixed
     */
    public function deleteTeamWhereMemberIn(string $memberIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereMemberIdIn($memberIds);
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @param int $published
     * @param int $authorized
     * @return mixed
     */
    public function addTeam(int $projectId, int $memberId, int $published, int $authorized)
    {
        return $this->teams_gateway->addTeam($projectId, $memberId, $published, $authorized);
    }

    /**
     * @param int $projectId
     * @param string $memberIds
     * @return mixed
     */
    public function publishToSite(int $projectId, string $memberIds)
    {
        return $this->teams_gateway->publishTeams($projectId, $memberIds);
    }

    /**
     * @param int $projectId
     * @param string $memberIds
     * @return mixed
     */
    public function unPublishToSite(int $projectId, string $memberIds)
    {
        return $this->teams_gateway->unPublishTeams($projectId, $memberIds);
    }

    /**
     * @param array $projectDetails
     * @param string $members
     * @throws Exception
     */
    public function sendRemoveProjectTeamNotification(array $projectDetails, string $members)
    {
        if ($projectDetails) {
            $mail = $this->notification;
            try {

                $mail->setFrom($projectDetails["pro_mem_email_work"], $projectDetails["pro_mem_name"]);

                $mail->partSubject = $this->strings["noti_removeprojectteam1"];
                $mail->partMessage = $this->strings["noti_removeprojectteam2"];


                if ($projectDetails["pro_org_id"] == "1") {
                    $projectDetails["pro_org_name"] = $this->strings["none"];
                }

                $body = $mail->partMessage . "\n\n";
                $body .= $this->strings["project"] . " : " . $projectDetails["pro_name"] . " (" . $projectDetails["pro_id"] . ")\n";
                $body .= $this->strings["organization"] . " : " . $projectDetails["pro_org_name"] . "\n\n";
                $body .= $this->strings["noti_moreinfo"] . "\n";

                // This is hard coded, so it is always "1"
                $organization = "1";
                if ($organization == "1") {
                    $body .= $this->root . "/general/login.php?url=projects/viewproject.php%3Fid=" . $projectDetails["pro_id"];
                }
                if ($organization != "1" && $projectDetails["pro_published"] == "0") {
                    $body .= $this->root;
                }

                $body .= "\n\n" . $mail->footer;

                $notifications = $this->notifications;
                $listNotifications = $notifications->getNotificationsWhereMemberIn($members);

                foreach ($listNotifications as $memberNotification) {

                    if ($memberNotification["not_removeprojectteam"] == "0" && $memberNotification["not_mem_email_work"] != "") {
                        $mail->Subject = $mail->partSubject . " " . $projectDetails["pro_name"];
                        $mail->Priority = "3";
                        $mail->Body = $body;
                        $mail->AddAddress($memberNotification["not_mem_email_work"],
                            $memberNotification["not_mem_name"]);
                        $mail->Send();
                        $mail->ClearAddresses();
                    }
                }
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }
    }

    /**
     * @param array $projectDetail
     * @param string $members
     * @param Session $session
     * @param Logger $logger
     * @throws Exception
     */
    public function sendAddProjectTeamNotification(array $projectDetail, string $members, Session $session, Logger $logger)
    {
        if ($projectDetail) {
            $mail = $this->notification;
            try {
                $logger->debug('Nofitication: Send project team notification', ['projectDetail' => $projectDetail]);
                $mail->getUserinfo($session->get("id"), "from", $logger);
                $mail->partSubject = $this->strings["noti_addprojectteam1"];
                $mail->partMessage = $this->strings["noti_addprojectteam2"];

                if ($projectDetail["pro_org_id"] == "1") {
                    $projectDetail["pro_org_name"] = $this->strings["none"];
                }

                $body = $mail->partMessage . "\n\n";
                $body .= $this->strings["project"] . " : " . $projectDetail["pro_name"] . " (" . $projectDetail["pro_id"] . ")\n";
                $body .= $this->strings["organization"] . " : " . $projectDetail["pro_org_name"] . "\n\n";
                $body .= $this->strings["noti_moreinfo"] . "\n";


                // This is hard coded, so it is always "1"
                $organization = "1";
                if ($organization == "1") {
                    $body .= $this->root . "/general/login.php?url=projects/viewproject.php%3Fid=" . $projectDetail["pro_id"];
                }
                if ($organization != "1" && $projectDetail["pro_published"] == "0") {
                    $body .= $this->root;
                    $body .= $this->root . "/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetail["pro_id"];
                }

                $body .= "\n\n" . $mail->footer;


                $notifications = new Notifications\Notifications($this->db);
                $listNotifications = $notifications->getNotificationsWhereMemberIn($members);

                foreach ($listNotifications as $memberNotification) {
                    if ($listNotifications["not_addprojectteam"] == "0" && $listNotifications["not_mem_email_work"] != "") {
                        $mail->Subject = $mail->partSubject . " " . $projectDetail["pro_name"];
                        $mail->Priority = "3";
                        $mail->Body = $body;
                        $mail->AddAddress($memberNotification["not_mem_email_work"],
                            $memberNotification["not_mem_name"]);
                        $mail->Send();
                        $mail->ClearAddresses();
                    }
                }
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }
    }

}

