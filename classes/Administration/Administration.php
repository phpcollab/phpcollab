<?php


namespace phpCollab\Administration;

use Apfelbox\FileDownload\FileDownload;
use phpCollab\Database;
use Ifsnop\Mysqldump as IMysqldump;
//use Apfelbox\FileDownload\FileDownload;

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
     * @param null $dumpSettings
     */
    public function dumpTables($dumpSettings = null)
    {
        if ($dumpSettings['compress'] == true) {
            $dumpSettings['compress'] = 'Gzip';
        }

        try {
            $fileExtension = ($dumpSettings['compress'] == true) ? '.zip' : '.sql';
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
