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

}