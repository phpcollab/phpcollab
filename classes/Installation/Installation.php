<?php


namespace phpCollab\Login\Installation;

use phpCollab\Login\Database;

class Installation
{
    protected $db;

    public function __construct($host, $dbname, $user, $pass)
    {
        if (!$host && !$dbname && !$user && !$pass) {
            throw new \Exception('No database information found.');
        }

        try {
            $this->db = new Database($host, $dbname, $user, $pass);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }


//        xdebug_var_dump($this->db);
    }

    public function installTables()
    {
        return "installTables";
    }

    /**
     * @param string $installationType
     * @param string $databaseType
     * @param string $dbServer
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbName
     * @param string $mkDirMethod
     * @param string $ftpServer
     * @param string $ftpUser
     * @param string $ftpPassword
     * @param string $ftpRoot
     * @param string $langDefault
     * @param string $loginMethod
     * @param string $root
     * @param boolean $updatechecker
     * @param boolean $notifications
     * @param boolean $forcedlogin
     * @param string $myprefix dbTable prefix
     * @param string $version phpCollab version
     */
    public function writeSettingsFile(
        $databaseType, $dbServer, $dbUser, $dbPassword, $dbName, $myprefix,
        $ftpServer, $ftpUser, $ftpPassword, $ftpRoot,
        $installationType, $langDefault, $mkDirMethod, $loginMethod, $root, $updatechecker,
        $notifications, $forcedlogin,  $version)
    {
        echo 'write the settings file';

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

# timezone GMT management
\$gmtTimezone = "false";

# language choice
\$langDefault = "$langdefault";

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
\$tableCollab["assignments"] = "{$myprefix}

assignments";
\$tableCollab["calendar"] = "{
    $myprefix}calendar";
\$tableCollab["files"] = "{
    $myprefix}files";
\$tableCollab["logs"] = "{
    $myprefix}logs";
\$tableCollab["members"] = "{
    $myprefix}members";
\$tableCollab["notes"] = "{
    $myprefix}notes";
\$tableCollab["notifications"] = "{
    $myprefix}notifications";
\$tableCollab["organizations"] = "{
    $myprefix}organizations";
\$tableCollab["posts"] = "{
    $myprefix}posts";
\$tableCollab["projects"] = "{
    $myprefix}projects";
\$tableCollab["reports"] = "{
    $myprefix}reports";
\$tableCollab["sorting"] = "{
    $myprefix}sorting";
\$tableCollab["tasks"] = "{
    $myprefix}tasks";
\$tableCollab["teams"] = "{
    $myprefix}teams";
\$tableCollab["topics"] = "{
    $myprefix}topics";
\$tableCollab["phases"] = "{
    $myprefix}phases";
\$tableCollab["support_requests"] = "{
    $myprefix}support_requests";
\$tableCollab["support_posts"] = "{
    $myprefix}support_posts";
\$tableCollab["subtasks"] = "{
    $myprefix}subtasks";
\$tableCollab["updates"] = "{
    $myprefix}updates";
\$tableCollab["bookmarks"] = "{
    $myprefix}bookmarks";
\$tableCollab["bookmarks_categories"] = "{
    $myprefix}bookmarks_categories";
\$tableCollab["invoices"] = "{
    $myprefix}invoices";
\$tableCollab["invoices_items"] = "{
    $myprefix}invoices_items";
\$tableCollab["services"] = "{
    $myprefix}services";
\$tableCollab["newsdeskcomments"] = "{
    $myprefix}newsdeskcomments";
\$tableCollab["newsdeskposts"] = "{
    $myprefix}newsdeskposts";

# PhpCollab version
\$version = "
$version";

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
    }

}