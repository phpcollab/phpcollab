<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: index.php

/**
 * Modification Log for 2.5
 *
 * 2008-11-20   -   Added setup detection (dab - norman77)
 *
 */
/* Do Setup Check */
if (!file_exists("includes/settings.php")) {
    header('Location: installation/setup.php');
}
/** END **/

$checkSession = "false";
$indexRedirect = "true";

include_once('includes/library.php');

//case session fails
global $session;
if ($session == "false") {
    phpCollab\Util::headerFunction("general/login.php?session=false");
} //default case
else {
    phpCollab\Util::headerFunction("general/login.php");
}
