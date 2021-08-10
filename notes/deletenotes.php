<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../notes/deletenotes.php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$action = $request->query->get('action');
$project = $request->query->get('project');
$id = $request->query->get('id');
$strings = $GLOBALS["strings"];

try {
    $notes = $container->getNotesLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($action == "delete") {
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                $id = str_replace("**", ",", $id);
                $notes->deleteNotes($id);
                phpCollab\Util::headerFunction("../projects/viewproject.php?id=$project&msg=delete");
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Notes: Delete' => $request->query->get("id"),
                '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
                '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
        }
    }
}

include APP_ROOT . '/views/layout/header.php';

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
$block1->openForm("../notes/deletenotes.php?project=$project&action=delete&id=$id", null, $csrfHandler);

$block1->heading($strings["delete_note"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listNotes = $notes->getNotesById($id);

foreach ($listNotes as $note) {
    $block1->contentRow("#" . $note["note_id"], $note["note_subject"]);
}

$block1->contentRow("",
    '<input type="submit" name="delete" value="' . $strings["delete"] . '"> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
