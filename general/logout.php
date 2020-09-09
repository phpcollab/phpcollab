<?php

use Symfony\Component\HttpFoundation\Response;

$checkSession = "false";
include '../includes/library.php';

if ($session->get('login')) {
    $loginLogs->setConnectedByLogin($session->get('login'), false);
}

$logger->info('User logged out', ['username' => $session->get('login')]);

$response = new Response(
    'Content',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);


// delete the authentication cookies
$response->headers->clearCookie('loginCookie');
$session->clear();
$session->invalidate();
$session->getFlashBag()->add(
    'message',
    $strings["success_logout"]
);
phpCollab\Util::headerFunction("../general/login.php");
