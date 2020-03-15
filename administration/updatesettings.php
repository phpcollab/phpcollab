<?php

use phpCollab\DataFunctions;

/*
** Application name: phpCollab
** Last Edit page: 2005-03-08
** Path by root: ../administration/updatesettings.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
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


$checkSession = "true";
include_once '../includes/library.php';
$setTitle .= " : Edit Settings";

if ($profilSession != "0") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$langSelected = $GLOBALS["langSelected"];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_GET["action"] == "generate") {
        if ($_POST["installationTypeNew"] == "offline") {
            $updateCheckerNew = "false";
        }

        if (substr($_POST["rootNew"], -1) == "/") {
            $rootNew = substr($_POST["rootNew"], 0, -1);
        }

        if (substr($_POST["ftpRootNew"], -1) == "/") {
            $ftpRootNew = substr($_POST["ftpRootNew"], 0, -1);
        }

        if (substr($_POST["pathMantisNew"], -1) != "/") {
            $pathMantisNew = $_POST["pathMantisNew"] . "/";
        }

        // DAB - scrub the data
        $dataFunction = new DataFunctions();
        $scrubbedData = $dataFunction->scrubData($_POST);
        extract($scrubbedData);
        // -- END Paranoia

        $content = <<<STAMP
<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings.php

# installation type
\$installationType = "{$scrubbedData["installationTypeNew"]}"; //select "offline" or "online"

# select database application
\$databaseType = "{$scrubbedData["databaseTypeNew"]}"; //select "sqlserver", "postgresql" or "mysql"

# database parameters
define('MYSERVER','{$scrubbedData["myserverNew"]}');
define('MYLOGIN','{$scrubbedData["myloginNew"]}');
define('MYPASSWORD','{$scrubbedData["mypasswordNew"]}');
define('MYDATABASE','{$scrubbedData["mydatabaseNew"]}');

# notification method
\$notificationMethod = "{$scrubbedData["notificationMethodNew"]}"; //select "mail" or "smtp"

# smtp parameters (only if \$notificationMethod == "smtp")
define('SMTPSERVER','{$scrubbedData["smtpserverNew"]}');
define('SMTPLOGIN','{$scrubbedData["smtploginNew"]}');
define('SMTPPASSWORD','{$scrubbedData["smtppasswordNew"]}');
define('SMTPPORT','{$scrubbedData["smtpPortNew"]}');

# create folder method
\$mkdirMethod = "{$scrubbedData["mkdirMethodNew"]}"; //select "FTP" or "PHP"

# ftp parameters (only if \$mkdirMethod == "FTP")
define('FTPSERVER','{$scrubbedData["ftpserverNew"]}');
define('FTPLOGIN','{$scrubbedData["ftploginNew"]}');
define('FTPPASSWORD','{$scrubbedData["ftppasswordNew"]}');

# PhpCollab root according to ftp account (only if \$mkdirMethod == "FTP")
\$ftpRoot = "{$scrubbedData["ftpRootNew"]}"; //no slash at the end

# Invoicing module
\$enableInvoicing = "true";

# theme choice
define('THEME','{$scrubbedData["mythemeNew"]}');

# newsdesk limiter
\$newsdesklimit = 1;

# if 1 the admin logs in his homepage
\$adminathome = 0;

# timezone GMT management
\$gmtTimezone = "{$scrubbedData["gmtTimezoneNew"]}";

# language choice
\$langDefault = "{$scrubbedData["langNew"]}";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
\$enableMantis = "{$scrubbedData["mantisNew"]}";

// Mantis installation directory
\$pathMantis = "$pathMantisNew";  // add slash at the end

# https related parameters
\$pathToOpenssl = "/usr/bin/openssl";

# login method, set to "CRYPT"
\$loginMethod = "{$scrubbedData["loginMethodNew"]}"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
\$useLDAP = "false";
\$configLDAP["ldapserver"] = "your.ldap.server.address";
\$configLDAP["searchroot"] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
\$htaccessAuth = "false";
\$fullPath = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
\$fileManagement = "true";
\$maxFileSize = {$scrubbedData["maxFileSizeNew"]}; //bytes limit for upload
\$root = "{$scrubbedData["rootNew"]}"; //no slash at the end

# security issue to disallow php files upload
\$allowPhp = "false";

# project site creation
\$sitePublish = "true";

# enable update checker
\$updateChecker = "{$scrubbedData["updateCheckerNew"]}";

# e-mail notifications
\$notifications = "{$scrubbedData["notificationsNew"]}";

# show peer review area
\$peerReview = "true";

# show items for home
\$showHomeBookmarks = {$scrubbedData["showHomeBookmarksNew"]};
\$showHomeProjects = {$scrubbedData["showHomeProjectsNew"]};
\$showHomeTasks = {$scrubbedData["showHomeTasksNew"]};
\$showHomeDiscussions = {$scrubbedData["showHomeDiscussionsNew"]};
\$showHomeReports = {$scrubbedData["showHomeReportsNew"]};
\$showHomeNotes = {$scrubbedData["showHomeNotesNew"]};
\$showHomeNewsdesk = {$scrubbedData["showHomeNewsdeskNew"]};
\$showHomeSubtasks = {$scrubbedData["showHomeSubtasksNew"]};

# security issue to disallow auto-login from external link
\$forcedLogin = "{$scrubbedData["forcedloginNew"]}";

# table prefix
\$tablePrefix = "{$scrubbedData["tablePrefixNew"]}";

# database tables
\$tableCollab["assignments"] = "{$scrubbedData["table_assignments"]}";
\$tableCollab["calendar"] = "{$scrubbedData["table_calendar"]}";
\$tableCollab["files"] = "{$scrubbedData["table_files"]}";
\$tableCollab["logs"] = "{$scrubbedData["table_logs"]}";
\$tableCollab["members"] = "{$scrubbedData["table_members"]}";
\$tableCollab["notes"] = "{$scrubbedData["table_notes"]}";
\$tableCollab["notifications"] = "{$scrubbedData["table_notifications"]}";
\$tableCollab["organizations"] = "{$scrubbedData["table_organizations"]}";
\$tableCollab["posts"] = "{$scrubbedData["tablescrubedDatas"]}";
\$tableCollab["projects"] = "{$scrubbedData["table_projects"]}";
\$tableCollab["reports"] = "{$scrubbedData["table_reports"]}";
\$tableCollab["sorting"] = "{$scrubbedData["table_sorting"]}";
\$tableCollab["tasks"] = "{$scrubbedData["table_tasks"]}";
\$tableCollab["teams"] = "{$scrubbedData["table_teams"]}";
\$tableCollab["topics"] = "{$scrubbedData["table_topics"]}";
\$tableCollab["phases"] = "{$scrubbedData["table_phases"]}";
\$tableCollab["support_requests"] = "{$scrubbedData["table_support_requests"]}";
\$tableCollab["supportscrubedDatas"] = "{$scrubbedData["table_supportscrubedDatas"]}";
\$tableCollab["subtasks"] = "{$scrubbedData["table_subtasks"]}";
\$tableCollab["updates"] = "{$scrubbedData["table_updates"]}";
\$tableCollab["bookmarks"] = "{$scrubbedData["table_bookmarks"]}";
\$tableCollab["bookmarks_categories"] = "{$scrubbedData["table_bookmarks_categories"]}";
\$tableCollab["invoices"] = "{$scrubbedData["table_invoices"]}";
\$tableCollab["invoices_items"] = "{$scrubbedData["table_invoices_items"]}";
\$tableCollab["services"] = "{$scrubbedData["table_services"]}";
\$tableCollab["newsdeskcomments"] = "{$scrubbedData["table_newsdeskcomments"]}";
\$tableCollab["newsdeskposts"] = "{$scrubbedData["table_newsdeskposts"]}";

# PhpCollab version
\$version = "$version";

# demo mode parameters
\$demoMode = "false";
\$urlContact = "http://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
\$activeJpgraph = "true";

# developement options in footer
\$footerDev = "{$scrubbedData["footerdevNew"]}";

# filter to see only logged user clients (in team / owner)
\$clientsFilter = "{$scrubbedData["clientsFilterNew"]}";

# filter to see only logged user projects (in team / owner)
\$projectsFilter = "{$scrubbedData["projectsFilterNew"]}";

# Enable help center support requests, values "true" or "false"
\$enableHelpSupport = "true";

# Return email address given for clients to respond too.
\$supportEmail = "email@yourdomain.com";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new Request is added
\$supportType = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
\$lastvisitedpage = false;

# auto-publish tasks?
\$autoPublishTasks = {$scrubbedData["autoPublishTasksNew"]};

# html header parameters
\$setDoctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
\$setTitle = "PhpCollab";
\$setDescription = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
\$setKeywords = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

# Email alerts
\$emailAlerts = {$scrubbedData["emailAlertsNew"]};

STAMP;

        if (!@fopen("../includes/settings.php", 'wb+')) {
            $msg = "settingsNotwritable";
        } else {
            $fp = @fopen("../includes/settings.php", 'wb+');
            $fw = @fwrite($fp, $content);

            if (!$fw) {
                $msg = "settingsNotwritable";
                fclose($fp);
            } else {
                fclose($fp);
                phpCollab\Util::headerFunction("../administration/admin.php?msg=update");
            }
        }

    }
}
$headBonus =
    <<<HEAD_BONUS
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
    });
</script>
HEAD_BONUS;


include APP_ROOT . '/themes/' . THEME . '/header.php';

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


$block1->openContent();
$block1->contentTitle("General");
$block1->form = "settings";
$block1->openForm("../administration/updatesettings.php?action=generate", 'autocomplete="new-password"');

if (substr($ftpRoot, -1) == "/") {
    $ftpRoot = substr($ftpRoot, 0, -1);
}

$tablePrefix = substr($tableCollab["projects"], 0, -8);

$myServer = MYSERVER;
$myLogin = MYLOGIN;
$myPassword = MYPASSWORD;
$myDatabase = MYDATABASE;
$versionOld = null;

echo <<<HTML
    	<input value="$tablePrefix" name="tablePrefixNew" type="hidden" />
		<input value="$databaseType" name="databaseTypeNew" type="hidden" />
		<input value="{$myServer}" name="myserverNew" type="hidden" />
		<input value="{$myLogin}" name="myloginNew" type="hidden" />
		<input value="{$myPassword}" name="mypasswordNew" type="hidden" />
		<input value="{$myDatabase}" name="mydatabaseNew" type="hidden" />
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
        <input value="{$loginMethod}" name="loginMethodNew" type="hidden" />
HTML;

if ($version == $versionNew) {
    if ($versionOld == "") {
        $versionOld = $version;
    }
    echo "<input value=\"$versionOld\" name=\"versionOldNew\" type=\"hidden\">";
} else {
    echo "<input value=\"$version\" name=\"versionOldNew\" type=\"hidden\">";
}

$notificationsTest = function_exists('mail');
if ($notificationsTest == "true") {
    $mail = "on";
} else {
    $mail = "off";
}

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
if ($gmtTimezone == "true") {
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

$block1->contentRow("Installation type", "<input type='radio' name='installationTypeNew' value='offline' $installCheckOffline /> Offline (firewall/intranet, no update checker)&nbsp;<input type='radio' name='installationTypeNew' value='online' $installCheckOnline /> Online");

$block1->contentRow("Update checker", "<input type='radio' name='updateCheckerNew' value='false' $updateCheckerFalse /> False&nbsp;<input type='radio' name='updateCheckerNew' value='true' $updateCheckerTrue /> True");


$ftpServer = FTPSERVER;
$ftpServerLogin = FTPLOGIN;
$ftpServerPassword = FTPPASSWORD;

if ($mkdirMethod == "PHP") {
    $ftpInfoStyle = 'style="display: none;"';
}

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">* Create folder method" {$blockPage->printHelp("setup_mkdirMethod")}</td>
    <td>
        <table class="nonStriped" style="width: 500px;">
            <tr>
                <td style="vertical-align: top;">
                    <input type="radio" id="mkdirMethodPHP" name="mkdirMethodNew" value="PHP" {$mkdirMethodPHP} /> PHP&nbsp;
                    <input type="radio" id="mkdirMethodFTP" name="mkdirMethodNew" value="FTP" {$mkdirMethodFTP} /> FTP
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 150px;">
                    <div id="ftpInfo" {$ftpInfoStyle}>
                    Ftp server <input size="44" value="{$ftpServer}" style="width: 200px" name="ftpserverNew" maxlength="100" type="text" autocomplete="new-password" /><br/>
                    Ftp login <input size="44" value="{$ftpServerLogin}" style="width: 200px" name="ftploginNew" maxlength="100" type="text" autocomplete="new-password" /><br/>
                    Ftp password <input size="44" value="{$ftpServerPassword}" style="width: 200px" name="ftppasswordNew" maxlength="100" type="password" autocomplete="new-password" /><br/>
                    Ftp root <input size="44" value="{$ftpRoot}" style="width: 200px" name="ftpRootNew" maxlength="100" type="text" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
HTML;


$smptServer = SMTPSERVER;
$smptLogin = SMTPLOGIN;
$smptPassword = SMTPPASSWORD;
$smptPort = SMTPPORT;

if ($notificationMethod == "mail") {
    $smtpInfoStyle = 'style="display: none;"';
}

echo <<< HTML
<tr class="odd">
    <td class="leftvalue">* Notification method{$blockPage->printHelp("setup_notificationMethod")} </td>
    <td>
        <table class="nonStriped" style="width: 500px;">
            <tr>
                <td style="">
                    <input type="radio" id="notificationMethodMail" name="notificationMethodNew" value="mail" {$notificationMethodMail} /> PHP mail function&nbsp;
                    <input type="radio" id="notificationMethodSmtp" name="notificationMethodNew" value="smtp" {$notificationMethodSMTP} /> SMTP
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding-right: 150px;">
                    <div id="smtpInfo" $smtpInfoStyle>
                        Smtp server <input size="44" value="{$smptServer}" style="width: 200px" name="smtpserverNew" maxlength="100" type="text /"><br/>
                        Smtp login <input size="44" value="{$smptLogin}" style="width: 200px" name="smtploginNew" maxlength="100" type="text" /><br/>
                        Smtp password <input size="44" value="{$smptPassword}" style="width: 200px" name="smtppasswordNew" maxlength="100" type="password" /><br />
                        Smtp port <input size="44" value="{$smptPort}" style="width: 200px" name="smtpPortNew" maxlength="5" type="number" />
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr class="odd">
    <td class="leftvalue">* Theme :</td>
    <td><select name="mythemeNew">
HTML;

$dir = new DirectoryIterator(APP_ROOT . "/themes");
foreach ($dir as $fileinfo) {
    if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        $selected = "";
        if ($fileinfo->getFilename() == THEME) {
            $selected = "selected";
        }
        echo '<option value="' . $fileinfo->getFilename() . '" '. $selected .'>'. $fileinfo->getFilename() . '</option>';
    }
}
echo "</td></tr>";

$block1->contentRow("Notifications" . $blockPage->printHelp("setup_notifications"), "<input type='radio' name='notificationsNew' value='false' $notificationFalse /> False&nbsp;<input type='radio' name='notificationsNew' value='true' $notificationTrue /> True<br/>[Mail $mail]");

$block1->contentRow("Timezone (GMT)", "<input type='radio' name='gmtTimezoneNew' value='false' $gmtTimezoneFalse /> False&nbsp;<input type='radio' name='gmtTimezoneNew' value='true' $gmtTimezoneTrue /> True");

$block1->contentRow("* Forced login" . $blockPage->printHelp("setup_forcedlogin"), "<input type='radio' name='forcedloginNew' value='false' $forcedLoginFalse /> False&nbsp;<input type='radio' name='forcedloginNew' value='true' $forcedLoginTrue  /> True");

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">Default language{$blockPage->printHelp("setup_langdefault")}</td><td>
        <select name="langNew">
            <option value="">Blank</option>
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



$block1->contentRow("* Root", "<input size='44' value='$root' style='width: 200px' name='rootNew' maxlength='100' type='text' />");
$block1->contentRow("* Default max file size", "<input size='44' value='$maxFileSize' style='width: 200px' name='maxFileSizeNew' maxlength='100' type='text' /> $byteUnits[0]");

$block1->contentTitle("Options");

$block1->contentRow("Clients filter" . $blockPage->printHelp("setup_clientsfilter"), "<input type='radio' name='clientsFilterNew' value='false' $clientsFilterFalse /> False&nbsp;<input type='radio' name='clientsFilterNew' value='true' $clientsFilterTrue /> True");
$block1->contentRow("Projects filter" . $blockPage->printHelp("setup_projectsfilter"), "<input type='radio' name='projectsFilterNew' value='false' $projectsFilterFalse /> False&nbsp;<input type='radio' name='projectsFilterNew' value='true' $projectsFilterTrue /> True");

$block1->contentRow('Show Bookmarks', '<input type="radio" name="showHomeBookmarksNew" value="false" ' . $checkedHomeBookmarks_f . ' /> False&nbsp;<input type="radio" name="showHomeBookmarksNew" value="true" ' . $checkedHomeBookmarks_t . ' /> True');
$block1->contentRow('Show Projects', '<input type="radio" name="showHomeProjectsNew" value="false" ' . $checkedHomeProjects_f . ' /> False&nbsp;<input type="radio" name="showHomeProjectsNew" value="true" ' . $checkedHomeProjects_t . ' /> True');
$block1->contentRow('Show Tasks', '<input type="radio" name="showHomeTasksNew" value="false" ' . $checkedHomeTasks_f . ' /> False&nbsp;<input type="radio" name="showHomeTasksNew" value="true" ' . $checkedHomeTasks_t . ' /> True');
$block1->contentRow('Show Subtasks', '<input type="radio" name="showHomeSubtasksNew" value="false" ' . $checkedHomeSubtasks_f . ' /> False&nbsp;<input type="radio" name="showHomeSubtasksNew" value="true" ' . $checkedHomeSubtasks_t . ' /> True');
$block1->contentRow('Show Discussions', '<input type="radio" name="showHomeDiscussionsNew" value="false" ' . $checkedHomeDiscussions_f . ' /> False&nbsp;<input type="radio" name="showHomeDiscussionsNew" value="true" ' . $checkedHomeDiscussions_t . ' /> True');
$block1->contentRow('Show Reports', '<input type="radio" name="showHomeReportsNew" value="false" ' . $checkedHomeReports_f . ' /> False&nbsp;<input type="radio" name="showHomeReportsNew" value="true" ' . $checkedHomeReports_t . ' /> True');
$block1->contentRow('Show Notes', '<input type="radio" name="showHomeNotesNew" value="false" ' . $checkedHomeNotes_f . ' /> False&nbsp;<input type="radio" name="showHomeNotesNew" value="true" ' . $checkedHomeNotes_t . ' /> True');
$block1->contentRow('Show NewsDesk', '<input type="radio" name="showHomeNewsdeskNew" value="false" ' . $checkedHomeNewsdesk_f . ' /> False&nbsp;<input type="radio" name="showHomeNewsdeskNew" value="true" ' . $checkedHomeNewsdesk_t . ' /> True');
$block1->contentRow('Auto-publish Tasks', '<input type="radio" name="autoPublishTasksNew" value="false" ' . $checkedAutoPublish_f . ' /> False&nbsp;<input type="radio" name="autoPublishTasksNew" value="true" ' . $checkedAutoPublish_t . ' /> True');
$block1->contentRow('Email Alerts', '<input type="radio" name="emailAlertsNew" value="false" ' . $checkedEmailAlerts_f . ' /> False&nbsp;<input type="radio" name="emailAlertsNew" value="true" ' . $checkedEmailAlerts_t . ' /> True');

$block1->contentTitle("Advanced");

$block1->contentRow("Extended footer (dev)", "<input type='radio' name='footerdevNew' value='false' $footerDevFalse /> False&nbsp;<input type='radio' name='footerdevNew' value='true' $footerDevTrue /> True");

$block1->contentRow("Mantis integration", "<input type='radio' name='mantisNew' value='false' $enableMantisFalse /> False&nbsp;<input type='radio' name='mantisNew' value='true' $enableMantisTrue /> True");

$block1->contentRow("Mantis url", "<input size='44' value='$pathMantis' style='width: 200px' name='pathMantisNew' maxlength='100' type='text' />");

$block1->contentRow("", "<input type='SUBMIT' value='" . $strings["save"] . "' />");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
