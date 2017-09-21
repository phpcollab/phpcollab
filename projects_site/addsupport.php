<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$tmpquery = "WHERE mem.id = '$idSession'";
$userDetail = new phpCollab\Request();
$userDetail->openMembers($tmpquery);

$tmpquery = "WHERE sr.member = '$idSession'";
$listRequests = new phpCollab\Request();
$listRequests->openSupportRequests($tmpquery);
$comptListRequests = count($listRequests->sr_id);

if ($action == "add") {
    $sub = phpCollab\Util::convertData($sub);
    $mes = phpCollab\Util::convertData($mes);

    $num = phpCollab\Util::newConnectSql(
        "INSERT INTO {$tableCollab["support_requests"]} (member,priority,subject,message,date_open,project,status) VALUES(:member,:priority,:subject,:message,:date_open,:project,:status)",
        ["member" => $user,"priority" => $pr,"subject" => $sub,"message" => $mes,"date_open" => $dateheure,"project" => $project,"status" => 0]
    );

    if ($notifications == "true") {
        include '../support/noti_newrequest.php';
    }

    phpCollab\Util::headerFunction("suprequestdetail.php?id=$num");
}

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo "<form accept-charset='UNKNOWN' method='POST' action='../projects_site/addsupport.php?action=add&project=$projectSession#filedetailsAnchor' name='addsupport' enctype='multipart/form-data' >";

echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\">
<tr><th colspan=\"2\">" . $strings["add_support_request"] . "</th></tr>
<tr><th>" . $strings["priority"] . " :</th><td><select name=\"pr\">";

$comptPri = count($priority);
for ($i = 0; $i < $comptPri; $i++) {
    if ($i != 0) {
        echo "<option value=\"$i\">$priority[$i]</option>";
    }
}
echo "</select></td></tr>
<tr><th>" . $strings["subject"] . "</th><td><input size=\"32\" value=\"$sub\" style=\"width: 250px\" name=\"sub\" maxlength=\"32\" type=\"TEXT\"></td></tr>
<tr><th>" . $strings["message"] . "</th><td><textarea rows=\"3\" style=\"width: 400px; height: 200px;\" name=\"mes\" cols=\"43\">$mes</textarea></td></tr>
<input type=\"hidden\" name=\"user\" value=\"$idSession\">
<tr><th>&nbsp;</th><td><input type=\"SUBMIT\" value=\"" . $strings["submit"] . "\"></td></tr>
</table>
</form>";

include("include_footer.php");
