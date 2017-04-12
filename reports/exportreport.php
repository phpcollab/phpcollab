<?php

// PDF setup
include('../includes/class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('../includes/fonts/Helvetica.afm');
$pdf->ezSetMargins(50, 70, 50, 50);

// begin PHPCollab code
$checkSession = "true";
include '../includes/library.php';

$id = isset($_GET["id"]) ? $_GET["id"] : 0;
$tri = isset($_GET["tri"]) ? $_GET["tri"] : null;

$S_PRJSEL = isset($GLOBALS["S_PRJSEL"]) ? $GLOBALS["S_PRJSEL"] : null;
$S_ORGSEL = isset($GLOBALS["S_ORGSEL"]) ? $GLOBALS["S_ORGSEL"] : null;
$S_ATSEL = isset($GLOBALS["S_ATSEL"]) ? $GLOBALS["S_ATSEL"] : null;
$S_STATSEL = isset($GLOBALS["S_STATSEL"]) ? $GLOBALS["S_STATSEL"] : null;
$S_PRIOSEL = isset($GLOBALS["S_PRIOSEL"]) ? $GLOBALS["S_PRIOSEL"] : null;
$S_DUEDATE = isset($GLOBALS["S_DUEDATE"]) ? $GLOBALS["S_DUEDATE"] : null;
$S_COMPLETEDATE = isset($GLOBALS["S_COMPLETEDATE"]) ? $GLOBALS["S_COMPLETEDATE"] : null;
$tableCollab = $GLOBALS["tableCollab"];
$idSession = $_SESSION["idSession"];
$msgLabel = $GLOBALS["msgLabel"];
$strings = $GLOBALS["strings"];

$organizations = new \phpCollab\Organizations\Organizations();
$reports = new \phpCollab\Reports\Reports();
$projects = new \phpCollab\Projects\Projects();
$tasks = new \phpCollab\Tasks\Tasks();

// get company info
$clientDetail = $organizations->getOrganizationById(1);

$cn = $clientDetail["org_name"];
$add = $clientDetail["org_address1"];
$wp = $clientDetail["org_phone"];
$url = $clientDetail["org_url"];
$email = $clientDetail["org_email"];
$c = $clientDetail["org_comments"];

// get task info
if ($id == "" && $tri != "true") {
    $compt1 = count($S_PRJSEL);
    $S_pro = "";

    for ($i = 0; $i < $compt1; $i++) {
        if ($S_PRJSEL[$i] == "ALL") {
            $S_pro = "ALL";
            break;
        }
        if ($i != $compt1 - 1) {
            $S_pro .= $S_PRJSEL[$i] . ",";
        } else {
            $S_pro .= $S_PRJSEL[$i];
        }
    }

    $compt2 = count($S_ATSEL);
    $S_mem = "";

    for ($i = 0; $i < $compt2; $i++) {
        if ($S_ATSEL[$i] == "ALL") {
            $S_mem = "ALL";
            break;
        }

        if ($i != $compt2 - 1) {
            $S_mem .= $S_ATSEL[$i] . ",";
        } else {
            $S_mem .= $S_ATSEL[$i];
        }
    }

    $compt3 = count($S_STATSEL);
    $S_sta = "";

    for ($i = 0; $i < $compt3; $i++) {
        if ($S_STATSEL[$i] == "ALL") {
            $S_sta = "ALL";
            break;
        }

        if ($i != $compt3 - 1) {
            $S_sta .= $S_STATSEL[$i] . ",";
        } else {
            $S_sta .= $S_STATSEL[$i];
        }
    }

    $compt4 = count($S_PRIOSEL);
    $S_pri = "";

    for ($i = 0; $i < $compt4; $i++) {
        if ($S_PRIOSEL[$i] == "ALL") {
            $S_pri = "ALL";
            break;
        }

        if ($i != $compt4 - 1) {
            $S_pri .= $S_PRIOSEL[$i] . ",";
        } else {
            $S_pri .= $S_PRIOSEL[$i];
        }
    }

    $compt5 = count($S_ORGSEL);
    $S_org = "";

    for ($i = 0; $i < $compt5; $i++) {
        if ($S_ORGSEL[$i] == "ALL") {
            $S_org = "ALL";
            break;
        }

        if ($i != $compt5 - 1) {
            $S_org .= $S_ORGSEL[$i] . ",";
        } else {
            $S_org .= $S_ORGSEL[$i];
        }
    }

    $S_ORGSEL = $S_org;
    $S_PRJSEL = $S_pro;
    $S_ATSEL = $S_mem;

    $S_STATSEL = $S_sta;
    $S_PRIOSEL = $S_pri;
}

if ($id != "") {

    $reportDetail = $reports->getReportsById($id);
    $reportName = $reportDetail["rep_name"];
    $S_ORGSEL = $reportDetail["rep_clients"];
    $S_PRJSEL = $reportDetail["rep_projects"];
    $S_ATSEL = $reportDetail["rep_members"];
    $S_STATSEL = $reportDetail["rep_status"];
    $S_PRIOSEL = $reportDetail["rep_priorities"];
    $S_SDATE = $reportDetail["rep_date_due_start"];
    $S_EDATE = $reportDetail["rep_date_due_end"];
    $S_SDATE2 = $reportDetail["rep_date_complete_start"];
    $S_EDATE2 = $reportDetail["rep_date_complete_end"];

    if ($S_SDATE == "" && $S_EDATE == "") {
        $S_DUEDATE = "ALL";
    }

    if ($S_SDATE2 == "" && $S_EDATE2 == "") {
        $S_COMPLETEDATE = "ALL";
    }
}

if ($S_PRJSEL != "ALL" || $S_ORGSEL != "ALL" || $S_ATSEL != "ALL" || $S_STATSEL != "ALL" || $S_PRIOSEL != "ALL" || $S_DUEDATE != "ALL" || $S_COMPLETEDATE != "ALL") {
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

    if ($S_DUEDATE != "ALL" && $S_SDATE != "") {
        if ($query != "") {
            $query .= " AND tas.due_date >= '$S_SDATE'";
        } else {
            $query .= "tas.due_date >= '$S_SDATE'";
        }
    }

    if ($S_DUEDATE != "ALL" && $S_EDATE != "") {
        if ($query != "") {
            $query .= " AND tas.due_date <= '$S_EDATE'";
        } else {
            $query .= "tas.due_date <= '$S_EDATE'";
        }
    }

    if ($S_COMPLETEDATE != "ALL" && $S_SDATE2 != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date >= '$S_SDATE2'";
        } else {
            $query .= "tas.complete_date >= '$S_SDATE2'";
        }
    }

    if ($S_COMPLETEDATE != "ALL" && $S_EDATE2 != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date <= '$S_EDATE2'";
        } else {
            $query .= "tas.complete_date <= '$S_EDATE2'";
        }
    }

    $query .= ")";
}


$blockPage = new phpCollab\Block();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}


$block1 = new phpCollab\Block();

$block1->sorting("report_tasks", $sortingUser->sor_report_tasks[0], "tas.complete_date DESC", $sortingFields = array(0 => "tas.name", 1 => "tas.project", 2 => "tas.actual_time", 3 => "tas.completion", 4 => "tas.status", 5 => "tas.start_date", 6 => "tas.due_date", 7 => "tas.complete_date", 8 => "mem.login", 9 => "tas.description", 10 => "tas.comments"));

if ($projectsFilter == "true") {
    $listProjectsTasks = $projects->getProjectList($idSession, 'active', 'pro.id');

    $filterTasks = null;
    if ($listProjectsTasks) {
        foreach ($listProjectsTasks as $task) {
            $filterTasks .= $task["pro_id"];
        }
        $filterTasks = rtrim(rtrim($filterTasks),',');

        if ($query != "") {
            $tmpquery = "$queryStart $query AND pro.id IN($filterTasks) ORDER BY {$block1->sortingValue}";
        } else {
            $tmpquery = "WHERE pro.id IN($filterTasks) ORDER BY " . $block1->sortingValue . " ";
        }

    } else {
        $validTasks = "false";
    }
} else {
    $tmpquery = "$queryStart $query ORDER BY " . $block1->sortingValue . " ";
}


$listTasks = new phpCollab\Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

$sum = 0.0;

// begin PDF code 	

// print the page number
$pdf->ezStartPageNumbers(526, 34, 6, 'right', '', 1);

// company name at the top of the first page
$pdf->ezText("<b>" . $cn . "</b>", 18, array('justification' => 'center'));

// report name at the top of the first page
$pdf->ezText($strings["report"] . ": " . $reportName . "\n", 16, array('justification' => 'center'));

// put a line top and bottom on all the pages and company info on the bottom
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0, 0, 0, 1);
$pdf->line(20, 40, 578, 40);
$pdf->line(20, 822, 578, 822);
$pdf->addText(50, 34, 6, $cn . " - " . $url);
$pdf->AddText(510, 34, 6, "Page ");
$pdf->restoreState();
$pdf->closeObject();
$pdf->addObject($all, 'all');

// iterate through tasks
for ($i = 0; $i < $comptListTasks; $i++) {
    $idStatus = $listTasks->tas_status[$i];
    $idPriority = $listTasks->tas_priority[$i];
    $actualTime = str_replace(",", ".", $listTasks->tas_actual_time[$i]);
    $sum += $actualTime;

    if ($listTasks->tas_assigned_to[$i] == "0") {
        $idAssigned = $strings["unassigned"];
    } else {
        $idAssigned = $listTasks->tas_mem_login[$i];
    }

    // stuff values into an array
    $data = array(
        array('item' => $strings["project"], 'value' => $listTasks->tas_pro_name[$i])
    , array('item' => $strings["worked_hours"], 'value' => $actualTime)
    , array('item' => $strings["Pct_Complete"], 'value' => ($listTasks->tas_completion[$i] * 10) . '%')
    , array('item' => $strings["status"], 'value' => $status[$idStatus])
    , array('item' => $strings["start_date"], 'value' => $listTasks->tas_start_date[$i])
    , array('item' => $strings["due_date"], 'value' => $listTasks->tas_due_date[$i])
    , array('item' => $strings["complete_date"], 'value' => $listTasks->tas_complete_date[$i])
    , array('item' => $strings["assigned_to"], 'value' => $idAssigned)
    , array('item' => $strings["description"], 'value' => $listTasks->tas_description[$i])
    , array('item' => $strings["comments"], 'value' => $listTasks->tas_comments[$i])
    );

    // set table data and draw table
    $cols = array('item' => 'Item', 'value' => 'Value');
    $pdf->ezText($strings["task"] . ": " . $listTasks->tas_name[$i] . "\n", 12);
    $pdf->saveState();
    $pdf->ezTable($data, $cols, '', array('xPos' => 50, 'xOrientation' => 'right', 'width' => 510, 'fontSize' => 10, 'showHeadings' => 0, 'protectRows' => 2, 'cols' => array('item' => array('width' => 90))));
    $pdf->restoreState();
    $pdf->ezText("\n");

    // if subtask
    $listSubTasks = $tasks->getSubtasksByParentTaskId($listTasks->tas_id[$i]);
//    $comptListSubTasks = count($listSubTasks->subtas_id);

    if ($listSubTasks) {
        foreach ($listSubTasks as $subTask) {
            $idStatus = $subTask["subtas_status"];
            $idPriority = $subTask["subtas_priority"];
            $actualTime = str_replace(",", ".", $subTask["subtas_actual_time"]);
            $sum += $actualTime;
            if ($subTask["subtas_assigned_to"] == "0") {
                $idAssigned = $strings["unassigned"];
            } else {
                $idAssigned = $subTask["subtas_mem_login"];
            }
            // stuff values into an array
            $data = array(
                array('item' => $strings["project"], 'value' => $listTasks->tas_pro_name[$i])
            , array('item' => $strings["worked_hours"], 'value' => $actualTime)
            , array('item' => $strings["Pct_Complete"], 'value' => ($subTask["subtas_completion"] * 10) . '%')
            , array('item' => $strings["status"], 'value' => $status[$idStatus])
            , array('item' => $strings["start_date"], 'value' => $subTask["subtas_start_date"])
            , array('item' => $strings["due_date"], 'value' => $subTask["subtas_due_date"])
            , array('item' => $strings["complete_date"], 'value' => $subTask["subtas_complete_date"])
            , array('item' => $strings["assigned_to"], 'value' => $idAssigned)
            , array('item' => $strings["description"], 'value' => $subTask["subtas_description"])
            , array('item' => $strings["comments"], 'value' => $subTask["subtas_comments"])
            );
            // set table data and draw table
            $cols = array('item' => 'Item', 'value' => 'Value');
            $pdf->ezText($strings["task"] . ": " . $subTask["subtas_name"] . "\n", 12);
            $pdf->saveState();
            $pdf->ezTable($data, $cols, '', array('xPos' => 50, 'xOrientation' => 'right', 'width' => 510, 'fontSize' => 10, 'showHeadings' => 0, 'protectRows' => 2, 'cols' => array('item' => array('width' => 90))));
            $pdf->restoreState();
            $pdf->ezText("\n");
        } // end for complistsubtask
    } // end if subtask

} // close task loop

// add a grey bar and output the hours worked
$tmp = $strings["Total_Hours_Worked"] . ": " . $sum;
$pdf->transaction('start');
$ok = 0;
while (!$ok) {
    $thisPageNum = $pdf->ezPageCount;
    $pdf->saveState();
    $pdf->setColor(0.9, 0.9, 0.9);
    $pdf->filledRectangle($pdf->ez['leftMargin'], $pdf->y - $pdf->getFontHeight(12) + $pdf->getFontDecender(12), $pdf->ez['pageWidth'] - $pdf->ez['leftMargin'] - $pdf->ez['rightMargin'], $pdf->getFontHeight(12));
    $pdf->restoreState();
    $pdf->ezText($tmp, 12, array('justification' => 'left'));

    if ($pdf->ezPageCount == $thisPageNum) {
        $pdf->transaction('commit');
        $ok = 1;
    } else {
        // then we have moved onto a new page, bad bad, as the background rectangle will be on the old one
        $pdf->transaction('rewind');
        $pdf->ezNewPage();
    }
}
// begin include gantt graph in pdf
$pdf->ezText("\n\n");
$graphPDF = ganttPDF($reportName, $listTasks);
$pdf->ezImage($graphPDF, -5, 510, "", "left");
unlink("../files/" . $graphPDF);
// end include gantt graph in pdf

// output the PDF
$pdf->ezStream();

function ganttPDF($reportName, $listTasks, $tasks)
{
    include '../includes/jpgraph/jpgraph.php';
    include '../includes/jpgraph/jpgraph_gantt.php';

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

    $comptListTasks = count($listTasks->tas_id);
    $posGantt = 0;

    for ($i = 0; $i < $comptListTasks; $i++) {
        $listTasks->tas_name[$i] = str_replace('&quot;', '"', $listTasks->tas_name[$i]);
        $listTasks->tas_name[$i] = str_replace("&#39;", "'", $listTasks->tas_name[$i]);
        $progress = round($listTasks->tas_completion[$i] / 10, 2);
        $printProgress = $listTasks->tas_completion[$i] * 10;
        $activity = new GanttBar($posGantt, $listTasks->tas_pro_name[$i] . " / " . $listTasks->tas_name[$i], $listTasks->tas_start_date[$i], $listTasks->tas_due_date[$i]);
        $activity->SetPattern(BAND_LDIAG, "yellow");
        $activity->caption->Set($listTasks->tas_mem_login[$i] . " (" . $printProgress . "%)");
        $activity->SetFillColor("gray");

        if ($listTasks->tas_priority[$i] == "4" || $listTasks->tas_priority[$i] == "5") {
            $activity->progress->SetPattern(BAND_SOLID, "#BB0000");
        } else {
            $activity->progress->SetPattern(BAND_SOLID, "#0000BB");
        }

        $activity->progress->Set($progress);
        $graph->Add($activity);

        // begin if subtask
        $listSubTasks = $tasks->getSubtasksByParentTaskId($listTasks->tas_id[$i]);

        if ($listSubTasks) {
            // list subtasks
            foreach ($listSubTasks as $subTask) {
                $subTask["subtas_name"] = str_replace('&quot;', '"', $subTask["subtas_name"]);
                $subTask["subtas_name"] = str_replace("&#39;", "'", $subTask["subtas_name"]);
                $progress = round($subTask["subtas_completion"] / 10, 2);
                $printProgress = $subTask["subtas_completion"] * 10;
                $posGantt += 1;
                // change name of project for name of parent task
                $activity = new GanttBar($posGantt, $subTask["subtas_tas_name"] . " / " . $subTask["subtas_name"], $subTask["subtas_start_date"], $subTask["subtas_due_date"]);
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
            } // end for compl�istsubtask
        } // end if subtask
        $posGantt += 1;
    } // end for complisttask

    $tmpGantt = "../files/" . md5(uniqid(rand()));
    $graph->Stroke($tmpGantt);
    return $tmpGantt;

} // end ganttPDF

