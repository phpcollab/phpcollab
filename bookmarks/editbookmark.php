<?php
/*
** Application name: phpCollab
** Path by root: ../bookmarks/editbookmark.php
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editbookmark.php
**
** DESC: Screen: modify/add bookmark in db
**
** =============================================================================
*/

use phpCollab\Bookmarks\Bookmark;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $bookmarkService = $container->getBookmarksLoader();
    $categories = $bookmarkService->getBookmarkCategories();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$name = "";
$url = "";
$description = "";
$category_new = "";

if ($request->isMethod('post')) {
    $logger->info('Edit Bookmark', ['bookmark' => $request->request->get("name")]);
    try {

        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if (empty($request->request->get('name')) && empty($request->request->get('url'))) {
                $session->getFlashBag()->add(
                    'errors',
                    $strings["bookmark_error_blank_name_and_url"]
                );
            }

            if (empty($request->request->get('name'))) {
                $session->getFlashBag()->add(
                    'errors',
                    $strings["bookmark_error_blank_name"]
                );
            }

            if (empty($request->request->get('url'))) {
                $session->getFlashBag()->add(
                    'errors',
                    $strings["bookmark_error_blank_url"]
                );
            }

            if (!filter_var($request->request->get('url'), FILTER_VALIDATE_URL)) {
                $session->getFlashBag()->add(
                    'errors',
                    $strings["bookmark_error_url_invalid"]
                );
            }


            if (!$session->getFlashBag()->has('errors')) {
                $bookmark = new Bookmark($session->get('id'), $request->request->get('name'), $request->request->get('url'));

                if ($request->query->get("id")) {
                    $bookmark->setId($request->query->get("id"));
                }

                $bookmark->setDescription($request->request->get('description'));

                if (!empty($request->request->get('category_new'))) {
                    $bookmark->setCategory($request->request->get('category_new'));
                } else {
                    $bookmark->setCategory($request->request->get('category'));
                }

                $bookmark->setShared($request->request->get('shared') ?? false);
                $bookmark->setHome($request->request->get('home') ?? false);
                $bookmark->setComments($request->request->get('comments') ?? false);
                $bookmark->setSharedWith($request->request->get('sharedWith') ?? null);

                $bookmarkService->update($bookmark);

                if ($request->query->get('action') == "add") {
                    $session->getFlashBag()->add(
                        'message',
                        $strings["bookmark_added"]
                    );
                }

                if ($request->query->get('action') == "update") {
                    $session->getFlashBag()->add(
                        'message',
                        $strings["bookmark_updated"]
                    );
                }

                phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my");
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Bookmarks: edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $exception) {
        $logger->critical('Exception', ['Error' => $exception->getMessage()]);
        $msg = 'permissiondenied';
    }
}

if (!empty($request->query->get('id'))) {
    $id = $request->query->get('id');

    $bookmarkId = filter_var((int)$id, FILTER_VALIDATE_INT);
    $bookmarkDetail = $bookmarkService->getBookmarkById($id);

    //set value in form
    $name = $bookmarkDetail['boo_name'];
    $url = $bookmarkDetail['boo_url'];
    $description = $bookmarkDetail['boo_description'];
    $category = $bookmarkDetail['boo_category'];
    $shared = $bookmarkDetail['boo_shared'];
    if ($shared == "1") {
        $checkedShared = "checked";
    }
    $home = $bookmarkDetail['boo_home'];
    if ($home == "1") {
        $checkedHome = "checked";
    }
    $comments = $bookmarkDetail['boo_comments'];
    if ($comments == "1") {
        $checkedComments = "checked";
    }

    $setTitle .= " : " . $strings["edit_bookmark"] . "($name)";

} else {
    $id = null;
    $checkedShared = "checked";
    $checkedComments = "checked";

    $setTitle .= " : " . $strings["add_bookmark"];
}

$bodyCommand = 'onLoad="document.bookmarkForm.name.focus();"';
include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=my", $strings["bookmarks"],
    'in'));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_bookmark"]);
}
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/viewbookmark.php?id=" . $bookmarkDetail['boo_id'],
        $bookmarkDetail['boo_name'], 'in'));
    $blockPage->itemBreadcrumbs($strings["edit_bookmark"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "bookmark";
if ($id == "") {
    $block1->openForm("../bookmarks/editbookmark.php?action=add&#" . $block1->form . "Anchor", null, $csrfHandler);
}
if ($id != "") {
    $block1->openForm("../bookmarks/editbookmark.php?id=$id&action=update&#" . $block1->form . "Anchor", null,
        $csrfHandler);
}

if ($session->getFlashBag()->has('errors')) {
    $block1->headingError($strings["errors"]);
    foreach ($session->getFlashBag()->get('errors', []) as $error) {
        $block1->contentError($error);
    }
} else if (!empty($errors)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($errors);
}

if ($id == "") {
    $block1->heading($strings["add_bookmark"]);
}
if ($id != "") {
    $block1->heading($strings["edit_bookmark"] . " : " . $bookmarkDetail['boo_name']);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["name"]} :</td>
    <td><input size="44" value="$name" style="width: 400px" name="name" type="text" required="required" aria-required="true"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["url"]} :</td>
    <td><input size="44" value="$url" style="width: 400px" name="url" type="url" required="required" aria-required="true"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["description"]} :</td>
    <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">$description</textarea></td>
</tr>
<tr class="odd">
    <td class="leftvalue"> {$strings['bookmark_category']} :</td>
    <td>
        <select name="category" style="width: 200px;">
            <option value="0">-</option>
HTML;



foreach ($categories as $item) {
    $selected = ($item['boocat_id'] == $bookmarkDetail['boo_category']) ? 'selected' : '';
    echo '<option value="' . $item['boocat_id'] . '" ' . $selected . '>' . $item['boocat_name'] . '</option>';
}

echo <<<HTML
</select>
</td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["bookmark_category_new"]} :</td>
    <td><input size="44" value="$category_new" style="width: 400px" name="category_new" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["shared"]} :</td>
    <td><input size="32" value="1" name="shared" type="checkbox" $checkedShared></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["home"]} :</td>
    <td><input size="32" value="1" name="home" type="checkbox" $checkedHome></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["comments"]} :</td>
    <td><input size="32" value="1" name="comments" type="checkbox" $checkedComments></td>
</tr>
HTML;

$listUsers = $members->getAllMembers();

$oldCaptured = $bookmarkDetail['boo_users'];

if ($bookmarkDetail['boo_users'] != "") {
    $listCaptured = explode("|", $bookmarkDetail['boo_users']);
}

if (count($listUsers) != "0") {
    echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["share_with"]} :</td>
    <td>
        <select name="sharedWith[]" multiple size=10 style="width: 200px;">
HTML;

    foreach ($listUsers as $user) {
        if ($listCaptured) {
            $selected = (in_array($user['mem_id'], $listCaptured)) ? 'selected' : '';
        }
        echo '<option value="' . $user['mem_id'] . '" ' . $selected . '>' . $user['mem_login'] . '</option>';
    }

    echo <<<HTML
    </select>
</td>
</tr>
<input type="hidden" name="oldCaptured" value="$oldCaptured">
HTML;
}

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">&nbsp;</td>
    <td><input type="submit" value="{$strings["save"]}"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
