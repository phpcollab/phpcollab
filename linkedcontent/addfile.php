<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../linkedcontent/addfile.php
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
** FILE: addfile.php
**
** DESC: Screen: adding file to linked content
**
** HISTORY:
** 	26/01/2004	-	added file notification
**  27/01/2004  -	added file comment
**  18/02/2005	-	added fix for php 4.3.11 and removed spaces from name
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include_once '../includes/library.php';

//set task to "0" for project main folder upload
if ($task == "") 
{
	$task = "0";
}

if ($action == "add") 
{

	$filename = phpCollab\Util::checkFileName($_FILES['upload']['name']);
	
	if ($maxCustom != "") 
	{
		$maxFileSize = $maxCustom;
	}

	if ($_FILES['upload']['size'] != 0) 
	{
		$taille_ko = $_FILES['upload']['size']/1024;
	} 
	else 
	{
		$taille_ko=0;
	}

	if ($filename == "") 
	{
		$error.=$strings["no_file"]."<br/>";
	}

	if ($_FILES['upload']['size'] > $maxFileSize) 
	{
		if($maxFileSize != 0) 
		{
			$taille_max_ko = $maxFileSize/1024;
		}
		$error.=$strings["exceed_size"]." ($taille_max_ko $byteUnits[1])<br/>";
	}

	$extension= strtolower( substr( strrchr($filename, ".") ,1) );

	if ($allowPhp == "false") 
	{
		$send = "";
		if ($filename != "" && ($extension=="php" || $extension=="php3" || $extension=="phtml")) 
		{
			$error.=$strings["no_php"]."<br/>";
			$send = "false";
		}
	}

	if ($filename != "" && $_FILES['upload']['size']<$maxFileSize && $_FILES['upload']['size']!=0 && $send != "false") 
	{
		$docopy = "true";
	}

	if ($docopy == "true") 
	{

		$match = strstr($versionFile,".");
		if ($match == "") 
		{
			$versionFile = $versionFile.".0";
		}

		if ($versionFile == "") 
		{
			$versionFile = "0.0";
		}

		$c = phpCollab\Util::convertData($c);
		$tmpquery = "INSERT INTO ".$tableCollab["files"]."(owner,project,phase,task,comments,upload,published,status,vc_version,vc_parent) VALUES('$idSession','$project','".phpCollab\Util::fixInt($phase)."','$task','$c','$dateheure','1','$statusField','$versionFile','0')";
		phpCollab\Util::connectSql("$tmpquery");
		$tmpquery = $tableCollab["files"];
		phpCollab\Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);
	}

	if ($task != "0") 
	{
		if ($docopy == "true") 
		{
			phpCollab\Util::uploadFile("files/$project/$task", $_FILES['upload']['tmp_name'], "$num--".$filename);
			$size = phpCollab\Util::fileInfoSize("../files/".$project."/".$task."/".$num."--".$filename);
			$chaine = strrev("../files/".$project."/".$task."/".$num."--".$filename);
			$tab = explode(".",$chaine);
			$extension = strtolower(strrev($tab[0]));
		}
	} 
	else 
	{
		if ($docopy == "true") 
		{
			phpCollab\Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'], "$num--".$filename);
			$size = phpCollab\Util::fileInfoSize("../files/".$project."/".$num."--".$filename);
			$chaine = strrev("../files/".$project."/".$num."--".$filename);
			$tab = explode(".",$chaine);
			$extension = strtolower(strrev($tab[0]));
		}
	}
	
	if ($docopy == "true") 
	{
		$name = $num."--".$filename;
		$tmpquery = "UPDATE ".$tableCollab["files"]." SET name='$name',date='$dateheure',size='$size',extension='$extension' WHERE id = '$num'";
		phpCollab\Util::connectSql("$tmpquery");

		if ($notifications == "true") 
		{			
			require("../projects_site/noti_uploadfile.php");
		}		
		
		phpCollab\Util::headerFunction("../linkedcontent/viewfile.php?id=$num&msg=addFile");
	}
}

$tmpquery = "WHERE pro.id = '$project'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new phpCollab\Request();
$memberTest->openTeams($tmpquery);
$comptMemberTest = count($memberTest->tea_id);

if ($comptMemberTest == "0") 
{
	$teamMember = "false";
} 
else 
{
	$teamMember = "true";
}

if ($teamMember == "false" && $projectsFilter == "true") 
{ 
	header("Location:../general/permissiondenied.php");
}

if ($projectDetail->pro_phase_set[0] != "0")
{
	$phase = $projectDetail->pro_phase_set[0];

	$tmpquery = "WHERE pha.id = '$phase'";
	$phaseDetail = new phpCollab\Request();
	$phaseDetail->openPhases($tmpquery);
}

if ($task != "0") 
{
	$tmpquery = "WHERE tas.id = '$task'";
	$taskDetail = new phpCollab\Request();
	$taskDetail->openTasks($tmpquery);
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=$project",$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0" && $phase != 0)
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=".$phaseDetail->pha_id[0],$phaseDetail->pha_name[0],in));
} 

if ($task != "0") 
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?$project=$project",$strings["tasks"],in));
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=$task",$taskDetail->tas_name[0],in));
}

$blockPage->itemBreadcrumbs($strings["add_file"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messageBox($msgLabel);

}

$block1 = new phpCollab\Block();


$block1->form = "filedetails";

echo "<a name='filedetailsAnchor'></a>";
echo "<form accept-charset='UNKNOWN' method='POST' action='../linkedcontent/addfile.php?action=add&project=$project&task=$task&phase=$phase&' name='filedetailsForm' enctype='multipart/form-data'><input type='hidden' name='MAX_FILE_SIZE' value='100000000'><input type='hidden' name='maxCustom' value='".$projectDetail->pro_upload_max[0]."'>";

if ($error != "") 
{            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

$block1->heading($strings["add_file"]);

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["status"]." :</td><td><select name='statusField'>";

$comptSta = count($statusFile);

for ($i=0;$i<$comptSta;$i++) 
{
	if ($i == "2") 
	{
		echo "<option value='$i' selected>$statusFile[$i]</option>";
	} 
	else 
	{
		echo "<option value='$i'>$statusFile[$i]</option>";
	}
}

echo"</select></td></tr>

<tr class='odd'><td valign='top' class='leftvalue'>* ".$strings["upload"]." :</td><td><input size='44' style='width: 400px' name='upload' type='FILE'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["comments"]." :</td><td><textarea rows='3' style='width: 400px; height: 50px;' name='c' cols='43'>$c</textarea></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["vc_version"]." :</td><td><input size='44' style='width: 400px' name='versionFile' type='text' value='0.0'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='SUBMIT' value='".$strings["save"]."'></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
