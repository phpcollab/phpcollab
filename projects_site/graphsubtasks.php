<?php
#Application name: PhpCollab
#Status page: 0

use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;

$checkSession = "true";
require_once '../includes/library.php';

$tasks = $container->getTasksLoader();
$projects = $container->getProjectsLoader();

$task = $request->query->get('task');
$strings = $GLOBALS["strings"];

$taskDetail = $tasks->getTaskById($task);

$projectDetail = $projects->getProjectById($taskDetail["tas_project"]);

$projectDetail["pro_created"] = phpCollab\Util::createDate($projectDetail["pro_created"], $session->get("timezone"));
$projectDetail["pro_name"] = str_replace('&quot;', '"', $projectDetail["pro_name"]);
$projectDetail["pro_name"] = str_replace("&#39;", "'", $projectDetail["pro_name"]);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["task"] . " " . $taskDetail["tas_name"]);
$graph->subtitle->Set("(" . $strings["created"] . ": " . $taskDetail["tas_created"] . ")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$listTasks = $tasks->getSubtasksByParentTaskIdAndStartAndEndDateAreNotEmptyAndNotPublished($task);

$i = 0;
foreach ($listTasks as $task) {
    $task["subtas_name"] = str_replace('&quot;', '"', $task["subtas_name"]);
    $task["subtas_name"] = str_replace("&#39;", "'", $task["subtas_name"]);
    $progress = round($task["subtas_completion"] / 10, 2);
    $printProgress = $task["subtas_completion"] * 10;
    $activity = new GanttBar($i, $task["subtas_name"], $task["subtas_start_date"], $task["subtas_due_date"]);
    $activity->SetPattern(BAND_LDIAG, "yellow");
    $activity->caption->Set($task["subtas_mem_login"] . " (" . $printProgress . "%)");
    $activity->SetFillColor("gray");
    if ($task["subtas_priority"] == "4" || $task["subtas_priority"] == "5") {
        $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
    } else {
        $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
    }
    $activity->progress->Set($progress);
    $graph->Add($activity);
    $i++;
}

$graph->Stroke();
