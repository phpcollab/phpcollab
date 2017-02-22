<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$tmpquery = "WHERE sr.id = '$id'";
$requestDetail = new phpCollab\Request();
$requestDetail->openSupportRequests($tmpquery);

if ($requestDetail->sr_project[0] != $projectSession || $requestDetail->sr_user[0] != $idSession) {
    phpCollab\Util::headerFunction("index.php");
}

if ($action == "add") {
    $mes = phpCollab\Util::convertData($mes);

    $num = phpCollab\Util::newConnectSql(
        "INSERT INTO {$tableCollab["support_posts"]} (request_id,message,date,owner,project) VALUES(:request_id,:message,:date,:owner,:project)",
        ["request_id" => $id,"message" => $mes,"date" => $dateheure,"owner" => $idSession,"project" => $requestDetail->sr_project[0]]
    );

    if ($notifications == "true") {
        if ($mes != "") {
            include '../support/noti_newpost.php';
        }
    }

    phpCollab\Util::headerFunction("suprequestdetail.php?id=$id");
}


$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo "<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../projects_site/addsupportpost.php?id=$id&action=add&project=$projectSession#filedetailsAnchor\" name=\"addsupport\" enctype=\"multipart/form-data\">";

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">" . $strings["add_support_response"] . "</th></tr>
<tr><th>" . $strings["message"] . "</th><td><textarea rows=\"3\" style=\"width: 400px; height: 200px;\" name=\"mes\" cols=\"43\">$mes</textarea></td></tr>
<input type=\"hidden\" name=\"user\" value=\"$idSession\">";

echo "<tr><th>&nbsp;</th><td><input type=\"SUBMIT\" value=\"" . $strings["submit"] . "\"></td></tr>
</table>
</form>";

include("include_footer.php");
?>