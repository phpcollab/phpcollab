<?php
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;

$filesPath = APP_ROOT . "/files";

// construct file path and test whether file exists/is accessible
$filename = $fileDetail["fil_name"];


if (!empty($fileDetail["fil_vc_parent"])) {
    $pos = strrpos($fileDetail["fil_name"], ".");
    $filename = substr_replace($fileDetail["fil_name"], "_v{$fileDetail["fil_vc_version"]}.", $pos, 1);
}

// take care of subdirectories for files associated with tasks
if (empty($fileDetail["fil_task"])) {
    $path = $filesPath . "/" . $fileDetail["fil_project"] . "/" . $filename;
} else {
    $path = $filesPath . "/" . $fileDetail["fil_project"] . "/" . $fileDetail["fil_task"] . "/" . $filename;
}

try {
    if (!file_exists($path)) {
        throw new Exception("file does not exist");
    } else {
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        if ($mimeTypeGuesser->isSupported()) {
            $mimeType = $mimeTypeGuesser->guess($path);
        } else {
            $mimeType = "text/plain";
        }

        // eval 'mode' parameter for either view or download
        if ($_GET["mode"] == "download") {
            header("Content-Length: " . filesize($path));
            header("Content-Type: $mimeType");
            header("Content-Disposition: attachment; filename=$filename");
        } elseif ($_GET["mode"] == "view") {
            header("Content-Length: " . filesize($path));
            header("Content-Type: $mimeType");
            header("Content-Disposition: inline; filename=$filename");
            // Apache is sending Last Modified header, so we'll do it, too
            $modified = filemtime($path);
            header("Last-Modified: " . date("D, j M Y G:i:s T", $modified));    // something like Thu, 03 Oct 2002 18:01:08 GMT
        }

        # write file as response
        readfile($path);

    }
} catch (Exception $e) {
    echo $e->getMessage();
}
