<?php

$notificationsClass = $container->getNotificationsManager();

$taskNoti = $tasks->getTaskById($listTask["tas_id"]);

$projectNoti = $projects->getProjectById($listTask["tas_project"]);

$task_owner = $listTask["tas_owner"];

$listNotifications = $notificationsClass->getMemberNotifications($task_owner);

if ($listNotifications["taskAssignment"] == "0") {
    $mail = $container->getNotification();

    try {

        $mail->getUserinfo($session->get("id"), "from");

        $mail->partSubject = $strings["noti_taskassignment1"];
        $mail->partMessage = $strings["noti_taskassignment2"];

        if ($projectNoti["pro_org_id"] == "1") {
            $projectNoti["pro_org_name"] = $strings["none"];
        }

        $complValue = ($taskNoti["tas_completion"] > 0) ? $taskNoti["tas_completion"] . "0 %" : $taskNoti["tas_completion"] . " %";
        $idStatus = $taskNoti["tas_status"];
        $idPriority = $taskNoti["tas_priority"];

        $body = $mail->partMessage . "\n\n";
        $body .= $strings["task"] . " : " . $taskNoti["tas_name"] . "\n";
        $body .= $strings["start_date"] . " : " . $taskNoti["tas_start_date"] . "\n";
        $body .= $strings["due_date"] . " : " . $taskNoti["tas_due_date"] . "\n";
        $body .= $strings["completion"] . " : " . $complValue . "\n";
        $body .= $strings["priority"] . " : " . $GLOBALS["priority"][$idPriority] . "\n";
        $body .= $strings["status"] . " : " . $GLOBALS["status"][$idStatus] . "\n";
        $body .= $strings["description"] . " : " . $taskNoti["tas_description"] . "\n\n";
        $body .= $strings["project"] . " : " . $projectNoti["pro_name"] . " (" . $projectNoti["pro_id"] . ")\n";
        $body .= $strings["organization"] . " : " . $projectNoti["pro_org_name"] . "\n\n";
        $body .= $strings["noti_moreinfo"] . "\n";

        if ($taskNoti["tas_mem_organization"] == "1") {
            $body .= "$root/general/login.php?url=tasks/viewtask.php%3Fid=$id";
        } elseif ($taskNoti["tas_mem_organization"] != "1" && $projectNoti["pro_published"] == "0" && $taskNoti["tas_published"] == "0") {
            $body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectNoti["pro_id"];
        }

        $body .= "\n\n" . $mail->footer;

        $subject = $mail->partSubject . " " . $taskNoti["tas_name"];

        $mail->Subject = $subject;

        if ($taskNoti["tas_priority"] == "4" || $taskNoti["tas_priority"] == "5") {
            $mail->Priority = "1";
        } else {
            $mail->Priority = "3";
        }

        $mail->addAddress($listNotifications["email_work"], $listNotifications["name"]);

        $mail->Body = $body;
        $mail->send();
        $mail->clearAddresses();
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
