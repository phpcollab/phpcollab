<?php


namespace phpCollab\Administration;

use Apfelbox\FileDownload\FileDownload;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use phpCollab\Database;
use Ifsnop\Mysqldump as IMysqldump;
use GuzzleHttp\Client;

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
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->admins_gateway = new AdministrationGateway($this->db);
        $this->update = false;
    }

    /**
     * @param $oldVersion
     * @return bool | string
     */
    public function checkForUpdate($oldVersion)
    {
        if (!isset($_SESSION['updateAvailable'])) {
            try {
                $client = new Client([
                    'base_uri' => 'https://www.phpcollab.com',
                    'timeout' => 2.0,
                    'headers' => [
                        'X-server' => $_SERVER['SERVER_SOFTWARE'],
                        'X-phpc_version' => $oldVersion,
                        'X-php_version' => phpversion(),
                    ]
                ]);

                $res = $client->request('GET', '/website/version.php',
                    [
                        'allow_redirects' => true,
                        'synchronous' => true,
                        'timeout' => 10.0
                    ]
                );

                $this->newVersion = $res->getBody()->getContents();

                if ($oldVersion < $this->newVersion) {
                    $this->update = true;
                    $_SESSION['newVersion'] = $this->newVersion;
                    $_SESSION['updateAvailable'] = true;
                } else {
                    $_SESSION['updateAvailable'] = false;
                }
            } catch (Exception $exception) {
                return false;
            } catch (GuzzleException $e) {
                return false;
            }
        } else if ($_SESSION['updateAvailable'] === true && isset($_SESSION['newVersion'])) {
            $this->update = true;
            $this->newVersion = $_SESSION['newVersion'];
        } else {
            $this->update = false;
        }
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
            $fileExtension = ($dumpSettings['compress'] === true) ? '.zip' : '.sql';
            $fileName = MYDATABASE . '_' . date("Y_m_d",time()) . $fileExtension;

            $dump = new IMysqldump\Mysqldump('mysql:host='.MYSERVER.';dbname='.MYDATABASE, MYLOGIN, MYPASSWORD, $dumpSettings);
            $dump->start('/tmp/'. $fileName);

            $fileDownload = FileDownload::createFromFilePath("/tmp/" . $fileName);
            $fileDownload->sendDownload($fileName);

        } catch (Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
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
