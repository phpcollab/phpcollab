<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings_default.php

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
define('SMTPSERVER', '');
define('SMTPLOGIN', '');
define('SMTPPASSWORD', '');

# create folder method
$mkdirMethod = "PHP"; //select "FTP" or "PHP"

# ftp parameters (only if $mkdirMethod == "FTP")
define('FTPSERVER', '');
define('FTPLOGIN', '');
define('FTPPASSWORD', '');

# PhpCollab root according to ftp account (only if $mkdirMethod == "FTP")
$ftpRoot = ""; //no slash at the end

# Invoicing module
$enableInvoicing = "true";

# theme choice
define('THEME', 'default');

// NewsDesk
$newsdesklimit = 1;

// if 1 the admin logs in his homepage
$adminathome = 0;

# session.trans_sid forced
$trans_sid = "true";

# timezone GMT management
$gmtTimezone = "false";

# language choice
$langDefault = "en";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
$enableMantis = "false";

// Mantis installation directory
$pathMantis = "http://localhost/mantis/";  // add slash at the end

# CVS parameters
// Should CVS be enabled?
$enable_cvs = "false";

// Should browsing CVS be limited to project members?
$cvs_protected = "false";

// Define where CVS repositories should be stored
$cvs_root = "D:\cvs"; //no slash at the end

// Who is the owner CVS files?
// Note that this should be user that runs the web server.
// Most *nix systems use "httpd" or "nobody"
$cvs_owner = "httpd";

// CVS related commands
$cvs_co = "/usr/bin/co";
$cvs_rlog = "/usr/bin/rlog";
$cvs_cmd = "/usr/bin/cvs";

# https related parameters
$pathToOpenssl = "/usr/bin/openssl";

# login method, set to "CRYPT" in order CVS authentication to work (if CVS support is enabled)
$loginMethod = "CRYPT"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
$useLDAP = "false";
$configLDAP[ldapserver] = "your.ldap.server.address";
$configLDAP[searchroot] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
$htaccessAuth = "false";
$fullPath = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
$fileManagement = "true";
$maxFileSize = 51200; //bytes limit for upload
$root = "http://localhost/phpcollab"; //no slash at the end

# security issue to disallow php files upload
$allowPhp = "false";

# project site creation
$sitePublish = "true";

# enable update checker
$updateChecker = "true";

# e-mail notifications
$notifications = "true";

# show peer review area
$peerReview = "true";

# show items for home
$showHomeBookmarks = "true";
$showHomeProjects = "true";
$showHomeTasks = "true";
$showHomeDiscussions = "true";
$showHomeReports = "true";
$showHomeNotes = "true";
$showHomeNewsdesk = "true";
$showHomeSubtasks = "true";

# security issue to disallow auto-login from external link
$forcedLogin = "false";

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

# PhpCollab version
$version = "2.5.1";

# demo mode parameters
$demoMode = "false";
$urlContact = "http://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
$activeJpgraph = "true";

# developement options in footer
$footerDev = "false";

# filter to see only logged user clients (in team / owner)
$clientsFilter = "false";

# filter to see only logged user projects (in team / owner)
$projectsFilter = "false";

# Enable help center support requests, values "true" or "false"
$enableHelpSupport = "true";

# Return email address given for clients to respond too.
$supportEmail = "email@yourdomain.com";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new request is added
$supportType = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
$lastvisitedpage = false;

# auto-publish tasks added from client site?
$autoPublishTasks = false;

# html header parameters
$setDoctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
$setTitle = "PhpCollab";
$setDescription = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
$setKeywords = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

# Email alerts.
$emailAlerts = "false";

?>
