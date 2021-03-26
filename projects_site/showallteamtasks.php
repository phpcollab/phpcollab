<?php
/*
** Application name: phpCollab
** Last Edit page: 18/05/2005
** Path by root: ../project_site/showllteamtasks.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: showallteamtasks.php
**
** DESC: Screen: library file
**
** HISTORY:
**	18/05/2005	-	show all the team task images
**	26/08/2005	-	[1273927] fix jpgraph wrong link
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

$projectSite = "true";

$checkSession = "true";
require_once '../includes/library.php';

$tasks = $container->getTasksLoader();

$bouton[2] = "over";
$titlePage = $strings["team_tasks"];
include 'include_header.php';


$listTasks = $tasks->getTeamTasks($session->get("project"));

$block1 = new phpCollab\Block();

$block1->heading($strings["team_tasks"]);

if (!empty($listTasks)) {
    echo <<<TABLE
        <table class="listing striped">
			<tr>
				<th class="active">{$strings["name"]}</th>
				<th>{$strings["description"]}</th>
				<th>{$strings["status"]}</th>
				<th>{$strings["due"]}</th>
			</tr>
            <tbody>
TABLE;


    foreach ($listTasks as $task) {
        if ($task["tas_due_date"] == "") {
            $task["tas_due_date"] = $strings["none"];
        }

        $idStatus = $task["tas_status"];

        $description = nl2br($task["tas_description"]);
        echo <<<TR
            <tr>
                <td><a href="teamtaskdetail.php?id={$task["tas_id"]}">{$task["tas_name"]}</a></td>
                <td>{$description}</td>
                <td>{$status[$idStatus]}</td><td>{$task["tas_due_date"]}</td>
            </tr>
TR;


    }

    echo "</tbody></table><hr />";
} else {
    echo <<<TABLE
        <table>
			<tr>
				<td colspan="4">{$strings["no_items"]}</td>
			</tr>
			</table><hr />
TABLE;

}

echo <<<LINK
<br/><br/><a href="addteamtask.php" class="FooterCell">{$strings["add_task"]}</a>
LINK;


include("include_footer.php");
