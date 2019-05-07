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
        return $this->db->single();
    }

    /**
     * @param $status
     * @param $sorting
     * @return mixed
     */
    public function getSupportRequestByStatus($status, $sorting)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.status = :status" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':status', $status);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param $sorting
     * @return mixed
     */
    public function getSupportRequestByProject($projectId, $sorting)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getSupportRequestByMemberId($memberId)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.member = :member_id";
        $this->db->query($query);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function getSupportRequestByIdIn($supportRequestIds)
    {
        $supportRequestIds = explode(',', $supportRequestIds);
        $placeholders = str_repeat('?, ', count($supportRequestIds) - 1) . '?';
        $sql = $this->initrequest["support_requests"] . " WHERE sr.id IN ($placeholders) ORDER BY sr.subject";
        $this->db->query($sql);
        $this->db->execute($supportRequestIds);
        return $this->db->fetchAll();
    }

    /**
     * @param Int $status
     * @param Int $projectId
     * @param $sorting
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId($status, $projectId, $sorting)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.status = :status AND sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':status', $status);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $memberId
     * @param $projectId
     * @param $sorting
     * @return mixed
     */
    public function getSupportRequestByMemberIdAndProjectId($memberId, $projectId, $sorting)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.member = :member_id AND sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':member_id', $memberId);
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
        $this->db->bind(':support_request_id', $requestId);
        return $this->db->resultset();
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getSupportPostById($postId)
    {
        $query = $this->initrequest["support_posts"] . " WHERE sp.id = :support_post_id";
        $this->db->query($query);
        $this->db->bind(':support_post_id', $postId);
        return $this->db->single();
    }

    /**
     * @param $supportPostIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn($supportPostIds)
    {
        $supportPostIds = explode(',', $supportPostIds);
        $placeholders = str_repeat('?, ', count($supportPostIds) - 1) . '?';
        $whereStatement = " WHERE sp.id IN($placeholders)";
        $sql = $this->initrequest["support_posts"] . $whereStatement . " ORDER BY sp.id";
        $this->db->query($sql);
        $this->db->execute($supportPostIds);
        return $this->db->fetchAll();
    }

    /**
     * @param $userId
     * @param $priority
     * @param $subject
     * @param $message
     * @param $project
     * @param $status
     * @return string
     */
    public function createSupportRequest($userId, $priority, $subject, $message, $project, $status)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["support_requests"]} (
member, priority, subject, message, project, status, date_open
) VALUES(
:member_id, :priority, :subject, :message, :project, :status, NOW())
SQL;
        $this->db->query($sql);
        $this->db->bind("member_id", $userId);
        $this->db->bind("priority", $priority);
        $this->db->bind("subject", $subject);
        $this->db->bind("message", $message);
        $this->db->bind("project", $project);
        $this->db->bind("status", $status);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param $requestId
     * @param $message
     * @param $dateCreated
     * @param $ownerId
     * @param $projectId
     * @return string
     */
    public function addPost($requestId, $message, $dateCreated, $ownerId, $projectId)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["support_posts"]} (
request_id, message, date, owner, project
) VALUES (
:request_id, :message, :date, :owner_id, :project_id)
SQL;
        $this->db->query($sql);
        $this->db->bind("request_id", $requestId);
        $this->db->bind(":message", $message);
        $this->db->bind(":date", $dateCreated);
        $this->db->bind(":owner_id", $ownerId);
        $this->db->bind(":project_id", $projectId);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param $requestId
     * @param $status
     * @param $dateClose
     * @return mixed
     */
    public function updateSupportRequest($requestId, $status, $dateClose)
    {
        $query = "UPDATE {$this->tableCollab["support_requests"]} SET status = :status, date_close = :date_close WHERE id = :request_id";

        $this->db->query($query);
        $this->db->bind(":request_id", $requestId);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_close", $dateClose);
        return $this->db->execute();
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests($supportRequestIds)
    {
        $supportRequestIds = explode(',', $supportRequestIds);
        $placeholders = str_repeat('?, ', count($supportRequestIds) - 1) . '?';
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
        $placeholders = str_repeat('?, ', count($projectIds) - 1) . '?';
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
        $placeholders = str_repeat('?, ', count($requestIds) - 1) . '?';
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
        $placeholders = str_repeat('?, ', count($supportPostIds) - 1) . '?';
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
        $placeholders = str_repeat('?, ', count($projectIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['support_posts']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["sr.id", "sr.subject", "sr.member", "sr.project", "sr.priority", "sr.status", "sr.date_open", "sr.date_close"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }

}
