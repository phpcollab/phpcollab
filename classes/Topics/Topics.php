<?php


namespace phpCollab\Topics;

use phpCollab\Database;

/**
 * Class Topics
 * @package phpCollab\Topics
 */
class Topics
{
    protected $topics_gateway;
    protected $db;

    /**
     * Topics constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->topics_gateway = new TopicsGateway($this->db);
    }

    /**
     * @param $projectId
     * @param $sorting
     * @return mixed
     */
    public function getTopicsByProjectId($projectId, $sorting)
    {
        $projectId = filter_var($projectId, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicsByProject($projectId, $sorting);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getTopicsByTopicOwner($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicsByOwner($ownerId, $sorting);
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getTopicByTopicId($topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getTopicById($topicId);
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getPostsByTopicId($topicId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicId($topicId);
    }

    /**
     * @param $topicId
     * @param $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner($topicId, $ownerId)
    {
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $ownerId = filter_var($ownerId, FILTER_SANITIZE_STRING);
        return $this->topics_gateway->getPostsByTopicIdAndNotOwner($topicId, $ownerId);
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function closeTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->closeTopic($topicId);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function publishTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->publishTopic($topicId);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @return string
     */
    public function unPublishTopic($topicId)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->unPublishTopic($topicId);

        return $data;
    }

    /**
     * @param $topicData
     */
    public function addTopic($topicData)
    {

    }

    /**
     * @param $postData
     */
    public function addPost($postData)
    {

    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteTopicWhereProjectIdIn($projectIds)
    {
        return $this->topics_gateway->deleteTopicsByProjectId($projectIds);
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deletePostsByProjectId($projectIds)
    {
        return $this->topics_gateway->deletePostsByProjectId($projectIds);
    }

}
