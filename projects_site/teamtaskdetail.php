<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Tasks\Tasks;
use phpCollab\Updates\Updates;

$checkSession = "true";
include '../includes/library.php';

$tasks = new Tasks();
$updates = new Updates();

$taskDetail = $tasks->getTaskById($id);

if ($taskDetail["tas_published"] == "1" || $taskDetail["tas_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[2] = "over";
$titlePage = $strings["team_task_details"];
include 'include_header.php';

$block1 = new phpCollab\Block();

$block1->heading($strings["team_task_details"]);

echo "<table class='nonStriped'>";
if ($taskDetail["tas_name"] != "") {
    echo "<tr><td>" . $strings["name"] . " :</td><td>" . $taskDetail["tas_name"] . "</td></tr>";
}
if ($taskDetail["tas_description"] != "") {
    $taskDescription = nl2br($taskDetail["tas_description"]);
    echo <<<TR
        <tr>
            <td style='vertical-align: top'>{$strings["description"]} :</td>
            <td>{$taskDescription}</td>
        </tr>
TR;
}
$complValue = ($taskDetail["tas_completion"] > 0) ? $taskDetail["tas_completion"] . "0 %" : $taskDetail["tas_completion"] . " %";
echo "<tr><td>" . $strings["completion"] . " :</td><td>" . $complValue . "</td></tr>";
if ($taskDetail["tas_assigned_to"] != "0") {
    echo "<tr><td>" . $strings["assigned_to"] . " :</td><td>" . $taskDetail["tas_mem_name"] . "</td></tr>";
}
if ($taskDetail["tas_comments"] != "") {
    echo "<tr><td>" . $strings["comments"] . " :</td><td>" . nl2br($taskDetail["tas_comments"]) . "</td></tr>";
}
if ($taskDetail["tas_start_date"] != "") {
    echo "<tr><td>" . $strings["start_date"] . " :</td><td>" . $taskDetail["tas_start_date"] . "</td></tr>";
}
if ($taskDetail["tas_due_date"] != "") {
    echo "<tr><td>" . $strings["due_date"] . " :</td><td>" . $taskDetail["tas_due_date"] . "</td></tr>";
}
echo "<tr><td style='vertical-align: top'>" . $strings["updates_task"] . " :</td><td>";

$listUpdates = $updates->getUpdates(1, $id, 'upd.created DESC');

if ($listUpdates) {
    $j = 1;
    foreach ($listUpdates as $update) {
        echo "<b>" . $j . ".</b> <i>" . phpCollab\Util::createDate($update["upd_created"], $timezoneSession) . "</i><br/>" . nl2br($update["upd_comments"]);
        echo "<br/>";
        $j++;
    }
} else {
    echo $strings["no_items"];
}

echo "</td></tr> </table> <hr>";

$listSubtasks = $tasks->getPublishedSubtasksByParentTaskId($id, 'subtas.name');

echo "<br/>";

$block2 = new phpCollab\Block();

$block2->heading($strings["subtasks"]);

if ($listSubtasks) {
    if ($activeJpgraph == "true") {
        echo <<<JPGRAPH
        <img src="graphsubtasks.php?task={$taskDetail["tas_id"]}" alt="">
        <span class="listEvenBold">[<a href="http://www.aditus.nu/jpgraph/" target="_blank">JpGraph</a>]</span><br/><br/>
JPGRAPH;
    }

    echo <<<SUBTASK_TABLE
<table style="width: 90%;" class="listing striped">
    <tr>
        <th class="active">{$strings["name"]}</th>
        <th>{$strings["description"]}</th>
        <th>{$strings["status"]}</th>
        <th>{$strings["due"]}</th>
    </tr>
SUBTASK_TABLE;
    foreach ($listSubtasks as $subtask) {
        $idStatus = $subtask["subtas_status"];
        $subtaskDescription = nl2br($subtask["subtas_description"]);
        echo <<<TR
            <tr>
                <td><a href=\"teamsubtaskdetail.php?task={$id}&id="{$subtask["subtas_id"]}">{$subtask["subtas_name"]}</a></td>
                <td>{$subtaskDescription}</td>
                <td>{$status[$idStatus]}</td>
                <td>{$subtask["subtas_due_date"]}</td>
            </tr>
TR;
    }
    echo "</table>";
} else {
    echo '<div class="no-records">' . $strings["no_items"] . '</div>';
}

echo <<<SHOW_ALL_LINK
<br/><br/>
<a href="showallteamtasks.php">{$strings["show_all"]}</a>
SHOW_ALL_LINK;

include("include_footer.php");
