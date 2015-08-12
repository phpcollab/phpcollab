<?php
#Application name: PhpCollab
#Status page: 1
#Path by root: ../projects/listprojects.php

$checkSession = "true";
include_once('../includes/library.php');

$setTitle .= " : List **ctive Projects";

if ($typeProjects == "") {
	$typeProjects = "active";
}

if ($typeProjects == "active") {
    $setTitle = str_replace("**", "A", $setTitle);
} else {
    $setTitle = str_replace("**", "Ina", $setTitle);
}

include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($strings["projects"]);
if ($typeProjects == "inactive") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?typeProjects=active",$strings["active"],in)." | ".$strings["inactive"]);
} else if ($typeProjects == "active") {
	$blockPage->itemBreadcrumbs($strings["active"]." | ".$blockPage->buildLink("../projects/listprojects.php?typeProjects=inactive",$strings["inactive"],in));
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include '../includes/messages.php';
	$blockPage->messagebox($msgLabel);
}

$blockPage->bornesNumber = "1";

$block1 = new Block();

$block1->form = "saP";
$block1->openForm("../projects/listprojects.php?typeProjects=$typeProjects&".session_name()."=".session_id()."#".$block1->form."Anchor");

$block1->heading($strings["projects"]);

$block1->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
	$block1->paletteIcon(0,"add",$strings["add"]);
	$block1->paletteIcon(1,"remove",$strings["delete"]);
}
$block1->paletteIcon(2,"info",$strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
	$block1->paletteIcon(3,"edit",$strings["edit"]);
	$block1->paletteIcon(4,"copy",$strings["copy"]);
}
if ($enable_cvs == "true") {
	$block1->paletteIcon(7,"cvs",$strings["browse_cvs"]);
}
if ($enableMantis == "true") {
	$block1->paletteIcon(8,"bug",$strings["bug"]);
}
$block1->closePaletteIcon();

$block1->borne = $blockPage->returnBorne("1");
$block1->rowsLimit = "20";

$block1->sorting("projects",$sortingUser->sor_projects[0],"pro.name ASC",$sortingFields = array(0=>"pro.id",1=>"pro.name",2=>"pro.priority",3=>"org.name",4=>"pro.status",5=>"mem.login",6=>"pro.published"));

if ($typeProjects == "inactive") {
	if ($projectsFilter == "true") {
		$tmpquery = "LEFT OUTER JOIN ".$tableCollab["teams"]." teams ON teams.project = pro.id ";
		$tmpquery .= " WHERE pro.status IN(0,1,4) AND teams.member = '$idSession' ORDER BY $block1->sortingValue";
	} else {
		$tmpquery = "WHERE pro.status IN(0,1,4) ORDER BY $block1->sortingValue";
	}
} else if ($typeProjects == "active") {
	if ($projectsFilter == "true") {
		$tmpquery = "LEFT OUTER JOIN ".$tableCollab["teams"]." teams ON teams.project = pro.id ";
		$tmpquery .= "WHERE pro.status IN(2,3) AND teams.member = '$idSession' ORDER BY $block1->sortingValue";
	} else {
		$tmpquery = "WHERE pro.status IN(2,3)  ORDER BY $block1->sortingValue";
	}
}

$block1->recordsTotal = Util::computeTotal($initrequest["projects"]." ".$tmpquery);

$listProjects = new Request();
$listProjects->openProjects($tmpquery,$block1->borne,$block1->rowsLimit);
$comptListProjects = count($listProjects->pro_id);

if ($comptListProjects != "0") {
	$block1->openResults();
	$block1->labels($labels = array(0=>$strings["id"],1=>$strings["project"],2=>$strings["priority"],3=>$strings["organization"],4=>$strings["status"],5=>$strings["owner"],6=>$strings["project_site"]),"true");

for ($i=0;$i<$comptListProjects;$i++) {
if ($listProjects->pro_org_id[$i] == "1") {
	$listProjects->pro_org_name[$i] = $strings["none"];
}
$idStatus = $listProjects->pro_status[$i];
$idPriority = $listProjects->pro_priority[$i];
$block1->openRow();
$block1->checkboxRow($listProjects->pro_id[$i]);
$block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=".$listProjects->pro_id[$i],$listProjects->pro_id[$i],in));
$block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=".$listProjects->pro_id[$i],$listProjects->pro_name[$i],in));
$block1->cellRow("<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt=\"\"> ".$priority[$idPriority]);
$block1->cellRow($listProjects->pro_org_name[$i]);
$block1->cellRow($status[$idStatus]);
$block1->cellRow($blockPage->buildLink($listProjects->pro_mem_email_work[$i],$listProjects->pro_mem_login[$i],mail));
if ($sitePublish == "true") {
	if ($listProjects->pro_published[$i] == "1") {
		$block1->cellRow("&lt;".$blockPage->buildLink("../projects/addprojectsite.php?id=".$listProjects->pro_id[$i],$strings["create"]."...",in)."&gt;");
} else {
		$block1->cellRow("&lt;".$blockPage->buildLink("../projects/viewprojectsite.php?id=".$listProjects->pro_id[$i],$strings["details"],in)."&gt;");
}
}
$block1->closeRow();
}
$block1->closeResults();

$block1->bornesFooter("1",$blockPage->bornesNumber,"","typeProjects=$typeProjects");

} else {
$block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
	$block1->paletteScript(0,"add","../projects/editproject.php?","true,false,false",$strings["add"]);
	$block1->paletteScript(1,"remove","../projects/deleteproject.php?","false,true,false",$strings["delete"]);
}
$block1->paletteScript(2,"info","../projects/viewproject.php?","false,true,false",$strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
	$block1->paletteScript(3,"edit","../projects/editproject.php?","false,true,false",$strings["edit"]);
	$block1->paletteScript(4,"copy","../projects/editproject.php?docopy=true","false,true,false",$strings["copy"]);
}
if ($enable_cvs == "true") {
	$block1->paletteScript(7,"cvs","../browsecvs/browsecvs.php?","false,true,false",$strings["browse_cvs"]);
}
if ($enableMantis == "true") {
	$block1->paletteScript(8,"bug",$pathMantis."login.php?url=http://{$HTTP_HOST}{$REQUEST_URI}&username=$loginSession&password=$passwordSession","false,true,false",$strings["bug"]);
}

$block1->closePaletteScript($comptListProjects,$listProjects->pro_id);

include '../themes/'.THEME.'/footer.php';
?>