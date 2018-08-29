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
     * @param $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getTopicsByOwner($ownerId, $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :owner_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getTopicsByProject($projectId, $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getTopicById($topicId)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.id = :topic_id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->single();
    }

    /**
     * @param $topicId
     * @return mixed
     */
    public function getPostsByTopicId($topicId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id ORDER BY pos.created DESC";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->resultset();
    }

    /**
     * @param $topicId
     * @param $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner($topicId, $ownerId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id AND pos.member != :owner_id ORDER BY mem.id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
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
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 0 WHERE id IN ($placeholders)";
            $this->db->query($sql);

            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 0 WHERE id = :topic_ids";

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
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET published = 1 WHERE id = :topic_ids";

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
            $sql = "UPDATE {$this->tableCollab['topics']} SET status=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->tableCollab['topics']} SET status=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deleteTopicsByProjectId($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['topics']} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function deletePostsByProjectId($projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['posts']} WHERE topic IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["topic.id","topic.project","topic.owner","topic.subject","topic.status","topic.last_post","topic.posts","topic.published","mem.id","mem.login","mem.name","mem.email_work","pro.id","pro.name"];
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
