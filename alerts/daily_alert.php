<?php
/*
** Application name: phpCollab
** Last Edit page: 2006-09-30
** Path by root: ../alerts/daily_alert.php
** Authors: Rich Cave (cavemansf)
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: daily_alert.php
**
** DESC: Notifications for daily alerts triggered by a cron job
**
** HISTORY:
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use phpCollab\Alerts\DailyAlerts;

$app_root = dirname(dirname(__FILE__));

// Includes
require $app_root . '/vendor/autoload.php';
require_once $app_root . "/includes/settings.php";


if (!isset($langDefault) || ($langDefault == '')) {
    $langDefault = 'en';
}

include $app_root . '/languages/lang_' . $langDefault . '.php';

// Check if emailAlerts is set to true
if ($emailAlerts === false) {
    // Return false
    exit(1);
}

// Check that database vars are set
if (!defined('MYSERVER') || !defined('MYLOGIN') || !defined('MYPASSWORD') || !defined('MYDATABASE')) {
    echo($strings['error_server']);
    exit(1);
}

try {
    $alert = new DailyAlerts();
    $alert->setMembersTable($tableCollab['members']);
    $alert->setNotificationsTable($tableCollab['notifications']);
    $alert->setTasksTable($tableCollab['tasks']);
    $alert->setSubtasksTable($tableCollab['subtasks']);
    $alert->setProjectsTable($tableCollab['projects']);
    $alert->sendEmail($langDefault);
} catch (Exception $e) {
    // handle exception
    echo($e->getMessage());
    exit(1);
}

// Return successfully
exit(0);
