<?php

use Symfony\Component\HttpFoundation\Response;

$checkSession = "false";
include '../includes/library.php';

$loginLogs->setConnectedByLogin($session->get('loginSession'), false);

$logger->info('User logged out', ['username' => $session->get('loginSession')]);

$response = new Response(
    'Content',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);

// delete the authentication cookies
$response->headers->clearCookie('loginCookie');
$session->clear();
$session->invalidate();
phpCollab\Util::headerFunction("../general/login.php?msg=logout");

