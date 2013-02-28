<?php
/* $Id: header.inc.php,v 1.1 2003/07/02 14:47:06 fullo Exp $ */
if (!defined("HEAD_LIB")) {
	if (!isset($no_include))
	   include("lib.inc.php");
	
	?>
<html>
	<head>
	<title><?php echo $cfgProgName, ": ", $cfgServer[host]; ?></title>
	<style type="text/css">
	//<!--
	body {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt}
	th   {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; font-weight: bold; background-color: <?php echo $cfgThBgcolor;?>;}
	td   {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt;}
	form   {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt}
	h1   {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 16pt; font-weight: bold}
	A:link    {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: none; color: blue}
	A:visited {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: none; color: blue}
	A:hover   {  font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-decoration: underline; color: red}
	A:link.nav {  font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000}
	A:visited.nav {  font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000}
	A:hover.nav {  font-family: Verdana, Arial, Helvetica, sans-serif; color: red;}
	.nav {  font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000}
	.generic {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size:10pt; color: #000000}
	
	//-->
	</style>
	<!--META HTTP-EQUIV="Expires" CONTENT="Fri, Jun 12 1981 08:20:00 GMT"-->
	<!--META HTTP-EQUIV="Pragma" CONTENT="no-cache"-->
	<!--META HTTP-EQUIV="Cache-Control" CONTENT="no-cache"-->
	</head>
	
	<body bgcolor="#F5F5F5" text="#000000" background="images/bkg.gif">
	<?php
	// echo $conn_str;

	if (isset($db) && $db != $cfgDefaultDB && $db != "phppgadmin") {
		echo "<h1> $strDatabase $db";
		if (isset($table))
			echo " - $strTable $table";
		if (isset($function))
			echo " - $strFunc $function";
		if (isset($view))
			echo " - $strView $view";
		if (isset($index))
			echo " - $strIndex $index";
		echo "</h1>";
	}
	define("HEAD_LIB", true);
}
?>
