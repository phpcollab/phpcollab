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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use phpCollab\Alerts\DailyAlerts;

$app_root = dirname(dirname(__FILE__));

// Includes
require $app_root . '/vendor/autoload.php';
require_once $app_root . "/includes/settings.php";

try {
    $stream = new StreamHandler($app_root . '/logs/phpcollab.log', Logger::DEBUG);
} catch (Exception $e) {
}

$dailyAlertLogger = new Logger('security');
$dailyAlertLogger->pushHandler($stream);

$dailyAlertLogger->info('cron job started');


if (!isset($langDefault) || ($langDefault == '')) {
    $langDefault = 'en';
}

include $app_root . '/languages/lang_' . $langDefault . '.php';

// Check if emailAlerts is set to true
if ($emailAlerts === false) {
    // Return false
    $dailyAlertLogger->warn('setting, emailAlerts is disabled');
    exit('ERROR - email alerts are disabled');
}

// Check that database vars are set
if (!defined('MYSERVER') || !defined('MYLOGIN') || !defined('MYPASSWORD') || !defined('MYDATABASE')) {
    $dailyAlertLogger->error($strings['error_server']);
    exit('ERROR - DATABASE' . $strings['error_server']);
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
    $dailyAlertLogger->error($e->getMessage());
    exit('ERROR - ' . $e->getMessage());
}

// Return successfully
$dailyAlertLogger->info('cron job completed');
exit('cron job completed');
