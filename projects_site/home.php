<?php
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004
** Path by root: ../projects_site/home.php
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
** FILE: home.php
**
** DESC: Screen: home for client project site
**
** HISTORY:
** 	26/01/2004	-	file comment added
**  27/01/2004  -	session bug resolved
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include("../includes/library.php");

if ($updateProject == "true") {
	$tmpquery = "WHERE tea.member = '$idSession' AND pro.id = '$project' AND pro.status IN(0,2,3) AND pro.published = '0'";
	$testProject = new request();
	$testProject->openTeams($tmpquery);
	$comptTestProject = count($testProject->tea_id);

	if ($comptTestProject != "0") {
		unset($_SESSION['projectSession']);

		$projectSession = $project;

		$_SESSION['projectSession'] = $projectSession;

		Util::headerFunction("home.php?".session_name()."=".session_id());
	} else {
		Util::headerFunction("home.php?changeProject=true&".session_name()."=".session_id());
	}
}

$bouton[0] = "over";
$titlePage = $strings["welcome"]." $nameSession ".$strings["your_projectsite"];
include ("include_header.php");

if ($updateProject != "true" && $changeProject != "true") {
	$tmpquery = "WHERE org.id = '".Util::fixInt($projectDetail->pro_organization[0])."'";
	$clientDetail = new request();
	$clientDetail->openOrganizations($tmpquery);
}

$idStatus = $projectDetail->pro_status[0];
$idPriority = $projectDetail->pro_priority[0];

if ($projectSession == "" || $changeProject == "true") {
	$tmpquery = "WHERE tea.member = '".Util::fixInt($idSession)."' AND pro.status IN(0,2,3) AND pro.published = '0' ORDER BY pro.name";
	$listProjects = new request();
	$listProjects->openTeams($tmpquery);
	$comptListProjects = count($listProjects->tea_id);

	$block1 = new Block();

	$block1->heading($strings["my_projects"]);

	if ($comptListProjects != "0") {
		echo "	<table cellspacing='0' width='90%' border='0' cellpadding='3' cols='4' class='listing'>
				<tr>
					<th class='active'>".$strings["name"]."</th>
					<th>".$strings["organization"]."</th>
					<th>".$strings["priority"]."</th>
					<th>".$strings["status"]."</th>
				</tr>";

		for ($i=0;$i<$comptListProjects;$i++) {

			if (!($i%2)) {
				$class = "odd";
				$highlightOff = $block1->oddColor;
			} else {
				$class = "even";
				$highlightOff = $block1->evenColor;
			}
			
			$idStatus = $listProjects->tea_pro_status[$i];
			$idPriority = $listProjects->tea_pro_priority[$i];
			echo "	<tr class='$class' onmouseover=\"this.style.backgroundColor='".$block1->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\">
						<td width='30%'><a href='home.php?$transmitSid&updateProject=true&project=".$listProjects->tea_pro_id[$i]."'>".$listProjects->tea_pro_name[$i]."</a></td>
						<td>".$listProjects->tea_org2_name[$i]."</td>
						<td>$priority[$idPriority]</td>
						<td>$status[$idStatus]</td>
					</tr>";
		}

		echo "	</table>
				<hr />\n";
	} else {
		echo "	<table cellspacing='0' border='0' cellpadding='2'>
				<tr>
					<td colspan='4' class='listOddBold'>".$strings["no_items"]."</td>
				</tr>
				</table><hr />";
	}

}

if ($projectSession != "" && $changeProject != "true") {

	if (file_exists("../logos_clients/".$clientDetail->org_id[0].".".$clientDetail->org_extension_logo[0])) {
		echo "<img src=\"../logos_clients/".$clientDetail->org_id[0].".".$clientDetail->org_extension_logo[0]."\"><br/><br/>";
	}

	echo "	<table cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th nowrap class='FormLabel'>".$strings["project"]." :</th>
				<td>&nbsp;".$projectDetail->pro_name[0]."</td>
			</tr>
			<tr>
				<th nowrap class='FormLabel' valign='top'>".$strings["description"]." : </th>
				<td>&nbsp;".nl2br($projectDetail->pro_description[0])."</td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>".$strings["status"]." :</th>
				<td>&nbsp;$status[$idStatus]</td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>".$strings["priority"]." :</th>
				<td>&nbsp;$priority[$idPriority]</td>
			</tr>";

	//Dispaly project active phase
	if ($projectDetail->pro_phase_set[0] != "0"){

		echo"	<tr><th nowrap valign='top' class='FormLabel'>".$strings["current_phase"]." :</td><td>";

		$tmpquery = "WHERE pha.project_id = '".$projectDetail->pro_id[0]."' AND status = '1'";
		$currentPhase = new request();
		$currentPhase->openPhases($tmpquery);
		$comptCurrentPhase = count($currentPhase->pha_id);
		if ($comptCurrentPhase == 0){
			echo "".$strings["no_current_phase"]." ";
		} else {
			for ($i=0;$i<$comptCurrentPhase;$i++){
				if ($i != $comptCurrentPhase){
					$pnum = $i+1;
					echo "$pnum.".$currentPhase->pha_name[$i]."  ";
				}
			}
		}

		echo"</td></tr>";

	}
	
	//-------------------------------------------------------------------------------------------

	echo "	<tr>
				<th nowrap class='FormLabel'>".$strings["url_dev"]." :</th>
				<td>&nbsp;<a href='".$projectDetail->pro_url_dev[0]."' target='_blank'>".$projectDetail->pro_url_dev[0]."</a></td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>".$strings["url_prod"]." :</th>
				<td>&nbsp;<a href='".$projectDetail->pro_url_prod[0]."' target='_blank'>".$projectDetail->pro_url_prod[0]."</a></td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>".$strings["created"]." :</th>
				<td>&nbsp;".Util::createDate($projectDetail->pro_created[0],$timezoneSession)."</td>
			</tr>
			<tr>
				<th nowrap class='FormLabel'>".$strings["modified"]." :</th>
				<td>&nbsp;".Util::createDate($projectDetail->pro_modified[0],$timezoneSession)."</td>
			</tr>
			</table>";

	$tmpquery = "WHERE tea.project = '$projectSession' AND tea.member = '".$projectDetail->pro_owner[0]."'";
	$detailContact = new request();
	$detailContact->openTeams($tmpquery);

	if ($detailContact->tea_published[0] == "0" && $detailContact->tea_project[0] == $projectSession) {
		echo "<br/><div>".$strings["contact_projectsite"].", <a href=\"contactdetail.php?$transmitSid&id=".$projectDetail->pro_owner[0]."\">".$projectDetail->pro_mem_name[0]."</a>.</div>";
	}
}

include ("include_footer.php");
?>