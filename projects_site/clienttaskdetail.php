<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Tasks\Tasks;
use phpCollab\Updates\Updates;

$checkSession = "true";
include '../includes/library.php';

$tasks = new Tasks();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "update") {
        $comments = phpCollab\Util::convertData($_POST["comments"]);

        if (!empty($_POST["checkbox"])) {
            phpCollab\Util::newConnectSql(
                "UPDATE {$tableCollab["tasks"]} SET comments = :comments, status = :status, modified = :modified WHERE id = :task_id",
                ["comments" => $comments, "status" => 0, "modified" => $dateheure, "task_id" => $id]
            );
        } else {
            phpCollab\Util::newConnectSql(
                "UPDATE {$tableCollab["tasks"]} SET comments = :comments, status = :status, modified = :modified WHERE id = :task_id",
                ["comments" => $comments, "status" => 3, "modified" => $dateheure, "task_id" => $id]
            );
        }
        phpCollab\Util::headerFunction("showallclienttasks.php");
    }
}

$taskDetail = $tasks->getTaskById($id);
$updates = new Updates();

if ($taskDetail["tas_published"] == "1" || $taskDetail["tas_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[3] = "over";
$titlePage = $strings["client_task_details"];
include 'include_header.php';

$block1 = new phpCollab\Block();

$block1->heading($strings["client_task_details"]);

echo '<table style="margin-bottom: 2em;" class="nonStriped">';

if ($taskDetail["tas_name"] != "") {
    echo <<<TR
        <tr>
            <td>{$strings["name"]} :</td>
            <td>{$taskDetail["tas_name"]}</td>
        </tr>
TR;
}
if ($taskDetail["tas_description"] != "") {
    $taskDescription = nl2br($taskDetail["tas_description"]);
    echo <<<TR
        <tr>
            <td>{$strings["description"]} :</td>
            <td>$taskDescription</td></tr>
TR;
}

$complValue = ($taskDetail["tas_completion"] > 0) ? $taskDetail["tas_completion"] . "0 %" : $taskDetail["tas_completion"] . " %";

echo <<<TR
        <tr>
            <td>{$strings["completion"]} :</td>
            <td>{$complValue}</td>
        </tr>
TR;

if ($taskDetail["tas_mem_name"] != "") {
    echo <<<TR
        <tr>
            <td>{$strings["assigned_to"]} :</td>
            <td>{$taskDetail["tas_mem_name"]}</td>
        </tr>
TR;
}

if ($taskDetail["tas_comments"] != "") {
    $taskComments = nl2br($taskDetail["tas_comments"]);
    echo <<<TR
        <tr>
            <td>{$strings["comments"]} :</td>
            <td>{$taskComments}</td>
        </tr>
TR;
}

if ($taskDetail["tas_start_date"] != "") {
    echo <<<TR
        <tr>
            <td>{$strings["start_date"]} :</td>
            <td>{$taskDetail["tas_start_date"]}</td>
        </tr>
TR;
}

if ($taskDetail["tas_due_date"] != "") {
    echo <<<TR
        <tr>
            <td>{$strings["due_date"]} :</td>
            <td>{$taskDetail["tas_due_date"]}</td>
        </tr>
TR;
}

echo <<<TR
        <tr>
            <td>{$strings["updates_task"]} :</td>
            <td>
TR;

$listUpdates = $updates->getUpdates(1, $id, 'upd.created DESC');

if ($listUpdates) {
    $j = 1;
    foreach ($listUpdates as $update) {
        $updateComment = nl2br($update["upd_comments"]);
        $updateCreated = phpCollab\Util::createDate($update["upd_created"], $timezoneSession);
        echo <<<UPDATE
<strong>{$j}</strong> <em>{$updateCreated}</em><br/>{$updateComment}
<br/>
UPDATE;
        $j++;
    }
} else {
    echo $strings["no_items"];
}

echo "</td>
    </tr>
</table>
<hr>";

$listSubtasks = $tasks->getSubtasksByParentTaskId($id, 'subtas.name');

$block2 = new phpCollab\Block();

$block2->heading($strings["subtasks"]);

echo '<div id="subTasks" style="margin-bottom: 2em;">';
if ($listSubtasks) {
    echo <<<START_TABLE
<table style="width: 90%" class="listing striped">
    <tr>
        <th class="active">{$strings["name"]}</th>
        <th>{$strings["description"]}</th>
        <th>{$strings["status"]}</th>
        <th>{$strings["due"]}</th>
    </tr>
START_TABLE;

    foreach ($listSubtasks as $subtask) {
        if (!($i % 2)) {
            $class = "odd";
            $highlightOff = $block2->getOddColor();
        } else {
            $class = "even";
            $highlightOff = $block2->getEvenColor();
        }
        $subtaskDescription = nl2br($subtask["subtas_description"]);
        echo <<<TR
    <tr>
        <td><a href="clientsubtaskdetail.php?task={$id}&id={$subtask["subtas_id"]}">{$subtask["subtas_name"]}</a></td>
        <td>{$subtaskDescription}</td>
        <td>{$status[$subtask["subtas_status"]]}</td>
        <td>{$subtask["subtas_due_date"]}</td>
    </tr>
TR;
    }
    echo "</table>";
} else {
    echo "<div class='no-records'>{$strings["no_items"]}</div>";
}
echo "</div>";

$statusChecked = ($taskDetail["tas_status"] == "0") ? 'checked' : '';


$block2->heading("Complete Task");

echo <<<STATUS_CHANGE_FORM
<form method="post" action="../projects_site/clienttaskdetail.php" name="clientTaskUpdate" enctype="multipart/form-data">
    <input name="id" type="hidden" value="{$id}">
    <input name="action" type="hidden" value="update">

    <table class="nonStriped">
        <tr>
            <th colspan="2">{$strings["client_change_status"]}</th>
        </tr>
        <tr>
            <td>{$strings["status"]} :</td>
            <td><input {$statusChecked} value="checkbox" name="checkbox" type="checkbox">&nbsp;$status[0]</td>
        </tr>
        <tr>
            <td class="leftvalue">{$strings["comments"]} :</td>
            <td><textarea cols="40" name="comments" rows="5">{$taskDetail["tas_comments"]}</textarea></td>
        </tr>
        <tr>
            <td>&#160;</td>
            <td><input name="submit" type="submit" value="{$strings["save"]}"></td>
        </tr>
    </table>
</form>
STATUS_CHANGE_FORM;

echo <<<SHOW_ALL_LINK
<br/><br/><a href="showallclienttasks.php">{$strings["show_all"]}</a>
SHOW_ALL_LINK;

include("include_footer.php");
