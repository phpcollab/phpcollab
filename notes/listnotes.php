<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../notes/listnotes.php

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

if ($action == "publish") {
	if ($addToSite == "true") {
		$multi = strstr($id,"**");
		if ($multi != "") {
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='0' WHERE id IN($id)";
		} else {
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='0' WHERE id = '$id'";
		}
		Util::connectSql("$tmpquery1");
		$msg = "addToSite";
		$id = $project;
	}
	
	if ($removeToSite == "true") {
		$multi = strstr($id,"**");
		if ($multi != "") {
			$id = str_replace("**",",",$id);
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='1' WHERE id IN($id)";
		} else {
			$tmpquery1 = "UPDATE ".$tableCollab["notes"]." SET published='1' WHERE id = '$id'";
		}
		Util::connectSql("$tmpquery1");
		$msg = "removeToSite";
		$id = $project;
	}
}

include '../themes/' . THEME . '/header.php';

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);
if ($comptMemberTest == "0") {
	$teamMember = "false";
} else {
	$teamMember = "true";
}

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
$blockPage->itemBreadcrumbs($strings["notes"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();
$block1->form = "saJ";
$block1->openForm("../notes/listnotes.php?&project=$project#".$block1->form."Anchor");

$block1->heading($strings["notes"]);

$block1->openPaletteIcon();
if ($teamMember == "true") {
	$block1->paletteIcon(0,"add",$strings["add"]);
	$block1->paletteIcon(1,"remove",$strings["delete"]);
	//$block1->paletteIcon(2,"export",$strings["export"]);
	if ($sitePublish == "true") {
		$block1->paletteIcon(3,"add_projectsite",$strings["add_project_site"]);
		$block1->paletteIcon(4,"remove_projectsite",$strings["remove_project_site"]);
	}
}
$block1->paletteIcon(5,"info",$strings["view"]);
if ($teamMember == "true") {
	$block1->paletteIcon(6,"edit",$strings["edit"]);
}
$block1->closePaletteIcon();

$comptTopic = count($topicNote);

if ($comptTopic != "0") {
	$block1->sorting("notes",$sortingUser->sor_notes[0],"note.date DESC",$sortingFields = array(0=>"note.subject",1=>"note.topic",2=>"note.date",3=>"mem.login",4=>"note.published"));
} else {
	$block1->sorting("notes",$sortingUser->sor_notes[0],"note.date DESC",$sortingFields = array(0=>"note.subject",1=>"note.date",2=>"mem.login",3=>"note.published"));
}
$tmpquery = "WHERE note.project = '$project' ORDER BY $block1->sortingValue";
$listNotes = new Request();
$listNotes->openNotes($tmpquery);
$comptListNotes = count($listNotes->note_id);

if ($comptListNotes != "0") {
	$block1->openResults();
$comptTopic = count($topicNote);

if ($comptTopic != "0") {
	$block1->labels($labels = array(0=>$strings["subject"],1=>$strings["topic"],2=>$strings["date"],3=>$strings["owner"],4=>$strings["published"]),"true");
} else {
	$block1->labels($labels = array(0=>$strings["subject"],1=>$strings["date"],2=>$strings["owner"],3=>$strings["published"]),"true");
}
	for ($i=0;$i<$comptListNotes;$i++) {
		$idPublish = $listNotes->note_published[$i];
		$block1->openRow();
		$block1->checkboxRow($listNotes->note_id[$i]);
		$block1->cellRow($blockPage->buildLink("../notes/viewnote.php?id=".$listNotes->note_id[$i],$listNotes->note_subject[$i],in));
if ($comptTopic != "0") {
		$block1->cellRow($topicNote[$listNotes->note_topic[$i]]);
}
		$block1->cellRow($listNotes->note_date[$i]);
		$block1->cellRow($blockPage->buildLink($listNotes->note_mem_email_work[$i],$listNotes->note_mem_login[$i],mail));
		if ($sitePublish == "true") {
			$block1->cellRow($statusPublish[$idPublish]);
		}
		$block1->closeRow();
	}
	$block1->closeResults();
} else {
	$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($teamMember == "true") {
	$block1->paletteScript(0,"add","../notes/editnote.php?project=$project","true,false,false",$strings["add"]);
	$block1->paletteScript(1,"remove","../notes/deletenotes.php?project=$project","false,true,true",$strings["delete"]);
	//$block1->paletteScript(2,"export","export.php?","false,true,true",$strings["export"]);
	if ($sitePublish == "true") {
		$block1->paletteScript(3,"add_projectsite","../notes/listnotes.php?addToSite=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["add_project_site"]);
		$block1->paletteScript(4,"remove_projectsite","../notes/listnotes.php?removeToSite=true&project=".$projectDetail->pro_id[0]."&action=publish","false,true,true",$strings["remove_project_site"]);
	}
}
$block1->paletteScript(5,"info","../notes/viewnote.php?","false,true,false",$strings["view"]);
if ($teamMember == "true") {
	$block1->paletteScript(6,"edit","../notes/editnote.php?project=$project","false,true,false",$strings["edit"]);
}
$block1->closePaletteScript($comptListNotes,$listNotes->note_id);

include '../themes/'.THEME.'/footer.php';

?>