<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../project_site/uploadfile.php
** Authors: Ceam / Fullo / Shaders
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: uploadfile.php
**
** DESC: Screen: notification class
**
** HISTORY:
** 	26/01/2004	-	added file notification
**  18/02/2005	-	added fix for php 4.3.11 and removed spaces from name
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include("../includes/library.php");

if ($action == "add") 
{
	$filename = Util::checkFileName($_FILES['upload']['name']);
	
	if ($maxCustom != "") 
	{
		$maxFileSize = $maxCustom;
	}

	if ($_FILES['upload']['size']!=0) 
	{
		$taille_ko=$_FILES['upload']['size']/1024;
	} 
	else 
	{
		$taille_ko=0;
	}

	if ($filename == "") 
	{
		$error.=$strings["no_file"]."<br/>";
	}

	if ($_FILES['upload']['size']>$maxFileSize) 
	{
		if($maxFileSize!=0) 
		{
			$taille_max_ko=$maxFileSize/1024;
		}
		$error.=$strings["exceed_size"]." ($taille_max_ko $byteUnits[1])<br/>";
	}

	$extension= strtolower( substr( strrchr($filename, ".") ,1) );

	if ($allowPhp == "false") 
	{
		$send = "";
		if ($filename != "" && ($extension=="php" || $extension=="php3" || $extension=="phtml")) {
			$error.=$strings["no_php"]."<br/>";
			$send = "false";
		}
	}

	if ($filename != "" && $_FILES['upload']['size']<$maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") 
	{
		$docopy = "true";
	}
		
	if ($docopy == "true") 
	{
		$commentsField = Util::convertData($commentsField);
		$tmpquery = "INSERT INTO ".$tableCollab["files"]."(owner,project,task,comments,upload,published,status,vc_version,vc_parent,phase) VALUES('$idSession','$projectSession','0','$commentsField','$dateheure','0','2','0.0','0','0')";
		Util::connectSql("$tmpquery");
		$tmpquery = $tableCollab["files"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);

		if ($notifications == "true") 
		{
			include("../projects_site/noti_uploadfile.php");
		}

		Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'], "$num--".$filename);
		
		$size = Util::fileInfoSize("../files/".$project."/".$num."--".$filename);
		//$dateFile = Util::getFileDate("files/".$project."/".$num."--".$filename);
		
		$chaine = strrev("../files/".$project."/".$num."--".$filename);
		$tab = explode(".",$chaine);
		$extension = strtolower(strrev($tab[0]));
				
		$name = $num."--".$filename;
		$tmpquery = "UPDATE ".$tableCollab["files"]." SET name='$name',date='$dateheure',size='$size',extension='$extension' WHERE id = '$num'";
			
		Util::connectSql("$tmpquery");
		Util::headerFunction("doclists.php?".session_name()."=".session_id());
		exit;
	}

}

$bouton[4] = "over";
$titlePage = $strings["upload_file"];
include ("include_header.php");

echo "
	<form accept-charset='UNKNOWN' method='POST' action='../projects_site/uploadfile.php?".session_name()."=".session_id()."&action=add&project=$projectSession&task=$task#filedetailsAnchor' name='feeedback' enctype='multipart/form-data'>
		<input type='hidden' name='MAX_FILE_SIZE' value='100000000'>
		<input type='hidden' name='maxCustom' value='".$projectDetail->pro_upload_max[0]."'>
	
		<table cellpadding='3' cellspacing='0' border='0'>
		<tr>
			<th colspan='2'>".$strings["upload_form"]."</th>
		</tr>

		<tr>
			<th>".$strings["comments"]." :</th>
			<td><textarea cols='60' name='commentsField' rows='6'>$commentsField</textarea></td>
		</tr>

		<tr>
			<th>".$strings["upload"]." :</th>
			<td><input size='35' value='' name='upload' type='file'></td>
		</tr>

		<tr>
			<th>&nbsp;</th>
			<td><input name='submit' type='submit' value='".$strings["save"]."'><br/><br/>$error</td>
		</tr>
		</table>
	</form>";

include ("include_footer.php");
?>