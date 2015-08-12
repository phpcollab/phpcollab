<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../project_site/noti_uploadfile.php
** Authors: Ceam / Fullo / Shaders
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: noti_uploadfile.php
**
** DESC: Screen: notification class
**
** HISTORY:
** 	26/01/2004	-	file notification
**  11/10/2004  -   fix bug http://phpcollab.sourceforge.net/viewtopic.php?t=1642
**  23/12/2004	-	fix notification for linked content
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$tmpquery = "WHERE fil.id = '$num'";
$detailFile = new request();
$detailFile->openFiles($tmpquery);

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

$tmpquery = "WHERE tea.project = '$project' AND tea.member != '$idSession' ORDER BY mem.id";

$listTeam = new request();
$listTeam->openTeams($tmpquery);

$comptListTeam = count($listTeam->tea_id);

for ($i=0;$i<$comptListTeam;$i++) {
	$posters .= $listTeam->tea_member[$i].",";
}


// echo $posters;


if (substr($posters, -1) == ",") { $posters = substr($posters, 0, -1); }

if ($posters != "") {

	$tmpquery = "WHERE noti.member IN ($posters)";

	$listNotifications = new request();
	$listNotifications->openNotifications($tmpquery);
	$comptListNotifications = count($listNotifications->not_id);

	$mail = new Notification();
	$mail->getUserinfo($idSession,"from");

	$mail->partSubject = $strings["noti_newfile1"];
	$mail->partMessage = $strings["noti_newfile2"];

	$subject = $mail->partSubject." ".$detailFile->fil_name[0];

	if ($projectDetail->pro_org_id[0] == "1") {
		$projectDetail->pro_org_name[0] = $strings["none"];
	}

	for ($i=0;$i<$comptListNotifications;$i++) {

		if ( ( ( $listNotifications->not_mem_organization[$i] != "1" ) && ( $detailTopic->fil_published[0] == "0" ) && ( $projectDetail->pro_published[0] == "0" ) ) || ( $listNotifications->not_mem_organization[$i] == "1" )	) {

			if ( ( $listNotifications->not_uploadfile[$i] == "0" ) && ( $listNotifications->not_mem_email_work[$i] != "") && ( $listNotifications->not_mem_id[$i] != $idSession) ) {

				$body = $mail->partMessage."\n\n".$strings["upload"]." : ".$detailFile->fil_name[0]."\n".$strings["posted_by"]." : ".$nameSession." (".$loginSession.")\n\n".$strings["project"]." : ".$projectDetail->pro_name[0]." (".$projectDetail->pro_id[0].")\n".$strings["organization"]." : ".$projectDetail->pro_org_name[0]."\n\n".$strings["noti_moreinfo"]."\n";

				if ($listNotifications->not_mem_organization[$i] == "1") { 
					$body .= "$root/general/login.php?url=linkedcontent/viewfile.php%3Fid=".$detailFile->fil_id[0];
				} else if ($listNotifications->not_mem_organization[$i] != "1") { 
					$body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=".$projectDetail->pro_id[0]; 
				}

				$body .= "\n\n".$mail->footer;

				$mail->Subject = $subject;
				$mail->Priority = "3";
				$mail->Body = $body;
				$mail->AddAddress($listNotifications->not_mem_email_work[$i], $listNotifications->not_mem_name[$i]);
				$mail->Send();
				$mail->ClearAddresses();



			}
		}
	}


}



?>