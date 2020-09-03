<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../notes/listnotes.php

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$action = $request->query->get('action');
$project = $request->query->get('project');
$id = $request->query->get('id');
$addToSite = $request->query->get('addToSite');
$removeToSite = $request->query->get('removeToSite');
$strings = $GLOBALS["strings"];

$notes = $container->getNotesLoader();

if ($action == "publish") {
    $multi = strstr($id, "**");
    $id = str_replace("**", ",", $id);

    if ($addToSite == "true") {
        $notes->publishToSite($id);
        $msg = "addToSite";
        $id = $project;
    }

    if ($removeToSite == "true") {
        $notes->unPublishFromSite($id);
        $msg = "removeToSite";
        $id = $project;
    }
}

include '../themes/' . THEME . '/header.php';

$projects = $container->getProjectsLoader();
$projectDetail = $projects->getProjectById($project);

$teams = $container->getTeams();
$teamMember = $teams->isTeamMember($project, $session->get("id"));

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"],
    $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["notes"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "saJ";
$block1->openForm("../notes/listnotes.php?&project=$project#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["notes"]);

$block1->openPaletteIcon();
if ($teamMember == "true") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
        $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
    }
}
$block1->paletteIcon(5, "info", $strings["view"]);
if ($teamMember == "true") {
    $block1->paletteIcon(6, "edit", $strings["edit"]);
}
$block1->closePaletteIcon();

$comptTopic = count($topicNote);

if ($comptTopic != "0") {
    $block1->sorting("notes", $sortingUser["notes"], "note.date DESC", $sortingFields = [
        0 => "note.subject",
        1 => "note.topic",
        2 => "note.date",
        3 => "mem.login",
        4 => "note.published"
    ]);
} else {
    $block1->sorting("notes", $sortingUser["notes"], "note.date DESC",
        $sortingFields = [0 => "note.subject", 1 => "note.date", 2 => "mem.login", 3 => "note.published"]);
}

$listNotes = $notes->getNoteByProject($project, $block1->sortingValue);

if ($listNotes) {
    $block1->openResults();
    $comptTopic = count($topicNote);

    if ($comptTopic != "0") {
        $block1->labels($labels = [
            0 => $strings["subject"],
            1 => $strings["topic"],
            2 => $strings["date"],
            3 => $strings["owner"],
            4 => $strings["published"]
        ], "true");
    } else {
        $block1->labels($labels = [
            0 => $strings["subject"],
            1 => $strings["date"],
            2 => $strings["owner"],
            3 => $strings["published"]
        ], "true");
    }
    foreach ($listNotes as $note) {
        $idPublish = $note["note_published"];
        $block1->openRow();
        $block1->checkboxRow($note["note_id"]);
        $block1->cellRow($blockPage->buildLink("../notes/viewnote.php?id=" . $note["note_id"], $note["note_subject"],
            "in"));
        if ($comptTopic != "0") {
            $block1->cellRow($topicNote[$note["note_topic"]]);
        }
        $block1->cellRow($note["note_date"]);
        $block1->cellRow($blockPage->buildLink($note["note_mem_email_work"], $note["note_mem_login"], "mail"));
        if ($sitePublish == "true") {
            $block1->cellRow($statusPublish[$idPublish]);
        }
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($teamMember == "true") {
    $block1->paletteScript(0, "add", "../notes/editnote.php?project=$project", "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../notes/deletenotes.php?project=$project", "false,true,true",
        $strings["delete"]);
    if ($sitePublish == "true") {
        $block1->paletteScript(3, "add_projectsite",
            "../notes/listnotes.php?addToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish",
            "false,true,true", $strings["add_project_site"]);
        $block1->paletteScript(4, "remove_projectsite",
            "../notes/listnotes.php?removeToSite=true&project=" . $projectDetail["pro_id"] . "&action=publish",
            "false,true,true", $strings["remove_project_site"]);
    }
}
$block1->paletteScript(5, "info", "../notes/viewnote.php?", "false,true,false", $strings["view"]);
if ($teamMember == "true") {
    $block1->paletteScript(6, "edit", "../notes/editnote.php?project=$project", "false,true,false", $strings["edit"]);
}
$block1->closePaletteScript(count($listNotes), array_column($listNotes, 'note_id'));

include '../themes/' . THEME . '/footer.php';
