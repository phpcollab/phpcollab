<?php
/*
** Application name: phpCollab
** Path by root: ../projects/editproject.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editproject.php
**
** DESC: Screen: Create or edit a project
**
*/

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';
include '../includes/customvalues.php';

$id = $request->query->get("id");
$docopy = $request->query->get("docopy");

try {
    $teams = $container->getTeams();
    $tasks = $container->getTasksLoader();
    $projects = $container->getProjectsLoader();
    $files = $container->getFilesLoader();
    $phases = $container->getPhasesLoader();
    $invoices = $container->getInvoicesLoader();
    $assignments = $container->getAssignmentsManager();
    $organizations = $container->getOrganizationsManager();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($htaccessAuth == "true") {
    $Htpasswd = $container->getHtpasswdService();
}

// Set default values for form fields
$projectName = null;
$description = null;
$url_dev = null;
$url_prod = null;
$hourly_rate = null;
$invoicing = null;


/**
 * case update or copy project
 */
if ($id != "") {
    /*
     * If the user is not an Admin, Project Manager, or Project Manager Administrator then redirect to project view
     */
    if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$id");
    }

    /*
     * See if the project exists, if it does not exist then redirect to project list
     */
    $projectDetail = $projects->getProjectById($id);


    if (empty($projectDetail)) {
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
    }

    if ($session->get("id") != $projectDetail["pro_owner"] && $session->get("profile") != "0" && $session->get("profile") != "5") {
        phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
    }

    /*
     * Set the page title
     */
    if ($docopy == "true") {
        $setTitle .= " : Copy Project (" . $projectDetail["pro_name"] . ")";
    } else {
        $setTitle .= " : Edit Project (" . $projectDetail["pro_name"] . ")";
    }

    /*
     * case update or copy project
     * See if the form has been submitted or not and key off the action field
     */
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->request->get("action") == "update") {

                    $published = filter_var($request->request->get('projectPublished'), FILTER_SANITIZE_NUMBER_INT);
                    $projectName = filter_var($request->request->get('name'), FILTER_SANITIZE_STRING);
                    $projectPriority = filter_var($request->request->get('priority'), FILTER_SANITIZE_NUMBER_INT);
                    $description = htmlspecialchars($request->request->get('description'), ENT_QUOTES);
                    $urlDev = (empty($request->request->get('url_dev'))) ? null : filter_var($request->request->get('url_dev'), FILTER_SANITIZE_SPECIAL_CHARS);
                    $urlProd = (empty($request->request->get('url_prod'))) ? null : filter_var($request->request->get('url_prod'), FILTER_SANITIZE_SPECIAL_CHARS);
                    $owner = filter_var($request->request->get('owner'), FILTER_SANITIZE_NUMBER_INT);
                    $organization = filter_var($request->request->get('client_organization'), FILTER_SANITIZE_NUMBER_INT);
                    $thisPhase = filter_var($request->request->get('thisPhase'), FILTER_SANITIZE_NUMBER_INT);
                    $projectStatus = filter_var($request->request->get('status'), FILTER_SANITIZE_NUMBER_INT);
                    $maxUploadSize = filter_var($request->request->get('max_upload_size'), FILTER_SANITIZE_NUMBER_INT);
                    $invoicing = filter_var($request->request->get('invoicing'), FILTER_SANITIZE_NUMBER_INT);
                    $hourlyRate = filter_var($request->request->get('hourly_rate'), FILTER_SANITIZE_NUMBER_FLOAT,
                        FILTER_FLAG_ALLOW_FRACTION);

                    //case copy project
                    if ($docopy == "true") {
                        if ($invoicing == "" || $organization == "1") {
                            $invoicing = "0";
                        }

                        if ($hourlyRate == "") {
                            $hourlyRate = "0.00";
                        }

                        try {

                            //insert into projects and teams (with last id project)
                            $newProjectId = $projects->createProject($projectName, $organization, $owner,
                                $projectPriority, $projectStatus, $description, $published, $thisPhase, $maxUploadSize,
                                $urlDev, $urlProd, $invoicing, $hourlyRate);

                            $newTeamId = $teams->addTeam($newProjectId, $session->get("id"), 1, 0);

                            if ($enableInvoicing == "true") {
                                $newInvoiceId = $invoices->addInvoice($newProjectId, 0, $invoicing, 1);
                            }

                            //create project folder if filemanagement = true
                            if ($fileManagement == "true") {
                                phpCollab\Util::createDirectory("files/$newProjectId");
                            }

                            if ($htaccessAuth == "true") {
                                $content = <<<STAMP
AuthName "$setTitle"
AuthType Basic
Require valid-user
AuthUserFile $fullPath/files/$newProjectId/.htpasswd
STAMP;
                                $fp = @fopen("../files/$newProjectId/.htaccess", 'wb+');
                                $fw = fwrite($fp, $content);
                                $fp = @fopen("../files/$newProjectId/.htpasswd", 'wb+');

                                $detailMember = $members->getMemberById($owner);

                                $Htpasswd = $container->getHtpasswdService();
                                $Htpasswd->initialize("../files/" . $newProjectId . "/.htpasswd");
                                $Htpasswd->addUser($detailMember["mem_login"], $detailMember["mem_password"]);
                            }

                            $listTasks = $tasks->getTasksByProjectId($id);

                            foreach ($listTasks as $task) {
                                $assigned = "";
                                $taskAssignedTo = "";
                                $taskName = phpCollab\Util::convertData($task["tas_name"]);
                                $taskDescription = phpCollab\Util::convertData($task["tas_description"]);
                                $taskOwner = $task["tas_owner"];
                                $taskAssignedTo = $task["tas_assigned_to"];
                                $taskStatus = $task["tas_status"];
                                $taskPriority = $task["tas_priority"];
                                $taskStartDate = $task["tas_start_date"];
                                $taskDueDate = $task["tas_due_date"];
                                $taskCompleteDate = $task["tas_complete_date"];
                                $taskEstimatedTime = $task["tas_estimated_time"];
                                $taskActualTime = $task["tas_actual_time"];
                                $taskComments = phpCollab\Util::convertData($task["tas_comments"]);
                                $taskParentPhase = $task["tas_parent_phase"];
                                $taskPublished = $task["tas_published"];
                                $taskCompleted = $task["tas_completion"];

                                if ($taskAssignedTo != "0") {
                                    $assigned = $dateheure;
                                }

                                $newTask = $tasks->addTask($newProjectId, $taskName, $taskDescription, $taskOwner,
                                    $taskAssignedTo, $taskStatus, $taskPriority, $taskStartDate, $taskDueDate,
                                    $taskEstimatedTime,
                                    $taskActualTime, $taskComments, $taskPublished, $taskCompleted, $taskParentPhase);

                                $newTaskId = $newTask["tas_id"];

                                $newAssignmentId = $assignments->addAssignment($newTaskId, $taskOwner, $taskAssignedTo,
                                    date('Y-m-d h:i'));

                                //start the subtask copy
                                $subtaskDetail = $tasks->getSubTaskById($task["tas_id"]);

                                if ($subtaskDetail) {

                                    foreach ($subtaskDetail as $subtask) {
                                        $s_tn = phpCollab\Util::convertData($subtask["subtas_name"]);
                                        $s_d = phpCollab\Util::convertData($subtask["subtas_description"]);
                                        $s_ow = $subtask["subtas_owner"];
                                        $s_at = $subtask["subtas_assigned_to"];
                                        $s_st = $subtask["subtas_status"];
                                        $s_pr = $subtask["subtas_priority"];
                                        $s_sd = $subtask["subtas_start_date"];
                                        $s_dd = $subtask["subtas_due_date"];
                                        $s_cd = $subtask["subtas_complete_date"];
                                        $s_etm = $subtask["subtas_estimated_time"];
                                        $s_atm = $subtask["subtas_actual_time"];
                                        $s_c = phpCollab\Util::convertData($subtask["subtas_comments"]);
                                        $s_published = $subtask["subtas_published"];
                                        $s_compl = $subtask["subtas_completion"];

                                        $newSubtaskId = $tasks->addSubTask($newTaskId, $s_tn, $s_d, $s_ow, $s_at, $s_st,
                                            $s_pr, $s_sd, $s_dd, $s_cd, $s_etm, $s_atm, $s_c, $s_published, $s_compl);

                                        $newSubtaskAssignmentId = $assignments->addAssignment($newSubtaskId, $s_ow,
                                            $s_at, $dateheure);
                                    }
                                }


                                if ($taskAssignedTo != "0") {
                                    $isTeamMember = $teams->isTeamMember($newProjectId, $taskAssignedTo);

                                    if ($isTeamMember) {
                                        $newTeamId = $teams->addTeam($newProjectId, $taskAssignedTo, 1, 0);

                                        if ($htaccessAuth == "true") {
                                            $detailMember = $members->getMemberById($taskAssignedTo);

                                            $Htpasswd->initialize("../files/" . $newProjectId . "/.htpasswd");
                                            $Htpasswd->addUser($detailMember["mem_login"],
                                                $detailMember["mem_password"]);
                                        }
                                    }
                                }

                                //create task sub-folder if filemanagement = true
                                if ($fileManagement == "true") {
                                    phpCollab\Util::createDirectory("files/$newProjectId/$newTaskId");
                                }
                            }

                            //if mantis bug tracker enabled
                            if ($enableMantis == "true") {
                                // call mantis function to copy project
                                /** @noinspection PhpIncludeInspection */
                                include $pathMantis . 'proj_add.php';
                            }

                            //create phase structure if enable phase was selected as true
                            if ($thisPhase != "0") {
                                $comptThisPhase = count($phaseArraySets[$thisPhase]);

                                for ($i = 0; $i < $comptThisPhase; $i++) {
                                    $newPhaseId = $phases->addPhase($newProjectId, $i, 0,
                                        $phaseArraySets[$thisPhase][$i]);
                                }
                            }

                            phpCollab\Util::headerFunction("../projects/viewproject.php?id=$newProjectId&msg=add");
                        } catch (Exception $e) {
                            $logger->error('Projects (edit)', ['Exception message', $e->getMessage()]);
                            $error = $strings["action_not_allowed"];

                        }

                    } else {

                        //if project owner change, add new to team members (only if doesn't already exist)
                        if ($owner != $projectDetail["pro_owner"]) {
                            $isTeamMember = $teams->isTeamMember($id, $owner);

                            if ($isTeamMember) {
                                $newTeamId = $teams->addTeam($id, $owner, 1, 0);

                                if ($htaccessAuth == "true") {
                                    $detailMember = $members->getMemberById($owner);

                                    try {
                                        $Htpasswd->initialize("../files/" . $id . "/.htpasswd");
                                        $Htpasswd->addUser($detailMember["mem_login"], $detailMember["mem_password"]);
                                    } catch (Exception $e) {
                                        $logger->error('Projects (htaccessAuth add user)',
                                            ['Exception message', $e->getMessage()]);
                                        $error = $strings["action_not_allowed"];
                                    }
                                }
                            }
                        }

                        //if organization change, delete old organization permitted users from teams
                        if ($organization != $projectDetail["pro_organization"]) {
                            $suppTeamClient = $teams->getClientTeamMembersByProject($id);
                            if ($suppTeamClient) {
                                $clientTeam = [];
                                foreach ($suppTeamClient as $clientUser) {
                                    array_push($clientTeam, $clientUser["tea_mem_id"]);

                                    if ($htaccessAuth == "true") {
                                        try {
                                            $Htpasswd->initialize("../files/" . $id . "/.htpasswd");
                                            $Htpasswd->deleteUser($clientUser["mem_login"]);
                                        } catch (Exception $e) {
                                            $logger->error('Projects (htpasswd)',
                                                ['Exception message', $e->getMessage()]);
                                            $error = $strings["action_not_allowed"];
                                        }
                                    }
                                }
                                $teams->deleteFromTeamsByProjectIdAndMemberId($id, implode(', ', $clientTeam));
                            }
                        }

                        //-------------------------------------------------------------------------------------------------
                        $targetProject = $projects->getProjectById($id);

                        //Delete old or unused phases
                        if ($targetProject["pro_phase_set"] != $thisPhase) {
                            $phases->deletePhasesByProjectId($id);
                        }

                        //Create new Phases
                        if ($targetProject["pro_phase_set"] != $thisPhase) {
                            $comptThisPhase = count($phaseArraySets[$thisPhase]);

                            for ($i = 0; $i < $comptThisPhase; $i++) {
                                $phases->addPhase($id, $i, 0, $phaseArraySets[$thisPhase][$i]);
                            }

                            //Get a listing of project tasks and files and re-assign them to new phases if the phase set of a project is changed.
                            $listTasks = $tasks->getTasksByProjectId($targetProject["pro_id"]);
                            $listFiles = $files->getFilesByProjectIdAndPhaseNotEqualZero($targetProject["pro_id"]);
                            $targetPhase = $phases->getPhasesByProjectIdAndPhaseOrderNum($targetProject["pro_id"], 0);

                            foreach ($listTasks as $task) {
                                $tasks->setParentPhase($task["tas_id"], 0);
                            }

                            foreach ($listFiles as $file) {
                                $files->setPhase($file["fil_id"], $targetPhase["pha_id"]);
                            }
                        }

                        //update project
                        if ($invoicing == "" || $organization == "1") {
                            //nb if the project has not client than the invoicing will be deactivated
                            $invoicing = "0";
                        }

                        if ($hourlyRate == "") {
                            $hourlyRate = "0.00";
                        }

                        $projects->updateProject($id, $projectName, $organization, $owner, $projectPriority,
                            $projectStatus,
                            $description,
                            $published,
                            $thisPhase, $maxUploadSize, $urlDev, $urlProd, $invoicing, $hourlyRate, $dateheure);


                        if ($enableInvoicing == "true") {
                            $invoices->setActive($id, $invoicing);
                        }

                        //if mantis bug tracker enabled
                        if ($enableMantis == "true") {
                            // call mantis function to copy project
                            include '../mantis/proj_update.php';
                        }
                        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$id&msg=update");
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Projects: Edit project',
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }


    //set value in form
    $projectName = $projectDetail["pro_name"];
    $description = $projectDetail["pro_description"];
    $url_dev = $projectDetail["pro_url_dev"];
    $url_prod = $projectDetail["pro_url_prod"];
    $hourly_rate = $projectDetail["pro_hourly_rate"];
    $invoicing = $projectDetail["pro_invoicing"];
}

//case add project
if ($id == "") {
    $setTitle .= " : Add Project";

    if ($session->get("profile") != "0"
        && $session->get("profile") != "1"
        && $session->get("profile") != "5"
    ) {
        phpCollab\Util::headerFunction("../projects/listprojects.php");
    }

    //set organization if add project action done from clientdetail
    if ($organization != "") {
        $projectDetail["pro_org_id"] = "$organization";
    }

    //set default values
    $projectDetail["pro_mem_id"] = $session->get("id");
    $projectDetail["pro_priority"] = "3";

    $projectDetail["pro_status"] = "2";
    $projectDetail["pro_upload_max"] = $maxFileSize;

    //case add project
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->request->get("action") == "add") {
                    try {
                        $published = filter_var($request->request->get("projectPublished"), FILTER_SANITIZE_NUMBER_INT);
                        $projectName = filter_var($request->request->get("name"), FILTER_SANITIZE_STRING);
                        $projectPriority = filter_var($request->request->get("priority"), FILTER_SANITIZE_NUMBER_INT);
                        $description = htmlspecialchars($request->request->get('description'), ENT_QUOTES);
                        $urlDev = (empty($request->request->get('url_dev'))) ? null : filter_var($request->request->get('url_dev'), FILTER_SANITIZE_SPECIAL_CHARS);
                        $urlProd = (empty($request->request->get('url_prod'))) ? null : filter_var($request->request->get('url_prod'), FILTER_SANITIZE_SPECIAL_CHARS);
                        $owner = filter_var($request->request->get("owner"), FILTER_SANITIZE_NUMBER_INT);
                        $organization = filter_var($request->request->get("client_organization"), FILTER_SANITIZE_NUMBER_INT);
                        $thisPhase = filter_var($request->request->get("thisPhase"), FILTER_SANITIZE_NUMBER_INT);
                        $projectStatus = filter_var($request->request->get("status"), FILTER_SANITIZE_NUMBER_INT);
                        $maxUploadSize = filter_var($request->request->get("max_upload_size"), FILTER_SANITIZE_NUMBER_INT);
                        $invoicing = filter_var($request->request->get("invoicing"), FILTER_SANITIZE_NUMBER_INT);
                        $hourlyRate = filter_var($request->request->get("hourly_rate"), FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);

                        if ($invoicing == "" || $organization == "1") {
                            $invoicing = "0";
                        }

                        if (empty($hourlyRate)) {
                            $hourlyRate = 0.00;
                        }

                        //insert into projects and teams (with last id project)
                        $newProjectId = $projects->createProject($projectName, $organization, $owner, $projectPriority,
                            $projectStatus, $description, 1, $thisPhase, $maxUploadSize, $urlDev, $urlProd, $invoicing,
                            $hourlyRate);

                        if ($enableInvoicing == "true") {
                            $newInvoiceId = $invoices->addInvoice($newProjectId, 0, $invoicing, 1);
                        }

                        $newTeamId = $teams->addTeam($newProjectId, $owner, 1, 0);

                        //create project folder if filemanagement = true
                        if ($fileManagement == "true") {
                            phpCollab\Util::createDirectory("files/$newProjectId");
                        }

                        if ($htaccessAuth == "true") {
                            $content = <<<STAMP
    AuthName "$setTitle"
    AuthType Basic
    Require valid-user
    AuthUserFile $fullPath/files/$newProjectId/.htpasswd
STAMP;

                            $fp = @fopen("../files/$newProjectId/.htaccess", 'wb+');
                            $fw = fwrite($fp, $content);
                            $fp = @fopen("../files/$num/.htpasswd", 'wb+');

                            $detailMember = $members->getMemberById($owner);

                            $Htpasswd = $container->getHtpasswdService();
                            $Htpasswd->initialize("../files/" . $newProjectId . "/.htpasswd");
                            $Htpasswd->addUser($detailMember["mem_login"], $detailMember["mem_password"]);
                        }

                        //if mantis bug tracker enabled
                        if ($enableMantis == "true") {
                            // call mantis function to copy project
                            include '../mantis/proj_add.php';
                        }

                        //create phase structure if enable phase was selected as true
                        if ($thisPhase != "0") {
                            $comptThisPhase = count($phaseArraySets[$thisPhase]);

                            for ($i = 0; $i < $comptThisPhase; $i++) {
                                $phases->addPhase($newProjectId, $i, 0, $phaseArraySets[$thisPhase][$i]);
                            }
                        }

                        phpCollab\Util::headerFunction("../projects/viewproject.php?id=$newProjectId&msg=add");
                    } catch (Exception $e) {
                        $logger->error('Projects (edit)', ['Exception message', $e->getMessage()]);
                        $error = $strings["action_not_allowed"];
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Projects: Add project',
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }
}

$bodyCommand = "onLoad='document.epDForm.name.focus();'";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));

//case add project

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_project"]);
}

//case update or copy project
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
        $projectDetail["pro_name"], "in"));

    if ($docopy == "true") {
        $blockPage->itemBreadcrumbs($strings["copy_project"]);
    } else {
        $blockPage->itemBreadcrumbs($strings["edit_project"]);
    }
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

//case add project
if ($id == "") {
    $block1->form = "epD";
    $submitValue = "add";
    $block1->openForm("../projects/editproject.php?#" . $block1->form . "Anchor", null, $csrfHandler);
}

//case update or copy project
if ($id != "") {
    $block1->form = "epD";
    $submitValue = "update";
    $block1->openForm('../projects/editproject.php?id=' . $id . '&docopy=' . $docopy . "&#" . $block1->form . "Anchor",
        null, $csrfHandler);
    echo "<input type='hidden' value='" . $projectDetail["pro_published"] . "' name='projectPublished'>";
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

//case add project
if ($id == "") {
    $block1->heading($strings["add_project"]);
}

//case update or copy project
if ($id != "") {
    if ($docopy == "true") {
        $block1->heading($strings["copy_project"] . " : " . $projectDetail["pro_name"]);
    } else {
        $block1->heading($strings["edit_project"] . " : " . $projectDetail["pro_name"]);
    }
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$projectName = ($docopy == "true") ? $strings["copy_of"] . $projectName : $projectName;

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["name"]} :</td>
    <td><input size="44" value="$projectName" style="width: 400px" name="name" maxlength="100" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["priority"]} :</td>
    <td><select name="priority">";
HTML;


$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    if ($projectDetail["pro_priority"] == $i) {
        echo "<option value='$i' selected>$priority[$i]</option>";
    } else {
        echo "<option value='$i'>$priority[$i]</option>";
    }
}

echo <<<HTML
    </select></td>
</tr>
<tr class='odd'>
    <td class="leftvalue">{$strings["description"]} :</td>
    <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">$description</textarea></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["url_dev"]} :</td>
    <td><input size="44" value="$url_dev" style="width: 400px" name="url_dev" maxlength="100" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["url_prod"]} :</td>
    <td><input size="44" value="$url_prod" style="width: 400px" name="url_prod" maxlength="100" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["owner"]} :</td>
    <td><select name="owner">
HTML;

if ($demoMode == "true") {
    $assignOwner = $members->getMembersByProfileIn('0,1,5', null, 'mem.name');
} else {
    $assignOwner = $members->getMembersByProfileIn('0,1,5', 2, 'mem.name');
}

foreach ($assignOwner as $option) {
    if ($projectDetail["pro_mem_id"] == $option["mem_id"]) {
        echo "<option value='" . $option["mem_id"] . "' selected>" . $option["mem_login"] . " / " . $option["mem_name"] . "</option>";
    } else {
        echo "<option value='" . $option["mem_id"] . "'>" . $option["mem_login"] . " / " . $option["mem_name"] . "</option>";
    }
}

echo <<<HTML
        </select></td>
</tr>
<tr class="odd"><td class="leftvalue">{$strings["organization"]} :</td>
<td><select name="client_organization">
HTML;

if ($clientsFilter == "true" && $session->get("profile") == "1") {
    $listClients = $organizations->getOrganizationsByOwner($session->get("id"), 'org.name');
} else {
    $listClients = $organizations->getAllOrganizations('org.name');
}

if ($projectDetail["pro_org_id"] == "1") {
    echo "<option value='1' selected>" . $strings["none"] . "</option>";
} else {
    echo "<option value='1'>" . $strings["none"] . "</option>";
}

if ($listClients) {
    foreach ($listClients as $client) {
        if ($projectDetail["pro_org_id"] == $client["org_id"]) {
            echo "<option value='" . $client["org_id"] . "' selected>" . $client["org_name"] . "</option>";
        } else {
            echo "<option value='" . $client["org_id"] . "'>" . $client["org_name"] . "</option>";
        }

    }
}

echo <<<HTML
    </select></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["enable_phases"]} :</td>
    <td><select name="thisPhase">
HTML;

$compSets = count($phaseArraySets["sets"]);

if ($projectDetail["pro_phase_set"] == "0") {
    echo "<option value='0' selected>" . $strings["none"] . "</option>";
} else {
    echo "<option value='0'>" . $strings["none"] . "</option>";
}

for ($i = 1; $i <= $compSets; $i++) {
    if ($projectDetail["pro_phase_set"] == "$i") {
        echo "<option value='$i' selected>" . $phaseArraySets["sets"][$i] . "</option>";
    } else {
        echo "<option value='$i'>" . $phaseArraySets["sets"][$i] . "</option>";
    }
}


echo <<<HTML
    </select></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["status"]} :</td>
    <td><select name="status">
HTML;

$comptSta = count($status);

for ($i = 0; $i < $comptSta; $i++) {
    if ($projectDetail["pro_status"] == $i) {
        echo "<option value='$i' selected>$status[$i]</option>";
    } else {
        echo "<option value='$i'>$status[$i]</option>";
    }
}

echo <<<CLOSE_SELECT
    </select></td>
</tr>
CLOSE_SELECT;

if ($fileManagement == "true") {
    echo <<<TR
<tr class="odd">
    <td class="leftvalue">{$strings["max_upload"]} :</td>
    <td><input size="20" value="{$projectDetail["pro_upload_max"]}" style="width: 150px" name="max_upload_size" maxlength="100" type="TEXT"> $byteUnits[0]</td>
</tr>
TR;
}

if ($enableInvoicing == "true") {
    if ($projectDetail["pro_invoicing"] == "1") {
        $checkedInvoicing = "checked";
    }
    $block1->contentRow($strings["invoicing"],
        '<input size="32" value="1" name="invoicing" type="checkbox" ' . $checkedInvoicing . '>');
    $block1->contentRow($strings["hourly_rate"],
        '<input size="25" value="' . $hourly_rate . '" style="width: 200px" name="hourly_rate" maxlength="50" type="TEXT">');
}

echo <<<TR
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><button type="submit" name="action" value="$submitValue">{$strings["save"]}</button></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
