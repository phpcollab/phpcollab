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

use phpCollab\Assignments\Assignments;
use phpCollab\Tasks\Tasks;

$checkSession = "true";
include '../includes/library.php';

$tasks = new Tasks();
$assignments = new Assignments();

//case add task
$id = (isset($_GET["id"])) ? $_GET["id"] : null;

$tableCollab = $GLOBALS["tableCollab"];
$strings = $GLOBALS["strings"];
$priority = $GLOBALS["priority"];

if (empty($_GET["id"])) {

    //case add task
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST["action"] == "add") {

            //concat values from date selector and replace quotes by html code in name
            $taskName = phpCollab\Util::convertData($_POST["task_name"]);
            $description = phpCollab\Util::convertData($_POST["description"]);
            $comments = phpCollab\Util::convertData($_POST["comments"]);
            $priority = $_POST["priority"];
            $startDate = $_POST["start_date"];
            $dueDate = $_POST["due_date"];
            $publshed = $_POST["published"];
            $assignedTo = $_POST["assigned_to"];
            $projectId = $_POST["project_id"];

            try {
                $newTask = $tasks->addTask($projectId, $taskName, $description, $idSession, 0, 2, $priority, $startDate, $dueDate, 0, 0, $comments, $publshed, 0);

                $assignments->addAssignment($newTask["tas_id"], $idSession, $assignedTo, $dateheure);

                //send task assignment mail if notifications = true
                if ($notifications == "true") {
                    $tasks->sendClientAddTaskNotification($newTask);
                }

                //create task sub-folder if filemanagement = true
                if ($fileManagement == "true") {
                    phpCollab\Util::createDirectory("../files/$projectSession/" . $newTask["tas_id"]);
                }
                phpCollab\Util::headerFunction("showallteamtasks.php");
            }
            catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

$bodyCommand = "onload='document.etDForm.tn.focus();'";

$bouton[2] = "over";
$titlePage = $strings["add_task"];
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include 'include_header.php';

echo <<<FORM
<form method="POST" action="../projects_site/addteamtask.php" name="etDForm">
FORM;

$publishTask = ($autoPublishTasks === false ? 0 : 1);

echo <<< HTML
<input type="hidden" name="owner" value="{$projectDetail->pro_owner[0]}" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="assigned_to" value="0" />
<input type="hidden" name="status" value="2" />
<input type="hidden" name="completion" value="0" />
<input type="hidden" name="project_id" value="$projectSession" />
<input type="hidden" value="{$publishTask}" name="publish" />
<table class="nonStriped">
	<tr>
	    <th colspan="2">{$strings["add_task"]}</th>
    </tr>
	<tr>
		<th>*&nbsp;{$strings["name"]} :</th>
		<td><input size="44" value="{$_POST["task_name"]}" style="width: 400px" name="task_name" maxlength="100" type="TEXT" /></td>
	</tr>
	<tr>
		<th style="vertical-align: top">{$strings["description"]} :</th>
		<td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$_POST["description"]}</textarea></td>
	</tr>

	<tr><th>{$strings["priority"]} :</th>
	<td><select name='priority'>
HTML;


$comptPri = count($priority);

for ($i = 0; $i < $comptPri; $i++) {
    if ($taskDetail->tas_priority[0] == $i) {
        echo "<option value='$i' selected>$priority[$i]</option>";
    } else {
        echo "<option value='$i'>$priority[$i]</option>";
    }
}

echo "</select></td></tr>";

if (empty($_POST["start_date"])) {
    $_POST["start_date"] = $GLOBALS["date"];
}
if (empty($_POST["due_date"])) {
    $_POST["due_date"] = "--";
}

echo <<< STARTDATE
 <tr>
			<th>{$strings["start_date"]} :</th>
			<td><input type="text" name="start_date" id="start_date" size="20" value="{$_POST["start_date"]}" />
				<input type="button" value=" ... " id="trigStartDate" />
				<script type="text/javascript">
				    Calendar.setup({
				        inputField     :    "start_date",
				        button         :    "trigStartDate",
				        {$calendar_common_settings}
				    })
				</script>
			</td>
	</tr>
STARTDATE;

echo <<< DUEDATE
	<tr>
		<th>{$strings["due_date"]} :</th>
		<td>
			<input type="text" name="due_date" id="due_date" size="20" value="{$_POST["due_date"]}" />
			<input type="button" value=" ... " id="trigDueDate" />
			<script type="text/javascript">
			    Calendar.setup({
			        inputField     :    "due_date",
			        button         :    "trigDueDate",
			        {$calendar_common_settings}
			    })
			</script>
		</td>
	</tr>
DUEDATE;

echo <<< COMMENT
	<tr>
		<th style="vertical-align: top">{$strings["comments"]} :</th>
		<td>
			<textarea rows='10' style='width: 400px; height: 160px;' name='comments' cols='47'>{$_POST["comments"]}</textarea>
		</td>
	</tr>
	<tr><th>&nbsp;</th>
		<td><input type='SUBMIT' value='{$strings["save"]}' /></td>
	</tr>
COMMENT;

echo <<< CLOSE
</table>
</form>
<h4 class="note">{$strings["client_add_task_note"]}</h4>
CLOSE;

include("include_footer.php");
