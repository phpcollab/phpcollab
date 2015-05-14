<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/edittask.php
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
** FILE: edittask.php
**
** DESC: Screen:  edit sub task information
**
** HISTORY:
**	05/11/2004	-	fixed 1059973
**	19/05/2005	-	fixed and &amp; in link
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

//case multiple edit tasks
$multi = strstr($id,"**");
if ($multi != "") {
	Util::headerFunction("batch../tasks/edittask.php?report=$report&project=$project&id=$id&".session_name()."=".session_id());
	exit;
}

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new request();
$taskDetail->openTasks($tmpquery);
$project = $taskDetail->tas_project[0];

if ($id != "") {
$tmpquery = "WHERE subtas.id = '$id'";
$subtaskDetail = new request();
$subtaskDetail->openSubtasks($tmpquery);
}

$tmpquery = "WHERE pro.id = '".$taskDetail->tas_project[0]."'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
	if ($comptMemberTest == "0") {
		$teamMember = "false";
	} else {
		$teamMember = "true";
	}

		if ($teamMember != "true" && $profilSession != "5") {
			Util::headerFunction("../tasks/viewtask.php?id=$task&msg=taskOwner&".session_name()."=".session_id());
		}

//case update or copy task
if ($id != "") {

//case update or copy task
if ($action == "update") {

//concat values from date selector and replace quotes by html code in name
	$tn = Util::convertData($tn);
	$d = Util::convertData($d);
	$c = Util::convertData($c);

//case copy task
		if ($docopy == "true") {

//case update task
		} else {

if ($pub == "") {
	$pub = "1";
}
if ($compl == "10") {
	$st = "1";
}

//Update task with our without parent phase
		$tmpquery5 = "UPDATE ".$tableCollab["subtasks"]." SET name='$tn',description='$d',assigned_to='$at',status='$st',priority='$pr',start_date='$sd',due_date='$dd',estimated_time='$etm',actual_time='$atm',comments='$c',modified='$dateheure',completion='$compl',published='$pub' WHERE id = '$id'";

//compute the average completion of all subtaks of this tasks
		 if ($old_completion != $compl) {
			 Util::taskComputeCompletion($task, $tableCollab["tasks"]);
		 }

		if ($st == "1" && $cd == "--") {
			$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET complete_date='$date' WHERE id = '$id'";
			Util::connectSql($tmpquery6);
		} else {
			$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET complete_date='$cd' WHERE id = '$id'";
			Util::connectSql($tmpquery6);
		}
		if ($old_st == "1" && $st != $old_st) {
			$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET complete_date='' WHERE id = '$id'";
			Util::connectSql($tmpquery6);
		}

//if assigned_to not blank and past assigned value blank, set assigned date
				if ($at != "0" && $old_assigned == "") {
					$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET assigned='$dateheure' WHERE id = '$id'";
					Util::connectSql($tmpquery6);
				}

//if assigned_to different from past value, insert into assignment
//add new assigned_to in team members (only if doesn't already exist)
				if ($at != $old_at) {
					$tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(subtask,owner,assigned_to,assigned) VALUES('$id','$idSession','$at','$dateheure')";
					Util::connectSql("$tmpquery2");
					$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
					$testinTeam = new request();
					$testinTeam->openTeams($tmpquery);
					$comptTestinTeam = count($testinTeam->tea_id);
						if ($comptTestinTeam == "0") {
							$tmpquery3 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$project','$at','1','0')";
							Util::connectSql("$tmpquery3");
						}
					//$msg = "updateAssignment";
					$msg = "update";
					Util::connectSql("$tmpquery5");

//send task assignment mail if notifications = true
						if ($notifications == "true") {
							include("../subtasks/noti_taskassignment.php");
						}
				} else {
					$msg = "update";
					Util::connectSql("$tmpquery5");

//send status task change mail if notifications = true
					if ($at != "0" && $st != $old_st) {
						if ($notifications == "true") {
								include("../subtasks/noti_statustaskchange.php");
						}
					}

//send priority task change mail if notifications = true
					if ($at != "0" && $pr != $old_pr) {
						if ($notifications == "true") {
								include("../subtasks/noti_prioritytaskchange.php");
						}
					}

//send due date task change mail if notifications = true
					if ($at != "0" && $dd != $old_dd) {
						if ($notifications == "true") {
								include("../subtasks/noti_duedatetaskchange.php");
						}
					}
				}

			if ($st != $old_st) {
				$cUp .= "\n[status:$st]";
			}
			if ($pr != $old_pr) {
				$cUp .= "\n[priority:$pr]";
			}
			if ($dd != $old_dd) {
				$cUp .= "\n[datedue:$dd]";
			}

			if ($cUp != "" || $st != $old_st || $pr != $old_pr || $dd != $old_dd) {
				$cUp = Util::convertData($cUp);
				$tmpquery6 = "INSERT INTO ".$tableCollab["updates"]."(type,item,member,comments,created) VALUES ('2','$id','$idSession','$cUp','$dateheure')";
				Util::connectSql($tmpquery6);
			}
			Util::headerFunction("../subtasks/viewsubtask.php?id=$id&task=$task&msg=$msg&".session_name()."=".session_id());
		}
}

//set value in form
$tn = $subtaskDetail->subtas_name[0];
$d = $subtaskDetail->subtas_description[0];
$sd = $subtaskDetail->subtas_start_date[0];
$dd = $subtaskDetail->subtas_due_date[0];
$cd = $subtaskDetail->subtas_complete_date[0];
$etm = $subtaskDetail->subtas_estimated_time[0];
$atm = $subtaskDetail->subtas_actual_time[0];
$c = $subtaskDetail->subtas_comments[0];
$pub = $subtaskDetail->subtas_published[0];
	if ($pub == "0") {
		$checkedPub = "checked";
	}
}

//case add task
if ($id == "") {

//case add task
	if ($action == "add") {

//concat values from date selector and replace quotes by html code in name
		$tn = Util::convertData($tn);
		$d = Util::convertData($d);
		$c = Util::convertData($c);

if ($compl == "10") {
	$st = "1";
}
if ($pub == "") {
	$pub = "1";
}

//Insert task with our without parent phase
		$tmpquery1 = "INSERT INTO ".$tableCollab["subtasks"]."(task,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion) VALUES('$task','$tn','$d','$idSession','$at','$st','$pr','$sd','$dd','$etm','$atm','$c','$dateheure','$pub','$compl')";
		Util::connectSql("$tmpquery1");
		$tmpquery = $tableCollab["subtasks"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);

		if ($st == "1") {
			$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET complete_date='$date' WHERE id = '$num'";
			Util::connectSql($tmpquery6);
		}

//compute the average completion of all subtaks of this tasks
		Util::taskComputeCompletion($task, $tableCollab["tasks"]);

//if assigned_to not blank, set assigned date
			if ($at != "0") {
				$tmpquery6 = "UPDATE ".$tableCollab["subtasks"]." SET assigned='$dateheure' WHERE id = '$num'";
				Util::connectSql($tmpquery6);
			}
		$tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(subtask,owner,assigned_to,assigned) VALUES('$num','$idSession','$at','$dateheure')";
		Util::connectSql($tmpquery2);

//if assigned_to not blank, add to team members (only if doesn't already exist)


//add assigned_to in team members (only if doesn't already exist)
			if ($at != "0") {
				$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
				$testinTeam = new request();
				$testinTeam->openTeams($tmpquery);
				$comptTestinTeam = count($testinTeam->tea_id);
					if ($comptTestinTeam == "0") {
						$tmpquery3 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$project','$at','1','0')";
						Util::connectSql($tmpquery3);
					}

//send task assignment mail if notifications = true
					if ($notifications == "true") {
						include("../subtasks/noti_taskassignment.php");
					}
			}

//create task sub-folder if filemanagement = true
			if ($fileManagement == "true") {
				Util::createDirectory("../files/$project/$num");
			}
		Util::headerFunction("../subtasks/viewsubtask.php?id=$num&task=$task&msg=add&".session_name()."=".session_id());
	}

//set default values
$subtaskDetail->subtas_assigned_to[0] = "0";
$subtaskDetail->subtas_priority[0] = "3";
$subtaskDetail->subtas_status[0] = "2";
}

if ($projectDetail->pro_org_id[0] == "1") {
	$projectDetail->pro_org_name[0] = $strings["none"];
}

if ($projectDetail->pro_phase_set[0] != "0"){
	if ($id != "") {
		$tPhase = $taskDetail->tas_parent_phase[0];
		if (!$tPhase){ $tPhase = '0'; }
		$tmpquery = "WHERE pha.project_id = '".$taskDetail->tas_project[0]."' AND pha.order_num = '$tPhase'";
	}
	if ($id == "") {
		$tPhase = $phase;
		$tmpquery = "WHERE pha.project_id = '$project' AND pha.order_num = '$tPhase'";
	}
	$targetPhase = new request();
	$targetPhase->openPhases($tmpquery);
}

$bodyCommand="onload=\"document.etDForm.compl.value = document.etDForm.completion.selectedIndex;document.etDForm.tn.focus();\"";
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0"){
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=".$projectDetail->pro_id[0],$strings["phases"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in));
}
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in));

if ($id == "") {
	$blockPage->itemBreadcrumbs($strings["add_subtask"]);
}
if ($id != "") {

$blockPage->itemBreadcrumbs($blockPage->buildLink("../subtasks/viewsubtask.php?task=$task&id=".$subtaskDetail->subtas_id[0],$subtaskDetail->subtas_name[0],in));
$blockPage->itemBreadcrumbs($strings["edit_subtask"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

if ($id == "") {
	$block1->form = "etD";
	$block1->openForm("../subtasks/editsubtask.php?task=$task&action=add&".session_name()."=".session_id()."#".$block1->form."Anchor");
}
if ($id != "") {
	$block1->form = "etD";
	$block1->openForm("../subtasks/editsubtask.php?task=$task&id=$id&action=update&docopy=$docopy&".session_name()."=".session_id()."#".$block1->form."Anchor");
	echo "	<input type='hidden' name='old_at' value='".$subtaskDetail->subtas_assigned_to[0]."'>
			<input type='hidden' name='old_assigned' value='".$subtaskDetail->subtas_assigned[0]."'>
			<input type='hidden' name='old_pr' value='".$subtaskDetail->subtas_priority[0]."'>
			<input type='hidden' name='old_st' value='".$subtaskDetail->subtas_status[0]."'>
			<input type='hidden' name='old_dd' value='".$subtaskDetail->subtas_due_date[0]."'>
			<input type='hidden' name='old_project' value='".$subtaskDetail->subtas_project[0]."'>
			<input type='hidden' name='old_completion' value='".$subtaskDetail->subtas_completion[0]."'>";
}

if ($error != "") {
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

if ($id == "") {
	$block1->heading($strings["add_subtask"]);
}
if ($id != "") {
	if ($docopy == "true") {
		$block1->heading($strings["copy_subtask"]." : ".$subtaskDetail->subtas_name[0]);
	} else {
		$block1->heading($strings["edit_subtask"]." : ".$subtaskDetail->subtas_name[0]);
	}
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["project"]." :</td><td>".$blockPage->buildLink("../projects/viewproject.php?id=".$taskDetail->tas_project[0],$taskDetail->tas_pro_name[0],in)."</td></tr>";

//Display task's phase
if ($projectDetail->pro_phase_set[0] != "0"){
	echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["phase"]." :</td><td>".$blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in)."</td></tr>";
}
echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["task"]." :</td><td>".$blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in)."</td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["organization"]." :</td><td>".$projectDetail->pro_org_name[0]."</td></tr>";

$block1->contentTitle($strings["details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["name"]." :</td><td><input size='44' value='";

if ($docopy == "true") {
	echo $strings["copy_of"];
}

echo "$tn' style='width: 400px' name='tn' maxlength='100' type='TEXT'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["description"]." :</td><td><textarea rows='10' style='width: 400px; height: 160px;' name='d' cols='47'>$d</textarea></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["assigned_to"]." :</td><td><select name='at'>";

if ($subtaskDetail->subtas_assigned_to[0] == "0") {
	echo "<option value='0' selected>".$strings["unassigned"]."</option>";
} else {
	echo "<option value='0'>".$strings["unassigned"]."</option>";
}

$tmpquery = "WHERE tea.project = '$project' ORDER BY mem.name";
$assignto = new request();
$assignto->openTeams($tmpquery);
$comptAssignto = count($assignto->tea_mem_id);

for ($i=0;$i<$comptAssignto;$i++) {

$clientUser = "";
if ($assignto->tea_mem_profil[$i] == "3") {
	$clientUser = " (".$strings["client_user"].")";
}
	if ($subtaskDetail->subtas_assigned_to[0] == $assignto->tea_mem_id[$i]) {
		echo "<option value=\"".$assignto->tea_mem_id[$i]."\" selected>".$assignto->tea_mem_login[$i]." / ".$assignto->tea_mem_name[$i]."$clientUser</option>";
	} else {
		echo "<option value=\"".$assignto->tea_mem_id[$i]."\">".$assignto->tea_mem_login[$i]." / ".$assignto->tea_mem_name[$i]."$clientUser</option>";
	}
}

echo "</select></td></tr>";

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["status"]." :</td><td><select name='st' onchange='changeSt(this)'>";

$comptSta = count($status);

for ($i=0;$i<$comptSta;$i++) {
	if ($subtaskDetail->subtas_status[0] == $i) {
		echo "<option value=\"$i\" selected>$status[$i]</option>";
	} else {
		echo "<option value=\"$i\">$status[$i]</option>";
	}
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["completion"]." :</td><td><input name='compl' type='hidden' value='".$subtaskDetail->subtas_completion[0]."'><select name='completion' onchange='changeCompletion(this)'>";

for ($i=0;$i<11;$i++) {
	$complValue = ($i>0) ? $i."0 %": $i." %";
	if ($subtaskDetail->subtas_completion[0] == $i) {
		echo "<option value='".$i."' selected>".$complValue."</option>";
	} else {
		echo "<option value='".$i."'>".$complValue."</option>";
	}
}

echo "</select></td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["priority"]." :</td><td><select name=\"pr\">";

$comptPri = count($priority);

for ($i=0;$i<$comptPri;$i++) {
	if ($subtaskDetail->subtas_priority[0] == $i) {
		echo "<option value='$i' selected>$priority[$i]</option>";
	} else {
		echo "<option value='$i'>$priority[$i]</option>";
	}
}

echo "</select></td></tr>";

if ($sd == "") {
	$sd = $date;
}
if ($dd == "") {
	$dd = "--";
}
if ($cd == "") {
	$cd = "--";
}


$block1->contentRow($strings["start_date"],"<input type='text' name='sd' id='start_date' size='20' value='$sd'><input type='button' value=' ... ' id='trigStartDate'>");
echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        $calendar_common_settings
    });
</script>
";

$block1->contentRow($strings["due_date"],"<input type='text' name='dd' id='due_date' size='20' value='$dd'><input type='button' value=' ... ' id='trigDueDate'>");
echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        $calendar_common_settings
    });
</script>";

if ($id != "")
{
	//$block1->contentRow($strings["complete_date"],"<input type='text' name='cd' id='sel5' size='20' value='$cd'><input type='button' value=' ... ' onclick=\"return showCalendar('sel5', '%Y-%m-%d');\">");
	$block1->contentRow($strings["complete_date"],"<input type='text' name='cd' id='complete_date' size='20' value='$cd'><input type='button' value=' ... ' id='trigCompleteDate'>");
echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'complete_date',
        button         :    'trigCompleteDate',
        $calendar_common_settings
    });
</script>";
}

echo "	<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["estimated_time"]." :</td>
			<td><input size='32' value='$etm' style='width: 250px' name='etm' maxlength='32' type='TEXT'>&nbsp;".$strings["hours"]."</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["actual_time"]." :</td>
			<td><input size='32' value='$atm' style='width: 250px' name='atm' maxlength='32' type='TEXT'>&nbsp;".$strings["hours"]."</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["comments"]." :</td>
			<td><textarea rows='10' style='width: 400px; height: 160px;' name='c' cols='47'>$c</textarea></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["published"]." :</td>
			<td><input size='32' value='0' name='pub' type='checkbox' $checkedPub></td>
		</tr>";

if ($id != "")
{
	$block1->contentTitle($strings["updates_subtask"]);
	echo "
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["comments"]." :</td>
			<td><textarea rows='10' style='width: 400px; height: 160px;' name='cUp' cols='47'></textarea></td>
		</tr>";
}

echo "	<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td><input type='SUBMIT' value='".$strings["save"]."'></td>
		</tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>

<script>
function changeSt(theObj, firstRun){
	if (theObj.selectedIndex==3) {
		if (firstRun!=true) document.etDForm.completion.selectedIndex=0;
		document.etDForm.compl.value=0;
		document.etDForm.completion.disabled=false;
	} else {
		if (theObj.selectedIndex==0 || theObj.selectedIndex==1) {
			document.etDForm.completion.selectedIndex=10;
			document.etDForm.compl.value=10;
		} else {
			document.etDForm.completion.selectedIndex=0;
			document.etDForm.compl.value=0;
		}
		document.etDForm.completion.disabled=true;
	}
}

function changeCompletion(){
	document.etDForm.compl.value = document.etDForm.completion.selectedIndex;
}

changeSt(document.etDForm.st, true);
</script>