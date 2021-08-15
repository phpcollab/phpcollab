<?php

use DebugBar\DebugBarException;
use DebugBar\StandardDebugBar;
use phpCollab\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

define('APP_ROOT', dirname(__FILE__, 2));

set_exception_handler(function ($exception) {
    error_log("FATAL ERROR (1): " . $exception . "\n");
    error_log("FATAL ERROR (2): " . $exception . "\n", 3, APP_ROOT . "/logs/phpcollab.log");
    require_once APP_ROOT . "/views/fatal_error.php";
});

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
    'dbType' => $databaseType,
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
$session = new Session(new NativeSessionStorage());
$session->start();

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
$languagesArray = array(
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

include APP_ROOT . '/includes/initrequests.php';

// Load english as the default language.
require_once APP_ROOT . '/languages/lang_en.php';
require_once APP_ROOT . '/languages/help_en.php';

// If language is not set to english, then load it, over-write the defaults as needed.
if ($session->get("language") !== 'en') {
    require_once APP_ROOT . '/languages/lang_' . $session->get("language") . '.php';
    require_once APP_ROOT . '/languages/help_' . $session->get("language") . '.php';
}

// Set the language in the Container
$container->setLanguage( $session->get('language') ?? $langDefault ?? 'en' );

try {
    $loginLogs = $container->getLoginLogs();
    $sort = $container->getSortingLoader();
    $members = $container->getMembersLoader();
} catch (Exception $exception) {
    $logger->critical('ERROR - Library.php: ' . $exception->getMessage());
}

if (empty(THEME) && empty($theme)) {
    $theme = "default";
}

if (!is_resource("FTPSERVER")) {
    $session->set('ftpServer', '');
}
if (!is_resource("FTPLOGIN")) {
    $session->set('ftpLogin', '');
}
if (!is_resource("FTPPASSWORD")) {
    $session->set('ftpPassword', '');
}

if ($peerReview == "") {
    $peerReview = "true";
    $session->set('peerReview', true);
}

if (empty($loginMethod)) {
    $session->set('loginMethod', 'CRYPT');
}
if (empty($databaseType)) {
    $databaseType = "mysql";
}
if (empty($installationType)) {
    $installationType = "online";
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

/*
 * This code should be called if $checkSession = true and we are not in demo mode.
 * If a session is not active, then redirect to the login page.
 */
if ($checkSession != "false" && $session->get('demo') != "true") {

    if (empty($session->getId())) {
        phpCollab\Util::headerFunction("../index.php?session=false");
    }


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
        if ($session->getId() != $checkLog["session"]) {
            // Invalidate the session to force the user to re-login
            $session->invalidate();
            phpCollab\Util::headerFunction("../index.php?session=false");
        }
    } else {
        $session->invalidate();
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
