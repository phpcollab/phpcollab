<?php

$checkSession = "true";
include_once '../includes/library.php';

$project = $_GET["project"];
$action = $_GET["action"];
$pub = $_GET["pub"];

$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];

$idSession = $_SESSION["idSession"];


$projects = new \phpCollab\Projects\Projects();
$teams = new \phpCollab\Teams\Teams();

$projectDetail = $projects->getProjectById($project);


if ($projectDetail["pro_org_id"] == "1") {
    $projectDetail["pro_org_name"] = $strings["none"];
}

if ($action == "add") {

    if ($pub == "") {
        $pub = "1";
    }

    $ttt = phpCollab\Util::convertData($_POST["ttt"]);
    $tpm = phpCollab\Util::convertData($_POST["tpm"]);

    $num = phpCollab\Util::newConnectSql(
        "INSERT INTO {$tableCollab["topics"]} (project,owner,subject,status,last_post,posts,published) VALUES (:project, :owner, :subject, '1', :last_post, '1', :published)",
        ["project" => $project, "owner" => $idSession, "subject" => $ttt, "last_post" => $dateheure, "published" => $pub]
    );

    phpCollab\Util::autoLinks($tpm);

    phpCollab\Util::newConnectSql(
        "INSERT INTO {$tableCollab["posts"]} (topic,member,created,message) VALUES (:topic, :member, :created, :message)",
        ["topic" => $num, "member" => $idSession, "created" => $dateheure, "message" => $tpm]
    );

    if ($notifications == "true") {
        include '../topics/noti_newtopic.php';
    }

    phpCollab\Util::headerFunction("../topics/viewtopic.php?project=$project&id=$num&msg=add");
}

$teamMember = "false";
$teamMember = $teams->isTeamMember($projectDetail["pro_id"], $idSession);

if ($teamMember == "false" && $projectsFilter == "true") {
    header("Location:../general/permissiondenied.php");
}

$bodyCommand = 'onLoad="document.ctTForm.ttt.focus();"';
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../topics/listtopics.php?project=" . $projectDetail["pro_id"], $strings["discussions"], "in"));
$blockPage->itemBreadcrumbs($strings["add_discussion"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "ctT";
$block1->openForm("../topics/addtopic.php?project=" . $projectDetail["pro_id"] . "&action=add");

if ((isset($error) && $error != "")) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["add_discussion"]);

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"] . " (#" . $projectDetail["pro_id"] . ")", "in"));
$block1->contentRow($strings["organization"], $projectDetail["pro_org_name"]);
$block1->contentRow($strings["owner"], $blockPage->buildLink("../users/viewuser.php?id=" . $projectDetail["pro_mem_id"], $projectDetail["pro_mem_name"], "in") . " (" . $blockPage->buildLink($projectDetail["pro_mem_email_work"], $projectDetail["pro_mem_login"], "mail") . ")");

$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["topic"], '<input size="44" value="'.$ttt.'" style="width: 400px" name="ttt" maxlength="64" type="TEXT">');
$block1->contentRow($strings["message"], '<textarea rows="10" style="width: 400px; height: 160px;" name="tpm" cols="47">'.$tpm.'</textarea>');
$block1->contentRow($strings["published"], '<input size="32" value="0" name="pub" type="checkbox">');
$block1->contentRow("", '<input type="submit" value="' . $strings["save"] . '">');

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
