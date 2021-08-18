<?php


namespace phpCollab\Installation;


use Exception;
use PDOException;
use phpCollab\Administration;
use phpCollab\Container;
use phpCollab\DataFunctionsService;
use phpCollab\Util;
use Ramsey\Uuid\Uuid;

class Installation
{
    private $version = "2.10.1";
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
            $this->logger->alert("SETUP: " . $exception->getMessage() . "\n");
            throw $exception;
        }
    }

    /**
     * Primary setup handler
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function setup(array $data): bool
    {
        try {
            // Test if the database is accessible, then continue
            $this->testDatabase();

            // Check if the folders are writable
            $this->checkIfWritable();

            // Scrub Data
            $this->scrubbedData = $this->scrubData($data);
            $this->scrubbedData["phpCollabVersion"] = $this->version;

            // We don't want to scrub password
            $this->scrubbedData["adminPassword"] = $data["adminPassword"];
            $this->scrubbedData["dbPassword"] = $data["dbPassword"];

            // Create a UUID for the install
            $uuid = Uuid::uuid4();

            $this->scrubbedData["uuid"] = $uuid->toString();

            // Create the database tables
            $this->createDatabaseTables();

            // Create the settings.php file
            $this->writeSettingsFile();

            return true;
        } catch (Exception $exception) {
            $this->logger->alert("SETUP - database: " . $exception->getMessage() . "\n");
            throw $exception;
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function testDatabase(): void
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
            return;
        } catch (Exception $exception) {
            $this->logger->critical("SETUP - testDatabase Exception: " . $exception->getMessage() . "\n");
            throw $exception;
        }
    }

    /**
     * @return void
     */
    private function createDatabaseTables(): void
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
                $this->logger->alert("SETUP - createDatabaseTables: " . $exception->getMessage() . "\n");
                throw $e;
            }
        }

    }

    /**
     * @throws Exception
     */
    private function writeSettingsFile(): void
    {
        try {
            Administration\Settings::writeSettings($this->appRoot, $this->scrubbedData, $this->logger);
            return;
        } catch (Exception $exception) {
            $this->logger->alert("SETUP - writeSettingsFile: " . $exception->getMessage() . "\n");
            throw $exception;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function checkIfWritable(): bool
    {
        /**
         * See if the includes, and logs directories are writable
         */
        if (!is_writable($this->appRoot . "/includes")) {
            $this->logger->critical("The 'includes' directory is not writable. Please correct and try again.");
            throw new Exception("It appears that the 'includes' directory is not writable. Please correct and try again.");
        }

        if (!is_writable($this->appRoot . "/logs")) {
            $this->logger->critical("The 'logs' directory is not writable. Please correct and try again.");
            throw new Exception("It appears that the 'logs' directory is not writable. Please correct and try again.");
        }
        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    private function scrubData(array $data): array
    {
        return DataFunctionsService::scrubData($data);
    }
}
