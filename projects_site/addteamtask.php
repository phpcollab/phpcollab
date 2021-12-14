<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../projects_site/addteamtask.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Management
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

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$setTitle .= " : " . $strings["add_task"];

try {
    $tasks = $container->getTasksLoader();
    $assignments = $container->getAssignmentsManager();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

//case add task
$id = $request->query->get('id');

$strings = $GLOBALS["strings"];
$priority = $GLOBALS["priority"];

if (empty($request->query->get('id'))) {

    //case add task
    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->request->get('action') == "add") {

                    //concat values from date selector and replace quotes by html code in name
                    $taskName = phpCollab\Util::convertData($request->request->get('task_name'));
                    $description = phpCollab\Util::convertData($request->request->get('description'));
                    $comments = phpCollab\Util::convertData($request->request->get('comments'));
                    $priority = $request->request->get('priority');
                    $startDate = $request->request->get('start_date');
                    $dueDate = $request->request->get('due_date');
                    $assignedTo = $request->request->get('assigned_to');
                    $projectId = $request->request->get('project_id');

                    try {
                        $newTask = $tasks->addTask(
                            $projectId,                 // projectId
                            $session->get("id"),        // owner
                            $taskName,                  // name
                            $description,               // description
                            0,                          // assignedTo
                            2,                          // status
                            $priority,                  // priority
                            $startDate,                 // startDate
                            $dueDate,                   // dueDate
                            0,                          // estimatedTime
                            0,                          // actualTime
                            $comments,                  // comments
                            0,                          // published
                            0,                          // completion
                            0,                          // parentPhase
                            0,                          // invoicing
                            0                           // workedHours
                        );

                        $assignments->addAssignment($newTask["tas_id"], $session->get("id"), $assignedTo, $dateheure);

                        //send task assignment mail if notifications = true
                        if ($notifications == "true") {
                            $tasks->sendClientAddTaskNotification($newTask);
                        }

                        //create task sub-folder if filemanagement = true
                        if ($fileManagement == "true") {
                            phpCollab\Util::createDirectory("../files/{$session->get("project")}/" . $newTask["tas_id"]);
                        }

                        $session->getFlashBag()->add(
                            'message',
                            $strings["team_task_created_success"]
                        );

                        phpCollab\Util::headerFunction("showallteamtasks.php");
                    } catch (Exception $e) {
                        $logger->error('Project Site (add team task)', ['Exception message', $e->getMessage()]);
                        $error = $strings["action_not_allowed"];
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Project Site: Add team task',
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
            ]);
        } catch (Exception $e) {
            $logger->critical('Exception', ['Error' => $e->getMessage()]);
            $msg = 'permissiondenied';
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
<input type="hidden" name="project_id" value="{$session->get("project")}" />
<input type="hidden" value="$publishTask" name="publish" />
<input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
<table class="nonStriped">
	<tr>
	    <th colspan="2">{$strings["add_task"]}</th>
    </tr>
	<tr>
		<th>*&nbsp;{$strings["name"]} :</th>
		<td><input size="44" value="{$request->request->get('task_name')}" style="width: 400px" name="task_name" maxlength="100" type="TEXT" /></td>
	</tr>
	<tr>
		<th style="vertical-align: top">{$strings["description"]} :</th>
		<td><textarea rows="10" style="width: 400px; height: 160px;" name="description" cols="47">{$request->request->get('description')}</textarea></td>
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

if (empty($request->request->get('start_date'))) {
    $request->request->set('start_date', $GLOBALS["date"]);
}
if (empty($request->request->get('due_date'))) {
    $request->request->set('due_date', "--");
}

echo <<< STARTDATE
 <tr>
			<th>{$strings["start_date"]} :</th>
			<td><input type="text" name="start_date" id="start_date" size="20" value="{$request->request->get('start_date')}" />
				<input type="button" value=" ... " id="trigStartDate" />
				<script type="text/javascript">
				    Calendar.setup({
				        inputField     :    "start_date",
				        button         :    "trigStartDate",
				        $calendar_common_settings
				    })
				</script>
			</td>
	</tr>
STARTDATE;

echo <<< DUEDATE
	<tr>
		<th>{$strings["due_date"]} :</th>
		<td>
			<input type="text" name="due_date" id="due_date" size="20" value="{$request->request->get('due_date')}" />
			<input type="button" value=" ... " id="trigDueDate" />
			<script type="text/javascript">
			    Calendar.setup({
			        inputField     :    "due_date",
			        button         :    "trigDueDate",
			        $calendar_common_settings
			    })
			</script>
		</td>
	</tr>
DUEDATE;

echo <<< COMMENT
	<tr>
		<th style="vertical-align: top">{$strings["comments"]} :</th>
		<td>
			<textarea rows='10' style='width: 400px; height: 160px;' name='comments' cols='47'>{$request->request->get('comments')}</textarea>
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
