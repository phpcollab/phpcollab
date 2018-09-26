<?php


namespace phpCollab\Support;

use phpCollab\Database;

/**
 * Class Support
 * @package phpCollab\Support
 */
class Support
{
    protected $support_gateway;
    protected $db;

    /**
     * Support constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->support_gateway = new SupportGateway($this->db);
    }

    /**
     * @param $supportRequestId
     * @return mixed
     */
    public function getSupportRequestById($supportRequestId)
    {
        return $this->support_gateway->getSupportRequestById($supportRequestId);
    }

    /**
     * @param $status
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatus($status, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatus($status, $sorting);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getSupportRequestByMemberId($memberId)
    {
        return $this->support_gateway->getSupportRequestByMemberId($memberId);
    }

    /**
     * @param $supportRequestId
     * @return mixed
     */
    public function getSupportRequestByIdIn($supportRequestId)
    {
        return $this->support_gateway->getSupportRequestByIdIn($supportRequestId);
    }

    /**
     * @param Int $requestStatus
     * @param Int $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getSupportRequestByStatusAndProjectId($requestStatus, $projectId, $sorting = null)
    {
        return $this->support_gateway->getSupportRequestByStatusAndProjectId($requestStatus, $projectId, $sorting);
    }

    /**
     * @param $requestId
     * @return mixed
     */
    public function getSupportPostsByRequestId($requestId)
    {
        return $this->support_gateway->getSupportPostsByRequestId($requestId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getSupportPostById($postId)
    {
        return $this->support_gateway->getSupportPostById($postId);
    }

    /**
     * @param $postIds
     * @return mixed
     */
    public function getSupportPostsByRequestIdIn($postIds)
    {
        return $this->support_gateway->getSupportPostsByRequestIdIn($postIds);
    }

    /**
     * @param $userId
     * @param $priority
     * @param $subject
     * @param $message
     * @param $project
     * @param int $status
     * @return string
     */
    public function addSupportRequest($userId, $priority, $subject, $message, $project, $status = 0)
    {
        return $this->support_gateway->createSupportRequest($userId, $priority, $subject, $message, $project, $status);
    }

    /**
     * @param $supportRequestIds
     * @return mixed
     */
    public function deleteSupportRequests($supportRequestIds)
    {
        return $this->support_gateway->deleteSupportRequests($supportRequestIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportRequestsByProjectId($projectIds)
    {
        return $this->support_gateway->deleteSupportRequestsByProjectId($projectIds);
    }

    /**
     * @param $requestIds
     * @return mixed
     */
    public function deleteSupportPostsByRequestId($requestIds)
    {
        return $this->support_gateway->deleteSupportPostsByRequestId($requestIds);
    }

    /**
     * @param $supportPostIds
     * @return mixed
     */
    public function deleteSupportPostsById($supportPostIds)
    {
        return $this->support_gateway->deleteSupportPostsById($supportPostIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteSupportPostsByProjectId($projectIds)
    {
        return $this->support_gateway->deleteSupportPostsByProjectId($projectIds);
    }

}
