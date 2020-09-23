<?php


namespace phpCollab\Installation;


use Exception;
use PDOException;
use phpCollab\Administration;
use phpCollab\Container;
use phpCollab\DataFunctionsService;
use phpCollab\Util;

class Installation
{
    private $version = "2.8.2";
    private $logger;
    private $container;
    private $database;
    private $appRoot;
    private $databaseInfo;
    private $scrubbedData;

    /**
     * Installation constructor.
     * @param array $databaseInfo
     * @param string $appRoot
     * @throws Exception
     */
    public function __construct(array $databaseInfo, string $appRoot)
    {
        try {
            // Create a Container instance
            $this->container = new Container([
                'dbServer' => $databaseInfo["dbServer"],
                'dbUsername' => $databaseInfo["dbUsername"],
                'dbPassword' => $databaseInfo["dbPassword"],
                'dbName' => $databaseInfo["dbName"],
                'dbType' => $databaseInfo["dbType"]
            ]);
            $this->logger = $this->container->getLogger();
            $this->appRoot = $appRoot;
            $this->databaseInfo = $databaseInfo;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Primary setup handler
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function setup(array $data)
    {
        try {
            // Test if the database is accessible, then continue
            $this->testDatabase();

            // Check if the folders are writable
            $this->checkIfWritable();

            // Scrub Data
            $this->scrubbedData = $this->scrubData($data);
            $this->scrubbedData["phpCollabVersion"] = $this->version;

            // Create the database tables
            $this->createDatabaseTables();

            // Create the settings.php file
            $this->writeSettingsFile();

            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function testDatabase()
    {
        // Test to see if we can access the database
        try {
            if (null === $this->database) {
                $this->database = $this->container->getPDO();

                // Extra check until getPDO is refactored to throw an Exception
                if ($this->database instanceof Exception) {
                    throw $this->database;
                }
            }
            return true;
        } catch (Exception $exception) {
            $this->logger->critical("SETUP - testDatabase Exception: " . $exception->getMessage() . "\n");
            throw $exception;
        }
    }

    /**
     * @return bool
     */
    private function createDatabaseTables()
    {
        // Since the includes below use "global" variables, we need to create them here so they are accessible.
        $databaseType = $this->databaseInfo["dbType"];
        $dbTablePrefix = $this->databaseInfo["tablePrefix"];

        $timestamp = date('Y-m-d h:i');
        $adminEmail = $this->scrubbedData["adminEmail"];

        $demoPwd = Util::getPassword("demo", $this->scrubbedData["loginMethod"]);
        $adminPassword = Util::getPassword($this->scrubbedData["adminPassword"],
            $this->scrubbedData["loginMethod"]);

        include $this->appRoot . '/includes/db_var.inc.php';
        include $this->appRoot . '/includes/setup_db.php';

        foreach ($SQL as $sqlStatement) {
            try {
                $this->database->query($sqlStatement);
                $this->database->execute();
            } catch (PDOException $e) {
                throw $e;
            }
        }

        return true;
    }

    /**
     * @throws Exception
     */
    private function writeSettingsFile()
    {
        try {
            return Administration\Settings::writeSettings($this->appRoot, $this->scrubbedData, $this->logger);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function checkIfWritable()
    {
        /**
         * See if the includes, and logs directories are writable
         */
        if (!is_writable("../includes")) {
            $this->logger->critical("The 'includes' directory is not writable. Please correct and try again.");
            throw new Exception("It appears that the 'includes' directory is not writable. Please correct and try again.");
        }

        if (!is_writable("../logs")) {
            $this->logger->critical("The 'logs' directory is not writable. Please correct and try again.");
            throw new Exception("It appears that the 'logs' directory is not writable. Please correct and try again.");
        }
        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private function scrubData(array $data)
    {
        return DataFunctionsService::scrubData($data);
    }
}
