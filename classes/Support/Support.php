<?php


namespace phpCollab\Support;

use Exception;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;

/**
 * Class Support
 * @package phpCollab\Support
 */
class Support
{
    protected $support_gateway;
    protected $members;
    protected $teams;
    protected $db;
    protected $strings;
    protected $root;
    protected $requestStatus;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var Logger|null
     */
    private $logger;

    /**
     * Support constructor.
     * @param Database $database
     * @param Container $container
     * @param Logger|null $logger
     */
    public function __construct(Database $database, Container $container, Logger $logger)
    {
        $this->db = $database;
        $this->container = $container;
        $this->logger = $logger;
        $this->support_gateway = new SupportGateway($this->db);
        $this->members = $container->getMembersLoader();
        $this->teams = $container->getTeams();
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];
        $this->requestStatus = $GLOBALS["requestStatus"];
    }

    /**
     * @param $supportRequestId
     * @return mixed
     */
    public function getSupportRequestById($supportRequestId)
    {
        return $this->support_gateway->getSupportRequestById($supportRequestId);
    }

    /**
     * @param $status
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatus($status, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatus($status, $sorting);
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByProject($projectId, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByProject($projectId, $sorting);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getSupportRequestByMemberId($memberId)
    {
        return $this->support_gateway->getSupportRequestByMemberId($memberId);
    }

    /**
     * @param $supportRequestId
     * @return mixed
     */
    public function getSupportRequestByIdIn($supportRequestId)
    {
        return $this->support_gateway->getSupportRequestByIdIn($supportRequestId);
    }

    /**
     * @param Int $requestStatus
     * @param Int $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId($requestStatus, $projectId, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatusAndProjectId($requestStatus, $projectId, $sorting);
    }

    /**
     * @param $memberId
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByMemberIdAndProjectId($memberId, $projectId, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByMemberIdAndProjectId($memberId, $projectId, $sorting);
    }

    /**
     * @param $requestId
     * @return mixed
     */
    public function getSupportPostsByRequestId($requestId)
    {
        return $this->support_gateway->getSupportPostsByRequestId($requestId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getSupportPostById($postId)
    {
        return $this->support_gateway->getSupportPostById($postId);
    }

    /**
     * @param $postIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn($postIds)
    {
        return $this->support_gateway->getSupportPostsByRequestIdIn($postIds);
    }

    /**
     * @param $userId
     * @param $priority
     * @param $subject
     * @param $message
     * @param $project
     * @param int $status
     * @return string
     */
    public function addSupportRequest($userId, $priority, $subject, $message, $project, $status = 0)
    {
        return $this->support_gateway->createSupportRequest($userId, $priority, $subject, $message, $project, $status);
    }

    /**
     * @param $requestId
     * @param $message
     * @param $dateCreated
     * @param $ownerId
     * @param $projectId
     * @return string
     */
    public function addSupportPost($requestId, $message, $dateCreated, $ownerId, $projectId)
    {
        $newPostId = $this->support_gateway->addPost($requestId, $message, $dateCreated, $ownerId, $projectId);
        return $this->getSupportPostById($newPostId);
    }

    /**
     * @param $requestId
     * @param $status
     * @param $dateClose
     * @return mixed
     */
    public function updateSupportPostStatus($requestId, $status, $dateClose = null)
    {
        return $this->support_gateway->updateSupportRequest($requestId, $status, $dateClose);
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests($supportRequestIds)
    {
        return $this->support_gateway->deleteSupportRequests($supportRequestIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportRequestsByProjectId($projectIds)
    {
        return $this->support_gateway->deleteSupportRequestsByProjectId($projectIds);
    }

    /**
     * @param $requestIds
     * @return mixed
     */
    public function deleteSupportPostsByRequestId($requestIds)
    {
        return $this->support_gateway->deleteSupportPostsByRequestId($requestIds);
    }

    /**
     * @param $supportPostIds
     * @return mixed
     */
    public function deleteSupportPostsById($supportPostIds)
    {
        return $this->support_gateway->deleteSupportPostsById($supportPostIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportPostsByProjectId($projectIds)
    {
        return $this->support_gateway->deleteSupportPostsByProjectId($projectIds);
    }

    /**
     * @param $postDetails
     * @return void
     * @throws Exception
     */
    public function sendPostChangedNotification($postDetails)
    {
        // Gather the needed information for populating the email template
        $requestDetail = $this->getSupportRequestById($postDetails["sp_request_id"]);
        $userDetail = $this->members->getMemberById($requestDetail["sr_member"]);
        $teamMembers = $this->teams->getTeamByProjectId($postDetails["sp_project"]);


        $mail = $this->container->getNotification();

        $emailSubject = $this->strings["support"] . " " . $this->strings["support_id"] . ": " . $requestDetail["sr_id"];

        $emailMessage = <<<EMAIL_MESSAGE
{$this->strings["noti_support_status2"]}

{$this->strings["id"]} : {$requestDetail["sr_id"]}
{$this->strings["subject"]} : {$requestDetail["sr_subject"]}
{$this->strings["status"]} : {$this->requestStatus[$requestDetail["sr_status"]]}
{$this->strings["details"]} : 

EMAIL_MESSAGE;

        $from = [
            'email' => $userDetail["mem_email_work"],
            'name' => $userDetail["mem_name"]
        ];

        try {
            // We want to send a notification to all team members so everyone is informed and anyone can respond
            // if needed.
            foreach ($teamMembers as $teamMember) {
                // If there is no email address, then skip it
                if ($teamMember["tea_mem_email_work"]) {

                    $to = [
                        'email' => $teamMember["tea_mem_email_work"],
                        'name' => $teamMember["tea_mem_name"]
                    ];

                    if ($teamMember["tea_mem_profil"] == 3) {
                        $emailMessage .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $postDetails["sp_project"] . "\n\n";
                    } else {
                        $emailMessage .= "$this->root/general/login.php?url=support/viewrequest.php%3Fid={$requestDetail["sr_id"]}\n\n";
                    }

                    $mail->sendMessage($to, $from, $emailSubject, $emailMessage);
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Notifications Section
     * @param $requestDetail
     * @param $postDetails
     * @param $userDetails
     * @throws Exception
     */
    public function sendNewPostNotification($requestDetail, $postDetails, $userDetails)
    {
        if (
            $requestDetail
            && $postDetails
            && $userDetails
            && !empty($userDetails["mem_email_work"])
        ) {
            $mail = $this->container->getNotification();
            try {

                // Set the From field
                $mail->setFrom($userDetails["mem_email_work"], $userDetails["mem_name"]);

                // Start building the Subject and Body
                $mail->partSubject = $this->strings["support"] . " " . $this->strings["support_id"];
                $mail->partMessage = $this->strings["noti_support_post2"];


                // Set the subject
                $subject = $mail->partSubject . ": " . $requestDetail["sr_id"];

                // Build the Email Body
                $body = <<<MAILBODY
{$mail->partMessage}

{$this->strings["id"]} : {$requestDetail["sr_id"]}
{$this->strings["subject"]} : {$requestDetail["sr_subject"]}
{$this->strings["status"]} : {$GLOBALS["requestStatus"][$requestDetail["sr_status"]]}

{$this->strings["details"]} : 

MAILBODY;

                if (isset($listTeam) && $listTeam["tea_mem_profil"] == 3) {
                    $body .= $this->root . "/general/login.php?url=projects_site/home.php%3Fproject=" . $requestDetail["sr_project"] . "\n\n";
                } else {
                    $body .= $this->root . "/general/login.php?url=support/viewrequest.php%3Fid=" . $requestDetail["sr_id"] . "\n\n";
                }
                $body .= $this->strings["message"] . " : " . $postDetails["sp_message"] . "";


                $body .= "\n\n" . $mail->footer;

                $mail->Subject = $subject;
                $mail->Priority = "3";
                $mail->Body = $body;
                $mail->AddAddress($userDetails["mem_email_work"], $userDetails["mem_name"]);
                $mail->Send();
                $mail->ClearAddresses();


            } catch (Exception $e) {
                throw new Exception($mail->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }

    }
}
