<?php
#Application name: PhpCollab
#Status page: 0

$strings = $GLOBALS['strings'];
$appRoot = APP_ROOT;

echo <<<HEAD
{$setDoctype}
{$setCopyright}

<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="utf-8">
<title>{$setTitle}</title>
<meta name="robots" content="none" />
<meta name="description" content="{$setDescription}" />
<meta name="keywords" content="{$setKeywords}" />
<meta name="copyright" content="PHPCollab" />

<link rel="icon" type="image/png" sizes="32x32" href="../../public/images/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../../public/images/favicons/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../../public/images/favicons/favicon-16x16.png">
<link rel="manifest" href="../../public/site.webmanifest">

<script type="text/Javascript">
<!--
    var gBrowserOK = true;
    var gOSOK = true;
    var gCookiesOK = true;
    var gFlashOK = true;
    // -->
</script>
<script type="text/javascript" src="../../javascript/general.js"></script>
<script type="text/JavaScript" src="../../javascript/overlib_mini.js"></script>
<link rel="stylesheet" href="../../public/css/all.min.css">
HEAD;


if ($debug === true && isset($debugbarRenderer) && is_object($debugbarRenderer)) {
    echo $debugbarRenderer->renderHead();
}

echo '<link rel="stylesheet" href="../themes/' . THEME . '/css/stylesheet.css" type="text/css" />';

if (isset($includeCalendar) && $includeCalendar === true) {
    include APP_ROOT . '/includes/calendar.php';
}

echo <<<HTML
{$headBonus}
</head>
<body {$bodyCommand}>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
HTML;

if ($blank != "true" && $version >= "2.0") {
    $organization = $container->getOrganizationsManager();
    $client = $organization->getOrganizationById(1);
}
if (file_exists("../logos_clients/1." . $client["org_extension_logo"]) && $blank != "true" && $version >= "2.0") {
    echo <<< HEADER
    <p id="header"><img src="../logos_clients/1.{$client["org_extension_logo"]}" alt="{$client["org_name"]}"></p>
HEADER;
} else {
    echo <<< HEADER
     <p id="header">{$setTitle}</p>
HEADER;
}

$blockHeader = new phpCollab\Block();

$blockHeader->openAccount();
if ($blank == "true") {
    $blockHeader->itemAccount("&nbsp;");
} else {
    if ($notLogged == "true") {
        $blockHeader->itemAccount("&nbsp;");
    } else {
        $blockHeader->itemAccount($strings["user"] . ":" . $session->get("name"));
        $blockHeader->itemAccount($blockHeader->buildLink("../general/logout.php", $strings["logout"], 'in'));
        $blockHeader->itemAccount($blockHeader->buildLink("../preferences/updateuser.php", $strings["preferences"],
            'in'));
        $blockHeader->itemAccount($blockHeader->buildLink("../projects_site/home.php?changeProject=true",
            $strings["go_projects_site"], 'inblank'));
    }
}
$blockHeader->closeAccount();

$blockHeader->openNavigation();
if ($blank == "true") {
    $blockHeader->itemNavigation("&nbsp;");
} else {
    if ($notLogged == "true") {
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/login.php", $strings["login"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/license.php", $strings["license"], 'in'));
    } else {
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/home.php", $strings["home"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../projects/listprojects.php", $strings["projects"],
            'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../clients/listclients.php", $strings["clients"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../reports/listreports.php", $strings["reports"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../search/createsearch.php", $strings["search"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../calendar/viewcalendar.php", $strings["calendar"],
            'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../newsdesk/listnews.php", $strings["newsdesk"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../bookmarks/listbookmarks.php?view=all",
            $strings["bookmarks"], 'in'));
        if ($session->get("profile") == "0") { // Remove the Admin menu item if user does not have admin privilages
            $blockHeader->itemNavigation($blockHeader->buildLink("../administration/admin.php", $strings["admin"],
                'in'));
        }
    }
}
$blockHeader->closeNavigation();
