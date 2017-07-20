<?php
#Application name: PhpCollab
#Status page: 0
// no caching to keep phpCollab 2.0 behaviour
session_cache_limiter('none');   // suppress error messages for PHP version < 4.0.2
error_reporting(0);
$checkSession = "true";
include '../includes/library.php';        // starts session and writes session cache headers

$id = $_GET["id"];

$files = new \phpCollab\Files\Files();
$fileDetail = $files->getFileById($id);

// test if file is published and part of the current project
if ($fileDetail["fil_published"] == "1" || $fileDetail["fil_project"] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

// serve the requested file
include '../includes/download.php';
