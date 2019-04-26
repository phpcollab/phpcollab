<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Members\Members;
use phpCollab\Support\Support;

$checkSession = "true";
include '../includes/library.php';

$members = new Members();
$support = new Support();

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

$userDetail = $members->getMemberById($idSession);

$listRequests = $support->getSupportRequestByMemberIdAndProjectId($idSession, $project);

$block1 = new phpCollab\Block();

$block1->heading($strings["my_support_request"]);

if ($listRequests) {
    echo <<<TABLE
        <table style="width: 90%;" class="listing">
            <tr>
                <th class="active">{$strings["id"]}</th>
                <th>{$strings["subject"]}</th>
                <th>{$strings["priority"]}</th>
                <th>{$strings["status"]}</th>
                <th>{$strings["project"]}</th>
                <th>{$strings["date_open"]}</th>
                <th>{$strings["date_close"]}</th>
            </tr>
TABLE;

    foreach ($listRequests as $listRequest) {
        if (!($i % 2)) {
            $class = "odd";
            $highlightOff = $block1->getOddColor();
            $highlightOn = $block1->getEvenColor();
        } else {
            $class = "even";
            $highlightOff = $block1->getEvenColor();
            $highlightOn = $block1->getOddColor();
        }

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
            <tr class="$class" 
                onmouseover="this.style.backgroundColor='{$highlightOn}'" 
                onmouseout="this.style.backgroundColor='{$highlightOff}'">
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
    echo "</table><hr />\n";
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
