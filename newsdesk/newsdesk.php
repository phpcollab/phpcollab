<!doctype html>
<html>
<head>
    <style>
        body {
            background-color: #ffffff;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 14px;
            margin: 0;
        }

        .post {
            border: thin dotted #9C9C9C;
            padding: 5px 5px 5px 10px;
            width: 400px;
        }

        .post-title {
            text-decoration: none;
            font-weight: bold;
            border-bottom: 2px dotted #9c9c9c;
            font-size: 13px;
        }

        .smalltext {
            font-size: 9px;
        }
    </style>
</head>

<body>

<?php

$checkSession = "false";
include '../includes/library.php';


/**
 * Class Foo
 */
class NewsdeskFeed
{
    /**
     * @var \phpCollab\NewsDesk\NewsDesk
     */
    protected $news;
    /**
     * @var \phpCollab\Members\Members
     */
    protected $members;
    /**
     * @var mixed
     */
    protected $strings;
    /**
     * @var int
     */
    protected $postId;
    /**
     * @var mixed
     */
    protected $tableCollab;
    /**
     * @var mixed
     */
    protected $supportEmail;

    /**
     * NewsdeskFeed constructor.
     */
    function __construct()
    {
        $this->news = new \phpCollab\NewsDesk\NewsDesk();
        $this->members = new \phpCollab\Members\Members();
        $this->strings = $GLOBALS["strings"];
        $this->tableCollab = $GLOBALS["tableCollab"];
        $this->supportEmail = $GLOBALS["supportEmail"];
        if ($_GET['id']) {
            $this->postId = (int)$_GET['id'];
        }
    }

    /**
     * @param $postId
     */
    function showSingle($postId)
    {
        $this->postId = $postId;
        if (!$this->postId) {
            echo "No results";
            return;
        }

        $post = $this->news->getPostById($this->postId);

        if (!$post) {
            echo "No results";
            return;
        }

        $comments = $this->showComments($this->postId);


        echo <<<POST
<div class="post">
    <div class="post-title">{$post['news_title']}</div>
    <div class="post-text">{$post['news_content']}</div>
    {$comments}
</div>
POST;
    }

    /**
     *
     */
    function showPosts()
    {
        $newsPosts = $this->news->getAllNewsdeskPosts();

        if (!$newsPosts) {
            echo "Nothing to Display!";
            return false;
        }

        foreach ($newsPosts as $post) {
            $title = htmlentities($post['news_title']);
            $content = nl2br($post['news_content']);

            //begin display
            echo <<<POST
            <div class="post">
                <div class="post-title">({$post['news_id']}) {$title}</div>
                <div class="post-text">{$content}</div>
POST;


            //get number of comments
            $comments = $this->news->getCommentsByPostId($post['news_id']);
            $comments = count($comments);

            //get the author name
            $author = $this->members->getMemberById($post['news_author']);

            if (!$author) {
                $author = "anonymous";
            }

            $author = $author['mem_name'];

            //display number of comments with link
            echo <<<COMMENTS
            <div class="author">posted by {$author} on {$post['news_date']}
                <a href="{$_SERVER['PHP_SELF']}?action=show&id={$post['news_id']}">{$this->strings['comments']}</a>
                {$comments}
            </div>
</div>
COMMENTS;

            echo '</div>';
        }
        return false;
    }

    /**
     * @param $postId
     * @return string
     */
    function showComments($postId)
    {
        $comments = $this->news->getCommentsByPostId($postId);

        if (!$comments) {
            return "No comments";
        }

        $output = '<div class="post-title">Comments:</div>';

        foreach ($comments as $comment) {

            $author = $this->members->getMemberById($comment['newscom_name']);
            if (!$author) {
                $author = 'Anonymous';
            } else {
                $author = $author['mem_name'];
            }

            $newscom_comment = nl2br(stripslashes($comment['newscom_comment']));

            $output .= <<< BLOCK
<br>
<div class="comment-author">by: {$author}</div>
<div class="comment-text">{$newscom_comment}</div>
BLOCK;
        }

        if (isset($_SESSION['idSession'])) {
            //form to enter comments
            $output .= <<< FORM
            <br/>
            <form action="{$_SERVER['PHP_SELF']}?action=addcomment&id={$postId}" method="post">
                <div class="post-title">{$this->strings['add_newsdesk_comment']}</div><br/>
                <div class="smalltext"><input type="hidden" size="50" name="name" value="{$_SESSION['idSession']}" /><stromng>{$_SESSION['nameSession']}</strong></div>
                <textarea cols="40" rows="5" name="comment"></textarea>
                <br /><input type="submit" name="submit" value="{$this->strings['send']}" />
            
            </form>
FORM;
        }

        return $output;
    }

    /**
     *
     */
    public function addComment()
    {

        if ($_POST && $_POST['name'] && $_POST['comment']) {

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

            $commentId = phpCollab\Util::newConnectSql(
                "INSERT INTO {$this->tableCollab["newsdeskcomments"]} (post_id,name,comment) VALUES (:id,:name,:comment)",
                ["id" => $this->postId, "name" => $name, "comment" => $comment]
            );

            if (!$commentId) {
                echo "Error adding comment. Please try again.";
            }
            echo <<<MESSAGE
    <p>Comment entered. Thanks!</p>
    <a href="{$_SERVER['PHP_SELF']}">Back to main page</a>
MESSAGE;

            $post = $this->news->getPostById($this->postId);
            $member = $this->members->getMemberById($name);

            if ($post) {
                $date = $post['news_date'];
                $title = htmlentities($post['news_title']);
                $name = $member['mem_name'];
                $email = $member['mem_name'];

                $headers = "From: $name <$email>";
                $mailSubject = "Web Log Response";
                $mailBody = <<< BODY
This message was generated by phpcollab groupware: \n
----------------------------------------------------\n
Comment Posted From: {$name} \n
Comment Posted For Topic: {$title} \n
Comment Post Timestamp: {$date}
BODY;

                mail($this->supportEmail, $mailSubject, $mailBody, $headers);
            }

        }
        // Add notifications entry
    }
}

$n = new NewsdeskFeed();

//switch between functions according to action passed along with URL
switch ($_GET['action']) {
    case 'show':
        $n->showSingle($_GET['id']);
        break;

    case 'all':
        $n->showPosts();
        break;

    case 'addcomment':
        $n->addComment();
        break;

    default:
        $n->showPosts();
}
?>

</body>
</html>
