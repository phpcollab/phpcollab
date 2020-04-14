<?php

$checkSession = "true";
include_once '../includes/library.php';

$members = new \phpCollab\Members\Members();
$projects = new \phpCollab\Projects\Projects();
$tasks = new \phpCollab\Tasks\Tasks();

$strings = $GLOBALS["strings"];
$msgLabel = $GLOBALS["msgLabel"];
$tableCollab = $GLOBALS["tableCollab"];

if ($request->query->get('action') == "delete") {
    $assignments = new \phpCollab\Assignments\Assignments();
    $sorting = new \phpCollab\Sorting\Sorting();
    $notifications = new \phpCollab\Notifications\Notifications();
    $teams = new \phpCollab\Teams\Teams();

    // Check for assigned to value
    $assignTo = isset($_POST["assign_to"]) ? $_POST["assign_to"] : 0;

    if ($assignTo == "0") {
        $atProject = "1";
    } else {
        $atProject = $assignTo;
    }

    $id = str_replace("**", ",", $_POST["id"]);

    $listProjects = $projects->getProjectsByOwner($id);

    foreach ($listProjects as $project) {
        $listTeams = $teams->getTeamByProjectIdAndTeamMember($project["pro_id"], $atProject);
        $comptListTeams = count($listTeams);
        if ($comptListTeams == "0") {
            phpCollab\Util::newConnectSql(
                "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES (:project, :member, 1, 0)",
                ["project" => $project[""],"member" => $atProject]
            );
        }
    }

    // Delete user from members table
    $members->deleteMemberByIdIn($id);

    // Reassign projects to new owner
    $projects->reassignProject($id, $atProject);

    // Reassign tasks to new owner
    $tasks->reassignTasks($id, $assignTo);

    // Reassign assignments to new owner
    $assignments->reassignAssignmentByAssignedTo($assignTo, $dateheure, $id);

    // Remove user form sorting table
    $sorting->deleteByMember($id);

    // Remove user notifications
    $notifications->deleteNotificationsByMemberIdIn($id);

    // Remove user from teams
    $teams->deleteTeamWhereMemberIn($id);
    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        // Call mantis function to remove user
        include("../mantis/user_delete.php");
    }

    phpCollab\Util::headerFunction("../users/listusers.php?msg=delete");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../users/listusers.php?", $strings["user_management"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "user_delete";
$block1->openForm("../users/deleteusers.php?action=delete");

$block1->heading($strings["delete_users"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $request->query->get('id'));
$listMembers = $members->getMembersByIdIn($id);

foreach ($listMembers as $member) {
    echo <<<ROW
    <tr class="odd">
        <td valign="top" class="leftvalue">&nbsp;</td>
        <td>{$member["mem_login"]}&nbsp;({$member["mem_name"]})</td>
    </tr>
ROW;
}

$totalProjects = count($projects->getProjectsByOwner($id));

$totalTasks = count($tasks->getTasksAssignedTo($id));

// Only show if there are projects or tasks assigned to the user(s)
if ($totalProjects || $totalTasks) {
    $block1->contentTitle($strings["reassignment_user"]);

    if ($totalProjects) {
        echo <<<OWNED_PROJECTS
    <tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td>{$strings["there"]} {$totalProjects} {$strings["projects"]} {$strings["owned_by"]}</td></tr>
OWNED_PROJECTS;
    }

    if ($totalTasks) {
        echo <<<OWNED_TASKS
    <tr class="odd">
        <td valign="top" class="leftvalue">&nbsp;</td>
        <td>{$strings["there"]} {$totalTasks} {$strings["tasks"]} {$strings["owned_by"]}</td>
    </tr>
OWNED_TASKS;
    }

    echo '<tr class="odd"><td valign="top" class="leftvalue">&nbsp;</td><td><b>' . $strings["reassign_to"] . ' : </b> ';
    $reassignMembersList = $members->getNonClientMembersExcept($id);
    echo '<select name="assign_to">';
    echo '<option value="0" selected>' . $strings["unassigned"] . '</option>';

    foreach ($reassignMembersList as $member) {
        echo '<option value="' . $member["mem_id"] . '">' . $member["mem_login"] . ' / ' . $member["mem_name"] . '</option>';
    }

    echo "</select></td></tr>";
}

echo <<<FORM_BUTTONS
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="submit" name="delete" value="{$strings["delete"]}"> <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();"><input type="hidden" value="{$id}" name="id"></td>
</tr>
FORM_BUTTONS;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
