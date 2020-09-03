<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../includes/calendar.php
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
** FILE: calendar.php
**
** DESC: Screen:	calendar js file, this file is included every time a calendar js
**					popup is needed
**
** HISTORY:
** 	04/12/2004	-	added new document info
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
** -----------------------------------------------------------------------------
** TO-DO:
**
**
** =============================================================================
*/


if (file_exists("../javascript/calendar/lang/calendar-{$session->get("language")}.js")) {
    $calendar_lang = $session->get("language");
} else {
    $calendar_lang = 'en';
}


$calendar_common_settings = "ifFormat: '%Y-%m-%d', singleClick: true, step: 1,weekNumbers: false";


$build = <<<END
<script type="text/javascript" src="../javascript/calendar/calendar.js"></script>
<script type="text/javascript" src="../javascript/calendar/lang/calendar-{$calendar_lang}.js"></script>
<script type="text/javascript" src="../javascript/calendar/calendar-setup.js"></script>
END;
echo "<link rel='stylesheet' href='../themes/" . THEME . "/css/calendar.css' type='text/css' />";

echo $build;
