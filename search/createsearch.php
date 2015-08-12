<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../search/createsearch.php
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
** FILE: createsearch.php
**
** DESC: Screen: CREATE SEARCH
**
** HISTORY:
** 	12/04/2005	-	added the subtask search http://www.php-collab.org/community/viewtopic.php?t=1938
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

//test required field searchfor
if ($action == "search") {

//if searchfor blank, $error set
	if ($searchfor == "") {
		$error = $strings["search_note"];

//if searchfor not blank, redirect to searchresults
	} else {
		$searchfor = urlencode($searchfor);
		Util::headerFunction("../search/resultssearch.php?searchfor=$searchfor&heading=$heading&".session_name()."=".session_id());
		exit;
	}
} 

$setTitle .= " : Search";

$bodyCommand = "onLoad=\"document.searchForm.searchfor.focus()\"";
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../search/createsearch.php?",$strings["search"],in));
$blockPage->itemBreadcrumbs($strings["search_options"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "search";
$block1->openForm("../search/createsearch.php?action=search&".session_name()."=".session_id());

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["search"]);

$block1->openContent();
$block1->contentTitle($strings["enter_keywords"]);

echo "
<tr class='odd'>
	<td valign='top' class='leftvalue'>* ".$strings["search_for"]." :</td>
	<td>
		<input value='' type='text' name='searchfor' style='width: 200px;' size='30' maxlength='64' />
		<select name='heading'>
				<option selected value='ALL'>".$strings["all_content"]."</option>
				<option value='notes'>".$strings["notes"]."</option>
				<option value='organizations'>".$strings["organizations"]."</option>
				<option value='projects'>".$strings["projects"]."</option>
				<option value='tasks'>".$strings["tasks"]."</option>
				<option value='subtasks'>".$strings["subtasks"]."</option>
				<option value='discussions'>".$strings["discussions"]."</option>
				<option value='members'>".$strings["users"]."</option>
		</select>
	</td>
</tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'>&nbsp;</td>
	<td><input type='submit' name='Save' value='".$strings["search"]."' /></td>
</tr>";

$block1->closeContent();
$block1->closeForm();


include '../themes/'.THEME.'/footer.php';
?>