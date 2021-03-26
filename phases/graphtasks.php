<?php
#Application name: PhpCollab
#Status page: 0

use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;
use phpCollab\Phases\Phases;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
require_once '../includes/library.php';

$phases = $container->getPhasesLoader();
$tasks = $container->getTasksLoader();

$strings = $GLOBALS["strings"];
$project = $request->query->get('project');
$phase = $request->query->get('phase');

$phaDetail = $phases->getPhasesByProjectIdAndPhaseOrderNum($project, $phase);

$phaDetail["pha_name"] = str_replace('&quot;', '"', $phaDetail["pha_name"]);
$phaDetail["pha_name"] = str_replace("&#39;", "'", $phaDetail["pha_name"]);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["phase"] . " " . $phaDetail["pha_name"]);
$graph->subtitle->Set("(" . $strings["created"] . ": " . $phaDetail["pha_date_start"] . ")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);

$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$listTasks = $tasks->getTasksByProjectIdAndParentPhaseAndStartEndDateNotBlank($project, $phase);

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
