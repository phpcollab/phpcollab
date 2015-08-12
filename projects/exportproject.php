<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../projects/exportproject.php

$export = "true";

$checkSession = "false";
include_once '../includes/library.php';

require("../includes/phpmyadmin/defines.lib.php");
    function which_crlf()
    {
        $the_crlf = "\n";

        // The 'USR_OS' constant is defined in "./libraries/defines.lib.php"
        // Win case
        if (USR_OS == 'Win') {
            $the_crlf = "\r\n";
        }
        // Mac case
        else if (USR_OS == 'Mac') {
            $the_crlf = "\r";
        }
        // Others
        else {
            $the_crlf = "\n";
        }

        return $the_crlf;
    }


@set_time_limit(600);
$crlf        = which_crlf();

/**
 * Send headers depending on whether the user choosen to download a dump file
 * or not
 */

$tmpquery = "WHERE pro.id = '$id'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_org_id[0] == "1") {
	$projectDetail->pro_org_name[0] = $strings["none"];
}
$idStatus = $projectDetail->pro_status[0];
$idPriority = $projectDetail->pro_priority[0];

$dump_buffer .= $strings["project"].$crlf;
$dump_buffer .= "\"".$strings["name"]."\";\"".$strings["description"]."\";\"".$strings["owner"]."\";\"".$strings["priority"]."\";\"".$strings["status"]."\";\"".$strings["created"]."\";\"".$strings["organization"]."\"".$crlf;
$dump_buffer .= "\"".$projectDetail->pro_name[0]."\";\"".$projectDetail->pro_description[0]."\";\"".$projectDetail->pro_mem_login[0]."\";\"".$priority[$idPriority]."\";\"".$status[$idStatus]."\";\"".Util::createDate($projectDetail->pro_created[0],$timezoneSession)."\";\"".$projectDetail->pro_org_name[0]."\"".$crlf.$crlf;

$tmpquery = "WHERE tas.project = '$id'";
$listTasks = new Request();
$listTasks->openTasks($tmpquery);
$comptListTasks = count($listTasks->tas_id);

if ($comptListTasks != "0") {
$dump_buffer .= $strings["tasks"].$crlf;
$dump_buffer .= "\"".$strings["name"]."\";\"".$strings["description"]."\";\"".$strings["owner"]."\";\"".$strings["priority"]."\";\"".$strings["status"]."\";\"".$strings["created"]."\";\"".$strings["start_date"]."\";\"".$strings["due_date"]."\";\"".$strings["complete_date"]."\";\"".$strings["completion"]."\";\"".$strings["scope_creep"]."\";\"".$strings["estimated_time"]."\";\"".$strings["actual_time"]."\";\"".$strings["published"]."\";\"".$strings["comments"]."\";\"".$strings["assigned"]."\";\"".$strings["assigned_to"]."\"".$crlf;

for ($i=0;$i<$comptListTasks;$i++) {

if ($listTasks->tas_assigned_to[$i] == "0") {
	$listTasks->tas_mem_login[$i] = $strings["unassigned"];
}
$idStatus = $listTasks->tas_status[$i];
$idPriority = $listTasks->tas_priority[$i];
$idPublish = $listTasks->tas_published[$i];
$complValue = ($listTasks->tas_completion[$i]>0) ? $listTasks->tas_completion[$i]."0 %": $listTasks->tas_completion[$i]." %"; 

if ($listTasks->tas_complete_date[$i] != "" && $listTasks->tas_complete_date[$i] != "--" && $listTasks->tas_due_date[$i] != "--") {
	$diff = Util::diffDate($listTasks->tas_complete_date[$i],$listTasks->tas_due_date[$i]);
}
$dump_buffer .= "\"".$listTasks->tas_name[$i]."\";\"".$listTasks->tas_description[$i]."\";\"".$listTasks->tas_mem2_login[$i]."\";\"".$priority[$idPriority]."\";\"".$status[$idStatus]."\";\"".Util::createDate($listTasks->tas_created[$i],$timezoneSession)."\";\"".$listTasks->tas_start_date[$i]."\";\"".$listTasks->tas_due_date[$i]."\";\"".$listTasks->tas_complete_date[$i]."\";\"".$complValue."\";\"$diff\";\"".$listTasks->tas_estimated_time[$i]."\";\"".$listTasks->tas_actual_time[$i]."\";\"".$statusPublish[$idPublish]."\";\"".$listTasks->tas_comments[$i]."\";\"".$listTasks->tas_assigned[$i]."\";\"".$listTasks->tas_mem_login[$i]."\"".$crlf;
}
}

$filename = $strings["project"].$projectDetail->pro_id[0];

        $ext       = 'csv';
        $mime_type = 'text/x-csv';

    // Send headers
    header('Content-Type: ' . $mime_type);
    // lem9: we need "inline" instead of "attachment" for IE 5.5
    $content_disp = (USR_BROWSER_AGENT == 'IE') ? 'inline' : 'attachment';
    header('Content-Disposition:  ' . $content_disp . '; filename="' . $filename . '.' . $ext . '"');
    header('Pragma: no-cache');
    header('Expires: 0');


echo $dump_buffer;
?>