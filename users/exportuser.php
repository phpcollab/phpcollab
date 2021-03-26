<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/exportuser.php

use phpCollab\Members\ExportMember;

$export = "true";

$checkSession = "false";
require_once '../includes/library.php';


try {
    $userDetail = $members->getMemberById($request->query->get('id'));
    if ($userDetail) {
        if (!empty($userDetail["mem_name"])) {
            ExportMember::generateVcard($userDetail, $container->getExportVCardService(), $logger);
        }
    }

} catch (Exception $exception) {
    $logger->error('vCard error: ' . $exception->getMessage());
}
