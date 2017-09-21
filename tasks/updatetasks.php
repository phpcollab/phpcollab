<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../tasks/updatetasks.php
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
** FILE: updatetasks.php
**
** DESC: Screen: modify tasks
**
** HISTORY:
** 	14/05/2005	-	file comment added
**	14/05/2005	-	fix for http://www.php-collab.org/community/viewtopic.php?t=1974
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE tas.id IN($id)";
$listTasks = new phpCollab\Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

if ($action == "update") {
    $acomm = phpCollab\Util::convertData($acomm);

    if ($at != $strings["no_change"]) {
        $query = "assigned_to='$at'";
        $assignUpdate = "true";
    }

    if ($st != $strings["no_change"]) {
        if ($query != "") {
            $query .= ",status='$st'";
        } else {
            $query .= "status='$st'";
        }
    }

    if ($compl != "") {
        if ($query != "") {
            $query .= ",completion='$compl'";
        } else {
            $query .= "completion='$compl'";
        }
    }

    if ($pr != $strings["no_change"]) {
        if ($query != "") {
            $query .= ",priority='$pr'";
        } else {
            $query .= "priority='$pr'";
        }
    }

    if ($sd != "--") {
        if ($query != "") {
            $query .= ",start_date='$sd'";
        } else {
            $query .= "start_date='$sd'";
        }
    }

    if ($dd != "--") {
        if ($query != "") {
            $query .= ",due_date='$dd'";
        } else {
            $query .= "due_date='$dd'";
        }
    }

    if ($query != "") {
        for ($i = 0; $i < $comptListTasks; $i++) {
            $sameAssign = "false";

            if ($at != "0" && $listTasks->tas_assigned[$i] == "") {
                $dbParams = [];
                $dbParams["assigned_date"] = $dateheure;
                $dbParams["task_id"] = $listTasks->tas_id[$i];
                phpCollab\Util::newConnectSql("UPDATE {$tableCollab["tasks"]} SET assigned=:assigned_date WHERE id = :task_id", $dbParams);
                unset($dbParams);
            }

            if ($listTasks->tas_assigned_to[$i] == $at) {
                $sameAssign = "true";
            }

            $dbParams = [];
            $dbParams["modified_date"] = $dateheure;
            $dbParams["task_id"] = $listTasks->tas_id[$i];
            phpCollab\Util::newConnectSql("UPDATE {$tableCollab["tasks"]} SET $query,modified=:modified_date'$dateheure' WHERE id = :task_id", $dbParams);
            unset($dbParams);


            if ($st != $strings["no_change"] && $listTasks->tas_status[$i] != $st && $assignUpdate != "true" && $listTasks->tas_assigned_to[$i] != "0") {
                if ($notifications == "true") {
                    include '../tasks/noti_statustaskchange.php';
                }
            }

            if ($pr != $strings["no_change"] && $listTasks->tas_priority[$i] != $pr && $assignUpdate != "true" && $listTasks->tas_assigned_to[$i] != "0") {
                if ($notifications == "true") {
                    include '../tasks/noti_prioritytaskchange.php';
                }
            }
            if ($dd != "--" && $listTasks->tas_due_date[$i] != $dd && $assignUpdate != "true" && $listTasks->tas_assigned_to[$i] != "0") {
                if ($notifications == "true") {
                    include '../tasks/noti_duedatetaskchange.php';
                }
            }

            if ($at != "0" && $sameAssign != "true" && $assignUpdate == "true") {
                $dbParams = [];
                $dbParams["task"] = $listTasks->tas_id[$i];
                $dbParams["owner"] = $listTasks->tas_owner[$i];
                $dbParams["assigned_to"] = $at;
                $dbParams["comments"] = $acomm;
                $dbParams["assigned"] = $dateheure;

                phpCollab\Util::newConnectSql("INSERT INTO {$tableCollab["assignments"]} (task,owner,assigned_to,comments,assigned) VALUES (:task,:owner,:assigned_to,:comments,:assigned)", $dbParams);
                unset($dbParams);

                $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
                $testinTeam = new phpCollab\Request();
                $testinTeam->openTeams($tmpquery);
                $comptTestinTeam = count($testinTeam->tea_id);

                if ($comptTestinTeam == "0") {
                    $dbParams = [];
                    $dbParams["project"] = $project;
                    $dbParams["member"] = $at;
                    $dbParams["published"] = 1;
                    $dbParams["authorized"] = 0;

                    phpCollab\Util::newConnectSql("INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES (:project,:member,:published,:authorized)", $dbParams);
                    unset($dbParams);
                }

                if ($notifications == "true") {
                    include '../tasks/noti_taskassignment.php';
                }
            }
        }
    }

    phpCollab\Util::headerFunction("../tasks/listtasks.php?project=$project&msg=update&PHPSESSID=$PHPSESSID");
}

$includeCalendar = true; //Include Javascript files for the pop-up calendar
include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();

if ($report != "") {
    $tmpquery = "WHERE id = '$report'";
    $reportDetail = new phpCollab\Request();
    $reportDetail->openReports($tmpquery);
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/createreport.php?", $strings["reports"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/resultsreport.php?id=" . $reportDetail->rep_id[0], $reportDetail->rep_name[0], in));
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=$project", $strings["tasks"], in));
}

$blockPage->itemBreadcrumbs($strings["edit_multiple_tasks"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->form = "batT";
$block1->openForm("../tasks/updatetasks.php?action=update&#" . $block1->form . "Anchor");

if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_multiple_tasks"]);
$block1->openContent();
$block1->contentTitle($strings["details"]);

echo "	<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td>$comptListTasks " . $strings["tasks_selected"] . "</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["assigned_to"] . "</td>
			<td>
				<select name='at'>
					<option value='" . $strings["no_change"] . "' selected>" . $strings["no_change"] . " :</option>
					<option value='0'>" . $strings["unassigned"] . "</option>";

if ($idSession == "1") {
    echo "<option value='1'>" . $strings["administrator"] . "</option>";
}

$tmpquery = "WHERE mem.id != '1' AND mem.profil != '3' ORDER BY mem.name";
$assignTo = new phpCollab\Request();
$assignTo->openMembers($tmpquery);
$comptAssignTo = count($assignTo->mem_id);

for ($i = 0; $i < $comptAssignTo; $i++) {
    echo "<option value='" . $assignTo->mem_id[$i] . "'>" . $assignTo->mem_name[$i] . "</option>";
}

echo "		</select></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["assignment_comment"] . " :</td>
			<td><textarea rows='3' style='width: 400px; height: 50px;' name='acomm' cols='43'></textarea></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["status"] . " :</td>
			<td><select name='st' onchange='changeSt(this)'>
				<option value='" . $strings["no_change"] . "' selected>" . $strings["no_change"] . "</option>";

$comptSta = count($status);

for ($i = 0; $i < $comptSta; $i++) {
    echo "<option value='$i'>$status[$i]</option>";
}

echo "		</select></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>" . $strings["completion"] . " :</td>
			<td><input name='compl' type='hidden' value=''>
				<select name='completion' onchange='changeCompletion(this)'>
					<option value='" . $strings["no_change"] . "' selected>" . $strings["no_change"] . "</option>";

for ($i = 0; $i < 11; $i++) {
    $complValue = ($i > 0) ? $i . "0 %" : $i . " %";
    echo "<option value='" . $i . "'>" . $complValue . "</option>";
}

echo "</select></td></tr>
<tr class='odd'>
	<td valign='top' class='leftvalue'>" . $strings["priority"] . " : </td>
	<td><select name='pr'>
			<option value='" . $strings["no_change"] . "' selected>" . $strings["no_change"] . "</option>";

$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    echo "<option value='$i'>$priority[$i]</option>";
}

echo "	</select></td></tr>";

if ($sd == "") {
    $sd = "--";
}
if ($dd == "") {
    $dd = "--";
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
</script>
";
echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='SUBMIT' value='" . $strings["update"] . "'></td></tr>";
echo "<input name='id' type='HIDDEN' value='$id'><input name='project' type='HIDDEN' value='$project'>";

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>
<script>
    function changeSt(theObj, firstRun) {
        if (theObj.selectedIndex == 4) {
            if (firstRun != true) document.forms[0].completion.selectedIndex = 1;
            document.forms[0].compl.value = 0;
            document.forms[0].completion.disabled = false;
        } else {
            if (theObj.selectedIndex == 0) {
                document.forms[0].completion.selectedIndex = 0;
                document.forms[0].compl.value = '';
            } else if (theObj.selectedIndex == 1 || theObj.selectedIndex == 2) {
                document.forms[0].completion.selectedIndex = 11;
                document.forms[0].compl.value = 10;
            } else {
                document.forms[0].completion.selectedIndex = 1;
                document.forms[0].compl.value = 0;
            }
            document.forms[0].completion.disabled = true;
        }
    }

    function changeCompletion() {
        document.forms[0].compl.value = document.forms[0].completion.selectedIndex - 1;
    }

    changeSt(document.forms[0].st, true);
</script>