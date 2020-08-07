<?php
/*
** Application name: phpCollab
** Path by root: ../includes/library.php
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
** =============================================================================
*/
use DebugBar\StandardDebugBar;
use Laminas\Escaper\Escaper;
use phpCollab\Logs\Logs;
use phpCollab\Members\Members;
use phpCollab\Sorting\Sorting;
use Symfony\Component\HttpFoundation\Request;


$debug = false;

define('APP_ROOT', dirname(dirname(__FILE__)));

require APP_ROOT . '/vendor/autoload.php';

if (ini_get('session.auto_start') == 0) {
    $profilSession = "";
}

$escaper = new Escaper('utf-8');

// Setup debugging
if ($debug) {
    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();
}

error_reporting(2039);

$request = Request::createFromGlobals();

session_start();

// register_globals cheat code
if (ini_get("register_globals") != "1") {
    //GET and POST VARS
    if (!empty($_REQUEST)) {
        foreach ($_REQUEST as $key => $val) {
            $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
        }
    }
    //$HTTP_SESSION_VARS
    if (!empty($_SESSION)) {
        foreach ($_SESSION as $key => $val) {
            $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
        }
    }
    //$HTTP_SERVER_VARS
    if (!empty($_SERVER)) {
        foreach ($_SERVER as $key => $val) {
            $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
        }
    }
}

$msg = $request->query->get("msg");
$session = $request->query->get("session");
$logout = $request->query->get("logout");
$idSession = $_SESSION["idSession"];
$dateunixSession = $_SESSION["dateunixSession"];
$loginSession = $_SESSION["loginSession"];
$profilSession = $_SESSION["profilSession"];
$logouttimeSession = $_SESSION["logouttimeSession"];

$parse_start = phpCollab\Util::getMicroTime();

//database update array
$updateDatabase = array(
    0 => "1.0",
    1 => "1.1",
    2 => "1.3",
    3 => "1.4",
    4 => "1.6",
    5 => "1.8",
    6 => "1.9",
    7 => "2.0",
    8 => "2.1",
    9 => "2.5"
);

//languages array
$langValue = array(
    "en" => "English",
    "es" => "Spanish",
    "fr" => "French",
    "it" => "Italian",
    "pt" => "Portuguese",
    "da" => "Danish",
    "no" => "Norwegian",
    "nl" => "Dutch",
    "de" => "German",
    "zh" => "Chinese simplified",
    "uk" => "Ukrainian",
    "pl" => "Polish",
    "in" => "Indonesian",
    "ru" => "Russian",
    "az" => "Azerbaijani",
    "ko" => "Korean",
    "zh-tw" => "Chinese traditional",
    "ca" => "Catalan",
    "pt-br" => "Brazilian Portuguese",
    "et" => "Estonian",
    "bg" => "Bulgarian",
    "ro" => "Romanian",
    "hu" => "Hungarian",
    "cs-iso" => "Czech (iso)",
    "cs-win1250" => "Czech (win1250)",
    "is" => "Icelandic",
    "sk-win1250" => "Slovak (win1250)",
    "tr" => "Turkish",
    "lv" => "Latvian",
    "ar" => "Arabic",
    "ja" => "Japanese"
);


//language browser detection
if ($langDefault == "") {
    if (isset($HTTP_ACCEPT_LANGUAGE)) {
        $plng = explode(",", $HTTP_ACCEPT_LANGUAGE);
        if (count($plng) > 0) {
            foreach($plng as $k => $v) {
                $k = explode(";", $v, 1);
                $k = explode("-", $k[0]);

                if (file_exists("../languages/lang_" . $k[0] . ".php")) {
                    $langDefault = $k[0];
                    break;
                }
                $langDefault = "en";
            }
        } else {
            $langDefault = "en";
        }
    } else {
        $langDefault = "en";
    }
}

//set language session
if ($langDefault != "") {
    $langSelected[$langDefault] = "selected";
} else {
    $langSelected = "";
}

if (empty($_SESSION["languageSession"])) {
    $lang = $langDefault;
} else {
    $lang = $_SESSION["languageSession"];
}


$settings = null;
//settings and date selector includes
include APP_ROOT . '/includes/settings.php';

include APP_ROOT . '/includes/initrequests.php';

include APP_ROOT . '/languages/lang_en.php';
include APP_ROOT . '/languages/lang_' . $lang . '.php';
include APP_ROOT . '/languages/help_' . $lang . '.php';

$logs = new Logs();
$sort = new Sorting();
$members = new Members();

//fix if update from old version
if ($theme == "") {
    $theme = "default";
}
if (!is_resource("THEME")) {
    define('THEME', $theme);
}
if (!is_resource("FTPSERVER")) {
    define('FTPSERVER', '');
}
if (!is_resource("FTPLOGIN")) {
    define('FTPLOGIN', '');
}
if (!is_resource("FTPPASSWORD")) {
    define('FTPPASSWORD', '');
}
if (empty($uploadMethod)) {
    $uploadMethod = "PHP";
}
if (empty($peerReview)) {
    $peerReview = "true";
}

if (empty($loginMethod)) {
    $loginMethod = "PLAIN";
}
if (empty($databaseType)) {
    $databaseType = "mysql";
}
if (empty($installationType)) {
    $installationType = "online";
}

/*
 * This code should be called if $checkSession = true and we are not in demo mode.
 * If a session is not active, then redirect to the login page.
 */
if ($checkSession != "false" && $_SESSION["demoSession"] != "true") {

    if (empty($_SESSION)) {
        phpCollab\Util::headerFunction("../index.php?session=false");
    };


    if ($profilSession == "3" && !strstr($request->server->get("PHP_SELF"), "projects_site")) {
        phpCollab\Util::headerFunction("../projects_site/home.php");
    }

    if ($lastvisitedpage && $profilSession != "0") { // If the user has admin permissions, do not log the last page visited.
        if (!strstr($request->server->get("PHP_SELF"), "graph")) {
            $sidCode = session_name();
            $page = $request->server->get("PHP_SELF") . "?" . $request->server->get("QUERY_STRING");
            $page = preg_replace('/(&' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = preg_replace('/(' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = strrev($page);
            $pieces = explode("/", $page);
            $pieces[0] = strrev($pieces[0]);
            $pieces[1] = strrev($pieces[1]);
            $page = $pieces[1] . "/" . $pieces[0];

            $members->setLastPageVisited($idSession, $page);
        }
    }
    //if auto logout feature used, store last required page before disconnecting
    if ($profilSession != "3") {
        if ($logouttimeSession != "0" && $logouttimeSession != "") {
            $dateunix = date("U");
            $diff = $dateunix - $dateunixSession;

            if ($diff > $logouttimeSession) {
                phpCollab\Util::headerFunction("../general/login.php?logout=true");
            } else {
                $dateunixSession = $dateunix;
                $_SESSION['dateunixSession'] = $dateunixSession;
            }
        }
    }

    $checkLog = $logs->getLogByLogin($loginSession);
    if ($checkLog !== false) {
        if (session_id() != $checkLog["session"]) {
            phpCollab\Util::headerFunction("../index.php?session=false");
        }
    } else {
        phpCollab\Util::headerFunction("../index.php?session=false");
    }
}


//count connected users
if (isset($checkConnected) && $checkConnected != "false") {
    $dateunix = date("U");
    $logs->updateConnectedTimeForUser($dateunix, $loginSession);
    $connectedUsers = $logs->getConnectedUsersCount();
}


//disable actions if demo user logged in demo mode
if ($action != "") {
    if ($_SESSION["demoSession"] == "true") {
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

//time variables
if ($gmtTimezone == "true") {
    $date = gmdate("Y-m-d");
    $dateheure = gmdate("Y-m-d H:i");
} else {
    $date = date("Y-m-d");
    $dateheure = date("Y-m-d H:i");
}

//update sorting table if query sort column
$sort_target = $request->request->get('sort_target');
$sort_fields = $request->request->get('sort_fields');
$sort_order = $request->request->get('sort_order');

if (!empty($sort_target) && $sort_target != "" && $sort_fields != "none") {
    $sort_fields = phpCollab\Util::convertData($sort_fields); // sort_fields
    $sort_target = phpCollab\Util::convertData($sort_target); // sort_target

    $sort_value = $sort_fields . ' ' . $sort_order;

    $sort->updateSortingTargetByUserId($sort_target, $sort_value, $idSession);

}

$sortingUser = $sort->getSortingValues($idSession);

// :-)
$setCopyright = "<!-- Powered by PhpCollab v$version //-->";
