<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../notes/deletenotes.php

$checkSession = "true";
include_once '../includes/library.php';

$action = $_GET["action"];
$project = $_GET["project"];
$id = $_GET["id"];
$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];

$notes = new \phpCollab\Notes\Notes();

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $notes->deleteNotes($id);
    phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($strings["delete_note"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}


$block1 = new phpCollab\Block();
$block1->form = "saP";
$block1->openForm("../notes/deletenotes.php?project=$project&action=delete&id=$id");

$block1->heading($strings["delete_note"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listNotes = $notes->getNotesById($id);

foreach ($listNotes as $note) {
    $block1->contentRow("#" . $note["note_id"], $note["note_subject"]);
}

$block1->contentRow("", '<input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
