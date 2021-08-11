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
     * @param int $topicId
     * @param int $memberId
     * @param string $message
     * @param string $created
     * @return string
     */
    public function createPost(int $topicId, int $memberId, string $message, string $created): string
    {
        $query = "INSERT INTO {$this->db->getTableName("posts")} (topic, member, created, message) VALUES (:topic, :member, :created, :message)";
        $this->db->query($query);
        $this->db->bind(":topic", $topicId);
        $this->db->bind(":member", $memberId);
        $this->db->bind(":message", $message);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param int $projectId
     * @param int $memberId
     * @param string $subject
     * @param int $status
     * @param string $last_post
     * @param int $posts
     * @param string $published
     * @return string
     */
    public function createTopic(int $projectId, int $memberId, string $subject, int $status, string $last_post, int $posts, string $published): string
    {

        $query = "INSERT INTO {$this->db->getTableName("topics")} (project, owner, subject, status, last_post, posts, published) VALUES (:project, :owner, :subject, :status, :last_post, :posts, :published)";
        $this->db->query($query);
        $this->db->bind(":project", $projectId);
        $this->db->bind(":owner", $memberId);
        $this->db->bind(":subject", $subject);
        $this->db->bind(":status", $status);
        $this->db->bind(":last_post", $last_post);
        $this->db->bind(":posts", $posts);
        $this->db->bind(":published", $published);
        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param int $ownerId
     * @param string|null $sorting
     * @return mixed
     */
    public function getTopicsByOwner(int $ownerId, string $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :owner_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param int|null $offset
     * @param int|null $limit
     * @param string|null $sorting
     * @return mixed
     */
    public function getTopicsByProject(int $projectId, int $offset = null, int $limit = null, string $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :project_id" . $this->orderBy($sorting) . $this->limit($offset,
                $limit);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $projectId
     * @param int|null $offset
     * @param int|null $limit
     * @param string|null $sorting
     * @return mixed
     */
    public function getProjectSiteTopics(int $projectId, int $offset = null, int $limit = null, string $sorting = null)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.project = :project_id AND topic.published = '0'" . $this->orderBy($sorting) . $this->limit($offset,
                $limit);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function getTopicById(int $topicId)
    {
        $query = $this->initrequest["topics"] . " WHERE topic.id = :topic_id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->single();
    }

    /**
     * @param string $topicIds
     * @return mixed
     */
    public function getTopicsIn(string $topicIds)
    {
        $topicIds = explode(',', $topicIds);
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $whereStatement = " WHERE topic.id IN($placeholders)";
        $this->db->query($this->initrequest["topics"] . $whereStatement);
        $this->db->execute($topicIds);
        return $this->db->resultset();
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function getPostsByTopicId(int $topicId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id ORDER BY pos.created DESC";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        return $this->db->resultset();
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function getPostById(int $postId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.id = :post_id";
        $this->db->query($query);
        $this->db->bind(':post_id', $postId);
        return $this->db->single();
    }

    /**
     * @param int $topicId
     * @param int $ownerId
     * @return mixed
     */
    public function getPostsByTopicIdAndNotOwner(int $topicId, int $ownerId)
    {
        $query = $this->initrequest["posts"] . " WHERE pos.topic = :topic_id AND pos.member != :owner_id ORDER BY mem.id";
        $this->db->query($query);
        $this->db->bind(':topic_id', $topicId);
        $this->db->bind(':owner_id', $ownerId);
        return $this->db->resultset();
    }

    /**
     * @param string $projectIds
     * @param string $dateFilter
     * @param string|null $sorting
     * @return mixed
     */
    public function getTopicsByProjectAndFilteredByDate(string $projectIds, string $dateFilter, string $sorting = null)
    {
        $projectId = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $where = " WHERE topic.project IN($placeholders) AND topic.last_post > ? AND topic.status = '1'";

        $query = $this->initrequest["topics"] . $where . $this->orderBy($sorting);
        array_push($projectId, $dateFilter);
        $this->db->query($query);
        $this->db->execute($projectId);
        return $this->db->fetchAll();
    }

    /**
     * @param string $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function publishTopic(string $topicIds)
    {
        if (strpos($topicIds, ',')) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("topics")} SET published = 0 WHERE id IN ($placeholders)";
            $this->db->query($sql);

            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("topics")} SET published = 0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param string $topicIds
     * @return mixed
     * @internal param string $table
     */
    public function unPublishTopic(string $topicIds)
    {
        if (strpos($topicIds, ',')) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("topics")} SET published = 1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("topics")} SET published = 1 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }


    /**
     * @param string $topicIds
     * @return mixed
     * @internal param $table
     */
    public function closeTopic(string $topicIds)
    {
        if (strpos($topicIds, ',')) {
            $topicIds = explode(',', $topicIds);
            $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("topics")} SET status=0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($topicIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("topics")} SET status=0 WHERE id = :topic_ids";

            $this->db->query($sql);

            $this->db->bind(':topic_ids', $topicIds);

            return $this->db->execute();
        }
    }

    /**
     * @param array $topicIds
     * @return mixed
     */
    public function deleteTopics(array $topicIds)
    {
        // Generate placeholders
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("topics")} WHERE id IN ($placeholders)";
        $this->db->query($sql);

        return $this->db->execute($topicIds);
    }

    /**
     * @param array $topicIds
     * @return mixed
     */
    public function deletePostsByTopicIds(array $topicIds)
    {
        // Generate placeholders
        $placeholders = str_repeat('?, ', count($topicIds) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("posts")} WHERE topic IN ($placeholders)";
        $this->db->query($sql);

        return $this->db->execute($topicIds);
    }

    /**
     * @param string $projectId
     * @return mixed
     */
    public function deleteTopicsByProjectId(string $projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("topics")} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param string $projectId
     * @return mixed
     */
    public function deletePostsByProjectId(string $projectId)
    {
        $projectId = explode(',', $projectId);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("posts")} WHERE topic IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }

    /**
     * @param int $topicId
     * @param string $updateDate
     * @return mixed
     */
    public function incrementTopicPostsCount(int $topicId, string $updateDate)
    {
        $query = "UPDATE {$this->db->getTableName("topics")} SET last_post = :last_post, posts = posts + 1 WHERE id = :topic_id";
        $this->db->query($query);
        $this->db->bind(":last_post", $updateDate);
        $this->db->bind(":topic_id", $topicId);
        return $this->db->execute();
    }

    /**
     * @param int $topicId
     * @return mixed
     */
    public function decrementTopicPostsCount(int $topicId)
    {
        $query = "UPDATE {$this->db->getTableName("topics")} SET last_post = :last_post, posts = posts - 1 WHERE id = :topic_id";
        $this->db->query($query);
        $this->db->bind(":last_post", date('Y-m-d h:i'));
        $this->db->bind(":topic_id", $topicId);
        return $this->db->execute();
    }

    /**
     * @param string $query
     * @param string|null $sorting
     * @param int|null $limit
     * @param int|null $rowLimit
     * @return mixed
     */
    public function searchResultTopics(string $query, string $sorting = null, int $limit = null, int $rowLimit = null)
    {
        $sql = $this->initrequest['topics'] . ' ' . $query . $this->orderBy($sorting) . $this->limit($limit, $rowLimit);
        $this->db->query($sql);
        $this->db->execute();
        return $this->db->resultset();
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function deletePost(int $postId)
    {
        $sql = "DELETE FROM {$this->db->getTableName("posts")} WHERE id = :post_id";
        $this->db->query($sql);
        $this->db->bind(':post_id', $postId);
        return $this->db->execute();
    }

    /**
     * Returns the LIMIT attribute for SQL strings
     * @param int|null $offset
     * @param int|null $limit
     * @return string
     */
    private function limit(int $offset = null, int $limit = null): string
    {
        if (!is_null($offset) && !is_null($limit)) {
            return " LIMIT $limit OFFSET $offset";
        }
        return '';
    }

    /**
     * @param string|null $sorting
     * @return string
     */
    private function orderBy(string $sorting = null): string
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "topic.id",
                "topic.project",
                "topic.owner",
                "topic.subject",
                "topic.status",
                "topic.last_post",
                "topic.posts",
                "topic.published",
                "mem.id",
                "mem.login",
                "mem.name",
                "mem.email_work",
                "pro.id",
                "pro.name"
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
