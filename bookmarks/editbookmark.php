<?php
/*
** Application name: phpCollab
** Last Edit page: 30/05/2005
** Path by root: ../bookmarks/editbookmark.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editbookmark.php
**
** DESC: Screen: modify/add bookmark in db
**
** HISTORY:
** 	2003-10-23	-	added new document info
**  30/05/2005	-	fix for [ 1211360 ] Fix for ' character in category
** -----------------------------------------------------------------------------
** TO-DO:
**	move to the template system 
**
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';

$bookmark = new \phpCollab\Bookmarks\Bookmarks();
$member = new \phpCollab\Members\Members();

if ($id != "") {
    $bookmarkId = filter_var( (int) $id, FILTER_VALIDATE_INT);
    $bookmarkDetail = $bookmark->getBookmarkById($id);
}

//case update bookmark entry
if ($id != "") {
    //case update bookmark entry
    if ($action == "update") {
        if ($piecesNew != "") {
            $users = "|" . implode("|", $piecesNew) . "|";
        }

        /**
         * Below does:
         * checks to see if the "new category" exists
         * If it doesn't, then it inserts it.
         * Otherwise it adds it to the $category
         */
        if ($category_new != "") {
            $category = $bookmark->getBookmarkCategoryByName($category_new);

            /**
             * If category is false, hence it doesn't exist, then add it
             */
            if (!$category) {
                $category = $bookmark->addNewBookmarkCategory(phpCollab\Util::convertData($category_new));
            } else {
                $category = $category["boocat_id"];
            }
        }

        if ($shared == "" || $users != "") {
            $shared = "0";
        }
        if ($home == "") {
            $home = "0";
        }
        if ($comments == "") {
            $comments = "0";
        }

        /**
         * Validate form data
         */

        $filteredData =  [];
        $filteredData['id'] = filter_var( (int) $id, FILTER_VALIDATE_INT);
        $filteredData['url'] = filter_var( (string) \phpCollab\Util::addHttp($_POST['url']), FILTER_SANITIZE_URL);
        $filteredData['name'] = filter_var( (string) phpCollab\Util::convertData($_POST['name']), FILTER_SANITIZE_STRING);
        $filteredData['description'] = filter_var( (string) phpCollab\Util::convertData($_POST['description']), FILTER_SANITIZE_STRING);
        $filteredData['comments'] = filter_var( phpCollab\Util::convertData($comments), FILTER_SANITIZE_STRING);
        $filteredData['modified'] = $dateheure;
        $filteredData['category'] = filter_var( (int) $category, FILTER_VALIDATE_INT);
        $filteredData['shared'] = filter_var( (int) $shared, FILTER_VALIDATE_INT);
        $filteredData['home'] = filter_var( (int) $home, FILTER_VALIDATE_INT);
        $filteredData['users'] = $users;

        $updateBookmark = $bookmark->updateBookmark($filteredData);

        phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=update");
    }

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
}

//case add note entry
if ($id == "") {
    $checkedShared = "checked";
    $checkedComments = "checked";

    $setTitle .= " : Add Bookmark";
    //case add note entry
    if ($action == "add") {
        if ($piecesNew != "") {
            $users = "|" . implode("|", $piecesNew) . "|";
        }
        if ($category_new != "") {
            /**
             * Check to see if the category exists
             */
            $category = $bookmark->getBookmarkCategoryByName($category_new);

            /**
             * If category is false, hence it doesn't exist, then add it
             */
            if (!$category) {
                $category = $bookmark->addNewBookmarkCategory($category_new);
            } else {
                $category = $category["boocat_id"];
            }
        }


        if ($shared == "" || $users != "") {
            $shared = "0";
        }
        if ($home == "") {
            $home = "0";
        }
        if ($comments == "") {
            $comments = "0";
        }

        /**
         * Validate form data
         */

        $filteredData =  [];
        $filteredData['owner_id'] = filter_var( (int) $idSession, FILTER_VALIDATE_INT);
        $filteredData['url'] = filter_var( (string) \phpCollab\Util::addHttp($_POST['url']), FILTER_SANITIZE_URL);
        $filteredData['name'] = filter_var( (string) $_POST['name'], FILTER_SANITIZE_STRING);
        $filteredData['description'] = filter_var( (string) $_POST['description'], FILTER_SANITIZE_STRING);
        $filteredData['comments'] = filter_var( $comments, FILTER_SANITIZE_STRING);
        $filteredData['created'] = $dateheure;
        $filteredData['category'] = filter_var( (int) $category, FILTER_VALIDATE_INT);
        $filteredData['shared'] = filter_var( (int) $shared, FILTER_VALIDATE_INT);
        $filteredData['home'] = filter_var( (int) $home, FILTER_VALIDATE_INT);
        $filteredData['users'] = $users;

        $addBookmark = $bookmark->addBookmark($filteredData);

        phpCollab\Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=add");
    }

}

$bodyCommand = 'onLoad="document.booForm.name.focus();"';
include '../themes/' . THEME . '/header.php';

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
    <td valign="top" class="leftvalue"> {$strings['bookmark_category']} :</td>
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
    <td valign="top" class="leftvalue">{$strings["bookmark_category_new"]} :</td>
    <td><input size="44" value="{$category_new}" style="width: 400px" name="category_new" type="text"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["name"]} :</td>
    <td><input size="44" value="{$name}" style="width: 400px" name="name" type="text"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["url"]} :</td>
    <td><input size="44" value="{$url}" style="width: 400px" name="url" type="text"></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["description"]} :</td>
    <td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$description}</textarea></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["shared"]} :</td>
    <td><input size="32" value="1" name="shared" type="checkbox" {$checkedShared}></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["home"]} :</td>
    <td><input size="32" value="1" name="home" type="checkbox" {$checkedHome}></td>
</tr>
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["comments"]} :</td>
    <td><input size="32" value="1" name="comments" type="checkbox" {$checkedComments}></td>
</tr>
HTML;

if ($demoMode == "true") {
    $tmpquery = "WHERE mem.id != '$idSession' AND mem.profil != '3' ORDER BY mem.login";
} else {
    $tmpquery = "WHERE mem.id != '$idSession' AND mem.profil != '3' AND mem.id != '2' ORDER BY mem.login";
}

$listUsers = $member->getAllMembers();

$oldCaptured = $bookmarkDetail['boo_users'];

if ($bookmarkDetail['boo_users'] != "") {
    $listCaptured = explode("|", $bookmarkDetail['boo_users']);
}

if (count($listUsers) != "0") {
    echo <<<HTML
<tr class="odd">
    <td valign="top" class="leftvalue">{$strings["private"]} :</td>
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
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="SUBMIT" value="{$strings["save"]}"></td>
</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include '../themes/' . THEME . '/footer.php';
