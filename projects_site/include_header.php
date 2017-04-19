<?php

if ($projectSession != "" && $changeProject != "true") {
    $projectDetail = $projects->getProjectById($projectSession);

    $teamMember = "false";
    $teamMember = $teams->isTeamMember($projectSession, $idSession);

    if ($teamMember == "false") {
        phpCollab\Util::headerFunction("index.php");
    }
}

echo <<<HTML
$setDoctype
$setCopyright
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="none">
<meta name="description" content="{$setDescription}">
<meta name="keywords" content="{$setKeywords}">
<title>{$setTitle} - 
HTML;

if ($projectSession != "" && $changeProject != "true") {
    echo $projectDetail["pro_name"];
}
if ($projectSession == "" || $changeProject == "true") {
    echo $strings["my_projects"];
}

echo "</title>\n";

echo <<<HEAD
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link rel="stylesheet" href="../themes/{THEME}/css/stylesheet.css">
<script type="text/javascript" src="../javascript/general.js"></script>
<script type="text/JavaScript" src="../javascript/overlib_mini.js"></script>
HEAD;

if ($includeCalendar && $includeCalendar === true) {
    include '../includes/calendar.php';
}

echo <<<HTML
</head>
<body {$bodyCommand}>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%" background="bg_header.jpg">
    <tr>
        <td align="left"><img src="spacer_black.gif" width="1" height="24" border="0" alt=""></td>
        <td align="right"><img src="spacer_black.gif" width="1" height="24" border="0" alt=""></td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" height="95%" width="100%">
    <tr>
HTML;
echo '        <td valign="middle" width="150" bgcolor="#5B7F93" height="75"><img src="../themes/'.THEME.'/images/spacer.gif" width="150" height="75" alt=""></td>';
echo <<< HTML
        <td bgcolor="#EFEFEF" height="75">&nbsp;&nbsp;&nbsp;<b>{$titlePage}</b></td>
    </tr>
    <tr>
        <td valign="top" bgcolor="#C4D3DB"><br/>
            <table cellspacing="2" cellpadding="3" border="0">
HTML;


for ($i = 0; $i < 7; $i++) {
    if ($bouton[$i] == "") {
        $bouton[$i] = "normal";
    }
}

if ($projectSession != "" && $changeProject != "true") {
    echo "<tr><td colspan='2'><b>" . $strings["project"] . " :<br/>" . $projectDetail["pro_name"] . "</b></td></tr>
	<tr><td><img src='ico_arrow_" . $bouton[0] . ".gif' border='0' alt=''></td><td><a href='home.php'>" . $strings["home"] . "</a></td></tr>
	<tr><td><img src='ico_arrow_" . $bouton[1] . ".gif' border='0' alt=''></td><td><a href='showallcontacts.php'>" . $strings["project_team"] . "</a></td></tr>
	<tr><td><img src='ico_arrow_" . $bouton[2] . ".gif' border='0' alt=''></td><td><a href='showallteamtasks.php'>" . $strings["team_tasks"] . "</a></td></tr>";

    if ($projectDetail["pro_organization"] != "" && $projectDetail["pro_organization"] != "1") {
        echo "<tr><td><img src='ico_arrow_" . $bouton[3] . ".gif' border='0' alt=''></td><td><a href='showallclienttasks.php'>" . $strings["client_tasks"] . "</a></td></tr>";
    }

    if ($fileManagement == "true") {
        echo "<tr><td><img src='ico_arrow_" . $bouton[4] . ".gif' border='0' alt=''></td><td><a href='doclists.php'>" . $strings["document_list"] . "</a></td></tr>";
    }

    echo "<tr><td><img src='ico_arrow_" . $bouton[5] . ".gif' border='0' alt=''></td><td><a href='showallthreadtopics.php'>" . $strings["bulletin_board"] . "</a></td></tr>";
    echo "<tr><td><img src='ico_arrow_" . $bouton[6] . ".gif' border='0' alt=''></td><td><a href='showcalendar.php'>" . $strings["calendar"] . "</a></td></tr>";

    if ($enableHelpSupport == "true") {
        echo "<tr><td><img src='ico_arrow_" . $bouton[6] . ".gif' border='0' alt=''></td><td><a href='showallsupport.php?project=$projectSession'>" . $strings["support"] . "</a></td></tr>";
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        include 'navigation.php';
        echo "<tr><td><img src='ico_arrow_" . $bouton[6] . ".gif' border='0' alt=''></td><td><a href='javascript:onClick= document.login.submit();'>" . $strings["bug"] . "</a></td></tr></form>";
    }

    echo "</table>
	<br/><hr>";
}

echo "
	<table cellspacing='2' cellpadding='3' border='0'>
		<tr>
			<td><a href='home.php?changeProject=true'><img src='ico_folder.gif' border='0' alt=''></a></td>
			<td><a href='home.php?changeProject=true'>" . $strings["my_projects"] . "</a></td>
		</tr>
		<tr><td colspan='2'><br/></td></tr>
		<tr>
			<td><a href='changepassword.php?changeProject=true'><img src='ico_prefs.gif' border='0' alt=''></a></td>
			<td><a href='changepassword.php?changeProject=true'>" . $strings["preferences"] . "</a></td>
		</tr>
		<tr><td colspan='2'><br/></td></tr>";

if ($profilSession != '3') {
    echo "<tr><td><a href='../general/home.php'><img src='ico_folder.gif' border='0' alt='0'></a></td><td><a href='../general/home.php'>Team Site</a></td></tr><tr><td colspan=2><br/></td></tr>";
}

echo "<tr><td><a href='../general/login.php?logout=true'><img src='ico_logout.gif' border='0' alt=''></a></td><td><a href='../general/login.php?logout=true'>" . $strings["logout"] . "</a></td></tr>
</table>

</td>
<td valign='top' width='100%'>

<table cellpadding='20' cellspacing='0' border='0' width='100%'><tr><td width='100%'>";
