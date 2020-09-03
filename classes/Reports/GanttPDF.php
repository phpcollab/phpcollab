<?php


namespace phpCollab\Reports;


use Amenadiel\JpGraph\Graph\GanttGraph;
use Amenadiel\JpGraph\Plot\GanttBar;
use Exception;

class GanttPDF
{
    public function generateImage($reportName, $listTasks)
    {
        try {

            $graph = new GanttGraph();
            $graph->SetBox();
            $graph->SetMarginColor("white");
            $graph->SetColor("white");
            $graph->title->Set($GLOBALS["strings"]["project"] . " " . $reportName);
            $graph->title->SetFont(FF_FONT1);
            $graph->SetColor("white");
            $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);
            $graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
            $graph->scale->week->SetFont(FF_FONT0);
            $graph->scale->year->SetFont(FF_FONT1);

            $posGantt = 0;

            foreach ($listTasks as $listTask) {
                $listTask["name"] = str_replace('&quot;', '"', $listTask["name"]);
                $listTask["name"] = str_replace("&#39;", "'", $listTask["name"]);
                $progress = round($listTask["completion"] / 10, 2);
                $printProgress = $listTask["completion"] * 10;

                $activity = new GanttBar($posGantt, $listTask["project_name"] . " / " . $listTask["name"],
                    $listTask["start_date"], $listTask["due_date"]);
                $activity->SetPattern(BAND_LDIAG, "yellow");
                $activity->caption->Set($listTask["mem_login"] . " (" . $printProgress . "%)");
                $activity->SetFillColor("gray");

                if ($listTask["priority"] == "4" || $listTask["priority"] == "5") {
                    $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
                } else {
                    $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
                }

                $activity->progress->Set($progress);
                $graph->Add($activity);

                // begin if subtask
                if (!empty($listTask["subtasks"])) {

                    // list subtasks
                    foreach ($listTask["subtasks"] as $subTask) {
                        $subTask["name"] = str_replace('&quot;', '"', $subTask["name"]);
                        $subTask["name"] = str_replace("&#39;", "'", $subTask["name"]);
                        $progress = round($subTask["completion"] / 10, 2);
                        $printProgress = $subTask["completion"] * 10;
                        $posGantt += 1;
                        // change name of project for name of parent task
                        $activity = new GanttBar($posGantt, $listTask["name"] . " / " . $subTask["name"],
                            $subTask["start_date"], $subTask["due_date"]);
                        $activity->SetPattern(BAND_LDIAG, "yellow");
                        $activity->caption->Set($subTask["member_login"] . " (" . $printProgress . "%)");
                        $activity->SetFillColor("gray");

                        if ($subTask["priority"] == "4" || $subTask["priority"] == "5") {
                            $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
                        } else {
                            $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
                        }

                        $activity->progress->Set($progress);
                        $graph->Add($activity);
                    } // end for compl�istsubtask
                } // end if subtask
                $posGantt += 1;
            } // end for complisttask

            $tmpGantt = "../files/" . md5(uniqid(rand()));
            $graph->Stroke($tmpGantt);

            return $tmpGantt;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    } // end ganttPDF

}
