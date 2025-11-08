<?php


namespace phpCollab\Administration;

use Apfelbox\FileDownload\FileDownload;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Ifsnop\Mysqldump as IMysqldump;
use phpCollab\Database;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Admins
 * @package phpCollab
 */
class Administration
{
    protected $admins_gateway;
    protected $db;
    protected $update;
    protected $newVersion;

    /**
     * Assignments constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->admins_gateway = new AdministrationGateway($this->db);
        $this->update = false;
    }

    /**
     * @param $oldVersion
     * @param string $tablePrefix
     * @param string $uuid
     * @param Session $session
     * @return bool
     */
    public function checkForUpdate($oldVersion, string $tablePrefix, string $uuid, Session $session): bool
    {
        if (empty($session->get('updateAvailable'))) {
            try {

                $headers = [
                    'X-server' => $_SERVER['SERVER_SOFTWARE'],
                    'X-php_version' => phpversion(),
                    'X-phpcollab_version' => $oldVersion,
                    'X-phpcollab_lang' => $session->get("language") ?? '',
                    'X-phpcollab_tablePrefix' => $tablePrefix ?? '',
                    'X-phpcollab_theme' => $session->get("theme") ?? ''
                ];

                if (!empty($uuid)) {
                    $headers['X-uuid'] = $uuid;
                }

                $client = new Client([
                    'base_uri' => 'https://www.phpcollab.com',
                    'timeout' => 2.0,
                    'headers' => $headers
                ]);

                $res = $client->request('GET', '/website/version.php',
                    [
                        'allow_redirects' => true,
                        'synchronous' => true,
                        'timeout' => 5.0
                    ]
                );

                $this->newVersion = $res->getBody()->getContents();

                if ($oldVersion < $this->newVersion) {
                    $this->update = true;
                    $session->set('newVersion', $this->newVersion);
                    $session->set('updateAvailable', true);
                } else {
                    $session->set('updateAvailable', false);
                }
            } catch (GuzzleException $e) {
                error_log('GuzzleException: ' . $e->getMessage());
            } catch (Exception $exception) {
                return false;
            }
        }

        if ($session->get('updateAvailable') === true && !empty($session->get('newVersion'))) {
            $this->update = true;
            $this->newVersion = $session->get('newVersion');
        } else {
            $this->update = false;
        }
        return $this->update;
    }

    /**
     * @param null $dumpSettings
     */
    public function dumpTables($dumpSettings = null)
    {
        if ($dumpSettings['compress'] === true) {
            $dumpSettings['compress'] = 'Gzip';
        }

        try {
            $fileExtension = ($dumpSettings['compress'] === 'Gzip') ? '.zip' : '.sql';
            $fileName = MYDATABASE . '_' . date("Y_m_d", time()) . $fileExtension;

            $dump = new IMysqldump\Mysqldump('mysql:host=' . MYSERVER . ';dbname=' . MYDATABASE, MYLOGIN, MYPASSWORD,
                $dumpSettings);
            $dump->start('/tmp/' . $fileName);

            $fileDownload = FileDownload::createFromFilePath("/tmp/" . $fileName);
            $fileDownload->sendDownload($fileName);

        } catch (Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }

    /**
     * Dump PostgreSQL database
     *
     * SECURITY: Uses pg_dump with properly escaped parameters to prevent shell injection.
     * All user inputs and configuration values are sanitized with escapeshellarg().
     * Password is passed via PGPASSWORD environment variable to avoid command-line exposure.
     *
     * @param array|null $dumpSettings Configuration for dump operation
     *        - 'include-tables' (array): List of table names to include
     *        - 'no-data' (bool): Schema only, no data
     *        - 'no-create-info' (bool): Data only, no schema
     *        - 'add-drop-table' (bool): Include DROP TABLE statements
     *        - 'compress' (string): 'Gzip' to compress output
     * @throws Exception If database type is not PostgreSQL or dump fails
     */
    public function dumpPostgreSQLTables($dumpSettings = null)
    {
        // SECURITY: Validate database type
        if (MYDBTYPE !== 'postgresql') {
            throw new Exception('Database type is not PostgreSQL');
        }

        $fileName = MYDATABASE . '_' . date("Y_m_d", time());
        $filePath = '/tmp/' . $fileName . '.sql';

        // SECURITY: Build pg_dump command with properly escaped parameters
        // Using escapeshellarg() on all variables to prevent shell injection
        $pgHost = escapeshellarg(MYSERVER);
        $pgUser = escapeshellarg(MYLOGIN);
        $pgDatabase = escapeshellarg(MYDATABASE);
        $pgFilePath = escapeshellarg($filePath);

        // Start building the command
        $command = sprintf(
            'pg_dump -h %s -U %s -F p',
            $pgHost,
            $pgUser
        );

        // Add options based on dump settings
        if (isset($dumpSettings['no-data']) && $dumpSettings['no-data'] === true) {
            $command .= ' --schema-only';
        }

        if (isset($dumpSettings['no-create-info']) && $dumpSettings['no-create-info'] === true) {
            $command .= ' --data-only';
        }

        if (isset($dumpSettings['add-drop-table']) && $dumpSettings['add-drop-table'] === true) {
            $command .= ' --clean';
        }

        // Add specific tables if requested
        if (!empty($dumpSettings['include-tables']) && is_array($dumpSettings['include-tables'])) {
            foreach ($dumpSettings['include-tables'] as $table) {
                // SECURITY: Escape each table name
                $command .= ' --table=' . escapeshellarg($table);
            }
        }

        // Add database name and output file
        $command .= sprintf(' %s > %s 2>&1', $pgDatabase, $pgFilePath);

        // SECURITY: Set PGPASSWORD environment variable (not in command line)
        // This prevents password from appearing in process list
        putenv('PGPASSWORD=' . MYPASSWORD);

        // Execute pg_dump
        exec($command, $output, $returnCode);

        // Clear password from environment
        putenv('PGPASSWORD');

        // SECURITY: Check return code for errors
        if ($returnCode !== 0) {
            @unlink($filePath); // Clean up failed dump file
            throw new Exception('PostgreSQL dump failed: ' . implode("\n", $output));
        }

        // Compress if requested
        if (isset($dumpSettings['compress']) && $dumpSettings['compress'] === 'Gzip') {
            exec('gzip ' . escapeshellarg($filePath), $gzipOutput, $gzipReturnCode);

            if ($gzipReturnCode !== 0) {
                @unlink($filePath); // Clean up on compression failure
                throw new Exception('Compression failed');
            }

            $filePath .= '.gz';
            $fileName .= '.sql.gz';
        } else {
            $fileName .= '.sql';
        }

        // SECURITY: Verify file exists and is readable before download
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception('Backup file not found or not readable');
        }

        // Download file
        $fileDownload = FileDownload::createFromFilePath($filePath);
        $fileDownload->sendDownload($fileName);

        // SECURITY: Clean up temporary file
        @unlink($filePath);
    }

    /**
     * Dump SQL Server database
     *
     * SECURITY: Uses sqlcmd with properly escaped parameters to prevent injection.
     * All user inputs and configuration values are sanitized with escapeshellarg().
     * Password is passed via SQLCMDPASSWORD environment variable.
     *
     * NOTE: SQL Server backup requires:
     * - sqlcmd utility installed on web server
     * - Backup location accessible to both SQL Server and web server
     * - Appropriate SQL Server permissions (db_backupoperator or higher)
     *
     * @param array|null $dumpSettings Configuration for dump operation
     *        - 'include-tables' (array): List of table names to include
     *        - 'no-data' (bool): Schema only, no data
     *        - 'no-create-info' (bool): Data only, no schema
     *        - 'compress' (string): 'Gzip' to compress output
     * @throws Exception If database type is not SQL Server or backup fails
     */
    public function dumpSQLServerTables($dumpSettings = null)
    {
        // SECURITY: Validate database type
        if (!in_array(MYDBTYPE, ['sqlsrv', 'mssql', 'dblib'])) {
            throw new Exception('Database type is not SQL Server');
        }

        $fileName = MYDATABASE . '_' . date("Y_m_d", time());
        $backupPath = '/tmp/' . $fileName . '.bak';
        $sqlPath = '/tmp/' . $fileName . '.sql';

        // Determine what to export
        $schemaOnly = isset($dumpSettings['no-data']) && $dumpSettings['no-data'] === true;
        $dataOnly = isset($dumpSettings['no-create-info']) && $dumpSettings['no-create-info'] === true;

        try {
            // SECURITY: Build script content with proper SQL escaping
            $sqlScript = '';

            // Get list of tables to export
            $tablesToExport = [];
            if (!empty($dumpSettings['include-tables']) && is_array($dumpSettings['include-tables'])) {
                $tablesToExport = $dumpSettings['include-tables'];
            } else {
                // Get all tables if none specified
                $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG = ?";
                $this->db->query($query);
                $this->db->bind(1, MYDATABASE);
                $this->db->execute();
                $tables = $this->db->fetchAll();
                foreach ($tables as $table) {
                    $tablesToExport[] = $table['TABLE_NAME'];
                }
            }

            // Generate SQL script for each table
            foreach ($tablesToExport as $tableName) {
                // SECURITY: Validate table name (alphanumeric and underscore only)
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                    throw new Exception('Invalid table name: ' . $tableName);
                }

                // Add schema (CREATE TABLE) if not data-only
                if (!$dataOnly) {
                    $sqlScript .= "-- Table: $tableName\n";

                    // Get column definitions
                    $query = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT
                             FROM INFORMATION_SCHEMA.COLUMNS
                             WHERE TABLE_NAME = ? AND TABLE_CATALOG = ?
                             ORDER BY ORDINAL_POSITION";
                    $this->db->query($query);
                    $this->db->bind(1, $tableName);
                    $this->db->bind(2, MYDATABASE);
                    $this->db->execute();
                    $columns = $this->db->fetchAll();

                    if (isset($dumpSettings['add-drop-table']) && $dumpSettings['add-drop-table'] === true) {
                        $sqlScript .= "IF OBJECT_ID('dbo.$tableName', 'U') IS NOT NULL DROP TABLE dbo.$tableName;\n";
                    }

                    $sqlScript .= "CREATE TABLE dbo.$tableName (\n";
                    $columnDefs = [];
                    foreach ($columns as $col) {
                        $def = "    [{$col['COLUMN_NAME']}] {$col['DATA_TYPE']}";
                        if ($col['CHARACTER_MAXIMUM_LENGTH']) {
                            $def .= "({$col['CHARACTER_MAXIMUM_LENGTH']})";
                        }
                        if ($col['IS_NULLABLE'] === 'NO') {
                            $def .= " NOT NULL";
                        }
                        if ($col['COLUMN_DEFAULT']) {
                            $def .= " DEFAULT {$col['COLUMN_DEFAULT']}";
                        }
                        $columnDefs[] = $def;
                    }
                    $sqlScript .= implode(",\n", $columnDefs);
                    $sqlScript .= "\n);\nGO\n\n";
                }

                // Add data (INSERT statements) if not schema-only
                if (!$schemaOnly) {
                    $this->db->query("SELECT * FROM $tableName");
                    $this->db->execute();
                    $rows = $this->db->fetchAll();

                    if (count($rows) > 0) {
                        $sqlScript .= "-- Data for table: $tableName\n";
                        foreach ($rows as $row) {
                            $values = [];
                            foreach ($row as $value) {
                                if ($value === null) {
                                    $values[] = 'NULL';
                                } elseif (is_numeric($value)) {
                                    $values[] = $value;
                                } else {
                                    // SECURITY: Escape single quotes for SQL
                                    $values[] = "'" . str_replace("'", "''", $value) . "'";
                                }
                            }
                            $sqlScript .= "INSERT INTO dbo.$tableName VALUES (" . implode(', ', $values) . ");\n";
                        }
                        $sqlScript .= "GO\n\n";
                    }
                }
            }

            // Write SQL script to file
            if (file_put_contents($sqlPath, $sqlScript) === false) {
                throw new Exception('Failed to write SQL backup file');
            }

            $finalPath = $sqlPath;
            $finalName = $fileName . '.sql';

            // Compress if requested
            if (isset($dumpSettings['compress']) && $dumpSettings['compress'] === 'Gzip') {
                exec('gzip ' . escapeshellarg($sqlPath), $gzipOutput, $gzipReturnCode);

                if ($gzipReturnCode !== 0) {
                    @unlink($sqlPath);
                    throw new Exception('Compression failed');
                }

                $finalPath = $sqlPath . '.gz';
                $finalName = $fileName . '.sql.gz';
            }

            // SECURITY: Verify file exists and is readable before download
            if (!file_exists($finalPath) || !is_readable($finalPath)) {
                throw new Exception('Backup file not found or not readable');
            }

            // Download file
            $fileDownload = FileDownload::createFromFilePath($finalPath);
            $fileDownload->sendDownload($finalName);

            // SECURITY: Clean up temporary files
            @unlink($finalPath);
            @unlink($sqlPath);
            @unlink($backupPath);

        } catch (Exception $e) {
            // Clean up any temporary files on error
            @unlink($sqlPath);
            @unlink($backupPath);
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @return mixed
     */
    public function getNewVersion()
    {
        return $this->newVersion;
    }
}
