<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/emailusers.php

$checkSession = "true";
include_once('../includes/library.php');

/*
//anyone can send a message
if ($profilSession != "0") {
	header("Location:../general/permissiondenied.php?".session_name()."=".session_id());
	exit;
}
*/

if ($action == "email") 
{
	global $root,$version,$setCharset;

	// get name and email of user sending the email
	$tmpquery = "WHERE mem.id = '$idSession'";
	$userPrefs = new Request();
	$userPrefs->openMembers($tmpquery);

	// get company name
	$tmpquery = "WHERE org.id = '1'";
	$clientDetail = new Request();
	$clientDetail->openOrganizations($tmpquery);

	// get users to email
	$id = str_replace("**",",",$id);
	$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";
	$listMembers = new Request();
	$listMembers->openMembers($tmpquery);
	$comptListMembers = count($listMembers->mem_id);

	// format body and message
	$subject = stripslashes($subject);
	$message = stripslashes($message);
	$message = str_replace("\r\n","\n",$message); 

	for ($i=0;$i<$comptListMembers;$i++) 
	{
		// send email to each user
		$email = $listMembers->mem_email_work[$i];
		$priorityMail = "3"; 
		$headers = "Content-type:text/plain;charset='$setCharset'\nFrom: '".$userPrefs->mem_name[0]."' <".$userPrefs->mem_email_work[0].">\nX-Priority: $priorityMail\nX-Mailer: PhpCollab $version"; 
		
		$footer = "---\n".$strings["noti_foot1"];
		$signature = $userPrefs->mem_name[0]."\n";
		if ($userPrefs->mem_title[0] != "") { $signature .= $userPrefs->mem_title[0].", ".$clientDetail->org_name[0]."\n"; } else {$signature .= $clientDetail->org_name[0]."\n";}
		if ($userPrefs->mem_phone_work[0] != "") { $signature .= "Phone: ".$userPrefs->mem_phone_work[0]."\n"; }
		if ($userPrefs->mem_mobile[0] != "") { $signature .= "Mobile: ".$userPrefs->mem_mobile[0]."\n"; }
		if ($userPrefs->mem_fax[0] != "") { $signature .= "Fax: ".$userPrefs->mem_fax[0]."\n"; }
		$newmessage = $message;
		$newmessage .= "\n\n".$signature;
		$newmessage .= "\n".$footer;
		@mail($email, $subject, $newmessage, $headers);
		$newmessage = "";

	}

	if ($profilSession == "0") 
	{
		header("Location:../users/listusers.php?id=$clod&msg=email&".session_name()."=".session_id());
		exit;
	} 
	else 
	{
		header("Location:../general/home.php?msg=email&".session_name()."=".session_id());
		exit;
	}


	
}

// start main page
include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?",$strings["user_management"],in));
$blockPage->itemBreadcrumbs($strings["email_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include("../themes/".THEME."/msg.php");
}

$block1 = new Block();

$block1->form = "user_email";
$block1->openForm("../users/emailusers.php?action=email&".session_name()."=".session_id());

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["email_users"]);

$block1->openContent();
$block1->contentTitle($strings["email_following"]);

$id = str_replace("**",",",$id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";
$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

for ($i=0;$i<$comptListMembers;$i++) {

if ($listMembers->mem_email_work[$i] != "") {
	$block1->contentRow("",$listMembers->mem_login[$i]."&nbsp;(".$listMembers->mem_name[$i].")");
}	
else {
	$block1->contentRow("",$listMembers->mem_login[$i]."&nbsp;(".$listMembers->mem_name[$i].") ".$strings["no_email"]);
}
	
}

$block1->contentTitle($strings["email"]);
$block1->contentRow($strings["subject"],"<input size='44' style='width: 400px' name='subject' maxlength='100' type='text'>");
$block1->contentRow($strings["message"],"<textarea rows='10' style='width: 400px; height: 160px;' name='message' cols='47'></textarea>");
$block1->contentRow("","<input type='submit' name='delete' value='".$strings["email"]."'> <input type='button' name='cancel' value='".$strings["cancel"]."' onClick='history.back();'><input type='hidden' value='$id' name='id'>");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>