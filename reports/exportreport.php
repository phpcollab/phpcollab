<?php

// begin PHPCollab code
use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Reports\GanttPDF;
use phpCollab\Reports\Reports;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include '../includes/library.php';


// PDF setup
$pdf = new Cezpdf();

$pdf->selectFont('../includes/fonts/Helvetica.afm');
$pdf->ezSetMargins(50, 70, 50, 50);

$id = $request->query->get('id', 0);
$tri = $request->query->get('tri');

$S_PRJSEL = isset($GLOBALS["S_PRJSEL"]) ? $GLOBALS["S_PRJSEL"] : null;
$S_ORGSEL = isset($GLOBALS["S_ORGSEL"]) ? $GLOBALS["S_ORGSEL"] : null;
$S_ATSEL = isset($GLOBALS["S_ATSEL"]) ? $GLOBALS["S_ATSEL"] : null;
$S_STATSEL = isset($GLOBALS["S_STATSEL"]) ? $GLOBALS["S_STATSEL"] : null;
$S_PRIOSEL = isset($GLOBALS["S_PRIOSEL"]) ? $GLOBALS["S_PRIOSEL"] : null;
$S_DUEDATE = isset($GLOBALS["S_DUEDATE"]) ? $GLOBALS["S_DUEDATE"] : null;
$S_SDATE = isset($GLOBALS["S_SDATE"]) ? $GLOBALS["S_SDATE"] : null;
$S_EDATE = isset($GLOBALS["S_EDATE"]) ? $GLOBALS["S_EDATE"] : null;
$S_SDATE2 = isset($GLOBALS["S_SDATE2"]) ? $GLOBALS["S_SDATE2"] : null;
$S_EDATE2 = isset($GLOBALS["S_EDATE2"]) ? $GLOBALS["S_EDATE2"] : null;
$S_COMPLETEDATE = isset($GLOBALS["S_COMPLETEDATE"]) ? $GLOBALS["S_COMPLETEDATE"] : null;

$msgLabel = $GLOBALS["msgLabel"];
$strings = $GLOBALS["strings"];

$organizations = new Organizations();
$reports = new Reports();
$projects = new Projects();
$tasks = new Tasks();

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

if ($S_PRJSEL != "ALL"
    || $S_ORGSEL != "ALL"
    || $S_ATSEL != "ALL"
    || $S_STATSEL != "ALL"
    || $S_PRIOSEL != "ALL"
    || $S_DUEDATE != "ALL"
    || $S_COMPLETEDATE != "ALL"
) {
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

$block1->sorting("report_tasks", $sortingUser["report_tasks"], "tas.complete_date DESC", $sortingFields = [0 => "tas.name", 1 => "tas.project", 2 => "tas.actual_time", 3 => "tas.completion", 4 => "tas.status", 5 => "tas.start_date", 6 => "tas.due_date", 7 => "tas.complete_date", 8 => "mem.login", 9 => "tas.description", 10 => "tas.comments"]);

$queryStart = isset($queryStart) ? $queryStart : null;
if ($projectsFilter == "true") {
    $listProjectsTasks = $projects->getProjectList($session->get("id"), 'active', 'pro.id');

    $filterTasks = null;
    if ($listProjectsTasks) {
        $filterTasks = implode(',', array_column($listProjectsTasks, 'pro_id'));

        if ($query != "" && isset($queryStart)) {
            $tmpquery = "$queryStart $query AND pro.id IN($filterTasks)";
        } else {
            $tmpquery = "WHERE pro.id IN($filterTasks)";
        }

    } else {
        $validTasks = "false";
    }
} else {
    $tmpquery = "$queryStart $query";
}


$listTasks = $tasks->getSearchTasks($tmpquery, $block1->sortingValue);
$comptListTasks = count($listTasks);

$sum = 0.0;

// begin PDF code

// print the page number
$pdf->ezStartPageNumbers(526, 34, 6, 'right', '', 1);

// company name at the top of the first page
$pdf->ezText("<b>" . $cn . "</b>", 18, ['justification' => 'center']);

// report name at the top of the first page
$reportName = isset($reportName) ? $reportName : '';
$pdf->ezText($strings["report"] . ": " . $reportName . "\n", 16, ['justification' => 'center']);

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

$pdfTasks = [];

// iterate through tasks
foreach ($listTasks as $task) {
    $idStatus = $task["tas_status"];
    $idPriority = $task["tas_priority"];
    $task["tas_actual_time"] = (empty($task["tas_actual_time"])) ? 0 : $task["tas_actual_time"];
    $actualTime = str_replace(",", ".", $task["tas_actual_time"]);
    $sum += $actualTime;

    if ($task["tas_assigned_to"] == "0") {
        $idAssigned = $strings["unassigned"];
    } else {
        $idAssigned = $task["tas_mem_login"];
    }

// stuff values into an array
    $data = [
        ['item' => $strings["project"], 'value' => $task["tas_pro_name"]]
        , ['item' => $strings["worked_hours"], 'value' => $actualTime]
        , ['item' => $strings["Pct_Complete"], 'value' => ($task["tas_completion"] * 10) . '%']
        , ['item' => $strings["status"], 'value' => $status[$idStatus]]
        , ['item' => $strings["start_date"], 'value' => $task["tas_start_date"]]
        , ['item' => $strings["due_date"], 'value' => $task["tas_due_date"]]
        , ['item' => $strings["complete_date"], 'value' => $task["tas_complete_date"]]
        , ['item' => $strings["assigned_to"], 'value' => $idAssigned]
        , ['item' => $strings["description"], 'value' => $task["tas_description"]]
        , ['item' => $strings["comments"], 'value' => $task["tas_comments"]]
    ];

    $thisPdfTask = [
        "id" => $task["tas_id"],
        "name" => $task["tas_name"],
        "completion" => $task["tas_completion"],
        "project_name" => $task["tas_pro_name"],
        "start_date" => $task["tas_start_date"],
        "due_date" => $task["tas_due_date"],
        "member_login" => $task["tas_mem_login"],
        "priority" => $task["tas_priority"],
        "subtasks" => []
    ];

// set table data and draw table
    $cols = ['item' => 'Item', 'value' => 'Value'];
    $pdf->ezText($strings["task"] . ": " . $task["tas_name"] . "\n", 12);
    $pdf->saveState();
    $pdf->ezTable($data, $cols, '', ['xPos' => 50, 'xOrientation' => 'right', 'width' => 510, 'fontSize' => 10, 'showHeadings' => 0, 'protectRows' => 2, 'cols' => ['item' => ['width' => 90]]]);
    $pdf->restoreState();
    $pdf->ezText("\n");

// if subtask
    $listSubTasks = $tasks->getSubtasksByParentTaskId($task["tas_id"]);

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
            $data = [
                ['item' => $strings["project"], 'value' => $task["tas_pro_name"]]
                , ['item' => $strings["worked_hours"], 'value' => $actualTime]
                , ['item' => $strings["Pct_Complete"], 'value' => ($subTask["subtas_completion"] * 10) . '%']
                , ['item' => $strings["status"], 'value' => $status[$idStatus]]
                , ['item' => $strings["start_date"], 'value' => $subTask["subtas_start_date"]]
                , ['item' => $strings["due_date"], 'value' => $subTask["subtas_due_date"]]
                , ['item' => $strings["complete_date"], 'value' => $subTask["subtas_complete_date"]]
                , ['item' => $strings["assigned_to"], 'value' => $idAssigned]
                , ['item' => $strings["description"], 'value' => $subTask["subtas_description"]]
                , ['item' => $strings["comments"], 'value' => $subTask["subtas_comments"]]
            ];
// set table data and draw table
            $cols = ['item' => 'Item', 'value' => 'Value'];
            $pdf->ezText($strings["task"] . ": " . $subTask["subtas_name"] . "\n", 12);
            $pdf->saveState();
            $pdf->ezTable($data, $cols, '', ['xPos' => 50, 'xOrientation' => 'right', 'width' => 510, 'fontSize' => 10, 'showHeadings' => 0, 'protectRows' => 2, 'cols' => ['item' => ['width' => 90]]]);
            $pdf->restoreState();
            $pdf->ezText("\n");

            array_push($thisPdfTask["subtasks"], [
                "id" => $subTask["subtas_id"],
                "name" => $subTask["subtas_name"],
                "completion" => $subTask["subtas_completion"],
                "start_date" => $subTask["subtas_start_date"],
                "due_date" => $subTask["subtas_due_date"],
                "member_login" => $subTask["subtas_mem_login"],
                "priority" => $subTask["subtas_priority"],
            ]);
        } // end for complistsubtask
    } // end if subtask

    array_push($pdfTasks, $thisPdfTask);
} // close task loop

// add a grey bar and output the hours worked
$tmp = $strings["Total_Hours_Worked"] . ": " . $sum;
$pdf->transaction('start');
$ok = 0;
while (!$ok) {
    $thisPageNum = $pdf->ezPageCount;


    $y = $pdf->y - $pdf->getFontHeight(12) + $pdf->getFontDescender(12);

    $pdf->saveState();
    $pdf->setColor(0.9, 0.9, 0.9);
    $pdf->filledRectangle(
        $pdf->ez['leftMargin'],
        $pdf->y - $pdf->getFontHeight(12) + $pdf->getFontDescender(12),
        $pdf->ez['pageWidth'] - $pdf->ez['leftMargin'] - $pdf->ez['rightMargin'],
        $pdf->getFontHeight(12)
    );
    $pdf->restoreState();
    $pdf->ezText($tmp, 12, ['justification' => 'left']);

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

$ganttPdf = new GanttPDF();
$graphPDF = $ganttPdf->generateImage($reportName, $pdfTasks);

$pdf->ezImage($graphPDF, -5, 510, "", "left");
unlink("../files/" . $graphPDF);
// end include gantt graph in pdf

// output the PDF
$pdf->ezStream();

