<?php
#Application name: PhpCollab
#Status page: 0

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["client_task_details"];

$taskId = !empty($request->query->get('id')) ? $request->query->get('id') : $request->request->get('taskId');

try {
    $tasks = $container->getTasksLoader();
    $taskStatus = $container->getSetTaskStatusServiceService();
    $updates = $container->getTaskUpdateService();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$taskDetail = $tasks->getTaskById($taskId);

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "update") {
                $comments = phpCollab\Util::convertData($request->request->get('comments'));

                if (!empty($request->request->get('status')) && $request->request->get('status') == "completed") {
                    if (!empty($request->request->get('status'))) {
                        $taskStatus->set($taskId, 0, $comments);
                    }
                } else {
                    $taskStatus->set($taskId, $taskDetail["tas_status"], $comments);
                }
                phpCollab\Util::headerFunction("showallclienttasks.php");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Project Site: Task Detail' => $taskId,
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}



if ($taskDetail["tas_published"] == "1" || $taskDetail["tas_project"] != $session->get("project")) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[3] = "over";
$titlePage = $strings["client_task_details"];

include APP_ROOT . '/projects_site/include_header.php';

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
            <td>$complValue</td>
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
            <td>$taskComments</td>
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

$listUpdates = $updates->getUpdates(1, $taskId, 'upd.created DESC');

if ($listUpdates) {
    $j = 1;
    foreach ($listUpdates as $update) {
        $updateComment = nl2br($update["upd_comments"]);
        $updateCreated = phpCollab\Util::createDate($update["upd_created"], $session->get("timezone"));
        echo <<<UPDATE
<strong>$j</strong> <em>$updateCreated</em><br/>$updateComment
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

$listSubtasks = $tasks->getSubtasksByParentTaskId($taskId, 'subtas.name');

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
        $subtaskDescription = nl2br($subtask["subtas_description"]);
        echo <<<TR
    <tr>
        <td><a href="clientsubtaskdetail.php?task=$taskId&id={$subtask["subtas_id"]}">{$subtask["subtas_name"]}</a></td>
        <td>$subtaskDescription</td>
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
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
    <input name="taskId" type="hidden" value="$taskId">

    <table class="nonStriped">
        <tr>
            <th colspan="2">{$strings["client_change_status"]}</th>
        </tr>
        <tr>
            <td>{$strings["status"]} :</td>
            <td><input $statusChecked value="completed" name="status" type="checkbox">&nbsp;$status[0]</td>
        </tr>
        <tr>
            <td class="leftvalue">{$strings["comments"]} :</td>
            <td><textarea cols="40" name="comments" rows="5">{$taskDetail["tas_comments"]}</textarea></td>
        </tr>
        <tr>
            <td>&#160;</td>
            <td><button name="action" type="submit" value="update">{$strings["save"]}</button></td>
        </tr>
    </table>
</form>
STATUS_CHANGE_FORM;

echo <<<SHOW_ALL_LINK
<br/><br/><a href="showallclienttasks.php">{$strings["show_all"]}</a>
SHOW_ALL_LINK;

include APP_ROOT . "/projects_site/include_footer.php";
