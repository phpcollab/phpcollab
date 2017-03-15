<?php
/**
 * RDF generator,
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

try {
    $connection = mysqli_connect(MYSERVER, MYLOGIN, MYPASSWORD);
}
catch (Exception $e) {
    echo self::$strings["error_server"];
    exit;
}

try {
    $selectedDb = mysqli_select_db($connection, MYDATABASE);
}
catch (Exception $e) {
    echo self::$strings["error_database"];
    exit;
}

function createRSS()
{
    global $connection, $newsdesklimit, $strings, $tableCollab, $root;

    if (!isset($langDefault) || ($langDefault == '')) {
        $langDefault = 'en';
    }
    include '../languages/lang_' . $langDefault . '.php';

    $query = "SELECT id,title,author,content,related, DATE_FORMAT(pdate, '%Y-%m-%d') as date FROM {$tableCollab["newsdeskposts"]} WHERE rss = '1' ORDER BY pdate DESC LIMIT 0,5";
    try {
        $result = mysqli_query($connection, $query);
    }
    catch (Exception $e) {
        echo "Error: " . mysqli_error($connection);
        exit;
    }

    //loop to display all items
    $row = mysqli_fetch_assoc($result);

    while ($row) {
        //define variables
        $date = $row['date'];
        $title = htmlentities($row['title']);
        $content = nl2br($row['content']);
        $id = $row['id'];


        //take the author name
        $query_author = "SELECT name FROM {$tableCollab["members"]} WHERE id = '{$row["author"]}'";

        try {
            $result_author = mysqli_query($connection, $query_author);
        }
        catch (Exception $e) {
            echo "Error: " . mysqli_error($connection);
            exit;
        }

        if (mysqli_num_rows($result_author) == 0) {
            $author = "anonymous";
        }
        while ($row_a = mysqli_fetch_assoc($result_author)) {
            $author = $row_a['name'];
        }

        // take the project related
        if ($row['related'] != 'g') {
            $query_prj = "SELECT name FROM {$tableCollab["projects"]} WHERE id = '{$row["related"]}'";

            try {
                $result_prj = mysqli_query($connection, $query_prj);
            }
            catch (Exception $e) {
                echo "Error: " . mysqli_error($connection);
                exit;
            }

            if (mysql_num_rows($result_prj) == 0) {
                $article_related = $strings["newsdesk_related_generic"];
            }
            while ($row_p = mysqli_fetch_assoc($result_prj)) {
                $article_related = $row_p['name'];
            }

        } else {
            $article_related = $strings["newsdesk_related_generic"];
        }

        //begin display
        $RSS = "";
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

        $RSS['channel'] .= "<rdf:li rdf:resource='$root/newsdesk/newsdesk.php?action=show&id=$id'/>";

    }

    @mysqli_free_result($result);
    @mysqli_free_result($result_author);
    @mysqli_free_result($result_prj);
    @mysqli_close($connection);

    return $RSS;
}


?>
<?php echo "<?xml version=\"1.0\"?" . ">\n"; ?>
<!-- generator="phpcollab/ phpCollab <?php echo $version; ?>" -->
<rdf:RDF
    xmlns="http://purl.org/rss/1.0/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
>

    <?php $_thisRSS = createRSS(); ?>

    <channel rdf:about="<?php $root ?>">
        <title><?php echo $setTitle; ?></title>
        <link><?php echo $root; ?></link>
        <description><?php echo $setDescription; ?></description>
        <dc:language><?php echo $langDefault; ?></dc:language>
        <dc:creator><?php echo $supportEmail; ?></dc:creator>
        <dc:date><?php echo gmdate('Y-m-d\TH:i:s'); ?></dc:date>
        <admin:generatorAgent rdf:resource="http://www.phpcollab.com/?v=<?php echo $version ?>"/>
        <admin:errorReportsTo rdf:resource="mailto:<?php echo $supportEmail ?>"/>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>

        <items>
            <rdf:Seq>
                <?php echo $_thisRSS['channel']; ?>
            </rdf:Seq>
        </items>
    </channel>

    <?php echo $_thisRSS['items']; ?>

</rdf:RDF>