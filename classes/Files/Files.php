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
     * @param $projectId
     * @param $phaseId
     * @param $sorting
     * @return mixed
     */
    public function getFilesByProjectAndPhaseWithoutTasksAndParent($projectId, $phaseId, $sorting)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        $phaseId = filter_var($phaseId, FILTER_VALIDATE_INT);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->files_gateway->getFilesByProjectAndPhaseWithoutTasksAndParent($projectId, $phaseId, $sorting);
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
     * @param mixed $filesId Can be a single ID or multiple IDs
     * @return mixed
     */
    public function publishFile($filesId)
    {
        $filesId = filter_var($filesId, FILTER_SANITIZE_STRING);
        return $this->files_gateway->publishFiles($filesId);
    }

    /**
     * @param $filesId
     * @return mixed
     */
    public function publishFileByIdOrVcParent($filesId)
    {
        $filesId = filter_var($filesId, FILTER_SANITIZE_STRING);
        return $this->files_gateway->publishFilesByIdOrInVcParent($filesId);
    }

    /**
     * @param $filesId
     * @return mixed
     */
    public function unPublishFile($filesId)
    {
        $filesId = filter_var($filesId, FILTER_SANITIZE_STRING);
        return $this->files_gateway->unPublishFiles($filesId);
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

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteFilesByProjectId($projectIds)
    {
        $projectIds = filter_var((string)$projectIds, FILTER_SANITIZE_STRING);
        return $this->files_gateway->deleteFilesByProjectId($projectIds);
    }

}