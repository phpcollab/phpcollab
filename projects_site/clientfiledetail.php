<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include("../includes/library.php");

include("../includes/files_types.php");

$tmpquery = "WHERE fil.id = '$id'";
$fileDetail = new request();
$fileDetail->openFiles($tmpquery);

if ($fileDetail->fil_published[0] == "1" || $fileDetail->fil_project[0] != $projectSession) {
Util::headerFunction("index.php");
}

$type = file_info_type($fileDetail->fil_extension[0]);
$displayname = $fileDetail->fil_name[0];

//---------------------------------------------------------------------------------------------------
//Update file code
if ($action == "update") {
if ($maxCustom != "") {
$maxFileSize = $maxCustom;
}
	if ($_FILES['upload']['size']!=0) {
		$taille_ko=$_FILES['upload']['size']/1024;
	} else {
		$taille_ko=0;
	}
	if ($_FILES['upload']['name'] == "") {
		$error4.=$strings["no_file"]."<br/>";
	}
	if ($_FILES['upload']['size']>$maxFileSize) {
		if($maxFileSize!=0) {
			$taille_max_ko=$maxFileSize/1024;
		}
		$error4.=$strings["exceed_size"]." ($taille_max_ko $byteUnits[1])<br/>";
	}
	
	$upload_name = $fileDetail->fil_name[0];
	$extension= strtolower( substr( strrchr($upload_name, ".") ,1) );
	
	//Add version number to the old copy's file name.
	$changename = str_replace("."," v".$fileDetail->fil_vc_version[0].".", $fileDetail->fil_name[0]);

	//Generate paths for use further down.
	if ($fileDetail->fil_task[0] != "0") {
	$path = "files/".$fileDetail->fil_project[0]."/".$fileDetail->fil_task[0]."/$upload_name";
	$path_source = "files/".$fileDetail->fil_project[0]."/".$fileDetail->fil_task[0]."/".$fileDetail->fil_name[0];
	$path_destination = "files/".$fileDetail->fil_project[0]."/".$fileDetail->fil_task[0]."/$changename";
	}
	else{
	$path = "files/".$fileDetail->fil_project[0]."/$upload_name";
	$path_source = "files/".$fileDetail->fil_project[0]."/".$fileDetail->fil_name[0];
	$path_destination = "files/".$fileDetail->fil_project[0]."/$changename";
	}
	
	if ($allowPhp == "false") {
		$send = "";
		if ($_FILES['upload']['name'] != "" && ($extension=="php" || $extension=="php3" || $extension=="phtml")) {
			$error4.=$strings["no_php"]."<br/>";
			$send = "false";
		}
	}

	if ($_FILES['upload']['name'] != "" && $_FILES['upload']['size']<$maxFileSize && $_FILES['upload']['size'] != 0 && $send != "false") {
	$docopy = "true";
	}
	
	
	if ($docopy == "true") {
	
		//Copy old file with a new file name
		Util::moveFile($path_source,$path_destination);
		
		//Set variables from original files details.
		$copy_project = $fileDetail->fil_project[0];
		$copy_task = $fileDetail->fil_task[0];
		$copy_date = $fileDetail->fil_date[0];
		$copy_size = $fileDetail->fil_size[0];
		$copy_extension = $fileDetail->fil_extension[0];		
		$copy_comments = $fileDetail->fil_comments[0];
		$copy_upload = $fileDetail->fil_upload[0];
		$copy_pusblished = $fileDetail->fil_published[0];
		$copy_vc_parent = $fileDetail->fil_vc_parent[0];
		$copy_id = $fileDetail->fil_id[0];
		$copy_vc_version = $fileDetail->fil_vc_version[0];
				
		//Insert a new row for the copied file
		$comments = Util::convertData($comments);
		$tmpquery = "INSERT INTO ".$tableCollab["files"]."(owner,project,task,name,date,size,extension,comments,upload,published,status,vc_status,vc_version,vc_parent,phase) VALUES('$idSession','$copy_project','$copy_task','$changename','$copy_date','$copy_size','$copy_extension','$copy_comments','$copy_upload','0','2','3','$copy_vc_version','$copy_id','0')";
		Util::connectSql("$tmpquery");
		$tmpquery = $tableCollab["files"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);
	}
	
	//Insert details into Database
	if ($docopy == "true") {
		Util::uploadFile(".", $_FILES['upload']['tmp_name'], $path);
		//$size = Util::fileInfoSize("$path");
		//$dateFile = Util::getFileDate("$path");
		$chaine = strrev("$path");
		$tab = explode(".",$chaine);
		$extension = strtolower(strrev($tab[0]));
	}
	
	$newversion = $fileDetail->fil_vc_version[0] + $change_file_version;
	if ($docopy == "true") {
		$name = "$upload_name";
		$tmpquery = "UPDATE ".$tableCollab["files"]." SET date='$dateheure',size='$size',comments='$c',status='$statusField',vc_version='$newversion' WHERE id = '$id'";
		Util::connectSql("$tmpquery");
		Util::headerFunction("clientfiledetail.php?id=".$fileDetail->fil_id[0]."&msg=addFile&".session_name()."=".session_id());
		exit;
	}	
}
//---------------------------------------------------------------------------------------------------



//---------------------------------------------------------------------------------------------------
//Add new revision code

if ($action == "add") {
if ($maxCustom != "") {
$maxFileSize = $maxCustom;
}
	if ($_FILES['upload']['size']!=0) {
		$taille_ko=$_FILES['upload']['size']/1024;
	} else {
		$taille_ko=0;
	}
	if ($_FILES['upload']['name'] == "") {
		$error3.=$strings["no_file"]."<br/>";
	}
	if ($_FILES['upload']['size']>$maxFileSize) {
		if($maxFileSize!=0) {
			$taille_max_ko=$maxFileSize/1024;
		}
		$error3.=$strings["exceed_size"]." ($taille_max_ko $byteUnits[1])<br/>";
	}
	
	$upload_name="$filename";
	//Add version and revision at the end of a file name but before the extension.
	$upload_name = str_replace("."," v$oldversion r$revision.", $upload_name);
	
	$extension= strtolower( substr( strrchr($upload_name, ".") ,1) );
	
	if ($allowPhp == "false") {
		$send = "";
	if ($_FILES['upload']['name'] != "" && ($extension=="php" || $extension=="php3" || $extension=="phtml")) {
			$error3.=$strings["no_php"]."<br/>";
			$send = "false";
		}
	}

	if ($_FILES['upload']['name'] != "" && $_FILES['upload']['size']<$maxFileSize && $_FILES['upload']['size']!=0 && $send != "false") {
	$docopy = "true";
	}
	
	//Insert details into Database
	if ($docopy == "true") {
		$comments = Util::convertData($comments);
		$tmpquery = "INSERT INTO ".$tableCollab["files"]."(owner,project,task,comments,upload,published,status,vc_status,vc_parent,phase) VALUES('$idSession','$project','$task','$c','$dateheure','0','2','0','$parent','0')";
		Util::connectSql("$tmpquery");
		$tmpquery = $tableCollab["files"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);
	}

	if ($task != "0") {
		if ($docopy == "true") {
			Util::uploadFile("files/$project/$task", $_FILES['upload']['tmp_name'], $upload_name);
			$size = Util::fileInfoSize("../files/$project/$task/$upload_name");
			//$dateFile = Util::getFileDate("../files/$project/$task/$upload_name");
			$chaine = strrev("../files/$project/$task/$upload_name");
			$tab = explode(".",$chaine);
			$extension = strtolower(strrev($tab[0]));
		}
	} else {
		if ($docopy == "true") {
			Util::uploadFile("files/$project", $_FILES['upload']['tmp_name'], $upload_name);
			$size = Util::fileInfoSize("../files/$project/$upload_name");
			//$dateFile = Util::getFileDate("../files/$project/$upload_name");
			$chaine = strrev("../files/$project/$upload_name");
			$tab = explode(".",$chaine);
			$extension = strtolower(strrev($tab[0]));
		}
	}
	
	if ($docopy == "true") {
		$name = "$upload_name";
		$tmpquery = "UPDATE ".$tableCollab["files"]." SET name='$name',date='$dateheure',size='$size',extension='$extension',vc_version='$oldversion' WHERE id = '$num'";
		Util::connectSql("$tmpquery");
		Util::headerFunction("clientfiledetail.php?id=$sendto&msg=addFile&".session_name()."=".session_id());
		exit;
	}
}

//---------------------------------------------------------------------------------------------------

$bouton[4] = "over";
$titlePage = $strings["document"];
include ("include_header.php");

// TABLE 1 - FILE DETAILS TABLE.
echo "<table cellpadding=20 cellspacing=0 border=0 width=\"100%\">
 <tr>
   <td><h1 class=\"heading\">".$strings["document"]."</h1>
	<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"4\" cols=\"4\">
	<tr><td width=\"40%\"><table cellspacing=\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\">";

	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["type"]." : </td><td><img src=\"../interface/icones/$type\" border=\"0\" alt=\"\"></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["name"]." : </td><td>".$fileDetail->fil_name[0]."</td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["vc_version"]." :</td><td>".$fileDetail->fil_vc_version[0]."</td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["ifc_last_date"]." :</td><td>".$fileDetail->fil_date[0]."</td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["size"].":</td><td>".Util::convertSize($fileDetail->fil_size[0])."</td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["owner"]." :</td><td><a href=\"contactdetail.php?$transmitSid&id=".$fileDetail->fil_mem_id[0]."\">".$fileDetail->fil_mem_name[0]."</a> (<a href=\"mailto:".$fileDetail->fil_mem_email_work[0]."\">".$fileDetail->fil_mem_login[0]."</a>)</td></tr>";

	if ($fileDetail->fil_comments[0] != "") {
		echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["comments"]." :</td><td>".nl2br($fileDetail->fil_comments[0])."</td></tr>";
	}


	$idStatus = $fileDetail->fil_status[0];
	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["approval_tracking"]." :</td><td><a href=\"docitemapproval.php?$transmitSid&id=".$fileDetail->fil_id[0]."\">$statusFile[$idStatus]</a></td></tr>";

	if ($fileDetail->fil_mem2_id[0] != "") {
		echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["approver"]." :</td><td><a href=\"userdetail.php?$transmitSid&id=".$fileDetail->fil_mem2_id[0]."\">".$fileDetail->fil_mem2_name[0]."</a> (<a href=\"mailto:".$fileDetail->fil_mem2_email_work[0]."\">".$fileDetail->fil_mem2_login[0]."</a>)&nbsp;</td></tr>";
		echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["approval_date"]." :</td><td>".$fileDetail->fil_date_approval[0]."&nbsp;</td></tr>";
	}

	if ($fileDetail->fil_comments_approval[0] != "") {
		echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["approval_comments"]." :</td><td>".nl2br($fileDetail->fil_comments_approval[0])."&nbsp;</td></tr>";
	}

	//------------------------------------------------------------------
	$tmpquery = "WHERE fil.id = '$id' OR fil.vc_parent = '$id' AND fil.vc_status = '3' ORDER BY fil.date DESC";

	$listVersions = new request();
	$listVersions->openFiles($tmpquery);
	$comptListVersions = count($listVersions->fil_vc_parent);

	echo"<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\">".$strings["ifc_version_history"]." :</td><td><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue2\"><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></td><td><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>
	<tr class=\"odd\"><td valign=\"top\" colspan=\"2\" align=\"center\"><table width=\"550\" cellpadding=\"0\" cellspacing=\"0\" class=\"tableRevision\">";
	for ($i=0;$i<$comptListVersions;$i++) {

	//Sort odds and evens for bg color
	if ($i == "0") {
		$vclass = "new";
	} else {
		$vclass = "old";
	}
	
	echo "<tr class=\"$vclass\" height=\"20\" onmouseover=\"this.style.backgroundColor='".$highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><td>&nbsp;</td>

	<td>".$strings["vc_version"]." : ".$listVersions->fil_vc_version[$i]."</td>
	<td>$displayname&nbsp;&nbsp;";
	
	if ($listVersions->fil_task[$i] != "0") {
		if (file_exists("../files/".$listVersions->fil_project[$i]."/".$listVersions->fil_task[$i]."/".$listVersions->fil_name[$i])) {
				echo " <a href=\"clientaccessfile.php?$transmitSid&mode=view&id=".$listVersions->fil_id[$i]."\">".$strings["view"]."</a>";
				$folder = $listVersions->fil_project[$i]."/".$listVersions->fil_task[$i];
				$existFile = "true";
		}
	} else {
		if (file_exists("../files/".$listVersions->fil_project[$i]."/".$listVersions->fil_name[$i])) {
				echo " <a href=\"clientaccessfile.php?$transmitSid&mode=view&id=".$listVersions->fil_id[$i]."\">".$strings["view"]."</a>";
				$folder = $listVersions->fil_project[$i];
				$existFile = "true";
		}
	}
	if ($existFile == "true") {
		echo " <a href=\"clientaccessfile.php?$transmitSid&mode=download&id=".$listVersions->fil_id[$i]."\">".$strings["save"]."</a>";
	} else {
		echo $strings["missing_file"];
	}
	
	echo"</td><td>".$strings["date"]." : ".$listVersions->fil_date[$i]."</td></tr>";
	}
	echo"</table></td></tr><br/>";
	echo "</table></td></tr>
	</table>					  
  </td>
 </tr>
</table>";

if ($peerReview == "true") {
// Table 2 - LIST OF REVIEWS TABLE.
echo "<table cellpadding=20 cellspacing=0 border=0 width=\"100%\">
 <tr>
   <td><h1 class=\"heading\">".$strings["ifc_revisions"]."</h1>
	<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\">
	<tr height=\"15\"><th width=\"100%\" class=\"ModuleColumnHeaderSort\"><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></th></tr>
	<tr><td width=\"40%\"><table cellpadding =\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\">";

	echo"<tr class=\"odd\"><td align=\"center\"><br/>";

	$tmpquery = "WHERE fil.vc_parent = '$id' AND fil.vc_status != '3' ORDER BY fil.date";
	$listReviews = new request();
	$listReviews->openFiles($tmpquery);
	$comptListReviews = count($listReviews->fil_vc_parent);
	for ($i=0;$i<$comptListReviews;$i++) {

	//Sort odds and evens for bg color
	if (!($i%2)) {
		$class = "odd";
		$highlightOff = $oddColor;
	} else {
		$class = "odd";
		$highlightOff = $oddColor;
	}
	
	//Calculate a revision number for display for each listing
	$displayrev = $i + 1;
	
	echo "<table width=\"550\" cellpadding=\"0\" cellspacing=\"0\" class=\"tableRevision\" onmouseover=\"this.style.backgroundColor='".$highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\">
	<tr class=\"reviewHeader\" height=\"25\"><td>";
	echo"&nbsp;</td>
	<td colspan=\"3\">$displayname&nbsp;&nbsp;";
	if ($listReviews->fil_task[$i] != "0") {
		if (file_exists("../files/".$listReviews->fil_project[$i]."/".$listReviews->fil_task[$i]."/$listReviews->fil_name[$i]")) {
			echo "<a href=\"clientaccessfile.php?$transmitSid&mode=view&id=".$listReviews->fil_id[$i]."\">".$strings["view"]."</a>";
			$folder = $listReviews->fil_project[$i]."/".$listReviews->fil_task[$i];
			$existFile = "true";
		}
	} else {
		if (file_exists("../files/".$listReviews->fil_project[$i]."/".$listReviews->fil_name[$i])) {
			echo "<a href=\"clientaccessfile.php?$transmitSid&mode=view&id=".$listReviews->fil_id[$i]."\">".$strings["view"]."</a>";
			$folder = $listReviews->fil_project[$i];
			$existFile = "true";
		}
	}
	if ($existFile == "true") {
		echo " <a href=\"clientaccessfile.php?$transmitSid&mode=download&id=".$listReviews->fil_id[$i]."\">".$strings["save"]."</a>";
	} else {
		echo $strings["missing_file"];
	}
	echo"</td><td align=\"right\">Revision: $displayrev&nbsp;&nbsp;</td></tr>
	<tr height=\"30\"><td>&nbsp;</td><td>".$strings["ifc_revision_of"]." : ".$listReviews->fil_vc_version[$i]."</td><td width=\"150\">".$strings["owner"]." : ".$listReviews->fil_mem_name[$i]."</td><td>".$strings["date"]." : ".$listReviews->fil_date[$i]."</td></tr>
	<tr><td>&nbsp;</td><td colspan=\"4\">".$strings["comments"]." : ".$listReviews->fil_comments[$i]."</td></tr>
	</table><br/>";
	}
	if($i==0){echo"<tr class=\"odd\"><td></td><td>".$strings["ifc_no_revisions"]."</td></tr>";}
	echo "</table></td></tr>
	</table>					  
  </td>
 </tr>
</table>";

// Table 3 - ADD REVIEW TABLE.
echo "<table cellpadding=20 cellspacing=0 border=0 width=\"100%\">

 <tr>
   <td><h1 class=\"heading\">".$strings["ifc_add_revision"]."</h1>
	<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\">
	<tr height=\"15\"><th width=\"100%\" class=\"ModuleColumnHeaderSort\"><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></th></tr>
	<tr><td width=\"40%\" class=\"$class\"><table cellspacing=\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\">";

	echo "<a name=\"filedetailsAnchor\"></a>";
	echo "<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../projects_site/clientfiledetail.php?action=add&id=".$fileDetail->fil_id[0]."&".session_name()."=".session_id()."#filedetailsAnchor\" name=\"filedetailsForm\" enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000000\"><input type=\"hidden\" name=\"maxCustom\" value=\"".$projectDetail->pro_upload_max[0]."\">";

	//Add one to the number of current revisions
	$revision = $displayrev+1;

	echo "<input value=\"".$fileDetail->fil_id[0]."\" name=\"sendto\" type=\"hidden\">
	<input value=\"".$fileDetail->fil_id[0]."\" name=\"parent\" type=\"hidden\">
	<input value=\"$revision\" name=\"revision\" type=\"hidden\">
	<input value=\"".$fileDetail->fil_vc_version[0]."\" name=\"oldversion\" type=\"hidden\">
	<input value=\"".$fileDetail->fil_project[0]."\" name=\"project\" type=\"hidden\">
	<input value=\"".$fileDetail->fil_task[0]."\" name=\"task\" type=\"hidden\">
	<input value=\"".$fileDetail->fil_name[0]."\" name=\"filename\" type=\"hidden\">
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">* ".$strings["upload"]." :</td><td><input size=\"44\" style=\"width: 400px\" name=\"upload\" type=\"FILE\"></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["comments"]." :</td><td><textarea rows=\"3\" style=\"width: 400px; height: 50px;\" name=\"c\" cols=\"43\">$c</textarea></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["save"]."\"><br/><br/>$error3</td></tr></form>";
	echo "</table></td></tr>
	</table>					  
  </td>
 </tr>
</table>";
}

// Table 4
if ($fileDetail->fil_owner[0]==$idSession){
echo "<table cellpadding=20 cellspacing=0 border=0 width=\"100%\">
 <tr>
   <td><h1 class=\"heading\">".$strings["ifc_update_file"]."</h1>
	<table cellspacing=\"0\" width=\"90%\" border=\"0\" cellpadding=\"3\" cols=\"4\">
	<tr height=\"15\"><th width=\"100%\" class=\"ModuleColumnHeaderSort\"><img src=\"../themes/".THEME."/spacer.gif\" width=\"1\" height=\"1\" border=\"0\"></th></tr>
	<tr><td width=\"40%\" class=\"$class\">
<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../projects_site/clientfiledetail.php?action=update&id=".$fileDetail->fil_id[0]."&".session_name()."=".session_id()."#filedetailsAnchor\" name=\"filedetailsForm\" enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000000\"><input type=\"hidden\" name=\"maxCustom\" value=\"".$projectDetail->pro_upload_max[0]."\">
<table cellpadding =\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\">";

	echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\"></td><td class=\"odd\">".$strings["version_increm"]."<br/>
	<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
	<tr><td align=\"right\">0.01</td><td width=\"30\" align=\"right\"><input name=\"change_file_version\" type=\"radio\" value=\"0.01\"></td></tr>
	<tr><td align=\"right\">0.1</td><td width=\"30\" align=\"right\"><input name=\"change_file_version\" type=\"radio\" value=\"0.1\" checked></td></tr>
	<tr><td align=\"right\">1.0</td><td width=\"30\" align=\"right\"><input name=\"change_file_version\" type=\"radio\" value=\"1.0\"></td></tr>
	</table>
	</td></tr>";
	
	echo"<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["status"]." :</td><td><select name=\"statusField\">";
	$comptSta = count($statusFile);

	for ($i=0;$i<$comptSta;$i++) {
		if ($fileDetail->fil_status[0] == $i) {
			echo "<option value=\"$i\" selected>$statusFile[$i]</option>";
		} else {
			echo "<option value=\"$i\">$statusFile[$i]</option>";
		}
	}
	echo"</select></td></tr>";
	echo"<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">* ".$strings["upload"]." :</td><td><input size=\"44\" style=\"width: 400px\" name=\"upload\" type=\"FILE\"></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["comments"]." :</td><td><textarea rows=\"3\" style=\"width: 400px; height: 50px;\" name=\"c\" cols=\"43\">$c</textarea></td></tr>
	<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["ifc_update_file"]."\"><br/><br/>$error4</td></tr></form>";
	echo "</table></td></tr>
	</table>					  
  </td>
 </tr>

</table>";

}

include ("include_footer.php");
?>