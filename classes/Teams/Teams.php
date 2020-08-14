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
    protected $strings;
    protected $root;

    /**
     * Teams constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->teams_gateway = new TeamsGateway($this->db);
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
    }

    /**
     * @param $projectId
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMember($projectId, $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByProjectIdAndTeamMember($projectId, $teamMember);
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function getOtherProjectTeamMembers($projectId, $memberId)
    {
        return $this->teams_gateway->getOtherProjectTeamMembers($projectId, $memberId);
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getClientTeamMembersByProject($projectId, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getClientTeamMembersByProject($projectId, $sorting);
    }

    /**
     * @param $projectId
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId, $teamMember)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId, $teamMember);
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getProjectSiteContacts($projectId, $sorting = null)
    {
        return $this->teams_gateway->getProjectSiteContacts($projectId, $sorting);
    }

    /**
     * @param $teamMember
     * @return mixed
     */
    public function getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($teamMember)
    {
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($teamMember);
    }

    /**
     * @param $teamMember
     * @param $orgId
     * @return mixed
     */
    public function getTeamByTeamMemberAndOrgId($teamMember, $orgId)
    {
        $orgId = filter_var($orgId, FILTER_VALIDATE_INT);
        $teamMember = filter_var($teamMember, FILTER_VALIDATE_INT);

        return $this->teams_gateway->getTeamByTeamMemberAndOrgId($teamMember, $orgId);
    }

    /**
     * @param $projectId
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getTeamByProjectId($projectId, $offset = null, $limit = null, $sorting = null)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }

        return $this->teams_gateway->getTeamByProjectId($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param $projectId
     * @return int
     */
    public function getTopicCountByProject($projectId)
    {
        $team = $this->getTeamByProjectId($projectId);
        return count($team);
    }

    /**
     * @param $memberId
     * @param null $sorting
     * @return mixed
     */
    public function getTeamByMemberId($memberId, $sorting = null)
    {
        return $this->teams_gateway->getTeamByMemberId($memberId, $sorting);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getTeamsImAMemberOf($memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return $this->teams_gateway->getTeamsImAMemberOf($memberId);
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return bool
     */
    public function isTeamMember($projectId, $memberId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return (count($this->teams_gateway->isTeamMember($projectId, $memberId)) > 0) ? "true" : "false";
    }

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function deleteFromTeamsByProjectIdAndMemberId($projectId, $memberId)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdEqualsAndMemberIdIn($projectId, $memberId);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteFromTeamsByProjectId($projectIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereProjectIdIn($projectIds);
    }

    /**
     * @param $memberIds
     * @return mixed
     */
    public function deleteTeamWhereMemberIn($memberIds)
    {
        return $this->teams_gateway->deleteFromTeamsWhereMemberIdIn($memberIds);
    }

    /**
     * @param $projectId
     * @param $memberId
     * @param $published
     * @param $authorized
     * @return mixed
     */
    public function addTeam($projectId, $memberId, $published, $authorized)
    {
        return $this->teams_gateway->addTeam($projectId, $memberId, $published, $authorized);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function publishToSite($projectId, $memberIds)
    {
        return $this->teams_gateway->publishTeams($projectId, $memberIds);
    }

    /**
     * @param $projectId
     * @param $memberIds
     * @return mixed
     */
    public function unPublishToSite($projectId, $memberIds)
    {
        return $this->teams_gateway->unPublishTeams($projectId, $memberIds);
    }

    /**
     * @param $projectDetails
     * @param $members
     * @throws Exception
     */
    public function sendRemoveProjectTeamNotification($projectDetails, $members)
    {
        if ($projectDetails) {

            $mail = new Notification(true);
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
                } elseif ($organization != "1" && $projectDetails["pro_published"] == "0") {
                    $body .= $this->root;
                }

                $body .= "\n\n" . $mail->footer;

                $notifications = new Notifications\Notifications();
                $listNotifications = $notifications->getNotificationsWhereMemberIn($members);

                foreach ($listNotifications as $memberNotification) {

                    if ($memberNotification["not_removeprojectteam"] == "0" && $memberNotification["not_mem_email_work"] != "") {
                        $mail->Subject = $mail->partSubject . " " . $projectDetails["pro_name"];
                        $mail->Priority = "3";
                        $mail->Body = $body;
                        $mail->AddAddress($memberNotification["not_mem_email_work"], $memberNotification["not_mem_name"]);
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
     * @param $projectDetail
     * @param $members
     * @param Session $session
     * @param Logger $logger
     * @throws Exception
     */
    public function sendAddProjectTeamNotification($projectDetail, $members, Session $session, Logger $logger)
    {
        if ($projectDetail) {

            $mail = new Notification(true);
            try {
                $logger->debug('Nofitication: Send project team notification', ['projectDetail' => $projectDetail]);
                $mail->getUserinfo($session->get("idSession"), "from", $logger);
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
                } elseif ($organization != "1" && $projectDetail["pro_published"] == "0") {
                    $body .= $this->root;
                    $body .= $this->root . "/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetail["pro_id"];
                }

                $body .= "\n\n" . $mail->footer;

                $notifications = new Notifications\Notifications();
                $listNotifications = $notifications->getNotificationsWhereMemberIn($members);

                foreach ($listNotifications as $memberNotification) {
                    if ($listNotifications["not_addprojectteam"] == "0" && $listNotifications["not_mem_email_work"] != "") {
                        $mail->Subject = $mail->partSubject . " " . $projectDetail["pro_name"];
                        $mail->Priority = "3";
                        $mail->Body = $body;
                        $mail->AddAddress($memberNotification["not_mem_email_work"], $memberNotification["not_mem_name"]);
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

