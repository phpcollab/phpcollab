<?php
/**
 * RSS 2.0 generator,
 * original version by garym@teledyn.com
 * @author Francesco Fullone <fullone@interfree.it>
 * @date 21/06/2003
 * @version 1.1
 */
header("Content-type: text/xml");
include '../includes/settings.php';
if (!isset($langDefault) || ($langDefault == '')) {
    $langDefault = 'en';
}

$connection = @mysql_connect(MYSERVER, MYLOGIN, MYPASSWORD) or die($strings["error_server"]);
@mysql_select_db(MYDATABASE, $connection) or die($strings["error_database"]);

function createRSS()
{
    global $connection, $newsdesklimit;

    $query = "SELECT id,title,author,content,related, DATE_FORMAT(pdate, '%Y-%m-%d') as date FROM " . $tableCollab["newsdeskposts"] . " WHERE rss = '1' ORDER BY pdate DESC LIMIT 0,5";
    $result = @mysql_query($query) or die("Error: " . mysql_error());

    $RSS = "";

    //loop to display all items
    while ($row = mysql_fetch_assoc($result)) {
        //define variables
        $date = $row['date'];
        $title = htmlentities($row['title']);
        $content = nl2br($row['content']);
        $id = $row['id'];


        //take the author name
        $query_author = 'SELECT name FROM ' . $tableCollab["members"] . ' WHERE id = "' . $row['author'] . '"';
        $result_author = @mysql_query($query_author) or die("Error: " . mysql_error());
        if (mysql_num_rows($result_author) == 0) {
            die('Author not exist!');
        }
        while ($row_a = mysql_fetch_assoc($result_author)) {
            $author = $row_a['name'];
        }

        // take the project related
        if ($row['related'] != 'g') {
            $query_prj = 'SELECT name FROM ' . $tableCollab["projects"] . ' WHERE id = "' . $row['related'] . '"';
            $result_prj = @mysql_query($query_prj) or die("Error: " . mysql_error());

            if (mysql_num_rows($result_prj) == 0) {
                die('Project doesn\'t exist!');
            }
            while ($row_p = mysql_fetch_assoc($result_prj)) {
                $article_related = $row_p['name'];
            }

        } else {
            $article_related = $strings["newsdesk_related_generic"];
        }

        //begin display

        $RSS['items'] .= "
					<item rdf:about='$root/newsdesk/newsdesk.php?action=show&id=$id'>
						<title>$title</title>
						<link>$root/newsdesk/newsdesk.php?action=show&id=$id</link>
						<dc:date>$date</dc:date>
						<dc:creator>$author</dc:creator>
						<dc:subject>$article_related</dc:subject>
						<description>" . utf8_encode(strip_tags($content)) . "</description>
						<content:encoded><![CDATA[" . utf8_encode(strip_tags($content)) . "]]></content:encoded>
					</item>
					";
        //end
    }

    return $RSS;
}


?>
<?php echo "<?xml version=\"1.0\"?" . ">\n"; ?>
<!-- generator="phpcollab/ phpCollab <?php echo $version; ?>" -->
<rss version="2.0"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:admin="http://webns.net/mvcb/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <?php $_thisRSS = createRSS(); ?>


    <channel>
        <title><?php echo $setTitle; ?></title>
        <link><?php echo $root; ?></link>
        <description><?php echo $setDescription; ?></description>
        <dc:language><?php echo $langDefault; ?></dc:language>
        <dc:creator><?php echo $supportEmail; ?></dc:creator>
        <dc:rights>Copyright 2003</dc:rights>
        <dc:date><?php echo gmdate('Y-m-d\TH:i:s'); ?>+00:00</dc:date>
        <admin:generatorAgent rdf:resource="http://www.phpcollab.com/?v=<?php echo $version ?>"/>
        <admin:errorReportsTo rdf:resource="mailto:<?php echo $supportEmail ?>"/>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

        <?php echo $_thisRSS['items']; ?>

    </channel>
</rss>