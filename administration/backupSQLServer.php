<?php
/*
** Application name: phpCollab
** Last Edit page: 2025-11-08
** Path by root: ../administration/backupSQLServer.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: backupSQLServer.php
**
** DESC: SQL Server database backup handler (secure modern implementation)
**
** SECURITY FEATURES:
** - CSRF token validation (prevents unauthorized backup requests)
** - Admin-only access (session check)
** - Parameter validation
** - Error logging
** - Uses secure Administration::dumpSQLServerTables() method
**
** =============================================================================
*/

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
        $logger->error('CSRF Token Error in backupSQLServer.php', [
            'ip' => $request->server->get('REMOTE_ADDR'),
            'user_id' => $session->get('id')
        ]);

        // Redirect back to sqlserver page with error
        $session->getFlashBag()->add('error', 'Security error: Invalid form submission. Please try again.');
        phpCollab\Util::headerFunction('../administration/sqlserver.php');
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

        if ($request->request->get('zip') == 'zip') {
            $dumpSettings['compress'] = 'Gzip';
        }

        $admins = $container->getAdministration();

        try {
            $admins->dumpSQLServerTables($dumpSettings);
        } catch (Exception $e) {
            // SECURITY: Log backup failure for audit trail
            $logger->error('SQL Server backup failed', [
                'error' => $e->getMessage(),
                'user_id' => $session->get('id'),
                'ip' => $request->server->get('REMOTE_ADDR')
            ]);

            $session->getFlashBag()->add('error', 'Backup failed: ' . htmlspecialchars($e->getMessage()));
            phpCollab\Util::headerFunction('../administration/sqlserver.php');
        }

    } else {
        // No tables selected, redirect back
        phpCollab\Util::headerFunction('../administration/sqlserver.php');
    }
}
