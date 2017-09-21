<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/addprojectsite.php

$checkSession = "true";
include_once '../includes/library.php';

$id = $_GET["id"];
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];

$projects = new \phpCollab\Projects\Projects();
$projectDetail = $projects->getProjectById($id);

if (!$projectDetail) {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
}
if ($_SESSION["idSession"] != $projectDetail["pro_owner"] && $profilSession != "5") {
    phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
}

if ($_GET["action"] == "create") {
    $tmpquery = "UPDATE {$tableCollab["projects"]} SET published='0' WHERE id = :id";
    phpCollab\Util::newConnectSql($tmpquery, ["id" => $id]);
    phpCollab\Util::headerFunction("../projects/viewprojectsite.php?id={$id}&msg=createProjectSite");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($strings["create_projectsite"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "csdD";
$block1->openForm("../projects/addprojectsite.php?action=create&id=$id");

$block1->heading($strings["create_projectsite"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=$id", $projectDetail["pro_name"], "in"));
if ($projectDetail->pro_org_id[0] == "1") {
    $block1->contentRow($strings["organization"], $strings["none"]);
} else {
    $block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail->pro_org_name[0], "in"));
}
$block1->contentRow("", "<input type=\"SUBMIT\" value=\"" . $strings["create"] . "\">");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
