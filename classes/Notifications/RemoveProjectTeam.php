<?php


namespace phpCollab\Notifications;


use Exception;
use phpCollab\Notification;

class RemoveProjectTeam extends Notification
{
    /**
     * @param $projectDetail
     * @param $notificationsList
     * @throws Exception
     */
    public function generateEmail($projectDetail, $notificationsList)
    {
        if ($projectDetail) {

            try {
                $this->getUserinfo($GLOBALS["idSession"], "from");

                $this->partSubject = $this->strings["noti_removeprojectteam1"];
                $this->partMessage = $this->strings["noti_removeprojectteam2"];

                $this->Subject = $this->strings["noti_removeprojectteam1"] . " " . $projectDetail["pro_name"];

                if ($projectDetail["pro_org_id"] == "1") {
                    $projectDetail["pro_org_name"] = $this->strings["none"];
                }

                $body = $this->partMessage . "\n\n";
                $body .= $this->strings["project"] . " : " . $projectDetail["pro_name"] . " (" . $projectDetail["pro_id"] . ")\n";
                $body .= $this->strings["organization"] . " : " . $projectDetail["pro_org_name"] . "\n\n";
                $body .= $this->strings["noti_moreinfo"] . "\n";

                // This is hard coded, so it is always "1"
                $organization = "";
                if ($organization == "1") {
                    $body .= $this->root . "/general/login.php?url=projects/viewproject.php%3Fid=" . $projectDetail["pro_id"];
                } elseif ($organization != "1" && $projectDetail["pro_published"] == "0") {
                    $body .= $this->root;
                }

                $body .= "\n\n" . $this->footer;

                $this->Priority = "3";
                $this->Body = $body;

                if ($notificationsList) {
                    foreach ($notificationsList as $memberNotification) {
                        if ($memberNotification["removeProjectTeam"] == "0" && $memberNotification["email_work"] != "") {
                            $this->AddAddress($memberNotification["email_work"], $memberNotification["name"]);
                            $this->Send();
                            $this->ClearAddresses();
                        }
                    }

                }

            } catch (Exception $e) {
                // Log this instead of echoing it?
                throw new Exception($this->ErrorInfo);
            }
        } else {
            throw new Exception('Error sending mail');
        }
    }

}
