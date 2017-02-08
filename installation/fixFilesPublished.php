<?php
#Application name: PhpCollab
#Status page: 0
#_fixFilesSize.php


$checkSession = "false";
include_once '../includes/library.php';

$tableCollab = $GLOBALS["tableCollab"];

echo '<p>Script to update published values with files updates and reviews: <a href="?action=update">launch</a></p>';

if ($_GET["action"] == "update") {
    $files = new \phpCollab\Files\Files();


    /**
     * Fix unpublished files
     */

    $filesPublished = $files->getPublishedFiles();
    $filesPublishedValue = [];
    if ($filesPublished) {
        foreach ($filesPublished as $file) {
            array_push($filesPublishedValue, $file["fil_id"]);
        }
        $placeholders = str_repeat ('?, ', count($filesPublishedValue)-1) . '?';
        $tmpquery1 = "UPDATE {$tableCollab["files"]} SET published= 0 WHERE vc_parent IN ($placeholders)";
        phpCollab\Util::newConnectSql($tmpquery1, $filesPublishedValue);
        unset($placeholders);
    }

    /**
     * Fix unpublished files
     */
    $filesPublishedNo = $files->getUnPublishedFiles();
    $filesPublishedNoValue = [];
    if ($filesPublishedNo) {
        foreach ($filesPublishedNo as $file) {
            array_push($filesPublishedNoValue, $file["fil_id"]);
        }
        $placeholders = str_repeat ('?, ', count($filesPublishedNoValue)-1) . '?';
        $tmpquery1 = "UPDATE {$tableCollab["files"]} SET published = 1 WHERE vc_parent IN ($placeholders)";
        phpCollab\Util::newConnectSql($tmpquery1, $filesPublishedNoValue);
    }
    echo "fixed :o)";
}
