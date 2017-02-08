<?php


namespace phpCollab\Files;

use phpCollab\Database;

/**
 * Class Files
 * @package phpCollab\Files
 */
class Files
{
    protected $files_gateway;
    protected $db;

    /**
     * Files constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->files_gateway = new FilesGateway($this->db);
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFiles($fileId)
    {
        return $this->files_gateway->getFiles($fileId);
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFileById($fileId)
    {
        return $this->files_gateway->getFileById($fileId);
    }

    /**
     * @return mixed
     */
    public function getPublishedFiles()
    {
        return $this->files_gateway->getPublishedFiles();
    }

    /**
     * @return mixed
     */
    public function getUnPublishedFiles()
    {
        return $this->files_gateway->getUnPublishedFiles();
    }

    /**
     *
     */
    public function publishFile()
    {

    }

    /**
     *
     */
    public function unPublishFile()
    {

    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFileVersions($fileId)
    {
        $fileId = filter_var((string)$fileId, FILTER_SANITIZE_STRING);
        $response = $this->files_gateway->getFileVersions($fileId, 3);
        return $response;
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFilePeerReviews($fileId)
    {
        $fileId = filter_var((string)$fileId, FILTER_SANITIZE_STRING);
        $response = $this->files_gateway->getFilePeerReviews($fileId);
        return $response;
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function deleteFile($fileId)
    {
        $fileId = filter_var((string)$fileId, FILTER_SANITIZE_STRING);

        $response = $this->files_gateway->deleteFiles($fileId);
        return $response;
    }

}