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
     * @return mixed
     */
    public function getRSSPosts()
    {
        $query = "SELECT id, title, author, content, related, pdate as date FROM {$this->tableCollab["newsdeskposts"]} WHERE rss = 1 ORDER BY pdate DESC LIMIT 0,5";
        $this->db->query($query);
        return $this->db->resultset();
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
