<?php

use phpCollab\Block;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["support_request_details"];

try {
    $support = $container->getSupportLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$id = $request->query->get('id');
$strings = $GLOBALS["strings"];

$requestDetail = $support->getSupportRequestById($id);

if ($requestDetail["sr_project"] != $session->get("project") || $requestDetail["sr_member"] != $session->get("id")) {
    if (!empty($requestDetail["sr_id"])) {
        // The support request wasn't found. This can happen if the 'lastvisited' page for a user is for
        // a request that no longer exists. If this happens the user gets stuck in a login loop and can't
        // log in.
        $members->setLastPageVisitedByLogin($session->get('login'), '');
    }
    phpCollab\Util::headerFunction("index.php");
}

$postDetail = $support->getSupportPostsByRequestId($id);

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

echo <<<HTML
 <table style="width: 90%" class="nonStriped">
    <tr>
        <th colspan="4">{$strings["information"]}:</th>
    </tr>
    <tr>
        <th>{$strings["support_id"]}:</th>
        <td>{$requestDetail["sr_id"]}</td>
        <th>{$strings["status"]}:</th>
        <td>{$requestStatus[$requestDetail["sr_status"]]}</td>
    </tr>
    <tr>
        <th>{$strings["subject"]}:</th>
        <td>{$escaper->escapeHtml($requestDetail["sr_subject"])}</td>
        <th>{$strings["priority"]}:</th>
        <td>{$priority[$requestDetail["sr_priority"]]}</td>
    </tr>
    <tr>
        <th>{$strings["message"]}:</th>
        <td>{$escaper->escapeHtml($requestDetail["sr_message"])}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th>{$strings["date_open"]}:</th>
        <td>{$requestDetail["sr_date_open"]}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
HTML;

if ($requestDetail["sr_status"] == "2") {
    echo <<<HTML
    <tr>
        <th>{$strings["date_close"]} :</th>
        <td>{$requestDetail["sr_date_close"]}</td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
    </tr>
HTML;
}

echo <<<HTML
<tr>
    <td colspan="4">&nbsp;</td>
</tr>
<tr>
    <th colspan="4">{$strings["responses"]}:</th>
</tr>
<tr>
    <td colspan="4" style="text-align: right"><a href="addsupportpost.php?id=$id" class="FooterCell">{$strings["add_support_response"]}</a></td>
</tr>
HTML;

$block1 = new Block();

if ($postDetail) {
    foreach ($postDetail as $key => $post) {
        if (!($key % 2)) {
            $class = "odd";
        } else {
            $class = "even";
        }
        $ownerDetail = $members->getMemberById($post["sp_owner"]);

        $responseMessage = nl2br( $escaper->escapeHtml($post["sp_message"]) );

        echo <<<HTML
            <tr class="{$class}">
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr class="{$class}">
                <th>{$strings["date"]} :</th>
                <td colspan="3">{$post["sp_date"]}</td>
            </tr>
            <tr class="{$class}">
                <th>{$strings["posted_by"]} :</th>
                <td colspan="3">{$ownerDetail["mem_name"]}</td>
            </tr>
            <tr class="{$class}">
                <th>{$strings["message"]} :</th>
                <td colspan="3">$responseMessage</td>
            </tr>

HTML;
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="4" class="ListOddRow">{$strings["no_items"]}</td>
    </tr>
HTML;
}

echo "</table>";

include("include_footer.php");
