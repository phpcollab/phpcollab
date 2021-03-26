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

        //case session fails
        if ($session == "false") {
            phpCollab\Util::headerFunction("general/login.php?session=false");
        } //default case
        else {
            phpCollab\Util::headerFunction("general/login.php");
        }
    }
} catch (Exception $exception) {
    require_once dirname(__FILE__) . "/views/fatal_error.php";
    error_log("FATAL ERROR: " . $exception . "\n");
    error_log("FATAL ERROR: " . $exception . "\n", 3, "./logs/phpcollab.log");
}
