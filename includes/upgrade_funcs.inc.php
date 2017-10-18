<?php
/*
** Application name: phpCollab
** Path by root: ../inlcudes/upgrade_funcs.inc.php
** Since: 2.5 rc3
** Authors: Norman77 / Mindblender
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: upgrade.php
**
** DESC: Functions used in the upgrade file and procedures
**
** =============================================================================
** CVS Tags and Keywords
** Last Editor  : $Author: norman77 $
** Last Edit    : $Date: 2009/01/21 21:29:18 $
** Version      : $Revision: 1.2 $
** =============================================================================
*/

/**
 * getParameter
 * Get's a parameter from GET or POST (auto detected, POST first) that is safe for use
 * in queries or use unchecked.. will return type that's in the paramter..
 * Returns null if parameter was not found.
 * @return mixed
 * @param String $paramName
 */
function getParameter($paramName)
{
    //Let's sanatize the param just in case, let's be extermly paranoid here
    $paramName = sanatize($paramName, false);
    $ret = null;

    if (array_key_exists($paramName, $_POST)) {
        $ret = sanatize($_POST[$paramName]);
    } elseif (array_key_exists($paramName, $_GET)) {
        $ret = sanatize($_GET[$paramName]);
    }

    return $ret;
}

/**
 * sanatize
 * Returns a clean version of data.
 * If allowSpecChars is true, will allow special characters (shift of numbers) otherwise it's only
 * letters and numbers (including space and period)
 * @return String
 * @param object $data
 * @param object $allowSpecChars [optional]
 */
function sanatize($data, $allowSpecChars = false)
{
    if (!$allowSpecChars) {
        $data = preg_replace('/^\W+|\W+$/', '', $data);
    }
    return $data;
}

/**
 * checkDatabase()
 * Check to see if the database should be upgraded from 2.4 to 2.5
 * @return bool
 */
function checkDatabase($errorMsg = '')
{
    $settingsFile = dirname(realpath(__FILE__)) . "/settings.php";
    include_once($settingsFile);

    if (file_exists($settingsFile)) {
        //first see if these database tables are missing from the table
        if (!array_key_exists("invoices", $tableCollab) ||
            !array_key_exists("invoices_items", $tableCollab) ||
            !array_key_exists("services", $tableCollab) ||
            !array_key_exists("newsdeskcomments", $tableCollab) ||
            !array_key_exists("newsdeskposts", $tableCollab)
        ) {

            //-------------
            return true; // Database should be upgraded
        } else {
            $errorMsg = "It appears that the tables have already been added.  Manual update is only possible at this time.";
        }
    }

    return false;
}

/**
 * convertDB()
 * Converts the DB, check's if it should be done first (ie: adding fields/tables)
 * @return bool
 */
function convertDB()
{
    $settingsFile = dirname(realpath(__FILE__)) . "/settings.php";
    include $settingsFile; // Reimport :)

    echo ":: Connecting to DB Server....";

    $errorMsg = null;
    $conn = connectDB($databaseType, MYSERVER, MYLOGIN, MYPASSWORD, MYDATABASE);

    if (!empty($conn)) {
        echo "<font style='font-weight: bold;color: green'>done</font> :: <br />";
    } else {
        echo "<font style='font-weight: bold;color: red'>ERROR</font> :: <br />";
        return false;
    }
    echo ":: Beggining Database Conversion... :: <br /><ul>";

    //Add Tables
    echo "<li>Adding new tables...";
    //Add the DB Vars and let's try to do this stuffs.. 
    if (addTables($databaseType, $conn, $tablePrefix, $errorMsg))
        echo "<font style='color: green;font-weight: bold'>completed</font>";
    else {
        echo "<font style='color: red;font-weight: bold'>error</font>";
        if (!empty($errorMsg)) echo "<br /><b>Error Messages: </b>$errorMsg";
        return false;
    }

    //Now let's reset the Error message, just in case
    $errorMsg = null;

    //Modify existing tables
    echo "<li>Updating existing tables...";
    if (modTables($databaseType, $conn, $tablePrefix, $errorMsg))
        echo "<font style='color: green;font-weight: bold'>completed</font>";
    else {
        echo "<font style='color: red;font-weight: bold'>error</font>";
        if (!empty($errorMsg)) echo "<br /><b>Error Messages: </b>$errorMsg";
        return false;
    }

    //Run tests
    $errorMsg = null;

    echo "</ul>:: Ending Conversion ::";
    return true;
}

/**
 * addTables
 * Adds new tables to the database, dependant on the type.  Connectino is already established in conn
 * @return bool
 * @param String $type
 * @param object $conn
 * @param object $prefix [optional]
 * @param object $errorMsg [optional]
 */
function addTables($type, $conn, $prefix = "", $errorMsg = null)
{
    if (empty($type) || empty($conn)) {
        return false;
    }

    $tableFields = array(
        "invoices" => array(
            "id" => "mediumint_auto",
            "project" => "mediumint",
            "header_note" => "text",
            "footer_note" => "text",
            "date_sent" => "varchar10",
            "due_date" => "varchar10",
            "total_ex_tax" => "float",
            "tax_rate" => "float",
            "tax_amount" => "float",
            "total_inc_tax" => "float",
            "status" => "char1",
            "active" => "char1",
            "created" => "varchar16",
            "modified" => "varchar16",
            "published" => "char1"
        ),
        "invoices_items" => array(
            "id" => "mediumint_auto",
            "invoice" => "mediumint",
            "position" => "mediumint",
            "mod_type" => "char1",
            "mod_value" => "mediumint",
            "title" => "varchar155",
            "description" => "text",
            "worked_hours" => "float",
            "amount_ex_tax" => "float",
            "rate_type" => "varchar10",
            "rate_value" => "float",
            "status" => "char1",
            "active" => "char1",
            "completed" => "char1",
            "created" => "varchar16",
            "modified" => "varchar16"
        ),
        "services" => array(
            "id" => "mediumint_auto",
            "name" => "varchar155",
            "name_print" => "varchar155",
            "hourly_rate" => "float"
        ),
        "newsdeskcomments" => array(
            "id" => "mediumint_auto",
            "post_id" => "mediumint",
            "name" => "mediumint",
            "comment" => "text"
        ),
        "newsdeskposts" => array(
            "id" => "mediumint_auto",
            "pdate" => "varchar16",
            "title" => "varchar155",
            "author" => "mediumint",
            "related" => "varchar155",
            "content" => "text",
            "links" => "text",
            "rss" => "char1"
        )
    );

    //Get DB Vaars
    $dbvar = dirname(realpath(__FILE__)) . "/db_var.inc.php";
    require_once($dbvar);

    // okay let's start it up
    foreach (array_keys($tableFields) as $table) {
        //Let's do the PGSQL Stuff
        if ($type == "postgresql") {
            //Create the sequence.. 
            $sql = "CREATE SEQUENCE {$prefix}{$table}_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1";
            $db_mediumint_auto[$type] = "int4 DEFAULT nextval('" . $prefix . "{$table}_seq'::text) NOT NULL";
            pg_query($conn, $sql);

            if (pg_last_error($conn) != 0) {
                $errorMsg = "PGSQL Panic: " . pg_last_error($conn);
                return;
            }
        }

        //Continuing on.. 
        // Create table and such
        $t_sql = "CREATE TABLE {$prefix}{$table} (";
        //Do all the fields
        $tf_sql = '';

        foreach ($tableFields[$table] as $field => $ftype) {
            $mytype = '';
            eval("\$mytype = \$db_{$ftype}['$type'];");
            $tf_sql .= "$field $mytype, ";
        }

        $t_sql .= $tf_sql . "PRIMARY KEY(id))";

        if ($type == "mysql") {
            mysql_query($t_sql, $conn);
            if (mysql_errno($conn) != 0) {
                $errorMsg = "MYSQL Panic! : " . mysql_error($conn) . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        } elseif ($type == "mssql") {
            mssql_query($t_sql, $conn);
            if (mssql_get_last_message() != 0) {
                $errorMsg = "MSSQL Panic! : " . mssql_get_last_message() . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        } elseif ($type == "postgresql") {
            pg_query($conn, $t_sql);
            if (pg_last_error($conn) != 0) {
                $errorMsg = "PG-SQL Panic! : " . pg_last_error($conn) . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        }
    }

    return true;
}


/**
 * modTables
 * Modifies tables, depending on what's in the array in the function
 * @return bool
 * @param String $type
 * @param object $conn
 * @param object $prefix [optional]
 * @param object $errorMsg [optional]
 */
function modTables($type, $conn, $prefix = "", $errorMsg = null)
{
    //Get DB Vaars
    $dbvar = dirname(realpath(__FILE__)) . "/db_var.inc.php";
    include $dbvar;

    if (empty($type) || empty($conn)) {
        return false;
    }

    $tableFields = array(
        "calendar" => array(
            "broadcast" => "char1",
            "location" => "varchar155"
        ),
        "notifications" => array(
            "uploadFile" => "char1"
        ),
        "organizations" => array(
            "hourly_rate" => "float"
        ),
        "projects" => array(
            "invoicing" => "char1",
            "hourly_rate" => "float"
        ),
        "sorting" => array(
            "invoices" => "varchar155",
            "newsdesk" => "varchar155"
        ),
        "tasks" => array(
            "invoicing" => "char1",
            "worked_hours" => "float"
        )
    );

    //Get DB Vaars
    $dbvar = dirname(realpath(__FILE__)) . "/db_var.inc.php";
    require_once($dbvar);

    // okay let's start it up
    foreach (array_keys($tableFields) as $table) {

        //Continuing on.. 
        // Create table and such
        $t_sql = "ALTER TABLE {$prefix}{$table} ";
        //Do all the fields
        $tf_sql = '';

        foreach ($tableFields[$table] as $field => $ftype) {
            $mytype = '';
            eval("\$mytype = \$db_{$ftype}['$type'];");
            if ($tf_sql != '') $tf_sql .= ", ADD $field $mytype";
            else $tf_sql = "ADD $field $mytype";
        }

        $t_sql .= $tf_sql;

        if ($type == "mysql") {
            mysql_query($t_sql, $conn);
            if (mysql_errno($conn) != 0) {
                $errorMsg = "MYSQL Panic! : " . mysql_error($conn) . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        } elseif ($type == "mssql") {
            mssql_query($t_sql, $conn);
            if (mssql_get_last_message() != 0) {
                $errorMsg = "MSSQL Panic! : " . mssql_get_last_message() . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        } elseif ($type == "postgresql") {
            pg_query($conn, $t_sql);
            if (pg_last_error($conn) != 0) {
                $errorMsg = "PG-SQL Panic! : " . pg_last_error($conn) . "<br />Could not continue, upgrade was not completed.";
                return;
            }
        }
    }
    return true;
}

/**
 * connectDB
 * Connects to the Database, dependant on the server type
 * @return object
 * @param String $db Database Type
 * @param String $server
 * @param String $user
 * @param String $pwd
 * @param String $dbname
 */
function connectDB($db, $server, $user, $pwd, $dbname)
{
    $my = null;

    switch ($db) {
        case 'mysql':
            $my = mysql_connect($server, $user, $pwd, true);
            mysql_select_db($dbname, $my);
            break;

        case 'postgresql':
            $my = pg_connect("host=$server port=55432 dbname=$dbname user=$user password=$pwd");
            break;

        case 'sqlserver':
            $my = @mssql_connect($server, $user, $pwd);
            mssql_select_db($dbname, $my);
            break;
    }

    return $my;
}

function rewriteConfig($settingsFile)
{
    //Okay this is the icky part.. We are going to open this up and you know do all the stuff
    include $settingsFile;
    $myserver = MYSERVER;
    $mylogin = MYLOGIN;
    $mypassword = MYPASSWORD;
    $mydatabase = MYDATABASE;

    if (defined("SMTPSERVER")) $smtpserver = SMTPSERVER; else $smtpserver = '';
    if (defined("SMTPLOGIN")) $smtplogin = SMTPLOGIN; else $smtplogin = '';
    if (defined("SMTPPASSWORD")) $smtppassword = SMTPPASSWORD; else $smtppassword = '';

    if (defined("FTPSERVER")) $ftpserver = FTPSERVER; else $ftpserver = '';
    if (defined("FTPLOGIN")) $ftplogin = FTPLOGIN; else $ftplogin = '';
    if (defined("FTPPASSWORD")) $ftppassword = FTPPASSWORD; else $ftppassword = '';

    $theme = THEME;

    $docType = addslashes($setDoctype);
    $docDesc = addslashes($setDescription);
    $docWords = addslashes($setKeywords);

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
\$notificationMethod = "$notificationMethod"; //select "mail" or "smtp"

# smtp parameters (only if \$notificationMethod == "smtp")
define('SMTPSERVER','$smtpserver');
define('SMTPLOGIN','$smtplogin');
define('SMTPPASSWORD','$smtppassword');

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
define('THEME','$theme');

# newsdesk limiter
\$newsdesklimit = 1;

# if 1 the admin logs in his homepage
\$adminathome = 0;

# timezone GMT management
\$gmtTimezone = "$gmtTimezone";

# language choice
\$langDefault = "$langdefault";

# Mantis bug tracking parameters
// Should bug tracking be enabled?
\$enableMantis = "$enableMantis";

// Mantis installation directory
\$pathMantis = "$pathMantis";  // add slash at the end

# CVS parameters
// Should CVS be enabled?
\$enable_cvs = "$enable_cvs";

// Should browsing CVS be limited to project members?
\$cvs_protected = "$cvs_protected";

// Define where CVS repositories should be stored
\$cvs_root = "$cvs_root"; //no slash at the end

// Who is the owner CVS files?
// Note that this should be user that runs the web server.
// Most *nix systems use "httpd" or "nobody"
\$cvs_owner = "$cvs_owner";

// CVS related commands
\$cvs_co = "$cvs_co";
\$cvs_rlog = "$cvs_rlog";
\$cvs_cmd = "$cvs_cmd";

# https related parameters
\$pathToOpenssl = "$pathToOpenssl";

# login method, set to "CRYPT" in order CVS authentication to work (if CVS support is enabled)
\$loginMethod = "$loginMethod"; //select "MD5", "CRYPT", or "PLAIN"

# enable LDAP
\$useLDAP = "$useLDAP";
\$configLDAP[ldapserver] = "{$configLDAP[ldapserver]}";
\$configLDAP[searchroot] = "{$configLDAP[searchroot]}";

# htaccess parameters
\$htaccessAuth = "$htaccessAuth";
\$fullPath = "$fullPath"; //no slash at the end

# file management parameters
\$fileManagement = "$fileManagement";
\$maxFileSize = $maxFileSize; //bytes limit for upload
\$root = "$root"; //no slash at the end

# security issue to disallow php files upload
\$allowPhp = "$allowPhp";

# project site creation
\$sitePublish = "$sitePublish";

# enable update checker
\$updateChecker = "$updateChecker";

# e-mail notifications
\$notifications = "$notifications";

# show peer review area
\$peerReview = "$peerReview";

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
\$forcedLogin = "$forcedLogin";

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
\$version = "$ver";

# demo mode parameters
\$demoMode = "false";
\$urlContact = "$urlContact";

# Gantt graphs
\$activeJpgraph = "$activeJpgraph";

# developement options in footer
\$footerDev = "$footerDev";

# filter to see only logged user clients (in team / owner)
\$clientsFilter = "$clientsFilter";

# filter to see only logged user projects (in team / owner)
\$projectsFilter = "$projectsFilter";

# Enable help center support requests, values "true" or "false"
\$enableHelpSupport = "$enableHelpSupport";

# Return email address given for clients to respond too.
\$supportEmail = "$supportEmail";

# Support Type, either team or admin. If team is selected a notification will be sent to everyone in the team when a new request is added
\$supportType = "$supportType";

# enable the redirection to the last visited page, EXPERIMENTAL DO NOT USE IT
\$lastvisitedpage = false;

# auto-publish tasks added from client site?
\$autoPublishTasks = false;

# html header parameters
\$setDoctype = "$docType";
\$setTitle = "$setTitle";
\$setDescription = "$docDesc";
\$setKeywords = "$docWords";

# Email alerts
\$emailAlerts = "$emailAlerts";
?>
STAMP;

    return file_put_contents($settingsFile, $content);
}
/* End of file upgrade_funcs.inc.php */
/* Location: ./includes/upgrade_funcs.inc.php */