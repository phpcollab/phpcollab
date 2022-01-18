<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: index.php


/**
 * Do Setup Check
 */
try {
    if (!file_exists("includes/settings.php")) {
        header('Location: installation/setup.php');
    } else {
        $checkSession = "false";
        $indexRedirect = "true";

        require_once 'includes/library.php';


        //case session fails or auth is false
        if ( $session == "false" || !$session->get('auth') || $session->get('auth') === false ) {
            phpCollab\Util::headerFunction("general/login.php");
        }

        if ( $session->get('auth') === true ) {
            $foo = 'bar';
            phpCollab\Util::headerFunction("general/home.php");
        }

        // Default case just in case the above falls through
        phpCollab\Util::headerFunction("general/login.php");
    }
} catch (Exception $exception) {
    require_once dirname(__FILE__) . "/views/fatal_error.php";
    $date = new DateTime();
    $now = $date->format("[Y-m-d\TH:i:s.uP]");
    error_log($now . " FATAL ERROR: " . $exception . "\n");
    error_log($now . " FATAL ERROR: " . $exception . "\n", 3, "./logs/phpcollab.log");
}
