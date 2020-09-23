<?php


namespace phpCollab\NewsDesk;

use InvalidArgumentException;
use phpCollab\Database;


/**
 * Class NewsDesk
 * @package phpCollab\NewsDesk
 */
class NewsDesk
{
    protected $newsdesk_gateway;
    protected $db;

    /**
     * NewsDesk constructor.
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->newsdesk_gateway = new NewsDeskGateway($this->db);
    }

    /**
     * @param $userId
     * @param $relatedPosts
     * @return int
     */
    public function getHomePostCount($userId, $relatedPosts)
    {
        $data = $this->newsdesk_gateway->getHomePosts($userId, $relatedPosts);
        return count($data);
    }

    /**
     * @param $userId
     * @param $relatedPosts
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     */
    public function getHomeViewNewsdeskPosts($userId, $relatedPosts, $offset = null, $limit = null, $sorting = null)
    {
        return $this->newsdesk_gateway->getHomePosts($userId, $relatedPosts, $offset, $limit, $sorting);
    }

    /**
     * @param null $sortBy
     * @return mixed
     */
    public function getAllNewsdeskPosts($sortBy = null)
    {
        return $this->newsdesk_gateway->getAllPosts($sortBy);
    }

    /**
     * @param $newsId
     * @return mixed
     */
    public function getPostById($newsId)
    {
        return $this->newsdesk_gateway->getNewsPostById($newsId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getPostByIdIn($postId)
    {
        return $this->newsdesk_gateway->getNewsPostByIdIn($postId);
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getNewsDeskCommentById($commentId)
    {
        return $this->newsdesk_gateway->getCommentById($commentId);
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getComments($commentId)
    {
        return $this->newsdesk_gateway->getComments($commentId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getCommentsByPostId($postId)
    {
        return $this->newsdesk_gateway->getCommentsByPostId($postId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function deleteNewsDeskPost($postId)
    {
        return $this->newsdesk_gateway->deletePostById($postId);
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function deleteNewsDeskComment($commentId)
    {
        return $this->newsdesk_gateway->deleteCommentById($commentId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function deleteCommentByPostId($postId)
    {
        return $this->newsdesk_gateway->deleteCommentByPostId($postId);
    }

    /**
     * @return mixed
     */
    public function getRSSFeed()
    {
        return $this->newsdesk_gateway->getRSSPosts();
    }

    /**
     * @param $userId
     * @param null $profile
     * @return mixed
     */
    public function getNewsdeskRelated($userId, $profile = null)
    {
        return $this->newsdesk_gateway->getRelated($userId, $profile);
    }

    /**
     * @param $postData
     * @return mixed
     */
    public function addPost($postData)
    {
        $title = $postData["title"];
        $author = (int)$postData["author"];
        $related = $postData["related"];
        $content = $postData["content"];
        $links = $postData["links"];
        $rss = !empty($postData["rss"]) ? $postData["rss"] : 0;

        if (empty($title) || empty($author)) {
            throw new InvalidArgumentException('Missing data');
        }

        $timestamp = date('Y-m-d H:i');
        return $this->newsdesk_gateway->addPost($title, $author, $related, $content, $links, $rss, $timestamp);
    }

    /**
     * @param $postData
     * @return mixed
     */
    public function updatePostById($postData)
    {
        $postId = (int)$postData["id"];
        $title = $postData["title"];
        $author = (int)$postData["author"];
        $related = $postData["related"];
        $content = $postData["content"];
        $links = filter_var($postData["links"], FILTER_SANITIZE_URL);
        $rss = !empty($postData["rss"]) ? $postData["rss"] : 0;

        if (empty($postId) || empty($title) || empty($author)) {
            throw new InvalidArgumentException('Invalid data');
        }

        return $this->newsdesk_gateway->updatePostById($postId, $title, $author, $related, $content, $links, $rss);
    }

    /**
     * @param $commentId
     * @param $comment
     * @return mixed
     */
    public function setComment($commentId, $comment)
    {
        if (empty($commentId)) {
            throw new InvalidArgumentException('Comment ID missing or empty.');
        } else {
            if (empty($comment)) {
                throw new InvalidArgumentException('Comment is missing or empty.');
            }
        }

        return $this->newsdesk_gateway->setComment($commentId, $comment);
    }

    /**
     * @param $postId
     * @param $commenterId
     * @param $comment
     * @return mixed
     */
    public function addComment($postId, $commenterId, $comment)
    {
        if (empty($postId)) {
            throw new InvalidArgumentException('Post ID missing or empty.');
        } else {
            if (empty($commenterId)) {
                throw new InvalidArgumentException('Commenter ID missing or empty.');
            } else {
                if (empty($comment)) {
                    throw new InvalidArgumentException('Comment is missing or empty.');
                }
            }
        }

        return $this->newsdesk_gateway->addComment($postId, $commenterId, $comment);
    }

}
