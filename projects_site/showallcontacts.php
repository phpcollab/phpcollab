<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Teams\Teams;

$checkSession = "true";
include '../includes/library.php';

$teams = new Teams();

$bouton[1] = "over";
$titlePage = $strings["project_team"];
include 'include_header.php';

$listContacts = $teams->getProjectSiteContacts($session->get("project"), 'mem.name');

$block1 = new phpCollab\Block();

$block1->heading($strings["project_team"]);

if ($listContacts) {
    echo <<<TABLE
    <table style="width: 90%" class="listing striped">
        <tr>
            <th class="active">{$strings["name"]}</th>
            <th>{$strings["title"]}</th>
            <th>{$strings["company"]}</th>
            <th>{$strings["email"]}</th>
        </tr>
TABLE;
    foreach ($listContacts as $contact) {

        if ($contact["tea_mem_phone_work"] == "") {
            $contact["tea_mem_phone_work"] = $strings["none"];
        }
        echo <<<TR
        <tr>
            <td><a href="contactdetail.php?id={$contact["tea_mem_id"]}">{$contact["tea_mem_name"]}</a></td>
            <td>{$contact["tea_mem_title"]}</td>
            <td>{$contact["tea_org_name"]}</td>
            <td><a href="mailto:{$contact["tea_mem_email_work"]}">{$contact["tea_mem_email_work"]}</a></td>
        </tr>
TR;
    }
    echo <<<'TAG'
</table>
<hr />\n
TAG;
} else {
    echo <<<NO_RECORDS
        <div class="no-records">{$strings["no_items"]}</div>
NO_RECORDS;
}

include("include_footer.php");
