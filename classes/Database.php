<?php
namespace phpCollab;

use Exception;
use Monolog\Logger;
use \PDO;
use PDOException;
use UnexpectedValueException;

/**
 * User: mindblender
 * Date: 5/4/16
 * Time: 12:36 AM
 */
class Database
{
    private $configuration;
    private $dbh;
    private $stmt;
    private $logger;
    private $tableCollab;

    /**
     * Database constructor.
     * @param array $config
     * @param Logger $logger
     * @throws Exception
     */
    public function __construct(array $config, Logger $logger)
    {
        try {
            // Create a "database" logger channel
            $this->logger = $logger->withName('database');
        } catch (Exception $e) {
            error_log('library error: ' . $e->getMessage());
        }

        $this->logger->info('Database init');
        $this->configuration = $config;

        $this->tableCollab = $this->configuration['tableCollab'];

        if ($this->dbh === null) {
            // Set DSN
            $this->logger->info('set DSN', ['database_type' => $this->configuration["dbType"]]);
            switch ($this->configuration['dbType']) {
                case ('mysql'):
                    $dsn = "mysql:host={$this->configuration["dbServer"]};dbname={$this->configuration["dbName"]}";
                    break;
                case ('postgresql'):
                    $dsn = "pgsql:dbname={$this->configuration["dbName"]};host={$this->configuration['dbServer']}";
                    break;
                case ('sqlserver'):
                    $dsn = "sqlsrv:Server={$this->configuration["dbServer"]};Database={$this->configuration['dbName']}";
                    break;
                default:
                    throw new Exception("Unexpected value");
            }

            // Set options
            $this->logger->info('set PDO options');
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // Create a new PDO instance
            $this->logger->info('create PDO instance');
            try {
                $this->dbh = new PDO($dsn, $this->configuration['dbUsername'], $this->configuration['dbPassword'], $options);
            } // Catch any errors
            catch (PDOException $e) {
                $this->logger->alert('PDO Exception' . ['Exception' => $e->getMessage()]);
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * @param $query
     */
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
        $this->logger->info('Query', ['query' => $query]);
    }

    /**
     * @param $param
     * @param $value
     * @param null $type
     */
    public function bind($param, $value, $type = null)
    {
        $this->logger->info('Bind DB parameters');
        $this->logger->debug('Bind DB parameters', ['param' => $param, 'value' => $value, 'type' => $type]);
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * @param null $param
     * @return mixed
     */
    public function execute($param = null)
    {
        return $this->stmt->execute($param);
    }

    /**
     * @return mixed
     */
    public function resultset()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function fetchAll()
    {
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    /**
     * @return bool
     */
    public function endTransaction()
    {
        return $this->dbh->commit();
    }

    /**
     * @return bool
     */
    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }

    /**
     * @return mixed
     */
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }

    public function getVersion()
    {
        return $this->dbh->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
    }

    public function getTableName(string $name)
    {
        $this->logger->info('Get table name', ['name' => $name]);
        if ($this->tableCollab && $this->tableCollab[$name]) {
            return $this->tableCollab[$name];
        }
        throw new UnexpectedValueException('Table name not found');
    }

}
