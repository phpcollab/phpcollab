<?php

$bouton = $GLOBALS['bouton'];

echo <<<HTML
$setDoctype
$setCopyright
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="robots" content="none">
    <meta name="description" content="$setDescription">
    <meta name="keywords" content="$setKeywords">
    <link rel="manifest" href="../public/site.webmanifest">
    <title>$setTitle
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../themes/{$session->get("theme")}/css/stylesheet.css">
    <link rel="stylesheet" href="../public/css/fa-all.min.css">
    
    <script type="text/javascript" src="../javascript/general.js"></script>
    <script type="text/JavaScript" src="../javascript/overlib_mini.js"></script>
HTML;

if ($includeCalendar && $includeCalendar === true) {
    include '../includes/calendar.php';
}

$theme = THEME;

echo <<<HTML
</head>
<body $bodyCommand>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>

<div id="topBar"></div>

<table style="height: 95%; width: 100%" class="nonStriped">
    <tr>
        <td id="logoBox" >
HTML;
// Check to see if the organization has a logo set and the file exists
//if ($clientDetail["org_extension_logo"] && file_exists("../logos_clients/" . $clientDetail["org_id"] . "." . $clientDetail["org_extension_logo"])) {
if ($session->get("clientDetail")["org_extension_logo"] && file_exists("../logos_clients/" . $session->get("clientDetail")["org_id"] . "." . $session->get("clientDetail")["org_extension_logo"])) {
    $image = $session->get("clientDetail")["org_id"] . '.' . $session->get("clientDetail")["org_extension_logo"];
    echo '<img alt="'. $session->get("clientDetail")["org_name"] . ' Logo" src="../logos_clients/' . $image . '">';
}

echo <<<HTML
</td>
        <td id="pageTitle">$titlePage</td>
    </tr>
    <tr>
        <td style="vertical-align: top; background-color: #C4D3DB;"><br/>
            <table class="nonStriped">
HTML;

for ($i = 0; $i < 7; $i++) {
    if ($bouton[$i] == "") {
        $bouton[$i] = "normal";
    }
}

if ($session->get("project") != "" && $changeProject != "true") {
    echo <<<HTML
                <tr>
                    <td colspan="2"><b>{$strings["project"]} :<br/>{$projectDetail["pro_name"]}</b></td>
                </tr>
                <tr>
                    <td><img src="ico_arrow_$bouton[0].gif" alt=""></td>
                    <td><a href="home.php">{$strings["home"]}</a></td>
                </tr>
                <tr>
                    <td><img src="ico_arrow_$bouton[1].gif" alt=""></td>
                    <td><a href="showallcontacts.php">{$strings["project_team"]}</a></td>
                </tr>
                <tr>
                    <td><img src="ico_arrow_$bouton[2].gif" alt=""></td>
                    <td><a href="showallteamtasks.php">{$strings["team_tasks"]}</a></td>
                </tr>
HTML;
    if ($session->get("projectDetail")["pro_organization"] && $session->get("projectDetail")["pro_organization"] != "1") {
        echo <<<TR
                <tr>
                    <td><img src="ico_arrow_$bouton[3].gif" alt=""></td>
                    <td><a href="showallclienttasks.php">{$strings["client_tasks"]}</a></td>
                </tr>
TR;
    }

    if ($fileManagement == "true") {
        echo <<<TR
                <tr>
                    <td><img src="ico_arrow_$bouton[4].gif" alt=""></td>
                    <td><a href="doclists.php">{$strings["document_list"]}</a></td>
                </tr>
TR;
    }

    echo <<<TR
                <tr>
                    <td><img src="ico_arrow_$bouton[5].gif" alt=""></td>
                    <td><a href="showallthreadtopics.php">{$strings["bulletin_board"]}</a></td>
                </tr>
                <tr>
                    <td><img src="ico_arrow_$bouton[6].gif" alt=""></td>
                    <td><a href="showcalendar.php">{$strings["calendar"]}</a></td>
                </tr>
TR;


    if ($enableHelpSupport == "true") {
        echo <<<TR
                <tr>
                    <td><img src="ico_arrow_$bouton[6].gif" alt=""></td>
                    <td><a href="showallsupport.php?project={$session->get("project")}">{$strings["support"]}</a></td>
                </tr>
TR;
    }

    //if mantis bug tracker enabled
    if ($enableMantis == "true") {
        include "navigation.php";
        echo <<<TR
                <tr>
                    <td><img src="ico_arrow_$bouton[6].gif" alt=""></td><td><a href="javascript:onClick= document.login.submit();">{$strings["bug"]}</a></td>
                </tr>
                </form>
TR;
    }

    echo "</table><br/><hr>";
}

echo <<<sidebar
	<table class="nonStriped">
		<tr>
			<td><a href="home.php?changeProject=true"><img src="ico_folder.gif" alt=""></a></td>
			<td><a href="home.php?changeProject=true">{$strings["my_projects"]}</a></td>
		</tr>
		<tr><td colspan="2"><br/></td></tr>
		<tr>
			<td><a href="changepassword.php?changeProject=true"><img src="ico_prefs.gif" alt=""></a></td>
			<td><a href="changepassword.php?changeProject=true">{$strings["preferences"]}</a></td>
		</tr>
		<tr><td colspan="2"><br/></td></tr>
sidebar;

if ($session->get("profile") != "3") {
    echo <<<TR
        <tr>
            <td><a href="../general/home.php"><img src="ico_folder.gif" alt="0"></a></td>
            <td><a href="../general/home.php">Team Site</a></td></tr><tr><td colspan=2><br/></td>
        </tr>
TR;
}

echo <<<HTML
        <tr>
            <td><a href="../general/logout.php"><img src="ico_logout.gif" alt=""></a></td>
            <td><a href="../general/logout.php">{$strings["logout"]}</a></td>
        </tr>
    </table>
</td>
<td style="vertical-align: top; width: 100%">
    <table style="width: 100%" class="nonStriped">
        <tr>
            <td style="width: 100%">
HTML;
