<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/viewsubtask.php
** Authors: Ceam / Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewsubtask.php
**
** DESC: Screen: view sub task information
**
** HISTORY:
**	05/11/2004	-	fixed 1059973 
**	19/05/2005	-	fixed and &amp; in link
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

if ($action == "publish") {

    if ($addToSite == "true") {
        $tmpquery1 = "UPDATE " . $tableCollab["subtasks"] . " SET published='0' WHERE id = '$id'";
        Util::connectSql("$tmpquery1");
        $msg = "addToSite";
    }

    if ($removeToSite == "true") {
        $tmpquery1 = "UPDATE " . $tableCollab["subtasks"] . " SET published='1' WHERE id = '$id'";
        Util::connectSql("$tmpquery1");
        $msg = "removeToSite";
    }

    if ($addToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $tmpquery1 = "UPDATE " . $tableCollab["files"] . " SET published='0' WHERE id IN($id) OR vc_parent IN ($id)";
        Util::connectSql("$tmpquery1");
        $msg = "addToSite";
        $id = $task;
    }

    if ($removeToSiteFile == "true") {
        $id = str_replace("**", ",", $id);
        $tmpquery1 = "UPDATE " . $tableCollab["files"] . " SET published='1' WHERE id IN($id) OR vc_parent IN ($id)";
        Util::connectSql("$tmpquery1");
        $msg = "removeToSite";
        $id = $task;
    }
}

include '../themes/' . THEME . '/header.php';

$tmpquery = "WHERE subtas.id = '$id'";
$subtaskDetail = new Request();
$subtaskDetail->openSubtasks($tmpquery);

$tmpquery = "WHERE tas.id = '$task'";
$taskDetail = new Request();
$taskDetail->openTasks($tmpquery);

$tmpquery = "WHERE pro.id = '" . $taskDetail->tas_project[0] . "'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_enable_phase[0] != "0") {
    $tPhase = $taskDetail->tas_parent_phase[0];
    if (!$tPhase) {
        $tPhase = '0';
    }
    $tmpquery = "WHERE pha.project_id = '" . $taskDetail->tas_project[0] . "' AND pha.order_num = '$tPhase'";
    $targetPhase = new Request();
    $targetPhase->openPhases($tmpquery);
}

$teamMember = "false";
$tmpquery = "WHERE tea.project = '" . $taskDetail->tas_project[0] . "' AND tea.member = '$idSession'";
$memberTest = new Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
if ($comptMemberTest == "0") {
    $teamMember = "false";
} else {
    $teamMember = "true";
}

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));

if ($projectDetail->pro_phase_set[0] != "0") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=" . $projectDetail->pro_id[0], $strings["phases"], in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase->pha_id[0], $targetPhase->pha_name[0], in));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=" . $projectDetail->pro_id[0], $strings["tasks"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail->tas_id[0], $taskDetail->tas_name[0], in));
$blockPage->itemBreadcrumbs($subtaskDetail->subtas_name[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->form = "tdD";
$block1->openForm("../subtasks/viewsubtask.php?" . session_name() . "=" . session_id() . "#" . $block1->form . "Anchor");

$block1->headingToggle($strings["subtask"] . " : " . $subtaskDetail->subtas_name[0]);

if ($teamMember == "true" || $profilSession == "5") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
//$block1->paletteIcon(1,"copy",$strings["copy"]);
//$block1->paletteIcon(2,"export",$strings["export"]);
    if ($sitePublish == "true") {
        $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
    $block1->paletteIcon(5, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

if ($projectDetail->pro_org_id[0] == "1") {
    $projectDetail->pro_org_name[0] = $strings["none"];
}

$block1->openContent();
$block1->contentTitle($strings["info"]);


$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));

//Display task's phase
if ($projectDetail->pro_phase_set[0] != "0") {
    $block1->contentRow($strings["phase"], $blockPage->buildLink("../phases/viewphase.php?id=" . $targetPhase->pha_id[0], $targetPhase->pha_name[0], in));
}

$block1->contentRow($strings["task"], $blockPage->buildLink("../tasks/viewtask.php?id=" . $taskDetail->tas_id[0], $taskDetail->tas_name[0], in));
$block1->contentRow($strings["organization"], $projectDetail->pro_org_name[0]);
$block1->contentRow($strings["created"], Util::createDate($subtaskDetail->subtas_created[0], $timezoneSession));
$block1->contentRow($strings["assigned"], Util::createDate($subtaskDetail->subtas_assigned[0], $timezoneSession));
$block1->contentRow($strings["modified"], Util::createDate($subtaskDetail->subtas_modified[0], $timezoneSession));

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["name"], $subtaskDetail->subtas_name[0]);

$block1->contentRow($strings["description"], nl2br($subtaskDetail->subtas_description[0]));
if ($subtaskDetail->subtas_assigned_to[0] == "0") {
    $block1->contentRow($strings["assigned_to"], $strings["unassigned"]);
} else {
    $block1->contentRow($strings["assigned_to"], $blockPage->buildLink("../users/viewuser.php?id=" . $subtaskDetail->subtas_mem_id[0], $subtaskDetail->subtas_mem_name[0], in) . " (" . $blockPage->buildLink($subtaskDetail->subtas_mem_email_work[0], $subtaskDetail->subtas_mem_login[0], mail) . ")");
}
$idStatus = $subtaskDetail->subtas_status[0];
$idPriority = $subtaskDetail->subtas_priority[0];
$idPublish = $subtaskDetail->subtas_published[0];
$complValue = ($subtaskDetail->subtas_completion[0] > 0) ? $subtaskDetail->subtas_completion[0] . "0 %" : $subtaskDetail->subtas_completion[0] . " %";
$block1->contentRow($strings["status"], $status[$idStatus]);
$block1->contentRow($strings["completion"], $complValue);
$block1->contentRow($strings["priority"], "<img src=\"../themes/" . THEME . "/images/gfx_priority/" . $idPriority . ".gif\" alt=\"\"> " . $priority[$idPriority]);
$block1->contentRow($strings["start_date"], $subtaskDetail->subtas_start_date[0]);
if ($subtaskDetail->subtas_due_date[0] <= $date && $subtaskDetail->subtas_completion[0] != "10") {
    $block1->contentRow($strings["due_date"], "<b>" . $subtaskDetail->subtas_due_date[0] . "</b>");
} else {
    $block1->contentRow($strings["due_date"], $subtaskDetail->subtas_due_date[0]);
}
if ($subtaskDetail->subtas_complete_date[0] != "" && $subtaskDetail->subtas_complete_date[0] != "--" && $subtaskDetail->subtas_due_date[0] != "--") {
    $diff = Util::diffDate($subtaskDetail->subtas_complete_date[0], $subtaskDetail->subtas_due_date[0]);
    if ($diff > 0) {
        $diff = "<b>+$diff</b>";
    }
    $block1->contentRow($strings["complete_date"], $subtaskDetail->subtas_complete_date[0]);
    $block1->contentRow($strings["scope_creep"] . $blockPage->printHelp("task_scope_creep"), "$diff " . $strings["days"]);
}
$block1->contentRow($strings["estimated_time"], $subtaskDetail->subtas_estimated_time[0] . " " . $strings["hours"]);
$block1->contentRow($strings["actual_time"], $subtaskDetail->subtas_actual_time[0] . " " . $strings["hours"]);
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

$block1->contentRow($strings["comments"], nl2br($subtaskDetail->subtas_comments[0]));

$block1->contentTitle($strings["updates_subtask"]);
$tmpquery = "WHERE upd.type='2' AND upd.item = '$id' ORDER BY upd.created DESC";
$listUpdates = new Request();
$listUpdates->openUpdates($tmpquery);
$comptListUpdates = count($listUpdates->upd_id);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>";
if ($comptListUpdates != "0") {
    $j = 1;
    for ($i = 0; $i < $comptListUpdates; $i++) {
        if (preg_match('|\[status:([0-9])\]|', $listUpdates->upd_comments[$i])) {
            preg_match('|\[status:([0-9])\]|i', $listUpdates->upd_comments[$i], $matches);
            $listUpdates->upd_comments[$i] = preg_replace('|\[status:([0-9])\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
            $listUpdates->upd_comments[$i] .= $strings["status"] . ' ' . $status[$matches[1]];
        }
        if (preg_match('|\[priority:([0-9])\]|', $listUpdates->upd_comments[$i])) {
            preg_match('|\[priority:([0-9])\]|i', $listUpdates->upd_comments[$i], $matches);
            $listUpdates->upd_comments[$i] = preg_replace('|\[priority:([0-9])\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
            $listUpdates->upd_comments[$i] .= $strings["priority"] . ' ' . $priority[$matches[1]];
        }
        if (preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', $listUpdates->upd_comments[$i])) {
            preg_match('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|i', $listUpdates->upd_comments[$i], $matches);
            $listUpdates->upd_comments[$i] = preg_replace('|\[datedue:([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})\]|', '', $listUpdates->upd_comments[$i] . '<br/>');
            $listUpdates->upd_comments[$i] .= $strings["due_date"] . ' ' . $matches[1];
        }

        $abbrev = stripslashes(substr($listUpdates->upd_comments[$i], 0, 100));
        echo "<b>" . $j . ".</b> <i>" . Util::createDate($listUpdates->upd_created[$i], $timezoneSession) . "</i> $abbrev";
        if (100 < strlen($listUpdates->upd_comments[$i])) {
            echo "...<br/>";
        } else {
            echo "<br/>";
        }
        $j++;
    }
    echo "<br/>" . $blockPage->buildLink("../subtasks/historysubtask.php?type=2&item=$id", $strings["show_details"], in);
} else {
    echo $strings["no_items"];
}

echo "</td></tr>";

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

if ($teamMember == "true" || $profilSession == "5") {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../subtasks/deletesubtasks.php?task=$task&id=" . $subtaskDetail->subtas_id[0] . "", "true,true,false", $strings["delete"]);
//$block1->paletteScript(1,"copy","../subtasks/editsubtask.php?task=$task&id=$id&docopy=true","true,true,false",$strings["copy"]);
//$block1->paletteScript(2,"export","export.php?","true,true,false",$strings["export"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite", "../subtasks/viewsubtask.php?addToSite=true&task=$task&id=" . $subtaskDetail->subtas_id[0] . "&action=publish", "true,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite", "../subtasks/viewsubtask.php?removeToSite=true&task=$task&id=" . $subtaskDetail->subtas_id[0] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    }
    $block1->paletteScript(5, "edit", "../subtasks/editsubtask.php?task=$task&id=$id&docopy=false", "true,true,false", $strings["edit"]);
    $block1->closePaletteScript("", "");
}

/*

if ($fileManagement == "true") {

$block2 = new Block();

$block2->form = "tdC";
$block2->openForm("../subtasks/viewsubtask.php?".session_name()."=".session_id()."&id=$id#".$block2->form."Anchor");

$block2->headingToggle($strings["linked_content"]);

$block2->openPaletteIcon();
if ($teamMember == "true" || $profilSession == "5") {
$block2->paletteIcon(0,"add",$strings["add"]);
$block2->paletteIcon(1,"remove",$strings["delete"]);
if ($sitePublish == "true") {
$block2->paletteIcon(2,"add_projectsite",$strings["add_project_site"]);
$block2->paletteIcon(3,"remove_projectsite",$strings["remove_project_site"]);
}
}
$block2->paletteIcon(4,"info",$strings["view"]);
if ($teamMember == "true") {
$block2->paletteIcon(5,"edit",$strings["edit"]);
}
$block2->closePaletteIcon();

$block2->sorting("files",$sortingUser->sor_files[0],"fil.name ASC",$sortingFields = array(0=>"fil.extension",1=>"fil.name",2=>"fil.date",3=>"fil.status",4=>"fil.published"));

$tmpquery = "WHERE fil.task = '$id' AND fil.vc_parent = '0' ORDER BY $block2->sortingValue";
$listFiles = new Request();
$listFiles->openFiles($tmpquery);
$comptListFiles = count($listFiles->fil_id);

if ($comptListFiles != "0") {
	$block2->openResults();

	$block2->labels($labels = array(0=>$strings["type"],1=>$strings["name"],2=>$strings["date"],3=>$strings["approval_tracking"],4=>$strings["published"]),"true");

for ($i=0;$i<$comptListFiles;$i++) {
$idStatus = $listFiles->fil_status[$i];
$idPublish = $listFiles->fil_published[$i];
$type = FileHandler::fileInfoType( $listFiles->fil_extension[$i]);
if (file_exists("../files/".$listFiles->fil_project[$i]."/".$listFiles->fil_task[$i]."/".$listFiles->fil_name[$i])) {
$existFile = "true";
}
$block2->openRow();
$block2->checkboxRow($listFiles->fil_id[$i]);
if ($existFile == "true") {
$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$type,icone));
} else {
$block2->cellRow("&nbsp;");
}
if ($existFile == "true") {
$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$listFiles->fil_name[$i],in));
} else {
$block2->cellRow($strings["missing_file"]." (".$listFiles->fil_name[$i].")");
}
$block2->cellRow($listFiles->fil_date[$i]);
$block2->cellRow($blockPage->buildLink("../linkedcontent/viewfile.php?id=".$listFiles->fil_id[$i],$statusFile[$idStatus],in));
if ($sitePublish == "true") {
$block2->cellRow($statusPublish[$idPublish]);
}
$block2->closeRow();
}
$block2->closeResults();
} else {
$block2->noresults();
}
$block2->closeToggle();
$block2->closeFormResults();

$block2->openPaletteScript();
if ($teamMember == "true" || $profilSession == "5") {
$block2->paletteScript(0,"add","../linkedcontent/addfile.php?project=".$taskDetail->tas_project[0]."&task=$id","true,true,true",$strings["add"]);
$block2->paletteScript(1,"remove","../linkedcontent/deletefiles.php?project=".$projectDetail->pro_id[0]."&task=".$taskDetail->tas_id[0]."","false,true,true",$strings["delete"]);
if ($sitePublish == "true") {
$block2->paletteScript(2,"add_projectsite","../subtasks/viewsubtask.php?addToSiteFile=true&task=".$taskDetail->tas_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
$block2->paletteScript(3,"remove_projectsite","../subtasks/viewsubtask.php?removeToSiteFile=true&task=".$taskDetail->tas_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
}
}
$block2->paletteScript(4,"info","../linkedcontent/viewfile.php?","false,true,false",$strings["view"]);
if ($teamMember == "true") {
$block2->paletteScript(5,"edit","../linkedcontent/viewfile.php?".session_name()."=".session_id()."&edit=true","false,true,false",$strings["edit"]);
}
$block2->closePaletteScript($comptListFiles,$listFiles->fil_id);
}
*/

$block3 = new Block();

$block3->form = "ahT";
$block3->openForm("../subtasks/viewsubtask.php?" . session_name() . "=" . session_id() . "&id=$id&task=$task#" . $block3->form . "Anchor");

$block3->headingToggle($strings["assignment_history"]);

$block3->sorting("assignment", $sortingUser->sor_assignment[0], "ass.assigned DESC", $sortingFields = array(0 => "ass.comments", 1 => "mem1.login", 2 => "mem2.login", 3 => "ass.assigned"));

$tmpquery = "WHERE ass.subtask = '$id' ORDER BY $block3->sortingValue";
$listAssign = new Request();
$listAssign->openAssignments($tmpquery);


$comptListAssign = count($listAssign->ass_id);

$block3->openResults($checkbox = "false");

$block3->labels($labels = array(0 => $strings["comment"], 1 => $strings["assigned_by"], 2 => $strings["to"], 3 => $strings["assigned_on"]), "false");

for ($i = 0; $i < $comptListAssign; $i++) {
    $block3->openRow();
    $block3->checkboxRow($listAssign->ass_id[$i], $checkbox = "false");
    if ($listAssign->ass_comments[$i] != "") {
        $block3->cellRow($listAssign->ass_comments[$i]);
    } else if ($listAssign->ass_assigned_to[$i] == "0") {
        $block3->cellRow($strings["task_unassigned"]);
    } else {
        $block3->cellRow($strings["task_assigned"] . " " . $listAssign->ass_mem2_name[$i] . " (" . $listAssign->ass_mem2_login[$i] . ")");
    }
    $block3->cellRow($blockPage->buildLink($listAssign->ass_mem1_email_work[$i], $listAssign->ass_mem1_login[$i], mail));
    if ($listAssign->ass_assigned_to[$i] == "0") {
        $block3->cellRow($strings["unassigned"]);
    } else {
        $block3->cellRow($blockPage->buildLink($listAssign->ass_mem2_email_work[$i], $listAssign->ass_mem2_login[$i], mail));
    }
    $block3->cellRow(Util::createDate($listAssign->ass_assigned[$i], $timezoneSession));
    $block3->closeRow();
}
$block3->closeResults();

$block3->closeToggle();
$block3->closeFormResults();

include '../themes/' . THEME . '/footer.php';
?>