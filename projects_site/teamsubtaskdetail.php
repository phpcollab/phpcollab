<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["team_subtask_details"];

try {
    $tasks = $container->getTasksLoader();
    $updates = $container->getTaskUpdateService();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$subtaskDetail = $tasks->getSubTaskById($request->query->get('id'));

$taskDetail = $tasks->getTaskById($request->query->get('task'));

if ($subtaskDetail["subtas_published"] == "0" || $taskDetail["tas_project"] != $session->get("project")) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[2] = "over";
$titlePage = $strings["team_subtask_details"];
include 'include_header.php';

echo <<<HEADING
<h1 class="heading">{$strings["team_subtask_details"]}</h1>
HEADING;

echo '<table class="nonStriped">';
if ($taskDetail["tas_name"] != "") {
    echo <<<TR
        <tr>
            <td>{$strings["task"]} :</td>
            <td><a href="teamtaskdetail.php?id={$taskDetail["tas_id"]}">{$taskDetail["tas_name"]}</a></td>
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

if ($subtaskDetail["subtas_description"] != "") {
    $subtaskDescription = nl2br($subtaskDetail["subtas_description"]);
    echo <<<TR
        <tr>
            <td style="vertical-align: top">{$strings["description"]} :</td>
            <td>$subtaskDescription</td>
        </tr>
TR;
}

$complValue = ($subtaskDetail["subtas_completion"] > 0) ? $subtaskDetail["subtas_completion"] . "0 %" : $subtaskDetail["subtas_completion"] . " %";

echo <<<TR
        <tr>
            <td>{$strings["completion"]} :</td>
            <td>$complValue</td>
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

echo <<<TR
        <tr>
            <td>{$strings["updates_subtask"]} :</td>
            <td>
TR;

$listUpdates = $updates->getUpdates(2, $request->query->get('id'));

if ($listUpdates) {
    $j = 1;
    foreach ($listUpdates as $update) {
        $updateComments = nl2br($update["upd_comments"]);
        $updateCreatedDate = phpCollab\Util::createDate($update["upd_created"], $session->get("timezone"));
        echo <<<UPDATE
                <b>$j</b> <i>$updateCreatedDate</i><br/> $updateComments
                <br/>
UPDATE;
        $j++;
    }
} else {
    echo $strings["no_items"];
}

echo <<<CLOSE_TABLE
            </td>
        </tr>
    </table>
    <hr>
CLOSE_TABLE;

include("include_footer.php");
