<?php


namespace phpCollab\Administration;

use Apfelbox\FileDownload\FileDownload;
use phpCollab\Database;
use Ifsnop\Mysqldump as IMysqldump;

/**
 * Class Admins
 * @package phpCollab
 */
class Administration
{
    protected $admins_gateway;
    protected $db;

    /**
     * Assignments constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->admins_gateway = new AdministrationGateway($this->db);
    }

    /**
     * @param $oldVersion
     * @return bool | string
     */
    public function checkForUpdate($oldVersion)
    {
        $phpcollab_url = 'http://www.phpcollab.com/website/version.txt';
        $url = parse_url($phpcollab_url);
        $connection_socket = @fsockopen($url['host'], 80, $errno, $errstr, 30);

        if ($connection_socket) {
            fputs($connection_socket,
                "GET /" . $url['path'] . ($url['query'] ? '?' . $url['query'] : '') . " HTTP/1.0\r\nHost: " . $url['host'] . "\r\n\r\n");
            $http_response = fgets($connection_socket, 22);

            if (preg_match("/200 OK/", $http_response, $regs) || preg_match("/301 Moved/", $http_response, $regs)) {
                // WARNING: in file(), use a final URL to avoid any HTTP redirection
                $sVersiondata = join('', file($phpcollab_url));
                $aVersiondata = explode("|", $sVersiondata);
                $newVersion = $aVersiondata[0];

                if ($oldVersion < $newVersion) {
                    return $newVersion;
                }
            }

            fclose($connection_socket);

            return false;
        } else {
            return false;
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

        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
