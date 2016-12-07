<?php


namespace phpCollab\Topics;

use phpCollab\Database;

class TopicsGateway
{
    protected $db;
    protected $initrequest;

    /**
     * Topics constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    /**
     * @param $topicIds
     * @param string $table
     * @return mixed
     */
    public function publishTopic($topicIds, $table) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE ". $table ." SET published=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);

            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE ". $table ." SET published=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param $topicIds
     * @param string $table
     * @return mixed
     */
    public function unPublishTopic($topicIds, $table) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE ". $table ." SET published=1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE ". $table ." SET published=1 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }


    /**
     * @param $topicIds
     * @param $table
     * @return mixed
     */
    public function closeTopic($topicIds, $table) {
        if ( strpos($topicIds, ',') ) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat ('?, ', count($topicIds)-1) . '?';
            $sql = "UPDATE " . $table ." SET status=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE " . $table . " SET status=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }
}