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

}