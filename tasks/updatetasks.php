<?php

use phpCollab\Assignments\Assignments;

$checkSession = "true";
include_once '../includes/library.php';

$projects = $container->getProjectsLoader();
$tasks = $container->getTasksLoader();

$project_id = $request->query->get('project') ?: $request->request->get('project');

if (!isset($project_id)) {
    // Redirect to where? Back to tasks list with an error mesage?
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=permissiondenied");
}

$projectDetail = $projects->getProjectById($project_id);

$task_id = str_replace("**", ",", $id);

$listTasks = $tasks->getTasksById($task_id);
$tasks->setTasksCount(count($listTasks));

if ($request->isMethod('post')) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            $acomm = phpCollab\Util::convertData($request->request->get('assignment_comment'));

            $assigned_to = $request->request->get('assign_to');
            $task_status = $request->request->get('task_status');
            $completion = $request->request->get('completion');
            $task_priority = $request->request->get('task_priority');
            $start_date = $request->request->get('start_date');
            $due_date = $request->request->get('due_date');

            $continue = false;

            if ($assigned_to != $strings["no_change"]) {
                $continue = true;
            }

            if ($task_status != $strings["no_change"]) {
                $continue = true;
            }

            if ($completion != "") {
                $continue = true;
            }

            if ($task_priority != $strings["no_change"]) {
                $continue = true;
            }

            if ($start_date != "--") {
                $continue = true;
            }

            if ($due_date != "--") {
                $continue = true;
            }

            if ($continue) {
                foreach ($listTasks as $listTask) {
                    if ($assigned_to != $strings["no_change"]) {
                        $tasks->setAssignedTo($listTask["tas_id"], $assigned_to);
                        $tasks->setAssignedDate($listTask["tas_id"], $dateheure);
                        $assignUpdate = true;
                    }

                    if ($task_status != $strings["no_change"]) {
                        $tasks->setStatus($listTask["tas_id"], $task_status);
                    }

                    if ($completion != "") {
                        $tasks->setCompletion($listTask["tas_id"], $completion);
                    }

                    if ($task_priority != $strings["no_change"]) {
                        $tasks->setPriority($listTask["tas_id"], $task_priority);
                    }

                    if ($start_date != "--") {
                        $tasks->setStartDate($listTask["tas_id"], $start_date);
                    }

                    if ($due_date != "--") {
                        $tasks->setDueDate($listTask["tas_id"], $due_date);
                    }

                    $sameAssign = $listTask["tas_assigned_to"] == $assigned_to;

                    $tasks->setModifiedDate($listTask["tas_id"]);

                    if ($notifications == "true") {
                        $notificationsClass = $container->getNotificationsManager();
                        if ($assigned_to != $strings["no_change"]) {
                            $memberInfo = $members->getMemberById($assigned_to);
                            $memberNotifications = $notificationsClass->getMemberNotifications($assigned_to);
                        } else {
                            $memberInfo = $members->getMemberById($listTask["tas_owner"]);
                            $memberNotifications = $notificationsClass->getMemberNotifications($listTask["tas_owner"]);
                        }

                        if ($task_status != $strings["no_change"] &&
                            $listTask["tas_status"] != $task_status &&
                            $assignUpdate !== true &&
                            $listTask["tas_assigned_to"] != "0" &&
                            $memberNotifications["statusTaskChange"] == "0"
                        ) {

                            try {
                                $tasks->sendTaskNotification($listTask, $projectDetail, $memberInfo,
                                    $strings["noti_statustaskchange1"], $strings["noti_statustaskchange2"]);
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }
                        }

                        if ($task_priority != $strings["no_change"] &&
                            $listTask["tas_priority"] != $task_priority &&
                            $assignUpdate !== true &&
                            $listTask["tas_assigned_to"] != "0" &&
                            $memberNotifications["priorityTaskChange"] == "0"
                        ) {
                            try {
                                $tasks->sendTaskNotification($listTask, $projectDetail, $memberInfo,
                                    $strings["noti_prioritytaskchange1"], $strings["noti_prioritytaskchange2"]);
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }
                        }

                        if ($due_date != "--" &&
                            $listTask["tas_due_date"] != $due_date &&
                            $assignUpdate !== true &&
                            $listTask["tas_assigned_to"] != "0" &&
                            $memberNotifications["duedateTaskChange"] == "0"
                        ) {
                            try {
                                $tasks->sendTaskNotification($listTask, $projectDetail, $memberInfo,
                                    $strings["noti_duedatetaskchange1"], $strings["noti_duedatetaskchange2"]);
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }
                        }
                    }

                    if ($assigned_to != "0" && $sameAssign !== true && $assignUpdate === true) {
                        // Add to assignment table
                        (new Assignments())->addAssignment($listTask["tas_id"], $listTask["tas_owner"], $assigned_to,
                            $dateheure, $acomm);

                        // Check teams and add if necessary
                        $teams = $container->getTeams();
                        $isTeamMember = $teams->isTeamMember($listTask["tas_project"], $assigned_to);

                        if ($isTeamMember === "false") {
                            $teams->addTeam($listTask["tas_project"], $assigned_to, 1, 0);
                        }

                        if ($notifications == "true") {
                            try {
                                $tasks->sendTaskNotification($listTask, $projectDetail, $memberInfo,
                                    $strings["noti_taskassignment1"], $strings["noti_taskassignment2"]);
                            } catch (Exception $e) {
                                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
                            }
                        }
                    }
                }
            }
            phpCollab\Util::headerFunction("../tasks/listtasks.php?project=$project_id&msg=update");
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
    }

}

$includeCalendar = true; //Include Javascript files for the pop-up calendar
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();

if ($request->query->get('report') != "") {
    $reports = $container->getReportsLoader();
    $reportDetail = $reports->getReportsById($request->query->get('report'));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/createreport.php?", $strings["reports"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/resultsreport.php?id=" . $reportDetail["rep_id"],
        $reportDetail["rep_name"], "in"));
} else {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
        $projectDetail["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=$project_id", $strings["tasks"],
        "in"));
}

$blockPage->itemBreadcrumbs($strings["edit_multiple_tasks"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();
$block1->form = "batT";
$block1->openForm("../tasks/updatetasks.php?action=update&#" . $block1->form . "Anchor", null, $csrfHandler);

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["edit_multiple_tasks"]);
$block1->openContent();
$block1->contentTitle($strings["details"]);

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td>{$tasks->getTasksCount()} {$strings["tasks_selected"]}</td>
</tr>
HTML;

$assignTo = $members->getNonClientMembers('mem.name');

echo <<<HTML
		<tr class="odd">
			<td class="leftvalue">{$strings["assigned_to"]}</td>
			<td>
				<select name="assign_to">
					<option value="{$strings["no_change"]}" selected>{$strings["no_change"]}</option>
					<option value="0">{$strings["unassigned"]}</option>
HTML;

if ($session->get("id") == "1") {
    echo '<option value="1">' . $strings["administrator"] . '</option>';
}

foreach ($assignTo as $assignee) {
    echo "<option value='" . $assignee["mem_id"] . "'>" . $assignee["mem_name"] . "</option>";
}

echo <<<HTML
		</select></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["assignment_comment"]} :</td>
			<td><textarea rows="3" style="width: 400px; height: 50px;" name="assignment_comment" cols="43"></textarea></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["status"]} :</td>
			<td><select name="task_status" onchange="changeSt(this)">
				<option value="{$strings["no_change"]}" selected>{$strings["no_change"]}</option>
HTML;

if ($GLOBALS["status"] && count($GLOBALS["status"]) > 0) {
    foreach ($status as $key => $item) {
        echo '<option value=' . $key . '>' . $item . '</option>';
    }
}

echo <<<HTML
		</select></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["completion"]} :</td>
			<td><input name="compl" type="hidden" value="">
				<select name="completion" onchange="changeCompletion(this)">
					<option value="{$strings["no_change"]}" selected>{$strings["no_change"]}</option>
HTML;

for ($i = 0; $i < 11; $i++) {
    $complValue = ($i > 0) ? $i . "0 %" : $i . " %";
    echo "<option value='" . $i . "'>" . $complValue . "</option>";
}

echo <<<HTML
    </select></td></tr>
<tr class="odd">
	<td class="leftvalue">{$strings["priority"]} : </td>
	<td><select name="task_priority">
			<option value="{$strings["no_change"]}" selected>{$strings["no_change"]}</option>
HTML;


$comptPri = count($task_priority);
if ($GLOBALS["priority"] && count($GLOBALS["priority"]) > 0) {
    foreach ($GLOBALS["priority"] as $key => $item) {
        echo '<option value="' . $key . '">' . $item . '</option>';
    }
}


echo "</select></td></tr>";

$start_date = empty($sd) ? '--' : $sd;
$due_date = empty($dd) ? '--' : $dd;

$block1->contentRow($strings["start_date"],
    "<input type='text' name='start_date' id='start_date' size='20' value='$start_date'><input type='button' value=' ... ' id='trigStartDate'>");
echo <<<JavaScript
<script type="text/javascript">
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        {$calendar_common_settings}
    })
</script>
JavaScript;

$block1->contentRow($strings["due_date"],
    "<input type='text' name='due_date' id='due_date' size='20' value='$due_date'><input type='button' value=' ... ' id='trigDueDate'>");
echo <<<JavaScript
<script type="text/javascript">
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        {$calendar_common_settings}
    })
</script>
JavaScript;

echo <<<TR
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><input type="SUBMIT" value="{$strings['update']}"></td>
    </tr>
TR;

echo <<<INPUT
<input name="id" type="HIDDEN" value="{$id}">
<input name="action" type="HIDDEN" value="update">
<input name="project" type="HIDDEN" value="{$project_id}">
INPUT;


$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
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
