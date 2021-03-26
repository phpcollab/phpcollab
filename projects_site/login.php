<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
require_once '../includes/library.php';

//case session fails
if ($url != "") {
    phpCollab\Util::headerFunction("../login.php?url=$url");

    //default case
} else {
    phpCollab\Util::headerFunction("../login.php");
}
