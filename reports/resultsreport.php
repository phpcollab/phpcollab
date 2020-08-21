<?php

use phpCollab\Projects\Projects;
use phpCollab\Reports\Reports;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include_once '../includes/library.php';

$tasks = new Tasks();
$reports = new Reports();
$projects = new Projects();

$strings = $GLOBALS["strings"];

$gantt = false;
$queryStart = null;

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "add") {
                $newReport = $reports->addReport(
                    $session->get("idSession"),
                    $request->request->get('report_name'),
                    $request->request->get('filterProject'),
                    $request->request->get('filterOrganization'),
                    $request->request->get('filterAssignedTo'),
                    $request->request->get('filterPriority'),
                    $request->request->get('filterStatus'),
                    $request->request->get('filterStartDate'),
                    $request->request->get('filterEndDate'),
                    $request->request->get('filterDateCompleteStart'),
                    $request->request->get('filterDateCompleteEnd')
                );

                phpCollab\Util::headerFunction("../reports/listreports.php?msg=addReport");
            }
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
    }
}

$setTitle .= " : Report Results";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$id = $request->query->get('id');
$tri = $request->query->get('tri');

$filterOrganization = null;
$filterProject = null;
$filterAssignedTo = null;
$filterStatus = null;
$filterPriority = null;
$filterStartDate = null;
$filterEndDate = null;
$filterDateCompleteStart = null;
$filterDateCompleteEnd = null;
$filterDueDate = null;
$filterCompletedDate = null;


if (empty($id) && $tri != "true") {
    if ($request->isMethod('post')) {
        $formData = $request->request->all();

        if (is_array($request->request->get('S_PRJSEL'))) {
            $S_pro = implode(',', $request->request->get('S_PRJSEL'));
        } else {
            $S_pro = "ALL";
        }

        if (is_array($request->request->get('S_ATSEL'))) {
            $S_mem = implode(',', $request->request->get('S_ATSEL'));
        } else {
            $S_mem = "ALL";
        }

        if (is_array($request->request->get('S_STATSEL'))) {
            $S_sta = implode(',', $request->request->get('S_STATSEL'));
        } else {
            $S_sta = "ALL";
        }

        if (is_array($request->request->get('S_PRIOSEL'))) {
            $S_pri = implode(',', $request->request->get('S_PRIOSEL'));
        } else {
            $S_pri = "ALL";
        }

        $compt5 = count($request->request->get('S_ORGSEL'));
        $S_org = "";

        for ($i = 0; $i < $compt5; $i++) {
            if ($request->request->get('S_ORGSEL')[$i] == "ALL") {
                $S_org = "ALL";
                break;
            }

            if ($i != $compt5 - 1) {
                $S_org .= $request->request->get('S_ORGSEL')[$i] . ",";
            } else {
                $S_org .= $request->request->get('S_ORGSEL')[$i];
            }
        }

        if (is_array($request->request->get('S_ORGSEL'))) {
            $S_org = implode(',', $request->request->get('S_ORGSEL'));
        } else {
            $S_org = "ALL";
        }

        if (!empty($request->request->get('S_SDATE'))) {
            $filterStartDate = DateTime::createFromFormat('Y-m-d', $request->request->get('S_SDATE')) ? $request->request->get('S_SDATE') : null;
        }

        if (!empty($request->request->get('S_EDATE'))) {
            $filterEndDate = DateTime::createFromFormat('Y-m-d', $request->request->get('S_EDATE')) ? $request->request->get('S_EDATE') : null;
        }

        if (!empty($request->request->get('S_SDATE2'))) {
            $filterDateCompleteStart = DateTime::createFromFormat('Y-m-d', $request->request->get('S_SDATE2')) ? $request->request->get('S_SDATE2') : null;
        }

        if (!empty($request->request->get('S_EDATE2'))) {
            $filterDateCompleteEnd = DateTime::createFromFormat('Y-m-d', $request->request->get('S_EDATE2')) ? $request->request->get('S_EDATE2') : null;
        }

        $filterOrganization = $S_org;
        $filterProject = $S_pro;
        $filterAssignedTo = $S_mem;

        $filterStatus = $S_sta;
        $filterPriority = $S_pri;
    }

}

if (!empty($id)) {
    $reportDetail = $reports->getReportsById($id);

    $filterOrganization = $reportDetail['rep_clients'];
    $filterProject = $reportDetail['rep_projects'];
    $filterAssignedTo = $reportDetail['rep_members'];
    $filterStatus = $reportDetail['rep_status'];
    $filterPriority = $reportDetail['rep_priorities'];
    $filterStartDate = $reportDetail['rep_date_due_start'];
    $filterEndDate = $reportDetail['rep_date_due_end'];
    $filterDateCompleteStart = $reportDetail['rep_date_complete_start'];
    $filterDateCompleteEnd = $reportDetail['rep_date_complete_end'];

    if (empty($filterStartDate) && (empty($filterEndDate))) {
        $filterDueDate = "ALL";
    }

    if (empty($filterDateCompleteStart) && empty($filterDateCompleteEnd)) {
        $filterCompletedDate = "ALL";
    }
}

if (is_array($filterProject)) {
    $filterProject = $filterProject[0];
}

if (is_array($filterOrganization)) {
    $filterOrganization = $filterOrganization[0];
}
if (is_array($filterAssignedTo)) {
    $filterAssignedTo = $filterAssignedTo[0];
}
if (is_array($filterStatus)) {
    $filterStatus = $filterStatus[0];
}
if (is_array($filterPriority)) {
    $filterPriority = $filterPriority[0];
}

if (
    $filterProject != "ALL"
    || $filterOrganization != "ALL"
    || $filterAssignedTo != "ALL"
    || $filterStatus != "ALL"
    || $filterPriority != "ALL"
    || $filterDueDate != "ALL"
    || $filterCompletedDate != "ALL"
) {
    $queryStart = "WHERE (";
    if ($filterProject != "ALL" && $filterProject != "") {
        $query = "tas.project IN({$filterProject})";
    }

    if ($filterOrganization != "ALL" && $filterOrganization != "") {
        if ($query != "") {
            $query .= ' AND org.id IN(' . $filterOrganization . ')';
        } else {
            $query .= 'org.id IN(' . $filterOrganization . ')';
        }
    }

    if ($filterAssignedTo != "ALL" && $filterAssignedTo != "") {
        if ($query != "") {
            $query .= " AND tas.assigned_to IN({$filterAssignedTo})";
        } else {
            $query .= "tas.assigned_to IN({$filterAssignedTo})";
        }
    }

    if ($filterStatus != "ALL" && $filterStatus != "") {
        if ($query != "") {
            $query .= " AND tas.status IN({$filterStatus})";
        } else {
            $query .= "tas.status IN({$filterStatus})";
        }
    }

    if ($filterPriority != "ALL" && $filterPriority != "") {
        if ($query != "") {
            $query .= " AND tas.priority IN({$filterPriority})";
        } else {
            $query .= "tas.priority IN({$filterPriority})";
        }
    }

    if ($filterDueDate != "ALL" && $filterStartDate != "") {
        if ($query != "") {
            $query .= " AND tas.due_date >= '{$filterStartDate}'";
        } else {
            $query .= "tas.due_date >= '{$filterStartDate}'";
        }
    }

    if ($filterDueDate != "ALL" && $filterEndDate != "") {
        if ($query != "") {
            $query .= " AND tas.due_date <= '{$filterEndDate}'";
        } else {
            $query .= "tas.due_date <= '{$filterEndDate}'";
        }
    }
    if ($filterCompletedDate != "ALL" && $filterDateCompleteStart != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date >= '{$filterDateCompleteStart}'";
        } else {
            $query .= "tas.complete_date >= '{$filterDateCompleteStart}'";
        }
    }

    if ($filterCompletedDate != "ALL" && $filterDateCompleteEnd != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date <= '{$filterDateCompleteEnd}'";
        } else {
            $query .= "tas.complete_date <= '{$filterDateCompleteEnd}'";
        }
    }

    if ($query != "") {
        $query .= " )";
    }
}

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?", $strings["reports"], "in"));

if ($id != "") {
    $blockPage->itemBreadcrumbs($reportDetail["rep_name"]);
} else {
    $blockPage->itemBreadcrumbs($strings["report_results"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->sorting(
    "report_tasks",
    $sortingUser["report_tasks"],
    "tas.name ASC",
    $sortingFields = [
        0 => "tas.name",
        1 => "tas.priority",
        2 => "tas.status",
        3 => "tas.due_date",
        4 => "tas.complete_date",
        5 => "mem.login",
        6 => "tas.project",
        7 => "tas.published"
    ]
);

if ($projectsFilter == "true") {
    $listProjectsTasks = $projects->getProjectList($session->get("idSession"), 'active', null, null, 'pro.id');


    if ($listProjectsTasks) {
        $filterTasks = array_column($listProjectsTasks, 'pro_id');

        if (!empty($filterTasks)) {
            $filterTasks = implode (", ", $filterTasks);
            if ($query != "") {
                $tmpquery = "{$queryStart} {$query} AND pro.id IN({$filterTasks}) ORDER BY {$block1->sortingValue} ";
            } else {
                $tmpquery = "WHERE pro.id IN({$filterTasks}) ORDER BY {$block1->sortingValue} ";
            }
        }
    } else {
        $validTasks = "false";
    }
} else {
    if (is_null($query)) {
        $tmpquery = " ORDER BY {$block1->sortingValue} ";
    } else {
        $tmpquery = "$queryStart $query ORDER BY {$block1->sortingValue} ";
    }
}

$listTasks = $tasks->getReportTasks($tmpquery);

if ($listTasks) {
    $taskIds = implode(',', array_column($listTasks, 'tas_id'));
}

$listSubTasks = $tasks->getSubtasksByParentTaskIdIn($taskIds);
$totalTasks = count($listTasks) + count($listSubTasks);
$block0 = new phpCollab\Block();

$block0->openContent();
$block0->contentTitle($strings["report_results"]);

if ($totalTasks == "0") {
    $block0->contentRow("", "0 " . $strings["matches"] . "<br/>" . $strings["no_results_report"]);
}

if ($totalTasks == "1") {
    $block0->contentRow("", "1 " . $strings["match"]);
}

if ($totalTasks > "1") {
    $block0->contentRow("", $totalTasks . " " . $strings["matches"]);
}

$block0->closeContent();

$block1->form = "Tasks";
$block1->openForm("../reports/resultsreport.php?&tri=true&id=$id#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["report_results"]);

if (!empty($listTasks)) {
    /**
     * you cannot export or delete a not saved report
     * $block1->openPaletteIcon();
     * $block1->paletteIcon(0,"export",$strings["export"]);
     * $block1->paletteIcon(1,"remove",$strings["delete"]);
     * $block1->closePaletteIcon();
     */

    $block1->openResults('false');

    $block1->labels($labels = [0 => $strings["task"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["due_date"], 4 => $strings["complete_date"], 5 => $strings["assigned_to"], 6 => $strings["project"], 7 => $strings["published"]], "true");

    foreach ($listTasks as $listTask) {
        $idStatus = $listTask["tas_status"];
        $idPriority = $listTask["tas_priority"];
        $idPublish = $listTask["tas_published"];

        $block1->openRow();
        $block1->cellRow('');
        $block1->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $listTask["tas_id"], $listTask["tas_name"], "in"));
        $block1->cellRow('<i style="background-color: yellow;"></i><img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $GLOBALS["priority"][$idPriority]);
        $block1->cellRow($GLOBALS["status"][$idStatus]);

        if ($listTask["tas_due_date"] <= $GLOBALS["date"] && $listTask["tas_completion"] != "10") {
            $block1->cellRow("<b>" . $listTask["tas_due_date"] . "</b>");
        } else {
            $block1->cellRow($listTask["tas_due_date"]);
        }

        if ($listTask["tas_start_date"] != "--" && $listTask["tas_due_date"] != "--") {
            $gantt = "true";
        }

        $block1->cellRow($listTask["tas_complete_date"]);

        if ($listTask["tas_assigned_to"] == "0") {
            $block1->cellRow($strings["unassigned"]);
        } else {
            $block1->cellRow($blockPage->buildLink($listTask["tas_mem_email_work"], $listTask["tas_mem_login"], "mail"));
        }

        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTask["tas_project"], $listTask["tas_pro_name"], "in"));

        if ($sitePublish == "true") {
            $block1->cellRow($GLOBALS["statusPublish"][$idPublish]);
        }

        $block1->closeRow();

        $thisId = $listTask["tas_id"];
        $mySubtasks = array_filter($listSubTasks, function($subTask) use($thisId) {
            return $subTask["subtas_task"] == $thisId;
        });

        if ($mySubtasks) {

            foreach ($mySubtasks as $subTask) {
                $idStatus = $subTask["subtas_status"];
                $idPriority = $subTask["subtas_priority"];
                $idPublish = $subTask["subtas_published"];
                $block1->openRow();
                $block1->cellRow('');
                $block1->cellRow($blockPage->buildLink("../subtasks/viewsubtask.php?id=" . $subTask["subtas_id"] . "&task=" . $subTask["subtas_task"], $subTask["subtas_name"], "in"));
                $block1->cellRow("<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $GLOBALS["priority"][$idPriority]);
                $block1->cellRow($GLOBALS["status"][$idStatus]);

                if ($subTask["subtas_due_date"] <= $GLOBALS["date"] && $subTask["subtas_completion"] != "10") {
                    $block1->cellRow("<b>" . $subTask["subtas_due_date"] . "</b>");
                } else {
                    $block1->cellRow($subTask["subtas_due_date"]);
                }

                if ($subTask["subtas_start_date"] != "--" && $subTask["subtas_due_date"] != "--") {
                    $gantt = "true";
                }

                $block1->cellRow($subTask["subtas_complete_date"]);

                if ($subTask["subtas_assigned_to"] == "0") {
                    $block1->cellRow($strings["unassigned"]);
                } else {
                    $block1->cellRow($blockPage->buildLink($subTask["subtas_mem_email_work"], $subTask["subtas_mem_login"], "mail"));
                }

                $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $subTask["subtas_project"], $subTask["subtas_pro_name"], "in"));

                if ($sitePublish == "true") {
                    $block1->cellRow($GLOBALS["statusPublish"][$idPublish]);
                }

                $block1->closeRow();
            } //end for
        }// end if subtask
    }//end for

    $block1->closeResults();

    if ($activeJpgraph == "true" && $gantt == "true" && $id != "") {
        echo <<< GANTT
			<div id="ganttChart_taskList" class="ganttChart">
				<img src="graphtasks.php?&report={$id}" alt=""><br/>
				<span class="listEvenBold"">{$blockPage->buildLink("http://www.aditus.nu/jpgraph/", "JpGraph", "powered")}</span>
			</div>
GANTT;
    }

    echo <<< HIDDEN
            <input type="hidden" name="filterOrganization" value="{$filterOrganization}" />
			<input type="hidden" name="filterProject" value="{$filterProject}" />
			<input type="hidden" name="filterAssignedTo" value="{$filterAssignedTo}" />
			<input type="hidden" name="filterStatus" value="{$filterStatus}" />
			<input type="hidden" name="filterPriority" value="{$filterPriority}" />
			<input type="hidden" name="filterCompletedDate" value="{$filterCompletedDate}" />
			<input type="hidden" name="filterDueDate" value="{$filterDueDate}" />
HIDDEN;

    $block1->closeFormResults();

    /** you cannot export/delete a not-saved report
     * $block1->openPaletteScript();
     * $block1->paletteScript(0,"export","../reports/exportreport.php?id=$id","true,true,true",$strings["export"]);
     * $block1->paletteScript(1,"remove","../reports/deletereports.php?id=$id","true,true,true",$strings["delete"]);
     * $block1->closePaletteScript($comptListTasks,$listTasks->tas_id);
     */
}

/**
 * Only show the save report section if not viewing a saved report
 */
if (empty($id)) {
    $block2 = new phpCollab\Block();

    $block2->form = "save_report";
    $block2->openForm("../reports/resultsreport.php?action=add", null, $csrfHandler);

    if (isset($error) && $error != "") {
        $block2->headingError($strings["errors"]);
        $block2->contentError($error);
    }

    $block2->openContent();
    $block2->contentTitle($strings["report_save"]);

    echo <<< TR
            <tr class="odd">
                <td class="leftvalue">{$strings["report_name"]} :</td>
                <td><input type="text" name="report_name" value="" style="width: 200px;" maxlength="64"></td>
            </tr>
            <tr class="odd">
                <td class="leftvalue">&nbsp;</td>
                <td><button type="submit" name="action" value="add">{$strings["save"]}</button>
                <input type="hidden" name="filterOrganization" value="{$filterOrganization}" />
                <input type="hidden" name="filterProject" value="{$filterProject}" />
                <input type="hidden" name="filterAssignedTo" value="{$filterAssignedTo}" />
                <input type="hidden" name="filterStatus" value="{$filterStatus}" />
                <input type="hidden" name="filterPriority" value="{$filterPriority}" />
                <input type="hidden" name="filterStartDate" value="{$filterStartDate}" />
                <input type="hidden" name="filterEndDate" value="{$filterEndDate}" />
                <input type="hidden" name="filterDateCompleteStart" value="{$filterDateCompleteStart}" />
                <input type="hidden" name="filterDateCompleteEnd" value="{$filterDateCompleteEnd}" />
                </td>
            </tr>
TR;

    $block2->closeContent();
    $block2->closeForm();
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
