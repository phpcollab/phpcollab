<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/index.php

$checkSession = "false";
include_once '../includes/library.php';
Util::headerFunction('../index.php?'.session_name().'='.session_id());
exit;
?>