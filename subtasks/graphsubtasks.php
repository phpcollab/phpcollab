<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

include '../includes/jpgraph/jpgraph.php';
include '../includes/jpgraph/jpgraph_gantt.php';

$tmpquery = "WHERE tas.id = '".$task."'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '".$taskDetail->tas_project[0]."'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$projectDetail->pro_created[0] = Util::createDate($projectDetail->pro_created[0],$timezoneSession);
$projectDetail->pro_name[0] = str_replace('&quot;','"',$projectDetail->pro_name[0]);
$projectDetail->pro_name[0] = str_replace("&#39;","'",$projectDetail->pro_name[0]);

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["task"]." ".$taskDetail->tas_name[0]);
$graph->subtitle->Set("(".$strings["created"].": ".$taskDetail->tas_created[0].")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$tmpquery = "WHERE subtas.task = '$task' AND subtas.start_date != '--' AND subtas.due_date != '--' ORDER BY subtas.due_date";
$listTasks = new Request();
$listTasks->openSubtasks($tmpquery);
$comptListTasks = count($listTasks->subtas_id);

for ($i=0;$i<$comptListTasks;$i++) {
$listTasks->subtas_name[$i] = str_replace('&quot;','"',$listTasks->subtas_name[$i]);
$listTasks->subtas_name[$i] = str_replace("&#39;","'",$listTasks->subtas_name[$i]);
$progress = round($listTasks->subtas_completion[$i]/10,2);
$printProgress = $listTasks->subtas_completion[$i]*10;
$activity = new GanttBar($i,$listTasks->subtas_name[$i],$listTasks->subtas_start_date[$i],$listTasks->subtas_due_date[$i]);
$activity->SetPattern(BAND_LDIAG,"yellow");
$activity->caption->Set($listTasks->subtas_mem_login[$i]." (".$printProgress."%)");

$activity->SetFillColor("gray");
if ($listTasks->subtas_priority[$i] == "4" || $listTasks->subtas_priority[$i] == "5") {
	$activity->progress->SetPattern(BAND_SOLID,"#BB0000");
} else {
	$activity->progress->SetPattern(BAND_SOLID,"#0000BB");
}
$activity->progress->Set($progress);
$graph->Add($activity);
}

$graph->Stroke();
?>