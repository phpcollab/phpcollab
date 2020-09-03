<?php


namespace phpCollab\Files;

use Exception;
use InvalidArgumentException;
use phpCollab\Database;
use phpCollab\Notification;


class PeerReview extends Files
{
    private $projectDetails;
    private $fileDetails;
    private $notifications;

    public function __construct(Database $database, Notification $notification)
    {
        parent::__construct($database);
        $this->notifications = $notification;
    }

    /**
     * @param $notificationDetails
     * @param $comment
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws Exception
     */
    public function sendEmail($notificationDetails, $comment)
    {
        if ($this->fileDetails && $this->projectDetails && $notificationDetails) {

            $mail = $this->notifications;

            $mail->setFrom($this->projectDetails["pro_mem_email_work"], $this->projectDetails["pro_mem_name"]);

            $mail->partSubject = $this->strings["noti_peer_review_added"];

            $subject = $mail->partSubject . " ({$this->fileDetails["fil_name"]})";

            if ($this->projectDetails["pro_org_id"] == "1") {
                $this->projectDetails["pro_org_name"] = $this->strings["none"];
            }

            if (
                (
                    ($notificationDetails["organization"] != "1")
                    && ($this->fileDetails["fil_published"] == "0")
                    && ($this->projectDetails["pro_published"] == "0")
                ) || ($notificationDetails["organization"] == "1")
            ) {

                if (!empty($notificationDetails["email_work"])) {
                    $body = <<< BODY
{$this->strings["noti_peer_review_added"]}

{$this->strings["noti_peer_review_details"]}
============
{$this->strings["comments"]}:
{$comment}


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
                    $mail->AddAddress($notificationDetails["email_work"], $notificationDetails["name"]);
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
