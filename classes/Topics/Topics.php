<?php


namespace phpCollab\Topics;

use phpCollab\Database;

class Topics
{
    protected $topics_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->topics_gateway = new TopicsGateway($this->db);
    }

    /**
     * @param integer|array $topicId
     * @param string $table
     * @return string
     */
    public function closeTopic($topicId, $table)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $table = filter_var($table, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->closeTopic($topicId, $table);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @param string $table
     * @return string
     */
    public function publishTopic($topicId, $table)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $table = filter_var($table, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->publishTopic($topicId, $table);

        return $data;
    }

    /**
     * @param integer|array $topicId
     * @param string $table
     * @return string
     */
    public function unPublishTopic($topicId, $table)
    {
        // Sanitaize data
        $topicId = filter_var($topicId, FILTER_SANITIZE_STRING);
        $table = filter_var($table, FILTER_SANITIZE_STRING);

        $data = $this->topics_gateway->unPublishTopic($topicId, $table);

        return $data;
    }
}