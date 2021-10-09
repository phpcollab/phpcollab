<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings_default.php

# PhpCollab version
$version = "2.10.3";

# installation type
$installationType = "online"; //select "offline" or "online"

# select database application
$databaseType = "mysql"; //select "sqlserver", "postgresql" or "mysql"

# database parameters
define('MYSERVER', 'localhost');
define('MYLOGIN', 'root');
define('MYPASSWORD', '');
define('MYDATABASE', 'phpcollab');

# notification method
$notificationMethod = "mail"; //select "mail" or "smtp"

# smtp parameters (only if $notificationMethod == "smtp")
if ($notificationMethod === "smtp") {
    define("SMTPSERVER", "");
    define("SMTPLOGIN", "");
    define("SMTPPASSWORD", "%");
    define("SMTPPORT", "");
}

# create folder method
$mkdirMethod = "PHP"; //select "FTP" or "PHP"

# ftp parameters
# note: only needed if the mkdirMethod is set to FTP
if ($mkdirMethod === "FTP") {
    # PhpCollab root according to ftp account
    # note: No slash at the end
    $ftpRoot = "%ftpRoot%";

    define("FTPSERVER", "");
    define("FTPLOGIN", "");
    define("FTPPASSWORD", "");
}

# PhpCollab root according to ftp account (only if $mkdirMethod == "FTP")
$ftpRoot = ""; //no slash at the end

# Invoicing module
$enableInvoicing = true;

# theme choice
define("THEME", "default");

# NewsDesk
$newsdesklimit = true;

# if 1 the admin logs in his homepage
$adminathome = false;

# timezone GMT management
$gmtTimezone = false;

# language choice
$langDefault = "en";

# Mantis bug tracking parameters
$enableMantis = false;

# Mantis installation directory
# Note: add slash at the end
$pathMantis = "http://localhost/mantis/";

# https related parameters
$pathToOpenssl = "/usr/bin/openssl";

# login method, set to "CRYPT"
# Options: (default) crypt | plain | md5
# It is highly recommended to NOT use md5 or plain
$loginMethod = "crypt";

# enable LDAP
$useLDAP = false;
$configLDAP["ldapserver"] = "your.ldap.server.address";
$configLDAP["searchroot"] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
$htaccessAuth = false;
if ($htaccessAuth) {
    # note: no slash at the end
    $fullPath = "%fullPath%";
}

# file management parameters
$fileManagement = true;

# Size in bytes for uploads
# Default is 10Mb
$maxFileSize = 10485760;

# Root Path
# note: no slash at the end
$root = "http://localhost/phpcollab";

# security issue to disallow php files upload
$allowPhp = false;

# project site creation
$sitePublish = true;

# enable update checker
# (default) true
$updateChecker = true;

# e-mail notifications
# (default) true
$notifications = true;

# show peer review area
$peerReview = true;

# show items for home
$showHomeBookmarks = true;
$showHomeProjects = true;
$showHomeTasks = true;
$showHomeDiscussions = true;
$showHomeReports = true;
$showHomeNotes = true;
$showHomeNewsdesk = true;
$showHomeSubtasks = true;

# security issue to disallow auto-login from external link
$forcedLogin = false;

# table prefix
$tablePrefix = "";

# database tables
$tableCollab["assignments"] = "assignments";
$tableCollab["calendar"] = "calendar";
$tableCollab["files"] = "files";
$tableCollab["logs"] = "logs";
$tableCollab["members"] = "members";
$tableCollab["notes"] = "notes";
$tableCollab["notifications"] = "notifications";
$tableCollab["organizations"] = "organizations";
$tableCollab["posts"] = "posts";
$tableCollab["projects"] = "projects";
$tableCollab["reports"] = "reports";
$tableCollab["sorting"] = "sorting";
$tableCollab["tasks"] = "tasks";
$tableCollab["teams"] = "teams";
$tableCollab["topics"] = "topics";
$tableCollab["phases"] = "phases";
$tableCollab["support_requests"] = "support_requests";
$tableCollab["support_posts"] = "support_posts";
$tableCollab["subtasks"] = "subtasks";
$tableCollab["updates"] = "updates";
$tableCollab["bookmarks"] = "bookmarks";
$tableCollab["bookmarks_categories"] = "bookmarks_categories";
$tableCollab["invoices"] = "invoices";
$tableCollab["invoices_items"] = "invoices_items";
$tableCollab["services"] = "services";
$tableCollab["newsdeskcomments"] = "newsdeskcomments";
$tableCollab["newsdeskposts"] = "newsdeskposts";

# demo mode parameters
$demoMode = false;
$urlContact = "https://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
$activeJpgraph = true;

# filter to see only logged user clients (in team / owner)
$clientsFilter = false;

# filter to see only logged user projects (in team / owner)
$projectsFilter = false;

# Enable help center support requests
$enableHelpSupport = true;

# Return email address given for clients to respond too.
$supportEmail = "email@yourdomain.com";

# Support Type
# Options: (default) team | admin
# If team is selected, a notification will be sent to everyone in the team when a new request is added
$supportType = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
$lastvisitedpage = false;

# auto-publish tasks added from client site?
$autoPublishTasks = false;

# html header parameters
$setDoctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
$setTitle = "PhpCollab";
$siteTitle = "PhpCollab";
$setDescription = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
$setKeywords = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

# Email alerts
$emailAlerts = false;


/**
 * Authentication Settings
 */
$resetPasswordTimes = [
    'tokenLifespan' => 60,
    'timeBetweenAttempts' => 15,
    'attemptLimit' => 3
];

/**
 * Debugging Settings.
 * DO NOT Change these on a Production server unless you know what you are doing.
 * Refer to: https://phpcollab.com/debugging for more information
 */

# enable development bar in footer
$footerDev = false;

$logLevel = 400;
