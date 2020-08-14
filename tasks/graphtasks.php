<?php

use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include '../includes/library.php';

$projects = new Projects();
$tasks = new Tasks();

$project = $request->query->get('project');
$strings = $GLOBALS["strings"];

$projectDetail = $projects->getProjectById($project);

$projectDetail["pro_created"] = phpCollab\Util::createDate($projectDetail["pro_created"], $session->get("timezoneSession"));
$projectDetail["pro_name"] = str_replace('&quot;', '"', $projectDetail["pro_name"]);
$projectDetail["pro_name"] = str_replace("&#39;", "'", $projectDetail["pro_name"]);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["project"] . " " . $projectDetail["pro_name"]);
$graph->subtitle->Set("(" . $strings["created"] . ": " . $projectDetail["pro_created"] . ")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$listTasks = $tasks->getTasksByProjectIdWhereStartAndEndAreNotEmpty($project);

$taskCount = 0;
foreach ($listTasks as $task) {
    $task["tas_name"] = str_replace('&quot;', '"', $task["tas_name"]);
    $task["tas_name"] = str_replace("&#39;", "'", $task["tas_name"]);
    $progress = round($task["tas_completion"] / 10, 2);
    $printProgress = $task["tas_completion"] * 10;
    $activity = new GanttBar($taskCount, $task["tas_name"], $task["tas_start_date"], $task["tas_due_date"]);
    $activity->SetPattern(BAND_LDIAG, "yellow");
    $activity->caption->Set($task["tas_mem_login"] . " (" . $printProgress . "%)");
    $activity->SetFillColor("gray");

    if ($task["tas_priority"] == "4" || $task["tas_priority"] == "5") {
        $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
    } else {
        $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
    }

    $activity->progress->Set($progress);
    $graph->Add($activity);
    $taskCount++;
}

$graph->Stroke();
