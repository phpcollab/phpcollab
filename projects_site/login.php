<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
includes("../includes/library.php");

//case session fails
if ($url != "") {
	Util::headerFunction("../login.php?url=$url");
	exit;

//default case
} else {
	Util::headerFunction("../login.php");
	exit;
}
?>