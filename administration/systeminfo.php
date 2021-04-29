<?php

use phpCollab\Util;

$checkSession = "true";
require_once '../includes/library.php';


$db = $container->getPDO();

$strings = $GLOBALS["strings"];

$mkdirMethodMore = $loginMethodMore = $extensions = $matches = null;

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$setTitle .= " : System Information";
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["system_information"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->heading($strings["system_information"]);

$block1->openContent();
$block1->contentTitle($strings["product_information"]);

$block1->contentRow("PhpCollab Version", $version);
if ($session->get("connectedUsers")) {
    $block1->contentRow("# of Connected Users", $session->get("connectedUsers"));
}

$block1->contentRow("File Management",
    ['Off', 'On'][$fileManagement] . " (default max file size $maxFileSize) " . Util::convertSize($maxFileSize));

if ($mkdirMethod == "FTP") {
    $mkdirMethodMore = " (Path to root with mentioned account: \"$ftpRoot\")";
}

$block1->contentRow("Create folder method", $mkdirMethod . $mkdirMethodMore);
$block1->contentRow("Theme", THEME);
$block1->contentRow("Product Site Publish", ['Off', 'On'][$sitePublish]);
$block1->contentRow("Notifications", ['Off', 'On'][$notifications]);
$block1->contentRow("Root", $root);

if ($useLDAP == "true") {
    $loginMethodMore = " + LDAP";
}
$block1->contentRow("Login Method", $loginMethod . $loginMethodMore);

switch ($databaseType) {
    case "postgresql":
        $databaseTypeMore = "PostgreSQL";
        break;
    case 'sqlserver':
        $databaseTypeMore = "Sql Server";
        break;
    default:
        $databaseTypeMore = "MySql";
        break;
}

$block1->contentRow("Database Type", $databaseTypeMore);
$block1->contentRow("Files folder size", phpCollab\Util::convertSize(phpCollab\Util::folderInfoSize("../files/")));

$block1->contentTitle($strings["system_properties"]);
$block1->contentRow("PHP Version",
    phpversion() . " " . $blockPage->buildLink("../administration/phpinfo.php?", "PhpInfo", "inblank"));
$block1->contentRow($databaseTypeMore . " version", $db->getVersion());
$block1->contentRow("extension_dir", ini_get('extension_dir'));

$ext = get_loaded_extensions();
$comptExt = count($ext);

for ($i = 0; $i < $comptExt; $i++) {
    $extensions .= "$ext[$i]";
    if ($i != $comptExt - 1) {
        $extensions .= ', ';
    }
}

$block1->contentRow("Loaded extensions", $extensions);

$include_path = ini_get('include_path');
if ($include_path == "") {
    $include_result = "<i>No value</i>";
} else {
    $include_result = $include_path;
}

$block1->contentRow("include_path", $include_result);

$register_globals = ini_get('register_globals');

$block1->contentRow("register_globals", ['Off', 'On'][$register_globals]);

$safemodeTest = ini_get('safe_mode');

$block1->contentRow("safe_mode", ['Off', 'On'][$safemodeTest]);

$mailTest = function_exists('mail');

$block1->contentRow("Mail", ['Off', 'On'][$mailTest]);

$gdlibraryTest = function_exists('imagecreate');

if ($gdlibraryTest == "true") {
    ob_start();
    phpinfo();
    $buffer = ob_get_contents();
    ob_end_clean();
    preg_match("|<b>GD Version</b></td><td align=\"left\">([^<]*)</td>|i", $buffer, $matches);
    preg_match("|GD Version </td><td class=\"v\">([^<]*)</td>|i", $buffer, $matches);
    preg_match("|GD Version</B></td><TD ALIGN=\"left\">([^<]*)</td>|i", $buffer, $matches);
    $gd_result = "On";
} else {
    $gd_result = "Off";
}

$block1->contentRow("GD", $gd_result);

if ($matches[1] != "") {
    $block1->contentRow("GD version", $matches[1]);
}

$block1->contentRow("SMTP", ini_get('SMTP'));
$block1->contentRow("upload_max_filesize", ini_get('upload_max_filesize'));
$block1->contentRow("session.name", session_name());

if (!empty(session_save_path())) {
    $block1->contentRow("session.save_path", session_save_path());
}

$block1->contentRow("HTTP_HOST", $request->server->get('HTTP_HOST'));

$block1->contentRow("SERVER_NAME", $request->server->get('SERVER_NAME'));
$block1->contentRow("SERVER_PORT", $request->server->get('SERVER_PORT'));
$block1->contentRow("SERVER_SOFTWARE", $request->server->get('SERVER_SOFTWARE'));
$block1->contentRow("SERVER_OS", PHP_OS);

$block1->contentTitle($strings["system_info_file_sizes"]);
$block1->contentRow("Post Max Size", ini_get('post_max_size'));
$block1->contentRow("Upload Max Size", ini_get('upload_max_filesize'));


$block1->closeContent();

include APP_ROOT . '/views/layout/footer.php';
