<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include("../includes/library.php");

$tmpquery = "WHERE mem.id = '$id'";
$userDetail = new Request();
$userDetail->openMembers($tmpquery);

$tmpquery = "WHERE tea.project = '$projectSession' AND tea.member = '$id'";
$detailContact = new Request();
$detailContact->openTeams($tmpquery);

if ($detailContact->tea_published[0] == "1" || $detailContact->tea_project[0] != $projectSession) {
Util::headerFunction("index.php");
}

$bouton[1] = "over";
$titlePage = $strings["team_member_details"];
include ("include_header.php");

echo "<h1 class=\"heading\">".$strings["team_member_details"]."</h1>";

echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"3\">";

if ($userDetail->mem_name[0] != "") {
echo "<tr><td>".$strings["full_name"]." :</td><td>".$userDetail->mem_name[0]."</td></tr>";
}
if ($userDetail->mem_organization[0] != "") {
echo "<tr><td>".$strings["company"]." :</td><td>".$userDetail->mem_org_name[0]."</td></tr>";
}
if ($userDetail->mem_title[0] != "") {
echo "<tr><td>".$strings["title"]." :</td><td>".$userDetail->mem_title[0]."</td></tr>";
}
if ($userDetail->mem_email_work[0] != "") {
echo "<tr><td>".$strings["email"]." : </td><td><a href=\"mailto:".$userDetail->mem_email_work[0]."\">".$userDetail->mem_email_work[0]."</a></td></tr>";
}
if ($userDetail->mem_phone_home[0] != "") {
echo "<tr><td>".$strings["home_phone"]." :</td><td>".$userDetail->mem_phone_home[0]."</td></tr>";
}
if ($userDetail->mem_phone_work[0] != "") {
echo "<tr><td>".$strings["work_phone"]." : </td><td>".$userDetail->mem_phone_work[0]."</td></tr>";
}
if ($userDetail->mem_mobile[0] != "") {
echo "<tr><td>".$strings["mobile_phone"]." :</td><td>".$userDetail->mem_mobile[0]."</td></tr>";
}
if ($userDetail->mem_fax[0] != "") {
echo "<tr><td> ".$strings["fax"]." :</td><td>".$userDetail->mem_fax[0]."</td></tr>";
}
echo "</table>
<hr>";

echo "<br/><br/>
<a href=\"showallcontacts.php?$transmitSid\">".$strings["show_all"]."</a>";

include ("include_footer.php");
?>