<?php
/*
** Application name: phpCollab
** Last Edit page: 2004-08-23 
** Path by root: ../installation/setup.php
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
** FILE: setup.php
**
** DESC: Screen: setup file 
**
** HISTORY:
**  2004-08-23  -   add/complete switch according to php version
**  2004-08-23  -   update register_globals cheat code to be compatible with php5 
**	19/05/2005	-	fixed and &amp; in link
** -----------------------------------------------------------------------------
** TO-DO:
**
** =============================================================================
*/

error_reporting(2039);

// register_globals cheat code
//GET and POST VARS
while (list($key, $val) = @each($_REQUEST)) 
{
       $GLOBALS[$key] = $val;
}
//$HTTP_SESSION_VARS
while (list($key, $val) = @each($_SESSION)) 
{
       $GLOBALS[$key] = $val;
}
//$HTTP_SERVER_VARS
while (list($key, $val) = @each($_SERVER)) 
{
       $GLOBALS[$key] = $val;
}

include("../languages/help_en.php");

if ($redirect == "true" && $step == "2") 
{
	header("Location:../installation/setup.php?step=2&connexion=$connexion");
}

if (substr($root, -1) == "/") { $root = substr($root, 0, -1); }
if (substr($ftpRoot, -1) == "/") { $ftpRoot = substr($ftpRoot, 0, -1); }

$version = "2.5";

$dateheure = date("Y-m-d H:i");

if ($action == "generate") 
{
	if ($myserver == '') 
	{
		$error = 'Must be insert the database Server';
	} 
	elseif ($mylogin == '') 
	{
		$error = 'Must be insert the database Login';
	} 
	elseif ($mydatabase == '') 
	{
		$error = 'Must be insert the database Name';
	} 
	elseif ($root == '') 
	{
		$error = 'Must be insert the Root path';
	} 
	elseif ($adminPwd == '') 
	{
		$error = 'Must be insert the Admin password';
	}

	// return a password using the globally specified method
	function get_password($newPassword) 
	{
		global $loginMethod;
		switch ($loginMethod) 
		{
			case MD5:	
				return md5($newPassword);
			case CRYPT:	
				$salt = substr($newPassword,0,2);
				return crypt($newPassword,$salt);
			case PLAIN:	
				return $newPassword;
//			return $newPassword;
		}
	}

    
	if ($installationType == "offline") 
	{
		$updatechecker = "false";
	}

//Let's also get pretty paranoid here ;)
// DAB - scrub the data
require_once(dirname(realpath(__FILE__)) . '/../general/data_funcs.inc.php');
$scrubData = scrubData($_POST);
extract($scrubData);
// -- END Paranoia

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
define('MYSERVER','$myserver');
define('MYLOGIN','$mylogin');
define('MYPASSWORD','$mypassword');
define('MYDATABASE','$mydatabase');

# notification method
\$notificationMethod = "mail"; //select "mail" or "smtp"

# smtp parameters (only if \$notificationMethod == "smtp")
define('SMTPSERVER','');
define('SMTPLOGIN','');
define('SMTPPASSWORD','');

# create folder method
\$mkdirMethod = "$mkdirMethod"; //select "FTP" or "PHP"

# ftp parameters (only if \$mkdirMethod == "FTP")
define('FTPSERVER','$ftpserver');
define('FTPLOGIN','$ftplogin');
define('FTPPASSWORD','$ftppassword');

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

# session.trans_sid forced
\$trans_sid = "true";

# timezone GMT management
\$gmtTimezone = "false";

# language choice
\$langDefault = "$langdefault";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
\$enableMantis = "false";

// Mantis installation directory
\$pathMantis = "http://localhost/mantis/";  // add slash at the end

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
\$loginMethod = "$loginMethod"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
\$useLDAP = "false";
\$configLDAP[ldapserver] = "your.ldap.server.address";
\$configLDAP[searchroot] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
\$htaccessAuth = "false";
\$fullPath = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
\$fileManagement = "true";
\$maxFileSize = 51200; //bytes limit for upload
\$root = "$root"; //no slash at the end

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
\$tablePrefix = "$myprefix";

# database tables
\$tableCollab["assignments"] = "{$myprefix}assignments";
\$tableCollab["calendar"] = "{$myprefix}calendar";
\$tableCollab["files"] = "{$myprefix}files";
\$tableCollab["logs"] = "{$myprefix}logs";
\$tableCollab["members"] = "{$myprefix}members";
\$tableCollab["notes"] = "{$myprefix}notes";
\$tableCollab["notifications"] = "{$myprefix}notifications";
\$tableCollab["organizations"] = "{$myprefix}organizations";
\$tableCollab["posts"] = "{$myprefix}posts";
\$tableCollab["projects"] = "{$myprefix}projects";
\$tableCollab["reports"] = "{$myprefix}reports";
\$tableCollab["sorting"] = "{$myprefix}sorting";
\$tableCollab["tasks"] = "{$myprefix}tasks";
\$tableCollab["teams"] = "{$myprefix}teams";
\$tableCollab["topics"] = "{$myprefix}topics";
\$tableCollab["phases"] = "{$myprefix}phases";
\$tableCollab["support_requests"] = "{$myprefix}support_requests";
\$tableCollab["support_posts"] = "{$myprefix}support_posts";
\$tableCollab["subtasks"] = "{$myprefix}subtasks";
\$tableCollab["updates"] = "{$myprefix}updates";
\$tableCollab["bookmarks"] = "{$myprefix}bookmarks";
\$tableCollab["bookmarks_categories"] = "{$myprefix}bookmarks_categories";
\$tableCollab["invoices"] = "{$myprefix}invoices";
\$tableCollab["invoices_items"] = "{$myprefix}invoices_items";
\$tableCollab["services"] = "{$myprefix}services";
\$tableCollab["newsdeskcomments"] = "{$myprefix}newsdeskcomments";
\$tableCollab["newsdeskposts"] = "{$myprefix}newsdeskposts";

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
    
    if (!$error) 
	{
		$fp = @fopen("../includes/settings.php",'wb+');
		$fw = fwrite($fp,$content);
		
		if (!$fw) 
		{
			$error = 1;
			echo "<br/><b>PANIC! <br/> settings.php can't be written!</b><br/>";
		}

		fclose($fp);
		$msg = 'File settings.php created correctly.';
		
		// crypt admin and demo password
		$demoPwd = get_password("demo");
		$adminPwd = get_password($adminPwd);

		// create all tables
		include("../includes/db_var.inc.php");
		include("../includes/setup_db.php");
		
		if ($databaseType == "mysql") 
		{
			$my = @mysql_connect($myserver, $mylogin, $mypassword);
			
			if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during connection on server MySQL.</b><br/>'); }
			mysql_select_db($mydatabase, $my);
			
			if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during selection database.</b><br/>'); }
			
			for($con = 0; $con < count($SQL); $con++)
			{
				mysql_query($SQL[$con]);
				//echo $SQL[$con] . ';<br/>';
				if (mysql_errno() != 0){ exit('<br/><b>PANIC! <br/> Error during the creation of the tables.</b><br/> Error: '. mysql_error()); }
			}
		}
	
		if ($databaseType == "postgresql") 
		{
			$my = pg_connect("host=$myserver port=5432 dbname=$mydatabase user=$mylogin password=$mypassword");
			if (pg_last_error() != 0){ exit('<br/><b>PANIC! <br/> Error during connection on server PostgreSQL.</b><br/>'); }

			for($con = 0; $con < count($SQL); $con++)
			{
				pg_query($SQL[$con]);
				//echo $SQL[$con] . ';<br/>';
				if (pg_last_error() != 0){ exit('<br/><b>PANIC! <br/> Error during the creation of the tables.</b><br/> Error: '. pg_last_error()); }
			}
		}
		
		if ($databaseType == "sqlserver") 
		{
			$my = @mssql_connect($myserver, $mylogin, $mypassword);

			if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during connection on server SQl Server.</b><br/>'); }
			mssql_select_db($mydatabase, $my);
			
			if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during selection database.</b><br/>'); }
			
			for($con = 0; $con < count($SQL); $con++)
			{
				mssql_query($SQL[$con]);
				//echo $SQL[$con] . '<br/>';
				if (mssql_get_last_message() != 0){ exit('<br/><b>PANIC! <br/> Error during the creation of the tables.</b><br/> Error: '. mssql_get_last_message()); }
			}
		}
		$msg .= "<br/>Tables and settings file created correctly.";
		$msg .= "<br/><br/><a href='../general/login.php'>Please log in</a>";
		
	} 
	else 
	{
		$msg = $error;
    }
}

if ($step == "") 
{ 
	$step = "1"; 
}

$setTitle = "PhpCollab";
define('THEME','default');
$blank = "true";
include("../themes/".THEME."/block.class.php");
include('../themes/'.THEME.'/header.php');


$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("<a href='../installation/setup.php'>Setup</a>");

if ($step == "1") 
{
	$blockPage->itemBreadcrumbs("License");
} 
elseif ($step > "1") 
{
    $blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=1'>License</a>");

   	if ($step == "2") 
	{
		$blockPage->itemBreadcrumbs("Settings");
	} 
	elseif ($step > "2") 
	{
		$blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=2'>Settings</a>");
		if ($step == "3") 
		{
			$blockPage->itemBreadcrumbs("Control");
		}
	}
}

$blockPage->closeBreadcrumbs();

$block1 = new block();

if ($step == "1") 
{
	$block1->heading("License");
}
if ($step == "2") 
{
	$block1->heading("Settings");
}
if ($step == "3") 
{
	$block1->heading("Control");
}

if ($step == "1") 
{
	$block1->openContent();
	$block1->contentTitle("&nbsp;");

	echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>
		<pre>";
		include("../docs/copying.txt");
		echo "</pre>
		</td></tr>";
	$block1->closeContent();
}



if ($step == "2") 
{
	$block1->openContent();
	$block1->contentTitle("Details");
	$block1->form = "settings";
	$block1->openForm("../installation/setup.php?action=generate&step=3");

	if ($connexion == "off") 
	{
		echo "<input value='false' name='updatechecker' type='hidden'>";
	} 
	elseif (@join('',file("http://www.phpcollab.com/website/version.txt"))) 
	{
		echo "<input value='true' name='updatechecker' type='hidden'>";
	} 
	else 
	{
		echo "<input value='false' name='updatechecker' type='hidden'>";
	}

	if ($connexion == "off") 
	{
		$installCheckOffline = "checked";
	} 
	else 
	{
		$installCheckOnline = "checked";
	}

	if ($databaseType == "mysql" || $databaseType == "") 
	{
		$dbCheckMysql = "checked";
	} 
	elseif ($databaseType == "sqlserver") 
	{
		$dbCheckSqlserver = "checked";
	} 
	elseif ($databaseType == "postgresql") 
	{
		$dbCheckPostgresql = "checked";
	}

	echo "	<tr class='odd'>
				<td valign='top' class='leftvalue'>* Installation type :</td>
				<td><input type='radio' name='installationType' value='offline' $installCheckOffline> Offline (firewall/intranet, no update checker)&nbsp;<input type='radio' name='installationType' value='online' $installCheckOnline> Online</td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>* Database type :</td>
				<td><input type='radio' name='databaseType' value='mysql' $dbCheckMysql> MySql&nbsp;<input type='radio' name='databaseType' value='sqlserver' $dbCheckSqlserver> Microsoft Sql Server&nbsp;<input type='radio' name='databaseType' value='postgresql' $dbCheckPostgresql> PostgreSQL</td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>* Database server :</td>
				<td><input size='44' value='$myserver' style='width: 200px' name='myserver' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>* Database login :</td>
				<td><input size='44' value='$mylogin' style='width: 200px' name='mylogin' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>Database password :</td>
				<td><input size='44' value='$mypassword' style='width: 200px' name='mypassword' maxlength='100' type='password'></td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>* Database name :</td>
				<td><input size='44' value='$mydatabase' style='width: 200px' name='mydatabase' maxlength='100' type='text'></td>
			</tr>
			<tr class='odd'>
				<td valign='top' class='leftvalue'>Table prefix :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_myprefix"])."',ABOVE,SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td>
				<td><input size='44' value='$myprefix' style='width: 200px' name='myprefix' maxlength='100' type='text'></td>
			</tr>";    

    $safemodeTest = ini_get(safe_mode);

	if ($safemodeTest == "1") 
	{
		$checked1_a = "checked"; //false
		$safemode = "on";
    } 
	else 
	{
		$checked2_a = "checked"; //true
		$safemode = "off";
    }

    $notificationsTest = function_exists('mail');
    if ($notificationsTest == "true") 
	{
		$checked2_b = "checked"; //false
		$gdlibrary = "on";
	} 
	else 
	{
		$checked1_b = "checked"; //true
		$gdlibrary = "off";
    }
    
	echo "<tr class='odd'><td valign='top' class='leftvalue'>* Create folder method :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_mkdirMethod"])."',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td>

	<table cellpadding=0 cellspacing=0><tr><td valign=top><input type='radio' name='mkdirMethod' value='FTP' $checked1_a> FTP&nbsp;<input type='radio' name='mkdirMethod' value='PHP' $checked2_a> PHP<br/>[Safe-mode $safemode]</td><td align=right>";
	if ($safemodeTest == "1") 
	{
		echo "Ftp server <input size='44' value='$ftpserver' style='width: 200px' name='ftpserver' maxlength='100' type='text'><br/>
		Ftp login <input size='44' value='$ftplogin' style='width: 200px' name='ftplogin' maxlength='100' type='text'><br/>
		Ftp password <input size='44' value='$ftppassword' style='width: 200px' name='ftppassword' maxlength='100' type='password'><br/>
		Ftp root <input size='44' value='$ftpRoot' style='width: 200px' name='ftpRoot' maxlength='100' type='text'>";
	}

	echo "</td></tr></table>

	</td></tr>
	<tr class='odd'><td valign='top' class='leftvalue'>* Notifications :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_notifications"])."',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td><input type=\"radio\" name='notifications' value='false' $checked1_b> False&nbsp;<input type='radio' name='notifications' value='true' $checked2_b> True<br/>[Mail $gdlibrary]</td></tr>
	<tr class='odd'><td valign='top' class='leftvalue'>* Forced login :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_forcedlogin"])."',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td><input type=\"radio\" name='forcedlogin' value='false' checked> False&nbsp;<input type='radio' name='forcedlogin' value='true'> True</td></tr>
	<tr class='odd'><td valign='top' class='leftvalue'>Default language :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_langdefault"])."',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td><td>
			   <select name='langdefault'>
				<option value=''>Blank</option>
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
		<option value='en'>English</option>
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


	$url = $SERVER_NAME;
	if ($SERVER_PORT != 80 && $SERVER_PORT != 443) 
	{
		$url .= ":". $SERVER_PORT;
	}
	if ($HTTPS == "on") 
	{
		$protocol = "https://";
	} 
	else 
	{
		$protocol = "http://";
	}
	
	$root = $protocol.$url.dirname($PHP_SELF);
	$root = str_replace("installation","",$root);

	echo "
		<tr class='odd'>
			<td valign='top' class='leftvalue'> * Root :</td>
			<td><input size='44' value='$root' style='width: 200px' name='root' maxlength='100' type='text'></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>* Login method :<br/>[<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help["setup_loginmethod"])."',SNAPX,550,BGCOLOR,'#5B7F93',FGCOLOR,'#C4D3DB');\" onmouseout=\"return nd();\">Help</a>] </td>
			<td><input type='radio' name='loginMethod' value='PLAIN'> Plain&nbsp;<input type='radio' name='loginMethod' value='MD5'> Md5&nbsp;<input type='radio' name='loginMethod' value='CRYPT' checked> Crypt</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>* Admin password :</td>
			<td><input size='44' value='$adminPwd' style='width: 200px' name='adminPwd' maxlength='100' type='password'></td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td><input type='SUBMIT' value='Save'></td>
		</tr>";
	$block1->closeContent();
	$block1->closeForm();
}

if ($step == "3") 
{
	$block1->openContent();
	$block1->contentTitle("&nbsp;");

	echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td>$msg</td></tr>";
	$block1->closeContent();
}


$stepNext = $step + 1;
if ($step < "2") { echo "<form name='license' action='../installation/setup.php?step=2&redirect=true' method='post'><center><a href=\"javascript:document.license.submit();\"><b>Step $stepNext</b></a><br/><br/><input type='checkbox' value='off' name='connexion'> Offline installation (firewall/intranet, no update checker)</center></form><br/>"; }

$footerDev = "false";
include('../themes/'.THEME.'/footer.php');
?>
