<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../projects_site/addteamtask.php
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
** FILE: addteamtask.php
**
** DESC: Screen: give the ability to a client to add a new task to a team
**
** HISTORY:
** 	04/12/2004	-	added new document info
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
**	03/06/2005	-	xhtml
**  25/04/2006  -   replaced JavaScript Calendar functions
**  11/04/2007  -   added check for $autoPublishTasks
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "true";
include("../includes/library.php");

//case add task
if ($id == "") 
{

	//case add task
	if ($action == "add") 
	{

		//concat values from date selector and replace quotes by html code in name
		$tn = Util::convertData($tn);
		$d = Util::convertData($d);
		$c = Util::convertData($c);

		$tmpquery1 = "INSERT INTO ".$tableCollab["tasks"]."(project,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion) VALUES('$projectSession','$tn','$d','$idSession','0','2','$pr','$sd','$dd','$etm','$atm','$c','$dateheure','$pub','0')";
		Util::connectSql("$tmpquery1");
		$tmpquery = $tableCollab["tasks"];
		Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);

		$tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(task,owner,assigned_to,assigned) VALUES('$num','$idSession','$at','$dateheure')";
		Util::connectSql("$tmpquery2");

		//send task assignment mail if notifications = true
		if ($notifications == "true") 
		{
				include("../tasks/noti_clientaddtask.php");
		}

		//create task sub-folder if filemanagement = true
		if ($fileManagement == "true") 
		{
			Util::createDirectory("../files/$projectSession/$num");
		}
		
		Util::headerFunction("showallteamtasks.php?".session_name()."=".session_id());
	}

}

$bodyCommand="onload='document.etDForm.tn.focus();'";

$bouton[2] = "over";
$titlePage = $strings["add_task"];
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include ("include_header.php");

echo "<form accept-charset='UNKNOWN' method='POST' action='../projects_site/addteamtask.php?project=$projectSession&action=add&".session_name()."=".session_id()."#etDAnchor' name='etDForm' enctype='application/x-www-form-urlencoded'>";

echo "
<table cellpadding='3' cellspacing='0' border='0'>
	<tr><th colspan='2'>".$strings["add_task"]."</th></tr>
	<tr>
		<th>*&nbsp;".$strings["name"]." :</th>
		<td><input size='44' value='$tn' style='width: 400px' name='tn' maxlength='100' type='TEXT' /></td>
	</tr>
	<tr>
		<th>".$strings["description"]." :</th>
		<td><textarea rows='10' style='width: 400px; height: 160px;' name='d' cols='47'>$d</textarea></td>
	</tr>

	<input type='hidden' name='owner' value='".$projectDetail->pro_owner[0]."' />
	<input type='hidden' name='at' value='0' />
	<input type='hidden' name='st' value='2' />
	<input type='hidden' name='completion' value='0' />
	<input type='hidden' value='".($autoPublishTasks === false ? '0' : '1')."' name='pub' />
	<tr><th>".$strings["priority"]." :</th>
	<td><select name='pr'>";

$comptPri = count($priority);

for ($i=0;$i<$comptPri;$i++) {
	if ($taskDetail->tas_priority[0] == $i) {
		echo "<option value='$i' selected>$priority[$i]</option>";
	} else {
		echo "<option value='$i'>$priority[$i]</option>";
	}
}

echo "</select></td></tr>";

if ($sd == "") {
	$sd = $date;
}
if ($dd == "") {
	$dd = "--";
}

echo "<tr>
			<th>".$strings["start_date"]." :</th>
			<td><input type='text' name='sd' id='start_date' size='20' value='$sd' />
				<input type='button' value=' ... ' id='trigStartDate' />
				<script type='text/javascript'>
				    Calendar.setup({
				        inputField     :    'start_date',
				        button         :    'trigStartDate',
				        $calendar_common_settings
				    });
				</script>
			</td>
	</tr>
	<tr>
		<th>".$strings["due_date"]." :</th>
		<td>
			<input type='text' name='dd' id='due_date' size='20' value='$dd' />
			<input type='button' value=' ... ' id='trigDueDate' />
			<script type='text/javascript'>
			    Calendar.setup({
			        inputField     :    'due_date',
			        button         :    'trigDueDate',
			        $calendar_common_settings
			    });
			</script>
		</td>
	</tr>
	<tr>
		<th>".$strings["comments"]." :</th>
		<td>
			<textarea rows='10' style='width: 400px; height: 160px;' name='c' cols='47'>$c</textarea>
		</td>
	</tr>
	<tr><th>&nbsp;</th>
		<td><input type='SUBMIT' value='".$strings["save"]."' /></td>
	</tr>
</table>
</form>
<p class='note'>".$strings["client_add_task_note"]."</p>";

include ("include_footer.php");
?>