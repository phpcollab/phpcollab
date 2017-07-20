<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "false";
include_once '../includes/library.php';
phpCollab\Util::headerFunction("../index.php");

//case session fails
if ($session == "false") {
    session_start();
    session_destroy();
    phpCollab\Util::headerFunction("../general/login.php?session=false");

//case log out
} else if ($logout == "true") {
    session_start();
    session_destroy();
    phpCollab\Util::headerFunction("../general/login.php?logout=true&login=$login");

//default case
} else {
    phpCollab\Util::headerFunction("../general/login.php");
}
