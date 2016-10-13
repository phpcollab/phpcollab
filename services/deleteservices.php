<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../services/deleteservices.php

$checkSession = "true";
include_once '../includes/library.php';

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($action == "delete") {
    $id = str_replace("**", ",", $id);
    $tmpquery1 = "DELETE FROM " . $tableCollab["services"] . " WHERE id IN($id)";
    phpCollab\Util::connectSql($tmpquery1);
    phpCollab\Util::headerFunction("../services/listservices.php?msg=delete");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../services/listservices.php?", $strings["service_management"], in));
$blockPage->itemBreadcrumbs($strings["delete_services"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "service_delete";
$block1->openForm("../services/deleteservices.php?action=delete");

$block1->heading($strings["delete_services"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);
$tmpquery = "WHERE serv.id IN($id) ORDER BY serv.name";
$listServices = new phpCollab\Request();
$listServices->openServices($tmpquery);
$comptListServices = count($listServices->serv_id);

for ($i = 0; $i < $comptListServices; $i++) {
    echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>" . $listServices->serv_name[$i] . "&nbsp;(" . $listServices->serv_name_print[$i] . ")</td></tr>";
}

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"submit\" name=\"delete\" value=\"" . $strings["delete"] . "\"> <input type=\"button\" name=\"cancel\" value=\"" . $strings["cancel"] . "\" onClick=\"history.back();\"><input type=\"hidden\" value=\"$id\" name=\"id\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
?>