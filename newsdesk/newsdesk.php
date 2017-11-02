<!doctype html>
<html>
<head>
    <style>
        body {
            background-color: #ffffff;
            margin: 0;
        }
        .post {
            font-family: Tahoma, Verdana, Helvetica;
            border-width: thin;
            border-style: dotted;
            border-color: #9C9C9C;
            padding: 5px 5px 5px 10px;
            width: 400px;
        }

        .post-title {
            font-family: Tahoma, Verdana, Helvetica;
            text-decoration: none;
            font-weight: bold;
            border-bottom-width: 2px;
            border-bottom-style: dotted;
            border-bottom-color: #9C9C9C;
            font-size: 13px;
        }

        .post-text {
            font-family: Tahoma, Verdana, Helvetica;
            font-size: 11px;
        }

        .author {
            font-family: Tahoma, Verdana, Helvetica;
            font-size: 11px;
        }

        .smalltext {
            font-family: Tahoma, Verdana, Helvetica;
            font-size: 9px;
        }

        .comment-author {
            font-family: Tahoma, Verdana, Helvetica;
            font-size: 11px;
        }

        .comment-text {
            font-family: Tahoma, Verdana, Helvetica;
            font-size: 11px;
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
class NewsdeskFeed {
    protected $news;
    protected $members;
    protected $strings;
    protected $postId;
    protected $tableCollab;
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
     *
     */
    function showSingle() {
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
    function showPosts() {
        $newsPosts = $this->news->getAllNewsdeskPosts();
        xdebug_var_dump($newsPosts);
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
    }

    public function addComment() {

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


function showPosts()
{
    global $connection, $newsdesklimit;

    if (!isset($langDefault) || ($langDefault == '')) {
        $langDefault = 'en';
    }
    include '../languages/lang_' . $langDefault . '.php';

    $page = $_GET[page];
    $query_count = "SELECT title FROM " . $tableCollab["newsdeskposts"];
    $result_count = @mysql_query($query_count);
    $totalrows = mysql_num_rows($result_count);

    if (!$page) {
        $page = 1;
    }

    $limitvalue = $page * $newsdesklimit - ($newsdesklimit);
    $query = "SELECT id,title,author,content, DATE_FORMAT(pdate, '%Y-%m-%d') as date FROM " . $tableCollab["newsdeskposts"] . " ORDER BY pdate DESC LIMIT $limitvalue, $newsdesklimit";
    $result = @mysql_query($query) or die("Error: " . mysql_error());

    if (mysql_num_rows($result) == 0) {
        echo "Nothing to Display!";
    }

    //loop to display all items
    while ($row = mysql_fetch_assoc($result)) {
        //define variables
        $date = $row['date'];
        $title = htmlentities($row['title']);
        $author = $row['author'];
        $content = nl2br($row['content']);

        //begin display
        echo "<div class=\"post\">\n";

        echo "<div class=\"post-title\">\n";
        echo "$title\n";
        echo "</div>\n";
        echo "<div class=\"post-text\">\n";
        echo "$content\n";
        echo "</div>\n";


        //get number of comments
        $comment_query = "SELECT count(*) FROM " . $tableCollab["newsdeskcomments"] . " WHERE post_id={$row['id']}";
        $comment_result = mysql_query($comment_query);
        $comment_row = mysql_fetch_row($comment_result);

        //get the author name
        $query_author = 'SELECT name FROM ' . $tableCollab["members"] . ' WHERE id = "' . $row['author'] . '"';
        $result_author = @mysql_query($query_author) or die("Error: " . mysql_error());
        if (mysql_num_rows($result_author) == 0) {
            $author = "anonymous";
        }
        while ($row_a = mysql_fetch_assoc($result_author)) {
            $author = $row_a['name'];
        }

        //display number of comments with link
        echo "<div class=\"author\">\n";
        echo "posted by $author on $date &nbsp;\n";
        echo "<a href=\"{$_SERVER['PHP_SELF']}?action=show&id={$row['id']}\">" . $strings['comments'] . "</a>\n";
        echo "($comment_row[0])\n";
        echo "</div>\n";

        //end
        echo "</div>\n";
    }

    if ($page > 1) {
        $pageprev = $page - 1;
        echo "<a href=\"{$_SERVER['PHP_SELF']}?page=$pageprev\" class=\"smalltext\">PREV</a>&nbsp;";
        echo "\n";
    } else {
        echo "<span class=\"smalltext\">PREV</span>&nbsp;";
        echo "\n";
    }

    $numofpages = $totalrows / $newsdesklimit;

    for ($i = 1; $i <= $numofpages; $i++) {
        if ($i == $page) {
            echo "<span class=\"smalltext\">$i</span>";
            echo "&nbsp;";
            echo "\n";
        } else {
            echo "<a href=\"{$_SERVER['PHP_SELF']}?page=$i\" class=\"smalltext\">$i</a>&nbsp;";
            echo "\n";
        }
    }

    if (($totalrows % $newsdesklimit) != 0) {
        if ($i == $page) {
            echo "<span class=\"smalltext\">$i</span>";
            echo "&nbsp;";
            echo "\n";
        } else {
            echo "<a href=\"{$_SERVER['PHP_SELF']}?page=$i\" class=\"smalltext\">$i</a>&nbsp;";
            echo "\n";
        }
    }

    if (($totalrows - ($newsdesklimit * $page)) > 0) {
        if (!$page) {
            $page = 1;
        }
        $pagenext = $page + 1;
        echo "<a href=\"{$_SERVER['PHP_SELF']}?page=$pagenext\" class=\"smalltext\">NEXT</a>";
        echo "\n";
    } else {
        echo "&nbsp;<span class=\"smalltext\">NEXT</span>";
        echo "\n";
    }
}

/*
function showComments($id)
{
    //variables
    global $connection;

    if (!isset($langDefault) || ($langDefault == '')) {
        $langDefault = 'en';
    }
    include '../languages/lang_' . $langDefault . '.php';

    //query string
    $query = "SELECT * FROM " . $tableCollab["newsdeskcomments"] . " WHERE post_id=$id";

    //store query result in a variable
    $result = mysql_query($query);

    //begin display
    echo "<div class=\"post-title\">";
    echo "Comments:\n";
    echo "</div>\n";

    //loop to display comments
    while ($row = mysql_fetch_assoc($result)) {
        //define variables

        //get the author name
        $query_author = 'SELECT name FROM ' . $tableCollab["members"] . ' WHERE id = "' . $row['name'] . '"';
        $result_author = @mysql_query($query_author) or die("Error: " . mysql_error());
        if (mysql_num_rows($result_author) == 0) {
            $author = "anonymous";
        }
        while ($row_a = mysql_fetch_assoc($result_author)) {
            $name = $row_a['name'];
        }


        echo "<br/><div class=\"comment-author\">";
        echo "\n";
        echo "by: $name";
        echo "\n";
        echo "</div>";
        echo "\n";

        $comment = strip_tags($row['comment'], '<a><b><i><u>');
        $comment = nl2br($comment);
        echo "<div class=\"comment-text\">";
        echo "\n";
        echo "$comment";
        echo "\n";
        echo "</div>";
        echo "\n";
    }

    if (isset($_SESSION['idSession'])) {
        //form to enter comments
        echo "<br/><form action=\"{$_SERVER['PHP_SELF']}?action=addcomment&id=$id\" method=\"post\">";
        echo "\n";
        //echo "<p>";
        echo "\n";
        echo "<div class='post-title'>" . $strings['add_newsdesk_comment'] . "</div><br/>";
        echo "\n";
        echo "<div class='smalltext'><input type='hidden' size=\"50\" name=\"name\" value='" . $_SESSION['idSession'] . "' /><b>" . $_SESSION['nameSession'] . "</b></div>";
        echo "\n";
        echo "<textarea cols=\"40\" rows=\"5\" name='comment'>" . $strings['comment'] . "</textarea>";
        echo "\n";
        echo "<br /><input type=\"submit\" name=\"submit\" value='" . $strings['send'] . "' />";
        echo "\n";
        //echo "</p>";
        echo "\n";
        echo "</form>";
    }
}
*/

/*
function addComment($id)
{
    global $connection;

    //query string
//    $query = "INSERT INTO " . $tableCollab["newsdeskcomments"] . " VALUES('',$id,'{$_POST['name']}', '{$_POST['comment']}')";
//    mysql_query($query);

    //display friendly message
//    echo "Comment entered. Thanks!<br />";
//    echo "\n";
//    echo "<a href=\"{$_SERVER['PHP_SELF']}\">Back to main page</a>";
//    echo "\n";

    //NOTIFICATION OF COMMENTS POSTED

    //query string
//    $firstquery = "SELECT id, post_id, name, comment FROM " . $tableCollab["newsdeskcomments"] . " ORDER BY id DESC LIMIT 1";
//    $secondquery = "SELECT id,title FROM " . $tableCollab["newsdeskposts"] . " WHERE id= '$id'";

    //store query result in a variable
//    $firstresult = mysql_query($firstquery);
//    $secondresult = mysql_query($secondquery);

    //in case of error display friendly message
//    if ((mysql_num_rows($firstresult) == 0) || (mysql_num_rows($secondresult) == 0)) {
//        echo "Bad news id";
//        echo "\n";
//        return;
//    }

//    $firstrow = mysql_fetch_assoc($firstresult);
//    $secondrow = mysql_fetch_assoc($secondresult);

    //define variables
//    $date = $firstrow['date'];
//    $title = htmlentities($secondrow['title']);
//    $name = $firstrow['name'];
//
//    $headers = "From: $name <$name>";
//    $mailSubject = "Web Log Response";
//    $mailBody = <<< BODY
//This message was generated by phpcollab groupware: \n
//----------------------------------------------------\n
//Comment Posted From: {$name} \n
//Comment Posted For Topic: {$title} \n
//Comment Post Timestamp: {$date}
//BODY;
//
//    mail($supportEmail, $mailSubject, $mailBody, $headers);
}
*/

//switch between functions according to action passed along with URL

switch ($_GET['action']) {
    case 'show':
        $n->showSingle($_GET['id']);
        break;

    case 'all':
//        showPosts(1);
        $n->showPosts();
        break;

    case 'addcomment':
//        addComment($_GET['id']);
        $n->addComment();
        break;

    default:
        $n->showPosts();
//        showPosts();
}

?>

</body>
</html>