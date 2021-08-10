<?php

if (isset($num) && $num == "") {
    $num = $id;
}

try {
    $tasks = $container->getTasksLoader();
    $projects = $container->getProjectsLoader();
    $notifications = $container->getNotificationsManager();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$strings = $GLOBALS["strings"];

$taskNoti = $tasks->getTaskById($num);

$projectNoti = $projects->getProjectById($taskNoti["tas_project"]);

$listNotifications = $notifications->getNotificationsWhereMemberIn($projectNoti["pro_owner"]);

if ($listNotifications["not_taskassignment"] == "0") {
    $mail = $container->getNotification();

    $mail->getUserinfo($session->get("id"), "from", $container->getLogger());

    $mail->partSubject = $strings["noti_clientaddtask1"];
    $mail->partMessage = $strings["noti_clientaddtask2"];

    if ($projectNoti["pro_org_id"] == "1") {
        $projectNoti["pro_org_name"] = $strings["none"];
    }
    $complValue = ($taskNoti["tas_completion"] > 0) ? $taskNoti["tas_completion"] . "0 %" : $taskNoti["tas_completion"] . " %";
    $idStatus = $taskNoti["tas_status"];
    $idPriority = $taskNoti["tas_priority"];

    $body = $mail->partMessage . "\n\n" . $strings["task"] . " : " . $taskNoti["tas_name"] . "\n" . $strings["start_date"] . " : " . $taskNoti["tas_start_date"] . "\n" . $strings["due_date"] . " : " . $taskNoti["tas_due_date"] . "\n" . $strings["completion"] . " : " . $complValue . "\n" . $strings["priority"] . " : $priority[$idPriority]\n" . $strings["status"] . " : $status[$idStatus]\n" . $strings["description"] . " : " . $taskNoti["tas_description"] . "\n\n" . $strings["project"] . " : " . $projectNoti["pro_name"] . " (" . $projectNoti["pro_id"] . ")\n" . $strings["organization"] . " : " . $projectNoti["pro_org_name"] . "\n\n" . $strings["noti_moreinfo"] . "\n";

    $body .= "$root/general/login.php?url=tasks/viewtask.php%3Fid=$num";

    $body .= "\n\n" . $mail->footer;

    $subject = $mail->partSubject . " " . $taskNoti["tas_name"];

    $mail->Subject = $subject;
    if ($taskNoti["tas_priority"] == "4" || $taskNoti["tas_priority"] == "5") {
        $mail->Priority = "1";
    } else {
        $mail->Priority = "3";
    }
    $mail->Body = $body;
    try {
        $mail->AddAddress($listNotifications["not_mem_email_work"], $listNotifications["not_mem_name"]);
        $mail->Send();
        $mail->ClearAddresses();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $logger->critical('PHPMailer: ' . $e->getMessage());
    }
}
