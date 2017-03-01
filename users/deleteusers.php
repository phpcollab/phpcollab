<?php

$checkSession = "true";
include_once '../includes/library.php';

//CVS library
include '../includes/cvslib.php';

$members = new \phpCollab\Members\Members();
$projects = new \phpCollab\Projects\Projects();
$tasks = new \phpCollab\Tasks\Tasks();

$strings = $GLOBALS["strings"];
$msgLabel = $GLOBALS["msgLabel"];

if ($_GET["action"] == "delete") {
    if ($at == "0") {
        $atProject = "1";
    } else {
        $atProject = $at;
    }

    $id = str_replace("**", ",", $id);
    $tmpquery1 = "DELETE FROM " . $tableCollab["members"] . " WHERE id IN($id)";
    $tmpquery2 = "UPDATE " . $tableCollab["projects"] . " SET owner='$atProject' WHERE owner IN($id)";
    $tmpquery3 = "UPDATE " . $tableCollab["tasks"] . " SET assigned_to='$at' WHERE assigned_to IN($id)";
    $tmpquery4 = "UPDATE " . $tableCollab["assignments"] . " SET assigned_to='$at',assigned='$dateheure' WHERE assigned_to IN($id)";
    $tmpquery5 = "DELETE FROM " . $tableCollab["sorting"] . " WHERE member IN($id)";
    $tmpquery6 = "DELETE FROM " . $tableCollab["notifications"] . " WHERE member IN($id)";
    $tmpquery7 = "DELETE FROM " . $tableCollab["teams"] . " WHERE member IN($id)";

    $tmpquery = "WHERE pro.owner IN($id)";
    $listProjects = new phpCollab\Request();
    $listProjects->openProjects($tmpquery);
    $comptListProjects = count($listProjects->pro_id);
    for ($i = 0; $i < $comptListProjects; $i++) {
        $listTeams->tea_id = "";
        $listTeams->tea_project = "";
        $listTeams->tea_member = "";
        $listTeams->tea_published = "";
        $listTeams->tea_authorized = "";
        $listTeams->tea_mem_login = "";
        $listTeams->tea_pro_id = "";

        $tmpquery = "WHERE tea.project = '" . $listProjects->pro_id[$i] . "' AND tea.member = '$atProject'";
        $listTeams = new phpCollab\Request();
        $listTeams->openTeams($tmpquery);
        $comptListTeams = count($listTeams->tea_id);
        if ($comptListTeams == "0") {
            $tmpquery = "INSERT INTO " . $tableCollab["teams"] . "(project,member,published,authorized) VALUES('" . $listProjects->pro_id[$i] . "','$atProject','1','0')";

            phpCollab\Util::connectSql("$tmpquery");
        }
    }

//if CVS repository enabled
    if ($enable_cvs == "true") {
        $pieces = explode(",", $id);
        for ($j = 0; $j < (count($pieces)); $j++) {

//remove the users from every repository
            $listTeams->tea_id = "";
            $listTeams->tea_project = "";
            $listTeams->tea_member = "";
            $listTeams->tea_published = "";
            $listTeams->tea_authorized = "";
            $listTeams->tea_mem_login = "";
            $listTeams->tea_pro_id = "";

            $tmpquery = "WHERE tea.member = '$pieces[$j]'";
            $listTeams = new phpCollab\Request();
            $listTeams->openTeams($tmpquery);
            $comptListTeams = count($listTeams->tea_id);
            for ($i = 0; $i < $comptListTeams; $i++) {
                cvs_delete_user($listTeams->tea_mem_login[$i], $listTeams->tea_pro_id[$i]);
            }
        }
    }
    phpCollab\Util::connectSql("$tmpquery1");
    phpCollab\Util::connectSql("$tmpquery2");
    phpCollab\Util::connectSql("$tmpquery3");
    phpCollab\Util::connectSql("$tmpquery4");
    phpCollab\Util::connectSql("$tmpquery5");
    phpCollab\Util::connectSql("$tmpquery6");
    phpCollab\Util::connectSql("$tmpquery7");
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

$id = str_replace("**", ",", $_GET["id"]);
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
    echo '<select name="at">';
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
