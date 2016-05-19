<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../reports/createreport.php
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
** FILE: createreport.php
**
** DESC: Screen: create a new report
**
** HISTORY:
** 	23/03/2004	-	added new document info
**  23/03/2004  -	new export to pdf by Angel
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/



$checkSession = "true";
include_once '../includes/library.php';
$includeCalendar = true;
include '../themes/' . THEME . '/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?",$strings["reports"],in));
$blockPage->itemBreadcrumbs($strings["create_report"]);
$blockPage->closeBreadcrumbs();

$block1 = new Block();

$block1->form = "customsearch";
$block1->openForm("../reports/resultsreport.php");

$block1->heading($strings["create_report"]);

$block1->openContent();
$block1->contentTitle($strings["report_intro"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["clients"]." :</td><td>";

if ($clientsFilter == "true" && $profilSession == "2")
{
	$teamMember = "false";
	$tmpquery = "WHERE tea.member = '$idSession'";
	$memberTest = new Request();
	$memberTest->openTeams($tmpquery);
	$comptMemberTest = count($memberTest->tea_id);

	if ($comptMemberTest == "0") {
		$listClients = "false";
	} else {

		for ($i=0;$i<$comptMemberTest;$i++)
		{
			$clientsOk .= $memberTest->tea_org2_id[$i];

			if ($comptMemberTest-1 != $i)
			{
				$clientsOk .= ",";
			}
		}

		if ($clientsOk == "")
		{
			$listClients = "false";
		} else {
			$tmpquery = "WHERE org.id IN($clientsOk) AND org.id != '1' ORDER BY org.name";
		}
	}
}
elseif ($clientsFilter == "true" && $profilSession == "1")
{
	$tmpquery = "WHERE org.owner = '$idSession' AND org.id != '1' ORDER BY org.name";
}
else
{
	$tmpquery = "WHERE org.id != '1' ORDER BY org.name";
}

$listOrganizations = new Request();
$listOrganizations->openOrganizations($tmpquery);
$comptListOrganizations = count($listOrganizations->org_id);

echo "	<select name='S_ORGSEL[]' size='4' multiple><option selected value='ALL'>".$strings["select_all"]."</option>";

for ($i=0;$i<$comptListOrganizations;$i++) {
	echo "<option value='".$listOrganizations->org_id[$i]."'>".$listOrganizations->org_name[$i]."</option>";
}

echo "	</select></td></tr>
		<tr class='odd'><td valign='top' class='leftvalue'>".$strings["projects"]." :</td><td>
	 ";

if ($projectsFilter == "true") {
	$tmpquery = "LEFT OUTER JOIN ".$tableCollab["teams"]." teams ON teams.project = pro.id ";
	$tmpquery .= "WHERE pro.status IN(0,2,3) AND teams.member = '$idSession' ORDER BY pro.name";
} else {
	$tmpquery = "WHERE pro.status IN(0,2,3)  ORDER BY pro.name";
}
$listProjects = new Request();
$listProjects->openProjects($tmpquery);
$comptListProjects = count($listProjects->pro_id);

echo "<select name='S_PRJSEL[]' size='4' multiple><option selected value='ALL'>".$strings["select_all"]."</option>";

for ($i=0;$i<$comptListProjects;$i++) {
	echo "<option value='".$listProjects->pro_id[$i]."'>".$listProjects->pro_name[$i]."</option>";
}

echo "	</select></td></tr>
		<tr class='odd'><td valign='top' class='leftvalue'>".$strings["assigned_to"]." :</td><td>
	 ";

if ($demoMode == "true") {
	$tmpquery = "ORDER BY mem.name";
} else {
	$tmpquery = "WHERE mem.id != '2' ORDER BY mem.name";
}

$listMembers = new Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);

echo "	<select name='S_ATSEL[]' size='4' multiple><option selected value='ALL'>".$strings["select_all"]."</option>
		<option value='0'>".$strings["unassigned"]."</option>
	 ";

for ($i=0;$i<$comptListMembers;$i++) {
	echo "<option value=\"".$listMembers->mem_id[$i]."\">".$listMembers->mem_login[$i];

	if ($listMembers->mem_profil[$i] == "3") {
		echo " (".$strings["client_user"].")";
	}

	echo "</option>";
}

echo "	</select></td></tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["due_date"]." :</td>
			<td>
	 ";

echo "			<table border='0' cellpadding='2' cellspacing='0'>
				<tr>
					<td width='16' align='center' class='infovalue'><input checked name='S_DUEDATE' type='radio' value='ALL'></td>
					<td align='left' width='200'>".$strings["all_dates"]."</td>
				</tr>
				<tr>
					<td width='16' align='center' class='infovalue'><input  name='S_DUEDATE' type='radio' value='DATERANGE'></td>
					<td align='left' width='200'>".$strings["between_dates"]."</td>
				</tr>
				</table>

				<table border='0' cellpadding='2' cellspacing='0'>
					<tr><td width='18'><img height='8' src='../themes/".THEME."/spacer.gif' alt='' width='18'></td>
					<td class='infoValue' noWrap><input type='text' name='S_SDATE' id='dueDate_start' size='20' value=''><input type='button' value=' ... ' id='trigDueDateStart'></td>
				</tr>
				<tr>
					<td width='18'>&nbsp;".$strings["and"]."&nbsp;<td class='infoValue' noWrap><input type='text' name='S_EDATE' id='dueDate_end' size='20' value=''><input type='button' value=' ... ' id='trigDueDateEnd'></td>
				</tr>
				</table>
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'dueDate_start',
        button         :    'trigDueDateStart',
        $calendar_common_settings
    });
</script>
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'dueDate_end',
        button         :    'trigDueDateEnd',
        $calendar_common_settings
    });
</script>
			</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["complete_date"]." :</td>
			<td>

				<table border='0' cellpadding='2' cellspacing='0'>
				<tr>
					<td width='16' align='center' class='infovalue'><input checked name='S_COMPLETEDATE' type='radio' value='ALL'></td>
					<td align='left' width='200'>".$strings["all_dates"]."</td>
				</tr>
				<tr>
					<td width='16' align='center' class='infovalue'><input  name='S_COMPLETEDATE' type='radio' value='DATERANGE'></td>
					<td align='left' width='200'>".$strings["between_dates"]."</td>
				</tr>
				</table>

				<table border='0' cellpadding='2' cellspacing='0'>
				<tr>
					<td width='18'><img height='8' src='../themes/".THEME."/spacer.gif' alt='' width='18'></td>
					<td class='infoValue' noWrap><input type='text' name='S_SDATE2' id='compDate_start' size='20' value=''><input type='button' value=' ... ' id='trigCompDateStart'></td>
				</tr>
				<tr>
					<td width=18>&nbsp;".$strings["and"]."&nbsp;<td class='infoValue' noWrap><input type='text' name='S_EDATE2' id='compDate_end' size='20' value=''><input type='button' value=' ... ' id='trigCompDateEnd'></td>
				</tr>
				</table>
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'compDate_start',
        button         :    'trigCompDateStart',
        $calendar_common_settings
    });
</script>
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'compDate_end',
        button         :    'trigCompDateEnd',
        $calendar_common_settings
    });
</script>
			</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["status"]." :</td>
			<td>";

$comptSta = count($status);

echo "		<select name='S_STATSEL[]' size='4' multiple><option value='ALL' selected>".$strings["select_all"]."</option>";

for ($i=0;$i<$comptSta;$i++) {
	echo "		<option value='$i'>$status[$i]</option>";
}

echo "		</select>
			</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>".$strings["priority"]." :</td>
			<td>";

$comptPri = count($priority);

echo "		<select name='S_PRIOSEL[]' size='4' multiple><option value='ALL' selected>".$strings["select_all"]."</option>";

for ($i=0;$i<$comptPri;$i++) {
	echo "	<option value='$i'>$priority[$i]</option>";
}


echo "		</select>
			</td>
		</tr>
		<tr class='odd'>
			<td valign='top' class='leftvalue'>&nbsp;</td>
			<td><input type='submit' name='Save' value='".$strings["create"]."'></td>
		</tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>