<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../phases/listphases.php

use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$projects = new Projects();
$teams = new Teams();
$phases = new Phases();
$tasks = new Tasks();

include APP_ROOT . '/themes/' . THEME . '/header.php';

$projectDetail = $projects->getProjectById($id);

$teamMember = "false";

$teamMember = $teams->isTeamMember($id, $session->get("idSession"));

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["phases"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

if ($teamMember == "true" || $session->get("profilSession") == "5") {
    $block7 = new phpCollab\Block();
    $block7->form = "wbSe";
    $block7->openForm("../phases/listphases.php?id=$id&#".$block7->form."Anchor");
    $block7->headingToggle($strings["phases"]);
    $block7->openPaletteIcon();

    $block7->paletteIcon(0, "info", $strings["view"]);

    if ($teamMember == "true" || $session->get("profilSession") == "5") {
        if ($session->get("idSession") == $projectDetail["pro_owner"] || $session->get("profilSession") == "0" || $session->get("profilSession") == "5") {
            $block7->paletteIcon(1, "edit", $strings["edit"]);
        }
    }
    $block7->closePaletteIcon();

    $block7->sorting("phases", $sortingUser["phases"], "pha.order_num ASC", $sortingFields = array(0=>"pha.order_num",1=>"pha.name",2=>"none",3=>"none",4=>"pha.status",5=>"pha.date_start",6=>"pha.date_end"));

    $listPhases = $phases->getPhasesByProjectId($id, $block7->sortingValue);

    if ($listPhases) {
        $block7->openResults();
        $block7->labels($labels = array(0=>$strings["order"],1=>$strings["name"],2=>$strings["total_tasks"],3=>$strings["uncomplete_tasks"],4=>$strings["status"],5=>$strings["date_start"],6=>$strings["date_end"]), "false");

        $listPhaseTasks = $tasks->getTasksByProjectId($id);

        foreach ($listPhases as $phase) {
            $comptlistTasksRow = 0;
            $comptUncompleteTasks = 0;
            foreach ($listPhaseTasks as $task) {
                if ($phase["pha_order_num"] == $task["tas_parent_phase"]) {
                    $comptlistTasksRow++;
                    if ($task["tas_status"] == "2" || $task["tas_status"] == "3" || $task["tas_status"] == "4") {
                        $comptUncompleteTasks++;
                    }
                }
            }

            $block7->openRow();
            $block7->checkboxRow($phase["pha_id"]);
            $block7->cellRow($phase["pha_order_num"]);
            $block7->cellRow($blockPage->buildLink("../phases/viewphase.php?id=".$phase["pha_id"], $phase["pha_name"], "in"));
            $block7->cellRow($comptlistTasksRow);
            $block7->cellRow($comptUncompleteTasks);
            $block7->cellRow($phaseStatus[$phase["pha_status"]]);
            $block7->cellRow(Util::isBlank($phase["pha_date_start"]));
            $block7->cellRow(Util::isBlank($phase["pha_date_end"]));
            $block7->closeRow();
        }
        $block7->closeResults();
    } else {
        $block7->noresults();
    }
    $block7->closeToggle();
    $block7->closeFormResults();

    $block7->openPaletteScript();
    $block7->paletteScript(0, "info", "../phases/viewphase.php?", "false,true,true", $strings["view"]);
    if ($teamMember == "true" || $session->get("profilSession") == "5") {
        if ($session->get("idSession") == $projectDetail["pro_owner"] || $session->get("profilSession") == "0" || $session->get("profilSession") == "5") {
            $block7->paletteScript(1, "edit", "../phases/editphase.php?", "false,true,true", $strings["edit"]);
        }
    }
    $block7->closePaletteScript(count($listPhases), array_column($listPhases, 'pha_id'));
}

include APP_ROOT . '/themes/'.THEME.'/footer.php';
