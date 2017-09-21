<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/deleteclientusers.php

$checkSession = "true";
include_once '../includes/library.php';
$tmpquery = "WHERE org.id = '$organization'";
$detailOrganization = new phpCollab\Request();
$detailOrganization->openOrganizations($tmpquery);
$comptDetailOrganization = count($detailOrganization->org_id);

$members = new \phpCollab\Members\Members();
$teams = new \phpCollab\Teams\Teams();
$tasks = new \phpCollab\Tasks\Tasks();
$assignments = new \phpCollab\Assignments\Assignments();
$notifications = new \phpCollab\Notifications\Notifications();

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $members->deleteMemberByIdIn($id);
    $tasks->setTasksAssignedToWhereAssignedToIn($at, $id);
    $assignments->reassignAssignmentByAssignedTo($at, $dateheure, $id);
    $notifications->deleteNotificationsByMemberIdIn($id);
    $teams->deleteTeamWhereMemberIn($id);

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        // Call mantis function to remove user
        include("../mantis/user_delete.php");
    }

    phpCollab\Util::headerFunction("../clients/viewclient.php?id=$organization&msg=delete");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $detailOrganization->org_id[0], $detailOrganization->org_name[0], in));
$blockPage->itemBreadcrumbs($strings["delete_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "client_user_delete";
$block1->openForm("../users/deleteclientusers.php?organization=$organization&action=delete");

$block1->heading($strings["delete_users"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE mem.id IN($id) ORDER BY mem.name";

$listMembers = new phpCollab\Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

for ($i = 0; $i < $comptListMembers; $i++) {
    echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>" . $listMembers->mem_login[$i] . "&nbsp;(" . $listMembers->mem_name[$i] . ")</td></tr>";
}

$tmpquery = "SELECT tas.id FROM {$tableCollab["tasks"]} tas WHERE tas.assigned_to IN($id)";
phpCollab\Util::computeTotal($tmpquery);
$totalTasks = $countEnregTotal;

$block1->contentTitle($strings["reassignment_clientuser"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>" . $strings["there"] . " $totalTasks " . $strings["tasks"] . " " . $strings["owned_by"] . "</td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><b>" . $strings["reassign_to"] . " : </b> ";

$tmpquery = "WHERE mem.profil != '3' ORDER BY mem.name";
$reassign = new phpCollab\Request();
$reassign->openMembers($tmpquery);
$comptReassign = count($reassign->mem_id);

echo "<select name='at'>
<option value='0' selected>" . $strings["unassigned"] . "</option>";

for ($i = 0; $i < $comptReassign; $i++) {
    echo "<option value='" . $reassign->mem_id[$i] . "'>" . $reassign->mem_login[$i] . " / " . $reassign->mem_name[$i] . "</option>";
}

echo "</select></td></tr>

<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='submit' name='delete' value='" . $strings["delete"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'><input type='hidden' value='$id' name='id'></td></tr>";

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
