<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../newsdesk/editmessage.php
** Authors: Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editmessage.php
**
** DESC: 
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	fixed multi delete 
**	23/03/2004	-	xhtml code
**  23/08/2004  -   fix error "Using $this when not in object context"
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

//if ($profilSession != "0" && $profilSession != "2" ) {
//	header("Location:../general/permissiondenied.php?".session_name()."=".session_id());
//	exit;
//}

//case update post
if ($id != "") {

	//test exists selected client organization, redirect to list if not
	$tmpquery = "WHERE newscom.id = '$id'";
	$commentDetail = new request();
	$commentDetail->openNewsDeskComments($tmpquery);
	$comptcommentDetail = count($commentDetail->newscom_id);
	
	if ($comptcommentDetail == "0") {
			Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=blankNews&".session_name()."=".session_id());
			exit;
	}

	// only comment's author, admin, prj-adm and prj-man can change the comments
	$tmpquery_user = "WHERE mem.id = '".$commentDetail->newscom_name[0]."' ";
	$commentAuthor = new request();
	$commentAuthor->openMembers($tmpquery_user);

	if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5" && $idSession != $commentDetail->newscom_name[0] ) {
			Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=commentpermissionNews&".session_name()."=".session_id());
			exit;
	}


	if ($action == "update") {
		$title = Util::convertData($title);
		$content = Util::convertData($content);
		$tmpquery = "UPDATE ".$tableCollab["newsdeskcomments"]." SET comment = '$comment' WHERE id = '$id'";
		Util::connectSql("$tmpquery");
		Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=update&".session_name()."=".session_id());
	}
	elseif ($action == "delete") {
		// only admin, prj-adm and prj-man can delete a comments
		if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5" ) {
			Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=commentpermissionNews&".session_name()."=".session_id());
			exit;
		}

		$id = str_replace("**",",",$id);
		$tmpquery = "DELETE FROM ".$tableCollab["newsdeskcomments"]." WHERE id IN('$id')";
		Util::connectSql("$tmpquery");
		Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=removeComment&".session_name()."=".session_id());
	}	
	else {
		//set value in form
		$name = $commentDetail->newscom_name[0];
		$comment = $commentDetail->newscom_comment[0];
	}

} else { // case of adding new post
	
	if ($action == "add") {

		//test if name blank
		if ($comment == "") {
			$error = $strings["blank_newsdesk_comment"];
		} else {

			//replace quotes by html code in name and address
			$name = Util::convertData($name);
			$comment = Util::convertData($comment);

			//insert into organizations and redirect to new client organization detail (last id)
			$tmpquery1 = "INSERT INTO ".$tableCollab["newsdeskcomments"]."(name,post_id,comment) VALUES ('$name','$postid' , '".addslashes($comment)."')";

			Util::connectSql("$tmpquery1");
			$tmpquery = $tableCollab["newsdeskcomments"];
			Util::getLastId($tmpquery);
			$num = $lastId[0];
			unset($lastId);
	
			Util::headerFunction("../newsdesk/viewnews.php?id=$postid&msg=add&".session_name()."=".session_id());

		}
	}
}


include('../themes/'.THEME.'/header.php');

$tmpquery2 = "WHERE news.id = '$postid'";
$newsDetail = new request();
$newsDetail->openNewsDesk($tmpquery2);

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?",$strings["newsdesk"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=$postid",$newsDetail->news_title[0],in));

if ($id == "") {
	$blockPage->itemBreadcrumbs($strings["add_newsdesk_comment"]);
} elseif ($action == "remove") {
	$blockPage->itemBreadcrumbs($commentAuthor->mem_name[0]);
	$blockPage->itemBreadcrumbs($strings["del_newsdesk_comment"]);
} else {
	$blockPage->itemBreadcrumbs($commentAuthor->mem_name[0]);
	$blockPage->itemBreadcrumbs($strings["edit_newsdesk_comment"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1 = new Block();

if ($action!='remove') {
	if ($id == "") {
		
		echo "	<a name=\"".$block1->form."Anchor\"></a>\n
				<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../newsdesk/editmessage.php?action=add&".session_name()."=".session_id()."\" 
				name=\"ecDForm\">\n
			 ";
		$block1->heading($strings["add_newsdesk_comment"]);

	} else {
		
		echo "	<a name=\"".$block1->form."Anchor\"></a>\n
				<form accept-charset=\"UNKNOWN\" method=\"POST\" 
				action=\"../newsdesk/editmessage.php?id=$id&action=update&".session_name()."=".session_id()."\" 
				name=\"ecDForm\">\n
			 ";
		$block1->heading($strings["edit_newsdesk_comment"]." : ".$newsDetail->news_title[0]);

	}


	$block1->openContent();
	$block1->contentTitle($strings["details"]);

	// add or edit comment
	if ($id == "") {
		$block1->contentRow($strings["author"],"<input type='hidden' name='name' value='$idSession'><b>$nameSession</b>");
	} else {
		$block1->contentRow($strings["author"],"<input type='hidden' name='name' value='".$commentDetail->newscom_name[0]."'><b>".$commentAuthor->mem_name[0]."</b>");	
	}

	$block1->contentRow($strings["comment"],"<textarea rows='30' name='comment' style='{width: 400px;}'>$comment</textarea>");
	$block1->contentRow($strings[""],"<input type='hidden' name='postid' value='$postid' /><input type='submit' name='submit' value='".$strings["save"]."' />  <input type='button' name='cancel' value='".$strings["cancel"]."' onClick='history.back();'>");

	$block1->closeContent();
	$block1->closeForm();

} else { //remove action

	$block1->form = "saP";
	$block1->openForm("../newsdesk/editmessage.php?action=delete&postid=$postid&".session_name()."=".session_id());

	$block1->heading($strings["del_newsdesk_comment"]);

	$block1->openContent();
	$block1->contentTitle($strings["delete_following"]);
	
	$old_id = $id;
	$id = str_replace("**",",",$id);
	$tmpquery = "WHERE newscom.id IN($id) ORDER BY newscom.id";
	$listNews = new request();
	$listNews->openNewsDeskComments($tmpquery);
	$comptListComments = count($listNews->newscom_id);

	for ($i=0;$i<$comptListComments;$i++) {
		
		$tmpquery_user = "WHERE mem.id = '".$listNews->newscom_name[$i]."' ";
		$newsAuthor = new request();
		$newsAuthor->openMembers($tmpquery_user);
		$block1->contentRow("#".$listNews->newscom_id[$i],$newsAuthor->mem_name[0]);
	}

	$block1->contentRow("","<input type='hidden' name='id' value='$old_id'><input type=\"submit\" name=\"delete\" value=\"".$strings["delete"]."\"> <input type=\"button\" name=\"cancel\" value=\"".$strings["cancel"]."\" onClick=\"history.back();\">");

	$block1->closeContent();
	$block1->closeForm();

}


include('../themes/'.THEME.'/footer.php');

?>