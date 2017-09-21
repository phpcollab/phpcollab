<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include '../includes/library.php';

$bouton[4] = "over";
$titlePage = $strings["document_list"];
include 'include_header.php';

$tmpquery = "WHERE fil.project = '$projectSession' AND fil.published = '0' AND fil.vc_parent = '0' ORDER BY fil.name";
$listFiles = new phpCollab\Request();
$listFiles->openFiles($tmpquery);
$comptListFiles = count($listFiles->fil_id);

$block1 = new phpCollab\Block();

$block1->heading($strings["document_list"]);

if ($comptListFiles != "0") {
    echo "<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\" class=\"listing\">
<tr><th class=\"active\">" . $strings["name"] . "</th><th>" . $strings["topic"] . "</th><th>" . $strings["date"] . "</th><th>" . $strings["approval_tracking"] . "</th></tr>";

    for ($i = 0; $i < $comptListFiles; $i++) {
        if (!($i % 2)) {
            $class = "odd";
            $highlightOff = $block1->getOddColor();
        } else {
            $class = "even";
            $highlightOff = $block1->getEvenColor();
        }
        $idStatus = $listFiles->fil_status[$i];
        echo "<tr class=\"$class\" onmouseover=\"this.style.backgroundColor='" . $block1->getHighlightOn() . "'\" onmouseout=\"this.style.backgroundColor='" . $highlightOff . "'\"><td>";
        if ($listFiles->fil_task[$i] != "0") {
            echo "<a href=\"clientfiledetail.php?id=" . $listFiles->fil_id[$i] . "\">" . $listFiles->fil_name[$i] . "</a>";
            $folder = $listFiles->fil_project[0] . "/" . $listFiles->fil_task[0];
        } else {
            echo "<a href=\"clientfiledetail.php?id=" . $listFiles->fil_id[$i] . "\">" . $listFiles->fil_name[$i] . "</a>";
            $folder = $listFiles->fil_project[0];
        }
        echo " </td><td><a href=\"createthread.php?topicField=" . $listFiles->fil_name[$i] . "\">" . $strings["create"] . "</a></td><td>" . $listFiles->fil_date[$i] . "</td><td width=\"20%\" class=\"$class\"><a href=\"docitemapproval.php?id=" . $listFiles->fil_id[$i] . "\">$statusFile[$idStatus]</a></td></tr>";
    }
    echo "</table>
<hr />\n";
} else {
    echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\" class=\"listOddBold\">" . $strings["no_items"] . "</td></tr></table><hr>";
}

echo "<br/><br/>

<a href=\"uploadfile.php\" class=\"FooterCell\">" . $strings["upload_file"] . "</a>";

include("include_footer.php");
