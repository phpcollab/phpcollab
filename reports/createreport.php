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

use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$includeCalendar = true;

$teams = new Teams();
$organizations = new Organizations();
$projects = new Projects();


include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../reports/listreports.php?", $strings["reports"], "in"));
$blockPage->itemBreadcrumbs($strings["create_report"]);
$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

$block1->form = "customsearch";
$block1->openForm("../reports/resultsreport.php", null, $csrfHandler);

$block1->heading($strings["create_report"]);

$block1->openContent();
$block1->contentTitle($strings["report_intro"]);

echo "<tr class='odd'><td class='leftvalue'>" . $strings["clients"] . " :</td><td>";

if ($clientsFilter == "true" && $session->get("profile") == "2") {
    $teamMember = "false";
    $teamsList = $teams->getTeamByMemberId($session->get("id"));

    if (empty($teamsList)) {
        $listClients = "false";
    } else {
        $clientsOk = [];

        foreach ($teamsList as $team) {
            array_push($clientsOk, $team["tea_org2_id"]);
        }

        if (empty($clientsOk)) {
            $listClients = "false";
        } else {
            $clientsOk = implode (", ", $clientsOk);

            $listOrganizations = $organizations->getFilteredOrganizations($clientsOk, 'org.name');
        }
    }
} elseif ($clientsFilter == "true" && $session->get("profile") == "1") {
    $listOrganizations = $organizations->getOrganizationsByOwner($session->get("id"), 'org.name');
} else {
    $listOrganizations = $organizations->getAllOrganizations('org.name');
}

echo <<<HTML
    <select name="S_ORGSEL[]" size="4" multiple>
        <option selected value="ALL">{$strings["select_all"]}</option>
HTML;

foreach ($listOrganizations as $org) {
    echo <<<OPTION
        <option value="{$org["org_id"]}">{$org["org_name"]}</option>
OPTION;
}

echo <<<HTML
    </select></td>
	</tr>
	<tr class="odd">
	    <td class="leftvalue">{$strings["projects"]} :</td>
	    <td>
HTML;

$listProjects = $projects->getProjectList($session->get("id"), 'active', null, null, 'pro.name');

echo <<<HTML
    <select name="S_PRJSEL[]" size="4" multiple>
        <option selected value="ALL">{$strings["select_all"]}</option>

HTML;

foreach ($listProjects as $project) {
    echo <<<OPTION
        <option value="{$project["pro_id"]}">{$project["pro_name"]}</option>
OPTION;
}

echo <<<HTML
            </select></td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">{$strings["assigned_to"]} :</td>
        <td>
HTML;

if ($demoMode == "true") {
    $listMembers = $members->getMembersByProfileIn('0,1,3,5', null, 'mem.name');
} else {
    $listMembers = $members->getMembersByProfileIn('0,1,3,5', 2, 'mem.name');
}

echo <<<HTML
        <select name="S_ATSEL[]" size="4" multiple>
            <option selected value="ALL">{$strings["select_all"]}</option>
		    <option value="0">{$strings["unassigned"]}</option>
HTML;

foreach ($listMembers as $listMember) {
    echo "<option value=\"" . $listMember["mem_id"] . "\">" . $listMember["mem_login"];

    if ($listMember["mem_profil"] == "3") {
        echo " (" . $strings["client_user"] . ")";
    }

    echo "</option>";
}

echo <<<HTML
        </select></td>
    </tr>
	<tr class="odd">
		<td class="leftvalue">{$strings["due_date"]} :</td>
        <td>
HTML;

$theme = THEME;
echo <<<HTML
			<table class="nonStriped">
				<tr>
					<td class="infovalue"><input checked name="S_DUEDATE" type="radio" value="ALL"></td>
					<td>{$strings["all_dates"]}</td>
				</tr>
				<tr>
					<td class="infovalue"><input  name="S_DUEDATE" type="radio" value="DATERANGE"></td>
					<td>{$strings["between_dates"]}</td>
				</tr>
            </table>

            <table class="nonStriped">
				<tr>
				    <td style="width: 18px;"></td>
					<td style="width: 200px;" class="infoValue" noWrap><input type="text" name="S_SDATE" id="dueDate_start" size="20" value=""><input type="button" value=" ... " id="trigDueDateStart"></td>
				</tr>
				<tr>
					<td style="width: 18px;">{$strings["and"]}</td>
					<td style="width: 200px;" class="infoValue" noWrap><input type="text" name="S_EDATE" id="dueDate_end" size="20" value=""><input type="button" value=" ... " id="trigDueDateEnd"></td>
				</tr>
            </table>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "dueDate_start",
        button         :    "trigDueDateStart",
        {$calendar_common_settings}
    })
</script>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "dueDate_end",
        button         :    "trigDueDateEnd",
        {$calendar_common_settings}
    })
</script>
			</td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["complete_date"]} :</td>
			<td>
				<table class="nonStriped">
                    <tr>
                        <td style="width: 18px;" class="infovalue"><input checked name="S_COMPLETEDATE" type="radio" value="ALL"></td>
                        <td style="width: 200px;">{$strings["all_dates"]}</td>
                    </tr>
                    <tr>
                        <td style="width: 18px;" class="infovalue"><input  name="S_COMPLETEDATE" type="radio" value="DATERANGE"></td>
                        <td style="width: 200px;">{$strings["between_dates"]}</td>
                    </tr>
				</table>

				<table class="nonStriped">
                    <tr>
                        <td style="width: 18px;"></td>
                        <td class="infoValue" noWrap><input type="text" name="S_SDATE2" id="compDate_start" size="20" value=""><input type="button" value=" ... " id="trigCompDateStart"></td>
                    </tr>
                    <tr>
                        <td style="width: 18px;">&nbsp;{$strings["and"]}&nbsp;</td>
                        <td class="infoValue" noWrap><input type="text" name="S_EDATE2" id="compDate_end" size="20" value=""><input type="button" value=" ... " id="trigCompDateEnd"></td>
                    </tr>
				</table>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "compDate_start",
        button         :    "trigCompDateStart",
        {$calendar_common_settings}
    })
</script>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "compDate_end",
        button         :    "trigCompDateEnd",
        {$calendar_common_settings}
    })
</script>
			</td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["status"]} :</td>
			<td>
HTML;

$comptSta = count($status);

echo <<<HTML
    <select name="S_STATSEL[]" size="4" multiple>
        <option value="ALL" selected>{$strings["select_all"]}</option>
HTML;

for ($i = 0; $i < $comptSta; $i++) {
    echo <<<OPTION
        <option value="{$i}">{$status[$i]}</option>
OPTION;
}

echo <<<HTML
	</select>
			</td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">{$strings["priority"]} :</td>
			<td>
HTML;

$comptPri = count($priority);

echo <<<HTML
		<select name="S_PRIOSEL[]" size="4" multiple>
		    <option value="ALL" selected>{$strings["select_all"]}</option>
HTML;

for ($i = 0; $i < $comptPri; $i++) {
    echo <<<OPTION
	<option value="{$i}">{$priority[$i]}</option>
OPTION;
}


echo <<<HTML
			</td>
		</select>
		</tr>
		<tr class="odd">
			<td class="leftvalue">&nbsp;</td>
			<td><input type="submit" name="Save" value="{$strings["create"]}"></td>
		</tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
