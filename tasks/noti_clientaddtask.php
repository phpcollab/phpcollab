<?php
if ($num == "") {
$num = $id;
}
$tmpquery = "WHERE tas.id IN($num)";
$taskNoti = new request();
$taskNoti->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '".$taskNoti->tas_project[0]."'";
$projectNoti = new request();
$projectNoti->openProjects($tmpquery);

$tmpquery = "WHERE noti.member IN(".$projectNoti->pro_owner[0].")";
$listNotifications = new request();
$listNotifications->openNotifications($tmpquery);
$comptListNotifications = count($listNotifications->not_id);

if ($listNotifications->not_taskassignment[0] == "0") {
$mail = new notification();

$mail->getUserinfo($idSession,"from");

$mail->partSubject = $strings["noti_clientaddtask1"];
$mail->partMessage = $strings["noti_clientaddtask2"];

	if ($projectNoti->pro_org_id[0] == "1") {
		$projectNoti->pro_org_name[0] = $strings["none"];
	}
	$complValue = ($taskNoti->tas_completion[0]>0) ? $taskNoti->tas_completion[0]."0 %": $taskNoti->tas_completion[0]." %"; 
	$idStatus = $taskNoti->tas_status[0];
	$idPriority = $taskNoti->tas_priority[0];

	$body = $mail->partMessage."\n\n".$strings["task"]." : ".$taskNoti->tas_name[0]."\n".$strings["start_date"]." : ".$taskNoti->tas_start_date[0]."\n".$strings["due_date"]." : ".$taskNoti->tas_due_date[0]."\n".$strings["completion"]." : ".$complValue."\n".$strings["priority"]." : $priority[$idPriority]\n".$strings["status"]." : $status[$idStatus]\n".$strings["description"]." : ".$taskNoti->tas_description[0]."\n\n".$strings["project"]." : ".$projectNoti->pro_name[0]." (".$projectNoti->pro_id[0].")\n".$strings["organization"]." : ".$projectNoti->pro_org_name[0]."\n\n".$strings["noti_moreinfo"]."\n"; 

	$body .= "$root/general/login.php?url=tasks/viewtask.php%3Fid=$num";

	$body .= "\n\n".$mail->footer;

	$subject = $mail->partSubject." ".$taskNoti->tas_name[0];

	$mail->Subject = $subject;
	if ($taskNoti->tas_priority[0] == "4" || $taskNoti->tas_priority[0] == "5") { 
		$mail->Priority = "1";
	} else { 
		$mail->Priority = "3";
	} 
	$mail->Body = $body;
	$mail->AddAddress($listNotifications->not_mem_email_work[0], $listNotifications->not_mem_name[0]);
	$mail->Send();
	$mail->ClearAddresses();
}
?>