<?php

$checkSession = "true";
require_once '../includes/library.php';

try {
    $teams = $container->getTeams();
    $organizations = $container->getOrganizationsManager();
    $projects = $container->getProjectsLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$updateProject = $request->query->get('updateProject');
$changeProject = $request->query->get('changeProject');
$projectId = $request->query->get('project');
$strings = $GLOBALS["strings"];
$priority = $GLOBALS["priority"];
$status = $GLOBALS["status"];

if ($updateProject == "true") {
    $testProject = $teams->getTeamByProjectIdAndTeamMemberAndStatusIsNotCompletedOrSuspendedAndIsNotPublished($projectId,
        $session->get("id"));
    if ($testProject) {
        $session->remove("project");
        $session->remove("projectDetail");

        $session->set('project', $projectId);
        $session->set('projectDetail', $testProject);

        phpCollab\Util::headerFunction("home.php");
    } else {
        phpCollab\Util::headerFunction("home.php?changeProject=true");
    }
}

if ($changeProject == "true") {
    $session->remove("project");
    $session->remove("projectDetail");
}

if ($session->get("project") != "" && $changeProject != "true") {
    $projectDetail = $projects->getProjectById($session->get("project"));
    $session->set('projectDetail', $projectDetail);

    $teamMember = "false";
    $teamMember = $teams->isTeamMember($session->get("project"), $session->get("id"));

    if ($teamMember == "false") {
        phpCollab\Util::headerFunction("index.php");
    }
}


$bouton[0] = "over";

$titlePage = $strings["welcome"] . " " . $session->get("name") . " " . $strings["your_projectsite"];

if (!empty($session->get("orgId")) && empty($session->get("clientDetail"))) {
    $clientDetail = $organizations->getOrganizationById($session->get("orgId"));
    $session->set("clientDetail", $clientDetail);
}

$setTitle .= " : " . $strings["home"];

include 'include_header.php';

$idStatus = $projectDetail["pro_status"];
$idPriority = $projectDetail["pro_priority"];

if ($session->get("project") == "" || $changeProject == "true") {
    $listProjects = $teams->getTeamByMemberIdAndStatusIsNotCompletedAndIsNotPublished($session->get("id"));

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
                <td>$priority[$idPriority]</td>
                <td>$status[$idStatus]</td>
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

if (!empty($session->get("project")) && $changeProject != "true") {

    $pro_description = nl2br($projectDetail["pro_description"]);
    echo <<<TABLE
        <table class="nonStriped">
            <tr>
                <td nowrap class="formLabel">{$strings["project"]} :</td>
                <td>&nbsp;{$projectDetail["pro_name"]}</td>
            </tr>
            <tr>
                <td nowrap class="formLabel" style="vertical-align: top">{$strings["description"]} : </td>
                <td>&nbsp;$pro_description</td>
            </tr>
            <tr>
                <td nowrap class="formLabel">{$strings["status"]} :</th>
                <td>&nbsp;$status[$idStatus]</td>
            </tr>
            <tr>
                <td nowrap class="formLabel">{$strings["priority"]} :</td>
                <td>$priority[$idPriority]</td>
            </tr>
TABLE;

    //Display project active phase
    if ($projectDetail["pro_phase_set"] != "0") {
        echo "<tr><td nowrap style='vertical-align: top' class='formLabel'>" . $strings["current_phase"] . " :</td><td>";

        $currentPhase = $phases->getPhasesByProjectIdAndIsCompleted($projectDetail["pro_id"]);
        $comptCurrentPhase = count($currentPhase);
        if ($comptCurrentPhase == 0) {
            echo "" . $strings["no_current_phase"] . " ";
        } else {
            for ($i = 0; $i < $comptCurrentPhase; $i++) {
                $pnum = $i + 1;
                echo "$pnum." . $currentPhase["pha_name"] . "  ";
            }
        }

        echo "</td></tr>";
    }

    $pro_created = phpCollab\Util::createDate($projectDetail["pro_created"], $session->get("timezone"));
    $pro_modified = phpCollab\Util::createDate($projectDetail["pro_modified"], $session->get("timezone"));

    if ($projectDetail["pro_url_dev"]) {
        echo <<<DEV_URL
        <tr>
            <td nowrap class="formLabel">{$strings["url_dev"]} :</td>
            <td>&nbsp;<a href="{$projectDetail["pro_url_dev"]}" target="_blank">{$projectDetail["pro_url_dev"]}</a></td>
        </tr>
DEV_URL;
    }

    if ($projectDetail["pro_url_prod"]) {
        echo <<<PROD_URL
        <tr>
            <td nowrap class="formLabel">{$strings["url_prod"]} :</td>
            <td>&nbsp;<a href="{$projectDetail["pro_url_prod"]}" target="_blank">{$projectDetail["pro_url_prod"]}</a></td>
        </tr>
PROD_URL;
    }

    echo <<<TR
        <tr>
            <td nowrap class="formLabel">{$strings["created"]} :</td>
            <td>$pro_created</td>
        </tr>
        <tr>
            <td nowrap class="formLabel">{$strings["modified"]} :</td>
            <td>$pro_modified</td>
        </tr>
        </table>
TR;

    $detailContact = $teams->getTeamByProjectIdAndTeamMember($session->get("project"), $projectDetail["pro_owner"]);

    if ($detailContact["tea_published"] == "0" && $detailContact["tea_project"] == $session->get("project")) {
        echo "<br/><div>" . $strings["contact_projectsite"] . ", <a href=\"contactdetail.php?id=" . $projectDetail["pro_owner"] . "\">" . $projectDetail["pro_mem_name"] . "</a>.</div>";
    }
}

include("include_footer.php");
