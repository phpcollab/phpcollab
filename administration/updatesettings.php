<?php

/*
** Application name: phpCollab
** Path by root: ../administration/updatesettings.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: updatesettings.php
**
** DESC: Screen: System information and php library
**
** =============================================================================
*/


use phpCollab\Administration\Settings;
use phpCollab\DataFunctionsService;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';
$origSetTitle = $setTitle;
$setTitle .= " : Edit Settings";

if ($session->get('profile') != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$langSelected = [ $GLOBALS["langDefault"] => "selected"];

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "generate") {
                    if ($request->request->get('installationType') == "offline") {
                    $updateChecker = "false";
                }

                $request->request->get('siteUrl', rtrim($request->request->get('siteUrl'), '/'));
                $request->request->get('ftpRoot', rtrim($request->request->get('ftpRoot'), '/'));

                if ($request->request->get("mantisPath")) {
                    $request->request->set("mantisPath", rtrim($request->request->get('mantisPath'), '/'));
                }

                $scrubbedData = DataFunctionsService::scrubData($request->request->all());
                Settings::writeSettings(APP_ROOT, $scrubbedData, $logger);
                phpCollab\Util::headerFunction("../administration/admin.php?msg=update");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Admin: Update Settings',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}
$headBonus = <<<HEAD_BONUS
<script type="text/JavaScript">
    function showInfo(el, bool) {
        document.getElementById(el).style.display = (bool) ? "block" : "none";
    }
    
    document.addEventListener("DOMContentLoaded", function(event) {
        event.preventDefault();
        document.getElementById("mkdirMethodFTP").addEventListener("click", function(){
            showInfo("ftpInfo", true);
        });
        document.getElementById("mkdirMethodPHP").addEventListener("click", function(){
            showInfo("ftpInfo", false);
        });
        document.getElementById("notificationMethodMail").addEventListener("click", function(){
            showInfo("smtpInfo", false);
        });
        document.getElementById("notificationMethodSmtp").addEventListener("click", function(){
            showInfo("smtpInfo", true);
        });
        
        // Mantis toggle
        document.getElementById("mantisEnabledFalse").addEventListener("click", function(){
            showInfo("mantisInfo", false);
        });
        document.getElementById("mantisEnabledTrue").addEventListener("click", function(){
            showInfo("mantisInfo", true);
        });
    });
</script>
HEAD_BONUS;


include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"], "in"));
$blockPage->itemBreadcrumbs($strings["edit_settings"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->heading($strings["edit_settings"]);


$block1->form = "settings";
$block1->openForm("../administration/updatesettings.php", 'autocomplete="new-password"', $csrfHandler);

$ftpRoot = rtrim($ftpRoot, '/');

$tablePrefix = substr($tableCollab["projects"], 0, -8);

$myServer = MYSERVER;
$myLogin = MYLOGIN;
$myPassword = MYPASSWORD;
$myDatabase = MYDATABASE;
$versionOld = null;

echo <<<HTML
    	<input value="{$tablePrefix}" name="dbTablePrefix" type="hidden" />
		<input value="{$databaseType}" name="databaseType" type="hidden" />
		<input value="{$myServer}" name="dbServer" type="hidden" />
		<input value="{$myLogin}" name="dbLogin" type="hidden" />
		<input value="{$myPassword}" name="dbPassword" type="hidden" />
		<input value="{$myDatabase}" name="dbName" type="hidden" />
		<input value="{$tablePrefix}assignments" name="table_assignments" type="hidden" />
		<input value="{$tablePrefix}calendar" name="table_calendar" type="hidden" />
		<input value="{$tablePrefix}files" name="table_files" type="hidden" />
		<input value="{$tablePrefix}logs" name="table_logs" type="hidden" />
		<input value="{$tablePrefix}members" name="table_members" type="hidden" />
		<input value="{$tablePrefix}notes" name="table_notes" type="hidden" />
		<input value="{$tablePrefix}notifications" name="table_notifications" type="hidden" />
		<input value="{$tablePrefix}organizations" name="table_organizations" type="hidden" />
		<input value="{$tablePrefix}posts" name="table_posts" type="hidden" />
		<input value="{$tablePrefix}projects" name="table_projects" type="hidden" />
		<input value="{$tablePrefix}reports" name="table_reports" type="hidden" />
		<input value="{$tablePrefix}sorting" name="table_sorting" type="hidden" />
		<input value="{$tablePrefix}tasks" name="table_tasks" type="hidden" />
		<input value="{$tablePrefix}teams" name="table_teams" type="hidden" />
		<input value="{$tablePrefix}topics" name="table_topics" type="hidden" />
		<input value="{$tablePrefix}phases" name="table_phases" type="hidden" />
		<input value="{$tablePrefix}support_requests" name="table_support_requests" type="hidden" />
		<input value="{$tablePrefix}support_posts" name="table_support_posts" type="hidden" />
		<input value="{$tablePrefix}subtasks" name="table_subtasks" type="hidden" />
		<input value="{$tablePrefix}updates" name="table_updates" type="hidden" />
		<input value="{$tablePrefix}bookmarks" name="table_bookmarks" type="hidden" />
		<input value="{$tablePrefix}bookmarks_categories" name="table_bookmarks_categories" type="hidden" />
		<input value="{$tablePrefix}invoices" name="table_invoices" type="hidden" />
		<input value="{$tablePrefix}invoices_items" name="table_invoices_items" type="hidden" />
		<input value="{$tablePrefix}services" name="table_services" type="hidden" />
		<input value="{$tablePrefix}newsdeskcomments" name="table_newsdeskcomments" type="hidden" />
		<input value="{$tablePrefix}newsdeskposts" name="table_newsdeskposts" type="hidden" />
        <input value="{$loginMethod}" name="loginMethod" type="hidden" />
        <input value="{$supportType}" name="supportType" type="hidden" />
        <input value="{$supportEmail}" name="supportEmail" type="hidden" />
        <input value="{$urlContact}" name="urlContact" type="hidden" />
        <input value="{$siteTitle}" name="siteTitle" type="hidden" />
        <input value="{$origSetTitle}" name="setTitle" type="hidden" />
        <input value="{$setDescription}" name="setDescription" type="hidden" />
        <input value="{$setKeywords}" name="setKeywords" type="hidden" />
        <input value="{$pathToOpenssl}" name="pathToOpenssl" type="hidden" />       
        <input value="{$configLDAP["ldapserver"]}" name="configLDAPServer" type="hidden" />
        <input value="{$configLDAP["searchroot"]}" name="configLDAPSearchRoot" type="hidden" />
        <input value="{$fullPath}" name="fullPath" type="hidden" />
HTML;

        // Since echo doesn't output bool values as true/false strings, we are using ternary to do checks.
        echo '<input value="', $htaccessAuth ? 'true' : 'false','" name="htaccessAuth" type="hidden" />';
        echo '<input value="', $lastvisitedpage ? 'true' : 'false','" name="lastvisitedpage" type="hidden" />';
        echo '<input value="', $sitePublish ? 'true' : 'false','" name="sitePublish" type="hidden" />';
        echo '<input value="', $activeJpgraph ? 'true' : 'false','" name="activeJpgraph" type="hidden" />';
        echo '<input value="', $adminathome ? 'true' : 'false','" name="adminathome" type="hidden" />';
        echo '<input value="', $allowPhp ? 'true' : 'false','" name="allowPhp" type="hidden" />';
        echo '<input value="', $useLDAP ? 'true' : 'false','" name="useLDAP" type="hidden" />';
        echo '<input value="', $enableHelpSupport ? 'true' : 'false','" name="enableHelpSupport" type="hidden" />';
        echo '<input value="', $demoMode ? 'true' : 'false','" name="demoMode" type="hidden" />';
        echo '<input value="', $enableInvoicing ? 'true' : 'false','" name="enableInvoicing" type="hidden" />';
        echo '<input value="', $fileManagement ? 'true' : 'false','" name="fileManagement" type="hidden" />';
        echo '<input value="', $newsdesklimit ? 'true' : 'false','" name="newsdesklimit" type="hidden" />';
        echo '<input value="', $peerReview ? 'true' : 'false','" name="peerReview" type="hidden" />';

if ($version == $versionNew) {
    if (empty($versionOld)) {
        $versionOld = $version;
    }
    echo '<input value="' . $versionOld . '" name="phpCollabVersion" type="hidden">';
} else {
    echo '<input value="' . $version . '" name="phpCollabVersion" type="hidden">';
}

$mail = function_exists('mail') ? "on" : "off";

if ($mkdirMethod == "FTP") {
    $mkdirMethodFTP = "checked";
} else {
    $mkdirMethodPHP = "checked";
}
if ($notifications == "true") {
    $notificationTrue = "checked";
} else {
    $notificationFalse = "checked";
}
if ($forcedLogin == "true") {
    $forcedLoginTrue = "checked";
} else {
    $forcedLoginFalse = "checked";
}
if ($clientsFilter == "true") {
    $clientsFilterTrue = "checked";
} else {
    $clientsFilterFalse = "checked";
}
if ($updateChecker == "true") {
    $updateCheckerTrue = "checked";
} else {
    $updateCheckerFalse = "checked";
}
if ($gmtTimezone == (int)"true") {
    $gmtTimezoneTrue = "checked";
} else {
    $gmtTimezoneFalse = "checked";
}
if ($projectsFilter == "true") {
    $projectsFilterTrue = "checked";
} else {
    $projectsFilterFalse = "checked";
}

if ($footerDev == "true") {
    $footerDevTrue = "checked";
} else {
    $footerDevFalse = "checked";
}

if ($enableMantis == "true") {
    $enableMantisTrue = "checked";
} else {
    $enableMantisFalse = "checked";
}

if ($notificationMethod == "smtp") {
    $notificationMethodSMTP = "checked";
} else {
    $notificationMethodMail = "checked";
}

if ($installationType == "offline") {
    $installCheckOffline = "checked";
} else {
    $installCheckOnline = "checked";
}

// preference for home page
if ($showHomeBookmarks) {
    $checkedHomeBookmarks_t = "checked";
} else {
    $checkedHomeBookmarks_f = "checked";
}

if ($showHomeDiscussions) {
    $checkedHomeDiscussions_t = "checked";
} else {
    $checkedHomeDiscussions_f = "checked";
}

if ($showHomeNewsdesk) {
    $checkedHomeNewsdesk_t = "checked";
} else {
    $checkedHomeNewsdesk_f = "checked";
}

if ($showHomeNotes) {
    $checkedHomeNotes_t = "checked";
} else {
    $checkedHomeNotes_f = "checked";
}

if ($showHomeProjects) {
    $checkedHomeProjects_t = "checked";
} else {
    $checkedHomeProjects_f = "checked";
}

if ($showHomeReports) {
    $checkedHomeReports_t = "checked";
} else {
    $checkedHomeReports_f = "checked";
}

if ($showHomeTasks) {
    $checkedHomeTasks_t = "checked";
} else {
    $checkedHomeTasks_f = "checked";
}
if ($showHomeSubtasks) {
    $checkedHomeSubtasks_t = "checked";
} else {
    $checkedHomeSubtasks_f = "checked";
}

if ($autoPublishTasks) {
    $checkedAutoPublish_t = "checked";
} else {
    $checkedAutoPublish_f = "checked";
}

if ($emailAlerts === true) {
    $checkedEmailAlerts_t = "checked";
} else {
    $checkedEmailAlerts_f = "checked";
}

$block1->openContent("updateSettings");
$block1->contentTitle("General");

$block1->contentRow("Installation type",
    "<label><input type='radio' name='installationType' value='offline' $installCheckOffline /> Offline (firewall/intranet, no update checker)</label>
     <label><input type='radio' name='installationType' value='online' $installCheckOnline /> Online</label>");

$block1->contentRow("Update checker",
    "<label><input type='radio' name='updateChecker' value='false' $updateCheckerFalse /> False</label>
    <label><input type='radio' name='updateChecker' value='true' $updateCheckerTrue /> True</label>");

$ftpServer = (defined("FTPSERVER")) ? FTPSERVER : '';
$ftpServerLogin = (defined("FTPLOGIN")) ? FTPLOGIN : '';
$ftpServerPassword = (defined("FTPPASSWORD")) ? FTPPASSWORD : '';

if ($mkdirMethod == "PHP") {
    $ftpInfoStyle = 'style="display: none;"';
}

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">* Create folder method" {$blockPage->printHelp("setup_mkdirMethod")} : </td>
    <td>
        <table class="nonStriped" style="width: 500px;">
            <tr>
                <td style="vertical-align: top;">
                    <label><input type="radio" id="mkdirMethodPHP" name="mkdirMethod" value="PHP" {$mkdirMethodPHP} /> PHP</label>&nbsp;
                    <label><input type="radio" id="mkdirMethodFTP" name="mkdirMethod" value="FTP" {$mkdirMethodFTP} /> FTP</label>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 150px;">
                    <div id="ftpInfo" {$ftpInfoStyle}>
                    Ftp server <input size="44" value="{$ftpServer}" style="width: 200px" name="ftpServer" maxlength="100" type="text" autocomplete="new-password" /><br/>
                    Ftp login <input size="44" value="{$ftpServerLogin}" style="width: 200px" name="ftpLogin" maxlength="100" type="text" autocomplete="new-password" /><br/>
                    Ftp password <input size="44" value="{$ftpServerPassword}" style="width: 200px" name="ftpPassword" maxlength="100" type="password" autocomplete="new-password" /><br/>
                    Ftp root <input size="44" value="{$ftpRoot}" style="width: 200px" name="ftpRoot" maxlength="100" type="text" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
HTML;


$smptServer = (defined("SMTPSERVER")) ? SMTPSERVER : '';
$smptLogin = (defined("SMTPLOGIN")) ? SMTPLOGIN : '';
$smptPassword = (defined("SMTPPASSWORD")) ? SMTPPASSWORD : '';
$smptPort = (defined("SMTPPORT")) ? SMTPPORT : '';

if ($notificationMethod == "mail") {
    $smtpInfoStyle = 'style="display: none;"';
}

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">* Notification method{$blockPage->printHelp("setup_notificationMethod")} :</td>
    <td>
        <table class="nonStriped" style="width: 500px;">
            <tr>
                <td style="">
                    <label><input type="radio" id="notificationMethodMail" name="notificationMethod" value="mail" {$notificationMethodMail} /> PHP mail function</label> 
                    <label><input type="radio" id="notificationMethodSmtp" name="notificationMethod" value="smtp" {$notificationMethodSMTP} /> SMTP</label>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 150px;">
                    <div id="smtpInfo" $smtpInfoStyle>
                        Smtp server <input size="44" value="{$smptServer}" style="width: 200px" name="smtpServer" maxlength="100" type="text /"><br/>
                        Smtp login <input size="44" value="{$smptLogin}" style="width: 200px" name="smtpLogin" maxlength="100" type="text" /><br/>
                        Smtp password <input size="44" value="{$smptPassword}" style="width: 200px" name="smtpPassword" maxlength="100" type="password" /><br />
                        Smtp port <input size="44" value="{$smptPort}" style="width: 200px" name="smtpPort" maxlength="5" type="number" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr class="odd">
    <td class="leftvalue">* Theme :</td>
    <td><select name="theme">
HTML;

$dir = new DirectoryIterator(APP_ROOT . "/themes");
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        $selected = "";
        if ($fileinfo->getFilename() == THEME) {
            $selected = "selected";
        }
        echo '<option value="' . $fileinfo->getFilename() . '" ' . $selected . '>' . $fileinfo->getFilename() . '</option>';
    }
}
echo "</td></tr>";

$block1->contentRow("Notifications" . $blockPage->printHelp("setup_notifications"),
    "<label><input type='radio' name='notifications' value='false' $notificationFalse /> False</label>
     <label><input type='radio' name='notifications' value='true' $notificationTrue /> True<br/>[Mail $mail]</label>");

$block1->contentRow("Timezone (GMT)",
    "<label><input type='radio' name='gmtTimezone' value='false' $gmtTimezoneFalse /> False</label>
     <label><input type='radio' name='gmtTimezone' value='true' $gmtTimezoneTrue /> True</label>");

$block1->contentRow("* Forced login" . $blockPage->printHelp("setup_forcedlogin"),
    "<label><input type='radio' name='forcedLogin' value='false' $forcedLoginFalse /> False</label>
     <label><input type='radio' name='forcedLogin' value='true' $forcedLoginTrue  /> True</label>");

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">Default language{$blockPage->printHelp("setup_langdefault")} :</td>
    <td>
        <select name="defaultLanguage">
            <option value="">Not Selected</option>
            <option value="ar" {$langSelected["ar"]}>Arabic</option>
            <option value="az" {$langSelected["az"]}>Azerbaijani</option>
            <option value="pt-br"" {$langSelected["pt-br"]}>Brazilian Portuguese</option>
            <option value="bg" {$langSelected["bg"]}>Bulgarian</option>
            <option value="ca" {$langSelected["ca"]}>Catalan</option>
            <option value="zh" {$langSelected["zh"]}>Chinese simplified</option>
            <option value="zh-tw" {$langSelected["zh-tw"]}>Chinese traditional</option>
            <option value="cs-iso" {$langSelected["cs-iso"]}>Czech (iso)</option>
            <option value="cs-win1250" {$langSelected["cs-win1250"]}>Czech (win1250)</option>
            <option value="da" {$langSelected["da"]}>Danish</option>
            <option value="nl" {$langSelected["nl"]}>Dutch</option>
            <option value="en" {$langSelected["en"]}>English</option>
            <option value="et" {$langSelected["et"]}>Estonian</option>
            <option value="fr" {$langSelected["fr"]}>French</option>
            <option value="de" {$langSelected["de"]}>German</option>
            <option value="hu" {$langSelected["hu"]}>Hungarian</option>
            <option value="is" {$langSelected["is"]}>Icelandic</option>
            <option value="in" {$langSelected["in"]}>Indonesian</option>
            <option value="it" {$langSelected["it"]}>Italian</option>
            <option value="ko" {$langSelected["ko"]}>Korean</option>
            <option value="lv" {$langSelected["lv"]}>Latvian</option>
            <option value="no" {$langSelected["no"]}>Norwegian</option>
            <option value="pl" {$langSelected["pl"]}>Polish</option>
            <option value="pt" {$langSelected["pt"]}>Portuguese</option>
            <option value="ro" {$langSelected["ro"]}>Romanian</option>
            <option value="ru" {$langSelected["ru"]}>Russian</option>
            <option value="sk-win1250" {$langSelected["sk-win1250"]}>Slovak (win1250)</option>
            <option value="es" {$langSelected["es"]}>Spanish</option>
            <option value="tr" {$langSelected["tr"]}>Turkish</option>
            <option value="uk" {$langSelected["uk"]}>Ukrainian</option>
        </select>
    </td>
</tr>
HTML;


$block1->contentRow("* Site URL",
    "<input size='44' value='$root' style='width: 200px' name='siteUrl' maxlength='100' type='text' />");
$block1->contentRow("* Default max file size",
    "<input size='44' value='$maxFileSize' style='width: 200px' name='maxFileSize' maxlength='100' type='text' /> $byteUnits[0]");

$block1->contentTitle("Options");

$block1->contentRow("Clients filter" . $blockPage->printHelp("setup_clientsfilter"),
    "<label><input type='radio' name='clientsFilter' value='false' $clientsFilterFalse /> False</label>
     <label><input type='radio' name='clientsFilter' value='true' $clientsFilterTrue /> True</label>");
$block1->contentRow("Projects filter" . $blockPage->printHelp("setup_projectsfilter"),
    "<label><input type='radio' name='projectsFilter' value='false' $projectsFilterFalse /> False</label>
     <label><input type='radio' name='projectsFilter' value='true' $projectsFilterTrue /> True</label>");

$block1->contentRow('Show Bookmarks',
    '<label><input type="radio" name="showHomeBookmarks" value="false" ' . $checkedHomeBookmarks_f . ' /> False
     <label><input type="radio" name="showHomeBookmarks" value="true" ' . $checkedHomeBookmarks_t . ' /> True');
$block1->contentRow('Show Projects',
    '<label><input type="radio" name="showHomeProjects" value="false" ' . $checkedHomeProjects_f . ' /> False</label>
     <label><input type="radio" name="showHomeProjects" value="true" ' . $checkedHomeProjects_t . ' /> True</label>');
$block1->contentRow('Show Tasks',
    '<label><input type="radio" name="showHomeTasks" value="false" ' . $checkedHomeTasks_f . ' /> False</label>
     <label><input type="radio" name="showHomeTasks" value="true" ' . $checkedHomeTasks_t . ' /> True</label>');
$block1->contentRow('Show Subtasks',
    '<label><input type="radio" name="showHomeSubtasks" value="false" ' . $checkedHomeSubtasks_f . ' /> False</label>
     <label><input type="radio" name="showHomeSubtasks" value="true" ' . $checkedHomeSubtasks_t . ' /> True</label>');
$block1->contentRow('Show Discussions',
    '<label><input type="radio" name="showHomeDiscussions" value="false" ' . $checkedHomeDiscussions_f . ' /> False</label>
     <label><input type="radio" name="showHomeDiscussions" value="true" ' . $checkedHomeDiscussions_t . ' /> True</label>');
$block1->contentRow('Show Reports',
    '<label><input type="radio" name="showHomeReports" value="false" ' . $checkedHomeReports_f . ' /> False</label>
     <label><input type="radio" name="showHomeReports" value="true" ' . $checkedHomeReports_t . ' /> True</label>');
$block1->contentRow('Show Notes',
    '<label><input type="radio" name="showHomeNotes" value="false" ' . $checkedHomeNotes_f . ' /> False</label>
     <label><input type="radio" name="showHomeNotes" value="true" ' . $checkedHomeNotes_t . ' /> True</label>');
$block1->contentRow('Show NewsDesk',
    '<label><input type="radio" name="showHomeNewsdesk" value="false" ' . $checkedHomeNewsdesk_f . ' /> False</label>
     <label><input type="radio" name="showHomeNewsdesk" value="true" ' . $checkedHomeNewsdesk_t . ' /> True</label>');
$block1->contentRow('Auto-publish Tasks',
    '<label><input type="radio" name="autoPublishTasks" value="false" ' . $checkedAutoPublish_f . ' /> False</label>
     <label><input type="radio" name="autoPublishTasks" value="true" ' . $checkedAutoPublish_t . ' /> True</label>');
$block1->contentRow('Email Alerts',
    '<label><input type="radio" name="emailAlerts" value="false" ' . $checkedEmailAlerts_f . ' /> False</label>
     <label><input type="radio" name="emailAlerts" value="true" ' . $checkedEmailAlerts_t . ' /> True</label>');

$block1->contentTitle("Advanced");

if ($enableMantis === false) {
    $mantisInfoStyle = 'style="display: none;"';
}

echo <<<MANTIS
<tr class="odd">
    <td class="leftvalue">Mantis integration : </td>
    <td>
        <table class="nonStriped" style="width: 500px;">
            <tr>
                <td style="">
                    <label><input type="radio" name="enableMantis" id="mantisEnabledFalse" value="false" {$enableMantisFalse} /> False</label>
                    <label><input type="radio" name="enableMantis" id="mantisEnabledTrue" value="true" {$enableMantisTrue} /> True</label>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 150px;">
                    <div id="mantisInfo" $mantisInfoStyle>
                        Mantis URL <input size="44" value="{$pathMantis}" style="width: 200px" name="mantisPath" maxlength="100" type="text" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
MANTIS;

if (isset($logLevels) && isset($logLevel)) {
    echo <<< LOGLEVEL
    <tr class="odd">
        <td class="leftvalue">
        Log Level {$blockPage->printHelp("logLevels", "VAUTO,WIDTH,500,CAPTION, 'Note: NEVER use a log level below 400 in production environment!'")} :
        </td>
        <td class="nonStriped" style="padding-left: 10px;">
LOGLEVEL;

    echo '<select name="logLevel">';

    foreach( $logLevels as $key => $levelDesc ) {
        echo   '<option value="' . $key . '"';
        echo (($key == $logLevel)) ? 'selected="selected"' : '';
        echo ">{$levelDesc}</option>";
    }

    echo '</select>';

    echo <<< LOGLEVEL
        </td>
    </tr>
LOGLEVEL;
}

$block1->contentRow("Extended footer (dev)",
    "<label><input type='radio' name='footerDev' value='false' $footerDevFalse /> False</label>
     <label><input type='radio' name='footerDev' value='true' $footerDevTrue /> True</label>");

$block1->contentRow("", "<button type='SUBMIT' value='generate' name='action'>" . $strings["save"] . "</button>");


$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
