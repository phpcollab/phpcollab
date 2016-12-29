<?php


namespace phpCollab\Topics;

use phpCollab\Database;

/**
 * Class TopicsGateway
 * @package phpCollab\Topics
 */
class TopicsGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * Topics constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function publishTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE ". $this->tableCollab['topics'] ." SET published=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);

            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE ". $this->tableCollab['topics'] ." SET published=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function unPublishTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE ". $this->tableCollab['topics'] ." SET published=1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE ". $this->tableCollab['topics'] ." SET published=1 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }


    /**
     * @param $topicIds
     * @return mixed
     * @internal param $table
     */
    public function closeTopic($topicIds) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE " . $this->tableCollab['topics'] ." SET status=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE " . $this->tableCollab['topics'] . " SET status=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }
}