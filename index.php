<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: index.php

/**
 * Do Setup Check
 */
if (!file_exists("includes/settings.php")) {
    header('Location: installation/setup.php');
    exit('redirecting');
} else {
    $checkSession = "false";
    $indexRedirect = "true";

    include_once('includes/library.php');

    //case session fails
    if ($session == "false") {
        phpCollab\Util::headerFunction("general/login.php?session=false");
    } //default case
    else {
        phpCollab\Util::headerFunction("general/login.php");
    }
}
