<?php

$checkSession = "true";
include_once '../includes/library.php';

$tasks = new \phpCollab\Tasks\Tasks();

$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];

$gantt = false;
$queryStart = null;

if ($_GET["action"] == "add") {
    $S_SAVENAME = phpCollab\Util::convertData($_POST["S_SAVENAME"]);
    $tmpquery1 = "INSERT INTO {$tableCollab["reports"]} (owner,name,projects,clients,members,priorities,status,date_due_start,date_due_end,date_complete_start,date_complete_end,created) VALUES(:owner,:name,:projects,:clients,:members,:priorities,:status,:date_due_start,:date_due_end,:date_complete_start,:date_complete_end,:created)";
    $dbParams = [];
    $dbParams["owner"] = $_SESSION["idSession"];
    $dbParams["name"] = $_POST["S_SAVENAME"];
    $dbParams["projects"] = $_POST["S_PRJSEL"];
    $dbParams["clients"] = $_POST["S_ORGSEL"];
    $dbParams["members"] = $_POST["S_ATSEL"];
    $dbParams["priorities"] = $_POST["S_PRIOSEL"];
    $dbParams["status"] = $_POST["S_STATSEL"];
    $dbParams["date_due_start"] = $_POST["S_SDATE"];
    $dbParams["date_due_end"] = $_POST["S_EDATE"];
    $dbParams["date_complete_start"] = $_POST["S_SDATE2"];
    $dbParams["date_complete_end"] = $_POST["S_EDATE2"];
    $dbParams["created"] = $dateheure;

    phpCollab\Util::newConnectSql($tmpquery1, $dbParams);
    phpCollab\Util::headerFunction("../general/home.php?msg=addReport");
}

$setTitle .= " : Report Results";
include APP_ROOT . '/themes/' . THEME . '/header.php';

$id = (isset($_GET["id"]) && $_GET["id"] != '') ? $_GET["id"] : null;
$tri = (isset($_GET["tri"]) && $_GET["tri"] != '') ? $_GET["tri"] : null;

if ($id == "" && $tri != "true") {
    $compt1 = count($_POST["S_PRJSEL"]);
    $S_pro = "";

    for ($i = 0; $i < $compt1; $i++) {
        if ($_POST["S_PRJSEL"][$i] == "ALL") {
            $S_pro = "ALL";
            break;
        }

        if ($i != $compt1 - 1) {
            $S_pro .= $_POST["S_PRJSEL"][$i] . ",";
        } else {
            $S_pro .= $_POST["S_PRJSEL"][$i];
        }
    }

    $compt2 = count($_POST["S_ATSEL"]);
    $S_mem = "";

    for ($i = 0; $i < $compt2; $i++) {
        if ($_POST["S_ATSEL"][$i] == "ALL") {
            $S_mem = "ALL";
            break;
        }

        if ($i != $compt2 - 1) {
            $S_mem .= $_POST["S_ATSEL"][$i] . ",";
        } else {
            $S_mem .= $_POST["S_ATSEL"][$i];
        }
    }

    $compt3 = count($_POST["S_STATSEL"]);
    $S_sta = "";

    for ($i = 0; $i < $compt3; $i++) {
        if ($_POST["S_STATSEL"][$i] == "ALL") {
            $S_sta = "ALL";
            break;
        }

        if ($i != $compt3 - 1) {
            $S_sta .= $_POST["S_STATSEL"][$i] . ",";
        } else {
            $S_sta .= $_POST["S_STATSEL"][$i];
        }
    }

    $compt4 = count($_POST["S_PRIOSEL"]);
    $S_pri = "";

    for ($i = 0; $i < $compt4; $i++) {
        if ($_POST["S_PRIOSEL"][$i] == "ALL") {
            $S_pri = "ALL";
            break;
        }

        if ($i != $compt4 - 1) {
            $S_pri .= $_POST["S_PRIOSEL"][$i] . ",";
        } else {
            $S_pri .= $_POST["S_PRIOSEL"][$i];
        }
    }

    $compt5 = count($_POST["S_ORGSEL"]);
    $S_org = "";

    for ($i = 0; $i < $compt5; $i++) {
        if ($_POST["S_ORGSEL"][$i] == "ALL") {
            $S_org = "ALL";
            break;
        }

        if ($i != $compt5 - 1) {
            $S_org .= $_POST["S_ORGSEL"][$i] . ",";
        } else {
            $S_org .= $_POST["S_ORGSEL"][$i];
        }
    }

    $_POST["S_ORGSEL"] = $S_org;
    $_POST["S_PRJSEL"] = $S_pro;
    $_POST["S_ATSEL"] = $S_mem;

    $_POST["S_STATSEL"] = $S_sta;
    $_POST["S_PRIOSEL"] = $S_pri;
}

if ($id != "") {
    $db = new \phpCollab\Database();
    $report_gateway = new \phpCollab\Reports\ReportsGateway($db);

    $reportDetail = $report_gateway->getReportById($id);

    $_POST["S_ORGSEL"] = $reportDetail[0]['clients'];
    $_POST["S_PRJSEL"] = $reportDetail[0]['projects'];
    $_POST["S_ATSEL"] = $reportDetail[0]['members'];
    $_POST["S_STATSEL"] = $reportDetail[0]['status'];
    $_POST["S_PRIOSEL"] = $reportDetail[0]['priorities'];
    $S_SDATE = $reportDetail[0]['date_due_start'];
    $S_EDATE = $reportDetail[0]['date_due_end'];
    $_POST["S_SDATE2"] = $reportDetail[0]['date_complete_start'];
    $S_EDATE2 = $reportDetail[0]['date_complete_end'];

    if (($S_SDATE == 0 || $S_SDATE == "") && ($S_EDATE == 0 || $S_EDATE == "")) {
        $_POST["S_DUEDATE"] = "ALL";
    }

    if (($_POST["S_SDATE2"] == 0 || $_POST["S_SDATE2"] == "") && ($S_EDATE2 == 0 || $S_EDATE2 == "")) {
        $_POST["S_COMPLETEDATE"] = "ALL";
    }
}

if (is_array($_POST["S_PRJSEL"])) {
    $_POST["S_PRJSEL"] = $_POST["S_PRJSEL"][0];
}
if (is_array($_POST["S_ORGSEL"])) {
    $_POST["S_ORGSEL"] = $_POST["S_ORGSEL"][0];
}
if (is_array($_POST["S_ATSEL"])) {
    $_POST["S_ATSEL"] = $_POST["S_ATSEL"][0];
}
if (is_array($_POST["S_STATSEL"])) {
    $_POST["S_STATSEL"] = $_POST["S_STATSEL"][0];
}
if (is_array($_POST["S_PRIOSEL"])) {
    $_POST["S_PRIOSEL"] = $_POST["S_PRIOSEL"][0];
}

if ($_POST["S_PRJSEL"] != "ALL" || $_POST["S_ORGSEL"] != "ALL" || $_POST["S_ATSEL"] != "ALL" || $_POST["S_STATSEL"] != "ALL" || $_POST["S_PRIOSEL"] != "ALL" || $_POST["S_DUEDATE"] != "ALL" || $_POST["S_COMPLETEDATE"] != "ALL") {
    $queryStart = "WHERE (";
    if ($_POST["S_PRJSEL"] != "ALL" && $_POST["S_PRJSEL"] != "") {
        $query = "tas.project IN({$_POST["S_PRJSEL"]})";
    }

    if ($_POST["S_ORGSEL"] != "ALL" && $_POST["S_ORGSEL"] != "") {
        if ($query != "") {
            $query .= ' AND org.id IN(' . $_POST["S_ORGSEL"] . ')';
        } else {
            $query .= 'org.id IN(' . $_POST["S_ORGSEL"] . ')';
        }
    }

    if ($_POST["S_ATSEL"] != "ALL" && $_POST["S_ATSEL"] != "") {
        if ($query != "") {
            $query .= " AND tas.assigned_to IN({$_POST["S_ATSEL"]})";
        } else {
            $query .= "tas.assigned_to IN({$_POST["S_ATSEL"]})";
        }
    }

    if ($_POST["S_STATSEL"] != "ALL" && $_POST["S_STATSEL"] != "") {
        if ($query != "") {
            $query .= " AND tas.status IN({$_POST["S_STATSEL"]})";
        } else {
            $query .= "tas.status IN({$_POST["S_STATSEL"]})";
        }
    }

    if ($_POST["S_PRIOSEL"] != "ALL" && $_POST["S_PRIOSEL"] != "") {
        if ($query != "") {
            $query .= " AND tas.priority IN({$_POST["S_PRIOSEL"]})";
        } else {
            $query .= "tas.priority IN({$_POST["S_PRIOSEL"]})";
        }
    }

    if ($_POST["S_DUEDATE"] != "ALL" && $_POST["S_SDATE"] != "") {
        if ($query != "") {
            $query .= " AND tas.due_date >= '{$_POST["S_SDATE"]}'";
        } else {
            $query .= "tas.due_date >= '{$_POST["S_SDATE"]}'";
        }
    }

    if ($_POST["S_DUEDATE"] != "ALL" && $_POST["S_EDATE"] != "") {
        if ($query != "") {
            $query .= " AND tas.due_date <= '{$_POST["S_EDATE"]}'";
        } else {
            $query .= "tas.due_date <= '{$_POST["S_EDATE"]}'";
        }
    }
    if ($_POST["S_COMPLETEDATE"] != "ALL" && $_POST["S_SDATE2"] != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date >= '{$_POST["S_SDATE2"]}'";
        } else {
            $query .= "tas.complete_date >= '{$_POST["S_SDATE2"]}'";
        }
    }

    if ($_POST["S_COMPLETEDATE"] != "ALL" && $_POST["S_EDATE2"] != "") {
        if ($query != "") {
            $query .= " AND tas.complete_date <= '{$_POST["S_EDATE2"]}'";
        } else {
            $query .= "tas.complete_date <= '{$_POST["S_EDATE2"]}'";
        }
    }

    if ($query != "") {
        $query .= ")";
    }
}

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?", $strings["reports"], "in"));

if ($id != "") {
    $blockPage->itemBreadcrumbs($reportDetail->rep_name[0]);
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
    $sortingUser->sor_report_tasks[0],
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
    $tmpquery = "LEFT OUTER JOIN " . $tableCollab["teams"] . " teams ON teams.project = pro.id ";
    $tmpquery .= "WHERE pro.status IN(0,2,3) AND teams.member = '{$_SESSION["idSession"]}' ORDER BY pro.id";

    $listProjectsTasks = new phpCollab\Request();
    $listProjectsTasks->openProjects($tmpquery);
    $comptListProjectsTasks = count($listProjectsTasks->pro_id);

    if ($comptListProjectsTasks != "0") {
        for ($i = 0; $i < $comptListProjectsTasks; $i++) {
            $filterTasks .= $listProjectsTasks->pro_id[$i];

            if ($comptListProjectsTasks - 1 != $i) {
                $filterTasks .= ",";
            }
        }

        if (isset($filterTasks)) {
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
    $tmpquery = "$queryStart $query ORDER BY {$block1->sortingValue} ";
}

if ($listTasks->tas_id != "") {
    $taskIds = implode(',', $listTasks->tas_id);
    $tmpquery = "WHERE task in ('{$taskIds}')";
} else {
    $tmpquery = 'WHERE task in ("")';
}

$listSubTasks = new phpCollab\Request();
$listSubTasks->openSubtasks($tmpquery);
$comptListSubTasks = count($listSubTasks->subtas_id);
$totalTasks = $comptListTasks + $comptListSubTasks;
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
$block1->openForm("../reports/resultsreport.php?&tri=true&id=$id#" . $block1->form . "Anchor");

$block1->heading($strings["report_results"]);

if ($comptListTasks != "0") {
    /**
     * you cannot export or delete a not saved report
     * $block1->openPaletteIcon();
     * $block1->paletteIcon(0,"export",$strings["export"]);
     * $block1->paletteIcon(1,"remove",$strings["delete"]);
     * $block1->closePaletteIcon();
     */

    $block1->openResults('false');

    $block1->labels($labels = [0 => $strings["task"], 1 => $strings["priority"], 2 => $strings["status"], 3 => $strings["due_date"], 4 => $strings["complete_date"], 5 => $strings["assigned_to"], 6 => $strings["project"], 7 => $strings["published"]], "true");

    for ($i = 0; $i < $comptListTasks; $i++) {
        $idStatus = $listTasks->tas_status[$i];
        $idPriority = $listTasks->tas_priority[$i];
        $idPublish = $listTasks->tas_published[$i];

        $block1->openRow();
        $block1->cellRow('');
        $block1->cellRow($blockPage->buildLink("../tasks/viewtask.php?id=" . $listTasks->tas_id[$i], $listTasks->tas_name[$i], "in"));
        $block1->cellRow('<i style="background-color: yellow;"></i><img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $GLOBALS["priority"][$idPriority]);
        $block1->cellRow($GLOBALS["status"][$idStatus]);

        if ($listTasks->tas_due_date[$i] <= $GLOBALS["date"] && $listTasks->tas_completion[$i] != "10") {
            $block1->cellRow("<b>" . $listTasks->tas_due_date[$i] . "</b>");
        } else {
            $block1->cellRow($listTasks->tas_due_date[$i]);
        }

        if ($listTasks->tas_start_date[$i] != "--" && $listTasks->tas_due_date[$i] != "--") {
            $gantt = "true";
        }

        $block1->cellRow($listTasks->tas_complete_date[$i]);

        if ($listTasks->tas_assigned_to[$i] == "0") {
            $block1->cellRow($strings["unassigned"]);
        } else {
            $block1->cellRow($blockPage->buildLink($listTasks->tas_mem_email_work[$i], $listTasks->tas_mem_login[$i], "mail"));
        }

        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $listTasks->tas_project[$i], $listTasks->tas_pro_name[$i], "in"));

        if ($sitePublish == "true") {
            $block1->cellRow($GLOBALS["statusPublish"][$idPublish]);
        }

        $block1->closeRow();
        // begin if subtask
        $listSubTasks = $tasks->getSubtasksByParentTaskId($listTasks->tas_id[$i]);

        if ($listSubTasks) {
            foreach ($listSubTasks as $subTask) {
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
            <input type="hidden" name="S_ORGSEL[]" value="{$_POST["S_ORGSEL"]}" />
			<input type="hidden" name="S_PRJSEL[]" value="{$_POST["S_PRJSEL"]}" />
			<input type="hidden" name="S_ATSEL[]" value="{$_POST["S_ATSEL"]}" />
			<input type="hidden" name="S_STATSEL[]" value="{$_POST["S_STATSEL"]}" />
			<input type="hidden" name="S_PRIOSEL[]" value="{$_POST["S_PRIOSEL"]}" />
			<input type="hidden" name="S_COMPLETEDATE" value="{$_POST["S_COMPLETEDATE"]}" />
			<input type="hidden" name="S_DUEDATE" value="{$_POST["S_DUEDATE"]}" />
HIDDEN;

    $block1->closeFormResults();

    /** you cannot export/delete a not-saved report
     * $block1->openPaletteScript();
     * $block1->paletteScript(0,"export","../reports/exportreport.php?id=$id","true,true,true",$strings["export"]);
     * $block1->paletteScript(1,"remove","../reports/deletereports.php?id=$id","true,true,true",$strings["delete"]);
     * $block1->closePaletteScript($comptListTasks,$listTasks->tas_id);
     */
}

$block2 = new phpCollab\Block();

$block2->form = "save_report";
$block2->openForm("../reports/resultsreport.php?action=add");

if (isset($error) && $error != "") {
    $block2->headingError($strings["errors"]);
    $block2->contentError($error);
}

$block2->openContent();
$block2->contentTitle($strings["report_save"]);

echo <<< TR
        <tr class="odd">
			<td valign="top" class="leftvalue">{$strings["report_name"]} :</td>
			<td><input type="text" name="S_SAVENAME" value="" style="width: 200px;" maxlength="64"></td>
		</tr>
		<tr class="odd">
			<td valign="top" class="leftvalue">&nbsp;</td>
			<td><input type="submit" name="{$strings["save"]}" value="{$strings["save"]}" />
			<input type="hidden" name="S_ORGSEL" value="{$_POST["S_ORGSEL"]}" />
			<input type="hidden" name="S_PRJSEL" value="{$_POST["S_PRJSEL"]}" />
			<input type="hidden" name="S_ATSEL" value="{$_POST["S_ATSEL"]}" />
			<input type="hidden" name="S_STATSEL" value="{$_POST["S_STATSEL"]}" />
			<input type="hidden" name="S_PRIOSEL" value="{$_POST["S_PRIOSEL"]}" />
			<input type="hidden" name="S_SDATE" value="{$_POST["S_SDATE"]}" />
			<input type="hidden" name="S_EDATE" value="{$_POST["S_EDATE"]}" />
			<input type="hidden" name="S_SDATE2" value="{$_POST["S_SDATE2"]}" />
			<input type="hidden" name="S_EDATE2" value="{$_POST["S_EDATE2"]}" />
			</td>
		</tr>
TR;

$block2->closeContent();
$block2->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
