<?php
#Application name: PhpCollab
#Status page: 0
#_fixFilesSize.php


$checkSession = "false";
include_once '../includes/library.php';

echo "Script to update published values with files updates and reviews: <a href=\"$PHP_SELF?action=update\">launch</a><br/><br/>";

if ($action == "update") {
$tmpquery = "WHERE fil.published = '0'";
$filesPublished = new phpCollab\Request();
$filesPublished->openFiles($tmpquery);
$comptFilesPublished = count($filesPublished->fil_id);

if ($comptFilesPublished != "0") {
	for ($i=0;$i<$comptFilesPublished;$i++) {
		$filesPublishedValue .= $filesPublished->fil_id[$i];
		if ($i != $comptFilesPublished-1) {
			$filesPublishedValue .= ",";
		}
	}
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='0' WHERE vc_parent IN ($filesPublishedValue)";
phpCollab\Util::connectSql("$tmpquery1");
}

$tmpquery = "WHERE fil.published = '1'";
$filesPublishedNo = new phpCollab\Request();
$filesPublishedNo->openFiles($tmpquery);
$comptFilesPublishedNo = count($filesPublishedNo->fil_id);

if ($comptFilesPublishedNo != "0") {
	for ($i=0;$i<$comptFilesPublishedNo;$i++) {
		$filesPublishedNoValue .= $filesPublishedNo->fil_id[$i];
		if ($i != $comptFilesPublishedNo-1) {
			$filesPublishedNoValue .= ",";
		}
	}
$tmpquery1 = "UPDATE ".$tableCollab["files"]." SET published='1' WHERE vc_parent IN ($filesPublishedNoValue)";
phpCollab\Util::connectSql("$tmpquery1");
}
echo "fixed :o)";
}
?>

