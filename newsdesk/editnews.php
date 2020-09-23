<?php
/*
** Application name: phpCollab
** Last Edit page: 23/03/2004
** Path by root: ../newsdesk/editnews.php
** Authors: Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editnews.php
**
** DESC:
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	fixed multi delete
**	23/03/2004	-	xhtml code
**  23/08/2004  -   fix error "Using $this when not in object context"
**  17/04/2005	-	fix the duplication of the projects name in the prj related select box
**  13/07/2008  -   fix for bug 1802203
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include_once '../includes/library.php';

if ($session->get("profile") != "0" && $session->get("profile") != "1" && $session->get("profile") != "5") {
    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=$id&msg=permissionNews");
}

$news = $container->getNewsdeskLoader();
$projects = $container->getProjectsLoader();

$action = $request->query->get('action');
$id = $request->query->get('id');

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if (empty($request->request->get("title"))) {
                $error = $strings["blank_newsdesk_title"];
            } else {
                $news->updatePostById($request->request->all());
                phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id={$request->request->get("id")}&msg=update");
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

        foreach ($newsDetail as $newsItem) {
            if ($session->get("profile") != "0" && $session->get("id") != $newsItem['news_author']) {
                phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id={$n['id']}&msg=permissionNews");
            }
        }
    } else {
        $newsDetail = $news->getPostById($id);

        if ($session->get("profile") != "0" && $session->get("id") != $newsDetail['news_author']) {
            phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id={$n['id']}&msg=permissionNews");
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
    phpCollab\Util::headerFunction("../newsdesk/listnews.php?msg=blankNews");
}


// htmlArea 3.0 initialization
$headBonus = "	
            <script type='text/javascript'> 
              _editor_url = '../includes/htmlarea/'; 
            </script> 
            <script type='text/javascript' src='../includes/htmlarea/htmlarea.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/lang/{$session->get("langDefault")}.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/dialog.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/popupdiv.js'></script>
            <script type='text/javascript' src='../includes/htmlarea/popupwin.js'></script> 
            <style type='text/css'>@import url(../includes/htmlarea/htmlarea.css)</style>
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
$setTitle .= " : Edit News Item (" . $newsDetail['news_title'] . ")";
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));

$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/viewnews.php?id=" . $newsDetail['news_id'],
    $newsDetail['news_title'], 'in'));
$blockPage->itemBreadcrumbs($strings["edit_newsdesk"]);

$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (isset($error) && $error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

echo "<form method='POST' action='../newsdesk/editnews.php?id={$id}' name='ecDForm'>\n";
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
