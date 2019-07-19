<?php
/*
** Application name: phpCollab
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** DESC: Screen: setup file
**
** =============================================================================
*/

use phpCollab\DataFunctions;

error_reporting(2039);

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$help = [];
require_once '../languages/help_en.php';

$step = $_GET["step"];
$redirect = $_GET["redirect"];
$connection = (!empty($_GET["connection"])) ? $_GET["connection"] : $_POST["connection"];

if ($redirect == "true" && $step == "2") {
    header("Location:../installation/setup.php?step=2&connection={$connection}");
}


$version = "2.7.0";

$dateheure = date("Y-m-d H:i");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if ($_POST["action"] == "generate") {
        if (empty($_POST["dbServer"])) {
            $error = $help["setup_error_database_server"];
        } elseif (empty($_POST["dbLogin"])) {
            $error = $help["setup_error_database_login"];
        } elseif (empty($_POST["dbName"])) {
            $error = $help["setup_error_database_name"];
        } elseif (empty($_POST["siteUrl"])) {
            $error = $help["setup_error_site_url"];
        } elseif (empty($_POST["adminPassword"])) {
            $error = $help["setup_error_admin_password"];
        }

        if ($_POST["installationType"] == "offline") {
            $updatechecker = "false";
        }

        if (substr($_POST["siteUrl"], -1) == "/") {
            $siteUrl = substr($_POST["siteUrl"], 0, -1);
        }
        if (substr($_POST["ftpRoot"], -1) == "/") {
            $ftpRoot = substr($_POST["ftpRoot"], 0, -1);
        }

        if (!$error) {
            //Let's also get pretty paranoid here ;)
            $dataFunctions = new DataFunctions();
            $scrubedData = $dataFunctions->scrubData($_POST);
            extract($scrubedData);

            $updatechecker = $scrubedData["updateChecker"];
            $action = $scrubedData["action"];
            $installationType = $scrubedData["installationType"];
            $databaseType = $scrubedData["databaseType"];
            $dbServer = $scrubedData["dbServer"];
            $dbLogin = $scrubedData["dbLogin"];
            $dbPassword = $scrubedData["dbPassword"];
            $dbName = $scrubedData["dbName"];
            $dbTablePrefix = $scrubedData["dbTablePrefix"];
            $mkdirMethod = $scrubedData["mkdirMethod"];
            $notifications = $scrubedData["notifications"];
            $forcedlogin = $scrubedData["forcedLogin"];
            $defaultLanguage = $scrubedData["defaultLanguage"];
            $siteUrl = $scrubedData["siteUrl"];
            $loginMethod = $scrubedData["loginMethod"];
            $adminPassword = $scrubedData["adminPassword"];
            $ftpServer = $scrubedData["ftpServer"];
            $ftpUrl = $scrubedData["ftpUrl"];
            $ftpLogin = $scrubedData["ftpLogin"];
            $ftpPassword = $scrubedData["ftpPassword"];
            // -- END Paranoia
            try {
                $msg = '';
                /**
                 * Check to see if the database is connectable
                 */
                switch ($databaseType) {
                    case "mysql":
                        $conn = new PDO("mysql:host=$dbServer;dbname=$dbName", $dbLogin, $dbPassword);
                        break;
                    case "sqlserver":
                        $conn = new PDO("dblib:host=$dbServer;dbname=$dbName", $dbLogin, $dbPassword);
                        break;
                    case "postgresql":
                        $conn = new PDO("pgsql:host=$dbServer;port=5432;dbname=$dbName;user=$dbLogin;password=$dbPassword");
                        break;
                    default:
                        $error = $help["setup_error_database"];
                        throw new Exception($help["setup_error_database"]);
                }
                // set the PDO error mode to exception
                if (!empty($conn)) {
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } else {
                    throw new Exception("yo momma");
                }

                /**
                 * See if the includes directory is writable
                 */
                $isWritable = is_writable("../includes");

                if (!$isWritable) {
                    $error = 1;
                    throw new Exception("It appears that the include directory is not writable. Please correct and try again.");
                }

                /**
                 * Create Database tables
                 */
                // crypt admin and demo password
                $demoPwd = phpCollab\Util::getPassword("demo");
                $adminPassword = phpCollab\Util::getPassword($adminPassword);

                // create all tables
                include '../includes/db_var.inc.php';
                include '../includes/setup_db.php';

                if (!empty($conn)) {
                    foreach ($SQL as $sqlStatement) {
                        try {
                            $conn->exec($sqlStatement);
                        } catch (PDOException $e) {
                            $error = $e->getMessage();
                            die('what happened?');
                        }
                    }

                    $msg .= "<p>Tables and settings file created correctly.</p>";
                }

                /**
                 * Write the settings file
                 */
                $content = <<<STAMP
<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings.php

# installation type
\$installationType = "$installationType"; //select "offline" or "online"

# select database application
\$databaseType = "$databaseType"; //select "sqlserver", "postgresql" or "mysql"

# database parameters
define('MYSERVER','$dbServer');
define('MYLOGIN','$dbLogin');
define('MYPASSWORD','$dbPassword');
define('MYDATABASE','$dbName');

# notification method
\$notificationMethod = "mail"; //select "mail" or "smtp"

# smtp parameters (only if \$notificationMethod == "smtp")
define('SMTPSERVER','');
define('SMTPLOGIN','');
define('SMTPPASSWORD','');

# create folder method
\$mkdirMethod = "$mkdirMethod"; //select "FTP" or "PHP"

# ftp parameters (only if \$mkdirMethod == "FTP")
define('FTPSERVER','$ftpServer');
define('FTPLOGIN','$ftpLogin');
define('FTPPASSWORD','$ftpPassword');

# PhpCollab root according to ftp account (only if \$mkdirMethod == "FTP")
\$ftpRoot = "$ftpRoot"; //no slash at the end

# Invoicing module
\$enableInvoicing = "true";

# theme choice
define('THEME','default');

# newsdesk limiter
\$newsdesklimit = 1; 

# if 1 the admin logs in his homepage
\$adminathome = 0;

# timezone GMT management
\$gmtTimezone = "false";

# language choice
\$langDefault = "$defaultLanguage";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
\$enableMantis = "false";

// Mantis installation directory
\$pathMantis = "http://localhost/mantis/";  // add slash at the end

# https related parameters
\$pathToOpenssl = "/usr/bin/openssl";

# login method, set to "CRYPT"
\$loginMethod = "$loginMethod"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
\$useLDAP = "false";
\$configLDAP["ldapserver"] = "your.ldap.server.address";
\$configLDAP["searchroot"] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
\$htaccessAuth = "false";
\$fullPath = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
\$fileManagement = "true";
\$maxFileSize = 51200; //bytes limit for upload
\$root = "$siteUrl"; //no slash at the end

# security issue to disallow php files upload
\$allowPhp = "false";

# project site creation
\$sitePublish = "true";

# enable update checker
\$updateChecker = "$updatechecker";

# e-mail notifications
\$notifications = "$notifications";

# show peer review area
\$peerReview = "true";

# show items for home
\$showHomeBookmarks =  "true";
\$showHomeProjects =  "true";
\$showHomeTasks =  "true";
\$showHomeSubtasks =  "true";
\$showHomeDiscussions =  "true";
\$showHomeReports =  "true";
\$showHomeNotes =  "true";
\$showHomeNewsdesk =  "true";

# security issue to disallow auto-login from external link
\$forcedLogin = "$forcedlogin";

# table prefix
\$tablePrefix = "$dbTablePrefix";

# database tables
\$tableCollab["assignments"] = "{$dbTablePrefix}assignments";
\$tableCollab["calendar"] = "{$dbTablePrefix}calendar";
\$tableCollab["files"] = "{$dbTablePrefix}files";
\$tableCollab["logs"] = "{$dbTablePrefix}logs";
\$tableCollab["members"] = "{$dbTablePrefix}members";
\$tableCollab["notes"] = "{$dbTablePrefix}notes";
\$tableCollab["notifications"] = "{$dbTablePrefix}notifications";
\$tableCollab["organizations"] = "{$dbTablePrefix}organizations";
\$tableCollab["posts"] = "{$dbTablePrefix}posts";
\$tableCollab["projects"] = "{$dbTablePrefix}projects";
\$tableCollab["reports"] = "{$dbTablePrefix}reports";
\$tableCollab["sorting"] = "{$dbTablePrefix}sorting";
\$tableCollab["tasks"] = "{$dbTablePrefix}tasks";
\$tableCollab["teams"] = "{$dbTablePrefix}teams";
\$tableCollab["topics"] = "{$dbTablePrefix}topics";
\$tableCollab["phases"] = "{$dbTablePrefix}phases";
\$tableCollab["support_requests"] = "{$dbTablePrefix}support_requests";
\$tableCollab["support_posts"] = "{$dbTablePrefix}support_posts";
\$tableCollab["subtasks"] = "{$dbTablePrefix}subtasks";
\$tableCollab["updates"] = "{$dbTablePrefix}updates";
\$tableCollab["bookmarks"] = "{$dbTablePrefix}bookmarks";
\$tableCollab["bookmarks_categories"] = "{$dbTablePrefix}bookmarks_categories";
\$tableCollab["invoices"] = "{$dbTablePrefix}invoices";
\$tableCollab["invoices_items"] = "{$dbTablePrefix}invoices_items";
\$tableCollab["services"] = "{$dbTablePrefix}services";
\$tableCollab["newsdeskcomments"] = "{$dbTablePrefix}newsdeskcomments";
\$tableCollab["newsdeskposts"] = "{$dbTablePrefix}newsdeskposts";

# PhpCollab version
\$version = "$version";

# demo mode parameters
\$demoMode = "false";
\$urlContact = "http://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
\$activeJpgraph = "true";

# developement options in footer
\$footerDev = "false";

# filter to see only logged user clients (in team / owner)
\$clientsFilter = "false";

# filter to see only logged user projects (in team / owner)
\$projectsFilter = "false";

# Enable help center support requests, values "true" or "false"
\$enableHelpSupport = "true";

# Return email address given for clients to respond too.
\$supportEmail = "email@yourdomain.com";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new request is added
\$supportType = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
\$lastvisitedpage = false;

# auto-publish tasks added from client site?
\$autoPublishTasks = false;

# html header parameters
\$setDoctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
\$setTitle = "PhpCollab";
\$setDescription = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
\$setKeywords = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";
?>
STAMP;


                $fp = @fopen("../includes/settings.php", 'wb+');
                if (!$fp) {
                    $error = 1;
                    throw new Exception("<br/><b>PANIC! <br/> settings.php can't be opened!</b><br/>");
                }
                $fw = fwrite($fp, $content);

                if (!$fw) {
                    $error = 1;
                    throw new Exception("<br/><b>PANIC! <br/> settings.php can't be written!</b><br/>");
                }

                fclose($fp);
                $msg .= '<p>File settings.php created correctly.</p>';

                $msg .= "<p><a href='../general/login.php'>Please log in</a></p>";


            } catch (PDOException $e) {
                $msg = $e->getMessage();
            } catch (Exception $e) {
                $msg = $e->getMessage();
            }

            unset($conn);
        } else {
            $msg = "Error with the database.  Please check and try again.";

        }
    }
}

if ($step == "") {
    $step = "1";
}

$setTitle = "PhpCollab : Installation";
define('THEME', 'default');
$blank = "true";

include dirname(dirname(__FILE__)) . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("<a href='../installation/setup.php'>Setup</a>");

if ($step == "1") {
    $blockPage->itemBreadcrumbs("License");
} elseif ($step > "1") {
    $blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=1'>License</a>");

    if ($step == "2") {
        $blockPage->itemBreadcrumbs("Settings");
    } elseif ($step > "2") {
        $blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=2'>Settings</a>");
        if ($step == "3") {
            $blockPage->itemBreadcrumbs("Control");
        }
    }
}

$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

if ($step == "1") {
    $block1->heading("License");
}
if ($step == "2") {
    $block1->heading("Settings");
}
if ($step == "3") {
    $block1->heading("Control");
}

if ($step == "1") {
    $block1->openContent();
    $block1->contentTitle("&nbsp;");

    echo "<tr class='odd'><td colspan='2'>
		<pre style='margin-left: 2rem; height: 500px; overflow: scroll;'>";
    include '../docs/copying.txt';
    echo "</pre>
		</td></tr>";
    $block1->closeContent();
}


if ($step == "2") {
    $block1->openContent();
    $block1->contentTitle("Details");
    $block1->form = "settings";
    $block1->openForm("../installation/setup.php?step=3");

    if ($connection == "off") {
        echo "<input value='false' name='updatechecker' type='hidden'>";
    } elseif (@join('', file("http://www.phpcollab.com/website/version.txt"))) {
        echo "<input value='true' name='updatechecker' type='hidden'>";
    } else {
        echo "<input value='false' name='updatechecker' type='hidden'>";
    }

    echo '<input type="hidden" name="action" value="generate">';

    if ($connection == "off") {
        $installCheckOffline = "checked";
    } else {
        $installCheckOnline = "checked";
    }

    if ($databaseType == "mysql" || $databaseType == "") {
        $dbCheckMysql = "checked";
    } elseif ($databaseType == "sqlserver") {
        $dbCheckSqlserver = "checked";
    } elseif ($databaseType == "postgresql") {
        $dbCheckPostgresql = "checked";
    }

    echo "	<tr class='odd'>
				<td class='leftvalue'>* Installation type :</td>
				<td><input type='radio' name='installationType' value='offline' $installCheckOffline> Offline (firewall/intranet, no update checker)&nbsp;<input type='radio' name='installationType' value='online' $installCheckOnline> Online</td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>* Database type :</td>
				<td>
				    <input type='radio' name='databaseType' value='mysql' $dbCheckMysql> MySql&nbsp;
				    <input type='radio' name='databaseType' value='sqlserver' $dbCheckSqlserver> Microsoft Sql Server&nbsp;
				    <input type='radio' name='databaseType' value='postgresql' $dbCheckPostgresql> PostgreSQL
                </td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>* Database server :</td>
				<td><input size='44' value='$dbServer' style='width: 200px' name='dbServer' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>* Database login :</td>
				<td><input size='44' value='$dbLogin' style='width: 200px' name='dbLogin' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>Database password :</td>
				<td><input size='44' value='$dbPassword' style='width: 200px' name='dbPassword' maxlength='100' type='password'></td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>* Database name :</td>
				<td><input size='44' value='$dbName' style='width: 200px' name='dbName' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td class='leftvalue'>Table prefix :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('" . addslashes($help["setup_myprefix"]) . "',ABOVE,SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td>
				<td><input size='44' value='$dbTablePrefix' style='width: 200px' name='dbTablePrefix' maxlength='100' type='text'></td>
			</tr>";

    $safemodeTest = ini_get("safe_mode");

    if ($safemodeTest == "1") {
        $checked1_a = "checked"; //false
        $safemode = "on";
    } else {
        $checked2_a = "checked"; //true
        $safemode = "off";
    }

    $notificationsTest = function_exists('mail');
    if ($notificationsTest == "true") {
        $checked2_b = "checked"; //false
        $gdlibrary = "on";
    } else {
        $checked1_b = "checked"; //true
        $gdlibrary = "off";
    }

    echo "<tr class='odd'><td class='leftvalue'>* Create folder method :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('" . addslashes($help["setup_mkdirMethod"]) . "',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td>

	<table cellpadding=0 cellspacing=0><tr><td valign=top><input type='radio' name='mkdirMethod' value='FTP' $checked1_a> FTP&nbsp;<input type='radio' name='mkdirMethod' value='PHP' $checked2_a> PHP<br/>[Safe-mode $safemode]</td><td align=right>";
    if ($safemodeTest == "1") {
        echo "Ftp server <input size='44' value='$ftpServer' style='width: 200px' name='ftpServer' maxlength='100' type='text'><br/>
		Ftp login <input size='44' value='$ftpLogin' style='width: 200px' name='ftpLogin' maxlength='100' type='text'><br/>
		Ftp password <input size='44' value='$ftpPassword' style='width: 200px' name='ftpPassword' maxlength='100' type='password'><br/>
		Ftp root <input size='44' value='$ftpRoot' style='width: 200px' name='ftpRoot' maxlength='100' type='text'>";
    }

    echo "</td></tr></table>

	</td></tr>
	<tr class='odd'><td class='leftvalue'>* Notifications :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('" . addslashes($help["setup_notifications"]) . "',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td><input type=\"radio\" name='notifications' value='false' $checked1_b> False&nbsp;<input type='radio' name='notifications' value='true' $checked2_b> True<br/>[Mail $gdlibrary]</td></tr>
	<tr class='odd'><td class='leftvalue'>* Forced login :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('" . addslashes($help["setup_forcedlogin"]) . "',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td><input type=\"radio\" name='forcedlogin' value='false' checked> False&nbsp;<input type='radio' name='forcedlogin' value='true'> True</td></tr>
	<tr class='odd'><td class='leftvalue'>Default language :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('" . addslashes($help["setup_langdefault"]) . "',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td>
			   <select name='defaultLanguage'>
		<option value='ar'>Arabic</option>
		<option value='az'>Azerbaijani</option>
		<option value='pt-br'>Brazilian Portuguese</option>
		<option value='bg'>Bulgarian</option>
		<option value='ca'>Catalan</option>
		<option value='zh'>Chinese simplified</option>
		<option value='zh-tw'>Chinese traditional</option>
		<option value='cs-iso'>Czech (iso)</option>
		<option value='cs-win1250'>Czech (win1250)</option>
		<option value='da'>Danish</option>
		<option value='nl'>Dutch</option>
		<option value='en' selected>English</option>
		<option value='et'>Estonian</option>
		<option value='fr'>French</option>
		<option value='de'>German</option>
		<option value='hu'>Hungarian</option>
		<option value='is'>Icelandic</option>
		<option value='in'>Indonesian</option>
		<option value='it'>Italian</option>
		<option value='ko'>Korean</option>
		<option value='lv'>Latvian</option>
		<option value='no'>Norwegian</option>
		<option value='pl'>Polish</option>
		<option value='pt'>Portuguese</option>
		<option value='ro'>Romanian</option>
		<option value='ru'>Russian</option>
		<option value='sk-win1250'>Slovak (win1250)</option>
		<option value='es'>Spanish</option>
		<option value='tr'>Turkish</option>
		<option value='uk'>Ukrainian</option>
			   </select>
			  </td>
			 </tr>";


    $url = $_SERVER["SERVER_NAME"];
    if ($_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) {
        $url .= ":" . $_SERVER["SERVER_PORT"];
    }
    if ($_SERVER["HTTPS"] == "on") {
        $protocol = "https://";
    } else {
        $protocol = "http://";
    }

    $siteUrl = $protocol . $url . dirname($_SERVER["PHP_SELF"]);
    $siteUrl = str_replace("installation", "", $siteUrl);

    $tooltipLoginMethod = addslashes($help["setup_loginmethod"]);
    echo <<<HTML
		<tr class="odd">
			<td class="leftvalue"> * Root :</td>
			<td><input size="44" value="{$siteUrl}" style="width: 200px" name="siteUrl" maxlength="100" type="text"></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">* Login method :<br/>
			    [<a href="javascript:void(0);" 
			        onmouseover="return overlib('{$tooltipLoginMethod}',SNAPX,550,BGCOLOR,"#5B7F93",FGCOLOR,"#C4D3DB");" 
			        onmouseout="return nd();">Help</a>]
            </td>
			<td>
			    <input type="radio" name="loginMethod" value="plain"> Plain&nbsp;
			    <input type="radio" name="loginMethod" value="md5"> Md5&nbsp;
			    <input type="radio" name="loginMethod" value="crypt" checked> Crypt
            </td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">* Admin password :</td>
			<td><input size="44" value="" style="width: 200px" name="adminPassword" maxlength="100" type="password"></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">&nbsp;</td>
			<td><input type="SUBMIT" value="Save"></td>
		</tr>
HTML;
    $block1->closeContent();
    $block1->closeForm();
}

if ($step == "3") {
    $block1->openContent();
    $block1->contentTitle("&nbsp;");

    echo <<<HTML
        <tr class='odd'>
            <td class='leftvalue'>&nbsp;</td>
            <td>{$msg}</td>
        </tr>
HTML;
    $block1->closeContent();
}


$stepNext = $step + 1;
if ($step < "2") {
    echo <<<FORM
    <form id="license" name='license' action='../installation/setup.php?step=2&redirect=true' method='post' style='text-align: center;'>
        <p><input type="submit" value="Step {$stepNext}" style="color: #000; font-weight: bold; background-color: transparent; border: none; text-decoration: underline" /></p>
        <input type='checkbox' value='off' name='connection'> Offline installation (firewall/intranet, no update checker)
    </form>
FORM;
}

$footerDev = "false";
include dirname(dirname(__FILE__)) . '/themes/' . THEME . '/footer.php';
