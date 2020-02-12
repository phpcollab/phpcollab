<?php
#Application name: PhpCollab
#Status page: 0
use phpCollab\Files\Files;
use phpCollab\Files\GetFile;

session_cache_limiter('none');        // suppress error messages for PHP version < 4.0.2
error_reporting(0);

$checkSession = "true";
include '../includes/library.php';    // starts session and writes session cache headers

$files = new Files();

$fileDetail = $files->getFileById($_GET["id"]);

if ($fileDetail) {
    $fileAction = new GetFile();

    try {
        if (!empty($fileDetail["fil_vc_parent"])) {
            $pos = strrpos($fileDetail["fil_name"], ".");
            $filename = substr_replace($fileDetail["fil_name"], "_v{$fileDetail["fil_vc_version"]}.", $pos, 1);
        }

        if (empty($fileDetail["fil_task"])) {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $filename);
        } else {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/" . $filename);
        }

        if ($_GET["mode"] == "download") {
            $fileAction->downloadFile($filename);
        } elseif ($_GET["mode"] == "view") {
            $fileAction->viewFile($filename);
        }
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

}


