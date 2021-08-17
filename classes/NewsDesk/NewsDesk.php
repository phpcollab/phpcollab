<?php


namespace phpCollab\NewsDesk;

use InvalidArgumentException;
use Laminas\Escaper\Escaper;
use phpCollab\Database;


/**
 * Class NewsDesk
 * @package phpCollab\NewsDesk
 */
class NewsDesk
{
    protected $newsdesk_gateway;
    protected $db;
    protected $escaper;

    /**
     * NewsDesk constructor.
     * @param Database $database
     * @param Escaper $escaper
     */
    public function __construct(Database $database, Escaper $escaper)
    {
        $this->db = $database;
        $this->newsdesk_gateway = new NewsDeskGateway($this->db);
        $this->escaper = $escaper;
    }

    /**
     * @param array|object $post
     * @return array
     */
    protected function escapeNewsdeskPostFields($post)
    {
        /*
         * Fields to be escaped, since they store user supplied data
         * - title
         * - content
         * - links
         */
        if (is_object($post)) {
            if (!empty($post->get("title"))) {
                $post->set('title', $this->escaper->escapeHtml($post->get("title")));
            }
            if (!empty($post->get("content"))) {
                $post->set('content', $this->escaper->escapeHtml($post->get("content")));
            }
            if (!empty($post->get("links"))) {
                $post->set('links', $this->escaper->escapeHtml($post->get("links")));
            }
        }

        if (is_array($post)) {
            if (!empty($post["news_title"])) {
                $post["news_title"] = $this->escaper->escapeHtml($post["news_title"]);
            }
            if (!empty($post["news_content"])) {
                $post["news_content"] = $this->escaper->escapeHtml($post["news_content"]);
            }
            if (!empty($post["news_links"])) {
                $post["news_links"] = $this->escaper->escapeHtml($post["news_links"]);
            }
        }
        return $post;
    }

    /**
     * @param $comment
     * @return array|mixed
     */
    protected function escapeComment($comment)
    {
        if (is_object($comment)) {
            if (!empty($comment->get("newscom_comment"))) {
                $comment->set('newscom_comment', $this->escaper->escapeHtml($comment->get("newscom_comment")));
            }
        }

        if (is_array($comment)) {
            if (!empty($comment["newscom_comment"])) {
                $comment["newscom_comment"] = $this->escaper->escapeHtml($comment["newscom_comment"]);
            }
        }
        return $comment;

    }

    /**
     * @param $userId
     * @param $relatedPosts
     * @return int
     */
    public function getHomePostCount($userId, $relatedPosts): int
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
        $posts = $this->newsdesk_gateway->getHomePosts($userId, $relatedPosts, $offset, $limit, $sorting);

        if ($posts) {
            foreach ($posts as $key => $value) {
                $posts[$key] = $this->escapeNewsdeskPostFields($value);
            }
        }

        return $posts;
    }

    /**
     * @param null $sortBy
     * @return mixed
     */
    public function getAllNewsdeskPosts($sortBy = null)
    {
        $newsPosts = $this->newsdesk_gateway->getAllPosts($sortBy);
        if ($newsPosts) {
            foreach ($newsPosts as $key => $value) {
                $newsPosts[$key] = $this->escapeNewsdeskPostFields($value);
            }
        }
        return $newsPosts;
    }

    /**
     * @param $newsId
     * @return mixed
     */
    public function getPostById($newsId)
    {
        // Gets an array of the post data
        $newsPost = $this->newsdesk_gateway->getNewsPostById($newsId);

        if ($newsPost) {
            $newsPost = $this->escapeNewsdeskPostFields($newsPost);
        }
        return $newsPost;
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getPostByIdIn($postId)
    {
        $newsPosts = $this->newsdesk_gateway->getNewsPostByIdIn($postId);
        if ($newsPosts) {
            foreach ($newsPosts as $key => $value) {
                $newsPosts[$key] = $this->escapeNewsdeskPostFields($value);
            }
        }
        return $newsPosts;
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getNewsDeskCommentById($commentId)
    {
        $comment = $this->newsdesk_gateway->getCommentById($commentId);
        if ($comment) {
            $comment = $this->escapeComment($comment);
        }
        return $comment;
    }

    /**
     * @param $commentId
     * @return mixed
     */
    public function getComments($commentId)
    {
        $comments = $this->newsdesk_gateway->getComments($commentId);
        if ($comments) {
            foreach ($comments as $key => $value) {
                $comments[$key] = $this->escapeComment($value);
            }
        }
        return $comments;
    }

    /**
     * @param $postId
     * @return mixed
     */
    public function getCommentsByPostId($postId)
    {
        $comments = $this->newsdesk_gateway->getCommentsByPostId($postId);

        if ($comments) {
            foreach ($comments as $key => $value) {
                $comments[$key] = $this->escapeComment($value);
            }
        }
        return $comments;
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
        $title = trim($postData["title"]);
        $author = (int)$postData["author"];
        $related = $postData["related"];
        $content = trim($postData["content"]);
        $links = trim($postData["links"]);
        $rss = !empty($postData["rss"]) ? $postData["rss"] : 0;

        if (!filter_var($postData["author"], FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Author is not valid.');
        }

        if (empty($title)) {
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
        $comment = trim($comment);

        if (!filter_var($postId, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Post ID is not valid.');
        }

        if (!filter_var($commenterId, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Commenter ID is not valid.');
        }

        if (empty($comment)) {
            throw new InvalidArgumentException('Comment is missing or empty.');
        }

        return $this->newsdesk_gateway->addComment($postId, $commenterId, $comment);
    }

}
