<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

if ($action == "update") {
    $commentField = phpCollab\Util::convertData($commentField);
    phpCollab\Util::newConnectSql("UPDATE {$tableCollab["files"]} SET comments_approval=:comments_approval,date_approval=:date_approval,approver=:approver,status=:status WHERE id = :file_id", ["comments_approval" => $commentField,"date_approval" => $dateheure,"approver" => $idSession,"status" => $statusField, "file_id" => $id]);
    $msg = "updateFile";
    phpCollab\Util::headerFunction("doclists.php");
}

$tmpquery = "WHERE fil.id = '$id'";
$fileDetail = new phpCollab\Request();
$fileDetail->openFiles($tmpquery);

if ($fileDetail->fil_published[0] == "1" || $fileDetail->fil_project[0] != $projectSession) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[4] = "over";
$titlePage = $strings["approval_tracking"];
include 'include_header.php';

echo "<form accept-charset=\"UNKNOWN\" method=\"post\" action=\"../projects_site/docitemapproval.php?action=update\" name=\"documentitemapproval\" enctype=\"application/x-www-form-urlencoded\">";

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">" . $strings["approval_tracking"] . " :</th></tr>
<tr><th>" . $strings["document"] . " :</th><td><a href=\"clientfiledetail.php?id=" . $fileDetail->fil_id[0] . "\">" . $fileDetail->fil_name[0] . "</a></td></tr>
<tr><th>" . $strings["status"] . " :</th><td><select name=\"statusField\">";

$comptSta = count($statusFile);

for ($i = 0; $i < $comptSta; $i++) {
    if ($fileDetail->fil_status[0] == $i) {
        echo "<option value=\"$i\" selected>$statusFile[$i]</option>";
    } else {
        echo "<option value=\"$i\">$statusFile[$i]</option>";
    }
}

echo "</select></td></tr>
<tr><th>" . $strings["comments"] . " :</th><td><textarea rows=\"3\" name=\"commentField\" cols=\"43\">" . $fileDetail->fil_comments_approval[0] . "</textarea></td></tr>
<tr><th>&nbsp;</th><td><input name=\"submit\" type=\"submit\" value=\"" . $strings["save"] . "\"></td></tr>
</table>
<input name=\"id\" type=\"hidden\" value=\"$id\">
</form>";

include("include_footer.php");
?>