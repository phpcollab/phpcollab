<?php
/*
** Application name: phpCollab
** Path by root: ../newsdesk/editnews.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: addnews.php
**
** =============================================================================
*/

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

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if (empty($request->request->get("title"))) {
                $error = $strings["blank_newsdesk_title"];
            } else {
                $num = $news->addPost($request->request->all());

                if (!empty($num)) {
                    $session->getFlashBag()->add(
                        'message',
                        $strings["newsdesk_item_add"]
                    );
                    phpCollab\Util::headerFunction("../newsdesk/viewnews.php?id=" . $num);
                }
                $session->getFlashBag()->add(
                    'message',
                    $strings["newsdesk_item_add_error"]
                );

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
        $session->getFlashBag()->add(
            'message',
            $strings["errorpermission_newsdesk"]
        );
    }
}

$headBonus = <<<HEADBONUS
<script type='text/javascript'> 
  _editor_url = '../includes/htmlarea/'; 
</script> 

<script type='text/javascript' src='../includes/htmlarea/htmlarea.js'></script>
<script type='text/javascript' src='../includes/htmlarea/lang/{$session->get("language")}.js'></script>
<script type='text/javascript' src='../includes/htmlarea/dialog.js'></script>
<script type='text/javascript' src='../includes/htmlarea/popupdiv.js'></script>
<script type='text/javascript' src='../includes/htmlarea/popupwin.js'></script> 

<link rel="stylesheet" href="../includes/htmlarea/htmlarea.css">
<script type='text/javascript'>

    HTMLArea.loadPlugin('TableOperations'); 
    
    let editor = null;
    
    function initEditor() {
      editor = new HTMLArea('content');
      editor.generate();
    }
</script>
HEADBONUS;

$bodyCommand = "onload='initEditor();'";

$setTitle .= " : " . $strings["add_newsdesk"];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();

$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../newsdesk/listnews.php?", $strings["newsdesk"], 'in'));
$blockPage->itemBreadcrumbs($strings["add_newsdesk"]);
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

$block1->heading($strings["add_newsdesk"]);
echo <<<FROM
<form method="POST" action="../newsdesk/addnews.php" name="addForm">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
FROM;

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["author"],
    '<input type="hidden" name="author" value="' . $session->get("id") . '"><b>' . $session->get("name") . '</b>');

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

$block1->contentRow($strings["newsdesk_related_links"] . $block1->printHelp("newsdesk_links"),
    '<input type="text" name="links" value="' . $links . '" style="width: 300px;">');

if ($rss == '1') {
    $checkedRSS = 'checked';
} else {
    $checkedRSS = '';
}

$block1->contentRow($strings["newsdesk_rss"],
    '<input size="32" value="1" name="rss" type="checkbox" ' . $checkedRSS . '>');

$block1->contentRow('',
    "<input type='submit' name='submit' value='" . $strings["save"] . "'> <input type='button' name='cancel' value='" . $strings["cancel"] . "' onClick='history.back();'>");

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
