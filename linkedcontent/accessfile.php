<?php
#Application name: PhpCollab
#Status page: 0

session_cache_limiter('none');        // suppress error messages for PHP version < 4.0.2
error_reporting(0);

$checkSession = "true";
include '../includes/library.php';    // starts session and writes session cache headers

$files = $container->getFilesLoader();

$fileDetail = $files->getFileById($request->query->get('id'));

if ($fileDetail) {
    $fileAction = $container->getFileDownloadService();

    try {
        if (!empty($fileDetail["fil_vc_parent"])) {
            $pos = strrpos($fileDetail["fil_name"], ".");
            $filename = substr_replace($fileDetail["fil_name"], "_v{$fileDetail["fil_vc_version"]}.", $pos, 1);
        } else {
            $filename = $fileDetail["fil_name"];
        }

        if (empty($fileDetail["fil_task"])) {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $filename);
        } else {
            $fileAction->setFilesPath(APP_ROOT . "/files/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/" . $filename);
        }

        if ($request->query->get('mode') == "download") {
            $fileAction->downloadFile($filename);
        } elseif ($request->query->get('mode') == "view") {
            $fileAction->viewFile($filename);
        }
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }

}


