<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/listprojects.php

$checkSession = "true";
include_once '../includes/library.php';

$setTitle .= " : List **ctive Projects";

$db = new phpCollab\Database(); // Move this to library?

$projects_gateway = new phpCollab\Projects\ProjectsGateway($db);

if ($typeProjects == "") {
    $typeProjects = "active";
}

if ($typeProjects == "active") {
    $setTitle = str_replace("**", "A", $setTitle);
} else {
    $setTitle = str_replace("**", "Ina", $setTitle);
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["projects"]);
if ($typeProjects == "inactive") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?typeProjects=active", $strings["active"], "in") . " | " . $strings["inactive"]);
} else if ($typeProjects == "active") {
    $blockPage->itemBreadcrumbs($strings["active"] . " | " . $blockPage->buildLink("../projects/listprojects.php?typeProjects=inactive", $strings["inactive"], "in"));
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->limitssNumber = "1";

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../projects/listprojects.php?typeProjects=$typeProjects&#" . $block1->form . "Anchor");

$block1->heading($strings["projects"]);

$block1->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
$block1->paletteIcon(2, "info", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteIcon(3, "edit", $strings["edit"]);
    $block1->paletteIcon(4, "copy", $strings["copy"]);
}
if ($enable_cvs == "true") {
    $block1->paletteIcon(7, "cvs", $strings["browse_cvs"]);
}
if ($enableMantis == "true") {
    $block1->paletteIcon(8, "bug", $strings["bug"]);
}
$block1->closePaletteIcon();

$block1->limit = $blockPage->returnLimit("1");
$block1->rowsLimit = "20";

$block1->sorting(
    "projects",
    $sortingUser->sor_projects[0],
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

// TODO: add limits back in
$sorting = $block1->sortingValue;

$dataSet = $projects_gateway->getProjectList($idSession, $typeProjects, $sorting);

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
        $block1->checkboxRow($data["tea_pro_id"]);
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_id"], "in"));
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $data["pro_id"], $data["pro_name"], "in"));
        $block1->cellRow('<img src="../themes/' . THEME . '/images/gfx_priority/' . $idPriority . '.gif" alt=""> ' . $priority[$idPriority]);
        $block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=" . $data["pro_org_id"], $data["pro_org_name"], "in"));
        $block1->cellRow($status[$idStatus]);

        $block1->cellRow($blockPage->buildLink('../users/viewuser.php?id=' . $data["pro_mem_id"], $data["pro_mem_login"], "in"));

        if ($sitePublish == "true") {
            if ($data["pro_published"] == "1") {
                $block1->cellRow("&lt;" . $blockPage->buildLink("../projects/addprojectsite.php?id=" . $data["pro_id"], $strings["create"] . "...", "in") . "&gt;");
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
} else {
    $block1->noresults();
}

$block1->closeFormResults();
$block1->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(0, "add", "../projects/editproject.php?", "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../projects/deleteproject.php?", "false,true,false", $strings["delete"]);
}
$block1->paletteScript(2, "info", "../projects/viewproject.php?", "false,true,false", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(3, "edit", "../projects/editproject.php?", "false,true,false", $strings["edit"]);
    $block1->paletteScript(4, "copy", "../projects/editproject.php?docopy=true", "false,true,false", $strings["copy"]);
}
if ($enable_cvs == "true") {
    $block1->paletteScript(7, "cvs", "../browsecvs/browsecvs.php?", "false,true,false", $strings["browse_cvs"]);
}
if ($enableMantis == "true") {
    $block1->paletteScript(8, "bug", $pathMantis . "login.php?url=http://{$HTTP_HOST}{$REQUEST_URI}&username=$loginSession&password=$passwordSession", "false,true,false", $strings["bug"]);
}

$block1->closePaletteScript($comptListProjects, $listProjects->pro_id);

include '../themes/' . THEME . '/footer.php';
