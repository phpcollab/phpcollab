<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/settings.php
$settings = array();

# installation type
$settings['installationType'] = "offline"; //select "offline" or "online"

# select database application
$settings['databaseType'] = "mysql"; //select "sqlserver", "postgresql" or "mysql"

# database parameters
define('MYSERVER','localhost');
define('MYLOGIN','phpcollab');
define('MYPASSWORD','phpcollab');
define('MYDATABASE','phpcollab_v2');

# notification method
$notificationMethod = "mail"; //select "mail" or "smtp"

# smtp parameters (only if $notificationMethod == "smtp")
define('SMTPSERVER','');
define('SMTPLOGIN','');
define('SMTPPASSWORD','');

# create folder method
$mkdirMethod = "PHP"; //select "FTP" or "PHP"

# ftp parameters (only if $mkdirMethod == "FTP")
define('FTPSERVER','');
define('FTPLOGIN','');
define('FTPPASSWORD','');

# PhpCollab root according to ftp account (only if $mkdirMethod == "FTP")
$settings['ftpRoot'] = ""; //no slash at the end

# Invoicing module
$settings['enableInvoicing'] = "true";

# theme choice
define('THEME','default');

# newsdesk limiter
$settings['newsdesklimit'] = 1;

# if 1 the admin logs in his homepage
$settings['adminathome'] = 0;

# session.trans_sid forced
$settings['trans_sid'] = "true";

# timezone GMT management
$settings['gmtTimezone'] = "false";

# language choice
$settings['langDefault'] = "en";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
$settings['enableMantis'] = "false";

// Mantis installation directory
$settings['pathMantis'] = "http://localhost/mantis/";  // add slash at the end

# CVS parameters
# TODO: RIP OUT CVS!!!!!!!!!!!
// Should CVS be enabled?
$settings['enable_cvs'] = "false";

// Should browsing CVS be limited to project members?
$settings['cvs_protected'] = "false";

// Define where CVS repositories should be stored
$settings['cvs_root'] = "D:\cvs"; //no slash at the end

// Who is the owner CVS files?
// Note that this should be user that runs the web server.
// Most *nix systems use "httpd" or "nobody"
$settings['cvs_owner'] = "httpd";

// CVS related commands
$settings['cvs_co'] = "/usr/bin/co";
$settings['cvs_rlog'] = "/usr/bin/rlog";
$settings['cvs_cmd'] = "/usr/bin/cvs";

# https related parameters
$settings['pathToOpenssl'] = "/usr/bin/openssl";

# login method, set to "CRYPT" in order CVS authentication to work (if CVS support is enabled)
$settings['loginMethod'] = "CRYPT"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
$settings['useLDAP'] = "false";
$configLDAP[ldapserver] = "your.ldap.server.address";
$configLDAP[searchroot] = "ou=People, ou=Intranet, dc=YourCompany, dc=com";

# htaccess parameters
$settings['htaccessAuth'] = "false";
$settings['fullPath'] = "/usr/local/apache/htdocs/phpcollab/files"; //no slash at the end

# file management parameters
$settings['fileManagement'] = "true";
$settings['maxFileSize'] = 2048000; //bytes limit for upload
$settings['root'] = "http://phpcollab.dev"; //no slash at the end

# security issue to disallow php files upload
$settings['allowPhp'] = "false";

# project site creation
$settings['sitePublish'] = "true";

# enable update checker
$settings['updateChecker'] = "true";

# e-mail notifications
$settings['notifications'] = "true";

# show peer review area
$settings['peerReview'] = "true";

# show items for home
$settings['showHomeBookmarks'] = true;
$settings['showHomeProjects'] = true;
$settings['showHomeTasks'] = true;
$settings['showHomeDiscussions'] = true;
$settings['showHomeReports'] = true;
$settings['showHomeNotes'] = true;
$settings['showHomeNewsdesk'] = true;
$settings['showHomeSubtasks'] = true;

# security issue to disallow auto-login from external link
$settings['forcedLogin'] = "false";

# table prefix
$settings['tablePrefix'] = "";

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
$settings['version'] = "2.5.1";

# demo mode parameters
$settings['demoMode'] = "false";
$settings['urlContact'] = "http://www.sourceforge.net/projects/phpcollab";

# Gantt graphs
$settings['activeJpgraph'] = "true";

# developement options in footer
$settings['footerDev'] = "false";

# filter to see only logged user clients (in team / owner)
$settings['clientsFilter'] = "false";

# filter to see only logged user projects (in team / owner)
$settings['projectsFilter'] = "false";

# Enable help center support requests, values "true" or "false"
$settings['enableHelpSupport'] = "true";

# Return email address given for clients to respond too.
$settings['supportEmail'] = "email@yourdomain.com";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new phpCollab\Request is added
$settings['supportType'] = "team";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
$settings['lastvisitedpage'] = false;

# auto-publish tasks?
$settings['autoPublishTasks'] = false;

# html header parameters
$settings['setDoctype'] = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">";
$settings['setTitle'] = "PhpCollab";
$settings['setDescription'] = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
$settings['setKeywords'] = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

# Email alerts
$settings['emailAlerts'] = "false";
?>