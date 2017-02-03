<?php

// MIMETypes should be handled using PHP mime.magic once it's out
include '../includes/mimetypes.php';
$filespath = "../files";

// construct file path and test whether file exists/is accessible
$name = $fileDetail["fil_name"];
$project = $fileDetail["fil_project"];
$task = $fileDetail["fil_task"];

// take care of subdirectories for files associated with tasks
if ($task == "0") {
    $path = $filespath . "/" . $project . "/" . $name;
} else {
    $path = $filespath . "/" . $project . "/" . $task . "/" . $name;
}

if (!file_exists($path)) {
    echo "file does not exist:-/";
    exit;
}

// figure out mimetype, should be done using PHP mime.magic once it's out
$mimetype = $mimetypes[$fileDetail["fil_extension"]];
// Apache behaviour seems to send text/plain for unknown mimetypes so that's what we do, too
if ($mimetype == "") {
    $mimetype = "text/plain";
}

// eval 'mode' parameter for either view or download
if ($mode == "download") {
    header("Content-Length: " . filesize($path));
    header("Content-Type: $mimetype");
    header("Content-Disposition: attachment; filename=$name");
} elseif ($mode == "view") {
    header("Content-Length: " . filesize($path));
    header("Content-Type: $mimetype");
    header("Content-Disposition: inline; filename=$name");
    // Apache is sending Last Modified header, so we'll do it, too
    $modified = filemtime($path);
    header("Last-Modified: " . date("D, j M Y G:i:s T", $modified));    // something like Thu, 03 Oct 2002 18:01:08 GMT
}

# write file as response
readfile($path);
