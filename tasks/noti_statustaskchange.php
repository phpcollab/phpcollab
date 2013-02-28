<?php
$tmpquery = "WHERE tas.id IN($id)";
$taskNoti = new request();
$taskNoti->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '$project'";
$projectNoti = new request();
$projectNoti->openProjects($tmpquery);

if (isset($task_owner) && is_numeric($task_owner)) {
        $tmpquery = "WHERE noti.member IN($task_owner)";
}
else {
        $tmpquery = "WHERE noti.member IN($at)";
} 

$listNotifications = new request();
$listNotifications->openNotifications($tmpquery);
$comptListNotifications = count($listNotifications->not_id);

if ($listNotifications->not_statustaskchange[0] == "0") {
$mail = new notification();

$mail->getUserinfo($idSession,"from");

$mail->partSubject = $strings["noti_statustaskchange1"];
$mail->partMessage = $strings["noti_statustaskchange2"];

	if ($projectNoti->pro_org_id[0] == "1") {
		$projectNoti->pro_org_name[0] = $strings["none"];
	}
	$complValue = ($taskNoti->tas_completion[0]>0) ? $taskNoti->tas_completion[0]."0 %": $taskNoti->tas_completion[0]." %"; 
	$idStatus = $taskNoti->tas_status[0];
	$idPriority = $taskNoti->tas_priority[0];

	$body = $mail->partMessage."\n\n".$strings["task"]." : ".$taskNoti->tas_name[0]."\n".$strings["start_date"]." : ".$taskNoti->tas_start_date[0]."\n".$strings["due_date"]." : ".$taskNoti->tas_due_date[0]."\n".$strings["completion"]." : ".$complValue."\n".$strings["priority"]." : $priority[$idPriority]\n".$strings["status"]." : $status[$idStatus]\n".$strings["description"]." : ".$taskNoti->tas_description[0]."\n\n".$strings["project"]." : ".$projectNoti->pro_name[0]." (".$projectNoti->pro_id[0].")\n".$strings["organization"]." : ".$projectNoti->pro_org_name[0]."\n\n".$strings["noti_moreinfo"]."\n"; 

	if ($taskNoti->tas_mem_organization[0] == "1") { 
		$body .= "$root/general/login.php?url=tasks/viewtask.php%3Fid=$id";
	} else if ($taskNoti->tas_mem_organization[0] != "1" && $projectNoti->pro_published[0] == "0" && $taskNoti->tas_published[0] == "0") { 
		$body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=".$projectNoti->pro_id[0];
	} 

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