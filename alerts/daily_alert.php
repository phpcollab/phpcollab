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

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phpCollab\Alerts\DailyAlerts;
use phpCollab\Container;

$app_root = dirname(dirname(__FILE__));

if (php_sapi_name() == "cli") {
    require $app_root . '/vendor/autoload.php';
    require_once $app_root . "/includes/settings.php";

    $container = new Container([
        'dbServer' => MYSERVER,
        'dbUsername' => MYLOGIN,
        'dbPassword' => MYPASSWORD,
        'dbName' => MYDATABASE
    ]);

    try {
        echo "inside try...\n";

        $stream = new StreamHandler($app_root . '/logs/phpcollab.log', Logger::DEBUG);

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
            $dailyAlertLogger->warning('setting, emailAlerts is disabled');
            echo 'ERROR - email alerts are disabled';
            exit('ERROR - email alerts are disabled');
        }

        // Check that database vars are set
        if (!defined('MYSERVER') || !defined('MYLOGIN') || !defined('MYPASSWORD') || !defined('MYDATABASE')) {
            $dailyAlertLogger->error($strings['error_server']);
            echo 'ERROR - DATABASE' . $strings['error_server'];
            exit('ERROR - DATABASE' . $strings['error_server']);
        }
        $alert = new DailyAlerts($container->getPDO(), $container);
        $alert->sendEmail($langDefault);

        // Return successfully
        $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

        $dailyAlertLogger->info('cron job completed ' . $executionTime);
        echo 'cron job completed';
        exit('cron job completed');

    } catch (Exception $exception) {
        if ($dailyAlertLogger) {
            $dailyAlertLogger->error($e->getMessage());
        }
        echo "exception!\n";
        die($exception->getMessage());
    }
}
