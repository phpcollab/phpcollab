<?php


namespace phpCollab\Support;

use phpCollab\Database;

/**
 * Class SupportGateway
 * @package phpCollab\Support
 */
class SupportGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * SupportGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $supportRequestId
     * @return mixed
     */
    public function getSupportRequestById($supportRequestId)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.id = :support_request_id";
        $this->db->query($query);
        $this->db->bind(':support_request_id', $supportRequestId);
        return $this->db->resultset();
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function getSupportRequestByIdIn($supportRequestIds)
    {
        $supportRequestIds = explode(',', $supportRequestIds);
        $placeholders = str_repeat ('?, ', count($supportRequestIds)-1) . '?';
        $sql = $this->initrequest["support_requests"] . " WHERE sr.id IN ($placeholders) ORDER BY sr.subject";
        $this->db->query($sql);
        $this->db->execute($supportRequestIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $status
     * @param $projectId
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId($status, $projectId)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.status = :status AND sr.project = :project_id";
        $this->db->query($query);
        $this->db->bind(':status', $status);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $requestId
     * @return mixed
     */
    public function getSupportPostsByRequestId($requestId)
    {
        $query = $this->initrequest["support_posts"] . " WHERE sp.request_id = :support_request_id ORDER BY sp.date";
        $this->db->query($query);
        $this->db->bind(':request_id', $requestId);
        return $this->db->resultset();
    }

    /**
     * @param $supportPostIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn($supportPostIds)
    {
        $supportPostIds = explode(',', $supportPostIds);
        $placeholders = str_repeat ('?, ', count($supportPostIds)-1) . '?';
        $whereStatement = " WHERE sp.id IN($placeholders)";
        $sql = $this->initrequest["support_posts"] . $whereStatement . " ORDER BY sp.id";
        $this->db->query($sql);
        $this->db->execute($supportPostIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests($supportRequestIds)
    {
        $supportRequestIds = explode(',', $supportRequestIds);
        $placeholders = str_repeat ('?, ', count($supportRequestIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_requests']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($supportRequestIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportRequestsByProjectId($projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat ('?, ', count($projectIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_requests']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param $requestIds
     * @return mixed
     */
    public function deleteSupportPostsByRequestId($requestIds)
    {
        $requestIds = explode(',', $requestIds);
        $placeholders = str_repeat ('?, ', count($requestIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_posts']} WHERE request_id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($requestIds);
    }

    /**
     * @param $supportPostIds
     * @return mixed
     */
    public function deleteSupportPostsById($supportPostIds)
    {
        $supportPostIds = explode(',', $supportPostIds);
        $placeholders = str_repeat ('?, ', count($supportPostIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_posts']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($supportPostIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportPostsByProjectId($projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat ('?, ', count($projectIds)-1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_posts']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

}