<?php


namespace phpCollab\NewsDesk;

use phpCollab\Database;

/**
 * Class NewsDeskGateway
 * @package phpCollab\NewsDesk
 */
class NewsDeskGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * NewsDeskGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param null $sortBy
     * @return mixed
     */
    public function getAllPosts($sortBy = null)
    {
        $query = $this->initrequest["newsdeskposts"] . " WHERE news.id != 0" . $this->orderBy($sortBy);
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @param $userId
     * @param $relatedPosts
     * @param null $startRow
     * @param null $rowsLimit
     * @param null $sorting
     * @return mixed
     */
    public function getHomePosts($userId, $relatedPosts, $startRow = null, $rowsLimit = null, $sorting = null)
    {
        /**
         *
         */
        if ( is_array($relatedPosts) ) {
            $placeholders = str_repeat('?, ', count($relatedPosts) - 1) . '?';

            // Add placeholder for the 'g' flag
            $placeholders .= ', ?';
        } else {
            $placeholders = "?";
        }

        $tmpquery = " WHERE news.author = ? OR news.rss = 1 OR news.related IN ({$placeholders})";
        $sql = $this->initrequest["newsdeskposts"] . $tmpquery . $this->orderBy($sorting) . $this->limit($startRow, $rowsLimit);

        if (is_array($relatedPosts)) {
            array_push($relatedPosts, 'g');
            array_unshift($relatedPosts, $userId);
        } else {
            $relatedPosts = explode(',', $userId . ',' . $relatedPosts);
        }
        $this->db->query($sql);
        $this->db->execute($relatedPosts);
        return $this->db->resultset();
    }

    /**
     * @param $newsId
     * @return mixed
     *
     * Get a list of newsdesk posts by news.id
     */
    public function getNewsPostById($newsId)
    {
        $query = $this->initrequest["newsdeskposts"] . " WHERE news.id = :news_id";
        $this->db->query($query);
        $this->db->bind(':news_id', $newsId);
        return $this->db->single();
    }

    /**
     * @param $postId
     * @return mixed
     *
     * Get a list of newsdesk posts by news.id
     */
    public function getNewsPostByIdIn($postId)
    {
        $postId = explode(',', $postId);
        $placeholders = str_repeat('?, ', count($postId) - 1) . '?';
        $query = $this->initrequest["newsdeskposts"] . " WHERE news.id in ($placeholders)";
        $this->db->query($query);
        $this->db->execute($postId);
        return $this->db->resultset();
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getCommentById($commentId)
    {
        $query = $this->initrequest["newsdeskcomments"] . " WHERE newscom.id = :comment_id";
        $this->db->query($query);
        $this->db->bind(':comment_id', $commentId);
        return $this->db->single();
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getComments($commentId)
    {
        if ( strpos($commentId, ',') ) {
            $ids = explode(',', $commentId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = $this->initrequest["newsdeskcomments"] . " WHERE newscom.id IN ($placeholders) ORDER BY newscom.id";
            $this->db->query($sql);
            $this->db->execute($ids);

            return $this->db->fetchAll();
        } else {
            $query = $this->initrequest["newsdeskcomments"] . " WHERE newscom.id = :comment_id";
            $this->db->query($query);
            $this->db->bind(':comment_id', $commentId);
            return $this->db->single();
        }
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getCommentsByPostId($postId)
    {
        $ids = explode(',', $postId);
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
        $sql = $this->initrequest["newsdeskcomments"] . " WHERE newscom.post_id IN ($placeholders) ORDER BY newscom.id";
        $this->db->query($sql);
        $this->db->execute($ids);
        return $this->db->fetchAll();
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getPostComments($commentId)
    {
        $query = $this->initrequest["newsdeskcomments"] . " WHERE newscom.id = :comment_id";
        $this->db->query($query);
        $this->db->bind(':comment_id', $commentId);
        return $this->db->single();
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function deletePostById($postId)
    {
        $postId = explode(',', $postId);
        $placeholders = str_repeat('?, ', count($postId) - 1) . '?';
        $query = "DELETE FROM {$this->tableCollab["newsdeskposts"]} WHERE id IN ($placeholders)";
        $this->db->query($query);
        return $this->db->execute($postId);
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function deleteCommentById($commentId)
    {
        $commentId = explode(',', $commentId);
        $placeholders = str_repeat('?, ', count($commentId) - 1) . '?';
        $query = "DELETE FROM {$this->tableCollab["newsdeskcomments"]} WHERE id IN ($placeholders)";
        $this->db->query($query);
        return $this->db->execute($commentId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function deleteCommentByPostId($postId)
    {
        $postId = explode(',', $postId);
        $placeholders = str_repeat('?, ', count($postId) - 1) . '?';
        $query = "DELETE FROM {$this->tableCollab["newsdeskcomments"]} WHERE post_id IN ($placeholders)";
        $this->db->query($query);
        return $this->db->execute($postId);    }

    /**
     * @return mixed
     */
    public function getRSSPosts()
    {
        $query = "SELECT id, title, author, content, related, pdate as date FROM {$this->tableCollab["newsdeskposts"]} WHERE rss = 1 ORDER BY pdate DESC LIMIT 0,5";
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @param $userId
     * @param $profile
     * @return mixed
     */
    public function getRelated($userId, $profile)
    {
        $sql = <<<SQL
        
SELECT DISTINCT pro.id as tea_pro_id, pro.name as tea_pro_name, tea.id as tea_id FROM {$this->tableCollab["teams"]} tea, {$this->tableCollab["projects"]} pro 
WHERE pro.id = tea.project
SQL;

        if ($profile == 0) {
            $sql .= " GROUP BY pro.id ";
        } else {
            $sql .= " AND tea.member = :user_id OR pro.id = 0 GROUP BY pro.id";
        }

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultset();
    }

    /**
     * @param $title
     * @param $author
     * @param $related
     * @param $content
     * @param $links
     * @param $rss
     * @return mixed
     */
    public function addPost($title, $author, $related, $content, $links, $rss)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["newsdeskposts"]} 
(title, author, related, content, links, rss, pdate) 
VALUES 
(:title, :author, :related, :content, :links, :rss, NOW())
SQL;
        $this->db->query($sql);
        $this->db->bind(':title', $title);
        $this->db->bind(':author', $author);
        $this->db->bind(':related', $related);
        $this->db->bind(':content', $content);
        $this->db->bind(':links', $links);
        $this->db->bind(':rss', $rss);

        $this->db->execute();
        return $this->db->lastInsertId();
    }

    /**
     * @param $postId
     * @param $title
     * @param $author
     * @param $related
     * @param $content
     * @param $links
     * @param $rss
     * @return mixed
     */
    public function updatePostById($postId, $title, $author, $related, $content, $links, $rss)
    {
        $sql = <<<SQL
UPDATE {$this->tableCollab["newsdeskposts"]} SET title = :title, author = :author, related = :related, content = :content, links = :links, rss = :rss WHERE id = :post_id
SQL;
        $this->db->query($sql);
        $this->db->bind(':post_id', $postId);
        $this->db->bind(':title', $title);
        $this->db->bind(':author', $author);
        $this->db->bind(':related', $related);
        $this->db->bind(':content', $content);
        $this->db->bind(':links', $links);
        $this->db->bind(':rss', $rss);
        return $this->db->execute();
    }

    /**
     * Returns the LIMIT attribute for SQL strings
     * @param $start
     * @param $rowLimit
     * @return string
     */
    private function limit($start, $rowLimit)
    {
        if (!is_null($start) && !is_null($rowLimit)) {
            return " LIMIT {$start},{$rowLimit}";
        }
        return '';
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["news.id", "news.pdate", "news.title", "news.author", "news.related", "news.content", "news.links", "news.rss", "newscom.id", "newscom.post_id", "newscom.name", "newscom.comment"];
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
