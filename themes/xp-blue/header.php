<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Organizations\Organizations;

$strings = $GLOBALS['strings'];

echo <<<HEAD
$setDoctype
$setCopyright

<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset="utf-8">
<title>$setTitle</title>
<meta name="robots" content="none" />
<meta name="description" content="$setDescription" />
<meta name="keywords" content="$setKeywords" />
<meta name="copyright" content="PHPCollab" />

<script type="text/Javascript">
<!--
var gBrowserOK = true;
var gOSOK = true;
var gCookiesOK = true;
var gFlashOK = true;
// -->
</script>
<script type="text/javascript" src="../javascript/general.js"></script>
<script type="text/JavaScript" src="../javascript/overlib_mini.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">


HEAD;
if ($debug === true && isset($debugbarRenderer) && is_object($debugbarRenderer)) {
    echo $debugbarRenderer->renderHead();
}

echo '<link rel="stylesheet" href="../themes/' . THEME . '/css/stylesheet.css" type="text/css" />';

if ($includeCalendar && $includeCalendar === true) {
    include APP_ROOT . '/includes/calendar.php';
}

echo "
$headBonus
</head>";
echo "<body $bodyCommand>";

echo "<div id='overDiv' style='position:absolute; visibility:hidden; z-index:1000;'></div>\n\n";

if ($blank != "true" && $version >= "2.0") {
    $organization = new Organizations();
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
} else if ($notLogged == "true") {
    $blockHeader->itemAccount("&nbsp;");
} else {
    $blockHeader->itemAccount($strings["user"] . ":" . $nameSession);
    $blockHeader->itemAccount($blockHeader->buildLink("../general/login.php?logout=true", $strings["logout"], 'in'));
    $blockHeader->itemAccount($blockHeader->buildLink("../preferences/updateuser.php", $strings["preferences"], 'in'));
    $blockHeader->itemAccount($blockHeader->buildLink("../projects_site/home.php?changeProject=true", $strings["go_projects_site"], 'inblank'));
}
$blockHeader->closeAccount();

$blockHeader->openNavigation();
if ($blank == "true") {
    $blockHeader->itemNavigation("&nbsp;");
} else if ($notLogged == "true") {
    $blockHeader->itemNavigation($blockHeader->buildLink("../general/login.php", $strings["login"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../general/systemrequirements.php", $strings["requirements"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../general/license.php", $strings["license"], 'in'));
} else {
    $blockHeader->itemNavigation($blockHeader->buildLink("../general/home.php", $strings["home"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../projects/listprojects.php", $strings["projects"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../clients/listclients.php", $strings["clients"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../reports/listreports.php", $strings["reports"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../search/createsearch.php", $strings["search"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../calendar/viewcalendar.php", $strings["calendar"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../newsdesk/listnews.php", $strings["newsdesk"], 'in'));
    $blockHeader->itemNavigation($blockHeader->buildLink("../bookmarks/listbookmarks.php?view=all", $strings["bookmarks"], 'in'));
    if ($profilSession == "0") { // Remove the Admin menu item if user does not have admin privilages
        $blockHeader->itemNavigation($blockHeader->buildLink("../administration/admin.php", $strings["admin"], 'in'));
    }
}
$blockHeader->closeNavigation();
