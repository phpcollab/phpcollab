<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Util;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["client_tasks"];

try {
    $tasks = $container->getTasksLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$bouton[3] = "over";
$titlePage = $strings["client_tasks"];

include 'include_header.php';

$listTasks = $tasks->getProjectSiteClientTasks($session->get("project"), null, null, 'tas.name');

$block1 = new phpCollab\Block();

$block1->heading($strings["client_tasks"]);

if ($listTasks) {
    echo <<<TABLE
    <table style="90%" class="listing striped">
        <tr>
            <th class="active">{$strings["name"]}</th>
            <th>{$strings["description"]}</th>
            <th>{$strings["status"]}</th>
            <th>{$strings["due"]}</th>
        </tr>
TABLE;

    foreach ($listTasks as $task) {
        if ($task["tas_due_date"] == "") {
            $task["tas_due_date"] = $strings["none"];
        }
        $idStatus = $task["tas_status"];

        $taskDescription = Util::isBlank(nl2br($task["tas_description"]));
        echo <<<TR
        <tr>
            <td><a href="clienttaskdetail.php?id={$task["tas_id"]}">{$task["tas_name"]}</a></td>
            <td>$taskDescription</td>
            <td>$status[$idStatus]</td>
            <td>{$task["tas_due_date"]}</td>
        </tr>
TR;
    }
    echo "</table>
<hr />\n";
} else {
    echo '<div class="no-records">' . $strings["no_items"] . '</div>';
}

include("include_footer.php");
