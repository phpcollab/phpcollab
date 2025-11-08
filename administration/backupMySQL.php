<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

if ($request->isMethod('post')) {

    // SECURITY: Validate CSRF token (CSRF protection)
    try {
        if (!$csrfHandler->isValid($request->request->get('csrf_token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
    } catch (InvalidCsrfTokenException $e) {
        $logger->error('CSRF Token Error in backupMySQL.php', [
            'ip' => $request->server->get('REMOTE_ADDR'),
            'user_id' => $session->get('id')
        ]);

        // Redirect back to phpmyadmin page with error
        $session->getFlashBag()->add('error', 'Security error: Invalid form submission. Please try again.');
        phpCollab\Util::headerFunction('../administration/phpmyadmin.php');
        exit;
    }

    if ($request->request->get('tables')) {

        $dumpSettings = [
            'include-tables' => $request->request->get('tables'),
        ];

        if ($request->request->get('what') == "structureonly") {
            $dumpSettings['no-data'] = true;
        }

        if ($request->request->get('what') == "dataonly") {
            $dumpSettings['no-create-info'] = true; // Data only
        }

        if ((bool)$request->request->get('drop')) {
            $dumpSettings['add-drop-table'] = true;
        }

        if ((bool)$request->request->get('extended_insert')) {
            $dumpSettings['extended-insert'] = true;
        }

        if ((bool)$request->request->get('complete_insert')) {
            $dumpSettings['complete-insert'] = true;
        }

        if ($request->request->get('zip') == 'zip') {
            $dumpSettings['compress'] = true;
        }

        $admins = $container->getAdministration();

        $admins->dumpTables($dumpSettings);

    } else {
        return;
    }
}




