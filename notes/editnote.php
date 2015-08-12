<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../notes/editnote.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editnote.php
**
** DESC: screen: edit a note
**
** HISTORY:
** 	04/12/2004	-	added new document info
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');
include("../includes/customvalues.php");

if ($id != "" && $action != "add") {
	$tmpquery = "WHERE note.id = '$id'";
	$noteDetail = new Request();
	$noteDetail->openNotes($tmpquery);
	$tmpquery = "WHERE pro.id = '".$noteDetail->note_project[0]."'";
	$project = $noteDetail->note_project[0];
if ($noteDetail->note_owner[0] != $idSession) {
	Util::headerFunction("../notes/listnotes.php?project=$project&msg=noteOwner&".session_name()."=".session_id());
	exit;
}

} else {
	$tmpquery = "WHERE pro.id = '$project'";
}

$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
if ($comptMemberTest == "0") {
	$teamMember = "false";
} else {
	$teamMember = "true";
}

//case update note entry
if ($id != "") {
	//case update note entry
	if ($action == "update") {
		$subject = Util::convertData($subject);
		$description = Util::convertData($description);
		$tmpquery5 = "UPDATE ".$tableCollab["notes"]." SET project='$projectMenu',topic='$topic',subject='$subject',description='$description',date='$dd',owner='$idSession' WHERE id = '$id'";
		$msg = "update";
		Util::connectSql("$tmpquery5");
		Util::headerFunction("../notes/viewnote.php?id=$id&msg=$msg&".session_name()."=".session_id());
		exit;
	}

	//set value in form
	$dd = $noteDetail->note_date[0];
	$subject = $noteDetail->note_subject[0];
	$description = $noteDetail->note_description[0];
	$topic = $noteDetail->note_topic[0];
}

//case add note entry
if ($id == "") {

	//case add note entry
	if ($action == "add") {
		$subject = Util::convertData($subject);
		$description = Util::convertData($description);
		$tmpquery1 = "INSERT INTO ".$tableCollab["notes"]."(project,topic,subject,description,date,owner,published) VALUES('$projectMenu','$topic','$subject','$description','$dd','$idSession','1')";
		Util::connectSql("$tmpquery1");
		$tmpquery = $tableCollab["notes"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);
		Util::headerFunction("../notes/viewnote.php?id=$num&msg=add&".session_name()."=".session_id());
		exit;
	}

}

$bodyCommand = "onLoad=\"document.etDForm.subject.focus();\"";
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../notes/listnotes.php?project=".$projectDetail->pro_id[0],$strings["notes"],in));
if ($id == "") {
	$blockPage->itemBreadcrumbs($strings["add_note"]);
}
if ($id != "") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../notes/viewnote.php?id=".$noteDetail->note_id[0],$noteDetail->note_subject[0],in));
	$blockPage->itemBreadcrumbs($strings["edit_note"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
if ($id == "") {
	$block1->form = "etD";
	$block1->openForm("../notes/editnote.php?project=$project&id=$id&action=add&".session_name()."=".session_id()."#".$block1->form."Anchor");
}
if ($id != "") {
	$block1->form = "etD";
	$block1->openForm("../notes/editnote.php?project=$project&id=$id&action=update&".session_name()."=".session_id()."#".$block1->form."Anchor");
}
if ($error != "") {
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}
if ($id == "") {
	$block1->heading($strings["add_note"]);
}
if ($id != "") {
	$block1->heading($strings["edit_note"]." : ".$noteDetail->note_subject[0]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);


echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["project"]." :</td><td><select name='projectMenu'>";

$tmpquery = "WHERE tea.member = '$idSession' ORDER BY pro.name";
$listProjects = new Request();
$listProjects->openTeams($tmpquery);
$comptListProjects = count($listProjects->tea_id);

for ($i=0;$i<$comptListProjects;$i++) {
	if ($listProjects->tea_pro_id[$i] == $noteDetail->note_project[0] || $project == $listProjects->tea_pro_id[$i]) {
		echo "<option value=\"".$listProjects->tea_pro_id[$i]."\" selected>".$listProjects->tea_pro_name[$i]."</option>";
	} else {
		echo "<option value=\"".$listProjects->tea_pro_id[$i]."\">".$listProjects->tea_pro_name[$i]."</option>";
	}
}

echo "</select></td></tr>";

$block1->contentRow($strings["date"],"<input type='text' name='dd' id='noteDate' size='20' value='$dd'><input type='button' value=' ... ' id='trigNoteDate'>");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'noteDate',
        button         :    'trigNoteDate',
        $calendar_common_settings
    });
</script>
";
$comptTopic = count($topicNote);

if ($comptTopic != "0") {
echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["topic"]." :</td><td><select name='topic'><option value=''>".$strings["choice"]."</option>";

for ($i=1;$i<=$comptTopic;$i++) {
	if ($topic == $i) {
		echo "<option value='$i' selected>$topicNote[$i]</option>";
	} else {
		echo "<option value='$i'>$topicNote[$i]</option>";
	}
}
echo "</select></td></tr>";
}

$block1->contentRow($strings["subject"],"<input size='44' value='$subject' style='width: 400px' name='subject' maxlength='100' type='TEXT'>");
$block1->contentRow($strings["description"],"<textarea rows='10' style='width: 400px; height: 160px;' name='description' cols='47'>$description</textarea>");
$block1->contentRow("","<input type=\"SUBMIT\" value=\"".$strings["save"]."\">");
$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>