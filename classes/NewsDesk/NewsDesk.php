<?php


namespace phpCollab\NewsDesk;

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
     */
    public function __construct()
    {
        $this->db = new Database();
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
        return $this->newsdesk_gateway->addPost($title, $author, $related, $content, $links, $rss);
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
        return $this->newsdesk_gateway->updatePostById($postId, $title, $author, $related, $content, $links, $rss);
    }

}
