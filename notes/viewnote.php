<?php
/*
** Application name: phpCollab
** Last Edit page: 03/06/2005
** Path by root: ../notes/viewnote.php
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
** FILE: viewfile.php
**
** DESC: Screen: adding file to linked content
**
** HISTORY:
**  03/06/2005  -	added file comment
**	03/06/2005	-	moved "description text" to the bottom of page
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use phpCollab\Notes\Notes;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';


$action = $request->query->get('action');
$project = $request->query->get('project');
$id = $request->query->get('id');
$addToSite = $request->query->get('addToSite');
$removeToSite = $request->query->get('removeToSite');
$strings = $GLOBALS["strings"];
$idSession = $_SESSION["idSession"];

$notes = new Notes();
$projects = new Projects();
$teams = new Teams();


if ($action == "publish") {
    if ($addToSite == "true") {
        $notes->publishToSite($id);
        $msg = "addToSite";
    }
    if ($removeToSite == "true") {
        $notes->unPublishFromSite($id);
        $msg = "removeToSite";
    }
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$noteDetail = $notes->getNoteById($id);
$projectDetail = $projects->getProjectById($noteDetail["note_project"]);

$teamMember = "false";
$teamMember = $teams->isTeamMember($noteDetail["note_project"], $idSession);

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../notes/listnotes.php?project=" . $projectDetail["pro_id"], $strings["notes"], "in"));
$blockPage->itemBreadcrumbs($noteDetail["note_subject"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();
$block1->form = "tdD";
$block1->openForm("../notes/viewnote.php#" . $block1->form . "Anchor");
$block1->heading($strings["note"] . " : " . $noteDetail["note_subject"]);

if ($teamMember == "true" && $idSession == $noteDetail["note_owner"]) {
    $block1->openPaletteIcon();
    $block1->paletteIcon(0, "remove", $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteIcon(2, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(3, "remove_projectsite", $strings["remove_project_site"]);
    }
    $block1->paletteIcon(4, "edit", $strings["edit"]);
    $block1->closePaletteIcon();
}

if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));

if ($noteDetail["note_topic"] != "") {
    $block1->contentRow($strings["topic"], $topicNote[$noteDetail["note_topic"]]);
}

$block1->contentRow($strings["subject"], $noteDetail["note_subject"]);
$block1->contentRow($strings["date"], $noteDetail["note_date"]);
$block1->contentRow($strings["owner"], $blockPage->buildLink($noteDetail["note_mem_email_work"], $noteDetail["note_mem_login"], "mail"));
$block1->contentRow($strings["description"], nl2br($noteDetail["note_description"]));

$idPublish = $noteDetail["note_published"];
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$idPublish]);
}

$block1->closeContent();
$block1->closeForm();

if ($teamMember == "true" && $idSession == $noteDetail["note_owner"]) {
    $block1->openPaletteScript();
    $block1->paletteScript(0, "remove", "../notes/deletenotes.php?project=" . $noteDetail["note_project"] . "&id=" . $noteDetail["note_id"] . "", "true,true,false", $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(2, "add_projectsite", "../notes/viewnote.php?addToSite=true&id=" . $noteDetail["note_id"] . "&action=publish", "true,true,true", $strings["add_project_site"]);
        $block1->paletteScript(3, "remove_projectsite", "../notes/viewnote.php?removeToSite=true&id=" . $noteDetail["note_id"] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
    }
    $block1->paletteScript(4, "edit", "../notes/editnote.php?project=" . $noteDetail["note_project"] . "&id=" . $noteDetail["note_id"] . "", "true,true,false", $strings["edit"]);
    $block1->closePaletteScript("", []);
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
