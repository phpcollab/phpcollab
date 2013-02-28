<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
includes("../includes/library.php");

//case session fails
if ($url != "") {
	headerFunction("../login.php?url=$url");
	exit;

//default case
} else {
	headerFunction("../login.php");
	exit;
}
?>