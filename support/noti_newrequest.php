<?php
$mail = new notification();

$mail->getUserinfo($idSession,"from");

$tmpquery = "WHERE sr.id = '$num'";
$requestDetail = new request();
$requestDetail->openSupportRequests($tmpquery);

if ($supportType == "team") {
			
	$tmpquery = "WHERE tea.project = '".$requestDetail->sr_project[0]."'";
	$listTeam = new request();
	$listTeam->openTeams($tmpquery);
	$comptListTeam = count($listTeam->tea_id);

	for ($i=0;$i<$comptListTeam;$i++) {
			if ($idSession == $listTeam->tea_mem_id[$i]) {
				$mail->partSubject = $strings["support"]." ".$strings["support_id"];
				$mail->partMessage = $strings["noti_support_request_new2"];
				$subject = $mail->partSubject.": ".$requestDetail->sr_id[0];
				$body = $mail->partMessage."";
				$body .= "".$requestDetail->sr_subject[0]."";
			} else {
				$mail->partSubject = $strings["support"]." ".$strings["support_id"];
				$mail->partMessage = $strings["noti_support_team_new2"];
				$subject = $mail->partSubject.": ".$requestDetail->sr_id[0];
				$body = $mail->partMessage."";
				$body .= "".$requestDetail->sr_pro_name[0]."";
			}
	
			$body .= "\n\n".$strings["id"]." : ".$requestDetail->sr_id[0]."\n".$strings["subject"]." : ".$requestDetail->sr_subject[0]."\n".$strings["status"]." : ".$requestStatus[$requestDetail->sr_status[0]]."\n".$strings["details"]." : ";
			if ($listTeam->tea_mem_profil[$i] == 3){
				$body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=".$requestDetail->sr_project[0]."\n\n";
			} else {
				$body .= "$root/general/login.php?url=support/viewrequest.php%3Fid=$num \n\n";
			}
			if ($listTeam->tea_mem_email_work[$i] != "") {
			$mail->Subject = $subject;
			$mail->Priority = "3";
			$mail->Body = $body;
			$mail->AddAddress($listTeam->tea_mem_email_work[$i], $listTeam->tea_mem_name[$i]);
			$mail->Send();
			$mail->ClearAddresses();
			}
	}
		
} else {
$tmpquery = "WHERE mem.id = '1'";
$userDetail = new request();
$userDetail->openMembers($tmpquery);

if ($userDetail->mem_email_work[0] != "") {
		$mail->partSubject = $strings["support"]." ".$strings["support_id"];
		$mail->partMessage = $strings["noti_support_request_new2"];
		$subject = $mail->partSubject.": ".$requestDetail->sr_id[0];
		$body = $mail->partMessage."";
		$body .= "".$requestDetail->sr_subject[0]."";
	
		$body .= "\n\n".$strings["id"]." : ".$requestDetail->sr_id[0]."\n".$strings["subject"]." : ".$requestDetail->sr_subject[0]."\n".$strings["status"]." : ".$requestStatus[$requestDetail->sr_status[0]]."\n".$strings["details"]." : ";
		$body .= "$root/general/login.php?url=support/viewrequest.php%3Fid=$num \n\n";

		$mail->Subject = $subject;
		$mail->Priority = "3";
		$mail->Body = $body;
		$mail->AddAddress($userDetail->mem_email_work[0], $userDetail->mem_name[0]);
		$mail->Send();
		$mail->ClearAddresses();
}
}
?>