<?php

use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include '../includes/library.php';

$teams = new Teams();
$organizations = new Organizations();
$projects = new Projects();

$updateProject = $_GET["updateProject"];
$changeProject = $_GET["changeProject"];
$idSession = $_SESSION["idSession"];
$nameSession = $_SESSION["nameSession"];
$timezoneSession = $_SESSION["timezoneSession"];
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
$titlePage = $strings["welcome"] . " $nameSession " . $strings["your_projectsite"];

include 'include_header.php';

if ($updateProject != "true" && $changeProject != "true") {
    $clientDetail = $organizations->getOrganizationById($projectDetail["pro_organization"]);
}

$idStatus = $projectDetail["pro_status"];
$idPriority = $projectDetail["pro_priority"];

if ($projectSession == "" || $changeProject == "true") {
    $listProjects = $teams->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($idSession);

    $block1 = new phpCollab\Block();

    $block1->heading($strings["my_projects"]);

    if ($listProjects) {
        echo <<<TABLE
        <table style="width: 90%" class="listing striped">
            <tr>
                <th class="active">{$strings["name"]}</th>
                <th>{$strings["organization"]}</th>
                <th>{$strings["priority"]}</th>
                <th>{$strings["status"]}</th>
            </tr>
TABLE;

        foreach ($listProjects as $project) {
            $idStatus = $project["tea_pro_status"];
            $idPriority = $project["tea_pro_priority"];
            
            echo <<<TR
            <tr>
                <td style="width: 30%"><a href="home.php?updateProject=true&project={$project["tea_pro_id"]}">{$project["tea_pro_name"]}</a></td>
                <td>{$project["tea_org2_name"]}</td>
                <td>{$priority[$idPriority]}</td>
                <td>{$status[$idStatus]}</td>
            </tr>
TR;
        }

        echo "	</table>
				<hr />\n";
    } else {
        echo <<<NO_RESULTS
        <div class="no-records">
            {$strings["no_items"]}
        </div>
        <hr />
NO_RESULTS;
    }
}

if ($projectSession != "" && $changeProject != "true") {
    if (file_exists("../logos_clients/" . $clientDetail["org_id"] . "." . $clientDetail["org_extension_logo"])) {
        $image = $clientDetail["org_id"] . '.' . $clientDetail["org_extension_logo"];
        echo '<img alt="" src="../logos_clients/' . $image . '"><br/><br/>';
    }

    $pro_description = nl2br($projectDetail["pro_description"]);
    echo <<<TABLE
        <table class="nonStriped">
            <tr>
                <th nowrap class="FormLabel">{$strings["project"]} :</th>
                <td>&nbsp;{$projectDetail["pro_name"]}</td>
            </tr>
            <tr>
                <th nowrap class="FormLabel" style="vertical-align: top">{$strings["description"]} : </th>
                <td>&nbsp;{$pro_description}</td>
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
    if ($projectDetail["pro_phase_set"] != "0") {
        echo "	<tr><th nowrap style='vertical-align: top' class='FormLabel'>" . $strings["current_phase"] . " :</td><td>";

        $currentPhase = $phases->getPhasesByProjectIdAndIsCompleted($projectDetail["pro_id"]);
        $comptCurrentPhase = count($currentPhase);
        if ($comptCurrentPhase == 0) {
            echo "" . $strings["no_current_phase"] . " ";
        } else {
            for ($i = 0; $i < $comptCurrentPhase; $i++) {
                if ($i != $comptCurrentPhase) {
                    $pnum = $i + 1;
                    echo "$pnum." . $currentPhase["pha_name"] . "  ";
                }
            }
        }

        echo "</td></tr>";
    }

    $pro_created = phpCollab\Util::createDate($projectDetail["pro_created"], $timezoneSession);
    $pro_modified = phpCollab\Util::createDate($projectDetail["pro_modified"], $timezoneSession);

    echo <<<TR
        <tr>
            <th nowrap class="FormLabel">{$strings["url_dev"]} :</th>
            <td>&nbsp;<a href="{$projectDetail["pro_url_dev"]}" target="_blank">{$projectDetail["pro_url_dev"]}</a></td>
        </tr>
        <tr>
            <th nowrap class="FormLabel">{$strings["url_prod"]} :</th>
            <td>&nbsp;<a href="{$projectDetail["pro_url_prod"]}" target="_blank">{$projectDetail["pro_url_prod"]}</a></td>
        </tr>
        <tr>
            <th nowrap class="FormLabel">{$strings["created"]} :</th>
            <td>&nbsp;{$pro_created}</td>
        </tr>
        <tr>
            <th nowrap class="FormLabel">{$strings["modified"]} :</th>
            <td>&nbsp;{$pro_modified}</td>
        </tr>
        </table>
TR;

    $detailContact = $teams->getTeamByProjectIdAndTeamMember($projectSession, $projectDetail["pro_owner"]);

    if ($detailContact["tea_published"] == "0" && $detailContact["tea_project"] == $projectSession) {
        echo "<br/><div>" . $strings["contact_projectsite"] . ", <a href=\"contactdetail.php?id=" . $projectDetail["pro_owner"] . "\">" . $projectDetail["pro_mem_name"] . "</a>.</div>";
    }
}

include("include_footer.php");
