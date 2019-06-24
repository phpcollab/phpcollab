<?php


namespace phpCollab\Notifications;


use Exception;
use phpCollab\Notification;

class TopicNewTopic extends Notification
{
    /**
     * @param $topicDetail
     * @param $projectDetail
     * @param $notificationsList
     * @throws Exception
     */
    public function generateEmail($topicDetail, $projectDetail, $notificationsList)
    {
        if ($topicDetail) {

            try {
                $this->getUserinfo($_SESSION["idSession"], "from");

                $this->partSubject = $this->strings["noti_newtopic1"];
                $this->partMessage = $this->strings["noti_newtopic2"];

                $this->Subject = $this->strings["noti_newtopic1"] . " " . $topicDetail["top_subject"];

                if ($projectDetail["pro_org_id"] == "1") {
                    $projectDetail["pro_org_name"] = $this->strings["none"];
                }

                $body = $this->partMessage . "\n\n";
                $body .= $this->strings["discussion"] . " : {$topicDetail["top_subject"]} \n";
                $body .= $this->strings["posted_by"] . " : {$_SESSION["nameSession"]} ({$_SESSION["loginSession"]})\n\n";
                $body .= $this->strings["project"] . " : {$projectDetail["pro_name"]} ({$projectDetail["pro_id"]})\n";
                $body .= $this->strings["organization"] . " : {$projectDetail["pro_org_name"]}\n\n";
                $body .= $this->strings["noti_moreinfo"] . "\n";

                $this->Priority = "3";

                if ($notificationsList) {
                    foreach ($notificationsList as $notificationList) {
                        if (
                            ($notificationList["organization"] != "1"
                                && $topicDetail["top_published"] == "0"
                                && $projectDetail["pro_published"] == "0")
                            || $notificationList["organization"] == "1"
                        ) {
                            if ($notificationList["newPost"] == "0" && $notificationList["email_work"] != "") {

                                if ($notificationList["organization"] == "1") {
                                    $body .= $this->root . "/general/login.php?url=topics/viewtopic.php%3Fid=" . $topicDetail["top_id"];
                                } elseif ($notificationList["organization"] != "1") {
                                    $body .= $this->root . "/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetail["pro_id"];
                                }

                                $body .= "\n\n" . $this->footer;

                                $this->Body = $body;
                                $this->AddAddress($notificationList["email_work"], $notificationList["name"]);
                                $this->Send();
                                $this->ClearAddresses();
                            }
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