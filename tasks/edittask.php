<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root:  ../tasks/edittask.php
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
** FILE: edittask.php
**
** DESC: Screen: edit task page
**
** HISTORY:
**  05/11/2004  -   fixed 1059973
**  12/01/2005  -   cleaned code
**  12/03/2005  -   fixed mssql bug for worked hours
**  19/05/2005  -   fixed and &amp; in link
**  22/05/2005  -   added subtask copy
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
** clean code
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

//case multiple edit tasks
$multi = strstr($id,"**");
if ($multi != "")
{
    Util::headerFunction("../tasks/updatetasks.php?report=$report&project=$project&id=$id&".session_name()."=".session_id());
    exit;
}

if ($id != "" && $action != "update" && $action != "add")
{
    $tmpquery = "WHERE tas.id = '$id'";
    $taskDetail = new Request();
    $taskDetail->openTasks($tmpquery);
    $tmpquery = "WHERE pro.id = '".$taskDetail->tas_project[0]."'";
    $project = $taskDetail->tas_project[0];
}
else
{
    $tmpquery = "WHERE pro.id = '$project'";
}

$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$teamMember = "false";
$tmpquery = "WHERE tea.project = '$project' AND tea.member = '$idSession'";
$memberTest = new Request();
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

if ($teamMember == "false" && $profilSession != "5")
{
    Util::headerFunction("../tasks/listtasks.php?project=$project&msg=taskOwner&".session_name()."=".session_id());
    exit;
}

//case update or copy task
if ($id != "")
{

    //case update or copy task
    if ($action == "update")
    {

        //concat values from date selector and replace quotes by html code in name
        $task_name = Util::convertData($task_name);
        $d = Util::convertData($d);
        $c = Util::convertData($c);

        //case copy task
        if ($docopy == "true")
        {

            //Change task status if parent phase is suspended, complete or not open.
            if ($projectDetail->pro_phase_set[0] != "0")
            {
                $tmpquery = "WHERE pha.project_id = '$project' AND pha.order_num = '$pha'";
                $currentPhase = new Request();
                $currentPhase->openPhases($tmpquery);
                if ($st == 3 && $currentPhase->pha_status[0] != 1)
                {
                    $st = 4;
                }
            }

            if ($compl == "10")
            {
                $st = "1";
            }

            if ($pub == "")
            {
                $pub = "1";
            }

            if ($invoicing == "")
            {
                $invoicing = "0";
            }

            if ($worked_hours == "")
            {
                $worked_hours = "0.00";
            }
            //Insert Task details with or without parent phase
            if ($projectDetail->pro_phase_set[0] != "0")
            {
                $tmpquery1 = "INSERT INTO ".$tableCollab["tasks"]."(project,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion,parent_phase,invoicing,worked_hours) VALUES('$project','$task_name','$d','$idSession','$at','$st','$pr','$start_date','$due_date','$etm','$atm','$c','$dateheure','$pub','$compl','$pha','$invoicing','$worked_hours')";
            }
            else
            {
                $tmpquery1 = "INSERT INTO ".$tableCollab["tasks"]."(project,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion,invoicing,worked_hours) VALUES('$project','$task_name','$d','$idSession','$at','$st','$pr','$start_date','$due_date','$etm','$atm','$c','$dateheure','$pub','$compl','$invoicing','$worked_hours')";
            }
            
            Util::connectSql("$tmpquery1");
            $tmpquery = $tableCollab["tasks"];
            Util::getLastId($tmpquery);
            $num = $lastId[0];
            unset($lastId);


            //subtask copying
            $tmpquery1 = "WHERE task = '$id'";
            $subtaskDetail = new Request();
            $subtaskDetail->openSubtasks($tmpquery1);

            $comptListSubtasks = count($subtaskDetail->subtas_id);

            for ($j=0;$j<$comptListSubtasks;$j++)
            {
                $s_tn = Util::convertData($subtaskDetail->subtas_name[$j]);
                $s_d = Util::convertData($subtaskDetail->subtas_description[$j]);
                $s_ow = $subtaskDetail->subtas_owner[$j];
                $s_at = $subtaskDetail->subtas_assigned_to[$j];
                $s_st = $subtaskDetail->subtas_status[$j];
                $s_pr = $subtaskDetail->subtas_priority[$j];
                $s_sd = $subtaskDetail->subtas_start_date[$j];
                $s_dd = $subtaskDetail->subtas_due_date[$j];
                $s_cd = $subtaskDetail->subtas_complete_date[$j];
                $s_etm = $subtaskDetail->subtas_estimated_time[$j];
                $s_atm = $subtaskDetail->subtas_actual_time[$j];
                $s_c = Util::convertData($subtaskDetail->subtas_comments[$j]);
                $s_published = $subtaskDetail->subtas_published[$j];
                $s_compl = $subtaskDetail->subtas_completion[$j];

                $tmpquery1 = "INSERT INTO ".$tableCollab["subtasks"]."(task,name,description,owner,assigned_to,status,priority,start_date,due_date,complete_date,estimated_time,actual_time,comments,created,assigned,published,completion) VALUES('$num','$s_tn','$s_d','$s_ow','$s_at','$s_st','$s_pr','$s_sd','$s_dd','$s_cd','$s_etm','$s_atm','$s_c','$dateheure','$dateheure','$s_published','$s_compl')";
                Util::connectSql("$tmpquery1");
            }

            // invoice
            if ($enableInvoicing == "true")
            {
                if ($st == "1")
                {
                    $completeItem = "1";
                }
                else
                {
                    $completeItem = "0";
                }

                $tmpquery = "WHERE project = '$project'";
                $detailInvoice = new Request();
                $detailInvoice->openInvoices($tmpquery);
                if ($detailInvoice->inv_status[0] == "0") {
                    //$tmpquery3 = "INSERT INTO ".$tableCollab["invoices_items"]." SET title='$task_name',description='$d',invoice='".$detailInvoice->inv_id[0]."',created='$dateheure',active='$invoicing',completed='$completeItem',mod_type='1',mod_value='$num',worked_hours='$worked_hours'";
                    $tmpquery3 = "INSERT INTO ".$tableCollab["invoices_items"]." (title,description,invoice,created,active,completed,mod_type,mod_value,worked_hours) VALUES ('$task_name','$d','".Util::fixInt($detailInvoice->inv_id[0])."','$dateheure','$invoicing','$completeItem','1','$num','$worked_hours')";
                    Util::connectSql($tmpquery3);
            }
        }

        if ($st == "1" && $complete_date != "--")
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET complete_date='$date' WHERE id = '$num'";
            Util::connectSql($tmpquery6);
        }

        //if assigned_to not blank, set assigned date
        if ($at != "0")
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET assigned='$dateheure' WHERE id = '$num'";
            Util::connectSql($tmpquery6);
        }

        $tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(task,owner,assigned_to,assigned) VALUES('$num','$idSession','$at','$dateheure')";
        Util::connectSql("$tmpquery2");

        //if assigned_to not blank, add to team members (only if doesn't already exist)
        if ($at != "0")
        {
            $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
            $testinTeam = new Request();
            $testinTeam->openTeams($tmpquery);
            $comptTestinTeam = count($testinTeam->tea_id);

            if ($comptTestinTeam == "0")
            {
                $tmpquery3 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$project','$at','1','0')";
                Util::connectSql("$tmpquery3");
            }

            //send task assignment mail if notifications = true
            if ($notifications == "true")
            {
                    include("../tasks/noti_taskassignment.php");
            }
        }

        //create task sub-folder if filemanagement = true
        if ($fileManagement == "true") {
            Util::createDirectory("files/$project/$num");
        }

        Util::headerFunction("../tasks/viewtask.php?id=$num&msg=addAssignment&".session_name()."=".session_id());
        exit;

        //case update task
    }
    else
    {

    //Change task status if parent phase is suspended, complete or not open.
        if ($projectDetail->pro_phase_set[0] != "0")
        {
            $tmpquery = "WHERE pha.project_id = '$project' AND pha.order_num = '$pha'";
            $currentPhase = new Request();
            $currentPhase->openPhases($tmpquery);

            if ($st == 3 && $currentPhase->pha_status[0] != 1)
            {
                $st = 4;
            }
        }

        if ($pub == "")
        {
            $pub = "1";
        }
        if ($compl == "10")
        {
            $st = "1";
        }

        //recompute number of completed tasks of the project
        $projectDetail->pro_name[0] = Util::projectComputeCompletion(
        $projectDetail,
        $tableCollab["projects"]);

        if ($invoicing == "")
        {
            $invoicing = "0";
        }

        //Update task with our without parent phase
        if ($projectDetail->pro_phase_set[0] != "0")
        {
            $tmpquery5 = "UPDATE ".$tableCollab["tasks"]." SET name='$task_name',description='$d',assigned_to='$at',status='$st',priority='$pr',start_date='$start_date',due_date='$due_date',estimated_time='$etm',actual_time='$atm',comments='$c',modified='$dateheure',completion='$compl',parent_phase='$pha',published='$pub',invoicing='$invoicing',worked_hours='$worked_hours' WHERE id = '$id'";
        }
        else
        {
            $tmpquery5 = "UPDATE ".$tableCollab["tasks"]." SET name='$task_name',description='$d',assigned_to='$at',status='$st',priority='$pr',start_date='$start_date',due_date='$due_date',estimated_time='$etm',actual_time='$atm',comments='$c',modified='$dateheure',completion='$compl',published='$pub',invoicing='$invoicing',worked_hours='$worked_hours' WHERE id = '$id'";
        }

        if ($st == "1" && $complete_date == "--")
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET complete_date='$date' WHERE id = '$id'";
            Util::connectSql($tmpquery6);
        }
        else
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET complete_date='$complete_date' WHERE id = '$id'";
            Util::connectSql($tmpquery6);
        }

        if ($old_st == "1" && $st != $old_st)
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET complete_date='' WHERE id = '$id'";
            Util::connectSql($tmpquery6);
        }

        //if project different from past value, set project number in tasks table
        if ($project != $old_project)
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET project='$project' WHERE id = '$id'";
            Util::connectSql($tmpquery6);
            $tmpquery7 = "UPDATE ".$tableCollab["files"]." SET project='$project' WHERE task = '$id'";
            Util::connectSql($tmpquery7);
            Util::createDirectory("files/$project/$id");

            $dir = opendir("../files/$old_project/$id");
            if (is_resource($dir))
            {
                while($v = readdir($dir))
                {
                    if ($v != '.' && $v != '..')
                    {
                        copy("../files/$old_project/$id/".$v,"../files/$project/$id/".$v);
                        @unlink("../files/$old_project/$id/".$v);
                    }
                }
            }

            //recompute number of completed tasks of the old project
            $tmpquery = "WHERE pro.id = '$old_project'";
            $oldproject = new Request();
            $oldproject->openProjects($tmpquery);
            Util::projectComputeCompletion(
            $oldproject,
            $tableCollab["projects"]);

        }

        if ($enableInvoicing == "true")
        {
            if ($st == "1")
            {
                $completeItem = "1";
            }
            else
            {
                $completeItem = "0";
            }
                $tmpquery = "WHERE project = '$project'";
                $detailInvoice = new Request();
                $detailInvoice->openInvoices($tmpquery);

                if ($detailInvoice->inv_status[0] == "0")
                {
                    $tmpquery3 = "UPDATE ".$tableCollab["invoices_items"]." SET active='$invoicing',completed='$completeItem',worked_hours='$worked_hours' WHERE mod_type = '1' AND mod_value = '$id'";
                    Util::connectSql($tmpquery3);
                }
            }

            //if assigned_to not blank and past assigned value blank, set assigned date
            if ($at != "0" && $old_assigned == "")
            {
                $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET assigned='$dateheure' WHERE id = '$id'";
                Util::connectSql($tmpquery6);
            }

            //if assigned_to different from past value, insert into assignment
            //add new assigned_to in team members (only if doesn't already exist)
            if ($at != $old_at)
            {
                $tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(task,owner,assigned_to,assigned) VALUES('$id','$idSession','$at','$dateheure')";
                Util::connectSql("$tmpquery2");
                $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
                $testinTeam = new Request();
                $testinTeam->openTeams($tmpquery);
                $comptTestinTeam = count($testinTeam->tea_id);

                if ($comptTestinTeam == "0")
                {
                    $tmpquery3 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$project','$at','1','0')";
                    Util::connectSql("$tmpquery3");
                }

                $msg = "updateAssignment";
                Util::connectSql("$tmpquery5");
                $tmpquery = "WHERE tas.id = '$id'";
                $taskDetail = new Request();
                $taskDetail->openTasks($tmpquery);

                //send task assignment mail if notifications = true
                if ($notifications == "true")
                {
                    include("../tasks/noti_taskassignment.php");
                }
            }
            else
            {
                $msg = "update";
                Util::connectSql("$tmpquery5");
                $tmpquery = "WHERE tas.id = '$id'";
                $taskDetail = new Request();
                $taskDetail->openTasks($tmpquery);

                //send status task change mail if notifications = true
                if ($at != "0" && $st != $old_st)
                {
                    if ($notifications == "true")
                    {
                            include("../tasks/noti_statustaskchange.php");
                    }
                }

                //send priority task change mail if notifications = true
                if ($at != "0" && $pr != $old_pr)
                {
                    if ($notifications == "true")
                    {
                            include("../tasks/noti_prioritytaskchange.php");
                    }
                }

                //send due date task change mail if notifications = true
                if ($at != "0" && $due_date != $old_dd)
                {
                    if ($notifications == "true")
                    {
                            include("../tasks/noti_duedatetaskchange.php");
                    }
                }
            }

            if ($st != $old_st)
            {
                $cUp .= "\n[status:$st]";
            }

            if ($pr != $old_pr)
            {
                $cUp .= "\n[priority:$pr]";
            }

            if ($due_date != $old_dd)
            {
                $cUp .= "\n[datedue:$due_date]";
            }

            if ($cUp != "" || $st != $old_st || $pr != $old_pr || $due_date != $old_dd)
            {
                $cUp = Util::convertData($cUp);
                $tmpquery6 = "INSERT INTO ".$tableCollab["updates"]."(type,item,member,comments,created) VALUES ('1','$id','$idSession','$cUp','$dateheure')";
                Util::connectSql($tmpquery6);
            }

            Util::headerFunction("../tasks/viewtask.php?id=$id&msg=$msg&".session_name()."=".session_id());
        }
    }

    //set value in form
    $task_name = $taskDetail->tas_name[0];
    $d = $taskDetail->tas_description[0];
    $start_date = $taskDetail->tas_start_date[0];
    $due_date = $taskDetail->tas_due_date[0];
    $complete_date = $taskDetail->tas_complete_date[0];
    $etm = $taskDetail->tas_estimated_time[0];
    $atm = $taskDetail->tas_actual_time[0];
    $c = $taskDetail->tas_comments[0];
    $pub = $taskDetail->tas_published[0];
    $worked_hours = $taskDetail->tas_worked_hours[0];

    if ($pub == "0")
    {
        $checkedPub = "checked";
    }
}

//case add task
if ($id == "")
{

    //case add task
    if ($action == "add")
    {

        //concat values from date selector and replace quotes by html code in name
        $task_name = Util::convertData($task_name);
        $d = Util::convertData($d);
        $c = Util::convertData($c);

        //Change task status if parent phase is suspended, complete or not open.
        if ($projectDetail->pro_enable_phase[0] == "1")
        {
            $tmpquery = "WHERE pha.project_id = '$project' AND pha.order_num = '$pha'";
            $currentPhase = new Request();
            $currentPhase->openPhases($tmpquery);

            if ($st == 3 && $currentPhase->pha_status[0] != 1)
            {
                $st = 4;
            }
        }

        if ($compl == "10")
        {
            $st = "1";
        }

        if ($pub == "")
        {
            $pub = "1";
        }

        if ($invoicing == "")
        {
            $invoicing = "0";
        }

        if ($worked_hours == "")
        {
            $worked_hours = "0.00";
        }

        //Insert task with our without parent phase
        if ($projectDetail->pro_phase_set[0] != "0")
        {
            $tmpquery1 = "INSERT INTO ".$tableCollab["tasks"]."(project,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion,parent_phase,invoicing,worked_hours) VALUES('$project','$task_name','$d','$idSession','$at','$st','$pr','$start_date','$due_date','$etm','$atm','$c','$dateheure','$pub','$compl','$pha','$invoicing','$worked_hours')";
        }
        else
        {
            $tmpquery1 = "INSERT INTO ".$tableCollab["tasks"]."(project,name,description,owner,assigned_to,status,priority,start_date,due_date,estimated_time,actual_time,comments,created,published,completion,invoicing,worked_hours) VALUES('$project','$task_name','$d','$idSession','$at','$st','$pr','$start_date','$due_date','$etm','$atm','$c','$dateheure','$pub','$compl','$invoicing','$worked_hours')";
        }

        Util::connectSql("$tmpquery1");
        $tmpquery = $tableCollab["tasks"];
        Util::getLastId($tmpquery);
        $num = $lastId[0];
        unset($lastId);

        if ($enableInvoicing == "true")
        {
            if ($st == "1")
            {
                $completeItem = "1";
            }
            else
            {
                $completeItem = "0";
            }

            $tmpquery = "WHERE project = '$project'";
            $detailInvoice = new Request();
            $detailInvoice->openInvoices($tmpquery);

            if ($detailInvoice->inv_status[0] == "0")
            {
                //$tmpquery3 = "INSERT INTO ".$tableCollab["invoices_items"]." SET title='$task_name',description='$d',invoice='".$detailInvoice->inv_id[0]."',created='$dateheure',active='$invoicing',completed='$completeItem',mod_type='1',mod_value='$num',worked_hours='$worked_hours'";
                $tmpquery3 = "INSERT INTO ".$tableCollab["invoices_items"]." (title,description,invoice,created,active,completed,mod_type,mod_value,worked_hours) VALUES ('$task_name','$d','".Util::fixInt($detailInvoice->inv_id[0])."','$dateheure','$invoicing','$completeItem','1','$num','$worked_hours')";
                Util::connectSql($tmpquery3);
            }
        }

        if ($st == "1")
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET complete_date='$date' WHERE id = '$num'";
            Util::connectSql($tmpquery6);
        }

        //recompute number of completed tasks of the project
        $projectDetail->pro_name[0] = Util::projectComputeCompletion(
        $projectDetail,
        $tableCollab["projects"]);

        //if assigned_to not blank, set assigned date
        if ($at != "0")
        {
            $tmpquery6 = "UPDATE ".$tableCollab["tasks"]." SET assigned='$dateheure' WHERE id = '$num'";
            Util::connectSql($tmpquery6);
        }
        $tmpquery2 = "INSERT INTO ".$tableCollab["assignments"]."(task,owner,assigned_to,assigned) VALUES('$num','$idSession','$at','$dateheure')";
        Util::connectSql($tmpquery2);

        //if assigned_to not blank, add to team members (only if doesn't already exist)
        //add assigned_to in team members (only if doesn't already exist)
        if ($at != "0")
        {
            $tmpquery = "WHERE tea.project = '$project' AND tea.member = '$at'";
            $testinTeam = new Request();
            $testinTeam->openTeams($tmpquery);
            $comptTestinTeam = count($testinTeam->tea_id);

            if ($comptTestinTeam == "0")
            {
                $tmpquery3 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$project','$at','1','0')";
                Util::connectSql($tmpquery3);
            }

            //send task assignment mail if notifications = true
            if ($notifications == "true")
            {
                    include("../tasks/noti_taskassignment.php");
            }
        }

        //create task sub-folder if filemanagement = true
        if ($fileManagement == "true")
        {
            Util::createDirectory("files/$project/$num");
        }
        Util::headerFunction("../tasks/viewtask.php?id=$num&msg=addAssignment&".session_name()."=".session_id());
    }

    //set default values
    $taskDetail->tas_assigned_to[0] = "0";
    $taskDetail->tas_priority[0] = $projectDetail->pro_priority[0];
    $taskDetail->tas_status[0] = "2";
}

if ($projectDetail->pro_org_id[0] == "1")
{
    $projectDetail->pro_org_name[0] = $strings["none"];
}


if ($projectDetail->pro_phase_set[0] != "0")
{
    if ($id != "")
    {
        $tPhase = $taskDetail->tas_parent_phase[0];
        if (!$tPhase){ $tPhase = '0'; }
        $tmpquery = "WHERE pha.project_id = '".$taskDetail->tas_project[0]."' AND pha.order_num = '$tPhase'";
    }

    if ($id == "")
    {
        $tPhase = $phase;
        if (!$tPhase){ $tPhase = '0'; }
        $tmpquery = "WHERE pha.project_id = '$project' AND pha.order_num = '$tPhase'";
    }

    $targetPhase = new Request();
    $targetPhase->openPhases($tmpquery);
}

$bodyCommand="onload=\"document.etDForm.compl.value = document.etDForm.completion.selectedIndex;document.etDForm.task_name.focus();\"";

$headBonus = "";
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include '../themes/'.THEME.'/header.php';

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));

if ($projectDetail->pro_phase_set[0] != "0")
{
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/listphases.php?id=".$projectDetail->pro_id[0],$strings["phases"],in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in));
}

$blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/listtasks.php?project=".$projectDetail->pro_id[0],$strings["tasks"],in));

if ($id == "")
{
    $blockPage->itemBreadcrumbs($strings["add_task"]);
}

if ($id != "")
{
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../tasks/viewtask.php?id=".$taskDetail->tas_id[0],$taskDetail->tas_name[0],in));
    $blockPage->itemBreadcrumbs($strings["edit_task"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "")
{
    include('../includes/messages.php');
    $blockPage->messagebox($msgLabel);
}

$block1 = new Block();


if ($id == "")
{
    $block1->form = "etD";
    $block1->openForm("../tasks/edittask.php?project=$project&action=add&".session_name()."=".session_id()."#".$block1->form."Anchor");
}

if ($id != "")
{
    $block1->form = "etD";
    $block1->openForm("../tasks/edittask.php?project=$project&id=$id&action=update&docopy=$docopy&".session_name()."=".session_id()."#".$block1->form."Anchor");
    echo "<input type=\"hidden\" name=\"old_at\" value=\"".$taskDetail->tas_assigned_to[0]."\"><input type=\"hidden\" name=\"old_assigned\" value=\"".$taskDetail->tas_assigned[0]."\"><input type=\"hidden\" name=\"old_pr\" value=\"".$taskDetail->tas_priority[0]."\"><input type=\"hidden\" name=\"old_st\" value=\"".$taskDetail->tas_status[0]."\"><input type=\"hidden\" name=\"old_dd\" value=\"".$taskDetail->tas_due_date[0]."\"><input type=\"hidden\" name=\"old_project\" value=\"".$taskDetail->tas_project[0]."\">";
}

if ($error != "")
{
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($id == "")
{
    $block1->heading($strings["add_task"]);
}

if ($id != "")
{

    if ($docopy == "true")
    {
        $block1->heading($strings["copy_task"]." : ".$taskDetail->tas_name[0]);
    }
    else
    {
        $block1->heading($strings["edit_task"]." : ".$taskDetail->tas_name[0]);
    }
}

$block1->openContent();
$block1->contentTitle($strings["info"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["project"]." :</td><td><select name='project'>";

if ($projectsFilter == "true")
{
    $tmpquery = "LEFT OUTER JOIN ".$tableCollab["teams"]." teams ON teams.project = pro.id ";
    $tmpquery .= "WHERE teams.member = '$idSession'";
}
else
{
    $tmpquery = "";
}

$listProjects = new Request();
$listProjects->openProjects($tmpquery);
$comptListProjects = count($listProjects->pro_id);

for ($i=0;$i<$comptListProjects;$i++)
{
    if($listProjects->pro_id[$i] == $projectDetail->pro_id[0])
    {
        echo "<option value='".$listProjects->pro_id[$i]."' selected>".$listProjects->pro_name[$i]."</option>";
    }
    else
    {
        echo "<option value='".$listProjects->pro_id[$i]."'>".$listProjects->pro_name[$i]."</option>";
    }
}
echo "</select></td></tr>";

//Display task's phase
if ($projectDetail->pro_phase_set[0] != "0")
{
    echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["phase"]." :</td><td>".$blockPage->buildLink("../phases/viewphase.php?id=".$targetPhase->pha_id[0],$targetPhase->pha_name[0],in)."</td></tr>";
}
echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["organization"]." :</td><td>".$projectDetail->pro_org_name[0]."</td></tr>";

$block1->contentTitle($strings["details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["name"]." :</td><td><input size='44' value='";

if ($docopy == "true") {
    echo $strings["copy_of"];
}

echo "$task_name' style='width: 400px' name='task_name' maxlength='100' type='TEXT'></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["description"]." :</td>
            <td><textarea rows='10' style='width: 400px; height: 160px;' name='d' cols='47'>$d</textarea></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["assigned_to"]." :</td>
            <td><select name='at'>";

if ($taskDetail->tas_assigned_to[0] == "0")
{
    echo "      <option value='0' selected>".$strings["unassigned"]."</option>";
}
else
{
    echo "      <option value='0'>".$strings["unassigned"]."</option>";
}

$tmpquery = "WHERE tea.project = '$project' ORDER BY mem.name";
$assignto = new Request();
$assignto->openTeams($tmpquery);
$comptAssignto = count($assignto->tea_mem_id);

for ($i=0;$i<$comptAssignto;$i++)
{
    $clientUser = "";

    if ($assignto->tea_mem_profil[$i] == "3")
    {
        $clientUser = " (".$strings["client_user"].")";
    }

    if ($taskDetail->tas_assigned_to[0] == $assignto->tea_mem_id[$i])
    {
        echo "      <option value='".$assignto->tea_mem_id[$i]."' selected>".$assignto->tea_mem_login[$i]." / ".$assignto->tea_mem_name[$i]."$clientUser</option>";
    }
    else
    {
        echo "      <option value='".$assignto->tea_mem_id[$i]."'>".$assignto->tea_mem_login[$i]." / ".$assignto->tea_mem_name[$i]."$clientUser</option>";
    }
}

echo "      </select></td>
        </tr>";

//Select phase
if ($projectDetail->pro_phase_set[0] != "0")
{
    echo"<tr class='odd'><td valign='top' class='leftvalue'>".$strings["phase"]." :</td><td><select name='pha'>";

    $projectTarget = $projectDetail->pro_id[0];
    $tmpquery = "WHERE pha.project_id = '$projectTarget' ORDER BY pha.order_num";
    $projectPhaseList = new Request();
    $projectPhaseList->openPhases($tmpquery);

    $comptlistPhase = count($projectPhaseList->pha_id);
    for ($i=0;$i<$comptlistPhase;$i++)
    {
        $phaseNum = $projectPhaseList->pha_order_num[$i];
        if ($taskDetail->tas_parent_phase[0] == $phaseNum || $phase == $phaseNum)
        {
            echo "<option value='$phaseNum' selected>".$projectPhaseList->pha_name[$i]."</option>";
        }
        else
        {
            echo "<option value='$phaseNum'>".$projectPhaseList->pha_name[$i]."</option>";
        }
    }
    echo "</select></td></tr>";
}

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["status"]." :</td><td><select name='st' onchange=\"changeSt(this)\">";

$comptSta = count($status);

for ($i=0;$i<$comptSta;$i++)
{
    if ($taskDetail->tas_status[0] == $i)
    {
        echo "<option value='$i' selected>$status[$i]</option>";
    }
    else
    {
        echo "<option value='$i'>$status[$i]</option>";
    }
}

echo "          </select>
            </td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["completion"]." :</td>
            <td><input name='compl' type='hidden' value='".$taskDetail->tas_completion[0]."'>
                <select name='completion' onchange=\"changeCompletion(this)\">";

for ($i=0;$i<11;$i++)
{
    $complValue = ($i>0) ? $i."0 %": $i." %";

    if ($taskDetail->tas_completion[0] == $i)
    {
        echo "<option value='".$i."' selected>".$complValue."</option>";
    }
    else
    {
        echo "<option value='".$i."'>".$complValue."</option>";
    }
}

echo "          </select>
            </td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["priority"]." :</td>
            <td><select name='pr'>";

$comptPri = count($priority);

for ($i=0;$i<$comptPri;$i++)
{
    if ($taskDetail->tas_priority[0] == $i)
    {
        echo "<option value='$i' selected>$priority[$i]</option>";
    }
    else
    {
        echo "<option value='$i'>$priority[$i]</option>";
    }
}

echo "</select></td></tr>";

if ($start_date == "") {
    $start_date = $date;
}
if ($due_date == "") {
    $due_date = "--";
}
if ($complete_date == "") {
    $complete_date = "--";
}

$block1->contentRow($strings["start_date"],"<input type='text' name='start_date' id='start_date' size='20' value='$start_date'><input type='button' value=' ... ' id=\"trigStartDate\">");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'start_date',
        button         :    'trigStartDate',
        $calendar_common_settings
    });
</script>
";
$block1->contentRow($strings["due_date"],"<input type='text' name='due_date' id='due_date' size='20' value='$due_date'><input type='button' value=' ... ' id=\"trigDueDate\">");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        $calendar_common_settings
    });
</script>
";
if ($id != "")
{
    $block1->contentRow($strings["complete_date"],"<input type='text' name='complete_date' id='complete_date' size='20' value='$complete_date'><input type='button' value=' ... ' id=\"trigCompleteDate\">");
	echo "
	<script type='text/javascript'>
	    Calendar.setup({
	        inputField     :    'complete_date',
	        button         :    'trigCompleteDate',
        $calendar_common_settings
	    });
	</script>
	";
}

echo "  <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["estimated_time"]." :</td>
            <td><input size='32' value='$etm' style='width: 250px' name='etm' maxlength='32' type='TEXT'>&nbsp;".$strings["hours"]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["actual_time"]." :</td>
            <td><input size='32' value='$atm' style='width: 250px' name='atm' maxlength='32' type='TEXT'>&nbsp;".$strings["hours"]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["comments"]." :</td>
            <td><textarea rows='10' style='width: 400px; height: 160px;' name='c' cols='47'>$c</textarea></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["published"]." :</td>
            <td><input size='32' value='0' name='pub' type='checkbox' $checkedPub></td>
        </tr>";

if ($enableInvoicing == "true")
{
    if ($taskDetail->tas_invoicing[0] == "1")
    {
        $ckeckedInvoicing = "checked";
    }
    $block1->contentRow($strings["invoicing"],"<input size=\"32\" value=\"1\" name=\"invoicing\" type=\"checkbox\" $ckeckedInvoicing>");
    $block1->contentRow($strings["worked_hours"],"<input size=\"32\" value=\"$worked_hours\" style=\"width: 250px\" name=\"worked_hours\" type=\"TEXT\">");
}

if ($id != "")
{
    $block1->contentTitle($strings["updates_task"]);
    echo "  <tr class='odd'>
                <td valign='top' class='leftvalue'>".$strings["comments"]." :</td>
                <td><textarea rows='10' style='width: 400px; height: 160px;' name='cUp' cols='47'></textarea></td>
            </tr>";
}

echo "      <tr class='odd'>
                <td valign='top' class='leftvalue'>&nbsp;</td>
                <td><input type='SUBMIT' value='".$strings["save"]."'></td>
            </tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>

<script>
function changeSt(theObj, firstRun){
    if (theObj.selectedIndex==3) {

        if (firstRun!=true) document.etDForm.completion.selectedIndex=0;
        document.etDForm.compl.value=0;
        document.etDForm.completion.disabled=false;
    } else {
        if (theObj.selectedIndex==0 || theObj.selectedIndex==1) {
            document.etDForm.completion.selectedIndex=10;

            document.etDForm.compl.value=10;


        } else {
            document.etDForm.completion.selectedIndex=0;
            document.etDForm.compl.value=0;
        }
        document.etDForm.completion.disabled=true;

    }
}

function changeCompletion(){
    document.etDForm.compl.value = document.etDForm.completion.selectedIndex;
}

changeSt(document.etDForm.st, true);
</script>