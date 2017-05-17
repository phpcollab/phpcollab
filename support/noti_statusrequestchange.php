<?php
$mail = new phpCollab\Notification();
$members = new \phpCollab\Members\Members();

$mail->getUserinfo($idSession, "from");

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new phpCollab\Request();
$requestDetail->openSupportRequests($tmpquery);

$userDetail = $members->getMemberById($requestDetail->sr_user[0]);

$mail->partSubject = $strings["support"] . " " . $strings["support_id"];
$mail->partMessage = $strings["noti_support_status2"];
$subject = $mail->partSubject . ": " . $requestDetail->sr_id[0];
$body = $mail->partMessage . "";

$body .= "\n\n" . $strings["id"] . " : " . $requestDetail->sr_id[0] . "\n" . $strings["subject"] . " : " . $requestDetail->sr_subject[0] . "\n" . $strings["status"] . " : " . $requestStatus[$requestDetail->sr_status[0]] . "\n" . $strings["details"] . " : ";
if ($listTeam->tea_mem_profil[$i] == 3) {
    $body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=" . $requestDetail->sr_project[0] . "\n\n";
} else {
    $body .= "$root/general/login.php?url=support/viewrequest.php%3Fid=$num \n\n";
}

$mail->Subject = $subject;
$mail->Priority = "3";
$mail->Body = $body;
$mail->AddAddress($userDetail["mem_email_work"], $userDetail["mem_name"]);
$mail->Send();
$mail->ClearAddresses();
