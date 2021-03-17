<?php
/*
** Application name: phpCollab
** Path by root: ../includes/library.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: library.php
**
** DESC: Screen: library file
**
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
*/

use DebugBar\DebugBarException;
use DebugBar\StandardDebugBar;
use phpCollab\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;


function phpcollab_global_exception_handler($exception) {
    //this code should log the exception to disk and an error tracking system
    error_log("FATAL ERROR: " . $exception . "\n");
    error_log("FATAL ERROR: " . $exception . "\n", 3, "./logs/phpcollab.log");
    phpCollab\Util::headerFunction("../views/fatal_error.php");
}

set_exception_handler('phpcollab_global_exception_handler');

define('APP_ROOT', dirname(dirname(__FILE__)));

require APP_ROOT . '/vendor/autoload.php';

error_reporting(2039);

$settings = null;
//settings and date selector includes
require_once APP_ROOT . '/includes/settings.php';

$debug = $footerDev ?? false;

$container = new Container([
    'dbServer' => MYSERVER,
    'dbUsername' => MYLOGIN,
    'dbPassword' => MYPASSWORD,
    'dbName' => MYDATABASE,
    'tableCollab' => $tableCollab,
    'dbType' => $databaseType
]);

/*
 * Setup logger
 */
$logger = $container->getLogger($GLOBALS["logLevel"]);
// End logger init

$escaper = $container->getEscaperService();

// Setup debugging, if it is enabled in settings
if ($debug) {
    $debugbar = new StandardDebugBar();
    try {
        $debugbarRenderer = $debugbar->getJavascriptRenderer();
        $debugbar->addCollector(new DebugBar\Bridge\MonologCollector($logger));
    } catch (DebugBarException $e) {
        $logger->error($e->getMessage());
    }
}

/*
 * Init Http Foundation
 */
$request = Request::createFromGlobals();

/*
 * Start the session
 */
$session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
$session->start();

$session->set('phpCollab', []);

$msg = $request->query->get("msg");

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
if ($session->get('language') == "") {
    $session->set('language', 'en');
    if (isset($HTTP_ACCEPT_LANGUAGE)) {
        $plng = explode(",", $HTTP_ACCEPT_LANGUAGE);
        if (count($plng) > 0) {
            foreach ($plng as $k => $v) {
                $k = explode(";", $v, 1);
                $k = explode("-", $k[0]);

                if (file_exists("../languages/lang_" . $k[0] . ".php")) {
                    $session->set('language', $k[0]);
                    break;
                }
            }
        }
    }
}

//set language session
if (!empty($session->get('langDefault'))) {
    $langSelected[$langDefault] = "selected";
} else {
    $langSelected["en"] = "selected";
}

include APP_ROOT . '/includes/initrequests.php';

require_once APP_ROOT . '/languages/lang_en.php';
require_once APP_ROOT . '/languages/lang_' . $session->get("language") . '.php';
require_once APP_ROOT . '/languages/help_' . $session->get("language") . '.php';

$loginLogs = $container->getLoginLogs();

$sort = $container->getSortingLoader();
$members = $container->getMembersLoader();

if (empty(THEME) && empty($theme)) {
    $theme = "default";
}

if (!is_resource("FTPSERVER")) {
    $session->set('phpCollab/ftpServer', '');
}
if (!is_resource("FTPLOGIN")) {
    $session->set('phpCollab/ftpLogin', '');
}
if (!is_resource("FTPPASSWORD")) {
    $session->set('phpCollab/ftpPassword', '');
}

if ($peerReview == "") {
    $peerReview = "true";
    $session->set('phpCollab/peerReview', true);
}

if (empty($loginMethod)) {
    $session->set('phpCollab/loginMethod', 'CRYPT');
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
if ($checkSession != "false" && $session->get('demo') != "true") {

    if (empty($session->getId())) {
        phpCollab\Util::headerFunction("../index.php?session=false");
    }

    /*
     * CSRF Setup
     */
    // Set the CSRF token in the session
    if (!$session->has('csrfToken')) {
        try {
            $logger->debug('setting csrfToken');
            $session->set('csrfToken', bin2hex(random_bytes(32)));
        } catch (Exception $exception) {
            $logger->critical('Unable to set csrfToken: ' . $e->getMessage());
            error_log('Unable to set csrfToken: ' . $e->getMessage());
        }
    }
    $csrfHandler = $container->setCSRFHandler($session);

    if ($session->get('profile') == "3" && !strstr($request->server->get('PHP_SELF'), "projects_site")) {
        phpCollab\Util::headerFunction("../projects_site/home.php");
    }

    if ($lastvisitedpage && $session->get('profile') != "0") { // If the user has admin permissions, do not log the last page visited.
        if (!strstr($request->server->get("PHP_SELF"), "graph")) {
            $sidCode = $session->getName();
            $page = $request->server->get("PHP_SELF") . "?" . $request->server->get("QUERY_STRING");
            $page = preg_replace('/(&' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = preg_replace('/(' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = strrev($page);
            $pieces = explode("/", $page);
            $pieces[0] = strrev($pieces[0]);
            $pieces[1] = strrev($pieces[1]);
            $page = $pieces[1] . "/" . $pieces[0];

            $members->setLastPageVisited($session->getId(), $page);
        }
    }
    //if auto logout feature used, store last required page before disconnecting
    if ($session->get('profile') != "3") {
        if ($session->get('logoutTime') != "0" && $session->get('logoutTime') != "") {
            $dateunix = date("U");
            $diff = $dateunix - $session->get('dateunix');

            if ($diff > $session->get('logoutTime')) {
                phpCollab\Util::headerFunction("../general/logout.php");
            } else {
                $session->set('dateunix', $dateunix);
            }
        }
    }

    $checkLog = $loginLogs->getLogByLogin($session->get('login'));
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
    $loginLogs->updateConnectedTimeForUser($dateunix, $session->get("login"));
    $session->set("connectedUsers", $loginLogs->getConnectedUsersCount());
}


//disable actions if demo user logged in demo mode
if ($session->get('demo') == "true") {
    if ($request->query->get('action') != "") {
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
if ($gmtTimezone == (int)"true") {
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

    $sort->updateSortingTargetByUserId($sort_target, $sort_value, $session->get("id"));

}

if ($session->getId()) {
    $sortingUser = $sort->getSortingValues($session->get("id"));
}

// :-)
$setCopyright = "<!-- Powered by PhpCollab (show us some ❤️! #phpcollab & phpcollab.com) //-->";

$copyrightYear = date("Y");
