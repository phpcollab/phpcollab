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
include_once '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();
$projects = new \phpCollab\Projects\Projects();
$members = new \phpCollab\Members\Members();
$teams = new \phpCollab\Teams\Teams();

//case multiple edit tasks
$multi = strstr($id, "**");
if ($multi != "") {
    phpCollab\Util::headerFunction("batch../tasks/edittask.php?report={$report}&project={$project}&id={$id}");
}

$taskDetail = $tasks->getTaskById($task);

$project = $taskDetail['tas_project'];

if ($id != "") {
//    $tmpquery = "WHERE subtas.id = '$id'";
//    $subtaskDetail = new phpCollab\Request();
//    $subtaskDetail['openSubtasks($tmpquery);
    $subtaskDetail = $tasks->getSubTaskById($id);
}

//$tmpquery = "WHERE pro.id = '" . $taskDetail['tas_project'] . "'";
//$projectDetail = new phpCollab\Request();
//$projectDetail->openProjects($tmpquery);
$projectDetail = $projects->getProjectById($taskDetail['tas_project']);

$teamMember = "false";
//$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
//$memberTest = new phpCollab\Request();
//$memberTest->openTeams($tmpquery);

$memberTest = $teams->getTeamByProjectIdAndTeamMember($project, $idSession);

$comptMemberTest = count($memberTest);

if ($comptMemberTest == "0") {
    $teamMember = "false";
} else {
    $teamMember = "true";
}

if ($teamMember != "true" && $profilSession != "5") {
    phpCollab\Util::headerFunction("../tasks/viewtask.php?id={$task}&msg=taskOwner");
}

//case update or copy task
if ($id != "") {
    echo 'update or copy task<br>';

//case update or copy task
    if ($action == "update") {

//concat values from date selector and replace quotes by html code in name
        $tn = phpCollab\Util::convertData($tn);
        $d = phpCollab\Util::convertData($d);
        $c = phpCollab\Util::convertData($c);

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
            $tmpquery5 = "UPDATE {$tableCollab["subtasks"]} SET name=:name,description=:description,assigned_to=:assigned_to,status=:status,priority=:priority,start_date=:start_date,due_date=:due_date,estimated_time=:estimated_time,actual_time=:actual_time,comments=:comments,modified=:modified,completion=:completion,published=:published WHERE id = :subtask_id";

            $tmpquery5Params = [];
            $tmpquery5Params['name'] = $tn;
            $tmpquery5Params['description'] = $d;
            $tmpquery5Params['assigned_to'] = $at;
            $tmpquery5Params['status'] = $st;
            $tmpquery5Params['priority'] = $pr;
            $tmpquery5Params['start_date'] = $sd;
            $tmpquery5Params['due_date'] = $dd;
            $tmpquery5Params['estimated_time'] = $etm;
            $tmpquery5Params['actual_time'] = $atm;
            $tmpquery5Params['comments'] = $c;
            $tmpquery5Params['modified'] = $dateheure;
            $tmpquery5Params['completion'] = $compl;
            $tmpquery5Params['published'] = $pub;
            $tmpquery5Params['subtask_id'] = $id;

//compute the average completion of all subtaks of this tasks
            if ($old_completion != $compl) {
                phpCollab\Util::taskComputeCompletion($task, $tableCollab["tasks"]);
            }

            if ($st == "1" && $cd == "--") {
                $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET complete_date=:complete_date WHERE id = :subtask_id";
                $dbParams = [];
                $dbParams['complete_date'] = $date;
                $dbParams['subtask_id'] = $id;
                phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
                unset($dbParams);
            } else {
                $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET complete_date=:complete_date WHERE id = :subtask_id";
                $dbParams = [];
                $dbParams['complete_date'] = $cd;
                $dbParams['subtask_id'] = $id;
                phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
                unset($dbParams);
            }
            if ($old_st == "1" && $st != $old_st) {
                $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET complete_date='' WHERE id = :subtask_id";
                $dbParams = [];
                $dbParams['subtask_id'] = $id;
                phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
                unset($dbParams);
            }

//if assigned_to not blank and past assigned value blank, set assigned date
            if ($at != "0" && $old_assigned == "") {
                $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET assigned=:assigned WHERE id = :subtask_id";
                $dbParams = [];
                $dbParams['assigned'] = $dateheure;
                $dbParams['subtask_id'] = $id;
                phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
                unset($dbParams);
            }

//if assigned_to different from past value, insert into assignment
//add new assigned_to in team members (only if doesn't already exist)
            if ($at != $old_at) {
                $tmpquery2 = "INSERT INTO {$tableCollab["assignments"]} (subtask,owner,assigned_to,assigned) VALUES(:subtask_id,:owner_id,:assigned_to,:assigned_date)";
                $dbParams = [];
                $dbParams['subtask_id'] = $id;
                $dbParams['owner_id'] = $dateheure;
                $dbParams['assigned_to'] = $at;
                $dbParams['assigned_date'] = $dateheure;
                phpCollab\Util::newConnectSql($tmpquery2, $dbParams);
                unset($dbParams);

                $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
                $testinTeam = new phpCollab\Request();
                $testinTeam->openTeams($tmpquery);
                $comptTestinTeam = count($testinTeam->tea_id);

                if ($comptTestinTeam == "0") {
                    $tmpquery3 = "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES(:project,:member,:published,:authorized)";
                    $dbParams = [];
                    $dbParams['project'] = $project;
                    $dbParams['member'] = $at;
                    $dbParams['published'] = 1;
                    $dbParams['authorized'] = 0;
                    phpCollab\Util::newConnectSql($tmpquery3, $dbParams);
                    unset($dbParams);

                }
                //$msg = "updateAssignment";
                $msg = "update";
                phpCollab\Util::newConnectSql($tmpquery5, $tmpquery5Params);
                unset($dbParams);


//send task assignment mail if notifications = true
                if ($notifications == "true") {
                    include '../subtasks/noti_taskassignment.php';
                }
            } else {
                $msg = "update";
                phpCollab\Util::newConnectSql($tmpquery5, $tmpquery5Params);

//send status task change mail if notifications = true
                if ($at != "0" && $st != $old_st) {
                    if ($notifications == "true") {
                        include '../subtasks/noti_statustaskchange.php';
                    }
                }

//send priority task change mail if notifications = true
                if ($at != "0" && $pr != $old_pr) {
                    if ($notifications == "true") {
                        include '../subtasks/noti_prioritytaskchange.php';
                    }
                }

//send due date task change mail if notifications = true
                if ($at != "0" && $dd != $old_dd) {
                    if ($notifications == "true") {
                        include '../subtasks/noti_duedatetaskchange.php';
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
                $cUp = phpCollab\Util::convertData($cUp);
                $tmpquery6 = "INSERT INTO {$tableCollab["updates"]} (type,item,member,comments,created) VALUES (:type,:item,:member,:comments,:created)";
                $dbParams = [];
                $dbParams['type'] = 2;
                $dbParams['item'] = $id;
                $dbParams['member'] = $idSession;
                $dbParams['comments'] = $cUp;
                $dbParams['created'] = $dateheure;
                phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
                unset($dbParams);

            }
            phpCollab\Util::headerFunction("../subtasks/viewsubtask.php?id={$id}&task={$task}&msg={$msg}");
        }
    }

//set value in form
    $tn = $subtaskDetail['subtas_name'];
    $d = $subtaskDetail['subtas_description'];
    $sd = $subtaskDetail['subtas_start_date'];
    $dd = $subtaskDetail['subtas_due_date'];
    $cd = $subtaskDetail['subtas_complete_date'];
    $etm = $subtaskDetail['subtas_estimated_time'];
    $atm = $subtaskDetail['subtas_actual_time'];
    $c = $subtaskDetail['subtas_comments'];
    $pub = $subtaskDetail['subtas_published'];
    if ($pub == "0") {
        $checkedPub = "checked";
    }
}

//case add task
if ($id == "") {

//case add task
    if ($action == "add") {

//concat values from date selector and replace quotes by html code in name
        $tn = phpCollab\Util::convertData($tn);
        $d = phpCollab\Util::convertData($d);
        $c = phpCollab\Util::convertData($c);

        if ($compl == "10") {
            $st = "1";
        }
        if ($pub == "") {
            $pub = "1";
        }

//Insert task with our without parent phase
        $tmpquery1 = "INSERT INTO {$tableCollab["subtasks"]} (task,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion) VALUES(:task,:name,:description,:owner,:assigned_to,:status,:priority,:start_date,:due_date,:estimated_time,:actual_time,:comments,:created,:published,:completion)";
        $dbParams = [];
        $dbParams['task'] = $task;
        $dbParams['name'] = $tn;
        $dbParams['description'] = $d;
        $dbParams['owner'] = $idSession;
        $dbParams['assigned_to'] = $at;
        $dbParams['status'] = $st;
        $dbParams['priority'] = $pr;
        $dbParams['start_date'] = $sd;
        $dbParams['due_date'] = $dd;
        $dbParams['estimated_time'] = $etm;
        $dbParams['actual_time'] = $atm;
        $dbParams['comments'] = $c;
        $dbParams['created'] = $dateheure;
        $dbParams['published'] = $pub;
        $dbParams['completion'] = $compl;
        phpCollab\Util::newConnectSql($tmpquery1, $dbParams);
        unset($dbParams);


        $tmpquery = $tableCollab["subtasks"];
        phpCollab\Util::getLastId($tmpquery);
        $num = $lastId[0];
        unset($lastId);

        if ($st == "1") {
            $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET complete_date=:complete_date WHERE id = :subtask_id";
            $dbParams = [];
            $dbParams['complete_date'] = $date;
            $dbParams['subtask_id'] = $num;
            phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
            unset($dbParams);

        }

//compute the average completion of all subtaks of this tasks
        phpCollab\Util::taskComputeCompletion($task, $tableCollab["tasks"]);

//if assigned_to not blank, set assigned date
        if ($at != "0") {
            $tmpquery6 = "UPDATE {$tableCollab["subtasks"]} SET assigned=:assigned WHERE id = :subtask_id";
            $dbParams = [];
            $dbParams['assigned'] = $dateheure;
            $dbParams['subtask_id'] = $num;
            phpCollab\Util::newConnectSql($tmpquery6, $dbParams);
            unset($dbParams);

        }
        $tmpquery2 = "INSERT INTO {$tableCollab["assignments"]} (subtask,owner,assigned_to,assigned) VALUES (:subtask,:owner,:assigned_to,:assigned)";
        $dbParams = [];
        $dbParams['subtask'] = $num;
        $dbParams['owner'] = $idSession;
        $dbParams['assigned_to'] = $at;
        $dbParams['assigned'] = $dateheure;
        phpCollab\Util::newConnectSql($tmpquery2, $dbParams);
        unset($dbParams);


//if assigned_to not blank, add to team members (only if doesn't already exist)


//add assigned_to in team members (only if doesn't already exist)
        if ($at != "0") {
//            $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
//            $testinTeam = new phpCollab\Request();
//            $testinTeam->openTeams($tmpquery);
            $testinTeam = $teams->getTeamByProjectIdAndTeamMember($project, $at);

            $comptTestinTeam = count($testinTeam);

            if ($comptTestinTeam == "0") {
                $tmpquery3 = "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES(:project,:member,:published,:authorized)";
                $dbParams = [];
                $dbParams['project'] = $project;
                $dbParams['member'] = $at;
                $dbParams['published'] = 1;
                $dbParams['authorized'] = 0;
                phpCollab\Util::newConnectSql($tmpquery3, $dbParams);
                unset($dbParams);

            }

//send task assignment mail if notifications = true
            if ($notifications == "true") {
                include '../subtasks/noti_taskassignment.php';
            }
        }

//create task sub-folder if filemanagement = true
        if ($fileManagement == "true") {
            phpCollab\Util::createDirectory("../files/$project/$num");
        }

        phpCollab\Util::headerFunction("../subtasks/viewsubtask.php?id={$num}&task={$task}&msg=add");
    }

//set default values
    $subtaskDetail['subtas_assigned_to'] = "0";
    $subtaskDetail['subtas_priority'] = "3";
    $subtaskDetail['subtas_status'] = "2";
}

if ($projectDetail['pro_org_id'] == "1") {
    $projectDetail['pro_org_name'] = $strings["none"];
}

if ($projectDetail['pro_phase_set'] != "0") {
    $phases = new \phpCollab\Phases\Phases();
    if ($id != "") {
        $tPhase = $taskDetail['tas_parent_phase'];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $projectId = $taskDetail['tas_project'];
    }
    if ($id == "") {
        $tPhase = $phase;
        $projectId = $project;
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($projectId, $tPhase);
//    $targetPhase = new phpCollab\Request();
//    $targetPhase->openPhases($tmpquery);
}

$bodyCommand = "onload=\"document.etDForm.compl.value = document.etDForm.completion.selectedIndex;document.etDForm.tn.focus();\"";
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'], $projectDetail['pro_name'], in));

if ($projectDetail['pro_phase_set'] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail['pro_id'], $strings["phases"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'], $targetPhase['pha_name'], in));
}
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail['pro_id'], $strings["tasks"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], in));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_subtask"]);
}
if ($id != "") {

    $blockPage->itemBreadcrumbs($blockPage->buildLink("../subtasks/viewsubtask.php?task=$task&id=" . $subtaskDetail['subtas_id'], $subtaskDetail['subtas_name'], in));
    $blockPage->itemBreadcrumbs($strings["edit_subtask"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

if ($id == "") {
    $block1->form = "etD";
    $block1->openForm("../subtasks/editsubtask.php?task=$task&action=add&#" . $block1->form . "Anchor");
}
if ($id != "") {
    $block1->form = "etD";
    $block1->openForm("../subtasks/editsubtask.php?task=$task&id=$id&action=update&docopy=$docopy&#" . $block1->form . "Anchor");
    echo "	<input type='hidden' name='old_at' value='" . $subtaskDetail['subtas_assigned_to'] . "'>
			<input type='hidden' name='old_assigned' value='" . $subtaskDetail['subtas_assigned'] . "'>
			<input type='hidden' name='old_pr' value='" . $subtaskDetail['subtas_priority'] . "'>
			<input type='hidden' name='old_st' value='" . $subtaskDetail['subtas_status'] . "'>
			<input type='hidden' name='old_dd' value='" . $subtaskDetail['subtas_due_date'] . "'>
			<input type='hidden' name='old_project' value='" . $subtaskDetail['subtas_project'] . "'>
			<input type='hidden' name='old_completion' value='" . $subtaskDetail['subtas_completion'] . "'>";
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
        $block1->heading($strings["copy_subtask"] . " : " . $subtaskDetail['subtas_name']);
    } else {
        $block1->heading($strings["edit_subtask"] . " : " . $subtaskDetail['subtas_name']);
    }
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["project"] . " :</td><td>" . $blockPage->buildLink("../projects/viewproject.php?id=" . $taskDetail['tas_project'], $taskDetail['tas_pro_name'], 'in') . "</td></tr>";

//Display task's phase
if ($projectDetail['pro_phase_set'] != "0") {
    echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["phase"] . " :</td><td>" . $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'], $targetPhase['pha_name'], in) . "</td></tr>";
}
echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["task"] . " :</td><td>" . $blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], in) . "</td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["organization"] . " :</td><td>" . $projectDetail['pro_org_name'] . "</td></tr>";

$block1->contentTitle($strings["details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["name"] . " :</td><td><input size='44' value='";

if ($docopy == "true") {
    echo $strings["copy_of"];
}

echo "$tn' style='width: 400px' name='tn' maxlength='100' type='TEXT'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["description"] . " :</td><td><textarea rows='10' style='width: 400px; height: 160px;' name='d' cols='47'>$d</textarea></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["assigned_to"] . " :</td><td><select name='at'>";

if ($subtaskDetail['subtas_assigned_to'] == "0") {
    echo "<option value='0' selected>" . $strings["unassigned"] . "</option>";
} else {
    echo "<option value='0'>" . $strings["unassigned"] . "</option>";
}

$tmpquery = "WHERE tea.project = '$project' ORDER BY mem.name";
$assignto = new phpCollab\Request();
$assignto->openTeams($tmpquery);
$comptAssignto = count($assignto->tea_mem_id);

for ($i = 0; $i < $comptAssignto; $i++) {

    $clientUser = "";
    if ($assignto->tea_mem_profil[$i] == "3") {
        $clientUser = " (" . $strings["client_user"] . ")";
    }
    if ($subtaskDetail['subtas_assigned_to'] == $assignto->tea_mem_id[$i]) {
        echo "<option value=\"" . $assignto->tea_mem_id[$i] . "\" selected>" . $assignto->tea_mem_login[$i] . " / " . $assignto->tea_mem_name[$i] . "$clientUser</option>";
    } else {
        echo "<option value=\"" . $assignto->tea_mem_id[$i] . "\">" . $assignto->tea_mem_login[$i] . " / " . $assignto->tea_mem_name[$i] . "$clientUser</option>";
    }
}

echo "</select></td></tr>";

echo "<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["status"] . " :</td><td><select name='st' onchange='changeSt(this)'>";

$comptSta = count($status);

for ($i = 0; $i < $comptSta; $i++) {
    if ($subtaskDetail['subtas_status'] == $i) {
        echo "<option value=\"$i\" selected>$status[$i]</option>";
    } else {
        echo "<option value=\"$i\">$status[$i]</option>";
    }
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>" . $strings["completion"] . " :</td><td><input name='compl' type='hidden' value='" . $subtaskDetail['subtas_completion'] . "'><select name='completion' onchange='changeCompletion(this)'>";

for ($i = 0; $i < 11; $i++) {
    $complValue = ($i > 0) ? $i . "0 %" : $i . " %";
    if ($subtaskDetail['subtas_completion'] == $i) {
        echo "<option value='" . $i . "' selected>" . $complValue . "</option>";
    } else {
        echo "<option value='" . $i . "'>" . $complValue . "</option>";
    }
}

echo "</select></td></tr>
<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">" . $strings["priority"] . " :</td><td><select name=\"pr\">";

$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    if ($subtaskDetail['subtas_priority'] == $i) {
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


$block1->contentRow($strings["start_date"], "<input type='text' name='sd' id='start_date' size='20' value='$sd'><input type='button' value=' ... ' id='trigStartDate'>");
echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        $calendar_common_settings
    });
</script>
";

$block1->contentRow($strings["due_date"], "<input type='text' name='dd' id='due_date' size='20' value='$dd'><input type='button' value=' ... ' id='trigDueDate'>");
echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        $calendar_common_settings
    });
</script>";

if ($id != "") {
    //$block1->contentRow($strings["complete_date"],"<input type='text' name='cd' id='sel5' size='20' value='$cd'><input type='button' value=' ... ' onclick=\"return showCalendar('sel5', '%Y-%m-%d');\">");
    $block1->contentRow($strings["complete_date"], "<input type='text' name='cd' id='complete_date' size='20' value='$cd'><input type='button' value=' ... ' id='trigCompleteDate'>");
    echo "<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'complete_date',
        button         :    'trigCompleteDate',
        $calendar_common_settings
    });
</script>";
}

echo "	<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["estimated_time"] . " :</td>
			<td><input size='32' value='$etm' style='width: 250px' name='etm' maxlength='32' type='TEXT'>&nbsp;" . $strings["hours"] . "</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["actual_time"] . " :</td>
			<td><input size='32' value='$atm' style='width: 250px' name='atm' maxlength='32' type='TEXT'>&nbsp;" . $strings["hours"] . "</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["comments"] . " :</td>
			<td><textarea rows='10' style='width: 400px; height: 160px;' name='c' cols='47'>$c</textarea></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["published"] . " :</td>
			<td><input size='32' value='0' name='pub' type='checkbox' $checkedPub></td>
		</tr>";

if ($id != "") {
    $block1->contentTitle($strings["updates_subtask"]);
    echo "
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["comments"] . " :</td>
			<td><textarea rows='10' style='width: 400px; height: 160px;' name='cUp' cols='47'></textarea></td>
		</tr>";
}

echo "	<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td><input type='SUBMIT' value='" . $strings["save"] . "'></td>
		</tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>

<script>
    function changeSt(theObj, firstRun) {
        if (theObj.selectedIndex == 3) {
            if (firstRun != true) document.etDForm.completion.selectedIndex = 0;
            document.etDForm.compl.value = 0;
            document.etDForm.completion.disabled = false;
        } else {
            if (theObj.selectedIndex == 0 || theObj.selectedIndex == 1) {
                document.etDForm.completion.selectedIndex = 10;
                document.etDForm.compl.value = 10;
            } else {
                document.etDForm.completion.selectedIndex = 0;
                document.etDForm.compl.value = 0;
            }
            document.etDForm.completion.disabled = true;
        }
    }

    function changeCompletion() {
        document.etDForm.compl.value = document.etDForm.completion.selectedIndex;
    }

    changeSt(document.etDForm.st, true);
</script>