<?php
/*
** Application name: phpCollab
** Last Edit page: 12/03/2005 
** Path by root: ../clients/editclient.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editclient.php
**
** DESC: screen: modify client data
**
** HISTORY:
** 	2003-10-23	-	main page for client module
**	11/03/2005	-	cleaned html and php
**	12/03/2005	-	fixed msSQL hourly rate bug
** -----------------------------------------------------------------------------
** TO-DO:
**	add more info to client (fax, phone #2, complete address info)
**	add export/import to VCF client data
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

//case update client organization
if ($id != "") 
{

	//test exists selected client organization, redirect to list if not
	$tmpquery = "WHERE org.id = '$id'";
	$clientDetail = new request();
	$clientDetail->openOrganizations($tmpquery);
	$comptClientDetail = count($clientDetail->org_id);
	
	if ($comptClientDetail == "0") 
	{
		Util::headerFunction("../clients/listclients.php?msg=blankClient&".session_name()."=".session_id());
		exit;
	}
}

//case update client organization
if ($id != "") 
{
	if ($action == "update") 
	{
		if ($logoDel == "on") 
		{
			$tmpquery = "UPDATE ".$tableCollab["organizations"]." SET extension_logo='' WHERE id='$id'";
			Util::connectSql("$tmpquery");
			@unlink("../logos_clients/".$id.".$extensionOld");
		}

		$extension = strtolower( substr( strrchr($_FILES['upload']['name'], ".") ,1) );

		if(@move_uploaded_file($_FILES['upload']['tmp_name'], "../logos_clients/".$id.".$extension")) 
		{
			chmod("../logos_clients/".$id.".$extension",0666);
			$tmpquery = "UPDATE ".$tableCollab["organizations"]." SET extension_logo='$extension' WHERE id='$id'";
			Util::connectSql("$tmpquery");
		}

		//replace quotes by html code in name and address
		$cn = Util::convertData($cn);
		$add = Util::convertData($add);
		//$c = Util::convertData($c);
		$comments = Util::convertData($comments);
		$tmpquery = "UPDATE ".$tableCollab["organizations"]." SET name='$cn',address1='$add',phone='$client_phone',url='$url',email='$email',comments='$comments',owner='".Util::fixInt($cown)."',hourly_rate='$hourly_rate' WHERE id = '$id'";
		Util::connectSql("$tmpquery");
		Util::headerFunction("../clients/viewclient.php?id=$id&msg=update&".session_name()."=".session_id());
	}

	//set value in form
	$cn = $clientDetail->org_name[0];
	$add = $clientDetail->org_address1[0];
	$client_phone = $clientDetail->org_phone[0];
	$url = $clientDetail->org_url[0];
	$email = $clientDetail->org_email[0];
	$comments = $clientDetail->org_comments[0];
	$hourly_rate = $clientDetail->org_hourly_rate[0];
    
    $setTitle .= " : Edit Client ($cn)";
}

//case add client organization
if ($id == "") 
{
    $setTitle .= " : Add Client";

	if ($action == "add") 
	{

		//test if name blank
		if ($cn == "") 
		{
			$error = $strings["blank_organization_field"];
		} 
		else 
		{

			//replace quotes by html code in name and address
			$cn = Util::convertData($cn);
			$add = Util::convertData($add);
			$comments = Util::convertData($comments);
			
			//test if name already exists
			$tmpquery = "WHERE org.name = '$cn'";
			$existsClient = new request();
			$existsClient->openOrganizations($tmpquery);
			$comptExistsClient = count($existsClient->org_id);

			if ($comptExistsClient!= "0") 
			{
				$error = $strings["organization_already_exists"];
			} 
			else 
			{

				if ($hourly_rate == "")
				{
				  $hourly_rate = 0.00;
				}
				
				//insert into organizations and redirect to new client organization detail (last id)
				$tmpquery1 = "INSERT INTO ".$tableCollab["organizations"]."(name,address1,phone,url,email,comments,created,owner,hourly_rate) VALUES('$cn','$add','$client_phone','$url','$email','$c','$dateheure','".Util::fixInt($cown)."','$hourly_rate')";

				Util::connectSql("$tmpquery1");
				$tmpquery = $tableCollab["organizations"];
				Util::getLastId($tmpquery);
				$num = $lastId[0];
				unset($lastId);

				$extension = strtolower( substr( strrchr($upload_name, ".") ,1) );

				if (@move_uploaded_file($upload, "../logos_clients/".$num.".$extension")) 
				{
					chmod("../logos_clients/".$num.".$extension", 0666);
					$tmpquery = "UPDATE ".$tableCollab["organizations"]." SET extension_logo='$extension' WHERE id='$num'";
					Util::connectSql("$tmpquery");
				}

				Util::headerFunction("../clients/viewclient.php?id=$num&msg=add&".session_name()."=".session_id());
			}
		}
	}
}

$bodyCommand = "onLoad=\"document.ecDForm.cn.focus();\"";
include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["clients"],in));

if ($id == "") 
{
	$blockPage->itemBreadcrumbs($strings["add_organization"]);
}

if ($id != "") 
{
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=".$clientDetail->org_id[0],$clientDetail->org_name[0],in));
	$blockPage->itemBreadcrumbs($strings["edit_organization"]);
}

$blockPage->closeBreadcrumbs();

if ($msg != "") 
{
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new block();

if ($id == "") 
{
	echo "<a name='".$block1->form."Anchor'></a>\n
	<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../clients/editclient.php?action=add&".session_name()."=".session_id()."\" name=\"ecDForm\" enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000000\">\n";
}

if ($id != "") 
{
	echo "<a name='".$block1->form."Anchor'></a>\n
	<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"../clients/editclient.php?id=$id&action=update&".session_name()."=".session_id()."\" name=\"ecDForm\" enctype=\"multipart/form-data\"><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"100000000\">\n";
}

if ($error != "") 
{            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

if ($id == "") 
{
	$block1->heading($strings["add_organization"]);
}

if ($id != "") 
{
	$block1->heading($strings["edit_organization"]." : ".$clientDetail->org_name[0]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

if ($clientsFilter == "true") 
{
	$selectOwner = "<select name='cown'>";
	$tmpquery = "WHERE (mem.profil = '1' OR mem.profil = '0') AND mem.login != 'demo' ORDER BY mem.name";
	$clientOwner = new request();
	$clientOwner->openMembers($tmpquery);
	$comptClientOwner = count($clientOwner->mem_id);

	for ($i=0;$i<$comptClientOwner;$i++) 
	{
		if ($clientDetail->org_owner[0] == $clientOwner->mem_id[$i] || $idSession == $clientOwner->mem_id[$i]) 
		{
			$selectOwner .= "<option value='".$clientOwner->mem_id[$i]."' selected>".$clientOwner->mem_login[$i]." / ".$clientOwner->mem_name[$i]."</option>";
		} 
		else 
		{
			$selectOwner .= "<option value='".$clientOwner->mem_id[$i]."'>".$clientOwner->mem_login[$i]." / ".$clientOwner->mem_name[$i]."</option>";
		}
	}

	$selectOwner .= "</select>";

	$block1->contentRow($strings["owner"],$selectOwner);
}

$block1->contentRow("* ".$strings["name"],"<input size='44' value='$cn' style='width: 400px' name='cn' maxlength='100' type='TEXT' />");
$block1->contentRow($strings["address"],"<textarea rows='3' style='width: 400px; height: 50px;' name='add' cols='43'>$add</textarea>");
$block1->contentRow($strings["phone"],"<input size='32' value='$client_phone' style='width: 250px' name='client_phone' maxlength='32' type='TEXT' />");
$block1->contentRow($strings["url"],"<input size='44' value='$url' style='width: 400px' name='url' maxlength='2000' type='TEXT' />");
$block1->contentRow($strings["email"],"<input size='44' value='$email' style='width: 400px' name='email' maxlength='2000' type='TEXT' />");
$block1->contentRow($strings["comments"],"<textarea rows='3' style='width: 400px; height: 50px;' name='comments' cols='43'>$c</textarea>");

if ($enableInvoicing == "true") 
{
	$block1->contentRow($strings["hourly_rate"],"<input size='25' value='$hourly_rate' style='width: 200px' name='hourly_rate' maxlength='50' type='TEXT' />");
}

$block1->contentRow($strings["logo"],"<input size=\"44\" style=\"width: 400px\" name=\"upload\" type=\"file\">");

if ($id != "") 
{
	if (file_exists("../logos_clients/".$id.".".$clientDetail->org_extension_logo[0])) 
	{
		$block1->contentRow("","<img src='../logos_clients/".$id.".".$clientDetail->org_extension_logo[0]."' /> <input name='extensionOld' type='hidden' value='".$clientDetail->org_extension_logo[0]."' /><input name='logoDel' type='checkbox' value='on' /> ".$strings["delete"]);
	}
}

$block1->contentRow("","<input type='SUBMIT' value='".$strings["save"]."' />");

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>