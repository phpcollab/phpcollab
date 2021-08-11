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

    /**
     * SupportGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];

    }

    /**
     * @param int $supportRequestId
     * @return mixed
     */
    public function getSupportRequestById(int $supportRequestId)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.id = :support_request_id";
        $this->db->query($query);
        $this->db->bind(':support_request_id', $supportRequestId);
        return $this->db->single();
    }

    /**
     * @param int $status
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatus(int $status, string $sorting = null)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.status = :status" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':status', $status);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByProject(int $projectId, string $sorting = null)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @return mixed
     */
    public function getSupportRequestByMemberId(int $memberId)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.member = :member_id";
        $this->db->query($query);
        $this->db->bind(':member_id', $memberId);
        return $this->db->resultset();
    }

    /**
     * @param string $supportRequestIds
     * @return mixed
     */
    public function getSupportRequestByIdIn(string $supportRequestIds)
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
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId(int $status, int $projectId, string $sorting = null)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.status = :status AND sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':status', $status);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $memberId
     * @param int $projectId
     * @param string|null $sorting
     * @return mixed
     */
    public function getSupportRequestByMemberIdAndProjectId(int $memberId, int $projectId, string $sorting = null)
    {
        $query = $this->initrequest["support_requests"] . " WHERE sr.member = :member_id AND sr.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':member_id', $memberId);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $requestId
     * @return mixed
     */
    public function getSupportPostsByRequestId(int $requestId)
    {
        $query = $this->initrequest["support_posts"] . " WHERE sp.request_id = :support_request_id ORDER BY sp.date";
        $this->db->query($query);
        $this->db->bind(':support_request_id', $requestId);
        return $this->db->resultset();
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function getSupportPostById(int $postId)
    {
        $query = $this->initrequest["support_posts"] . " WHERE sp.id = :support_post_id";
        $this->db->query($query);
        $this->db->bind(':support_post_id', $postId);
        return $this->db->single();
    }

    /**
     * @param string $supportPostIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn(string $supportPostIds)
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
     * @param int $userId
     * @param int $priority
     * @param string $subject
     * @param string $message
     * @param int $project
     * @param int $status
     * @return string
     */
    public function createSupportRequest(int $userId, int $priority, string $subject, string $message, int $project, int $status): string
    {
        $sql = <<<SQL
INSERT INTO {$this->db->getTableName("support_requests")} (
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
     * @param int $requestId
     * @param string $message
     * @param string $dateCreated
     * @param int $ownerId
     * @param int $projectId
     * @return string
     */
    public function addPost(int $requestId, string $message, string $dateCreated, int $ownerId, int $projectId): string
    {
        $sql = <<<SQL
INSERT INTO {$this->db->getTableName("support_posts")} (
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
     * @param int $requestId
     * @param int $status
     * @param string $dateClose
     * @return mixed
     */
    public function updateSupportRequest(int $requestId, int $status, string $dateClose)
    {
        $query = "UPDATE {$this->db->getTableName("support_requests")} SET status = :status, date_close = :date_close WHERE id = :request_id";

        $this->db->query($query);
        $this->db->bind(":request_id", $requestId);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_close", $dateClose);
        return $this->db->execute();
    }

    /**
     * @param string $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests(string $supportRequestIds)
    {
        $supportRequestIds = explode(',', $supportRequestIds);
        $placeholders = str_repeat('?, ', count($supportRequestIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("support_requests")} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($supportRequestIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteSupportRequestsByProjectId(string $projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("support_requests")} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param string $requestIds
     * @return mixed
     */
    public function deleteSupportPostsByRequestId(string $requestIds)
    {
        $requestIds = explode(',', $requestIds);
        $placeholders = str_repeat('?, ', count($requestIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("support_posts")} WHERE request_id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($requestIds);
    }

    /**
     * @param string $supportPostIds
     * @return mixed
     */
    public function deleteSupportPostsById(string $supportPostIds)
    {
        $supportPostIds = explode(',', $supportPostIds);
        $placeholders = str_repeat('?, ', count($supportPostIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("support_posts")} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($supportPostIds);
    }

    /**
     * @param string $projectIds
     * @return mixed
     */
    public function deleteSupportPostsByProjectId(string $projectIds)
    {
        $projectIds = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("support_posts")} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectIds);
    }

    /**
     * @param string|null $sorting
     * @return string
     */
    private function orderBy(string $sorting = null): string
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "sr.id",
                "sr.subject",
                "sr.member",
                "sr.project",
                "sr.priority",
                "sr.status",
                "sr.date_open",
                "sr.date_close"
            ];
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
