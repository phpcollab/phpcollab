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
** DESC: Screen: edit task page
**
** HISTORY:
**  05/11/2004  -   fixed 1059973
**  12/01/2005  -   cleaned code
**  12/03/2005  -   fixed mssql bug for worked hours
**  19/05/2005  -   fixed and &amp; in link
**  22/05/2005  -   added subtask copy
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


use phpCollab\Assignments\Assignments;
use phpCollab\Files\Files;
use phpCollab\Invoices\Invoices;
use phpCollab\Notifications\Notifications;
use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Updates\Updates;

$checkSession = "true";
include_once '../includes/library.php';


if (!empty($request->query->get('id'))) {
    $task_id = $request->query->get('id');
} else {
    $task_id = $request->query->get('task');
}

$docopy = $request->query->get('docopy');

if (strstr($task_id, "**")) {
    phpCollab\Util::headerFunction("../tasks/updatetasks.php?report={$request->query->get('report')}&project={$request->query->get('project')}&id={$task_id}");
}

$tasks = new Tasks();

$taskDetail = null;
$errors = null;

if ($request->isMethod('post')) {
    if ($request->request->get('task_name') != "") {
        $request->request->get('task_name', filter_var($request->request->get('task_name'), FILTER_SANITIZE_STRING));
        if ($request->request->get('task_name') == "") {
            $errors .= 'Please enter a valid task name.<br/><br/>';
        }
    } else {
        $errors .= 'Please enter a task name.<br/>';
    }
}

/*
 * If POST and no task name, then display error.  Will need to get task details for form
 */

if ($request->isMethod('post') && !is_null($errors) && !empty($task_id)) {
    $taskDetail = $tasks->getTaskById(filter_var($task_id, FILTER_VALIDATE_INT));
    $project = $taskDetail['tas_project'];
} else {
    if (!empty($task_id) && $request->request->get('action') != "update" && $request->request->get('action') != "add") {
        $taskDetail = $tasks->getTaskById(filter_var($task_id, FILTER_VALIDATE_INT));
        $project = $taskDetail['tas_project'];
    } else {
        $project = $request->query->get('project');
    }

}

$projects = new Projects();

$projectDetail = $projects->getProjectById($project);


$teams = new Teams();
$phases = new Phases();

// Check to see if the task owner == the current user, if so then consider them a "team member", otherwise
// check to see if they are in the team
$teamMember = "false";
if (!empty($taskDetail) && $taskDetail["tas_owner"] === $session->get("idSession")) {
    $teamMember = "true";
} else {
    $teamMember = $teams->isTeamMember($project, $session->get("idSession"));
}

if ($teamMember == "false" && $session->get("profile") != "5") {
    phpCollab\Util::headerFunction("../tasks/listtasks.php?project={$project}&msg=taskOwner");
}

//case update or copy task
if (
    $request->isMethod('post')
    && !empty($request->request->get('action'))
    && !empty($request->request->get('task_name'))
) {

    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            $assignments = new Assignments();
            $files = new Files();

            if ($enableInvoicing == "true") {
                $invoices = new Invoices();
            }

            if ($notifications == "true") {
                $notificationsClass = new Notifications();
            }

            $form_data = [
                "name" => $request->request->get('task_name'),
                "description" => $request->request->get('description'),
                "comments" => $request->request->get('comments'),
                "status" => $request->request->get('taskStatus'),
                "old_status" => $request->request->get('old_status'),
                "completion" => $request->request->get('completion'),
                "completion_date" => $request->request->get('complete_date'),
                "invoicing" => $request->request->get('invoicing'),
                "priority" => $request->request->get('priority'),
                "old_priority" => $request->request->get('old_priority'),
                "worked_hours" => $request->request->get('worked_hours'),
                "assigned_to" => $request->request->get('assigned_to'),
                "old_assigned_to" => $request->request->get('old_assigned_to'),
                "start_date" => $request->request->get('start_date'),
                "due_date" => $request->request->get('due_date'),
                "old_due_date" => $request->request->get('old_due_date'),
                "estimated_time" => $request->request->get('estimated_time'),
                "actual_time" => $request->request->get('actual_time'),
                "project" => $request->request->get('project'),
                "old_project" => $request->request->get('old_project'),
                "published" => $request->request->get('published'),
                "phase" => $request->request->get('phase'),
            ];

            /**
             * Common functionality between update and add
             */


            /*
             * Update task
             */
            if (
                is_null($errors)
                && !empty($task_id)
                && $request->request->get('action') == "update"
            ) {
                if ($form_data["assgnment_comment"]) {
                    $form_data["assignment_comment"] = $form_data["assgnment_comment"];
                }

                //Change task status if parent phase is suspended, complete or not open.
                if ($projectDetail['pro_phase_set'] != "0") {
                    $currentPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($project, $phase);
                    if ($form_data["status"] == 3 && $currentPhase['pha_status'] != 1) {
                        $form_data["status"] = 4;
                    }
                }

                if (empty($form_data["published"])) {
                    $form_data["published"] = "1";
                }

                // Copy Task
                if ($docopy == "true") {
                    if ($form_data["completion"] == "10") {
                        $form_data["taskStatus"] = "1";
                    }

                    if (empty($form_data["invoicing"])) {
                        $form_data["invoicing"] = "0";
                    }

                    if (empty($form_data["worked_hours"])) {
                        $form_data["worked_hours"] = "0.00";
                    }

                    try {
                        $newTask = $tasks->addTask($form_data["project"], $form_data["name"], $form_data["description"],
                            $session->get("idSession"), $form_data["assigned_to"], $form_data["status"], $form_data["priority"],
                            $form_data["start_date"], $form_data["due_date"], $form_data["estimated_time"],
                            $form_data["actual_time"], $form_data["comments"], $form_data["published"], $form_data["completion"],
                            ($form_data["phase"] != 0) ? $form_data["phase"] : 0, $form_data["invoicing"],
                            $form_data["worked_hours"]);

                    } catch (Exception $e) {
                        // Log the exception
                    }

                    if ($newTask) {
                        $newTaskId = $newTask["tas_id"];
                        // Check for any subtasks
                        $listSubTasks = $tasks->getSubtasksByParentTaskId($task_id);
                        foreach ($listSubTasks as $subTask) {
                            $tasks->addSubTask($newTaskId, $subTask["subtas_name"], $subTask["subtas_description"],
                                $subTask["subtas_owner"], $subTask["subtas_assigned_to"], $subTask["subtas_status"],
                                $subTask["subtas_priority"], $subTask["subtas_start_date"], $subTask["subtas_due_date"],
                                $subTask["subtas_complete_date"], $subTask["subtas_estimated_time"], $subTask["subtas_actual_time"],
                                $subTask["comments"], $subTask["subtas_published"], $subTask["subtas_completion"]);
                        }

                        if ($enableInvoicing == "true") {
                            $detailInvoice = $invoices->getInvoicesByProjectId($project);

                            if ($detailInvoice["inv_status"] == "0") {
                                try {
                                    $newInvoiceId = $invoices->addInvoiceItem($form_data["name"], $form_data["description"],
                                        $detailInvoice["inv_id"], $form_data["invoicing"], ($form_data["status"] == "1") ? 1 : 0, 1,
                                        $newTaskId, $form_data["worked_hours"]);
                                } catch (Exception $e) {
                                    // Log the exception
                                }
                            }
                        }

                        if ($form_data["status"] == "1" && $form_data["complete_date"] != "--") {
                            $tasks->setCompletionDateForTaskById($newTaskId, $dateheure);
                        }

                        //if assigned_to not blank, set assigned date
                        if ($form_data["assigned_to"] != "0") {
                            $tasks->updateAssignedDate($newTaskId, $dateheure);
                        }

                        $assignmentId = $assignments->addAssignment($newTaskId, $session->get("idSession"), $form_data["assigned_to"], $dateheure);

                        //if assigned_to not blank, add to team members (only if doesn't already exist)
                        if ($form_data["assigned_to"] != "0") {
                            $teamMember = $teams->isTeamMember($project, $form_data["assigned_to"]);

                            if (!$teamMember) {
                                $teamMemberId = $teams->addTeam($project, $form_data["assigned_to"], 1, 0);
                            }

                            //send task assignment mail if notifications = true
                            if ($notifications == "true") {
                                try {
                                    $tasks->sendTaskNotification($newTask, $projectDetail, $memberInfo, $strings["noti_taskassignment1"], $strings["noti_taskassignment2"]);
                                } catch (Exception $e) {
                                    // Log the exception
                                }

                            }
                        }

                        //create task sub-folder if filemanagement = true
                        if ($fileManagement == "true") {
                            phpCollab\Util::createDirectory("files/{$project}/{$newTaskId}");
                        }

                        phpCollab\Util::headerFunction("../tasks/viewtask.php?id={$newTaskId}&msg=addAssignment");
                    }
                } else {

                    if (empty($form_data["published"])) {
                        $form_data["published"] = "1";
                    }
                    if ($form_data["completion"] == "10") {
                        $form_data["status"] = "1";
                    }

                    //recompute number of completed tasks of the project
                    $projectDetail['pro_name'] = phpCollab\Util::projectComputeCompletion($projectDetail);

                    if (empty($form_data["invoicing"])) {
                        $form_data["invoicing"] = "0";
                    }

                    if ($form_data["status"] == "1" && $form_data["complete_date"] == "--") {
                        $tasks->setCompletionDateForTaskById($task_id, $date);
                    } else {
                        $tasks->setCompletionDateForTaskById($task_id, $form_data["complete_date"]);
                    }

                    if ($form_data["old_status"] == "1" && $form_data["status"] != $form_data["old_status"]) {
                        $tasks->setCompletionDateForTaskById($task_id, '');
                    }

                    //if project different from past value, set project number in tasks table
                    if ($project != $form_data["old_project"]) {
                        $tasks->setProjectByTaskId($project, $task_id);

                        $files->setProjectByTaskId($project, $task_id);
                        phpCollab\Util::createDirectory("files/$project/$task_id");

                        $dir = opendir("../files/{$form_data["old_project"]}/{$task_id}");

                        if (is_resource($dir)) {
                            while ($v = readdir($dir)) {
                                if ($v != '.' && $v != '..') {
                                    try {
                                        copy("../files/{$form_data["old_project"]}/{$task_id}/" . $v, "../files/{$project}/{$task_id}/" . $v);
                                        unlink("../files/{$form_data["old_project"]}/{$task_id}/" . $v);
                                    } catch (Exception $e) {
                                        // Log the exception
                                    }
                                }
                            }
                        }

                        //recompute number of completed tasks of the old project
                        $oldproject = $projects->getProjectById($form_data["old_project"]);

                        phpCollab\Util::projectComputeCompletion($oldproject);
                    }

                    if ($enableInvoicing == "true") {
                        if ($form_data["status"] == "1") {
                            $completeItem = "1";
                        } else {
                            $completeItem = "0";
                        }

                        $detailInvoice = $invoices->getInvoicesByProjectId($project);

                        if ($detailInvoice["inv_status"] == "0") {
                            $invoiceItemsId = $invoices->updateInvoiceItems($form_data["invoicing"], $completeItem,
                                $form_data["worked_hours"], $task_id);
                        }
                    }

                    //if assigned_to not blank and past assigned value blank, set assigned date
                    if ($form_data["assigned_to"] != "0" && $form_data["old_assigned"] == "") {
                        $tasks->updateAssignedDate($task_id, $dateheure);
                    }

                    //if assigned_to different from past value, insert into assignment
                    //add new assigned_to in team members (only if doesn't already exist)
                    if ($form_data["assigned_to"] != $form_data["old_assigned_to"]) {
                        $memberInfo = $members->getMemberById($form_data["assigned_to"]);
                        $memberNotifications = $notificationsClass->getMemberNotifications($form_data["assigned_to"]);

                        // Update the assignment table
                        $assignments->addAssignment($task_id, $session->get("idSession"), $form_data["assigned_to"], $dateheure);

                        // Check to see if the new "assigned_to" member id is a team member, if not then add them
                        $isTeamMember = $teams->isTeamMember($project, $form_data["assigned_to"]);

                        if (!$isTeamMember) {
                            $teams->addTeam($project, $form_data["assigned_to"], 1, 0);
                        }

                        $msg = "updateAssignment";

                        $tasks->updateTask(
                            $form_data["name"],
                            $form_data["description"],
                            $form_data["assigned_to"],
                            $form_data["status"],
                            $form_data["priority"],
                            $form_data["start_date"],
                            $form_data["due_date"],
                            $form_data["estimated_time"],
                            $form_data["actual_time"],
                            $form_data["comments"],
                            date('Y-m-d h:i'),
                            $form_data["completion"],
                            ($form_data["phase"] != 0) ? $form_data["phase"] : 0,
                            $form_data["published"],
                            $form_data["invoicing"],
                            $form_data["worked_hours"],
                            $task_id
                        );

                        // Get the updated task details to be passed
                        $updatedTaskDetails = $tasks->getTaskById($task_id);

                        //send task assignment mail if notifications = true
                        if ($notifications == "true") {
                            try {
                                $tasks->sendTaskNotification($updatedTaskDetails, $projectDetail, $memberInfo, $strings["noti_taskassignment1"], $strings["noti_taskassignment2"]);
                            } catch (Exception $e) {
                                // Log the exception
                            }
                        }

                    } else {
                        $msg = "update";

                        $tasks->updateTask(
                            $form_data["name"],
                            $form_data["description"],
                            $form_data["assigned_to"],
                            $form_data["status"],
                            $form_data["priority"],
                            $form_data["start_date"],
                            $form_data["due_date"],
                            $form_data["estimated_time"],
                            $form_data["actual_time"],
                            $form_data["comments"],
                            date('Y-m-d h:i'),
                            $form_data["completion"],
                            ($form_data["phase"] != 0) ? $form_data["phase"] : 0,
                            $form_data["published"],
                            $form_data["invoicing"],
                            $form_data["worked_hours"],
                            $task_id
                        );

                        $updatedTaskDetails = $tasks->getTaskById($task_id);

                        $memberInfo = $members->getMemberById($updatedTaskDetails["tas_owner"]);
                        $memberNotifications = $notificationsClass->getMemberNotifications($updatedTaskDetails["tas_owner"]);

                        if ($notifications == "true") {

                            if (
                                $form_data["assigned_to"] != "0" &&
                                $form_data["status"] != $form_data["old_status"]
                            ) {
                                try {
                                    $tasks->sendTaskNotification(
                                        $updatedTaskDetails, $projectDetail, $memberInfo,
                                        $strings["noti_statustaskchange1"], $strings["noti_statustaskchange2"]
                                    );
                                } catch (Exception $e) {
                                    // Log the exception
                                }
                            }

                            if (
                                $form_data["assigned_to"] != "0" &&
                                $form_data["priority"] != $form_data["old_priority"]
                            ) {
                                try {
                                    $tasks->sendTaskNotification($updatedTaskDetails, $projectDetail, $memberInfo, $strings["noti_prioritytaskchange1"], $strings["noti_prioritytaskchange2"]);
                                } catch (Exception $e) {
                                    // Log the exception
                                }
                            }

                            if (
                                $form_data["assigned_to"] != "0" &&
                                $form_data["due_date"] != $form_data["old_due_date"]
                            ) {
                                try {
                                    $tasks->sendTaskNotification($updatedTaskDetails, $projectDetail, $memberInfo, $strings["noti_duedatetaskchange1"], $strings["noti_duedatetaskchange2"]);
                                } catch (Exception $e) {
                                    // Log the exception
                                }
                            }
                        }
                    }
                    // continue update code....
                    if ($form_data["status"] != $form_data["old_status"]) {
                        $assignment_comment .= "\n[status: {$form_data["status"]}]";
                    }

                    if ($form_data["priority"] != $form_data["old_priority"]) {
                        $assignment_comment .= "\n[priority: {$GLOBALS["priority"][$form_data["priority"]]} ({$form_data["priority"]})]";
                    }

                    if ($form_data["due_date"] != $form_data["old_due_date"]) {
                        $assignment_comment .= "\n[datedue: {$form_data["due_date"]}";
                    }

                    if (
                        $assignment_comment != "" ||
                        $form_data["status"] != $form_data["old_status"] ||
                        $form_data["priority"] != $form_data["old_priority"] ||
                        $form_data["due_date"] != $form_data["old_due_date"]
                    ) {
                        $updates = new Updates();
                        $updates->addUpdate(1, $task_id, $session->get("idSession"), $assignment_comment);
                    }

                    phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$task_id&msg=$msg");
                }
            }

            /*
             * Add task
             */
            if (
                is_null($errors)
                && empty($task_id)
                && !empty($request->request->get('action'))
                && $request->request->get('action') == "add"
                && !empty($form_data["name"])
            ) {

                if ($projectDetail['pro_phase_set'] == 1) {
                    $currentPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($project, $phase);
                    if ($form_data["status"] == 3 && $currentPhase['pha_status'] != 1) {
                        $form_data["status"] = 4;
                    }
                }

                if ($form_data["completion"] == "10") {
                    $form_data["status"] = 1;
                }

                if (!empty($form_data["published"])) {
                    $published = 1;
                }

                if (!empty($form_data["invoicing"])) {
                    $invoicing = 0;
                }

                if (!empty($form_data["worked_hours"])) {
                    $worked_hours = "0.00";
                }

                try {
                    $newTask = $tasks->addTask(
                        $project,
                        $form_data["name"],
                        $form_data["description"],
                        $session->get("idSession"),
                        $form_data["assigned_to"],
                        $form_data["status"],
                        $form_data["priority"],
                        $form_data["start_date"],
                        $form_data["due_date"],
                        $form_data["estimated_time"],
                        $form_data["actual_time"],
                        $form_data["comments"],
                        $form_data["published"],
                        $form_data["completion"],
                        ($form_data["phase"] != 0) ? $form_data["phase"] : 0,
                        $form_data["invoicing"],
                        $form_data["worked_hours"]
                    );

                } catch (Exception $e) {
                    // Log the exception
                }

                // If new task is created successfully, then continue, otherwise display error
                if ($newTask) {
                    $newTaskId = $newTask["tas_id"];

                    $memberInfo = $members->getMemberById($session->get("idSession"));

                    if ($enableInvoicing == "true") {
                        if ($form_data["status"] == "1") {
                            $completeItem = "1";
                        } else {
                            $completeItem = "0";
                        }

                        $detailInvoice = $invoices->getInvoicesByProjectId($project);

                        if ($detailInvoice["inv_status"] == "0") {
                            try {
                                $invNum = $invoices->addInvoiceItem(
                                    $form_data["name"],
                                    $form_data["description"],
                                    $detailInvoice["inv_id"],
                                    $form_data["invoicing"],
                                    $completeItem,
                                    1,
                                    $newTaskId,
                                    $form_data["worked_hours"]
                                );
                            } catch (Exception $e) {
                                // Log the exception
                            }
                        }
                    }

                    if ($form_data["status"] == "1") {
                        $tasks->setCompletionDateForTaskById($newTaskId, date('Y-m-d h:i'));
                    }

                    //if assigned_to not blank, set assigned date
                    if ($form_data["assigned_to"] != "0") {
                        // Update the assigned date
                        $tasks->updateAssignedDate($newTaskId, date('Y-m-d h:i'));

                        // Add entry to the assigned table
                        $assignments->addAssignment(
                            $newTaskId,
                            $session->get("idSession"),
                            $form_data["assigned_to"],
                            date('Y-m-d h:i')
                        );

                        // Check to see if the assigned_to user is a team member
                        $isTeamMember = $teams->isTeamMember($project, $form_data["assigned_to"]);

                        // If not a team member, then add the assigned_to user to the team members table
                        if (!$isTeamMember) {
                            $teams->addTeam($project, $form_data["assigned_to"], 1, 0);
                        }

                        if ($notifications == "true") {
                            try {
                                $tasks->sendTaskNotification($newTask, $projectDetail, $memberInfo, $strings["noti_taskassignment1"], $strings["noti_taskassignment2"]);
                            } catch (Exception $e) {
                                // Log the exception
                            }
                        }
                    }

                    //create task sub-folder if filemanagement = true
                    if ($fileManagement == "true") {
                        phpCollab\Util::createDirectory("files/$project/$newTaskId");
                    }

                    phpCollab\Util::headerFunction("../tasks/viewtask.php?id=$newTaskId&msg=addAssignment");
                }
            }
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

//set value in form
$task_name = $taskDetail['tas_name'];
$task_description = $taskDetail['tas_description'];
$start_date = $taskDetail['tas_start_date'];
$due_date = $taskDetail['tas_due_date'];
$complete_date = $taskDetail['tas_complete_date'];
$estimated_time = $taskDetail['tas_estimated_time'];
$actual_time = $taskDetail['tas_actual_time'];
$comments = $taskDetail['tas_comments'];
$published = $taskDetail['tas_published'];
$worked_hours = $taskDetail['tas_worked_hours'];

if ($published == "0") {
    $checkedPub = "checked";
}


if ($projectDetail['pro_org_id'] == "1") {
    $projectDetail['pro_org_name'] = $strings["none"];
}

if ($projectDetail['pro_phase_set'] != "0") {
    if ($task_id != "") {
        $tPhase = $taskDetail['tas_parent_phase'];
        if (!$tPhase) {
            $tPhase = '0';
        }
        $project = $subtaskDetail['tas_project'];
    }

    if ($task_id == "") {
        $tPhase = $phase;
        if (!$tPhase) {
            $tPhase = '0';
        }
    }

    $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($project, $tPhase);
}

$bodyCommand = "onload=\"document.etDForm.task_name.focus();\"";

$headBonus = "";
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail['pro_id'], $projectDetail['pro_name'], "in"));
if ($projectDetail['pro_phase_set'] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail['pro_id'], $strings["phases"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase["pha_id"], $targetPhase["pha_name"], "in"));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail['pro_id'], $strings["tasks"], "in"));

if ($task_id == "") {
    $blockPage->itemBreadcrumbs($strings["add_task"]);
}

if ($task_id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail['tas_id'], $taskDetail['tas_name'], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_task"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();


if ($task_id == "") {
    $block1->form = "etD";
    $submitValue = "add";
    $block1->openForm("../tasks/edittask.php?project=$project&#" . $block1->form . "Anchor", null, $csrfHandler);
}

if ($task_id != "") {
    $block1->form = "etD";
    $submitValue = "update";
    $block1->openForm("../tasks/edittask.php?project=$project&id=$task_id&docopy=$docopy&#" . $block1->form . "Anchor", null, $csrfHandler);
    echo <<<HIDDENFIELDS
<input type="hidden" name="old_assigned_to" value="{$taskDetail["tas_assigned_to"]}">
<input type="hidden" name="old_assigned" value="{$taskDetail["tas_assigned"]}">
<input type="hidden" name="old_priority" value="{$taskDetail["tas_priority"]}">
<input type="hidden" name="old_status" value="{$taskDetail["tas_status"]}">
<input type="hidden" name="old_due_date" value="{$taskDetail['tas_due_date']}">
<input type="hidden" name="old_project" value="{$taskDetail['tas_project']}">
HIDDENFIELDS;
}

if (!empty($errors)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($errors);
}

if ($task_id == "") {
    $block1->heading($strings["add_task"]);
}

if ($task_id != "") {
    if ($docopy == "true") {
        $block1->heading($strings["copy_task"] . " : " . $taskDetail['tas_name']);
    } else {
        $block1->heading($strings["edit_task"] . " : " . $taskDetail['tas_name']);
    }
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

echo <<< Project
    <tr class="odd">
    <td style="vertical-align:top" class="leftvalue">{$strings["project"]} :</td>
    <td>
    <select name="project">
Project;

if ($projectsFilter == "true") {
    $listProjects = $projects->getFilteredProjectsByTeamMember($session->get("idSession"));
} else {
    $listProjects = $projects->getAllProjects();
}

foreach ($listProjects as $proj) {
    if ($proj["pro_id"] == $projectDetail['pro_id']) {
        echo '<option value="' . $proj["pro_id"] . '" selected>' . $proj["pro_name"] . '</option>';
    } else {
        echo '<option value="' . $proj["pro_id"] . '">' . $proj["pro_name"] . '</option>';
    }
}
echo "</select></td></tr>";


//Display task's phase
if ($projectDetail['pro_phase_set'] != "0") {
    $viewPhaseLink = $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase["pha_id"], $targetPhase["pha_name"], "in");
    echo <<<HTML
    <tr class="odd">
        <td style="vertical-align:top" class="leftvalue">{$strings["phase"]} :</td>
        <td>{$viewPhaseLink}</td>
    </tr>
HTML;

}
echo <<<HTML
    <tr class="odd">
        <td style="vertical-align:top" class="leftvalue">{$strings["organization"]} :</td>
        <td>{$projectDetail["pro_org_name"]}</td>
    </tr>
HTML;
$block1->contentTitle($strings["details"]);

echo <<<HTML
    <tr class="odd">
        <td style="vertical-align:top" class="leftvalue">{$strings["name"]} :</td>
        <td><input size="44" value="
HTML;

if ($docopy == "true") {
    echo $strings["copy_of"];
}

echo <<<HTML
{$task_name}" style="width: 400px" name="task_name" maxlength="100" type="text"></td>
    </tr>
HTML;

echo <<<Description
    <tr class="odd">
        <td style="vertical-align:top" class="leftvalue">{$strings["description"]} :</td>
            <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$task_description}</textarea></td>
        </tr>
Description;

echo <<<AssignedTo
        <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["assigned_to"]} :</td>
            <td><select name="assigned_to">
AssignedTo;


if ($taskDetail['tas_assigned_to'] == "0") {
    echo '<option value="0" selected>' . $strings["unassigned"] . '</option>';
} else {
    echo '<option value="0">' . $strings["unassigned"] . '</option>';
}

$teamList = $teams->getTeamByProjectId($project, 'mem.name');

foreach ($teamList as $team_member) {
    $clientUser = "";

    if ($team_member["tea_mem_profil"] == "3") {
        $clientUser = " (" . $strings["client_user"] . ")";
    }


    if (!empty($taskDetail['tas_assigned_to']) && $taskDetail['tas_assigned_to'] === $team_member["tea_mem_id"]) {
        echo <<<Option
<option value="{$team_member["tea_mem_id"]}" selected>{$team_member["tea_mem_login"]} / {$team_member["tea_mem_name"]}{$clientUser} </option>
Option;
    } else {
        echo <<<Option
<option value="{$team_member["tea_mem_id"]}">{$team_member["tea_mem_login"]} / {$team_member["tea_mem_name"]}{$clientUser}</option>
Option;

    }
}
echo "      </select></td>
        </tr>";

//Select phase
if ($projectDetail['pro_phase_set'] != "0") {
    $projectTarget = $projectDetail['pro_id'];
    $phaseList = $phases->getPhasesByProjectId($projectTarget, 'order_num');

    echo '<tr class="odd"><td style="vertical-align:top" class="leftvalue">' . $strings["phase"] . ' :</td><td>';
    echo '<select name="phase">';

    $phaseCounter = 0;
    foreach ($phaseList as $item) {
        $phaseNum = $item['pha_order_num'];
        if ($taskDetail['tas_parent_phase'] == $phaseNum || $phase == $phaseNum) {
            echo '<option value="' . $phaseNum . '" selected>' . $item["pha_name"] . '</option>';
        } else {
            echo '<option value="' . $phaseNum . '">' . $item["pha_name"] . '</option>';
        }
    }
    echo "</select></td></tr>";
}

echo "<tr class='odd'><td style='vertical-align:top' class='leftvalue'>" . $strings["status"] . " :</td><td><select name='taskStatus' onchange=\"changeSt(this)\">";

$comptSta = count($status);

foreach ($status as $key => $item) {
    if (!empty($taskDetail['tas_status']) && $taskDetail['tas_status'] == $key) {
        echo '<option value="' . $key . '" selected>' . $item . '</option>';
    } else {
        if (empty($taskDetail['tas_status']) && $key === 2) {
            echo '<option value="' . $key . '" selected>' . $item . '</option>';
        } else {
            echo '<option value="' . $key . '">' . $item . '</option>';
        }
    }
}

echo <<<HTML
    </select>
            </td>
        </tr>
        <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["completion"]} :</td>
            <td>
                <select name="completion">
HTML;


for ($i = 0; $i < 11; $i++) {
    $complValue = ($i > 0) ? $i . "0 %" : $i . " %";

    if ($taskDetail['tas_completion'] == $i) {
        echo '<option value="' . $i . '" selected>' . $complValue . '</option>';
    } else {
        echo '<option value="' . $i . '">' . $complValue . '</option>';
    }
}

echo "          </select>
            </td>
        </tr>
        <tr class='odd'>
            <td style='vertical-align:top' class='leftvalue'>" . $strings["priority"] . " :</td>
            <td><select name='priority'>";

$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    if ($taskDetail['tas_priority'] == $i) {
        echo "<option value='$i' selected>$priority[$i]</option>";
    } else {
        echo "<option value='$i'>$priority[$i]</option>";
    }
}

echo "</select></td></tr>";

if ($start_date == "") {
    $start_date = $date;
}
if ($due_date == "") {
    $due_date = "--";
}
if ($complete_date == "") {
    $complete_date = "--";
}

$block1->contentRow($strings["start_date"], "<input type='text' name='start_date' id='start_date' size='20' value='$start_date'><input type='button' value=' ... ' id=\"trigStartDate\">");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        $calendar_common_settings
    })
</script>
";
$block1->contentRow($strings["due_date"], "<input type='text' name='due_date' id='due_date' size='20' value='$due_date'><input type='button' value=' ... ' id=\"trigDueDate\">");
echo <<<JAVASCRIPT
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        {$calendar_common_settings}
    })
</script>
JAVASCRIPT;

if ($task_id != "") {
    $block1->contentRow($strings["complete_date"], "<input type='text' name='complete_date' id='complete_date' size='20' value='$complete_date'><input type='button' value=' ... ' id=\"trigCompleteDate\">");
    echo <<<JAVASCRIPT
	<script type='text/javascript'>
	    Calendar.setup({
	        inputField     :    'complete_date',
	        button         :    'trigCompleteDate',
        {$calendar_common_settings}
	    })
	</script>
JAVASCRIPT;
}

echo <<<TR
    <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["estimated_time"]} :</td>
            <td><input size="32" value="$estimated_time" style="width: 250px" name="estimated_time" maxlength="32" type="TEXT">{$strings["hours"]}</td>
        </tr>
TR;
echo <<<TR
        <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["actual_time"]} :</td>
            <td><input size="32" value="$actual_time" style="width: 250px" name="actual_time" maxlength="32" type="TEXT">{$strings["hours"]}</td>
        </tr>
TR;
echo <<<TR
        <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["comments"]} :</td>
            <td><textarea rows="10" style="width: 400px; height: 160px;" name="comments" cols="47">{$comments}</textarea></td>
        </tr>
TR;
echo <<<TR
        <tr class="odd">
            <td style="vertical-align:top" class="leftvalue">{$strings["published"]} :</td>
            <td><input size="32" value="0" name="published" type="checkbox" {$checkedPub}></td>
        </tr>
TR;

if ($enableInvoicing == "true") {
    if ($taskDetail["tas_invoicing"] == "1") {
        $checkedInvoicing = "checked";
    }
    $block1->contentRow($strings["invoicing"], '<input size="32" value="1" name="invoicing" type="checkbox" ' . $checkedInvoicing . '>');
    $block1->contentRow($strings["worked_hours"], '<input size="32" value="' . $worked_hours . '" style="width: 250px" name="worked_hours" type="TEXT">');
}

if ($task_id != "") {
    $block1->contentTitle($strings["updates_task"]);
    echo "  <tr class='odd'>
                <td style='vertical-align:top' class='leftvalue'>" . $strings["comments"] . " :</td>
                <td><textarea rows='10' style='width: 400px; height: 160px;' name='assignment_comment' cols='47'></textarea></td>
            </tr>";
}

echo <<<HTML
      <tr class="odd">
                <td style="vertical-align:top" class="leftvalue">&nbsp;</td>
                <td><button type="submit" name="action" value="{$submitValue}">{$strings["save"]}</button></td>
            </tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
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

    changeSt(document.etDForm.taskStatus, true);
</script>
