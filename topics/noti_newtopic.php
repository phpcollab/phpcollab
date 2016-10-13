<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../topics/noti_newtopic.php
** Authors: Ceam / Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: noti_newtopic.php
**
** DESC: Screen: notification function for new topic
**
** HISTORY:
** 	26/01/2004	-	file comment added
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/
$tmpquery = "WHERE topic.id = '$num'";
$detailTopic = new phpCollab\Request();
$detailTopic->openTopics($tmpquery);

$tmpquery = "WHERE tea.project = '$project' AND tea.member != '$idSession' ORDER BY mem.id";
$listTeam = new phpCollab\Request();
$listTeam->openTeams($tmpquery);
$comptListTeam = count($listTeam->tea_id);

for ($i = 0; $i < $comptListTeam; $i++) {
    $posters .= $listTeam->tea_member[$i] . ",";
}
if (substr($posters, -1) == ",") {
    $posters = substr($posters, 0, -1);
}
//echo $posters;

if ($posters != "") {
    $tmpquery = "WHERE noti.member IN($posters)";
    $listNotifications = new phpCollab\Request();
    $listNotifications->openNotifications($tmpquery);
    $comptListNotifications = count($listNotifications->not_id);

    $mail = new phpCollab\Notification();

    $mail->getUserinfo($idSession, "from");

    $mail->partSubject = $strings["noti_newtopic1"];
    $mail->partMessage = $strings["noti_newtopic2"];

    $subject = $mail->partSubject . " " . $detailTopic->top_subject[0];

    if ($projectDetail->pro_org_id[0] == "1") {
        $projectDetail->pro_org_name[0] = $strings["none"];
    }

    for ($i = 0; $i < $comptListNotifications; $i++) {
        if (($listNotifications->not_mem_organization[$i] != "1" && $detailTopic->top_published[0] == "0" && $projectDetail->pro_published[0] == "0") || $listNotifications->not_mem_organization[$i] == "1") {
            if ($listNotifications->not_newtopic[$i] == "0" && $listNotifications->not_mem_email_work[$i] != "") {

                $body = $mail->partMessage . "\n\n" . $strings["discussion"] . " : " . $detailTopic->top_subject[0] . "\n" . $strings["posted_by"] . " : " . $nameSession . " (" . $loginSession . ")\n\n" . $strings["project"] . " : " . $projectDetail->pro_name[0] . " (" . $projectDetail->pro_id[0] . ")\n" . $strings["organization"] . " : " . $projectDetail->pro_org_name[0] . "\n\n" . $strings["noti_moreinfo"] . "\n";

                if ($listNotifications->not_mem_organization[$i] == "1") {
                    $body .= "$root/general/login.php?url=topics/viewtopic.php%3Fid=" . $detailTopic->top_id[0];
                } else if ($listNotifications->not_mem_organization[$i] != "1") {
                    $body .= "$root/general/login.php?url=projects_site/home.php%3Fproject=" . $projectDetail->pro_id[0];
                }

                $body .= "\n\n" . $mail->footer;

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