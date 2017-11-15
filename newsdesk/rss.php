<?php
/**
 * RSS 2.0 generator,
 * original version by garym@teledyn.com
 * @author Francesco Fullone <fullone@interfree.it>
 * @date 21/06/2003
 * @version 1.1
 */
header("Content-type: text/xml");

require_once '../includes/library.php';


if (!isset($langDefault) || ($langDefault == '')) {
    $langDefault = 'en';
}

/**
 * @return array
 * @throws Exception
 */
function createRSS()
{
    $strings = $GLOBALS["strings"];
    $root = $GLOBALS["root"];

    $newsdeskPosts = new \phpCollab\NewsDesk\NewsDesk();
    $members = new \phpCollab\Members\Members();
    $projects = new \phpCollab\Projects\Projects();

    $result = $newsdeskPosts->getRSSFeed();

    $RSS = [];

    foreach ($result as $row) {
        //define variables
        $date = date_format($row['date'], 'Y-m-d');
        $title = htmlentities($row['title']);
        $content = nl2br($row['content']);
        $id = $row['id'];

        //take the author name
        $query_author = $members->getMemberById($row["author"]);
        $result_author = $query_author["mem_name"];

        if (!$result_author) {
            throw new Exception("Author does not exist");
        }

        $author = $result_author;

        // take the project related
        if ($row['related'] != 'g') {
            $result_prj = $projects->getProjectById($row["related"]);
            if (!$result_prj) {
                throw new Exception('Project does not exist');
            }

            $article_related = $result_prj["pro_name"];
        } else {
            $article_related = $strings["newsdesk_related_generic"];
        }

        //begin display

        $RSS['items'] .= "
					<item rdf:about='$root/newsdesk/newsdesk.php?action=show&amp;id=$id'>
						<title>$title</title>
						<link>$root/newsdesk/newsdesk.php?action=show&amp;id=$id</link>
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
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
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
