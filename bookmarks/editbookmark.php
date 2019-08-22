<?php
/*
** Application name: phpCollab
** Path by root: ../bookmarks/editbookmark.php
** Authors: Jeff Sittler / mindblender
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

use phpCollab\Bookmarks\Bookmarks;
use phpCollab\Members\Members;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$bookmark = new Bookmarks();
$member = new Members();

$name = "";
$url = "";
$description = "";
$category_new = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST["name"]) && empty($_POST["url"])) {
        $error = "Please enter a name and URL";
    } else if (empty($_POST["name"])) {
        $error = "Please enter a name for the bookmark";
    } else if (empty($_POST["url"])) {
        $error = "Please enter a URL for the bookmark";
    } else {
        if ($_POST["piecesNew"] != "") {
            $users = "|" . implode("|", $_POST["piecesNew"]) . "|";
        }
        if ($_POST["category_new"] != "") {
            /**
             * Check to see if the category exists
             */
            $category = $bookmark->getBookmarkCategoryByName($_POST["category_new"]);

            /**
             * If category is false, hence it doesn't exist, then add it
             */
            if (!$category) {
                $category = $bookmark->addNewBookmarkCategory(phpCollab\Util::convertData($_POST["category_new"]));
            } else {
                $category = $category["boocat_id"];
            }
        }

        if ($_POST["shared"] == "" || $users != "") {
            $shared = "0";
        }
        if ($_POST["home"] == "") {
            $home = "0";
        }
        if ($_POST["comments"] == "") {
            $comments = "0";
        }

        /**
         * Validate form data
         */
        $filteredData =  [];
        $filteredData['url'] = filter_var((string) Util::addHttp($_POST['url']), FILTER_SANITIZE_URL);
        $filteredData['name'] = filter_var((string) Util::convertData($_POST['name']), FILTER_SANITIZE_STRING);
        $filteredData['description'] = filter_var((string) Util::convertData($_POST['description']), FILTER_SANITIZE_STRING);
        $filteredData['comments'] = filter_var(Util::convertData($comments), FILTER_SANITIZE_STRING);
        $filteredData['timestamp'] = $dateheure;
        $filteredData['category'] = filter_var((int) $category, FILTER_VALIDATE_INT);
        $filteredData['shared'] = filter_var((int) $shared, FILTER_VALIDATE_INT);
        $filteredData['home'] = filter_var((int) $home, FILTER_VALIDATE_INT);
        $filteredData['users'] = $users;
    }

    if ($_GET["action"] == "update") {
        $filteredData['id'] = filter_var((int) $id, FILTER_VALIDATE_INT);
        $updateBookmark = $bookmark->updateBookmark($filteredData);
        phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=update");
    }

    if ($_GET["action"] == "add") {
        $filteredData['owner_id'] = filter_var((int) $idSession, FILTER_VALIDATE_INT);
        $addBookmark = $bookmark->addBookmark($filteredData);

        phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=add");
    }
}


if (!empty($_GET["id"])) {
    $id = $_GET["id"];

    $bookmarkId = filter_var((int) $id, FILTER_VALIDATE_INT);
    $bookmarkDetail = $bookmark->getBookmarkById($id);

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

    $setTitle .= " : Edit Bookmark ($name)";

} else {
    $id = null;
    $checkedShared = "checked";
    $checkedComments = "checked";

    $setTitle .= " : Add Bookmark";

}

$bodyCommand = 'onLoad="document.booForm.name.focus();"';
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=my", $strings["bookmarks"], 'in'));

if ($id == "") {
    $blockPage->itemBreadcrumbs($strings["add_bookmark"]);
}
if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/viewbookmark.php?id=" . $bookmarkDetail['boo_id'], $bookmarkDetail['boo_name'], 'in'));
    $blockPage->itemBreadcrumbs($strings["edit_bookmark"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
if ($id == "") {
    $block1->form = "boo";
    $block1->openForm("../bookmarks/editbookmark.php?action=add&#" . $block1->form . "Anchor");
}
if ($id != "") {
    $block1->form = "boo";
    $block1->openForm("../bookmarks/editbookmark.php?id=$id&action=update&#" . $block1->form . "Anchor");
}
if ($error != "") {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
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
    <td class="leftvalue"> {$strings['bookmark_category']} :</td>
    <td>
        <select name="category" style="width: 200px;">
            <option value="0">-</option>
HTML;

$categories = $bookmark->getBookmarkCategories();

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
    <td><input size="44" value="{$category_new}" style="width: 400px" name="category_new" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["name"]} :</td>
    <td><input size="44" value="{$name}" style="width: 400px" name="name" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["url"]} :</td>
    <td><input size="44" value="{$url}" style="width: 400px" name="url" type="text"></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["description"]} :</td>
    <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$description}</textarea></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["shared"]} :</td>
    <td><input size="32" value="1" name="shared" type="checkbox" {$checkedShared}></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["home"]} :</td>
    <td><input size="32" value="1" name="home" type="checkbox" {$checkedHome}></td>
</tr>
<tr class="odd">
    <td class="leftvalue">{$strings["comments"]} :</td>
    <td><input size="32" value="1" name="comments" type="checkbox" {$checkedComments}></td>
</tr>
HTML;

$listUsers = $member->getAllMembers();

$oldCaptured = $bookmarkDetail['boo_users'];

if ($bookmarkDetail['boo_users'] != "") {
    $listCaptured = explode("|", $bookmarkDetail['boo_users']);
}

if (count($listUsers) != "0") {
    echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["private"]} :</td>
    <td>
        <select name="piecesNew[]" multiple size=10 style="width: 200px;">
HTML;

    foreach ($listUsers as $user) {
        if ($listCaptured) {
            $selected = (in_array($user['mem_id'], $listCaptured)) ? 'selected' : '';
        }
        echo '<option value="' . $user['mem_id'] . '" '. $selected.'>' . $user['mem_login'] . '</option>';
    }

    echo <<<HTML
    </select>
</td>
</tr>
<input type="hidden" name="oldCaptured" value="{$oldCaptured}">
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

include APP_ROOT . '/themes/' . THEME . '/footer.php';
