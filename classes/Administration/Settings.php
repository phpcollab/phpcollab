<?php


namespace phpCollab\Administration;


use Exception;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class Settings
{
    /**
     * @param $appRoot
     * @param array $settings
     * @param Logger $logger
     * @param bool $backup
     * @return bool
     * @throws Exception
     */
    public static function writeSettings($appRoot, array $settings, Logger $logger, bool $backup = true): bool
    {
        $filesystem = new Filesystem();

        $includesPath = $appRoot . '/includes/';
        $settingsFilePath = $includesPath . 'settings.php';

        try {
            $logger->info("Writing settings to the ~/includes/settings.php file");

            // Make sure the settings.txt template exists
            if ($filesystem->exists($appRoot . '/templates/core/settings.txt')) {
                $settingsTemplate = file_get_contents($appRoot . '/templates/core/settings.txt');

                // Add an auto-generated timestamp to add to the file.
                $settings['generated-timestamp'] = date('Y-m-d h:ia');

                // Combine the default values array with the user supplied values
                $combinedSettings = self::prepareSettings($settings);

                // Get a parameterized version of the settings array
                $parameterizedSettings = self::parameterizeSettingsKeys($combinedSettings);

                $populatedTemplate = strtr($settingsTemplate, $parameterizedSettings);

                if ($backup) {
                    self::backupSettings($includesPath, $logger);
                }

                // Create the settings.php file
                $filesystem->dumpFile($settingsFilePath, $populatedTemplate);

                // Verify the file got written
                if (!$filesystem->exists($settingsFilePath)) {
                    throw new Exception('Unable to write settings.php file');
                }

                return true;
            } else {
                throw new Exception('settings.txt template was not found.');
            }
        } catch (Exception $exception) {
            $logger->error('Unable to write settings.php file');
            throw new Exception('Unable to write settings.php file');
        }
    }

    /**
     * Create an array with the array keys wrapped in %
     * @param array $settingsData
     * @return array
     */
    private static function parameterizeSettingsKeys(array $settingsData): array
    {
        $parameterized = [];
        foreach ($settingsData as $key => $value) {
            $parameterized["%$key%"] = $value;
        }
        return $parameterized;
    }

    /**
     * @param $settingsData
     * @return array
     */
    public static function prepareSettings($settingsData): array
    {
        return array_replace_recursive(self::getDefaultValues(), $settingsData);
    }

    /**
     * @param $appRoot
     * @param Logger $logger
     * @param bool $backup
     * @return string
     * @throws Exception
     */
    public static function appendUUID($appRoot, Logger $logger, bool $backup = true): string
    {
        $filesystem = new Filesystem();
        $uuid = Uuid::uuid4();

        $includesPath = $appRoot . '/includes/';
        $settingsFilePath = $includesPath . 'settings.php';

        try {

            if ($backup) {
                self::backupSettings($includesPath, $logger);
            }

            $logger->info("Writing settings to the ~/includes/settings.php file");
            $filesystem->appendToFile($settingsFilePath, '$uuid = "' . $uuid->toString() . '";' . "\n");
            return $uuid->toString();

        } catch (Exception $exception) {
            $logger->error('Unable to append the UUID to the settings.php file');
            throw new Exception('Unable to append the UUID to the settings.php file');
        }
    }

    /**
     * @param string $includesPath
     * @param Logger $logger
     * @throws Exception
     */
    private static function backupSettings(string $includesPath, Logger $logger)
    {
        try {
            $filesystem = new Filesystem();
            if ($filesystem->exists($includesPath . 'settings.php')) {
                // Backup file name format: settings-yyyy-mm-dd-h-m.php
                // Using .php so that the file is not viewable in the browser if someone happened to
                // figure out/stumble upon the exact timestamp
                $logger->info("Backing up settings settings to ~/includes/settings-" . date('Y-m-d-h_ia') . ".php file");
                $filesystem->copy($includesPath . 'settings.php', $includesPath . 'settings-' . date('Y-m-d-h_ia') . '.php');
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Array of default values.
     * User supplied values will override these.  These are just used to make sure that valid values are always set
     *
     * @return array
     */
    public static function getDefaultValues(): array
    {
        // Set initial defaults
        $defaultValues = [];
        $defaultValues["installationType"] = "online";
        $defaultValues["databaseType"] = "";
        $defaultValues["dbServer"] = "";
        $defaultValues["dbLogin"] = "";
        $defaultValues["dbPassword"] = "";
        $defaultValues["dbName"] = "";
        $defaultValues["defaultLanguage"] = "en";
        $defaultValues["theme"] = "default";
        $defaultValues["loginMethod"] = "crypt";
        $defaultValues["siteUrl"] = "";
        $defaultValues["updateChecker"] = "true";
        $defaultValues["dbTablePrefix"] = "";
        $defaultValues["phpCollabVersion"] = "";
        $defaultValues["forcedLogin"] = "false";


        $defaultValues["enableInvoicing"] = "false";
        $defaultValues["newsdesklimit"] = "true";
        $defaultValues["adminathome"] = "false";
        $defaultValues["gmtTimezone"] = "false";
        $defaultValues["enableMantis"] = "false";
        $defaultValues["mantisPath"] = "";
        $defaultValues["pathToOpenssl"] = "/usr/bin/openssl";
        $defaultValues["htaccessAuth"] = "false";
        $defaultValues["fullPath"] = "";

        $defaultValues["fileManagement"] = "true";
        $defaultValues["maxFileSize"] = "10485760";

        $defaultValues["setTitle"] = "phpCollab";
        $defaultValues["siteTitle"] = "phpCollab";
        $defaultValues["setDescription"] = "Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).";
        $defaultValues["setKeywords"] = "PhpCollab, phpcollab.com, Sourceforge, management, web, projects, tasks, organizations, reports, Php, MySql, Sql Server, mssql, Microsoft Sql Server, PostgreSQL, module, application, module, file management, project site, team collaboration, free, crm, CRM, cutomer relationship management, workflow, workgroup";

        // Boolean Flagged settings
        $defaultValues["emailAlerts"] = "false";
        $defaultValues["lastvisitedpage"] = "false";
        $defaultValues["autoPublishTasks"] = "false";
        $defaultValues["allowPhp"] = "false";
        $defaultValues["sitePublish"] = "true";
        $defaultValues["peerReview"] = "true";
        $defaultValues["showHomeBookmarks"] = "true";
        $defaultValues["showHomeProjects"] = "true";
        $defaultValues["showHomeTasks"] = "true";
        $defaultValues["showHomeSubtasks"] = "true";
        $defaultValues["showHomeDiscussions"] = "true";
        $defaultValues["showHomeReports"] = "true";
        $defaultValues["showHomeNotes"] = "true";
        $defaultValues["showHomeNewsdesk"] = "true";
        $defaultValues["demoMode"] = "false";
        $defaultValues["activeJpgraph"] = "true";
        $defaultValues["clientsFilter"] = "true";
        $defaultValues["projectsFilter"] = "true";
        $defaultValues["enableHelpSupport"] = "true";

        // Advanced Settings
        $defaultValues["footerDev"] = "false";
        $defaultValues["logLevel"] = 400;

        // Folder creation related
        $defaultValues["mkdirMethod"] = "PHP";
        $defaultValues["ftpRoot"] = "";
        $defaultValues["ftpServer"] = "";
        $defaultValues["ftpLogin"] = "";
        $defaultValues["ftpPassword"] = "";

        // Notifications related
        $defaultValues["notifications"] = "true";
        $defaultValues["notificationMethod"] = "mail";
        $defaultValues["smtpServer"] = "";
        $defaultValues["smtpLogin"] = "";
        $defaultValues["smtpPassword"] = "";
        $defaultValues["smtpPort"] = "";
        $defaultValues["urlContact"] = "https://www.sourceforge.net/projects/phpcollab";
        $defaultValues["supportEmail"] = "";
        $defaultValues["supportType"] = "team";

        $defaultValues["useLDAP"] = "false";
        $defaultValues["configLDAPServer"] = "";
        $defaultValues["configLDAPSearchRoot"] = "";

        // Authentication related
        $defaultValues["tokenLifespan"] = 60;
        $defaultValues["timeBetweenAttempts"] = 15;
        $defaultValues["attemptLimit"] = 3;

        return $defaultValues;
    }
}
