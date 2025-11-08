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
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
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


// Pell editor initialization
$headBonus = <<<HEADBONUS
<link rel="stylesheet" href="../javascript/pell/pell.css">
<script type='text/javascript' src='../javascript/pell/pell.min.js'></script>
HEADBONUS;

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

// Pell editor with internationalization support
$contentJson = json_encode($content ?? '');

// Prepare translated strings for JavaScript (properly escaped)
$editorStrings = [
    'bold' => htmlspecialchars($strings["editor_bold"], ENT_QUOTES, 'UTF-8'),
    'italic' => htmlspecialchars($strings["editor_italic"], ENT_QUOTES, 'UTF-8'),
    'underline' => htmlspecialchars($strings["editor_underline"], ENT_QUOTES, 'UTF-8'),
    'strikethrough' => htmlspecialchars($strings["editor_strikethrough"], ENT_QUOTES, 'UTF-8'),
    'heading1' => htmlspecialchars($strings["editor_heading1"], ENT_QUOTES, 'UTF-8'),
    'heading2' => htmlspecialchars($strings["editor_heading2"], ENT_QUOTES, 'UTF-8'),
    'paragraph' => htmlspecialchars($strings["editor_paragraph"], ENT_QUOTES, 'UTF-8'),
    'quote' => htmlspecialchars($strings["editor_quote"], ENT_QUOTES, 'UTF-8'),
    'olist' => htmlspecialchars($strings["editor_olist"], ENT_QUOTES, 'UTF-8'),
    'ulist' => htmlspecialchars($strings["editor_ulist"], ENT_QUOTES, 'UTF-8'),
    'code' => htmlspecialchars($strings["editor_code"], ENT_QUOTES, 'UTF-8'),
    'line' => htmlspecialchars($strings["editor_line"], ENT_QUOTES, 'UTF-8'),
    'link' => htmlspecialchars($strings["editor_link"], ENT_QUOTES, 'UTF-8'),
    'link_prompt' => htmlspecialchars($strings["editor_link_prompt"], ENT_QUOTES, 'UTF-8')
];

$editorHtml = <<<EDITOR
<div id="pell-editor" class="pell"></div>
<input type="hidden" name="content" id="content-input">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = pell.init({
        element: document.getElementById('pell-editor'),
        onChange: function(html) {
            document.getElementById('content-input').value = html;
        },
        defaultParagraphSeparator: 'p',
        styleWithCSS: false,
        actions: [
            {name: 'bold', icon: '<b>B</b>', title: '{$editorStrings['bold']}', result: () => pell.exec('bold')},
            {name: 'italic', icon: '<i>I</i>', title: '{$editorStrings['italic']}', result: () => pell.exec('italic')},
            {name: 'underline', icon: '<u>U</u>', title: '{$editorStrings['underline']}', result: () => pell.exec('underline')},
            {name: 'strikethrough', icon: '<strike>S</strike>', title: '{$editorStrings['strikethrough']}', result: () => pell.exec('strikethrough')},
            {name: 'heading1', icon: '<b>H<sub>1</sub></b>', title: '{$editorStrings['heading1']}', result: () => pell.exec('formatBlock', '<h1>')},
            {name: 'heading2', icon: '<b>H<sub>2</sub></b>', title: '{$editorStrings['heading2']}', result: () => pell.exec('formatBlock', '<h2>')},
            {name: 'paragraph', icon: '&#182;', title: '{$editorStrings['paragraph']}', result: () => pell.exec('formatBlock', '<p>')},
            {name: 'quote', icon: '&#8220; &#8221;', title: '{$editorStrings['quote']}', result: () => pell.exec('formatBlock', '<blockquote>')},
            {name: 'olist', icon: '&#35;', title: '{$editorStrings['olist']}', result: () => pell.exec('insertOrderedList')},
            {name: 'ulist', icon: '&#8226;', title: '{$editorStrings['ulist']}', result: () => pell.exec('insertUnorderedList')},
            {name: 'code', icon: '&lt;/&gt;', title: '{$editorStrings['code']}', result: () => pell.exec('formatBlock', '<pre>')},
            {name: 'line', icon: '&#8213;', title: '{$editorStrings['line']}', result: () => pell.exec('insertHorizontalRule')},
            {
                name: 'link',
                icon: '&#128279;',
                title: '{$editorStrings['link']}',
                result: () => {
                    const url = window.prompt('{$editorStrings['link_prompt']}');
                    if (url) pell.exec('createLink', url);
                }
            }
        ]
    });
    // Set initial content
    const initialContent = $contentJson;
    if (initialContent) {
        editor.content.innerHTML = initialContent;
        document.getElementById('content-input').value = initialContent;
    }
});
</script>
EDITOR;

$block1->contentRow($strings["comments"], $editorHtml);

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
