<?php
/*
** Application name: phpCollab
** Path by root: ../search/createsearch.php
**
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: createsearch.php
**
** DESC: Screen: CREATE SEARCH
**
*/


$checkSession = "true";
include_once '../includes/library.php';

$error = null;

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            //test required field searchfor
            if ($request->request->get("action") == "search") {
                //if searchfor blank, $error set
                $searchfor = $request->request->get("searchfor");
                $heading = $request->request->get("heading");

                if ($searchfor == "") {
                    $error = $strings["search_note"];

                    //if searchfor not blank, redirect to searchresults
                } else {
                    $searchfor = urlencode($searchfor);
                    phpCollab\Util::headerFunction("../search/resultssearch.php?searchfor={$searchfor}&heading={$heading}");
                }
            }
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
    }
}


$setTitle .= " : Search";

$bodyCommand = 'onLoad="document.searchForm.searchfor.focus()"';
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../search/createsearch.php?", $strings["search"], "in"));
$blockPage->itemBreadcrumbs($strings["search_options"]);
$blockPage->closeBreadcrumbs();

if ($request->query->get("msg") != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "search";
$block1->openForm("../search/createsearch.php?", null, $csrfHandler);

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

$block1->heading($strings["search"]);

$block1->openContent();
$block1->contentTitle($strings["enter_keywords"]);

echo <<<HTML
<tr class="odd">
	<td class="leftvalue">* {$strings["search_for"]} :</td>
	<td>
		<input value="" type="text" name="searchfor" style="width: 200px;" size="30" maxlength="64" />
		<select name="heading">
				<option selected value="ALL">{$strings["all_content"]}</option>
				<option value="notes">{$strings["notes"]}</option>
				<option value="organizations">{$strings["organizations"]}</option>
				<option value="projects">{$strings["projects"]}</option>
				<option value="tasks">{$strings["tasks"]}</option>
				<option value="subtasks">{$strings["subtasks"]}</option>
				<option value="discussions">{$strings["discussions"]}</option>
				<option value="members">{$strings["users"]}</option>
		</select>
	</td>
</tr>
<tr class="odd">
	<td class="leftvalue">&nbsp;</td>
	<td><button type="submit" name="action" value="search">{$strings["search"]}</button></td>
</tr>
HTML;


$block1->closeContent();
$block1->closeForm();


include APP_ROOT . '/themes/' . THEME . '/footer.php';
