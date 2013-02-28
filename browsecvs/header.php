<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../browsecvs/header.php
** Authors: Ceam / TY / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: header.php
**
** DESC: Screen: cvs header
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	check template usage
**
** =============================================================================
*/

if ($use_compression==1) ob_start("ob_gzhandler");

$themepath = "modules/themes/".$themes_list->get_dir($userdata['theme'])."/";
require_once($themepath."index.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title><?php echo $sitename; ?></title>
	<link href="<?php echo $themepath; ?>style.css" rel="stylesheet" type="text/css">
</head>
<?php
$theme->header();
?>
