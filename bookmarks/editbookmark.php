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

if ($id != "" && $action != "add") 
{
	$tmpquery = "WHERE boo.id = '$id'";
	$bookmarkDetail = new Request();
	$bookmarkDetail->openBookmarks($tmpquery);

	if ($bookmarkDetail->boo_owner[0] != $idSession) 
	{
		Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=bookmarkOwner");
		exit;
	}
}

//case update bookmark entry
if ($id != "") {

	//case update bookmark entry
	if ($action == "update") 
	{
		if ($piecesNew != "") 
		{
			$users = "|".implode("|",$piecesNew)."|";
		}
		if ($category_new != "") 
		{
			$category_new = Util::convertData($category_new);
			$tmpquery = "WHERE boocat.name = '$category_new'";
			$listCategories = new Request();
			$listCategories->openBookmarksCategories($tmpquery);
			$comptListCategories = count($listCategories->boocat_id);
			if ($comptListCategories == "0") 
			{
				$tmpquery1 = "INSERT INTO ".$tableCollab["bookmarks_categories"]."(name) VALUES('$category_new')";
				Util::connectSql("$tmpquery1");

				$tmpquery = $tableCollab["bookmarks_categories"];
				Util::getLastId($tmpquery);
				$num = $lastId[0];
				unset($lastId);
				
				$category = $num;
			} 
			else 
			{
				$category = $listCategories->boocat_id[0];
			}
		}
		if ($shared == "" || $users != "") 
		{
			$shared = "0";
		}
		if ($home == "") 
		{
			$home = "0";
		}
		if ($comments == "") 
		{
			$comments = "0";
		}

		$name = Util::convertData($name);
		$description = Util::convertData($description);
		$tmpquery5 = "UPDATE ".$tableCollab["bookmarks"]." SET url='$url',name='$name',description='$description',modified='$dateheure',category='$category',shared='$shared',home='$home',comments='$comments',users='$users' WHERE id = '$id'";
		Util::connectSql("$tmpquery5");
		Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=update");
	}
	
	//set value in form
	$name = $bookmarkDetail->boo_name[0];
	$url = $bookmarkDetail->boo_url[0];
	$description = $bookmarkDetail->boo_description[0];
	$category = $bookmarkDetail->boo_category[0];
	$shared = $bookmarkDetail->boo_shared[0];
	if ($shared == "1") 
	{
		$checkedShared = "checked";
	}
	$home = $bookmarkDetail->boo_home[0];
	if ($home == "1") 
	{
		$checkedHome = "checked";
	}
	$comments = $bookmarkDetail->boo_comments[0];
	if ($comments == "1") 
	{
		$checkedComments = "checked";
	}
    
    $setTitle .= " : Edit Bookmark ($name)";
}

//case add note entry
if ($id == "") 
{
	$checkedShared = "checked";
	$checkedComments = "checked";

    $setTitle .= " : Add Bookmark";
	//case add note entry
	if ($action == "add") 
	{
		if ($piecesNew != "") 
		{
			$users = "|".implode("|",$piecesNew)."|";
		}
		if ($category_new != "") 
		{
			$category_new = Util::convertData($category_new);
			$tmpquery = "WHERE boocat.name = '$category_new'";
			$listCategories = new Request();
			$listCategories->openBookmarksCategories($tmpquery);
			$comptListCategories = count($listCategories->boocat_id);
			if ($comptListCategories == "0") 
			{
				$tmpquery1 = "INSERT INTO ".$tableCollab["bookmarks_categories"]."(name) VALUES('$category_new')";
				Util::connectSql("$tmpquery1");

				$tmpquery = $tableCollab["bookmarks_categories"];
				Util::getLastId($tmpquery);
				$num = $lastId[0];
				unset($lastId);
				
				$category = $num;
			} 
			else 
			{
				$category = $listCategories->boocat_id[0];
			}
		}

		if ($shared == "" || $users != "") 
		{
			$shared = "0";
		}
		if ($home == "") 
		{
			$home = "0";
		}
		if ($comments == "") 
		{
			$comments = "0";
		}

		$name = Util::convertData($name);
		$description = Util::convertData($description);
		$tmpquery1 = "INSERT INTO ".$tableCollab["bookmarks"]."(owner,category,name,url,description,shared,home,comments,users,created) VALUES('$idSession','$category','$name','$url','$description','$shared','$home','$comments','$users','$dateheure')";
		Util::connectSql("$tmpquery1");
		Util::headerFunction("../bookmarks/listbookmarks.php?view=my&msg=add");
	}

}

$bodyCommand = "onLoad=\"document.booForm.name.focus();\"";
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/listbookmarks.php?view=my",$strings["bookmarks"],in));

if ($id == "") 
{
	$blockPage->itemBreadcrumbs($strings["add_bookmark"]);
}
if ($id != "") 
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../bookmarks/viewbookmark.php?id=".$bookmarkDetail->boo_id[0],$bookmarkDetail->boo_name[0],in));
	$blockPage->itemBreadcrumbs($strings["edit_bookmark"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
if ($id == "") 
{
	$block1->form = "boo";
	$block1->openForm("../bookmarks/editbookmark.php?action=add&#".$block1->form."Anchor");
}
if ($id != "") 
{
	$block1->form = "boo";
	$block1->openForm("../bookmarks/editbookmark.php?id=$id&action=update&#".$block1->form."Anchor");
}
if ($error != "") 
{            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);

}
if ($id == "") 
{
	$block1->heading($strings["add_bookmark"]);
}
if ($id != "") 
{
	$block1->heading($strings["edit_bookmark"]." : ".$bookmarkDetail->boo_name[0]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["bookmark_category"]." :</td><td><select name=\"category\">
<option value=\"0\">-</option>";

$tmpquery = "ORDER BY boocat.name";
$listCategories = new Request();
$listCategories->openBookmarksCategories($tmpquery);
$comptListCategories = count($listCategories->boocat_id);

for ($i=0;$i<$comptListCategories;$i++) 
{
	if ($listCategories->boocat_id[$i] == $bookmarkDetail->boo_category[0]) 
	{
		echo "<option value=\"".$listCategories->boocat_id[$i]."\" selected>".$listCategories->boocat_name[$i]."</option>";
	} 
	else 
	{
		echo "<option value=\"".$listCategories->boocat_id[$i]."\">".$listCategories->boocat_name[$i]."</option>";
	}
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["bookmark_category_new"]." :</td><td><input size='44' value='$category_new' style='width: 400px' name='category_new' type='TEXT'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["name"]." :</td><td><input size='44' value='$name' style='width: 400px' name='name' type='TEXT'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["url"]." :</td><td><input size='44' value='$url' style='width: 400px' name='url' type='TEXT'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["description"]." :</td><td><textarea rows='10' style='width: 400px; height: 160px;' name='description' cols='47'>$description</textarea></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["shared"]." :</td><td><input size='32' value='1' name='shared' type='checkbox' $checkedShared></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["home"]." :</td><td><input size='32' value='1' name='home' type='checkbox' $checkedHome></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["comments"]." :</td><td><input size='32' value='1' name='comments' type='checkbox' $checkedComments></td></tr>";

if ($demoMode == "true") 
{
	$tmpquery = "WHERE mem.id != '$idSession' AND mem.profil != '3' ORDER BY mem.login";
} 
else 
{
	$tmpquery = "WHERE mem.id != '$idSession' AND mem.profil != '3' AND mem.id != '2' ORDER BY mem.login";
}
$listUsers = new Request();
$listUsers->openMembers($tmpquery);
$comptListUsers = count($listUsers->mem_id);


$oldCaptured = $bookmarkDetail->boo_users[0];

if ($bookmarkDetail->boo_users[0] != "") 
{
	$listCaptured = explode("|",$bookmarkDetail->boo_users[0]);
	$comptListCaptured = count($listCaptured);
}
if ($comptListUsers != "0") 
{
	echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["private"]." :</td><td><select name='piecesNew[]' multiple size=10>";
	//$oldCaptured = "";
	for($i=0; $i<$comptListUsers; $i++) 
	{
		$selected[$i] = "";
		for($j=0; $j<$comptListCaptured; $j++) 
		{
			if ($listUsers->mem_id[$i] == $listCaptured[$j]) 
			{
				$selected[$i] = "selected";
				//$oldCaptured .= $listCaptured[$j].":";
				break;
			} 
			else 
			{
				$selected[$i] = "";
			}
		}

		echo "<option value=".$listUsers->mem_id[$i]." $selected[$i]>".$listUsers->mem_login[$i]."</option>";
	}

	echo "</select></td></tr><input type='hidden' name='oldCaptured' value='$oldCaptured'>";
}

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='SUBMIT' value='".$strings["save"]."'></td></tr>";
$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>