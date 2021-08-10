<?php
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
    $session->getFlashBag()->add(
        'message',
        $strings["errorpermission_newsdesk"]
    );

    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $id);
}

try {
    $news = $container->getNewsdeskLoader();
    $projects = $container->getProjectsLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$action = $request->query->get('action');
$id = $request->query->get('id');

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if (empty($request->request->get("title"))) {
                $error = $strings["blank_newsdesk_title"];
            } else {
                if (empty($error)) {
                    $news->updatePostById($request->request->all());

                    $session->getFlashBag()->add(
                        'message',
                        $strings["newsdesk_item_updated"]
                    );

                    phpCollab\Util::headerFunction( "../newsdesk/viewnews.php?id=" . $request->request->get("id") );
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Newsdesk: Edit News' => $request->query->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

//case edit news
if ($id != "") {
    $id = str_replace("**", ",", $id);

    if (strpos($id, ',')) {
        $newsDetail = $news->getPostByIdIn($id);

        // Check to see if eligible to edit item
        foreach ($newsDetail as $newsItem) {
            if ($session->get("profile") != "0" && $session->get("id") != $newsItem['news_author']) {

                $session->getFlashBag()->add(
                    'message',
                    $strings["errorpermission_newsdesk"]
                );


                phpCollab\Util::headerFunction( "../newsdesk/viewnews.php?id=" . $id );
            }
        }
    } else {
        $newsDetail = $news->getPostById($id);

        // Check to see if eligible to edit item
        if ($session->get("profile") != "0" && $session->get("id") != $newsDetail['news_author']) {

            $session->getFlashBag()->add(
                'message',
                $strings["errorpermission_newsdesk"]

            );

            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $newsDetail['news_id']);
        }
    }
    //set value in form
    $title = $newsDetail['news_title'];
    $content = $newsDetail['news_content'];
    $author = $newsDetail['news_author'];
    $links = $newsDetail['news_links'];
    $rss = $newsDetail['news_rss'];
}

if (empty($id) || !$newsDetail) {

    $session->getFlashBag()->add(
        'message',
        $strings["newsdesk_item_blank"]
    );

    phpCollab\Util::headerFunction("../newsdesk/listnews.php");
}


// htmlArea 3.0 initialization
$headBonus = "	
            <script type='text/javascript'> 
              _editor_url = '../includes/htmlarea/'; 
            </script> 
            <script type='text/javascript' src='../includes/htmlarea/htmlarea.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/lang/{$session->get("language")}.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/dialog.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/popupdiv.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/popupwin.js'></script> 
            <style>@import url(../includes/htmlarea/htmlarea.css)</style>
            <script type='text/javascript'>
                HTMLArea.loadPlugin('TableOperations'); 
                
                let editor = null;
                
                function initEditor() {
                  editor = new HTMLArea('content');
                  editor.registerPlugin('TableOperations');
                  editor.generate();
                }
            </script>
            ";
$bodyCommand = "onload='initEditor();'";

//** Title stuff here.. **

$setTitle .= sprintf($strings["newsdesk_item_edit"], $newsDetail["news_title"]);

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));

$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $newsDetail['news_id'],
    $newsDetail['news_title'], 'in'));
$blockPage->itemBreadcrumbs($strings["edit_newsdesk"]);

$blockPage->closeBreadcrumbs();


if ($session->getFlashBag()->has('message')) {
    $blockPage->messageBox( $session->getFlashBag()->get('message')[0] );
} else if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

echo '<form method="post" action="../newsdesk/editnews.php?id=' . $id . '" name="ecDForm">';
$block1->heading($strings["edit_newsdesk"] . " : " . $newsDetail['news_title']);

echo <<<CSRF
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
    <input type="hidden" name="author" value="{$newsDetail["news_author"]}">
    <input type="hidden" name="id" value="{$newsDetail["news_id"]}">
CSRF;

$block1->openContent();
$block1->contentTitle($strings["details"]);

$newsAuthor = $members->getMemberById($newsDetail['news_author']);
$block1->contentRow($strings["author"],
    "<strong>" . $newsAuthor["mem_name"] . "</strong>");

    $block1->contentRow($strings["title"], "<input type='text' name='title' value='$title' style='width: 300px;'>");

    $listProjects = $news->getNewsdeskRelated($session->get("id"), $session->get("profile"));
    $option = '<option value="g">' . $strings['newsdesk_related_generic'] . '</option>\n';

    if ($listProjects) {
        $option .= '<optgroup label="Projects">';
        foreach ($listProjects as $listProject) {
            if (isset($newsDetail) && $newsDetail['news_related'] == $listProject['tea_pro_id']) {
                $selected = 'selected';
            } else {
                $selected = '';
            }
            $option .= '<option value="' . $listProject['tea_pro_id'] . '" ' . $selected . ' >' . $listProject['tea_pro_name'] . '</option>\n';
        }

        $option .= '</optgroup>';
    }

$block1->contentRow($strings["newsdesk_related"], "<select name='related' style='width: 300px;'>$option</select>");

$block1->contentRow($strings["comments"],
    '<textarea rows="30" name="content" id="content" style="width: 400px;">' . $content . '</textarea>');

    // 14/06/2003 related links & rss enabled by fullo
$block1->contentRow($strings["newsdesk_related_links"] . $block1->printHelp("newsdesk_links"),
    "<input type='text' name='links' value='$links' style='width: 300px;'>");

if ($rss == '1') {
    $checkedRSS = 'checked';
} else {
    $checkedRSS = '';
}

$block1->contentRow($strings["newsdesk_rss"], "<input size='32' value='1' name='rss' type='checkbox' $checkedRSS>");


$block1->contentRow('',
    "<input type='submit' name='submit' value='" . $strings["save"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'>");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
