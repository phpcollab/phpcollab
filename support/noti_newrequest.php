<?php
$supportRequests = new \phpCollab\Support\Support();

$mail = new phpCollab\Notification();

$mail->getUserinfo($_SESSION["idSession"], "from");
$num = $_GET["num"];

$strings = $GLOBALS["strings"];

$requestDetail = $supportRequests->getSupportRequestById($num);

if ($supportType == "team") {
    $teams = new \phpCollab\Teams\Teams();
    
    $listTeam = $teams->getTeamByProjectIdAndOrderedBy($requestDetail["sr_project"]);

    foreach ($listTeam as $teamMember) {
        if ($_SESSION["idSession"] == $teamMember["tea_mem_id"]) {
            $mail->partSubject = $strings["support"] . " " . $strings["support_id"];
            $mail->partMessage = $strings["noti_support_request_new2"];
            $subject = $mail->partSubject . ": " . $requestDetail["sr_id"];
            $body = $mail->partMessage . "";
            $body .= "" . $requestDetail["sr_subject"] . "";
        } else {
            $mail->partSubject = $strings["support"] . " " . $strings["support_id"];
            $mail->partMessage = $strings["noti_support_team_new2"];
            $subject = $mail->partSubject . ": " . $requestDetail["sr_id"];
            $body = $mail->partMessage . "";
            $body .= "" . $requestDetail["sr_pro_name"] . "";
        }

        $body .= "\n\n" . $strings["id"] . " : " . $requestDetail["sr_id"] . "\n" . $strings["subject"] . " : " . $requestDetail["sr_subject"] . "\n" . $strings["status"] . " : " . $requestStatus[$requestDetail["sr_status"]] . "\n" . $strings["details"] . " : ";
        if ($teamMember["tea_mem_profil"] == 3) {
            $body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=" . $requestDetail["sr_project"] . "\n\n";
        } else {
            $body .= "$root/general/login.php?url=support/viewrequest.php%3Fid=$num \n\n";
        }
        if ($teamMember["tea_mem_email_work"] != "") {
            $mail->Subject = $subject;
            $mail->Priority = "3";
            $mail->Body = $body;
            $mail->AddAddress($teamMember["tea_mem_email_work"], $teamMember["tea_mem_name"]);
            $mail->Send();
            $mail->ClearAddresses();
        }
    }

} else {
    $members = new \phpCollab\Members\Members();
    $userDetail = $members->getMemberById(1);

    if ($userDetail["mem_email_work"] != "") {
        $mail->partSubject = $strings["support"] . " " . $strings["support_id"];
        $mail->partMessage = $strings["noti_support_request_new2"];
        $subject = $mail->partSubject . ": " . $requestDetail["sr_id"];
        $body = $mail->partMessage . "";
        $body .= "" . $requestDetail["sr_subject"] . "";

        $body .= "\n\n" . $strings["id"] . " : " . $requestDetail["sr_id"] . "\n" . $strings["subject"] . " : " . $requestDetail["sr_subject"] . "\n" . $strings["status"] . " : " . $requestStatus[$requestDetail["sr_status"]] . "\n" . $strings["details"] . " : ";
        $body .= "$root/general/login.php?url=support/viewrequest.php%3Fid=$num \n\n";

        $mail->Subject = $subject;
        $mail->Priority = "3";
        $mail->Body = $body;
        $mail->AddAddress($userDetail["mem_email_work"], $userDetail["mem_name"]);
        $mail->Send();
        $mail->ClearAddresses();
    }
}
