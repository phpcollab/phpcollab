<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../clients/deleteclients.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: deleteclients.php
**
** DESC: screen: delete client info
**
** HISTORY:
** 	2003-10-23	-	main page for client module
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/


use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

$clients = $container->getOrganizationsManager();
$projects = $container->getProjectsLoader();

$id = $request->query->get("id");

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {

            if ($request->request->get("action") == "delete") {

                $id = str_replace("**", ",", $id);

                $listOrganizations = $clients->getOrganizationsOrderedByName($id);

                foreach ($listOrganizations as $org) {
                    if (file_exists("logos_clients/" . $org['org_id'] . "." . $org['org_extension_logo'])) {
                        unlink("logos_clients/" . $org['org_id'] . "." . $org['org_extension_logo']);
                    }
                }

                try {
                    $deleteOrg = $clients->deleteClient($id);

                    $setDefaultOrg = $projects->setDefaultOrg($id);
                    $deleteMembers = $members->deleteMemberByOrgId($id);

                    phpCollab\Util::headerFunction("../clients/listclients.php?msg=delete");
                } catch (Exception $e) {
                    echo 'Message: ' . $e->getMessage();
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Clients: Delete Client',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}


$setTitle .= " : Delete Client";

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], 'in'));
$blockPage->itemBreadcrumbs($strings["delete_organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../clients/deleteclients.php?action=delete&id=$id", null, $csrfHandler);

$block1->heading($strings["delete_organizations"]);

$block1->openContent();
$block1->contentTitle($strings["delete_following"]);

$id = str_replace("**", ",", $id);

$listOrganizations = $clients->getOrganizationsOrderedByName($id);

foreach ($listOrganizations as $org) {
    $block1->contentRow("#" . $org['org_id'], $org['org_name']);
}

$block1->contentRow("",
    '<button type="submit" name="action" value="delete">' . $strings["delete"] . '</button> <input type="button" name="cancel" value="' . $strings["cancel"] . '" onClick="history.back();">');

$block1->closeContent();
$block1->closeForm();

$block1->note($strings["delete_organizations_note"]);

include APP_ROOT . '/views/layout/footer.php';
