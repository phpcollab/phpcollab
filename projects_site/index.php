<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "false";
include_once('../includes/library.php');
Util::headerFunction("../index.php");

//case session fails
if ($session == "false") {
	session_start();
	session_destroy();
	Util::headerFunction("../general/login.php?session=false");
	exit;

//case log out
} else if ($logout == "true") {
	session_start();
	session_destroy();
	Util::headerFunction("../general/login.php?logout=true&login=$login");
	exit;

//default case
} else {
	Util::headerFunction("../general/login.php");
	exit;
}
?>