<?php
#Application name: PhpCollab
#Status page: 0
// no caching to keep phpCollab 2.0 behaviour
@session_cache_limiter('none');		// suppress error messages for PHP version < 4.0.2
error_reporting(0);

$checkSession = "true";
include '../includes/library.php';		// starts session and writes session cache headers

$tmpquery = "WHERE fil.id = '$id'";
$fileDetail = new phpCollab\Request();
$fileDetail->openFiles($tmpquery);

// serve the requested file
include '../includes/download.php';
?>