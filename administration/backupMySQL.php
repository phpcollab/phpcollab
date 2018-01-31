<?php

$checkSession = "true";
include_once '../includes/library.php';

if ($_POST) {

    if ($_POST["tables"]) {

        $dumpSettings = [
            'include-tables' => $_POST["tables"],
        ];

        if ($_POST["what"] == "structureonly") {
            $dumpSettings['no-data'] = true;
        }

        if ($_POST["what"] == "dataonly") {
            $dumpSettings['no-create-info'] = true; // Data only
        }

        if ((bool) $_POST["drop"]) {
            $dumpSettings['add-drop-table'] = true;
        }

        if ((bool) $_POST["extended_insert"]) {
            $dumpSettings['extended-insert'] = true;
        }

        if ((bool) $_POST["complete_insert"]) {
            $dumpSettings['complete-insert'] = true;
        }

        if ($_POST['zip'] == 'zip') {
            $dumpSettings['compress'] = true;
        }

        $admins = new \phpCollab\Administration\Administration();

        $admins->dumpTables($dumpSettings);

    } else {
        return;
    }
}




