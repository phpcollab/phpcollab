<?php
/*
** Application name: phpCollab
** Last Edit page: 2005-03-08 
** Path by root: ../projects/editproject.php
** Authors: Ceam / Fullo / dracono
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editproject.php
**
** DESC: Screen: Create or edit a project
**
** HISTORY:
**  2005-03-08	-	fixed null value for hourly rate
**	19/05/2005	-	fixed and &amp; in link
**	22/05/2005	-	added subtask copy
** -----------------------------------------------------------------------------
** TO-DO:
** =============================================================================
*/

$checkSession = "true";
include_once '../includes/library.php';
include '../includes/customvalues.php';

$id = phpCollab\Util::returnGlobal('id','REQUEST');
$docopy = phpCollab\Util::returnGlobal('docopy','REQUEST');

$teams = new \phpCollab\Teams\Teams();

if ($htaccessAuth == "true") 
{
	$Htpasswd = new Htpasswd;
}

if ($enable_cvs == "true") 
{
	include '../includes/cvslib.php';
}

//case update or copy project
if ($id != "") 
{
	if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5") 
	{
		phpCollab\Util::headerFunction("../projects/viewproject.php?id=$id");
	}

//test exists selected project, redirect to list if not
	$tmpquery = "WHERE pro.id = '$id'";
	$projectDetail = new phpCollab\Request();
	$projectDetail->openProjects($tmpquery);
	$comptProjectDetail = count($projectDetail->pro_id);

    if ($docopy == "true") $setTitle .= " : Copy Project (" . $projectDetail->pro_name[0] . ")";
    else $setTitle .= " : Edit Project (" . $projectDetail->pro_name[0] . ")";


	if ($comptProjectDetail == "0") 
	{
		phpCollab\Util::headerFunction("../projects/listprojects.php?msg=blankProject");
	}
	
	if ($idSession != $projectDetail->pro_owner[0] && $profilSession != "0" && $profilSession != "5") 
	{
		phpCollab\Util::headerFunction("../projects/listprojects.php?msg=projectOwner");
	}

	//case update or copy project
	if ($action == "update") 
	{

		//replace quotes by html code in name and description
		$pn = phpCollab\Util::convertData($pn);
		$d = phpCollab\Util::convertData($d);

		//case copy project
		if ($docopy == "true") 
		{

			if ($invoicing == "" || $clod == "1") 
			{
				$invoicing = "0";
			}

			if ($hourly_rate == "")
			{
				$hourly_rate = "0.00";
			}

			//insert into projects and teams (with last id project)
			$dbParams = [];
            $dbParams["name"] = $pn;
            $dbParams["priority"] = $pr;
            $dbParams["description"] = $d;
            $dbParams["owner"] = $pown;
            $dbParams["organization"] = $clod;
            $dbParams["status"] = $st;
            $dbParams["created"] = $dateheure;
            $dbParams["published"] = $projectPublished;
            $dbParams["upload_max"] = $up;
            $dbParams["url_dev"] = $url_dev;
            $dbParams["url_prod"] = $url_prod;
            $dbParams["phase_set"] = $thisPhase;
            $dbParams["invoicing"] = $invoicing;
            $dbParams["hourly_rate"] = $hourly_rate;
            
			$projectNew = phpCollab\Util::newConnectSql(
                "INSERT INTO {$tableCollab["projects"]} (name,priority,description,owner,organization,status,created,published,upload_max,url_dev,url_prod,phase_set,invoicing,hourly_rate) VALUES(:name,:priority,:description,:owner,:organization,:status,:created,:published,:upload_max,:url_dev,:url_prod,:phase_set,:invoicing,:hourly_rate)"
			    , $dbParams
            );
			unset($dbParams);

			phpCollab\Util::newConnectSql(
                "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES(:project,:member,:published,:authorized)",
			    ["project" => $projectNew,"member" => $pown,"published" => 1,"authorized" => 0]
            );

			if ($enableInvoicing == "true") 
			{
                phpCollab\Util::newConnectSql(
                    "INSERT INTO {$tableCollab["invoices"]} (project,created,status,active,published) VALUES (:project,:created,:status,:active,:published)",
                    ["project" => $projectNew,"created" => $dateheure,"status" => 0,"active" => $invoicing,"published" => 1]
                );
			}

			//create project folder if filemanagement = true
			if ($fileManagement == "true") 
			{
				phpCollab\Util::createDirectory("files/$projectNew");
			}
		
			if ($htaccessAuth == "true") 
			{

$content = <<<STAMP
AuthName "$setTitle"
AuthType Basic
Require valid-user
AuthUserFile $fullPath/files/$projectNew/.htpasswd
STAMP;
				$fp = @fopen("../files/$projectNew/.htaccess",'wb+');
				$fw = fwrite($fp,$content);
				$fp = @fopen("../files/$projectNew/.htpasswd",'wb+');

				$tmpquery = "WHERE mem.id = '$pown'";
				$detailMember = new phpCollab\Request();
				$detailMember->openMembers($tmpquery);

				$Htpasswd = new Htpasswd;
				$Htpasswd->initialize("../files/".$projectNew."/.htpasswd");
				$Htpasswd->addUser($detailMember->mem_login[0],$detailMember->mem_password[0]);
			}

			$tmpquery = "WHERE tas.project = '$id'";
			$listTasks = new phpCollab\Request();
			$listTasks->openTasks($tmpquery);
			$comptListTasks = count($listTasks->tas_id);

			for ($i=0;$i<$comptListTasks;$i++) 
			{
				$assigned = "";
				$at = "";
				$tn = phpCollab\Util::convertData($listTasks->tas_name[$i]);
				$d = phpCollab\Util::convertData($listTasks->tas_description[$i]);
				$ow = $listTasks->tas_owner[$i];
				$at = $listTasks->tas_assigned_to[$i];
				$st = $listTasks->tas_status[$i];
				$pr = $listTasks->tas_priority[$i];
				$sd = $listTasks->tas_start_date[$i];
				$dd = $listTasks->tas_due_date[$i];
				$cd = $listTasks->tas_complete_date[$i];
				$etm = $listTasks->tas_estimated_time[$i];
				$atm = $listTasks->tas_actual_time[$i];
				$c = phpCollab\Util::convertData($listTasks->tas_comments[$i]);
				$pha = $listTasks->tas_parent_phase[$i];
				$published = $listTasks->tas_published[$i];
				$compl = $listTasks->tas_completion[$i];
								
				if ($at != "0") 
				{
					$assigned = $dateheure;
				}
						
				$dbParams = [];
                $dbParams["project"] = $projectNew;
                $dbParams["name"] = $tn;
                $dbParams["description"] = $d;
                $dbParams["owner"] = $ow;
                $dbParams["assigned_to"] = $at;
                $dbParams["status"] = $st;
                $dbParams["priority"] = $pr;
                $dbParams["start_date"] = $sd;
                $dbParams["due_date"] = $dd;
                $dbParams["complete_date"] = $cd;
                $dbParams["estimated_time"] = $etm;
                $dbParams["actual_time"] = $atm;
                $dbParams["comments"] = $c;
                $dbParams["created"] = $dateheure;
                $dbParams["assigned"] = $assigned;
                $dbParams["published"] = $published;
                $dbParams["completion"] = $compl;
                $dbParams["parent_phase"] = $pha;

				$num = phpCollab\Util::newConnectSql(
                    "INSERT INTO {$tableCollab["tasks"]} (project,name,description,owner,assigned_to,status,priority,start_date,due_date,complete_date,estimated_time,actual_time,comments,created,assigned,published,completion,parent_phase) VALUES(:project,:name,:description,:owner,:assigned_to,:status,:priority,:start_date,:due_date,:complete_date,:estimated_time,:actual_time,:comments,:created,:assigned,:published,:completion,:parent_phase)",
                    $dbParams
                );
				unset($dbParams);

				phpCollab\Util::newConnectSql(
                    "INSERT INTO {$tableCollab["assignments"]} (task,owner,assigned_to,assigned) VALUES(:task,:owner,:assigned_to,:assigned)",
                    ["task" => $num,"owner" => $ow,"assigned_to" => $at,"assigned" => $dateheure]
                );
				
				//start the subtask copy
				$T_id=$listTasks->tas_id[$i];
				
				$tmpquery1 = "WHERE task = '$T_id'";
				$subtaskDetail = new phpCollab\Request();
				$subtaskDetail->openSubtasks($tmpquery1);
				$comptListSubtasks = count($subtaskDetail->subtas_id);
				
				for ($j=0;$j<$comptListSubtasks;$j++) 
				{
					$s_tn = phpCollab\Util::convertData($subtaskDetail->subtas_name[$j]);
					$s_d = phpCollab\Util::convertData($subtaskDetail->subtas_description[$j]);
					$s_ow = $subtaskDetail->subtas_owner[$j];
					$s_at = $subtaskDetail->subtas_assigned_to[$j];
					$s_st = $subtaskDetail->subtas_status[$j];
					$s_pr = $subtaskDetail->subtas_priority[$j];
					$s_sd = $subtaskDetail->subtas_start_date[$j];
					$s_dd = $subtaskDetail->subtas_due_date[$j];
					$s_cd = $subtaskDetail->subtas_complete_date[$j];
					$s_etm = $subtaskDetail->subtas_estimated_time[$j];
					$s_atm = $subtaskDetail->subtas_actual_time[$j];
					$s_c = phpCollab\Util::convertData($subtaskDetail->subtas_comments[$j]);
					$s_published = $subtaskDetail->subtas_published[$j];
					$s_compl = $subtaskDetail->subtas_completion[$j];
						
					$subTasksData = [];
                    $subTasksData["task"] = $num;
                    $subTasksData["name"] = $s_tn;
                    $subTasksData["description"] = $s_d;
                    $subTasksData["owner"] = $s_ow;
                    $subTasksData["assigned_to"] = $s_at;
                    $subTasksData["status"] = $s_st;
                    $subTasksData["priority"] = $s_pr;
                    $subTasksData["start_date"] = $s_sd;
                    $subTasksData["due_date"] = $s_dd;
                    $subTasksData["complete_date"] = $s_cd;
                    $subTasksData["estimated_time"] = $s_etm;
                    $subTasksData["actual_time"] = $s_atm;
                    $subTasksData["comments"] = $s_c;
                    $subTasksData["created"] = $dateheure;
                    $subTasksData["assigned"] = $dateheure;
                    $subTasksData["published"] = $s_published;
                    $subTasksData["completion"] = $s_compl;

					$s_num = phpCollab\Util::newConnectSql(
                        "INSERT INTO {$tableCollab["subtasks"]} (task,name,description,owner,assigned_to,status,priority,start_date,due_date,complete_date,estimated_time,actual_time,comments,created,assigned,published,completion) VALUES(:task,:name,:description,:owner,:assigned_to,:status,:priority,:start_date,:due_date,:complete_date,:estimated_time,:actual_time,:comments,:created,:assigned,:published,:completion)",
                        $subTasksData
                    );
					unset($subTasksData);
					
					phpCollab\Util::newConnectSql(
                        "INSERT INTO {$tableCollab["assignments"]} (subtask,owner,assigned_to,assigned) VALUES(:subtask,:owner,:assigned_to,:assigned)",
                        ["subtask" => $s_num,"owner" => $s_ow,"assigned_to" => $s_at,"assigned" => $dateheure]
                    );
				}	
			
				
				if ($at != "0") 
				{
					$tmpquery = "WHERE tea.project = '$projectNew' AND tea.member = '$at'";
					$testinTeam = new phpCollab\Request();
					$testinTeam->openTeams($tmpquery);
					$comptTestinTeam = count($testinTeam->tea_id);
					
					if ($comptTestinTeam == "0") 
					{
						phpCollab\Util::newConnectSql(
                            "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES (:project,:member,:published,:authorized)",
                            ["project" => $projectNew,"member" => $at,"published" => 1,"authorized" => 0]
                        );
					
						if ($htaccessAuth == "true") 
						{
							$tmpquery = "WHERE mem.id = '$at'";
							$detailMember = new phpCollab\Request();
							$detailMember->openMembers($tmpquery);

							$Htpasswd->initialize("../files/".$projectNew."/.htpasswd");
							$Htpasswd->addUser($detailMember->mem_login[0],$detailMember->mem_password[0]);
						}
					}
				}

			//create task sub-folder if filemanagement = true
				if ($fileManagement == "true") 
				{
					phpCollab\Util::createDirectory("files/$projectNew/$num");
				}
			}
	
			//if mantis bug tracker enabled
			if ($enableMantis == "true") 
			{
				// call mantis function to copy project
				include $pathMantis . '/proj_add.php';
			}

			//if CVS repository enabled
			if ($enable_cvs == "true") 
			{
				$user_query = "WHERE mem.id = '$pown'";
				$cvsUser = new phpCollab\Request();
				$cvsUser->openMembers($user_query);
				cvs_add_repository($cvsUser->mem_login[0], $cvsUser->mem_password[0], $projectNew);
			}

			//create phase structure if enable phase was selected as true
			if ($thisPhase != "0") 
			{
				$comptThisPhase = count($phaseArraySets[$thisPhase]);
			
				for($i=0;$i<$comptThisPhase;$i++)
				{
					phpCollab\Util::newConnectSql(
                        "INSERT INTO {$tableCollab["phases"]} (project_id,order_num,status,name) VALUES(:project_id,:order_num,:status,:name)",
                        ["project_id" => $projectNew,"order_num" => $i,"status" => 0,"name" => $phaseArraySets[$thisPhase][$i]]
                    );
				}
			}

			phpCollab\Util::headerFunction("../projects/viewproject.php?id=$projectNew&msg=add");

		} 
		else
		{

			//if project owner change, add new to team members (only if doesn't already exist)
			if ($pown != $projectDetail->pro_owner[0]) 
			{
				$tmpquery = "WHERE tea.project = '$id' AND tea.member = '$pown'";
				$testinTeam = new phpCollab\Request();
				$testinTeam->openTeams($tmpquery);
				$comptTestinTeam = count($testinTeam->tea_id);
						
				if ($comptTestinTeam == "0") 
				{
					phpCollab\Util::newConnectSql(
                        "INSERT INTO {$tableCollab["teams"]} (project,member,published,authorized) VALUES(:project,:member,:published,:authorized)",
                        ["project" => $id,"member" =>$pown,"published" => 1,"authorized" => 0]
                    );

					if ($htaccessAuth == "true") 
					{
						$tmpquery = "WHERE mem.id = '$pown'";
						$detailMember = new phpCollab\Request();
						$detailMember->openMembers($tmpquery);

						$Htpasswd->initialize("../files/".$id."/.htpasswd");
						$Htpasswd->addUser($detailMember->mem_login[0],$detailMember->mem_password[0]);
					}
				}
			}

		//if organization change, delete old organization permitted users from teams
		if ($clod != $projectDetail->pro_organization[0]) 
		{
			$tmpquery = "WHERE tea.project = '$id' AND mem.profil = '3'";
			$suppTeamClient = new phpCollab\Request();
			$suppTeamClient->openTeams($tmpquery);
			$comptSuppTeamClient = count($suppTeamClient->tea_id);
				
				if ($comptSuppTeamClient == "0") 
				{
					for ($i=0;$i<$comptSuppTeamClient;$i++)
					{
						$membersTeam .= $suppTeamClient->tea_mem_id[$i];
						if ($i < $comptSuppTeamClient-1) 
						{
							$membersTeam .= ",";
						} 

						if ($htaccessAuth == "true")
						{
							$Htpasswd->initialize("../files/".$id."/.htpasswd");
							$Htpasswd->deleteUser($suppTeamClient->mem_login[$i]);
						}
                    }


                    $teams->deleteFromTeamsByProjectIdAndMemberId($id, $membersTeam);
				}
		}

//-------------------------------------------------------------------------------------------------		
		$tmpquery = "WHERE pro.id = '$id'";
		$targetProject = new phpCollab\Request();
		$targetProject->openProjects($tmpquery);

		//Delete old or unused phases
		if ($targetProject->pro_phase_set[0] != $thisPhase)
		{
			phpCollab\Util::newConnectSql("DELETE FROM {$tableCollab["phases"]} WHERE project_id = :project_id", ["project_id" => $id]);
		}
		
		//Create new Phases
		if($targetProject->pro_phase_set[0] != $thisPhase)
		{
			$comptThisPhase = count($phaseArraySets[$thisPhase]);

			for($i=0;$i<$comptThisPhase;$i++)
			{
				phpCollab\Util::newConnectSql(
                    "INSERT INTO {$tableCollab["phases"]} (project_id,order_num,status,name) VALUES(:project_id,:order_num,:status,:name)",
                    ["project_id" => $id, "order_num" => $i, "status" => 0, "name" => $phaseArraySets[$thisPhase][$i]]);
			}
			
			//Get a listing of project tasks and files and re-assign them to new phases if the phase set of a project is changed.
			$tmpquery = "WHERE tas.project = '".$targetProject->pro_id[0]."'";
			$listTasks = new phpCollab\Request();
			$listTasks->openTasks($tmpquery);
			$comptListTasks = count($listTasks->tas_id);
			
			$tmpquery = "WHERE fil.project = '".$targetProject->pro_id[0]."' AND fil.phase !='0'";
			$listFiles = new phpCollab\Request();
			$listFiles->openFiles($tmpquery);
			$comptListFiles = count($listFiles->fil_id);
			
			$tmpquery = "WHERE pha.project_id = '".$targetProject->pro_id[0]."' AND pha.order_num ='0'";
			$targetPhase = new phpCollab\Request();
			$targetPhase->openPhases($tmpquery);
			$comptTargetPhase = count($targetPhase->pha_id);			
			
			for($i=0;$i<$comptListTasks;$i++)
			{
				phpCollab\Util::newConnectSql(
                    "UPDATE {$tableCollab["tasks"]} SET parent_phase = 0 WHERE id = :task_id",
                    ["task_id" => $listTasks->tas_id[$i]]);
				phpCollab\Util::newConnectSql(
                    "UPDATE {$tableCollab["files"]} SET phase = :phase WHERE id = :file_id",
                    ["phase" => $targetPhase->pha_id[0], "file_id" => $listFiles->fil_id[$i]]);
			}
			
		}

		//update project
		if ($invoicing == "" || $clod == "1") 
		{
			//nb if the project has not client than the invocing will be deactivated
			$invoicing = "0";
		}
		$updateProjectsSql = "UPDATE {$tableCollab["projects"]} SET name=:name,priority=:priority,description=:description,url_dev=:url_dev,url_prod=:url_prod,owner=:owner,organization=:organization,status=:status,modified=:modified,upload_max=:upload_max,phase_set=:phase_set,invoicing=:invoicing,hourly_rate=:hourly_rate WHERE id = :project_id";
		$dbParams = [];
		$dbParams["name"] = $pn;
		$dbParams["priority"] = $pr;
		$dbParams["description"] = $d;
		$dbParams["url_dev"] = $url_dev;
		$dbParams["url_prod"] = $url_prod;
		$dbParams["owner"] = $pown;
		$dbParams["organization"] = $clod;
		$dbParams["status"] = $st;
		$dbParams["modified"] = $dateheure;
		$dbParams["upload_max"] = $up;
		$dbParams["phase_set"] = $thisPhase;
		$dbParams["invoicing"] = $invoicing;
		$dbParams["hourly_rate"] = $hourly_rate;
		$dbParams["project_id"] = $id;
		phpCollab\Util::newConnectSql($updateProjectsSql, $dbParams);
		unset($dbParams);

		if ($enableInvoicing == "true") 
		{
			phpCollab\Util::newConnectSql(
			    "UPDATE {$tableCollab["invoices"]} SET active = :active WHERE project = :project_id",
                ["active" => $invoicing, "project_id" => $id]
            );
		}

		//if mantis bug tracker enabled
		if ($enableMantis == "true") 
		{
			// call mantis function to copy project
			include '../mantis/proj_update.php';
		}
			phpCollab\Util::headerFunction("../projects/viewproject.php?id=$id&msg=update");
		}
	}


	//set value in form
	$pn = $projectDetail->pro_name[0];
	$d = $projectDetail->pro_description[0];
	$url_dev = $projectDetail->pro_url_dev[0];
	$url_prod = $projectDetail->pro_url_prod[0];
	$hourly_rate = $projectDetail->pro_hourly_rate[0]; 
	$invoicing = $projectDetail->pro_invoicing[0];
}

//case add project
if ($id == "") 
{
    $setTitle .= " : Add Project";

	if ($profilSession != "0" && $profilSession != "1" && $profilSession != "5") 
	{
		phpCollab\Util::headerFunction("../projects/listprojects.php");
	}

	//set organization if add project action done from clientdetail
	if ($organization != "") 
	{
		$projectDetail->pro_org_id[0] = "$organization";
	}

	//set default values
	$projectDetail->pro_mem_id[0] = "$idSession";
	$projectDetail->pro_priority[0] = "3";

	$projectDetail->pro_status[0] = "2";
	$projectDetail->pro_upload_max[0] = $maxFileSize;

	//case add project
	if ($action == "add") 
	{
		//replace quotes by html code in name and description
		$pn = phpCollab\Util::convertData($pn);
		$d = phpCollab\Util::convertData($d);

		if ($invoicing == "" || $clod == "1") 
		{
			$invoicing = "0";
		}

		if ($hourly_rate == "")
		{
			$hourly_rate = "0.00";
		}

		//insert into projects and teams (with last id project)
		$tmpquery1 = "INSERT INTO ".$tableCollab["projects"]."(name,priority,description,owner,organization,status,created,published,upload_max,url_dev,url_prod,phase_set,invoicing,hourly_rate) VALUES('$pn','$pr','$d','$pown','$clod','$st','$dateheure','1','$up','$url_dev','$url_prod','$thisPhase','$invoicing','$hourly_rate')";
		phpCollab\Util::connectSql("$tmpquery1");
		$tmpquery = $tableCollab["projects"];
		phpCollab\Util::getLastId($tmpquery);
		$num = $lastId[0];
		unset($lastId);

		if ($enableInvoicing == "true") 
		{
			$tmpquery3 = "INSERT INTO ".$tableCollab["invoices"]." (project,status,created,active,published) VALUES ('$num','0','$dateheure','$invoicing','1')";
			phpCollab\Util::connectSql($tmpquery3);
		}

		$tmpquery2 = "INSERT INTO ".$tableCollab["teams"]."(project,member,published,authorized) VALUES('$num','$pown','1','0')";
		phpCollab\Util::connectSql("$tmpquery2");

		//if CVS repository enabled
		if ($enable_cvs == "true") 
		{
			$user_query = "WHERE mem.id = '$pown'";
			$cvsUser = new phpCollab\Request();
			$cvsUser->openMembers($user_query);
			cvs_add_repository($cvsUser->mem_login[0], $cvsUser->mem_password[0], $num);
		}

		//create project folder if filemanagement = true
		if ($fileManagement == "true") 
		{
			phpCollab\Util::createDirectory("files/$num");
		}
	
		if ($htaccessAuth == "true") 
		{
$content = <<<STAMP
AuthName "$setTitle"
AuthType Basic
Require valid-user
AuthUserFile $fullPath/files/$num/.htpasswd
STAMP;
			
				$fp = @fopen("../files/$num/.htaccess",'wb+');
				$fw = fwrite($fp,$content);
				$fp = @fopen("../files/$num/.htpasswd",'wb+');

				$tmpquery = "WHERE mem.id = '$pown'";
				$detailMember = new phpCollab\Request();
				$detailMember->openMembers($tmpquery);

				$Htpasswd = new Htpasswd;
				$Htpasswd->initialize("../files/".$num."/.htpasswd");
				$Htpasswd->addUser($detailMember->mem_login[0],$detailMember->mem_password[0]);
		}

		//if mantis bug tracker enabled
		if ($enableMantis == "true") 
		{
			// call mantis function to copy project
			include '../mantis/proj_add.php';
		}
		
		//create phase structure if enable phase was selected as true
		if($thisPhase != "0")
		{
			$comptThisPhase = count($phaseArraySets[$thisPhase]);
		
			for($i=0;$i<$comptThisPhase;$i++)
			{
				$tmpquery = "INSERT INTO ".$tableCollab["phases"]."(project_id,order_num,status,name) VALUES('$num','$i','0','".$phaseArraySets[$thisPhase][$i]."')";
				phpCollab\Util::connectSql("$tmpquery");
			}
		}

		phpCollab\Util::headerFunction("../projects/viewproject.php?id=$num&msg=add");
	}
}

$bodyCommand = "onLoad='document.epDForm.pn.focus();'";


include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?",$strings["projects"],in));

//case add project

if ($id == "") 
{
	$blockPage->itemBreadcrumbs($strings["add_project"]);
}

//case update or copy project
if ($id != "") 
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=".$projectDetail->pro_id[0],$projectDetail->pro_name[0],in));
	
	if ($docopy == "true") 
	{
		$blockPage->itemBreadcrumbs($strings["copy_project"]);
	} 
	else 
	{
		$blockPage->itemBreadcrumbs($strings["edit_project"]);
	}
}
$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include '../includes/messages.php';
	$blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

//case add project
if ($id == "") 
{
	$block1->form = "epD";
	$block1->openForm("../projects/editproject.php?action=add&#".$block1->form."Anchor");
}

//case update or copy project
if ($id != "") 
{
	$block1->form = "epD";
	$block1->openForm('../projects/editproject.php?id='.$id.'&action=update&docopy='.$docopy."&#".$block1->form."Anchor");
	echo "<input type='hidden' value='".$projectDetail->pro_published[0]."' name='projectPublished'>";
}

if ($error != "") 
{            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

//case add project
if ($id == "") 
{
	$block1->heading($strings["add_project"]);
}

//case update or copy project
if ($id != "") 
{
	if ($docopy == "true") 
	{
		$block1->heading($strings["copy_project"]." : ".$projectDetail->pro_name[0]);
	} 
	else 
	{
		$block1->heading($strings["edit_project"]." : ".$projectDetail->pro_name[0]);
	}
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["name"]." :</td><td><input size='44' value='";

//case copy project
if ($docopy == "true") 
{
	echo $strings["copy_of"];
}

echo "$pn' style='width: 400px' name='pn' maxlength='100' type='text'></td></tr>

<tr class='odd'><td valign='top' class='leftvalue'>".$strings["priority"]." :</td><td><select name='pr'>";

$comptPri = count($priority);

for ($i=0;$i<$comptPri;$i++) 
{
	if ($projectDetail->pro_priority[0] == $i) 
	{
		echo "<option value='$i' selected>$priority[$i]</option>";
	} 
	else 
	{
		echo "<option value='$i'>$priority[$i]</option>";
	}
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["description"]." :</td><td><textarea rows='10' style='width: 400px; height: 160px;' name='d' cols='47'>$d</textarea></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["url_dev"]." :</td><td><input size='44' value='$url_dev' style='width: 400px' name='url_dev' maxlength='100' type='text'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["url_prod"]." :</td><td><input size='44' value='$url_prod' style='width: 400px' name='url_prod' maxlength='100' type='text'></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["owner"]." :</td><td><select name='pown'>";

if ($demoMode == "true") 
{
	$tmpquery = "WHERE (mem.profil = '1' OR mem.profil = '0' OR mem.profil = '5') ORDER BY mem.name";
} 
else 
{
	$tmpquery = "WHERE (mem.profil = '1' OR mem.profil = '0' OR mem.profil = '5') AND mem.id != '2' ORDER BY mem.name";
}
$assignOwner = new phpCollab\Request();

$assignOwner->openMembers($tmpquery);
$comptAssignOwner = count($assignOwner->mem_id);

for ($i=0;$i<$comptAssignOwner;$i++) 
{
	if ($projectDetail->pro_mem_id[0] == $assignOwner->mem_id[$i]) 
	{
		echo "<option value='".$assignOwner->mem_id[$i]."' selected>".$assignOwner->mem_login[$i]." / ".$assignOwner->mem_name[$i]."</option>";
	} 
	else 
	{
		echo "<option value='".$assignOwner->mem_id[$i]."'>".$assignOwner->mem_login[$i]." / ".$assignOwner->mem_name[$i]."</option>";
	}
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["organization"]." :</td><td><select name='clod'>";

if ($clientsFilter == "true" && $profilSession == "1") 
{
	$tmpquery = "WHERE org.owner = '$idSession' AND org.id != '1' ORDER BY org.name";
} 
else 
{
	$tmpquery = "WHERE org.id != '1' ORDER BY org.name";
}

$listClients = new phpCollab\Request();
$listClients->openOrganizations($tmpquery);
$comptListClients = count($listClients->org_id);

if ($projectDetail->pro_org_id[0] == "1") 
{
	echo "<option value='1' selected>".$strings["none"]."</option>";
} 
else 
{
	echo "<option value='1'>".$strings["none"]."</option>";
}


for ($i=0;$i<$comptListClients;$i++) 
{
	if ($projectDetail->pro_org_id[0] == $listClients->org_id[$i]) 
	{
		echo "<option value='".$listClients->org_id[$i]."' selected>".$listClients->org_name[$i]."</option>";
	}
	else 
	{
		echo "<option value='".$listClients->org_id[$i]."'>".$listClients->org_name[$i]."</option>";
	}
}

echo "</select></td></tr>
<tr class='odd'><td valign='top' class='leftvalue'>".$strings["enable_phases"]." :</td><td>

<select name='thisPhase'>";

$compSets = count($phaseArraySets["sets"]);

if($projectDetail->pro_phase_set[0] == "0") 
{
	echo "<option value='0' selected>".$strings["none"]."</option>";
}
else
{
	echo "<option value='0'>".$strings["none"]."</option>";
}

for($i=1;$i<=$compSets;$i++)
{
	if($projectDetail->pro_phase_set[0] == "$i") 
	{
		echo "<option value='$i' selected>".$phaseArraySets["sets"][$i]."</option>";
	}
	else
	{
		echo "<option value='$i'>".$phaseArraySets["sets"][$i]."</option>";
	}
}


echo"</select></td></tr><tr class='odd'><td valign='top' class='leftvalue'>".$strings["status"]." :</td><td><select name='st'>";

$comptSta = count($status);

for ($i=0;$i<$comptSta;$i++) 

{
	if ($projectDetail->pro_status[0] == $i)
	 {
		echo "<option value='$i' selected>$status[$i]</option>";
	} 
	else 
	{
		echo "<option value='$i'>$status[$i]</option>";
	}
}

echo "</select></td></tr>";
if ($fileManagement == "true") 
{
echo "<tr class='odd'><td valign='top' class='leftvalue'>".$strings["max_upload"]." :</td><td><input size='20' value='".$projectDetail->pro_upload_max[0]."' style='width: 150px' name='up' maxlength='100' type='TEXT'> $byteUnits[0]</td></tr>";
}

if ($enableInvoicing == "true")
{
	if ($projectDetail->pro_invoicing[0] == "1") 
	{
		$ckeckedInvoicing = "checked";
	}
	$block1->contentRow($strings["invoicing"],"<input size='32' value='1' name='invoicing' type='checkbox' $ckeckedInvoicing>");
$block1->contentRow($strings["hourly_rate"],"<input size='25' value='$hourly_rate' style='width: 200px' name='hourly_rate' maxlength='50' type='TEXT'>");
}

echo "<tr class='odd'><td valign='top' class='leftvalue'>&nbsp;</td><td><input type='SUBMIT' value='".$strings["save"]."'></td></tr>";

$block1->closeContent();
$block1->closeForm();

include '../themes/'.THEME.'/footer.php';
?>