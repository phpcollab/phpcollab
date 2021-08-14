<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Util;

$strings = $GLOBALS['strings'];
$appRoot = APP_ROOT;

$bodyCommand = (isset($bodyCommand)) ? $bodyCommand :'';
$headBonus = (isset($headBonus)) ? $headBonus :'';

echo <<<HEAD
<!doctype html>
<html lang="en">
<head>
<title>$setTitle</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="utf-8">
<meta name="robots" content="none" />
<meta name="description" content="$setDescription" />
<meta name="keywords" content="$setKeywords" />
<meta name="copyright" content="$setCopyright" />

<link rel="icon" type="image/png" sizes="32x32" href="../public/images/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../public/images/favicons/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../public/images/favicons/favicon-16x16.png">
<link rel="manifest" href="../public/site.webmanifest">

<script type="text/javascript" src="../javascript/general.js"></script>
<script type="text/JavaScript" src="../javascript/overlib_mini.js"></script>
<link rel="stylesheet" href="../public/css/fa-all.min.css">
HEAD;

echo '<link rel="stylesheet" href="../themes/' . THEME . '/css/stylesheet.css" type="text/css" />';

if ($debug === true && isset($debugbarRenderer) && is_object($debugbarRenderer)) {
    echo $debugbarRenderer->renderHead();
}


if (isset($includeCalendar) && $includeCalendar === true) {
    include APP_ROOT . '/includes/calendar.php';
}

echo <<<HTML
$headBonus
</head>
<body $bodyCommand>
    <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
HTML;

if (isset($blank) && $blank != "true" && $version >= "2.0") {
    try {
        $organization = $container->getOrganizationsManager();
        $client = $organization->getOrganizationById(1);
    } catch (Exception $exception) {
        $logger->error('Exception', ['Error' => $exception->getMessage()]);
    }
}

echo "<header>";

if (isset($client) && file_exists("../logos_clients/1." . $client["org_extension_logo"]) && $blank != "true" && $version >= "2.0") {
    echo <<< HEADER
    <img src="../logos_clients/1.{$client["org_extension_logo"]}" alt="{$client["org_name"]}">
HEADER;
} else {
    echo <<< HEADER
     <h1>
        $setTitle
    </h1>
HEADER;
}

$blockHeader = new phpCollab\Block();

if ($blank == "true") {
    $blockHeader->itemAccount("&nbsp;");
} else {
    if ( $session->get('auth') === true ) {
        $blockHeader->openAccount($session);
        echo $blockHeader->buildLink("../preferences/updateuser.php", $strings["preferences"], 'in');
        echo $blockHeader->buildLink("../projects_site/home.php?changeProject=true", $strings["go_projects_site"], 'inblank');
        echo $blockHeader->buildLink("../general/logout.php", $strings["logout"], 'in');
    }
}
$blockHeader->closeAccount();

echo "</header>";

$blockHeader->openNavigation();
if ($blank == "true") {
    $blockHeader->itemNavigation("&nbsp;");
} else {
    $navUrl = $_SERVER["REQUEST_URI"];
    if ($notLogged == "true") {
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/login.php", $strings["login"], 'in'));
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/license.php", $strings["license"], 'in'));
    } else {
        $blockHeader->itemNavigation($blockHeader->buildLink("../general/home.php", $strings["home"], 'in', Util::setNavActive($navUrl, "general")));
        $blockHeader->itemNavigation($blockHeader->buildLink("../projects/listprojects.php", $strings["projects"],
            'in', Util::setNavActive($navUrl, "projects") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../clients/listclients.php", $strings["clients"], 'in', Util::setNavActive($navUrl, "clients") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../reports/listreports.php", $strings["reports"], 'in', Util::setNavActive($navUrl, "reports") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../search/createsearch.php", $strings["search"], 'in', Util::setNavActive($navUrl, "search") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../calendar/viewcalendar.php", $strings["calendar"],
            'in', Util::setNavActive($navUrl, "calendar") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../newsdesk/listnews.php", $strings["newsdesk"], 'in', Util::setNavActive($navUrl, "newsdesk") ));
        $blockHeader->itemNavigation($blockHeader->buildLink("../bookmarks/listbookmarks.php?view=all",
            $strings["bookmarks"], 'in', Util::setNavActive($navUrl, "bookmarks") ));
        if ($session->get("profile") == "0") { // Remove the Admin menu item if user does not have admin privilages
            $blockHeader->itemNavigation($blockHeader->buildLink("../administration/admin.php", $strings["admin"],
                'in', Util::setNavActive($navUrl, "administration") ));
        }
    }
}
$blockHeader->closeNavigation();
