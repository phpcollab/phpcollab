<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23
** Path by root: ../administration/admin.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: admin.php
**
** DESC: Screen: ADMINISTRATION
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use phpCollab\Administration\Settings;

$checkSession = "true";
require_once '../includes/library.php';
$setTitle .= " : Administration";
$admin = $container->getAdministration();

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["admin_intro"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include APP_ROOT . '/includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->heading($strings["administration"]);

$block1->openContent();
$block1->contentTitle($strings["admin_intro"]);

$block1->contentRow("", $blockPage->buildLink("../users/listusers.php", $strings["user_management"], "in"));

if ($enableInvoicing == "true") {
    $block1->contentRow("",
        $blockPage->buildLink("../services/listservices.php", $strings["service_management"], "in"));
}

if ($supportType == "admin") {
    $block1->contentRow("",
        $blockPage->buildLink("../administration/support.php", $strings["support_management"], "in"));
}

if ($databaseType == "mysql") {
    $block1->contentRow("", $blockPage->buildLink("../administration/phpmyadmin.php", $strings["database"], "in"));
}

$block1->contentRow("",
    $blockPage->buildLink("../administration/systeminfo.php", $strings["system_information"], "in"));
$block1->contentRow("", $blockPage->buildLink("../administration/mycompany.php", $strings["company_details"], "in"));
$block1->contentRow("", $blockPage->buildLink("../administration/listlogs.php", $strings["logs"], "in"));
$block1->contentRow($strings["update"],
    $blockPage->buildLink("../administration/updatesettings.php", $strings["edit_settings"],
        "in"));

if (file_exists("../installation/setup.php")) {
    $deleteSettingsAlert = <<<HTML
<div class="alert error">
    <h3>{$strings["attention"]}: {$strings["setup_erase"]}</h3>
HTML;


    if (is_writable("../setup.php")) {
        $deleteSettingsAlert .= <<<HTML
        <a href="../installation/remove_files.php">{$strings["setup_erase_file"]}</a>
HTML;
    } else {
        $deleteSettingsAlert .= <<<HTML
        <p style="color: #F00;font-weight:bold;">{$strings["setup_erase_file_ua"]}</p>
HTML;
    }

    $deleteSettingsAlert .= '</div>';

    $block1->contentRow("", $deleteSettingsAlert);
}


if (!isset($uuid) || empty($uuid)) {
    try {
        $uuid = Settings::appendUUID(APP_ROOT, $logger);
    } catch (Exception $e) {
        $logger->error('Exception', ['Error' => $e->getMessage()]);
    }
}

if ($updateChecker == "true" && $installationType == "online") {

    $admin->checkForUpdate($version, $uuid, $session);

    if ($admin->isUpdate() !== false) {
        $checkMsg = <<<HTML
        <div class="alert info">
            <h3>{$strings["update_available"]}</h3>
            <p>{$strings["version_current"]} $version {$strings["version_latest"]} {$admin->getNewVersion()}.</p>
HTML;
        $checkMsg .= "<p>" . sprintf($strings["latest_release_link_text"], "https://github.com/phpcollab/phpcollab/releases/latest") . "</p>";
        $checkMsg .= <<<HTML
        </div>
HTML;
        $block1->contentRow("", $checkMsg);
    }
}

$block1->closeContent();

include APP_ROOT . '/views/layout/footer.php';
