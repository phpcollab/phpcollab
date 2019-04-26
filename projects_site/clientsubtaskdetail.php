<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Tasks\Tasks;
use phpCollab\Updates\Updates;
use phpCollab\Util;

$checkSession = "true";
include '../includes/library.php';

$tasks = new Tasks();
$updates = new Updates();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == "update") {
        $comments = phpCollab\Util::convertData($_POST["comments"]);

        if (!empty($_POST["checkbox"]) && $_POST["checkbox"] == "completed") {
            phpCollab\Util::newConnectSql(
                "UPDATE {$tableCollab["subtasks"]} SET comments = :comments, status = :status, modified = :modified, complete_date = :complete_date WHERE id = :subtask_id",
                ["comments" => $comments, "status" => 0, "modified" => $dateheure, "complete_date" => $dateheure, "subtask_id" => $id]
            );
        } else {
            phpCollab\Util::newConnectSql(
                "UPDATE {$tableCollab["subtasks"]} SET comments = :comments, status = :status, modified = :modified, complete_date = :complete_date  WHERE id = :subtask_id",
                ["comments" => $comments, "status" => 3, "modified" => $dateheure, "complete_date" => null, "subtask_id" => $id]
            );
        }
        phpCollab\Util::headerFunction("clienttaskdetail.php?id=$task");
    }
}

$subtaskDetail = $tasks->getSubTaskById($id);

$taskDetail = $tasks->getTaskById($task);

if ($subtaskDetail["subtas_published"] == "1" || $taskDetail["tas_project"] != $projectSession) {
    Util::headerFunction("index.php");
}

$bouton[3] = "over";
$titlePage = $strings["client_subtask_details"];
include 'include_header.php';

echo <<<START_PAGE
<h1 class="heading">{$strings["client_subtask_details"]}</h1>
<table class="nonStriped">
START_PAGE;

if ($taskDetail->tas_name[0] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["task"]} :</td>
        <td><a href="clienttaskdetail.php?id={$taskDetail["tas_id"]}">{$taskDetail["tas_name"]}</a></td>
    </tr>
TR;
}

if ($subtaskDetail["subtas_name"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["name"]} :</td>
        <td>{$subtaskDetail["subtas_name"]}</td>
    </tr>
TR;
}

if ($subtaskDetail->subtas_description[0] != "") {
    $subtaskDescription = nl2br($subtaskDetail["subtas_description"]);
    echo <<<TR
    <tr>
        <td class="leftvalue">{$strings["description"]} :</td>
        <td>{$subtaskDescription}</td>
    </tr>
TR;
}

$complValue = ($subtaskDetail["subtas_completion"] > 0) ?
    $subtaskDetail["subtas_completion"] . "0 %"
    : $subtaskDetail["subtas_completion"] . " %";

    echo <<<TR
    <tr>
        <td>{$strings["completion"]} :</td>
        <td>{$complValue}</td>
    </tr>
TR;

if ($subtaskDetail["subtas_assigned_to"] != "0") {
    echo <<<TR
    <tr>
        <td>{$strings["assigned_to"]} :</td>
        <td>{$subtaskDetail["subtas_mem_name"]}</td>
    </tr>
TR;
}

if ($subtaskDetail["subtas_comments"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["comments"]} :</td>
        <td>{$subtaskDetail["subtas_comments"]}</td>
    </tr>
TR;
}

if ($subtaskDetail["subtas_start_date"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["start_date"]} :</td>
        <td>{$subtaskDetail["subtas_start_date"]}</td>
    </tr>
TR;
}

if ($subtaskDetail["subtas_due_date"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["due_date"]} :</td>
        <td>{$subtaskDetail["subtas_due_date"]}</td>
    </tr>
TR;
}

echo "<tr>
        <td>{$strings["updates_subtask"]} :</td>
        <td>";

$listUpdates = $updates->getUpdates(2, $id, 'upd.created DESC');

if ($listUpdates) {
    $updateCount = 1;
    foreach ($listUpdates as $update) {
        $updateCreatedDate = phpCollab\Util::createDate($listUpdates->upd_created[$i], $timezoneSession);
        $updateComments = nl2br($listUpdates->upd_comments[$i]);
        echo <<<UPDATE
            <strong>{$updateCount}</strong> <em>{$updateCreatedDate}</em><br/>{$updateComments}<br/>
UPDATE;
        $updateCount++;
    }
} else {
    echo $strings["no_items"];
}

echo "</td></tr>
</table>";


$isChecked = ($subtaskDetail["subtas_status"] == "0") ? 'checked' : '';
echo <<<COMPLETE_TASK_FORM
<div id="completeTask" style="margin-top: 2em;">
    <h1 class="heading">Complete Subtask</h1>
    <form method="post" action="../projects_site/clientsubtaskdetail.php" name="clientTaskUpdate" style="">
        <input name="id" type="hidden" value="{$id}">
        <input name="task" type="hidden" value="{$task}">
        <input name="action" type="hidden" value="update">
    
        <table class="nonStriped" style="margin-top: 0">
            <tr>
                <th colspan="2">{$strings["client_change_status_subtask"]}</th>
            </tr>
            <tr>
                <td>{$strings["status"]} :</td>
                <td><input {$isChecked} value="completed" name="checkbox" type="checkbox" id="completedCheckbox"> <label for="completedCheckbox">{$status[0]}</label></td>
            </tr>
            <tr>
                <td class="leftvalue">{$strings["comments"]} :</td>
                <td><textarea cols="40" name="comments" rows="5">{$subtaskDetail["subtas_comments"]}</textarea></td>
            </tr>
            <tr>
                <td>&#160;</td>
                <td><input name="submit" type="submit" value="{$strings["save"]}"></td>
            </tr>
        </table>
    </form>
</div>
COMPLETE_TASK_FORM;

include("include_footer.php");
