<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

$teams = $container->getTeams();

$userDetail = $members->getMemberById($id);

$detailContact = $teams->getTeamByProjectIdAndTeamMember($session->get("project"), $request->query->get("id"));

if ($detailContact[0]["tea_published"] == "1" || $detailContact["tea_project"][0] != $session->get("project")) {
    phpCollab\Util::headerFunction("home.php");
}

$bouton[1] = "over";
$titlePage = $strings["team_member_details"];
include 'include_header.php';

echo <<<START_PAGE
<h1 class="heading">{$strings["team_member_details"]}</h1>
<table class="nonStriped">
START_PAGE;


if ($userDetail["mem_name"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["full_name"]} :</td>
        <td>{$userDetail["mem_name"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_organization"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["company"]} :</td>
        <td>{$userDetail["org_name"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_title"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["title"]} :</td>
        <td>{$userDetail["mem_title"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_email_work"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["email"]} : </td>
        <td><a href=\"mailto:{$userDetail["mem_email_work"]}\">{$userDetail["mem_email_work"]}</a></td>
    </tr>
TR;
}
if ($userDetail["mem_phone_home"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["home_phone"]} :</td>
        <td>{$userDetail["mem_phone_home"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_phone_work"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["work_phone"]} : </td>
        <td>{$userDetail["mem_phone_work"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_mobile"] != "") {
    echo <<<TR
    <tr>
        <td>{$strings["mobile_phone"]} :</td>
        <td>{$userDetail["mem_mobile"]}</td>
    </tr>
TR;
}
if ($userDetail["mem_fax"] != "") {
    echo <<<TR
    <tr>
        <td> {$strings["fax"]} :</td>
        <td>{$userDetail["mem_fax"]}</td>
    </tr>
TR;
}

echo <<<END_PAGE
</table>
<br/>
<br/>
<a href="showallcontacts.php">{$strings["show_all"]}</a>
END_PAGE;

include("include_footer.php");
