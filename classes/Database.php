<?php
namespace phpCollab;

use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use \PDO;
use PDOException;

/**
 * User: mindblender
 * Date: 5/4/16
 * Time: 12:36 AM
 */
class Database
{
//    private $host = MYSERVER;
//    private $user = MYLOGIN;
//    private $pass = MYPASSWORD;
//    private $dbname = MYDATABASE;
    private $configuration;
    private $dbh;
    private $stmt;
    private $logger;
    private $tablenameArray;

    /**
     * Database constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        /*
         * Setup logger
         */
        try {
            $stream = new StreamHandler(APP_ROOT . '/logs/phpcollab.log', Logger::DEBUG);
            $this->logger = new Logger('database');
            $this->logger->pushHandler($stream);
            $this->logger->pushProcessor(new IntrospectionProcessor());
        } catch (Exception $e) {
            error_log('library error: ' . $e->getMessage());
        }

        /*
         * End logger init
         */
        $this->configuration = $config;



        $this->log('__construct init');
        if ($this->dbh === null) {
            $this->log('set DSN');
            // Set DSN
            $dsn = 'mysql:host=' . $this->configuration['dbServer'] . ';dbname=' . $this->configuration['dbName'];
            // Set options
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );

            // Create a new PDO instanace
            try {
                $this->dbh = new PDO($dsn, $this->configuration['dbUsername'], $this->configuration['dbPassword'], $options);
            } // Catch any errors
            catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    private function log($msg)
    {
        $this->logger->debug($msg);
    }

    /**
     * @param $query
     */
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    /**
     * @param $param
     * @param $value
     * @param null $type
     */
    public function bind($param, $value, $type = null)
    {
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

}
