<?php


namespace phpCollab\Files;


use Exception;
use InvalidArgumentException;

class UpdateFile extends Files
{
    /**
     * @var
     */
    private $notifications;
    /**
     * @var
     */
    private $projectDetails;
    /**
     * @var
     */
    private $fileDetails;

    /**
     * @param $owner
     * @param $project
     * @param $task
     * @param $name
     * @param $date
     * @param $size
     * @param $extension
     * @param $comments
     * @param $approver
     * @param $approvalComments
     * @param $approvalDate
     * @param $upload
     * @param $published
     * @param $vc_version
     * @param $vc_parent
     * @param int $status
     * @param int $vc_status
     * @return string
     * @throws Exception
     */
    public function add(
        $owner,
        $project,
        $task,
        $name,
        $date,
        $size,
        $extension,
        $comments,
        $approver,
        $approvalComments,
        $approvalDate,
        $upload,
        $published,
        $vc_version,
        $vc_parent,
        int $status = 2,
        int $vc_status = 3
    ): string {
        if (empty($approver)) {
            $approver = 0;
        } elseif (!is_int(filter_var($approver, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('Approver ID is missing or invalid.');
        }

        if (empty($approvalComments)) {
            $approvalComments = null;
        }

        try {
            return $this->addUpdatedFileToDatabase($owner, $project, $task, $name, $date, $size, $extension, $comments,
                $approver, $approvalComments, $approvalDate, $upload, $published,
                $status, $vc_status, $vc_version, $vc_parent);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $comments
     * @param $status
     * @param $vcVersion
     * @param $fileId
     * @return mixed
     */
    public function update($comments, $status, $vcVersion, $fileId)
    {
        if (!is_int(filter_var($fileId, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('File ID is missing or invalid.');
        }
        $timestamp = date('Y-m-d h:i');
        return $this->updateFileInDatabase($timestamp, $comments, $status, $vcVersion, $fileId);
    }

    /**
     * @param int $fileId
     * @param int $fileSize
     * @return mixed
     */
    public function setFileSize(int $fileId, int $fileSize)
    {
        if (!is_int(filter_var($fileId, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('File ID is missing or invalid.');
        }
        if (!is_int(filter_var($fileSize, FILTER_VALIDATE_INT))) {
            throw new InvalidArgumentException('File Size is missing or invalid.');
        }

        return $this->updateSize($fileId, $fileSize);
    }

    /**
     * @param $fileId
     * @param $size
     * @return mixed
     */
    private function updateSize($fileId, $size)
    {
        $sql = <<<SQL
UPDATE {$this->db->getTableName("files")} SET size = :size
WHERE id = :file_id
SQL;
        $this->db->query($sql);
        $this->db->bind(":file_id", $fileId);
        $this->db->bind(":size", $size);
        return $this->db->execute();
    }

    /**
     * @param $timestamp
     * @param $comments
     * @param $status
     * @param $vcVersion
     * @param $fileId
     * @return mixed
     */
    private function updateFileInDatabase($timestamp, $comments, $status, $vcVersion, $fileId)
    {
        $sql = <<<SQL
UPDATE {$this->db->getTableName("files")} SET 
date = :date,
comments = :comments,
comments_approval = null,
approver = null,
date_approval = null,
status = :status,
vc_version = :vc_version
WHERE id = :file_id
SQL;
        $this->db->query($sql);
        $this->db->bind(":date", $timestamp);
        $this->db->bind(":comments", $comments);
        $this->db->bind(":status", $status);
        $this->db->bind(":vc_version", $vcVersion);
        $this->db->bind(":file_id", $fileId);
        return $this->db->execute();
    }

    /**
     * @param $owner
     * @param $project
     * @param $task
     * @param $name
     * @param $date
     * @param $size
     * @param $extension
     * @param $comments
     * @param $approver
     * @param $approverComments
     * @param $date_approval
     * @param $upload
     * @param $published
     * @param $status
     * @param $vc_status
     * @param $vc_version
     * @param $vc_parent
     * @return string
     */
    private function addUpdatedFileToDatabase(
        $owner,
        $project,
        $task,
        $name,
        $date,
        $size,
        $extension,
        $comments,
        $approver,
        $approverComments,
        $date_approval,
        $upload,
        $published,
        $status,
        $vc_status,
        $vc_version,
        $vc_parent
    ): string {
        $sql = <<< SQL
INSERT INTO {$this->db->getTableName("files")} 
(owner, project, task, name, date, size, extension, comments, comments_approval, approver, date_approval, upload, published, status, vc_status, vc_version, vc_parent)
VALUES 
(:owner, :project, :task, :name, :date, :size, :extension, :comments, :approval_comments, :approver, :approval_date, :upload, :published, :status, :vc_status, :vc_version, :vc_parent)
SQL;

        $this->db->query($sql);
        $this->db->bind("owner", $owner);
        $this->db->bind("project", $project);
        $this->db->bind("task", $task);
        $this->db->bind("name", $name);
        $this->db->bind("date", $date);
        $this->db->bind("size", $size);
        $this->db->bind("extension", $extension);
        $this->db->bind("comments", $comments);
        $this->db->bind("approval_comments", $approverComments);
        $this->db->bind("approver", $approver);
        $this->db->bind("approval_date", $date_approval);
        $this->db->bind("upload", $upload);
        $this->db->bind("published", $published);
        $this->db->bind("status", $status);
        $this->db->bind("vc_status", $vc_status);
        $this->db->bind("vc_version", $vc_version);
        $this->db->bind("vc_parent", $vc_parent);
        $this->db->execute();
        return $this->db->lastInsertId();
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
            $mail = $this->container->getNotification();

            $mail->setFrom($this->projectDetails["pro_mem_email_work"], $this->projectDetails["pro_mem_name"]);

            $mail->partSubject = $this->strings["noti_file_update_added"];

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
{$this->strings["noti_file_update_added"]}

{$this->strings["noti_file_update_details"]}
============
{$this->strings["comments"]}:
$comment


{$this->strings["file_details"]}
============
{$this->strings["file"]}: {$this->fileDetails["fil_name"]}

{$this->strings["project"]} : {$this->projectDetails["pro_name"]} ({$this->projectDetails["pro_id"]})
{$this->strings["organization"]} : {$this->projectDetails["pro_org_name"]}

{$this->strings["noti_moreinfo"]}
BODY;

                    if ($notificationDetails["organization"] == "1") {
                        $body .= $this->root . "/general/login.php?url=linkedcontent/viewfile.php?id=" . $this->fileDetails["fil_id"];
                    }
                    if ($notificationDetails["organization"] != "1") {
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
