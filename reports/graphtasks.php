<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../reports/graphtasks.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: graphtasks.php
**
** DESC:
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	new export to PDF by Angel
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $reports = $container->getReportsLoader();
    $tasks = $container->getTasksLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$report = $request->query->get('report');

$reportDetail = $reports->getReportsById($report);

$S_ORGSEL = $reportDetail["rep_clients"];
$S_PRJSEL = $reportDetail["rep_projects"];
$S_ATSEL = $reportDetail["rep_members"];
$S_STATSEL = $reportDetail["rep_status"];
$S_PRIOSEL = $reportDetail["rep_priorities"];
$S_SDATE = $reportDetail["rep_date_due_start"];
$S_EDATE = $reportDetail["rep_date_due_end"];

if ($S_SDATE == "" && $S_EDATE == "") {
    $S_DUEDATE = "ALL";
}

if ($S_ORGSEL != "ALL" || $S_PRJSEL != "ALL" || $S_ATSEL != "ALL" || $S_STATSEL != "ALL" || $S_PRIOSEL != "ALL" || $S_DUEDATE != "ALL") {
    $queryStart = "WHERE (";

    if ($S_PRJSEL != "ALL" && $S_PRJSEL != "") {
        $query = "tas.project IN($S_PRJSEL)";
    }

    if ($S_ORGSEL != "ALL" && $S_ORGSEL != "") {
        if ($query != "") {
            $query .= " AND org.id IN($S_ORGSEL)";
        } else {
            $query .= "org.id IN($S_ORGSEL)";
        }
    }

    if ($S_ATSEL != "ALL" && $S_ATSEL != "") {
        if ($query != "") {
            $query .= " AND tas.assigned_to IN($S_ATSEL)";
        } else {
            $query .= "tas.assigned_to IN($S_ATSEL)";
        }
    }

    if ($S_STATSEL != "ALL" && $S_STATSEL != "") {
        if ($query != "") {
            $query .= " AND tas.status IN($S_STATSEL)";
        } else {
            $query .= "tas.status IN($S_STATSEL)";
        }
    }

    if ($S_PRIOSEL != "ALL" && $S_PRIOSEL != "") {
        if ($query != "") {
            $query .= " AND tas.priority IN($S_PRIOSEL)";
        } else {
            $query .= "tas.priority IN($S_PRIOSEL)";
        }
    }

    if ($S_DUEDATE != "ALL" && $S_SDATE != "--") {
        if ($query != "") {
            $query .= " AND tas.due_date >= '$S_SDATE'";
        } else {
            $query .= "tas.due_date >= '$S_SDATE'";
        }
    }

    if ($S_DUEDATE != "ALL" && $S_EDATE != "--") {
        if ($query != "") {
            $query .= " AND tas.due_date <= '$S_EDATE'";
        } else {
            $query .= "tas.due_date <= '$S_EDATE'";
        }
    }

    $query .= ")";
}

$reportDetail["rep_created"] = phpCollab\Util::createDate($reportDetail["rep_created"], $session->get("timezone"));

$graph = new GanttGraph();
$graph->SetBox();
$graph->SetMarginColor("white");
$graph->SetColor("white");
$graph->title->Set($strings["report"] . " " . $reportDetail["rep_name"]);
$graph->subtitle->Set("(" . $strings["created"] . ": " . $reportDetail["rep_created"] . ")");
$graph->title->SetFont(FF_FONT1);
$graph->SetColor("white");
$graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
$graph->scale->week->SetFont(FF_FONT0);
$graph->scale->year->SetFont(FF_FONT1);

$tmpquery = "$queryStart $query";
$posGantt = 0;

$listTasks = $tasks->getSearchTasks($tmpquery, 'tas.name');

foreach ($listTasks as $listTask) {
    if (empty($listTask["tas_start_date"])) {
        $listTask["tas_start_date"] = $listTask["tas_created"];
    }
    $listTask["tas_name"] = str_replace('&quot;', '"', $listTask["tas_name"]);
    $listTask["tas_name"] = str_replace("&#39;", "'", $listTask["tas_name"]);
    $listTask["tas_pro_name"] = str_replace('&quot;', '"', $listTask["tas_pro_name"]);
    $listTask["tas_pro_name"] = str_replace("&#39;", "'", $listTask["tas_pro_name"]);

    $tmpStartDate = $listTask["tas_start_date"];
    $progress = round($listTask["tas_completion"] / 10, 2);
    $printProgress = $listTask["tas_completion"] * 10;
    $activity = new GanttBar(
        $posGantt,
        $listTask["tas_pro_name"] . " / " . $listTask["tas_name"],
        $listTask["tas_start_date"],
        $listTask["tas_due_date"]
    );

    $activity->SetPattern(BAND_LDIAG, "yellow");
    $activity->caption->Set($listTask["tas_mem_login"] . " (" . $printProgress . "%)");
    $activity->SetFillColor("gray");

    if ($listTask["tas_priority"] == "4" || $listTask["tas_priority"] == "5") {
        $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
    } else {
        $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
    }

    $activity->progress->Set($progress);
    $graph->Add($activity);

    // begin if subtask
    $listSubTasks = $tasks->getSubtasksByParentTaskId($listTask["tas_id"]);

    if ($listSubTasks) {
        // list subtasks
        foreach ($listSubTasks as $subTask) {
            $subTask["subtas_name"] = str_replace('&quot;', '"', $subTask["subtas_name"]);
            $subTask["subtas_name"] = str_replace("&#39;", "'", $subTask["subtas_name"]);
            $subTask["subtas_due_date"] = ($subTask["subtas_due_date"] == "--") ? "" : $subTask["subtas_due_date"];
            $progress = round($subTask["subtas_completion"] / 10, 2);
            $printProgress = $subTask["subtas_completion"] * 10;
            $posGantt += 1;
            // change name of project for name of parent task
            $activity = new GanttBar(
                $posGantt,
                $subTask["subtas_tas_name"] . " / " . $subTask["subtas_name"],
                $subTask["subtas_start_date"],
                $subTask["subtas_due_date"]
            );
            $activity->SetPattern(BAND_LDIAG, "yellow");
            $activity->caption->Set($subTask["subtas_mem_login"] . " (" . $printProgress . "%)");
            $activity->SetFillColor("gray");

            if ($subTask["subtas_priority"] == "4" || $subTask["subtas_priority"] == "5") {
                $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
            } else {
                $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
            }

            $activity->progress->Set($progress);
            $graph->Add($activity);
        }
    }// end if subtask

    // end subtask
    $posGantt += 1;
}

$graph->Stroke();
