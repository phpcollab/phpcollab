<?php
/*
** Application name: phpCollab
** Path by root:  ../tasks/edittask.php
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
** =============================================================================
*/


use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$tasks = $container->getTasksLoader();
$projects = $container->getProjectsLoader();
$teams = $container->getTeams();
$subtasks = $container->getSubtasksLoader();
$assignments = $container->getAssignmentsManager();

$id = $request->query->get("id");
$parentTaskId = $request->query->get("task");

$date = date('Y-m-d');
$timestamp = date('Y-m-d h:i');

//case multiple edit tasks
$multi = strstr($id, "**");

if ($multi != "") {
    phpCollab\Util::headerFunction("../tasks/edittask.php?report={$report}&project={$project}&id={$id}");
}

/**
 * Get the parent task details
 */
$parentTaskDetail = $tasks->getTaskById($parentTaskId);
// get the project id
$project = $parentTaskDetail['tas_project'];

if ($id != "") {
    $subtaskDetail = $subtasks->getById($id);
}

$projectDetail = $projects->getProjectById($parentTaskDetail['tas_project']);

$teamMember = "false";

$teamMember = $teams->isTeamMember($projectDetail["pro_id"], $session->get("id"));

if ($teamMember != "true" && $session->get("profile") != "5") {
    phpCollab\Util::headerFunction("../tasks/viewtask.php?id={$parentTaskId}&msg=taskOwner");
}

if ($request->isMethod('post')) {
    $taskName = $request->request->get("task_name");
    $description = $request->request->get("description");
    $comments = $request->request->get("comments");
    $publish = $request->request->get("published");
    $completion = $request->request->get("completion");
    $taskStatus = $request->request->get("status");
    $estimatedTime = $request->request->get("estimated_time");
    $actualTime = $request->request->get("actual_time");
    $assignedTo = $request->request->get("assigned_to");
    $taskPriority = $request->request->get("priority");
    $startDate = $request->request->get("start_date");
    $dueDate = $request->request->get("due_date");
    $completedDate = $request->request->get("completed_date");
    $updateComments = $request->request->get("update_comments");

    $publish = ($publish === "true") ? "0" : "1";

    $completion = (empty($completion)) ? "0" : $completion;

    // If the completion is 100%, then set status to completed
    if ($completion == "10") {
        $taskStatus = "1";
    }
}

//case update or copy task
if (!empty($id)) {
    if ($request->isMethod('post')) {

        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                //case update or copy task
                if ($request->request->get("action") == "update") {

                    //case copy task
                    if ($request->query->get("docopy") == "true") {
                        //case update task
                        echo "do something!";
                    } else {

                        //compute the average completion of all subtaks of this tasks
                        if ($subtaskDetail["subtas_completion"] != $completion) {
                            $tasks->setCompletion($parentTaskId, $tasks->recalculateSubtaskAverages($parentTaskId));
                        }

                        if ($taskStatus == "1" && $completedDate == "--") {
                            /**
                             * If status is "complete" and the completion date is not set completion date to today
                             */
                            $subtasks->setCompletionDate($id, $date);
                        } else {
                            $subtasks->setCompletionDate($id, $completedDate);
                        }

                        if ($subtaskDetail["subtas_status"] == "1" && $taskStatus != $subtaskDetail["subtas_status"]) {
                            $subtasks->setCompletionDate($id, '');
                        }

                        //if assigned_to not blank and past assigned value blank, set assigned date
                        if ($assignedTo != "0" && $subtaskDetail["subtas_assigned_to"] == "") {
                            $subtasks->setAssignedDate($id, $timestamp);
                        }

                        //if assigned_to different from past value, insert into assignment
                        //add new assigned_to in team members (only if doesn't already exist)
                        if ($assignedTo != $subtaskDetail["subtas_assigned_to"]) {
                            /**
                             * Add to assignment table
                             */
                            $assignments->addAssignment($id, $timestamp, $assignedTo, $timestamp);

                            if (!$teams->isTeamMember($project, $assignedTo)) {
                                /**
                                 * Add to Teams table
                                 */
                                $teams->addTeam($project, $assignedTo, 1, 0);
                            }
                            $msg = "update";
                            /**
                             * Update subTask
                             */
                            $updatedDetails = $subtasks->update($id, $taskName, $description, $assignedTo, $taskStatus,
                                $taskPriority, $startDate,
                                $dueDate, $estimatedTime, $actualTime, $comments, $timestamp, $completion, $publish);

                            //send task assignment mail if notifications = true
                            if ($notifications == "true") {
                                try {
                                    $subtasks->sendNotification("assignment", $updatedDetails, $projectDetail, $session,
                                        $logger);
                                } catch (Exception $exception) {
                                    $logger->error('Subtasks (update)', ['Exception message', $e->getMessage()]);
                                    $error = $strings["action_not_allowed"];
                                }
                            }
                        } else {
                            $msg = "update";
                            /**
                             * Update subTask
                             */
                            $updatedDetails = $subtasks->update($id, $taskName, $description, $assignedTo, $taskStatus,
                                $taskPriority, $startDate,
                                $dueDate, $estimatedTime, $actualTime, $comments, $timestamp, $completion, $publish);

                            if ($notifications == "true") {
                                try {
                                    if ($assignedTo != "0") {
                                        //send status task change mail if notifications = true
                                        if ($taskStatus != $subtaskDetail["subtas_status"]) {
                                            $subtasks->sendNotification("status", $updatedDetails, $projectDetail,
                                                $session, $logger);
                                        }
                                        //send priority task change mail if notifications = true
                                        if ($taskPriority != $subtaskDetail["subtas_priority"]) {
                                            $subtasks->sendNotification("priority", $updatedDetails, $projectDetail,
                                                $session, $logger);
                                        }

                                        if ($dueDate != $subtaskDetail["subtas_due_date"]) {
                                            $subtasks->sendNotification("dueDate", $updatedDetails, $projectDetail,
                                                $session, $logger);
                                        }
                                    }
                                } catch (Exception $exception) {
                                    $logger->error('Subtasks (edit)', ['Exception message', $e->getMessage()]);
                                    $error = $strings["action_not_allowed"];
                                }
                            }
                        }

                        if ($taskStatus != $subtaskDetail["subtas_status"]) {
                            $updateComments .= "\n[status:$taskStatus]";
                        }
                        if ($taskPriority != $subtaskDetail["subtas_priority"]) {
                            $updateComments .= "\n[priority:$taskPriority]";
                        }
                        if ($dueDate != $subtaskDetail["subtas_due_date"]) {
                            $updateComments .= "\n[datedue:$dueDate]";
                        }

                        if (
                            !empty($updateComments)
                            || $taskStatus != $subtaskDetail["subtas_status"]
                            || $taskPriority != $subtaskDetail["subtas_priority"]
                            || $dueDate != $subtaskDetail["subtas_due_date"]) {
                            /**
                             * Add to updates table
                             */
                            $updates = $container->getTaskUpdateService();
                            $updateComments = phpCollab\Util::convertData($updateComments);
                            $updates->addUpdate(2, $id, $session->get("id"), $updateComments);
                        }

                        phpCollab\Util::headerFunction("../subtasks/viewsubtask.php?id={$id}&task={$parentTaskId}&msg={$msg}");
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Subtasks: Edit subtask',
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }

    }

    //set value in form
    $taskName = $subtaskDetail['subtas_name'];
    $description = $subtaskDetail['subtas_description'];
    $startDate = $subtaskDetail['subtas_start_date'];
    $dueDate = $subtaskDetail['subtas_due_date'];
    $completedDate = $subtaskDetail['subtas_complete_date'];
    $estimatedTime = $subtaskDetail['subtas_estimated_time'];
    $actualTime = $subtaskDetail['subtas_actual_time'];
    $comments = $subtaskDetail['subtas_comments'];
    $publish = $subtaskDetail['subtas_published'];
    if ($publish == "0") {
        $checkedPub = "checked";
    }
}

//case add task
if (empty($id)) {

    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                //case add task
                if ($request->request->get("action") == "add") {

                    //concat values from date selector and replace quotes by html code in name
                    $taskName = phpCollab\Util::convertData($taskName);
                    $description = phpCollab\Util::convertData($description);
                    $comments = phpCollab\Util::convertData($comments);

                    /**
                     * Create new subtask
                     */
                    $newSubtaskId = $subtasks->add($parentTaskId, $taskName, $description, $session->get("id"),
                        $assignedTo, $taskStatus,
                        $taskPriority, $startDate, $dueDate, $estimatedTime, $actualTime, $comments, $completion,
                        $publish);

                    if ($taskStatus == "1") {
                        $subtasks->setCompletionDate($newSubtaskId, $date);
                    }

                    //compute the average completion of all subtaks of this tasks
                    $tasks->setCompletion($parentTaskId, $tasks->recalculateSubtaskAverages($parentTaskId));

                    //if assigned_to not blank, set assigned date
                    if ($assignedTo != "0") {
                        $subtasks->setAssignedDate($newSubtaskId, $timestamp);
                    }

                    $assignments->addAssignment($newSubtaskId, $session->get("id"), $assignedTo, $timestamp);

                    //if assigned_to not blank, add to team members (only if doesn't already exist)
                    //add assigned_to in team members (only if doesn't already exist)
                    if ($assignedTo != "0") {
                        if (!$teams->isTeamMember($project, $session->get("id"))) {
                            $teams->addTeam($project, $assignedTo, 1, 0);
                        }

                        //send task assignment mail if notifications = true
                        if ($notifications == "true") {
                            try {
                                $subtasks->sendNotification("assignment", $subtaskDetail, $projectDetail, $session,
                                    $logger);
                            } catch (Exception $exception) {
                                $logger->error('Subtasks (add)', ['Exception message', $e->getMessage()]);
                                $error = $strings["action_not_allowed"];
                            }
                        }
                    }

                    //create task sub-folder if filemanagement = true
                    if ($fileManagement == "true") {
                        phpCollab\Util::createDirectory("../files/$project/$newSubtaskId");
                    }

                    phpCollab\Util::headerFunction("../subtasks/viewsubtask.php?id={$newSubtaskId}&task={$parentTaskId}&msg=add");
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Subtasks: Add subtask',
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
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
    $phases = $container->getPhasesLoader();
    if ($id != "") {
        $tPhase = $parentTaskDetail['tas_parent_phase'];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $projectId = $parentTaskDetail['tas_project'];
    }
    if ($id == "") {
        $tPhase = $phase;
        $projectId = $project;
    }
    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($projectDetail["pro_id"], $tPhase);
}

$bodyCommand = 'onload="document.etDForm.task_name.focus();"';
$includeCalendar = true; //Include Javascript files for the pop-up calendar

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'],
    $projectDetail['pro_name'], "in"));

if ($projectDetail['pro_phase_set'] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail['pro_id'],
        $strings["phases"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase['pha_id'],
        $targetPhase['pha_name'], "in"));
}
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail['pro_id'],
    $strings["tasks"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $parentTaskDetail['tas_id'],
    $parentTaskDetail['tas_name'], "in"));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_subtask"]);
}
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../subtasks/viewsubtask.php?task={$parentTaskId}&id=" . $subtaskDetail['subtas_id'],
        $subtaskDetail['subtas_name'], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_subtask"]);
}

$blockPage->closeBreadcrumbs();

if (!empty($msg)) {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if ($id == "") {
    $block1->form = "etD";
    $submitValue = "add";
    $block1->openForm("../subtasks/editsubtask.php?task={$parentTaskId}&#" . $block1->form . "Anchor", null,
        $csrfHandler);
}
if ($id != "") {
    $block1->form = "etD";
    $submitValue = "update";
    $block1->openForm("../subtasks/editsubtask.php?task={$parentTaskId}&id={$id}&docopy={$docopy}&#" . $block1->form . "Anchor",
        null, $csrfHandler);
}

if (isset($error) && !empty($error)) {
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

$projectLink = $blockPage->buildLink("../projects/viewproject.php?id={$parentTaskDetail["tas_project"]}",
    $parentTaskDetail["tas_pro_name"], "in");
echo <<< HTML
    <tr class="odd">
        <td class="leftvalue">{$strings["project"]} :</td>
        <td>{$projectLink}</td>
    </tr>
HTML;


//Display task's phase
if ($projectDetail['pro_phase_set'] != "0") {
    echo <<< HTML
    <tr class="odd">
        <td class="leftvalue">{$strings["phase"]} :</td>
        <td>{$blockPage->buildLink("../phases/viewphase.php?id={$targetPhase["pha_id"]}", $targetPhase["pha_name"],
        "in")}</td>
    </tr>
HTML;

}
echo <<< HTML
<tr class="odd">
    <td class="leftvalue">{$strings["task"]} :</td>
    <td>{$blockPage->buildLink('../tasks/viewtask.php?id=' . $parentTaskDetail["tas_id"], $parentTaskDetail["tas_name"],
    "in")}</td></tr>
<tr class="odd"><td class="leftvalue">{$strings["organization"]} :</td><td>{$projectDetail["pro_org_name"]}</td></tr>
HTML;


$block1->contentTitle($strings["details"]);

$subtaskDetail['subtas_name'] = ($docopy != "true") ? $subtaskDetail['subtas_name'] : $strings["copy_of"] . ' ' . $subtaskDetail['subtas_name'];

echo <<< HTML
    <tr class="odd">
        <td class="leftvalue">{$strings["name"]} :</td>
        <td><input size="44" value="{$subtaskDetail['subtas_name']}" style="width: 400px" name="task_name" maxlength="100" type="text"></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["description"]} :</td>
        <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$subtaskDetail['subtas_description']}</textarea></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["assigned_to"]} :</td>
        <td><select name="assigned_to">
HTML;

if ($subtaskDetail['subtas_assigned_to'] == "0") {
    $selected = "selected";
} else {
    $selected = "";
}
echo <<< HTML
    <option value="0" {$selected}>{$strings["unassigned"]}</option>
HTML;

$teamMembers = $teams->getTeamByProjectId($project, 'mem.name');

foreach ($teamMembers as $team_member) {
    $clientUser = "";
    if ($team_member['tea_mem_profil'] == "3") {
        $clientUser = " (" . $strings["client_user"] . ")";
    }
    if ($subtaskDetail['subtas_assigned_to'] == $team_member['tea_mem_id']) {
        $selected = "selected";
    } else {
        $selected = "";
    }
    echo <<< HTML
        <option value="{$team_member["tea_mem_id"]}" {$selected}>{$team_member["tea_mem_login"]} / {$team_member["tea_mem_name"]} {$clientUser}</option>
HTML;
}

echo <<< HTML
    </select></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["status"]} :</td>
    <td><select name="status" onchange="changeSt(this)">
HTML;


$comptSta = count($status);

for ($i = 0; $i < $comptSta; $i++) {
    if ($subtaskDetail['subtas_status'] == $i) {
        $selected = "selected";
    } else {
        $selected = "";
    }
    echo <<< HTML
        <option value="{$i}" {$selected}>{$status[$i]}</option>";
HTML;
}

echo <<< HTML
    </select></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["completion"]} :</td>
    <td><select name="completion" onchange="changeCompletion(this)">
HTML;


for ($i = 0; $i < 11; $i++) {
    $completionValue = ($i > 0) ? $i . "0 %" : $i . " %";
    if ($subtaskDetail['subtas_completion'] == $i) {
        $selected = "selected";
    } else {
        $selected = "";
    }
    echo <<< HTML
        <option value="{$i}" {$selected}>{$completionValue}</option>";
HTML;
}

echo <<< HTML
    </select></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["priority"]} :</td>
    <td><select name="priority">
HTML;


$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    if ($subtaskDetail['subtas_priority'] == $i) {
        $selected = "selected";
    } else {
        $selected = "";
    }
    echo <<< HTML
        <option value="{$i}" {$selected}>{$priority[$i]}</option>";
HTML;

}

echo <<< HTML
    </select></td>
</tr>
HTML;

if ($subtaskDetail["subtas_start_date"] == "") {
    $subtaskDetail["subtas_start_date"] = $date;
}
if ($subtaskDetail["subtas_due_date"] == "") {
    $subtaskDetail["subtas_due_date"] = "--";
}
if ($subtaskDetail["subtas_complete_date"] == "") {
    $subtaskDetail["subtas_complete_date"] = "--";
}

$block1->contentRow($strings["start_date"],
    "<input type='text' name='start_date' id='start_date' size='20' value='{$subtaskDetail["subtas_start_date"]}'><input type='button' value=' ... ' id='trigStartDate'>");

echo <<< JavaScript
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "start_date",
        button         :    "trigStartDate",
        {$calendar_common_settings}
    })
</script>
JavaScript;

$block1->contentRow($strings["due_date"],
    "<input type='text' name='due_date' id='due_date' size='20' value='{$subtaskDetail["subtas_due_date"]}'><input type='button' value=' ... ' id='trigDueDate'>");
echo <<< JavaScript
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "due_date",
        button         :    "trigDueDate",
        {$calendar_common_settings}
    })
</script>
JavaScript;


if ($id != "") {
    $block1->contentRow($strings["complete_date"],
        "<input type='text' name='completed_date' id='complete_date' size='20' value='{$subtaskDetail["subtas_complete_date"]}'><input type='button' value=' ... ' id='trigCompleteDate'>");
    echo <<< JavaScript
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "complete_date",
        button         :    "trigCompleteDate",
        {$calendar_common_settings}
    })
</script>
JavaScript;

}

echo <<< HTML
        <tr class="odd">
			<td class="leftvalue">{$strings["estimated_time"]} :</td>
			<td><input size="32" value="{$subtaskDetail["subtas_estimated_time"]}" style="width: 250px" name="estimated_time" maxlength="32" type="text">&nbsp;{$strings["hours"]}</td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["actual_time"]} :</td>
			<td><input size="32" value="{$subtaskDetail["subtas_actual_time"]}" style="width: 250px" name="actual_time" maxlength="32" type="text">&nbsp;{$strings["hours"]}</td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["comments"]} :</td>
			<td><textarea rows="10" style="width: 400px; height: 160px;" name="comments" cols="47">{$subtaskDetail["subtas_comments"]}</textarea></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["published"]} :</td>
			<td><input size="32" value="true" name="published" type="checkbox" {$checkedPub}></td>
		</tr>
HTML;


if ($id != "") {
    $block1->contentTitle($strings["updates_subtask"]);
    echo <<< HTML
		<tr class="odd">
			<td class="leftvalue">{$strings["comments"]} :</td>
			<td><textarea rows="10" style="width: 400px; height: 160px;" name="update_comments" cols="47"></textarea></td>
		</tr>
HTML;

}

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><button type="submit" name="action" value="{$submitValue}">{$strings["save"]}</button></td>
</tr>
HTML;


$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
?>

<script>
    function changeSt(theObj, firstRun) {
        if (theObj.selectedIndex === 3) {
            if (firstRun !== true) document.etDForm.completion.selectedIndex = 0;
            document.etDForm.completion.disabled = false;
        } else {
            if (theObj.selectedIndex === 0 || theObj.selectedIndex === 1) {
                document.etDForm.completion.selectedIndex = 10;
            } else {
                document.etDForm.completion.selectedIndex = 0;
            }
            document.etDForm.completion.disabled = true;
        }
    }

    function changeCompletion() {
        document.etDForm.compl.value = document.etDForm.completion.selectedIndex;
    }

    changeSt(document.etDForm.status, true);
</script>
