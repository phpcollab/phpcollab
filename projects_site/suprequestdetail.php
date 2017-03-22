<?php
/*
** Application name: phpCollab
** Last Edit page: 30/05/2005
** Path by root: ../project_site/suprequestdestail.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: suprequestdestail.php
**
** DESC: Screen: manage the support request
**
** HISTORY:
** 30/05/2005	-	fix for [ 1210293 ] Login fails when last_page=non-existent support req
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/

$checkSession = "true";
include '../includes/library.php';

$support = new \phpCollab\Support\Support();

$id = $_GET["id"];
$idSession = $_SESSION["idSession"];
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];

$requestDetail = $support->getSupportRequestById($id);

if ($requestDetail["sr_project"] != $projectSession || $requestDetail["sr_user"] != $idSession) {
    if (!isset($requestDetail["sr_id"])) {
        // The support request wasn't found. This can happen if the lastvisited page for a user is for
        // a request that no longer exists. If this happens the user gets stuck in a login loop and can't
        // login.
        phpCollab\Util::newConnectSql(
            "UPDATE {$tableCollab["members"]} SET last_page='' WHERE login = :login",
            ["login" => $_SESSION['loginSession']]
        );
    }
    phpCollab\Util::headerFunction("index.php");
}

$postDetail = $support->getSupportPostsByRequestId($id);

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo "<table cellspacing='0' width='90%' cellpadding='3'><tr><th colspan='4'>" . $strings["information"] . ":</th></tr>";

$comptSupStatus = count($requestStatus);
for ($i = 0; $i < $comptSupStatus; $i++) {
    if ($requestDetail["sr_status"] == $i) {
        $requestStatus = $requestStatus[$i];
    }
}

$comptPri = count($priority);
for ($i = 0; $i < $comptPri; $i++) {
    if ($requestDetail["sr_priority"] == $i) {
        $requestPriority = $priority[$i];
    }
}

echo "<tr><th>" . $strings["support_id"] . ":</th><td>" . $requestDetail["sr_id"] . "</td><th>" . $strings["status"] . ":</th><td>$requestStatus</td></tr>
<tr><th>" . $strings["subject"] . ":</th><td>" . $requestDetail["sr_subject"] . "</td><th>" . $strings["priority"] . ":</th><td>$requestPriority</td></tr>
<tr><th>" . $strings["message"] . ":</th><td>" . $requestDetail["sr_message"] . "</td><th>&nbsp;</th><td>&nbsp;</td></tr>
<tr><th>" . $strings["date_open"] . " :</th><td>" . $requestDetail["sr_date_open"] . "</td><th>&nbsp;</th><td>&nbsp;</td></tr>";

if ($requestDetail["sr_status"] == "2") {
    echo "<tr><th>" . $strings["date_close"] . " :</th><td>" . $requestDetail["sr_date_close"] . "</td><th>&nbsp;</th><td>&nbsp;</td></tr>";
}

echo "<tr><td colspan=\"4\">&nbsp;</td></tr>
<tr><th colspan=\"4\">" . $strings["responses"] . ":</th></tr>
<tr><td colspan=\"4\" align=\"right\"><a href=\"addsupportpost.php?id=$id\" class=\"FooterCell\">" . $strings["add_support_response"] . "</a></td></tr>";

if ($postDetail) {
//    for ($i = 0; $i < $comptPostDetail; $i++) {
    foreach ($postDetail as $post) {
        if (!($i % 2)) {
            $class = "odd";
            $highlightOff = $block1->getOddColor();
        } else {
            $class = "even";
            $highlightOff = $block1->getEvenColor();
        }

        echo "	<tr><td colspan='4' class='$class'>&nbsp;</td></tr><tr class='$class'><th>" . $strings["date"] . " :</th><td colspan='3'>" . $post["sp_date"] . "</td></tr>";

        $tmpquery = "WHERE mem.id = '" . $post["sp_owner"] . "'";
        $ownerDetail = new phpCollab\Request();
        $ownerDetail->openMembers($tmpquery);

        echo "<tr class='$class'><th>" . $strings["posted_by"] . " :</th><td colspan='3'>" . $ownerDetail->mem_name[0] . "</td></tr><tr class='$class'><th>" . $strings["message"] . " :</th><td colspan='3'>" . nl2br($post["sp_message"]) . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='4' class='ListOddRow'>" . $strings["no_items"] . "</td></tr>";
}
echo "</table>";

include("include_footer.php");
?>