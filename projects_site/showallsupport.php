<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$support = $container->getSupportLoader();

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

$userDetail = $members->getMemberById($session->get("id"));

$listRequests = $support->getSupportRequestByMemberIdAndProjectId($session->get("id"), $project);

$block1 = new phpCollab\Block();

$block1->heading($strings["my_support_request"]);

if ($listRequests) {
    echo <<<TABLE
        <table style="width: 90%;" class="listing striped">
            <tr>
                <th class="active">{$strings["id"]}</th>
                <th>{$strings["subject"]}</th>
                <th>{$strings["priority"]}</th>
                <th>{$strings["status"]}</th>
                <th>{$strings["project"]}</th>
                <th>{$strings["date_open"]}</th>
                <th>{$strings["date_close"]}</th>
            </tr>
            <tbody>
TABLE;

    foreach ($listRequests as $listRequest) {
        $comptSta = count($requestStatus);
        for ($sr = 0; $sr < $comptSta; $sr++) {
            if ($listRequest["sr_status"] == $sr) {
                $currentStatus = $requestStatus[$sr];
            }
        }

        $comptPri = count($priority);
        for ($rp = 0; $rp < $comptPri; $rp++) {
            if ($listRequest["sr_priority"] == $rp) {
                $requestPriority = $priority[$rp];
            }
        }

        echo <<<TABLE_ROW
            <tr>
                <td>{$listRequest["sr_id"]}</td>
                <td><a href="suprequestdetail.php?id={$listRequest["sr_id"]}">{$listRequest["sr_subject"]}</a></td>
                <td>{$requestPriority}</td>
                <td>{$currentStatus}</td>
                <td>{$listRequest["sr_project"]}</td>
                <td>{$listRequest["sr_date_open"]}</td>
                <td>{$listRequest["sr_date_close"]}</td>
            </tr>
TABLE_ROW;


    }
    echo "</tbody></table><hr />\n";
} else {
    echo <<<NO_RESULTS
    <table><tr><td colspan="4">{$strings["no_items"]}</td></tr></table><hr>
NO_RESULTS;

}
echo <<<FOOTER
    <br/><br/>
<a href="addsupport.php?project={$project}" class="FooterCell">{$strings["add_support_request"]}</a>
FOOTER;

include("include_footer.php");
