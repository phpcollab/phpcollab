<?php
/*
** Application name: phpCollab
** Last Edit page: 2004-08-23 
** Path by root: ../includes/library.php
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
** FILE: library.php
**
** DESC: Screen: library file 
**
** HISTORY:
**  2004-08-23  -   add/complete switch according to php version
**  2004-08-23  -   update register_globals cheat code to be compatible with php5 
**  18/02/2005	-	added check_FileName() function
**	26/05/2005	-	fix for http://www.php-collab.org/community/viewtopic.php?t=2002
**	03/06/2005	-	mod by dracono http://www.php-collab.org/community/viewtopic.php?t=2022
**	03/06/2005	-	fix for http://www.php-collab.org/community/viewtopic.php?t=2023
**	28/06/2005	-	fix for 840757 
**  17/03/2007 	- 	fix for 1446645
**  17/03/2007 	- 	fix for 1632170
**  31/07/2007  -   removed notifications include from the indexRedirect if block. 
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
**
** New Edit Blocks
** Last Modified: $Date: 2009/02/01 13:52:37 $
** RCS: $Id: library.php,v 1.23 2009/02/01 13:52:37 norman77 Exp $
** -- Edit Log: --
** 2008-11-18   -   Updated the library.php to reflect the new settings object. (dab-norman77)
**
*/

if (ini_get('session.auto_start') == 0) {
    $profilSession = "";
}

error_reporting(2039);
@ini_set("session.use_trans_sid", 0);

//disable session on export
if ($export != "true") {
    session_start();
}

/**
 * Wrapper to make sure null strings display as 0 in sql queries
 * @param string $var An integer represented as a string
 **/
function fixInt($var)
{
    if ($var == '') {
        return 0;
    } else {
        return $var;
    }
}

// replace spec.chars , you can add rule
Function spechars($return)
{
    $return = str_replace('"', '&quot', $return);
    $return = str_replace("'", '&#039;', $return);
    $return = str_replace('=', '&#61;', $return);
    $return = str_replace('$', '&#36;', $return);
    $return = str_replace("\\", '&#92;', $return);
    return $return;
}

/**
 * Return global variable
 * @param string $var Variable name
 * @param string $type Variable type (SERVER, POST, GET, SESSION, REQUEST, COOKIE)
 * @access public
 **/
function returnGlobal($var, $type)
{
    if (phpversion() >= "4.1.0") {
        if ($type == "SERVER") {
            return spechars($_SERVER[$var]);
        }
        if ($type == "POST") {
            return spechars($_POST[$var]);
        }
        if ($type == "GET") {
            return spechars($_GET[$var]);
        }
        if ($type == "SESSION") {
            return spechars($_SESSION[$var]);
        }
        if ($type == "REQUEST") {
            return spechars($_REQUEST[$var]);
        }
        if ($type == "COOKIE") {
            return spechars($_COOKIE[$var]);
        }
    } else {
        global $$var;
        return $$var;
    }
}

// register_globals cheat code
if (ini_get(register_globals) != "1") {
    //GET and POST VARS
    while (list($key, $val) = @each($_REQUEST)) {
        $GLOBALS[$key] = spechars($val);
    }
    //$HTTP_SESSION_VARS
    while (list($key, $val) = @each($_SESSION)) {
        $GLOBALS[$key] = spechars($val);
    }
    //$HTTP_SERVER_VARS
    while (list($key, $val) = @each($_SERVER)) {
        $GLOBALS[$key] = spechars($val);
    }
}

$msg = returnGlobal('msg', 'GET');
$session = returnGlobal('session', 'GET');
$logout = returnGlobal('logout', 'GET');
$idSession = returnGlobal('idSession', 'SESSION');
$dateunixSession = returnGlobal('dateunixSession', 'SESSION');
$loginSession = returnGlobal('loginSession', 'SESSION');
$profilSession = returnGlobal('profilSession', 'SESSION');
$logouttimeSession = returnGlobal('logouttimeSession', 'SESSION');

/**
 * Check last version of PhpCollab
 * @param string $iCV Version to compare
 * @access public
 **/
function updatechecker($iCV)
{
    global $strings;

    $phpcollab_url = 'http://www.php-collab.org/website/version.txt';

    $url = parse_url($phpcollab_url);

    $connection_socket = @fsockopen($url['host'], 80, $errno, $errstr, 30);

    if ($connection_socket) {

        fputs($connection_socket, "GET /" . $url['path'] . ($url['query'] ? '?' . $url['query'] : '') . " HTTP/1.0\r\nHost: " . $url['host'] . "\r\n\r\n");
        $http_response = fgets($connection_socket, 22);

        if (preg_match("/200 OK/", $http_response, $regs)) {
            // WARNING: in file(), use a final URL to avoid any HTTP redirection
            $sVersiondata = join('', file($phpcollab_url));
            $aVersiondata = explode("|", $sVersiondata);
            $iNV = $aVersiondata[0];

            if ($iCV < $iNV) {
                $checkMsg = "<br/><b>" . $strings["update_available"] . "</b> " . $strings["version_current"] . " $iCV. " . $strings["version_latest"] . " $iNV.<br/>";
                $checkMsg .= "<a href='http://www.sourceforge.net/projects/phpcollab' target='_blank'>" . $strings["sourceforge_link"] . "</a>.";
            }
        } else {
            $checkMsg = $strings["version_check_error"];
        }

        fclose($connection_socket);

    } else {
        $checkMsg = $strings["version_check_error"] . "<br/>Error type: $errno - $errstr";
    }

    return $checkMsg;
}

/**
 * Calculate time to parse page (used with footer.php)
 * @access public
 **/
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$parse_start = getmicrotime();

//database update array
$updateDatabase = array(0 => "1.0", 1 => "1.1", 2 => "1.3", 3 => "1.4", 4 => "1.6", 5 => "1.8", 6 => "1.9", 7 => "2.0", 8 => "2.1", 9 => "2.5");

//languages array
$languageArray = array(
    "ar" => "Arabic",
    "az" => "Azerbaijani",
    "pt-br" => "Brazilian Portuguese",
    "bg" => "Bulgarian",
    "ca" => "Catalan",
    "zh" => "Chinese simplified",
    "zh-tw" => "Chinese traditional",
    "cs-iso" => "Czech (iso)",
    "cs-win1250" => "Czech (win1250)",
    "da" => "Danish",
    "nl" => "Dutch",
    "en" => "English",
    "et" => "Estonian",
    "fr" => "French",
    "de" => "German",
    "hu" => "Hungarian",
    "is" => "Icelandic",
    "in" => "Indonesian",
    "it" => "Italian",
    "ja" => "Japanese",
    "ko" => "Korean",
    "lv" => "Latvian",
    "no" => "Norwegian",
    "pl" => "Polish",
    "pt" => "Portuguese",
    "ro" => "Romanian",
    "ru" => "Russian",
    "sk-win1250" => "Slovak (win1250)",
    "es" => "Spanish",
    "tr" => "Turkish",
    "uk" => "Ukrainian"
);

/**
 * setLanguage
 * Get the language from the HTTP_ACCEPT_LANGUAGE header and use that,
 * otherwise set it to English
 */
function setLanguage()
{
    global $langDefault;
    $preferred_locale = array();
    $preferred_language = array();
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        preg_match_all('/([a-z]{2})(?:-[a-zA-Z]{2})?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
        foreach ($matches[0] as $locale) {
            $preferred_locale[] = strtolower($locale);
        }
        foreach ($matches[1] as $language) {
            if (!in_array($language, $preferred_language)) {
                $preferred_language[] = strtolower($language);
					}
				}
        /**
         * Now that we have the user's preferred locale/language, let's use the appropriate
         * language file.
         *
         * Selection uses locale first, then language, then default
         */
        if (file_exists("../languages/lang_" . $preferred_locale[0] . ".php")) {
            $langDefault = $preferred_locale[0];
        } else if (file_exists("../languages/lang_" . $preferred_language[0] . ".php")) {
            $langDefault = $preferred_language[0];
			} else {
				$langDefault = "en";
			}
	} else {
        // Set the language to English
		$langDefault = "en";
	}
}
if ($langDefault == "") {
    setLanguage();
}

//set language session
if ($langDefault != "") {
    $langSelected[$langDefault] = "selected";
} else {
    $langSelected = "";
}

if ($languageSession == "") {
    $lang = $langDefault;
} else {
    $lang = $languageSession;
}

/**
 * getLanguageDropdown
 * Compiles a select element containing all of the languages and sets the appropriate
 * selection based on $langDefault or a passed in value ($preferredLanguage)
 * @param null $preferredLanguage
 * @return string html select menu
 */
function getLanguageDropdown($preferredLanguage = null)
{
    global $langDefault, $languageArray, $lang;

    $preferredLanguage = (isset($preferredLanguage)) ? $preferredLanguage : $lang;
    $dropdown = '<select name="defaultLanguage">';
    if (!empty($langDefault) && $langDefault != $preferredLanguage) {
        $dropdown .= "<option value='$langDefault'>Default (" . $languageArray["$langDefault"] . ")</option>";
    }
    foreach ($languageArray as $language_code => $language_name) {
        if ($preferredLanguage == $language_code) {
            $dropdown .= "<option value=\"$language_code\" selected>$language_name</option>";
        } else {
            if (empty($langDefault) && $language_code == 'en') {
                $dropdown .= "<option value=\"$language_code\">Default ($language_name)</option>";
            } else {
                $dropdown .= "<option value=\"$language_code\">$language_name</option>";
            }
        }
    }
    $dropdown .= '</select>';
    return $dropdown;
}

$settings = null;
//settings and date selector includes
if ($indexRedirect == "true") {
    include("includes/settings.php");
//    echo "DEBUG:: Server - " . MYSERVER . "<br />User: " . MYLOGIN . "<br />Database: " . MYDATABASE;
    if (defined('CONVERTED') && CONVERTED) {
        require_once("includes/classes/settings.class.php");
        $settings = new Settings(true);
        $settings->makeGlobal();
    }
    
	include("includes/initrequests.php");
	include("includes/request.class.php");
	
	include("themes/".THEME."/block.class.php");

	include("languages/lang_en.php");

    $languageFile = "languages/lang_" . $lang . ".php";
    $localizedHelpFile = "languages/help_" . $lang . ".php";
    file_exists($languageFile) AND include $languageFile;
    file_exists($localizedHelpFile) AND include $localizedHelpFile;
} else {
    include("../includes/settings.php");

    if (defined('CONVERTED') && CONVERTED) {
        require_once("../includes/classes/settings.class.php");
        $settings = new Settings(true);
        $settings->makeGlobal();
    }

	if ($notifications == "true") {
		include("../includes/notification.class.php");
	}
		
	include("../includes/initrequests.php");
	include("../includes/request.class.php");
	
	include("../themes/".THEME."/block.class.php");
	
	include("../languages/lang_en.php");

    // TODO: Abstract this out to a function/localization Class
    $languageFile = "../languages/lang_" . $lang . ".php";
    $localizedHelpFile = "../languages/help_" . $lang . ".php";
    file_exists($languageFile) AND include $languageFile;
    file_exists($localizedHelpFile) AND include $localizedHelpFile;
}

//fix if update from old version
if ($theme == "") {
    $theme = "default";
}
if (!is_resource(THEME)) {
    define('THEME', $theme);
}
if (!is_resource(FTPSERVER)) {
    define('FTPSERVER', '');
}
if (!is_resource(FTPLOGIN)) {
    define('FTPLOGIN', '');
}
if (!is_resource(FTPPASSWORD)) {
    define('FTPPASSWORD', '');
}
if ($uploadMethod == "") {
    $uploadMethod = "PHP";
}
if ($peerReview == "") {
    $peerReview = "true";
}

if ($loginMethod == "") {
    $loginMethod = "PLAIN";
}
if ($databaseType == "") {
    $databaseType = "mysql";
}
if ($installationType == "") {
    $installationType = "online";
}

/**
 * Redirect to specified url
 * @param string $url Path to redirect
 * @access public
 **/
function headerFunction($url)
{
    header("Location:$url");
}

//check session validity on main phpcollab, except for demo user
if ($checkSession != "false" && $demoSession != "true") {
    if ($profilSession == "3" && !strstr($PHP_SELF, "projects_site")) {
        headerFunction("../projects_site/home.php");
    }

    if ($lastvisitedpage && $profilSession != "0") { // If the user has admin permissions, do not log the last page visited.
        if (!strstr($_SERVER['PHP_SELF'], "graph")) {
            $sidCode = session_name();
            $page = $_SERVER['PHP_SELF'] . "?" . $QUERY_STRING;
            $page = preg_replace('/(&' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = preg_replace('/(' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = strrev($page);
            $pieces = explode("/", $page);
            $pieces[0] = strrev($pieces[0]);
            $pieces[1] = strrev($pieces[1]);
            $page = $pieces[1] . "/" . $pieces[0];
            $tmpquery = "UPDATE " . $tableCollab["members"] . " SET last_page='$page' WHERE id = '" . fixInt($idSession) . "'";
            connectSql("$tmpquery");
        }
    }
    //if auto logout feature used, store last required page before deconnexion
    if ($profilSession != "3") {
        if ($logouttimeSession != "0" && $logouttimeSession != "") {
            $dateunix = date("U");
            $diff = $dateunix - $dateunixSession;

            if ($diff > $logouttimeSession) {
                headerFunction("../general/login.php?logout=true");
            } else {
                $dateunixSession = $dateunix;
                $_SESSION['dateunixSession'] = $dateunixSession;
            }
        }
    }

    $tmpquery = "WHERE log.login = '" . fixInt($loginSession) . "'";
    $checkLog = new request();
    $checkLog->openLogs($tmpquery);
    $comptCheckLog = count($checkLog->log_id);
    if ($comptCheckLog != "0") {
        if (session_id() != $checkLog->log_session[0]) {
            headerFunction("../index.php?session=false");
        }
    } else {
        headerFunction("../index.php?session=false");
    }
}

//count connected users
if ($checkConnected != "false") {
    $dateunix = date("U");
    $tmpquery1 = "UPDATE " . $tableCollab["logs"] . " SET connected='$dateunix' WHERE login = '$loginSession'";
    connectSql("$tmpquery1");
    $tmpsql = "SELECT * FROM " . $tableCollab["logs"] . " WHERE connected > $dateunix-5*60";
    compt($tmpsql);
    $connectedUsers = $countEnregTotal;
}

//redirect if server/database in error
if ($databaseType == "mysql") {
    if (!@mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD)) {
        headerFunction("../general/error.php?type=myserver");
        exit;
    } else {
        $res = mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
    }
    if (!@mysql_select_db(MYDATABASE, $res)) {
        headerFunction("../general/error.php?type=mydatabase");
        exit;
    } else {
        @mysql_close($res);
    }
}

//disable actions if demo user logged in demo mode
if ($action != "") {
    if ($demoSession == "true") {
        $closeTopic = "";
        $addToSiteTask = "";
        $removeToSiteTask = "";
        $addToSiteTopic = "";
        $removeToSiteTopic = "";
        $addToSiteTeam = "";
        $removeToSiteTeam = "";
        $action = "";
        $msg = "demo";
    }
}

/**
 * Automatic links
 * @param string $data Text to parse
 * @access public
 **/
function autoLinks($data)
{
    global $newText;
    $lines = explode("\n", $data);
    while (list ($key, $line) = each($lines)) {
        $line = preg_replace('|([ \t]|^)www\.|', ' http://www.', $line);

        $line = preg_replace('/([ \t]|^)ftp\./', ' ftp://ftp.', $line);
        $line = preg_replace('|(http://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
        $line = preg_replace('|(https://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
        $line = preg_replace('|(ftp://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
        $line = preg_replace('|([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))|', '<a href="mailto:$1">$1</a>', $line);

        if (empty($newText)) {
            $newText = $line;
        } else {
            $newText .= "\n$line";
        }
    }
}


/**
 * Return number of day between 2 dates
 * @param string $date1 Date to compare
 * @param string $date2 Date to compare
 * @access public
 **/
function diff_date($date1, $date2)
{
    $an = substr("$date1", 0, 4);
    $mois = substr("$date1", 5, 2);
    $jour = substr("$date1", 8, 2);

    $an2 = substr("$date2", 0, 4);
    $mois2 = substr("$date2", 5, 2);
    $jour2 = substr("$date2", 8, 2);

    $timestamp = mktime(0, 0, 0, $mois, $jour, $an);
    $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $an2);
    $diff = floor(($timestamp - $timestamp2) / (3600 * 24));
    return $diff;
}

/**
 * Checks for password match using the globally specified login method
 * @param string $formUsername User name to test
 * @param string $formPassword User name password to test
 * @param string $storedPassword Password stored in database
 * @access public
 **/
function is_password_match($formUsername, $formPassword, $storedPassword)
{
    global $loginMethod, $useLDAP, $configLDAP;

    if ($useLDAP == "true") {
        if ($formUsername == "admin") {
            switch ($loginMethod) {
                case MD5:
                    if (md5($formPassword) == $storedPassword) {
                        return true;
                    } else {
                        return false;
                    }
                case CRYPT:
                    $salt = substr($storedPassword, 0, 2);
                    if (crypt($formPassword, $salt) == $storedPassword) {
                        return true;
                    } else {
                        return false;
                    }
                case PLAIN:
                    if ($formPassword == $storedPassword) {
                        return true;
                    } else {
                        return false;
                    }
                    return false;
            }
        }
        $conn = ldap_connect($configLDAP[ldapserver]);
        $sr = ldap_search($conn, $configLDAP[searchroot], "uid=$formUsername");
        $info = ldap_get_entries($conn, $sr);
        $user_dn = $info[0]["dn"];
        if (!$bind = @ldap_bind($conn, $user_dn, $formPassword))
            return false;
        else
            return true;
    } else {
        switch ($loginMethod) {
            case MD5:
                if (md5($formPassword) == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
            case CRYPT:
                $salt = substr($storedPassword, 0, 2);
                if (crypt($formPassword, $salt) == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
            case PLAIN:
                if ($formPassword == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
                return false;
        }
    }
}

/**
 * Return a password using the globally specified method
 * @param string $newPassword Password to transfom
 * @access public
 **/
function get_password($newPassword)
{
    global $loginMethod;

    switch ($loginMethod) {
        case MD5:
            return md5($newPassword);
        case CRYPT:
            $salt = substr($newPassword, 0, 2);
            return crypt($newPassword, $salt);
        case PLAIN:
            return $newPassword;
            return $newPassword;
    }
}

/**
 * Generate a random password
 * @param string $size Size of geenrated password
 * @param boolean $with_numbers Option to use numbers
 * @param boolean $with_tiny_letters Option to use tiny letters
 * @param boolean $with_capital_letters Option to use capital letters
 * @access public
 **/
function password_generator($size = 8, $with_numbers = true, $with_tiny_letters = true, $with_capital_letters = true)
{
    global $pass_g;

    $pass_g = "";
    $sizeof_lchar = 0;
    $letter = "";
    $letter_tiny = "abcdefghijklmnopqrstuvwxyz";
    $letter_capital = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $letter_number = "0123456789";

    if ($with_tiny_letters == true) {
        $sizeof_lchar += 26;
        if (isset($letter)) {
            $letter .= $letter_tiny;
        } else {
            $letter = $letter_tiny;
        }
    }

    if ($with_capital_letters == true) {
        $sizeof_lchar += 26;
        if (isset($letter)) {
            $letter .= $letter_capital;
        } else {
            $letter = $letter_capital;
        }
    }

    if ($with_numbers == true) {
        $sizeof_lchar += 10;
        if (isset($letter)) {
            $letter .= $letter_number;
        } else {
            $letter = $letter_number;
        }
    }

    if ($sizeof_lchar > 0) {
        srand((double)microtime() * date("YmdGis"));
        for ($cnt = 0; $cnt < $size; $cnt++) {
            $char_select = rand(0, $sizeof_lchar - 1);
            $pass_g .= $letter[$char_select];
        }
    }
    return $pass_g;
}

/**
 * Move a file in a new destination
 * @param string $source Current path of file
 * @param string $dest New path of file
 * @access public
 **/
function moveFile($source, $dest)
{
    global $mkdirMethod, $ftpRoot;

    if ($mkdirMethod == "FTP") {
        $ftp = ftp_connect(FTPSERVER);
        ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
        ftp_rename($ftp, "$ftpRoot/$source", "$ftpRoot/$dest");
        ftp_quit($ftp);
    } else {
        copy("../" . $source, "../" . $dest);
    }
}

/**
 * Delete a file with a specified path
 * @param string $source Path of file
 * @access public
 **/
function deleteFile($source)
{
    global $mkdirMethod, $ftpRoot;

    if ($mkdirMethod == "FTP") {
        $ftp = ftp_connect(FTPSERVER);
        ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
        ftp_delete($ftp, $ftpRoot . "/" . $source);
        ftp_quit($ftp);
    } else {
        unlink("../" . $source);
    }
}

/**
 * Upload a file to a specified destination
 * @param string $path Path of original file
 * @param string $source Temp file
 * @param string $dest Destination path
 * @access public
 **/
function uploadFile($path, $source, $dest)
{
    global $mkdirMethod, $ftpRoot;

    $pathNew = $ftpRoot . "/" . $path;

    if (!file_exists($pathNew)) {
        # if there is no project dir first create it
        $path_info = pathinfo($path);
        if ($path != 'files/' . $path_info['basename']) {
            createDir($path_info['dirname']);
            createDir($path);
        } else {
            createDir($path);
        }
    }


    if ($mkdirMethod == "FTP") {
        $ftp = ftp_connect(FTPSERVER);
        ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
        ftp_chdir($ftp, $pathNew);
        ftp_put($ftp, $dest, $source, FTP_BINARY);
        ftp_quit($ftp);
    } else {
        @move_uploaded_file($source, "../" . $path . "/" . $dest);
    }
}

/**
 * Folder creation
 * @param string $path Path to the new directory
 * @access public
 **/
function createDir($path)
{
    global $mkdirMethod, $ftpRoot;

    if ($mkdirMethod == "FTP") {
        $pathNew = $ftpRoot . "/" . $path;

        $ftp = ftp_connect(FTPSERVER);
        ftp_login($ftp, FTPLOGIN, FTPPASSWORD);

        //if (!file_exists($pathNew))
        //{
        ftp_mkdir($ftp, $pathNew);
        //}

        ftp_quit($ftp);
    }

    if ($mkdirMethod == "PHP") {
        @mkdir("../$path", 0755);
        @chmod("../$path", 0777);
    }
}

/**
 * Folder recursive deletion
 * @param string $location Path of directory to delete
 * @access public
 **/
function delDir($location)
{
    if (is_dir($location)) {
        $all = opendir($location);
        while ($file = readdir($all)) {
            if (is_dir("$location/$file") && $file != ".." && $file != ".") {
                deldir("$location/$file");
                if (file_exists("$location/$file")) {
                    @rmdir("$location/$file");
                }
                unset($file);
            } else if (!is_dir("$location/$file")) {
                if (file_exists("$location/$file")) {
                    @unlink("$location/$file");
                }
                unset($file);
            }
        }
        closedir($all);
        @rmdir($location);
    } else {
        if (file_exists("$location")) {
            @unlink("$location");
        }
    }
}

/**
 * Return recursive folder size
 * @param string $location Path of directory to calculate
 * @param boolean $recursive Option to use recursivity
 * @access public
 **/
function folder_info_size($path, $recursive = TRUE)
{
    $result = 0;
    if (is_dir($path) || is_readable($path)) {
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if ($file != "." && $file != "..") {
                if (@is_dir("$path$file/")) {
                    $result += $recursive ? folder_info_size("$path$file/") : 0;
                } else {
                    $result += filesize("$path$file");
                }
            }
        }

        closedir($dir);
        return $result;
    }
}

/**
 * Return size converted with units (in the user language)
 * @param string $result Result to convert
 * @access public
 **/
function convertSize($result)
{
    global $byteUnits;

    if ($result >= 1073741824) {
        $result = round($result / 1073741824 * 100) / 100 . " " . $byteUnits[3];
    } else if ($result >= 1048576) {
        $result = round($result / 1048576 * 100) / 100 . " " . $byteUnits[2];
    } else if ($result >= 1024) {
        $result = round($result / 1024 * 100) / 100 . " " . $byteUnits[1];
    } else {
        $result = $result . " " . $byteUnits[0];
    }

    if ($result == 0) {
        $result = "-";
    }

    return $result;
}

/**
 * Return file size
 * @param string $fichier File used
 * @access public
 **/
function file_info_size($fichier)
{
    global $taille;

    $taille = filesize($fichier);
    return $taille;
}

/**
 * Return file dimensions
 * @param string $fichier File used
 * @access public
 **/
function file_info_dim($fichier)
{
    global $dim;

    $temp = GetImageSize($fichier);
    $dim = ($temp[0]) . "x" . ($temp[1]);
    return $dim;
}

/**
 * Return file date
 * @param string $fichier File used
 * @access public
 **/
function file_info_date($file)
{
    global $dateFile;

    $dateFile = date("Y-m-d", filemtime($file));
    return $dateFile;
}

/**
 * Read the content of a file
 * @param string $file File used
 * @access public
 **/
function recupFile($file)
{
    $content = '';

    if (!file_exists($file)) {
        echo "File does not exist : " . $file;
        return false;
    }

    $fp = fopen($file, "r");

    if (!$fp) {
        echo "Unable to open file : " . $file;
        return false;
    }

    while (!feof($fp)) {
        $tmpline = fgets($fp, 4096);
        $content .= $tmpline;
    }

    fclose($fp);
    return $content;
}

//provide id session if trans_sid false on server (if $trans_sid true in settings)
if ($trans_sid == "true") {
    global $transmitSid;
    $transmitSid = session_name() . "=" . session_id();
}

//time variables
if ($gmtTimezone == "true") {
    $date = gmdate("Y-m-d");
    $dateheure = gmdate("Y-m-d H:i");
} else {
    $date = date("Y-m-d");
    $dateheure = date("Y-m-d H:i");
}

/**
 * Displat date according to timezone (if timezone enabled)
 * @param string $storedDate Date stored in database
 * @param string $gmtUser User timezone
 * @access public
 **/
function createDate($storedDate, $gmtUser)
{
    global $gmtTimezone;

    if ($gmtTimezone == "true") {
        if ($storedDate != "") {
            $extractHour = substr("$storedDate", 11, 2);
            $extractMinute = substr("$storedDate", 14, 2);
            $extractYear = substr("$storedDate", 0, 4);
            $extractMonth = substr("$storedDate", 5, 2);
            $extractDay = substr("$storedDate", 8, 2);

            return date("Y-m-d H:i", mktime($extractHour + $gmtUser, $extractMinute, 0, $extractMonth, $extractDay, $extractYear));
        }
    } else {
        return $storedDate;
    }
}

//update sorting table if query sort column
if (!empty($sor_cible) && $sor_cible != "" && $sor_champs != "none") {
    $sor_champs = convertData($sor_champs);
    $sor_cible = convertData($sor_cible);

    $tmpquery = "UPDATE " . $tableCollab["sorting"] . " SET $sor_cible='$sor_champs $sor_ordre' WHERE member = '$idSession'";
    connectSql("$tmpquery");
}

//set all sorting values for logged user
$tmpquery = "WHERE sor.member = '" . fixInt($idSession) . "'";
$sortingUser = new request();
$sortingUser->openSorting($tmpquery);

/**
 * Convert insert data value in form
 * @param string $data Data to convert
 * @access public
 **/
function convertData($data)
{
    global $databaseType;

    if ($databaseType == "sqlserver") {
        $data = str_replace('"', '&quot;', $data);
        $data = str_replace("'", '&#39;', $data);
        $data = str_replace('<', '&lt;', $data);
        $data = str_replace('>', '&gt;', $data);
        $data = stripslashes($data);
        return ($data);
    } elseif (get_magic_quotes_gpc() == 1) {
        $data = str_replace('"', '&quot;', $data);
        $data = str_replace('<', '&lt;', $data);
        $data = str_replace('>', '&gt;', $data);
        $data = str_replace("'", '&#39;', $data);
        return ($data);
    } else {
        $data = str_replace('"', '&quot;', $data);
        $data = str_replace('<', '&lt;', $data);
        $data = str_replace('>', '&gt;', $data);
        $data = str_replace("'", '&#39;', $data);
        $data = addslashes($data);
        return ($data);
    }

}

/**
 * Count total results from a request
 * @param string $tmpsql Sql query
 * @access public
 **/
function compt($tmpsql)
{
    global $tableCollab, $databaseType, $countEnregTotal, $comptRequest;

    $comptRequest = $comptRequest + 1;

    if ($databaseType == "mysql") {
        $res = mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mysql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        $sql = "$tmpsql";
        $index = mysql_query($sql, $res);

        while ($row = mysql_fetch_row($index)) {
            $countEnreg[] = ($row[0]);
        }

        $countEnregTotal = count($countEnreg);
        @mysql_free_result($index);
        @mysql_close($res);
    }

    if ($databaseType == "postgresql") {
        $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
        $sql = "$tmpsql";
        $index = pg_query($res, $sql);

        while ($row = pg_fetch_row($index)) {
            $countEnreg[] = ($row[0]);
        }

        $countEnregTotal = count($countEnreg);
        @pg_free_result($index);
        @pg_close($res);
    }

    if ($databaseType == "sqlserver") {
        $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mssql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        $sql = "$tmpsql";
        $index = mssql_query($sql, $res);

        while ($row = mssql_fetch_row($index)) {
            $countEnreg[] = ($row[0]);
        }

        $countEnregTotal = count($countEnreg);
        @mssql_free_result($index);
        @mssql_close($res);
    }

    return $countEnregTotal;
}

/**
 * Simple query
 * @param string $tmpsql Sql query
 * @access public
 **/
function connectSql($tmpsql)
{
    global $tableCollab, $databaseType;

    if ($databaseType == "mysql") {
        $res = mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mysql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        $sql = $tmpsql;
        $index = mysql_query($sql, $res);
        @mysql_free_result($index);
        @mysql_close($res);
    }
    if ($databaseType == "postgresql") {
        $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
        $sql = $tmpsql;
        $index = pg_query($res, $sql);
        @pg_free_result($index);
        @pg_close($res);
    }
    if ($databaseType == "sqlserver") {
        $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mssql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        $sql = $tmpsql;
        $index = mssql_query($sql, $res);
        @mssql_free_result($index);
        @mssql_close($res);
    }
}

/**
 * Return last id from any table
 * @param string $tmpsql Table name
 * @access public
 **/
function last_id($tmpsql)
{
    global $tableCollab, $databaseType;
    if ($databaseType == "mysql") {
        $res = mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mysql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        global $lastId;
        $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
        $index = mysql_query($sql, $res);
        while ($row = mysql_fetch_row($index)) {
            $lastId[] = ($row[0]);
        }
        @mysql_free_result($index);
        @mysql_close($res);
    }
    if ($databaseType == "postgresql") {
        $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
        global $lastId;
        $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
        $index = pg_query($res, $sql);
        while ($row = pg_fetch_row($index)) {
            $lastId[] = ($row[0]);
        }
        @pg_free_result($index);
        @pg_close($res);
    }
    if ($databaseType == "sqlserver") {
        $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
        mssql_select_db(MYDATABASE, $res) or die($strings["error_database"]);
        global $lastId;
        $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
        $index = mssql_query($sql, $res);
        while ($row = mssql_fetch_row($index)) {
            $lastId[] = ($row[0]);
        }
        @mssql_free_result($index);
        @mssql_close($res);
    }
}

//recompute number of completed tasks of the project
// Do it only if the project name contains [ / ]
//list tasks of the same project and count the number of completed
function projectComputeCompletion($projectDetail, $tableProject)
{
    $prj_name = $projectDetail->pro_name[0];
    preg_match("/\[([0-9 ]*\/[0-9 ]*)\]/", $prj_name, $findit);;
    if ($findit[1] != "") {
        $prj_id = $projectDetail->pro_id[0];
        $taskDetails = new request();
        $tmpquery = "WHERE tas.project = '$prj_id'";
        $taskDetails->openTasks($tmpquery);
        $tasksNumb = count($taskDetails->tas_id);
        $tasksCompleted = 0;
        foreach ($taskDetails->tas_status as $stat) {
            if ($stat == 1) $tasksCompleted++;
        }
        $prj_name = preg_replace("/\[[0-9 ]*\/[0-9 ]*\]/", "[ $tasksCompleted / $tasksNumb ]", $prj_name);
        $tmpquery5 = "UPDATE " . $tableProject . " SET name='$prj_name' WHERE id = '$prj_id'";
        $ad = connectSql($tmpquery5);
    }
    return $prj_name;
}

//compute the average completion of all subtaks of a task
//update the main task completion
//24/05/03: Florian DECKERT
function taskComputeCompletion($taskid, $tableTask)
{
    $tmpquery = "WHERE subtas.tasks = '$taskid'";
    $subtaskList = new request();
    $subtaskList->openAvgTasks($taskid);
    $avg = $subtaskList->tas_avg[0];
    settype($avg, "integer");
    $tmpquery6 = "UPDATE " . $tableTask . " set completion = $avg where id='$taskid'";
    connectSql($tmpquery6);
}

/**
 * check a file name and remove backslash and spaces
 * this function remove also the file path if IE is used for upload
 * @param string $name the name of the file
 */
function check_FileName($name = '')
{

    $name = str_replace('\\', '/', $name);
    $name = str_replace(" ", "_", $name);
    $name = str_replace("'", "", $name);

    if (get_magic_quotes_gpc()) {
        $name = basename(stripslashes($name));
    } else {
        $name = basename($name);
    }

    return $name;
}

// :-)
$setCopyright = "<!-- Powered by PhpCollab v$version //-->";
?>
