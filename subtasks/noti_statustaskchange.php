<?php
$tmpquery = "WHERE subtas.id IN($id)";
$subtaskNoti = new Request();
$subtaskNoti->openSubtasks($tmpquery);

$tmpquery = "WHERE tas.id = '".$subtaskNoti->subtas_task[0]."'";
$taskNoti = new Request();
$taskNoti->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '".$taskNoti->tas_project[0]."'";
$projectNoti = new Request();
$projectNoti->openProjects($tmpquery);

$tmpquery = "WHERE noti.member IN($at)";
$listNotifications = new Request();
$listNotifications->openNotifications($tmpquery);
$comptListNotifications = count($listNotifications->not_id);

if ($listNotifications->not_statustaskchange[0] == "0") {
$mail = new Notification();

$mail->getUserinfo($idSession,"from");

$mail->partSubject = $strings["noti_statustaskchange1"];
$mail->partMessage = $strings["noti_statustaskchange2"];

	if ($projectNoti->pro_org_id[0] == "1") {
		$projectNoti->pro_org_name[0] = $strings["none"];
	}
	$complValue = ($subtaskNoti->subtas_completion[0]>0) ? $subtaskNoti->subtas_completion[0]."0 %": $subtaskNoti->subtas_completion[0]." %"; 
	$idStatus = $subtaskNoti->subtas_status[0];
	$idPriority = $subtaskNoti->subtas_priority[0];

	$body = $mail->partMessage."\n\n".$strings["subtask"]." : ".$subtaskNoti->subtas_name[0]."\n".$strings["start_date"]." : ".$subtaskNoti->subtas_start_date[0]."\n".$strings["due_date"]." : ".$subtaskNoti->subtas_due_date[0]."\n".$strings["completion"]." : ".$complValue."\n".$strings["priority"]." : $priority[$idPriority]\n".$strings["status"]." : $status[$idStatus]\n".$strings["description"]." : ".$subtaskNoti->subtas_description[0]."\n\n".$strings["project"]." : ".$projectNoti->pro_name[0]." (".$projectNoti->pro_id[0].")\n".$strings["task"]." : ".$taskNoti->tas_name[0]." (".$taskNoti->tas_id[0].")\n".$strings["organization"]." : ".$projectNoti->pro_org_name[0]."\n\n".$strings["noti_moreinfo"]."\n"; 

	if ($subtaskNoti->subtas_mem_organization[0] == "1") { 
		$body .= "$root/general/login.php?url=subtasks/viewsubtask.php%3Fid=$id%26task=".$taskNoti->tas_id[0];
	} else if ($subtaskNoti->subtas_mem_organization[0] != "1" && $projectNoti->pro_published[0] == "0" && $subtaskNoti->subtas_published[0] == "0") { 
		$body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=".$projectNoti->pro_id[0];
	} 

	$body .= "\n\n".$mail->footer;

	$subject = $mail->partSubject." ".$subtaskNoti->subtas_name[0];

	$mail->Subject = $subject;
	if ($subtaskNoti->subtas_priority[0] == "4" || $subtaskNoti->subtas_priority[0] == "5") { 
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