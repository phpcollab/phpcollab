<?php
#Application name: PhpCollab
#Status page: 0

session_cache_limiter('none');        // suppress error messages for PHP version < 4.0.2
error_reporting(0);

$checkSession = "true";
require_once '../includes/library.php';    // starts session and writes session cache headers

use phpCollab\Security\UnauthorizedException;

try {
    $files = $container->getFilesLoader();
    $authorization = $container->getAuthorization();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$fileId = (int)$request->query->get('id');

// SECURITY: Check authorization before file access (IDOR prevention)
try {
    $authorization->requireFileAccess($fileId);
} catch (UnauthorizedException $e) {
    $logger->warning('Unauthorized file access attempt', [
        'file_id' => $fileId,
        'user_id' => $session->get('id'),
        'ip' => $request->server->get('REMOTE_ADDR'),
        'error' => $e->getMessage()
    ]);
    http_response_code(403);
    die('Access Denied: You are not authorized to access this file.');
}

$fileDetail = $files->getFileById($fileId);

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


