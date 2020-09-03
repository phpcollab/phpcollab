<?php


namespace phpCollab\Files;


use Exception;
use InvalidArgumentException;

class ApprovalTracking extends Files
{
    private $notifications;
    private $projectDetails;
    private $fileDetails;

    /**
     * @param integer $approverId
     * @param string $comment
     * @param integer $fileId
     * @param integer $fileStatus
     * @param null $approvalDate
     * @return mixed
     * @throws Exception
     */
    public function addApproval(int $approverId, string $comment, int $fileId, int $fileStatus, $approvalDate = null)
    {
        if (empty($approverId) || !is_int($approverId)) {
            throw new InvalidArgumentException('Approver ID is missing or invalid.');
        }

        if (is_null($approvalDate)) {
            $approvalDate = date('Y-m-d h:i');
        }
        try {
            return $this->addApprovalToDatabase($approverId, $comment, $fileId, $fileStatus, $approvalDate);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $fileId
     * @param $approverId
     * @param $comment
     * @param $approvalDate
     * @param $status
     * @return mixed
     */
    private function addApprovalToDatabase($approverId, $comment, $fileId, $status, $approvalDate)
    {
        $query = <<< SQL
UPDATE {$this->tableCollab["files"]} 
SET 
comments_approval = :comments_approval, 
date_approval = :date_approval, 
approver = :approver, 
status=:status 
WHERE id = :file_id
SQL;
        $this->db->query($query);
        $this->db->bind(":comments_approval", $comment);
        $this->db->bind(":approver", $approverId);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_approval", $approvalDate);
        $this->db->bind(":file_id", $fileId);
        return $this->db->execute();
    }

    /**
     * @param $notificationDetails
     * @param $comment
     * @param $status
     * @param $username
     * @param $name
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    public function sendEmail($notificationDetails, $comment, $status, $username, $name)
    {
        if ($this->fileDetails && $this->projectDetails && $notificationDetails) {
            $mail = $this->container->getNotification();

            $mail->setFrom($this->projectDetails["pro_mem_email_work"], $this->projectDetails["pro_mem_name"]);

            $mail->partSubject = $this->strings["noti_file_approval_added"];

            $subject = $mail->partSubject . " ({$this->fileDetails["fil_name"]})";

            if ($this->projectDetails["pro_org_id"] == "1") {
                $this->projectDetails["pro_org_name"] = $this->strings["none"];
            }

            if ($notificationDetails["tea_org2_id"] != "1") {
                if (!empty($notificationDetails["tea_mem_email_work"])) {
                    $body = <<< BODY
{$this->strings["noti_file_approval_added"]}

{$this->strings["approval_details"]}
============
{$this->strings["status"]}: {$status}
{$this->strings["comments"]}:
{$comment}

{$this->strings["approver"]}: {$name} ({$username})



{$this->strings["file_details"]}
============
{$this->strings["file"]}: {$this->fileDetails["fil_name"]}

{$this->strings["project"]} : {$this->projectDetails["pro_name"]} ({$this->projectDetails["pro_id"]})
{$this->strings["organization"]} : {$this->projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]} 
BODY;

                    if ($notificationDetails["organization"] == "1") {
                        $body .= $this->root . "/general/login.php?url=linkedcontent/viewfile.php?id=" . $this->fileDetails["fil_id"];
                    } elseif ($notificationDetails["organization"] != "1") {
                        $body .= $this->root . "/general/login.php?url=projects_site/home.php?project=" . $this->projectDetails["pro_id"];
                    }

                    $body .= "\n\n" . $mail->footer;

                    $mail->Subject = $subject;
                    $mail->Priority = "3";
                    $mail->Body = $body;
                    $mail->AddAddress($notificationDetails["tea_mem_email_work"], $notificationDetails["tea_mem_name"]);
                    $mail->Send();
                    $mail->ClearAddresses();
                }
            }
        } else {
            if (empty($fileDetails)) {
                throw new InvalidArgumentException('File Details is missing or empty.');
            } else {
                if (empty($projectDetails)) {
                    throw new InvalidArgumentException('Project Details is missing or empty.');
                } else {
                    if (empty($notificationDetails)) {
                        throw new InvalidArgumentException('Notification Details is missing or empty.');
                    } else {
                        throw new Exception('Error sending file uploaded notification');
                    }
                }
            }
        }
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications): void
    {
        $this->notifications = $notifications;
    }

    /**
     * @param mixed $projectDetails
     */
    public function setProjectDetails($projectDetails): void
    {
        $this->projectDetails = $projectDetails;
    }

    /**
     * @param mixed $fileDetails
     */
    public function setFileDetails($fileDetails): void
    {
        $this->fileDetails = $fileDetails;
    }
}
