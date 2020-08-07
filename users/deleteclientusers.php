<?php

use phpCollab\Assignments\Assignments;
use phpCollab\Members\Members;
use phpCollab\Notifications\Notifications;
use phpCollab\Organizations\Organizations;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$org_id = $request->query->get('orgid');
$user_id = $request->query->get('id') || $request->request->get('id');

if (empty($user_id) || empty($org_id)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

$organizations = new Organizations();

$detailOrganization = $organizations->getOrganizationById($org_id);

$members = new Members();
$teams = new Teams();
$tasks = new Tasks();
$assignments = new Assignments();
$notifications = new Notifications();

if ($request->request->get('action') == "delete") {
    if ($request->request->get('id')) {
        $id = str_replace("**", ",", $request->request->get('id'));

        if (!empty( $request->request->get('assign_to')) ) {
            $tasks->setTasksAssignedToWhereAssignedToIn($request->request->get('assign_to'), $id);
            $assignments->reassignAssignmentByAssignedTo($request->request->get('assign_to'), $dateheure, $id);
        }

        $notifications->deleteNotificationsByMemberIdIn($id);
        $teams->deleteTeamWhereMemberIn($id);

        $members->deleteMemberByIdIn($id);

        //if mantis bug tracker enabled
        if ($enableMantis == "true") {
            // Call mantis function to remove user
            include("../mantis/user_delete.php");
        }

        phpCollab\Util::headerFunction("../clients/viewclient.php?id=$org_id&msg=delete");
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], 'in'));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $detailOrganization["org_id"], $detailOrganization["org_name"], 'in'));
$blockPage->itemBreadcrumbs($strings["delete_users"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "client_user_delete";
$block1->openForm("../users/deleteclientusers.php?orgid=$org_id");

$block1->heading($strings["delete_users"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listMembers = $members->getMembersByIdIn($id, "mem.name");

foreach ($listMembers as $listMember) {
    echo <<< HTML
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td>{$listMember["mem_login"]} ({$listMember["mem_name"]})</td>
</tr>
HTML;
}

$totalTasks = $tasks->getClientUserTasksCount($id);

/**
 * If there are tasks, then display the select member to re-assign to
 * If no tasks, then skip
 */
if ($totalTasks) {
    $block1->contentTitle($strings["reassignment_clientuser"]);
    echo <<<HTML
    <tr class="odd">
        <td valign="top" class="leftvalue">&nbsp;</td>
        <td>{$strings["there"]} $totalTasks {$strings["tasks"]} {$strings["owned_by"]}</td>
    </tr>
    <tr class="odd">
        <td valign="top" class="leftvalue">&nbsp;</td>
        <td><b>{$strings["reassign_to"]} : </b>
HTML;

    $reassign = $members->getNonClientMembersExcept($user_id);
    echo <<<HTML
    <select name="assign_to">
        <option value="0" selected>{$strings["unassigned"]}</option>
HTML;

foreach ($reassign as $item) {
    echo '<option value="' . $item["mem_id"] . '">' . $item["mem_login"] . ' / ' . $item["mem_name"] . '</option>';
}

echo <<<HTML
    </select></td>
</tr>
HTML;
}
echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td>
    <input type="submit" value="{$strings["delete"]}">
    <input type="button" name="cancel" value="{$strings["cancel"]}" onClick="history.back();">
    <input type="hidden" name="id" value="$id"></td>
    <input type="hidden" name="action" value="delete"></td>
</tr>
HTML;


$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
