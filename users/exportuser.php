<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../users/exportuser.php

$export = "true";

$checkSession = "false";
include_once '../includes/library.php';

include '../includes/vcard.class.php';

$userDetail = $members->getMemberById($id);

$v = new vCard();

$v->setPhoneNumber($userDetail["mem_phone_work"]);

$v->setName($userDetail["mem_name"]);

$v->setTitle($userDetail["mem_title"]);

$v->setOrganization($userDetail["mem_org_name"]);

$v->setEmail($userDetail["mem_email_work"]);

$v->setPhoneNumber($userDetail["mem_phone_work"], "WORK;VOICE");

$v->setPhoneNumber($userDetail["mem_phone_home"], "HOME;VOICE");

$v->setPhoneNumber($userDetail["mem_mobile"], "CELL;VOICE");

$v->setPhoneNumber($userDetail["mem_fax"], "WORK;FAX");

$output = $v->getVCard();
$filename = $v->getFileName();

Header("Content-Disposition: attachment; filename=$filename");
Header("Content-Length: " . strlen($output));
Header("Connection: close");
Header("Content-Type: text/x-vCard; name=$filename");

echo $output;
