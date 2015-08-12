<?php
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
** HISTORY:
** 	2003-10-23	-	rewrite setting.php
**  2005-03-08	-	fixed preferences for user home page
**	2005-06-13	-	added show subtask in home
**	2006-09-30	-	added email alerts
**
**  2008-11-05  -  Created more paranoid commands when saving.. Started framework for DB Save
** -----------------------------------------------------------------------------
** TO-DO:
** 	add new option
**	move option to db
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');
$setTitle .= " : Edit Settings";

if ($profilSession != "0")
{
	Util::headerFunction('../general/permissiondenied.php?'.session_name().'='.session_id());
	exit;
}

if ($action == "generate")
{
	if ($installationTypeNew == "offline") 
	{
		$updateCheckerNew = "false";
	}

	if (substr($rootNew, -1) == "/") 
	{	
		$rootNew = substr($rootNew, 0, -1);
	}

	if (substr($ftpRootNew, -1) == "/") 
	{
		$ftpRootNew = substr($ftpRootNew, 0, -1);
	}

	if (substr($pathMantisNew, -1) != "/") 
	{
		$pathMantisNew = $pathMantisNew."/";	
	}

// DAB - scrub the data
$scrubData = DataFunctions::scrubData($_POST);
extract($scrubData);
// -- END Paranoia

$content = <<<STAMP
<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings.php

# installation type
\$installationType = "$installationTypeNew"; //select "offline" or "online"

# select database application
\$databaseType = "$databaseTypeNew"; //select "sqlserver", "postgresql" or "mysql"

# database parameters
define('MYSERVER','$myserverNew');
define('MYLOGIN','$myloginNew');
define('MYPASSWORD','$mypasswordNew');
define('MYDATABASE','$mydatabaseNew');

# notification method
\$notificationMethod = "$notificationMethodNew"; //select "mail" or "smtp"

# smtp parameters (only if \$notificationMethod == "smtp")
define('SMTPSERVER','$smtpserverNew');
define('SMTPLOGIN','$smtploginNew');
define('SMTPPASSWORD','$smtppasswordNew');

# create folder method
\$mkdirMethod = "$mkdirMethodNew"; //select "FTP" or "PHP"

# ftp parameters (only if \$mkdirMethod == "FTP")
define('FTPSERVER','$ftpserverNew');
define('FTPLOGIN','$ftploginNew');
define('FTPPASSWORD','$ftppasswordNew');

# PhpCollab root according to ftp account (only if \$mkdirMethod == "FTP")
\$ftpRoot = "$ftpRootNew"; //no slash at the end

# Invoicing module
\$enableInvoicing = "true";

# theme choice
define('THEME','$mythemeNew');

# newsdesk limiter
\$newsdesklimit = 1; 

# if 1 the admin logs in his homepage
\$adminathome = 0;

# session.trans_sid forced
\$trans_sid = "true";

# timezone GMT management
\$gmtTimezone = "$gmtTimezoneNew";

# language choice
\$langDefault = "$langNew";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
\$enableMantis = "$mantisNew";

// Mantis installation directory
\$pathMantis = "$pathMantisNew";  // add slash at the end

# CVS parameters
// Should CVS be enabled?
\$enable_cvs = "false";

// Should browsing CVS be limited to project members?
\$cvs_protected = "false";

// Define where CVS repositories should be stored
\$cvs_root = "D:\cvs"; //no slash at the end

// Who is the owner CVS files?
// Note that this should be user that runs the web server.
// Most *nix systems use "httpd" or "nobody"
\$cvs_owner = "httpd";

// CVS related commands
\$cvs_co = "/usr/bin/co";
\$cvs_rlog = "/usr/bin/rlog";
\$cvs_cmd = "/usr/bin/cvs";

# https related parameters
\$pathToOpenssl = "/usr/bin/openssl";

# login method, set to "CRYPT" in order CVS authentication to work (if CVS support is enabled)
\$loginMethod = "$loginMethodNew"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
\$useLDAP = "false";
\$configLDAP[ldapserver] = "your.ldap.server.address";
\$configLDAP[searchroot] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
\$htaccessAuth = "false";
\$fullPath = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
\$fileManagement = "true";
\$maxFileSize = $maxFileSizeNew; //bytes limit for upload
\$root = "$rootNew"; //no slash at the end

# security issue to disallow php files upload
\$allowPhp = "false";

# project site creation
\$sitePublish = "true";

# enable update checker
\$updateChecker = "$updateCheckerNew";

# e-mail notifications
\$notifications = "$notificationsNew";

# show peer review area
\$peerReview = "true";

# show items for home
\$showHomeBookmarks = $showHomeBookmarksNew;
\$showHomeProjects = $showHomeProjectsNew;
\$showHomeTasks = $showHomeTasksNew;
\$showHomeDiscussions = $showHomeDiscussionsNew;
\$showHomeReports = $showHomeReportsNew;
\$showHomeNotes = $showHomeNotesNew;
\$showHomeNewsdesk = $showHomeNewsdeskNew;
\$showHomeSubtasks = $showHomeSubtasksNew;

# security issue to disallow auto-login from external link
\$forcedLogin = "$forcedloginNew";

# table prefix
\$tablePrefix = "$tablePrefixNew";

# database tables
\$tableCollab["assignments"] = "$table_assignments";
\$tableCollab["calendar"] = "$table_calendar";
\$tableCollab["files"] = "$table_files";
\$tableCollab["logs"] = "$table_logs";
\$tableCollab["members"] = "$table_members";
\$tableCollab["notes"] = "$table_notes";
\$tableCollab["notifications"] = "$table_notifications";
\$tableCollab["organizations"] = "$table_organizations";
\$tableCollab["posts"] = "$table_posts";
\$tableCollab["projects"] = "$table_projects";
\$tableCollab["reports"] = "$table_reports";
\$tableCollab["sorting"] = "$table_sorting";
\$tableCollab["tasks"] = "$table_tasks";
\$tableCollab["teams"] = "$table_teams";
\$tableCollab["topics"] = "$table_topics";
\$tableCollab["phases"] = "$table_phases";
\$tableCollab["support_requests"] = "$table_support_requests";
\$tableCollab["support_posts"] = "$table_support_posts";
\$tableCollab["subtasks"] = "$table_subtasks";
\$tableCollab["updates"] = "$table_updates";
\$tableCollab["bookmarks"] = "$table_bookmarks";
\$tableCollab["bookmarks_categories"] = "$table_bookmarks_categories";
\$tableCollab["invoices"] = "$table_invoices";
\$tableCollab["invoices_items"] = "$table_invoices_items";
\$tableCollab["services"] = "$table_services";
\$tableCollab["newsdeskcomments"] = "$table_newsdeskcomments";
\$tableCollab["newsdeskposts"] = "$table_newsdeskposts";

# PhpCollab version
\$version = "$version";

# demo mode parameters
\$demoMode = "false";
\$urlContact = "http://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
\$activeJpgraph = "true";

# developement options in footer
\$footerDev = "$footerdevNew";

# filter to see only logged user clients (in team / owner)
\$clientsFilter = "$clientsFilterNew";

# filter to see only logged user projects (in team / owner)
\$projectsFilter = "$projectsFilterNew";

# Enable help center support requests, values "true" or "false"
\$enableHelpSupport = "true";

# Return email address given for clients to respond too.
\$supportEmail = "email@yourdomain.com";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new request is added
\$supportType = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
\$lastvisitedpage = false;

# auto-publish tasks?
\$autoPublishTasks = $autoPublishTasksNew;

# html header parameters
\$setDoctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
\$setTitle = "PhpCollab";
\$setDescription = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
\$setKeywords = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

# Email alerts
\$emailAlerts = "false";
?>
STAMP;
    
	if (!@fopen("../includes/settings.php",'wb+')) 
	{
		$msg = "settingsNotwritable";
	} 
	else 
	{
		$fp = @fopen("../includes/settings.php",'wb+');
		$fw = @fwrite($fp,$content);
		
		if (!$fw) 
		{
			$msg = "settingsNotwritable";
			fclose($fp);
		} 
		else 
		{
			fclose($fp);
			Util::headerFunction("../administration/admin.php?msg=update&".session_name()."=".session_id());
		}
	}
}

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?",$strings["administration"],in));
$blockPage->itemBreadcrumbs($strings["edit_settings"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

$block1->heading($strings["edit_settings"]);


$block1->openContent();
$block1->contentTitle("General");
$block1->form = "settings";
$block1->openForm("../administration/updatesettings.php?action=generate&".session_name()."=".session_id());

if (substr($ftpRoot, -1) == "/") { $ftpRoot = substr($ftpRoot, 0, -1); }

$tablePrefix = substr($tableCollab["projects"], 0, -8);

echo "	<input value='$tablePrefix' name='tablePrefixNew' type='hidden' />
		<input value='$databaseType' name='databaseTypeNew' type='hidden' />
		<input value='".MYSERVER."' name='myserverNew' type='hidden' />
		<input value='".MYLOGIN."' name='myloginNew' type='hidden' />
		<input value='".MYPASSWORD."' name='mypasswordNew' type='hidden' />
		<input value='".MYDATABASE."' name='mydatabaseNew' type='hidden' />
		<input value='".$tablePrefix."assignments' name='table_assignments' type='hidden' />
		<input value='".$tablePrefix."calendar' name='table_calendar' type='hidden' />
		<input value='".$tablePrefix."files' name='table_files' type='hidden' />
		<input value='".$tablePrefix."logs' name='table_logs' type='hidden' />
		<input value='".$tablePrefix."members' name='table_members' type='hidden' />
		<input value='".$tablePrefix."notes' name='table_notes' type='hidden' />
		<input value='".$tablePrefix."notifications' name='table_notifications' type='hidden' />
		<input value='".$tablePrefix."organizations' name='table_organizations' type='hidden' />
		<input value='".$tablePrefix."posts' name='table_posts' type='hidden' />
		<input value='".$tablePrefix."projects' name='table_projects' type='hidden' />
		<input value='".$tablePrefix."reports' name='table_reports' type='hidden' />
		<input value='".$tablePrefix."sorting' name='table_sorting' type='hidden' />
		<input value='".$tablePrefix."tasks' name='table_tasks' type='hidden' />
		<input value='".$tablePrefix."teams' name='table_teams' type='hidden' />
		<input value='".$tablePrefix."topics' name='table_topics' type='hidden' />
		<input value='".$tablePrefix."phases' name='table_phases' type='hidden' />
		<input value='".$tablePrefix."support_requests' name='table_support_requests' type='hidden' />
		<input value='".$tablePrefix."support_posts' name='table_support_posts' type='hidden' />
		<input value='".$tablePrefix."subtasks' name='table_subtasks' type='hidden' />
		<input value='".$tablePrefix."updates' name='table_updates' type='hidden' />
		<input value='".$tablePrefix."bookmarks' name='table_bookmarks' type='hidden' />
		<input value='".$tablePrefix."bookmarks_categories' name='table_bookmarks_categories' type='hidden' />
		<input value='".$tablePrefix."invoices' name='table_invoices' type='hidden' />
		<input value='".$tablePrefix."invoices_items' name='table_invoices_items' type='hidden' />
		<input value='".$tablePrefix."services' name='table_services' type='hidden' />
		<input value='".$tablePrefix."newsdeskcomments' name='table_newsdeskcomments' type='hidden' />
		<input value='".$tablePrefix."newsdeskposts' name='table_newsdeskposts' type='hidden' />

<input value=\"$loginMethod\" name=\"loginMethodNew\" type=\"hidden\">";

if ($version == $versionNew) {
	if ($versionOld == "") {
		$versionOld = $version;
	}
	echo "<input value=\"$versionOld\" name=\"versionOldNew\" type=\"hidden\">";
} else {
	echo "<input value=\"$version\" name=\"versionOldNew\" type=\"hidden\">";
}

$safemodeTest = ini_get(safe_mode);
if ($safemodeTest == "1") {
	$safemode = "on";
} else {
	$safemode = "off";
}

$notificationsTest = function_exists('mail');
if ($notificationsTest == "true") {
	$mail = "on";
} else {
	$mail = "off";
}

if ($mkdirMethod == "FTP") {
	$checked1_a = "checked";
} else {
	$checked2_a = "checked";
}
if ($notifications == "true") {
	$checked2_b = "checked";
} else {
	$checked1_b = "checked";
}
if ($forcedLogin == "true") {
	$checked1_c = "checked";
} else {
	$checked2_c = "checked";
}
if ($clientsFilter == "true") {
	$checked1_d = "checked";
} else {
	$checked2_d = "checked";
}
if ($updateChecker == "true") {
	$checked1_e = "checked";
} else {
	$checked2_e = "checked";
}
if ($gmtTimezone == "true") {
	$checked1_f = "checked";
} else {
	$checked2_f = "checked";
}
if ($projectsFilter == "true") {
	$checked1_h = "checked";
} else {
	$checked2_h = "checked";
}

if ($footerDev == "true") 
{
	$checked1_j = "checked";
} else {
	$checked2_j = "checked";
}

if ($enableMantis == "true") 
{
	$checked1_k = "checked";
} else {
	$checked2_k = "checked";
}

if ($notificationMethod == "smtp") 
{
	$checked1_g = "checked";
} else {
	$checked2_g = "checked";
}

if ($installationType == "offline") 
{
	$installCheckOffline = "checked";
} else {
	$installCheckOnline = "checked";
}

// preference for home page
if ($showHomeBookmarks)
{
	$checkedHomeBookmarks_t = "checked";	
} else {
	$checkedHomeBookmarks_f = "checked";
}

if ($showHomeDiscussions)
{
	$checkedHomeDiscussions_t = "checked";
} else {
	$checkedHomeDiscussions_f = "checked";
}

if ($showHomeNewsdesk)
{
	$checkedHomeNewsdesk_t = "checked";
} else {
	$checkedHomeNewsdesk_f = "checked";
}

if ($showHomeNotes)
{
	$checkedHomeNotes_t = "checked";
} else {
	$checkedHomeNotes_f = "checked";
}

if ($showHomeProjects)
{
	$checkedHomeProjects_t = "checked";
} else {
	$checkedHomeProjects_f = "checked";
}

if ($showHomeReports)
{
	$checkedHomeReports_t = "checked";
} else {
	$checkedHomeReports_f = "checked";
}

if ($showHomeTasks)
{
	$checkedHomeTasks_t = "checked";
} else {
	$checkedHomeTasks_f = "checked";
}

if ($showHomeSubtasks)
{
	$checkedHomeSubtasks_t = "checked";
} else {
	$checkedHomeSubtasks_f = "checked";
}

if ($autoPublishTasks)
{
	$checkedAutoPublish_t = "checked";
} else {
	$checkedAutoPublish_f = "checked";
}


$block1->contentRow("Installation type","<input type='radio' name='installationTypeNew' value='offline' $installCheckOffline /> Offline (firewall/intranet, no update checker)&nbsp;<input type='radio' name='installationTypeNew' value='online' $installCheckOnline /> Online");

$block1->contentRow("Update checker","<input type='radio' name='updateCheckerNew' value='false' $checked2_e /> False&nbsp;<input type='radio' name='updateCheckerNew' value='true' $checked1_e /> True");

echo "<tr class='odd'><td valign='top' class='leftvalue'>* Create folder method".$blockPage->printHelp("setup_mkdirMethod")."</td><td>
<table cellpadding=0 cellspacing=0 width=500><tr><td valign=top><input type='radio' name='mkdirMethodNew' value='PHP' $checked2_a /> PHP&nbsp;<input type='radio' name='mkdirMethodNew' value='FTP' $checked1_a /> FTP<br/>[Safe-mode $safemode]</td><td align=right>";
echo "Ftp server <input size='44' value='".FTPSERVER."' style='width: 200px' name='ftpserverNew' maxlength='100' type='text' /><br/>
Ftp login <input size='44' value='".FTPLOGIN."' style='width: 200px' name='ftploginNew' maxlength='100' type='text' /><br/>
Ftp password <input size='44' value='".FTPPASSWORD."' style='width: 200px' name='ftppasswordNew' maxlength='100' type='password' /><br/>
Ftp root <input size='44' value='$ftpRoot' style='width: 200px' name='ftpRootNew' maxlength='100' type='text' />";
echo "</td></tr></table>
</td></tr>";

echo "<tr class='odd'><td valign='top' class='leftvalue'>* Notification method".$blockPage->printHelp("setup_notificationMethod")."</td><td>
<table cellpadding=0 cellspacing=0 width=500><tr><td valign=top><input type='radio' name='notificationMethodNew' value='mail' $checked2_g /> PHP mail function&nbsp;<input type='radio' name='notificationMethodNew' value='smtp' $checked1_g /> SMTP</td><td align=right>";
echo "Smtp server <input size='44' value='".SMTPSERVER."' style='width: 200px' name='smtpserverNew' maxlength='100' type='text /'><br/>
Smtp login <input size='44' value='".SMTPLOGIN."' style='width: 200px' name='smtploginNew' maxlength='100' type='text' /><br/>
Smtp password <input size='44' value='".SMTPPASSWORD."' style='width: 200px' name='smtppasswordNew' maxlength='100' type='password' />";
echo "</td></tr></table>
</td></tr>";

echo "<tr class='odd'><td valign='top' class='leftvalue'>* Theme :</td><td><select name='mythemeNew'>";

$all = opendir("../themes"); 
while ($file = readdir($all)) 
{ 
	if ($file != "index.php" && $file !=".." && $file!="." && $file!="CVS") 
	{
		if ($file == THEME) 
		{
			echo "<option value=\"$file\" selected>$file</option>";
		} 
		else 
		{
			echo "<option value=\"$file\">$file</option>";
		}
	}
}
closedir($all);
echo "</td></tr>";

$block1->contentRow("Notifications".$blockPage->printHelp("setup_notifications"),"<input type='radio' name='notificationsNew' value='false' $checked1_b /> False&nbsp;<input type='radio' name='notificationsNew' value='true' $checked2_b /> True<br/>[Mail $mail]");

$block1->contentRow("Timezone (GMT)","<input type='radio' name='gmtTimezoneNew' value='false' $checked2_f /> False&nbsp;<input type='radio' name='gmtTimezoneNew' value='true' $checked1_f /> True");

$block1->contentRow("* Forced login".$blockPage->printHelp("setup_forcedlogin"),"<input type='radio' name='forcedloginNew' value='false' $checked2_c /> False&nbsp;<input type='radio' name='forcedloginNew' value='true' $checked1_c  /> True");

echo "<tr class='odd'>
		<td valign='top' class='leftvalue'>Default language".$blockPage->printHelp("setup_langdefault")."</td><td>
			<select name='langNew'>
				<option value=''>Blank</option>
				<option value='ar' ".$langSelected["ar"].">Arabic</option>
				<option value='az' ".$langSelected["az"].">Azerbaijani</option>
				<option value='pt-br'' ".$langSelected["pt-br"].">Brazilian Portuguese</option>
				<option value='bg' ".$langSelected["bg"].">Bulgarian</option>
				<option value='ca' ".$langSelected["ca"].">Catalan</option>
				<option value='zh' ".$langSelected["zh"].">Chinese simplified</option>
				<option value='zh-tw' ".$langSelected["zh-tw"].">Chinese traditional</option>
				<option value='cs-iso' ".$langSelected["cs-iso"].">Czech (iso)</option>
				<option value='cs-win1250' ".$langSelected["cs-win1250"].">Czech (win1250)</option>
				<option value='da' ".$langSelected["da"].">Danish</option>
				<option value='nl' ".$langSelected["nl"].">Dutch</option>
				<option value='en' ".$langSelected["en"].">English</option>
				<option value='et' ".$langSelected["et"].">Estonian</option>
				<option value='fr' ".$langSelected["fr"].">French</option>
				<option value='de' ".$langSelected["de"].">German</option>
				<option value='hu' ".$langSelected["hu"].">Hungarian</option>
				<option value='is' ".$langSelected["is"].">Icelandic</option>
				<option value='in' ".$langSelected["in"].">Indonesian</option>
				<option value='it' ".$langSelected["it"].">Italian</option>
				<option value='ko' ".$langSelected["ko"].">Korean</option>
				<option value='lv' ".$langSelected["lv"].">Latvian</option>
				<option value='no' ".$langSelected["no"].">Norwegian</option>
				<option value='pl' ".$langSelected["pl"].">Polish</option>
				<option value='pt' ".$langSelected["pt"].">Portuguese</option>
				<option value='ro' ".$langSelected["ro"].">Romanian</option>
				<option value='ru' ".$langSelected["ru"].">Russian</option>
				<option value='sk-win1250' ".$langSelected["sk-win1250"].">Slovak (win1250)</option>
				<option value='es' ".$langSelected["es"].">Spanish</option>
				<option value='tr' ".$langSelected["tr"].">Turkish</option>
				<option value='uk' ".$langSelected["uk"].">Ukrainian</option>
			</select>
          </td>
         </tr>";
    
$block1->contentRow("* Root","<input size='44' value='$root' style='width: 200px' name='rootNew' maxlength='100' type='text' />");
$block1->contentRow("* Default max file size","<input size='44' value='$maxFileSize' style='width: 200px' name='maxFileSizeNew' maxlength='100' type='text' /> $byteUnits[0]");

$block1->contentTitle("Options");

$block1->contentRow("Clients filter".$blockPage->printHelp("setup_clientsfilter"),"<input type='radio' name='clientsFilterNew' value='false' $checked2_d /> False&nbsp;<input type='radio' name='clientsFilterNew' value='true' $checked1_d /> True");
$block1->contentRow("Projects filter".$blockPage->printHelp("setup_projectsfilter"),"<input type='radio' name='projectsFilterNew' value='false' $checked2_h /> False&nbsp;<input type='radio' name='projectsFilterNew' value='true' $checked1_h /> True");

$block1->contentRow('Show Bookmarks','<input type="radio" name="showHomeBookmarksNew" value="false" '.$checkedHomeBookmarks_f.' /> False&nbsp;<input type="radio" name="showHomeBookmarksNew" value="true" '.$checkedHomeBookmarks_t.' /> True');
$block1->contentRow('Show Projects','<input type="radio" name="showHomeProjectsNew" value="false" '.$checkedHomeProjects_f.' /> False&nbsp;<input type="radio" name="showHomeProjectsNew" value="true" '.$checkedHomeProjects_t.' /> True');
$block1->contentRow('Show Tasks','<input type="radio" name="showHomeTasksNew" value="false" '.$checkedHomeTasks_f.' /> False&nbsp;<input type="radio" name="showHomeTasksNew" value="true" '.$checkedHomeTasks_t.' /> True');
$block1->contentRow('Show Subtasks','<input type="radio" name="showHomeSubtasksNew" value="false" '.$checkedHomeSubtasks_f.' /> False&nbsp;<input type="radio" name="showHomeSubtasksNew" value="true" '.$checkedHomeSubtasks_t.' /> True');
$block1->contentRow('Show Discussions','<input type="radio" name="showHomeDiscussionsNew" value="false" '.$checkedHomeDiscussions_f.' /> False&nbsp;<input type="radio" name="showHomeDiscussionsNew" value="true" '.$checkedHomeDiscussions_t.' /> True');
$block1->contentRow('Show Reports','<input type="radio" name="showHomeReportsNew" value="false" '.$checkedHomeReports_f.' /> False&nbsp;<input type="radio" name="showHomeReportsNew" value="true" '.$checkedHomeReports_t.' /> True');
$block1->contentRow('Show Notes','<input type="radio" name="showHomeNotesNew" value="false" '.$checkedHomeNotes_f.' /> False&nbsp;<input type="radio" name="showHomeNotesNew" value="true" '.$checkedHomeNotes_t.' /> True');
$block1->contentRow('Show NewsDesk','<input type="radio" name="showHomeNewsdeskNew" value="false" '.$checkedHomeNewsdesk_f.' /> False&nbsp;<input type="radio" name="showHomeNewsdeskNew" value="true" '.$checkedHomeNewsdesk_t.' /> True');
$block1->contentRow('Auto-publish Tasks','<input type="radio" name="autoPublishTasksNew" value="false" '.$checkedAutoPublish_f.' /> False&nbsp;<input type="radio" name="autoPublishTasksNew" value="true" '.$checkedAutoPublish_t.' /> True');

$block1->contentTitle("Advanced");

$block1->contentRow("Extended footer (dev)","<input type='radio' name='footerdevNew' value='false' $checked2_j /> False&nbsp;<input type='radio' name='footerdevNew' value='true' $checked1_j /> True");

$block1->contentRow("Mantis integration","<input type='radio' name='mantisNew' value='false' $checked2_k /> False&nbsp;<input type='radio' name='mantisNew' value='true' $checked1_k /> True");

$block1->contentRow("Mantis url","<input size='44' value='$pathMantis' style='width: 200px' name='pathMantisNew' maxlength='100' type='text' />");

$block1->contentRow("","<input type='SUBMIT' value='".$strings["save"]."' />");

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>
