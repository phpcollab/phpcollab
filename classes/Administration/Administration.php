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
