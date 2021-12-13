<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["document_list"];

try {
    $files = $container->getFilesLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$bouton[4] = "over";
$titlePage = $strings["document_list"];
include 'include_header.php';

$listFiles = $files->getProjectSiteFiles($session->get("project"), 'fil.name');

$block1 = new phpCollab\Block();

$block1->heading($strings["document_list"]);


if (!empty($request->query->get('msg'))) {
    include '../includes/messages.php';
    $block1->messageBox($msgLabel);
}


if ($listFiles) {
    echo <<<TABLE
    <table style="width: 90%;" class="listing striped">
        <tr>
            <th class="active">{$strings["name"]}</th>
            <th>{$strings["topic"]}</th>
            <th>{$strings["date"]}</th>
            <th>{$strings["approval_tracking"]}</th>
        </tr>
TABLE;

    foreach ($listFiles as $file) {

        $idStatus = $file["fil_status"];
        echo '<tr><td>';
        echo '<a href="clientfiledetail.php?id=' . $file["fil_id"] . '">' . $file["fil_name"] . '</a>';
        if ($file["fil_task"] != "0") {
            $folder = $file["fil_project"] . '/' . $file["fil_task"];
        } else {
            $folder = $file["fil_project"];
        }
        echo <<<TD
                </td>
                <td><a href="createthread.php?topicField={$file["fil_name"]}">{$strings["create"]}</a></td>
                <td>{$file["fil_date"]}</td>
                <td style="width: 20%;"><a href="docitemapproval.php?id={$file["fil_id"]}">$statusFile[$idStatus]</a></td>
            </tr>
TD;
    }
    echo "</table>";
} else {
    echo '<div class="no-records">' . $strings["no_items"] . '</div>';
}

echo <<<LINK
<br/><br/>
<a href="uploadfile.php" class="FooterCell">{$strings["upload_file"]}</a>
LINK;

include("include_footer.php");
