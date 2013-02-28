<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../general/systemrequirements.php
** Authors: Ceam / Fullo / kiles / mashbe
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: systemrequirements.php
**
** DESC: Screen: show system requirements and features
**
** HISTORY:
**	2003-08-01	-	added features paragraph
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

$checkSession = "false";
include_once('../includes/library.php');

$notLogged = "true";
include('../themes/'.THEME.'/header.php');

$blockPage = new block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("&nbsp;");
$blockPage->closeBreadcrumbs();

$block1 = new block();
$block1->heading($setTitle . " : ".$strings["requirements"]);

$block1->openContent();
$block1->contentTitle($strings["requirements"]);

$block1->contentRow("Windows","- Internet Explorer 5.x, 6.x<br/>- Netscape 6.x");
$block1->contentRow("Macintosh","- Internet Explorer 5.x");
$block1->contentRow("Linux","- Mozilla<br/>- Galeon");

$block1->closeContent();

// 21/06/2003 kiles & mashbe

$block2 = new block();
$block2->heading($setTitle . " : Quick Start");

$block2->openContent();
$block2->contentTitle("Introduction");

$block2->contentRow("1-","phpCollab is a project management system. It is comprised of two interfaces:");
$block2->contentRow("a","User, which includes admin, employees, co-workers, etc.");
$block2->contentRow("b","Client.");
$block2->contentRow("2-","A central login handles everything.");
$block2->contentRow("a","If a client logs in, then the client is directed to the client interface.");
$block2->contentRow("b","If a team user logs in, then they are directed to the user interface.");

$block2->contentTitle("User Features");
$block2->contentRow("1-","Within each project you have the following features:");
$block2->contentRow("a","Project Overview");
$block2->contentRow("b","Phases (optional)");
$block2->contentRow("c","Tasks & Sub tasks");
$block2->contentRow("d","Discussions");
$block2->contentRow("e","Team Members");
$block2->contentRow("f","Linked Content (uploaded files)");
$block2->contentRow("g","Notes");
$block2->contentRow("2-","In addition, you also have a few global features such as:");
$block2->contentRow("a","Reports");
$block2->contentRow("b","Calendar");
$block2->contentRow("c","Search");
$block2->contentRow("d","Bookmarks");
$block2->contentRow("3-","One very useful feature is the <b>Home</b> page. This page pulls up all the relevant items for that individual user in one place. This is also the first page that a user sees when logging into the system. So, a user can easily determine what tasks are due, or what projects they are involved in.");

$block2->contentTitle("Client Features");
$block2->contentRow("1-","Having a unique interface for your clients to use allows your clients to interact with the project team members.");
$block2->contentRow("a","Please note that you must <b>publish</b> any of the items mentioned in order for a client to be able to see it.");
$block2->contentRow("b","At all times you maintain full control of the project.");
$block2->contentRow("2-","When a client first logs into phpCollab they are presented with a <b>Home</b> page that shows all projects that have been created for them. Once a client selects a project they will have the following options:");
$block2->contentRow("a","Project Team - a list of users assigned to the project.");
$block2->contentRow("b","Team Tasks - all the tasks assigned to the project team");
$block2->contentRow("c","Client Tasks - all the tasks assigned to the client");
$block2->contentRow("d","Document List - a list of all the documents uploaded in the <b>Linked Content</b>");
$block2->contentRow("e","Bulletin Board - interfaces with the <b>Discussion</b> section found in the user interface");
$block2->contentRow("f","Support - a client can submit a task as a support request to the project team");

$block2->contentTitle("Publishing");
$block2->contentRow("1-","By default, a client will not be able to view anything.");
$block2->contentRow("a","Throughout a project you have the option of <b>publishing</b> items, such as tasks, linked content, team members, etc. Once published, your clients will be able to view the item.");
$block2->contentRow("b","You also have the option of <b>un-publishing</b> items as well.");

$block2->contentTitle("What it does NOT do");
$block2->contentRow("1-","phpCollab is a fantastic program that can give you the tools necessary to manage all of your tasks and projects. However, there is one misconception.");
$block2->contentRow("a","phpCollab does not publish your web site. phpCollab manages your projects, not your web site.");
$block2->closeContent(); 

include('../themes/'.THEME.'/footer.php');
?>