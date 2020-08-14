<?php

use phpCollab\Support\Support;

$checkSession = "true";
include '../includes/library.php';

$support = new Support($logger);

$id = $request->query->get('id');
$strings = $GLOBALS["strings"];

$requestDetail = $support->getSupportRequestById($id);

if ($requestDetail["sr_project"] != $session->get("projectSession") || $requestDetail["sr_member"] != $session->get("idSession")) {
    if (!empty($requestDetail["sr_id"])) {
        // The support request wasn't found. This can happen if the lastvisited page for a user is for
        // a request that no longer exists. If this happens the user gets stuck in a login loop and can't
        // login.
        $members->setLastPageVisitedByLogin($session->get('loginSession'), '');
    }
    phpCollab\Util::headerFunction("index.php");
}

$postDetail = $support->getSupportPostsByRequestId($id);

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo "<table style='width: 90%' class='nonStriped'><tr><th colspan='4'>" . $strings["information"] . ":</th></tr>";

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

echo <<< TR
    <tr>
        <th>{$strings["support_id"]}:</th>
        <td>{$requestDetail["sr_id"]}</td>
        <th>{$strings["status"]}:</th>
        <td>{$requestStatus}</td>
    </tr>
    <tr>
        <th>{$strings["subject"]}:</th>
        <td>{$requestDetail["sr_subject"]}</td>
        <th>{$strings["priority"]}:</th>
        <td>{$requestPriority}</td>
    </tr>
    <tr>
        <th>{$strings["message"]}:</th>
        <td>{$requestDetail["sr_message"]}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th>{$strings["date_open"]}:</th>
        <td>{$requestDetail["sr_date_open"]}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
TR;

if ($requestDetail["sr_status"] == "2") {
    echo "<tr><th>" . $strings["date_close"] . " :</th><td>" . $requestDetail["sr_date_close"] . "</td><th>&nbsp;</th><td>&nbsp;</td></tr>";
}

echo <<<HTML
<tr>
    <td colspan="4">&nbsp;</td>
</tr>
<tr>
    <th colspan="4">{$strings["responses"]}:</th>
</tr>
<tr>
    <td colspan="4" style="text-align: right"><a href="addsupportpost.php?id={$id}" class="FooterCell">{$strings["add_support_response"]}</a></td>
</tr>
HTML;

$block1 = new \phpCollab\Block();

if ($postDetail) {
    foreach ($postDetail as $key => $post) {
        if (!($key % 2)) {
            $class = "odd";
            $highlightOff = $block1->getOddColor();
        } else {
            $class = "even";
            $highlightOff = $block1->getEvenColor();
        }

        echo '<tr><td colspan="4" class="' . $class . '">&nbsp;</td></tr>';
        echo '<tr class="' . $class . '"><th>' . $strings["date"] . ' :</th><td colspan="3">' . $post["sp_date"] . '</td></tr>';

        $ownerDetail = $members->getMemberById($post["sp_owner"]);

        echo '<tr class="' . $class . '"><th>' . $strings["posted_by"] . ' :</th><td colspan="3">' . $ownerDetail["mem_name"] . '</td></tr>';
        echo '<tr class="' . $class . '"><th>' . $strings["message"] . ' :</th><td colspan="3">' . nl2br($post["sp_message"]) . '</td></tr>';
    }
} else {
    echo "<tr><td colspan='4' class='ListOddRow'>" . $strings["no_items"] . "</td></tr>";
}
echo "</table>";

include("include_footer.php");
