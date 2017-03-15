<?php

$checkSession = "true";
include '../includes/library.php';

$teams = new \phpCollab\Teams\Teams();
$organizations = new \phpCollab\Organizations\Organizations();

$updateProject = $_GET["updateProject"];
$changeProject = $_GET["changeProject"];
$idSession = $_SESSION["idSession"];
$projectSession = $_SESSION["projectSession"];
$project = $_GET["project"];
$strings = $GLOBALS["strings"];
$priority = $GLOBALS["priority"];
$status = $GLOBALS["status"];

if ($updateProject == "true") {
    $testProject = $teams->getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($project, $idSession);

    if ($testProject) {
        unset($_SESSION['projectSession']);

        $projectSession = $project;

        $_SESSION['projectSession'] = $projectSession;

        phpCollab\Util::headerFunction("home.php");
    } else {
        phpCollab\Util::headerFunction("home.php?changeProject=true");
    }
}

$bouton[0] = "over";
$titlePage = $strings["welcome"]{$_SESSION["nameSession"]} . $strings["your_projectsite"];
include 'include_header.php';

if ($updateProject != "true" && $changeProject != "true") {
    $clientDetail = $organizations->getOrganizationById($projectDetail["pro_organization"]);
}

$idStatus = $projectDetail->pro_status[0];
$idPriority = $projectDetail->pro_priority[0];

if ($projectSession == "" || $changeProject == "true") {
    $listProjects = $teams->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($idSession);

    $block1 = new phpCollab\Block();

    $block1->heading($strings["my_projects"]);

    if ($listProjects) {
        echo <<<TABLE
        <table cellspacing='0' width='90%' border='0' cellpadding='3' cols='4' class='listing'>
            <tr>
                <th class="active">{$strings["name"]}</th>
                <th>{$strings["organization"]}</th>
                <th>{$strings["priority"]}</th>
                <th>{$strings["status"]}</th>
            </tr>
TABLE;

        foreach ($listProjects as $project) {
            if (!($i % 2)) {
                $class = "odd";
                $highlightOff = $block1->getHighlightOff();
            } else {
                $class = "even";
                $highlightOff = $block1->getHighlightOff();
            }

            $idStatus = $project["tea_pro_status"];
            $idPriority = $project["tea_pro_priority"];
            
            echo <<<TR
            <tr class="{$class}" onmouseover="this.style.backgroundColor='{$block1->getHighlightOn()}'" onmouseout="this.style.backgroundColor='{$highlightOff}'">
                <td width="30%"><a href="home.php?updateProject=true&project={$project["tea_pro_id"]}">{$project["tea_pro_name"]}</a></td>
                <td>{$project["tea_org2_name"]}</td>
                <td>{$priority[$idPriority]}</td>
                <td>{$status[$idStatus]}</td>
            </tr>
TR;
        }

        echo "	</table>
				<hr />\n";
    } else {
        echo <<<TABLE
        <table cellspacing="0" border="0" cellpadding="2">
            <tr>
                <td colspan="4" class="listOddBold">{$strings["no_items"]}</td>
            </tr>
        </table>
        <hr />
TABLE;
    }

}

if ($projectSession != "" && $changeProject != "true") {

    if (file_exists("../logos_clients/" . $clientDetail["org_id"] . "." . $clientDetail["org_extension_logo"])) {
        echo "<img src=\"../logos_clients/" . $clientDetail["org_id"] . "." . $clientDetail["org_extension_logo"] . "\"><br/><br/>";
    }

    echo <<<TABLE
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th nowrap class="FormLabel">{$strings["project"]} :</th>
                <td>&nbsp;{$projectDetail->pro_name[0]}</td>
            </tr>
            <tr>
                <th nowrap class="FormLabel" valign="top">{$strings["description"]} : </th>
                <td>&nbsp;{nl2br($projectDetail->pro_description[0])}</td>
            </tr>
            <tr>
                <th nowrap class="FormLabel">{$strings["status"]} :</th>
                <td>&nbsp;{$status[$idStatus]}</td>
            </tr>
            <tr>
                <th nowrap class="FormLabel">{$strings["priority"]} :</th>
                <td>&nbsp;{$priority[$idPriority]}</td>
            </tr>
TABLE;

    //Dispaly project active phase
    if ($projectDetail->pro_phase_set[0] != "0") {

        echo "	<tr><th nowrap valign='top' class='FormLabel'>" . $strings["current_phase"] . " :</td><td>";

        $tmpquery = "WHERE pha.project_id = '" . $projectDetail->pro_id[0] . "' AND status = '1'";
        $currentPhase = new phpCollab\Request();
        $currentPhase->openPhases($tmpquery);
        $comptCurrentPhase = count($currentPhase->pha_id);
        if ($comptCurrentPhase == 0) {
            echo "" . $strings["no_current_phase"] . " ";
        } else {
            for ($i = 0; $i < $comptCurrentPhase; $i++) {
                if ($i != $comptCurrentPhase) {
                    $pnum = $i + 1;
                    echo "$pnum." . $currentPhase->pha_name[$i] . "  ";
                }
            }
        }

        echo "</td></tr>";

    }

    //-------------------------------------------------------------------------------------------

    echo "	<tr>
				<th nowrap class='FormLabel'>" . $strings["url_dev"] . " :</th>
				<td>&nbsp;<a href='" . $projectDetail->pro_url_dev[0] . "' target='_blank'>" . $projectDetail->pro_url_dev[0] . "</a></td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>" . $strings["url_prod"] . " :</th>
				<td>&nbsp;<a href='" . $projectDetail->pro_url_prod[0] . "' target='_blank'>" . $projectDetail->pro_url_prod[0] . "</a></td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>" . $strings["created"] . " :</th>
				<td>&nbsp;" . phpCollab\Util::createDate($projectDetail->pro_created[0], $timezoneSession) . "</td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>" . $strings["modified"] . " :</th>
				<td>&nbsp;" . phpCollab\Util::createDate($projectDetail->pro_modified[0], $timezoneSession) . "</td>
			</tr>
			</table>";

    $tmpquery = "WHERE tea.project = '$projectSession' AND tea.member = '" . $projectDetail->pro_owner[0] . "'";
    $detailContact = new phpCollab\Request();
    $detailContact->openTeams($tmpquery);

    if ($detailContact->tea_published[0] == "0" && $detailContact->tea_project[0] == $projectSession) {
        echo "<br/><div>" . $strings["contact_projectsite"] . ", <a href=\"contactdetail.php?id=" . $projectDetail->pro_owner[0] . "\">" . $projectDetail->pro_mem_name[0] . "</a>.</div>";
    }
}

include("include_footer.php");
?>