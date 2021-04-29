<?php

use Symfony\Component\HttpFoundation\Response;

$checkSession = "false";
require_once '../includes/library.php';

if ($session->get('login')) {
    $loginLogs->setConnectedByLogin($session->get('login'), false);
}

$logger->info('User logged out', ['username' => $session->get('login')]);

$response = new Response(
    'Content',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);

// We want to persist the selected language, so temporarily store it
$sessionLanguage = $session->get("language");

// delete the authentication cookies
$response->headers->clearCookie('loginCookie');
$session->clear();
$session->invalidate();
$session->getFlashBag()->add(
    'message',
    $strings["success_logout"]
);
$session->set("language", $sessionLanguage);
phpCollab\Util::headerFunction("../general/login.php");
