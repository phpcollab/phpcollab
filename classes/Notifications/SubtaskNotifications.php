<?php


namespace phpCollab\Notifications;

use Exception;
use Monolog\Logger;
use phpCollab\Notification;
use Symfony\Component\HttpFoundation\Session\Session;

class SubtaskNotifications extends Notification
{
    protected $emailSubject;
    protected $emailBody;
    protected $taskDetails;
    protected $projectDetails;
    protected $workEmail;
    protected $userName;

    /**
     * @param Session $session
     * @param Logger $logger
     * @throws Exception
     */
    public function sendEmail(Session $session, Logger $logger)
    {
        if ($this->workEmail && $this->emailSubject && $this->emailBody && $this->taskDetails && $this->projectDetails) {
            try {
                $this->getUserinfo($session->get("id"), "from", $logger);

                $tmpSubject = $this->emailSubject . " " . $this->taskDetails["subtas_name"];
                $this->partMessage = $this->emailBody;

                $this->Subject = substr($tmpSubject, 0, 50) . "...";


                $complValue = ($this->taskDetails["subtas_completion"] > 0) ? $this->taskDetails["subtas_completion"] . "0 %" : $this->taskDetails["subtas_completion"] . " %";

                $idStatus = $this->taskDetails["subtas_status"];
                $idPriority = $this->taskDetails["subtas_priority"];

                $body = <<<MESSAGE_BODY
$this->partMessage

{$this->strings["subtask"]} : {$this->taskDetails["subtas_name"]}
{$this->strings["start_date"]} : {$this->taskDetails["subtas_start_date"]}
{$this->strings["due_date"]} : {$this->taskDetails["subtas_due_date"]}
{$this->strings["completion"]} : $complValue
{$this->strings["priority"]} : {$this->priority[$idPriority]}
{$this->strings["status"]} : {$this->status[$idStatus]}
{$this->strings["description"]} : {$this->taskDetails["subtas_description"]}

{$this->strings["project"]} : {$this->projectDetails["pro_name"]} ({$this->projectDetails["pro_id"]})
{$this->strings["task"]} : {$this->taskDetails["subtas_tas_name"]} ({$this->taskDetails["subtas_task"]})
{$this->strings["organization"]} : {$this->projectDetails["pro_org_name"]}\n

{$this->strings["noti_moreinfo"]}
MESSAGE_BODY;

                if ($this->taskDetails["subtas_priority"] == "4" || $this->taskDetails["subtas_priority"] == "5") {
                    $this->Priority = "1";
                } else {
                    $this->Priority = "3";
                }

                if ($this->taskDetails["subtas_mem_organization"] == "1") {
                    $body .= $this->root . "/general/login.php?url=subtasks/viewsubtask.php%3Fid={$this->taskDetails["subtas_id"]}%26task={$this->taskDetails["subtas_task"]}";
                } elseif ($this->taskDetails["subtas_mem_organization"] != "1" && $this->projectDetails["pro_published"] == "0" && $this->taskDetails["subtas_published"] == "0") {
                    $body .= "$this->root/general/login.php?url=projects_site/home.php%3Fproject=" . $this->projectDetails["pro_id"];
                }

                $body .= "\n\n" . $this->footer;

                $this->Body = $body;
                $this->AddAddress($this->workEmail, $this->userName);
                $this->Send();
                $this->ClearAddresses();
            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($this->ErrorInfo);
            }

        } else {
            throw new Exception('Error sending mail');
        }
    }

    /**
     * @param mixed $emailBody
     */
    public function setBody($emailBody): void
    {
        $this->emailBody = $emailBody;
    }

    /**
     * @param mixed $emailSubject
     */
    public function setSubject($emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }

    /**
     * @param mixed $taskDetails
     */
    public function setTaskDetails($taskDetails): void
    {
        $this->taskDetails = $taskDetails;
    }

    /**
     * @param mixed $projectDetails
     */
    public function setProjectDetails($projectDetails): void
    {
        $this->projectDetails = $projectDetails;
    }

    /**
     * @return mixed
     */
    public function getTaskDetails()
    {
        return $this->taskDetails;
    }

    /**
     * @return mixed
     */
    public function getProjectDetails()
    {
        return $this->projectDetails;
    }

    /**
     * @param mixed $workEmail
     */
    public function setWorkEmail($workEmail): void
    {
        $this->workEmail = $workEmail;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName): void
    {
        $this->userName = $userName;
    }

}
