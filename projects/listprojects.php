<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/listprojects.php

use phpCollab\Projects\Projects;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$setTitle .= " : List **ctive Projects";

$defaultNumRowsToDisplay = 40;

$db = new phpCollab\Database(); // Move this to library?

$projects = new Projects();

$typeProjects = $request->query->get('typeProjects');

if (empty($typeProjects)) {
    $typeProjects = "active";
}

if ($typeProjects == "active") {
    $setTitle = str_replace("**", "A", $setTitle);
} else {
    $setTitle = str_replace("**", "Ina", $setTitle);
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->setLimitsNumber(4);
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["projects"]);
if ($typeProjects == "inactive") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?typeProjects=active", $strings["active"], "in") . " | " . $strings["inactive"]);
} elseif ($typeProjects == "active") {
    $blockPage->itemBreadcrumbs($strings["active"] . " | " . $blockPage->buildLink("../projects/listprojects.php?typeProjects=inactive", $strings["inactive"], "in"));
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../projects/listprojects.php?typeProjects=$typeProjects&#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["projects"]);

$block1->openPaletteIcon();
if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
$block1->paletteIcon(2, "info", $strings["view"]);
if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->paletteIcon(3, "edit", $strings["edit"]);
    $block1->paletteIcon(4, "copy", $strings["copy"]);
}
if ($enableMantis == "true") {
    $block1->paletteIcon(8, "bug", $strings["bug"]);
}
$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit($defaultNumRowsToDisplay);

$block1->sorting(
    "projects",
    $sortingUser["projects"],
    "pro.name ASC",
    $sortingFields = array(
        0 => "pro.id",
        1 => "pro.name",
        2 => "pro.priority",
        3 => "org.name",
        4 => "pro.status",
        5 => "mem.login",
        6 => "pro.published"
    )
);

$sorting = $block1->sortingValue;

$block1->setRecordsTotal(count($projects->getProjectList($session->get("idSession"), $typeProjects)));

$dataSet = $projects->getProjectList($session->get("idSession"), $typeProjects, $block1->getRowsLimit(), $block1->getLimit(), $sorting);

$projectCount = count($dataSet);

if ($projectCount > 0) {
    $block1->openResults();
    $block1->labels(
        $labels = array(
            0 => $strings["id"],
            1 => $strings["project"],
            2 => $strings["priority"],
            3 => $strings["organization"],
            4 => $strings["status"],
            5 => $strings["owner"],
            6 => $strings["project_site"]
        ),
        "true"
    );

    foreach ($dataSet as $data) {
        $idStatus = $data["pro_status"];
        $idPriority = $data["pro_priority"];

        $block1->openRow();
        $block1->checkboxRow($data["pro_id"]);
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_id"], "in"));
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_name"], "in"));
        $block1->cellRow('<img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $priority[$idPriority]);
        $block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=" . $data["pro_org_id"], $data["pro_org_name"], "in"));
        $block1->cellRow($status[$idStatus]);

        $block1->cellRow($blockPage->buildLink('../users/viewuser.php?id=' . $data["pro_mem_id"], $data["pro_mem_login"], "in"));

        if ($sitePublish == "true") {
            if ($data["pro_published"] === "1") {
                if ($data['pro_owner'] == $session->get("idSession")) {
                    $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $data["pro_id"], $strings["create"] . "...", "in") . "&gt;");
                } else {
                    $block1->cellRow(Util::doubleDash());
                }

            } else {
                $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/viewprojectsite.php?id=" . $data["pro_id"], $strings["details"], "in") . "&gt;");
            }
        }

        $block1->closeRow();
        $projectsTopics .= $data["pro_id"];

        if ($i != $comptListProjects - 1) {
            $projectsTopics .= ",";
        }
    }
    $block1->closeResults();
    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "");
} else {
    $block1->noresults();
}

$block1->closeFormResults();
$block1->openPaletteScript();
if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->paletteScript(0, "add", "../projects/editproject.php?", "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../projects/deleteproject.php?", "false,true,false", $strings["delete"]);
}
$block1->paletteScript(2, "info", "../projects/viewproject.php?", "false,true,false", $strings["view"]);
if ($session->get("profilSession") == "0" || $session->get("profilSession") == "1" || $session->get("profilSession") == "5") {
    $block1->paletteScript(3, "edit", "../projects/editproject.php?", "false,true,false", $strings["edit"]);
    $block1->paletteScript(4, "copy", "../projects/editproject.php?docopy=true", "false,true,false", $strings["copy"]);
}
if ($enableMantis == "true") {
    $block1->paletteScript(8, "bug", $pathMantis . "login.php?url=http://{$request->server->get("HTTP_HOST")}{$request->server->get("REQUEST_URI")}&username={$session->get("loginSession")}&password=$passwordSession", "false,true,false", $strings["bug"]);
}

$block1->closePaletteScript(count($dataSet), array_column($dataSet, 'pro_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
