<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../projects/exportproject.php

use phpCollab\Util;

$export = "true";

$checkSession = "true";
require_once '../includes/library.php';


$projects = $container->getProjectsLoader();
$tasks = $container->getTasksLoader();

$projectDetail = $projects->getProjectById($request->query->get("id"));

if ($projectDetail) {
    if ($projectDetail["pro_org_id"] == "1") {
        $projectDetail["pro_org_name"] = $strings["none"];
    }
    $idStatus = $projectDetail["pro_status"];
    $idPriority = $projectDetail["pro_priority"];

    $export_csv = [];
    array_push($export_csv, array($strings["this_report_generated_by"]));
    array_push($export_csv, array($strings["project"]));
    array_push($export_csv, array(
        $strings["name"],
        $strings["description"],
        $strings["owner"],
        $strings["priority"],
        $strings["status"],
        $strings["created"],
        $strings["organization"]
    ));
    array_push($export_csv, array(
        $projectDetail["pro_name"],
        $projectDetail["pro_description"],
        $projectDetail["pro_mem_login"],
        $priority[$idPriority],
        $status[$idStatus],
        Util::createDate($projectDetail["pro_created"], $session->get("timezone")),
        $projectDetail["pro_org_name"]
    ));

    $listTasks = $tasks->getTasksByProjectId($request->query->get("id"));

    if ($listTasks) {
        array_push($export_csv, array($strings["tasks"]));
        array_push($export_csv,
            array(
                $strings["name"],
                $strings["description"],
                $strings["owner"],
                $strings["priority"],
                $strings["status"],
                $strings["created"],
                $strings["start_date"],
                $strings["due_date"],
                $strings["complete_date"],
                $strings["completion"],
                $strings["scope_creep"],
                $strings["estimated_time"],
                $strings["actual_time"],
                $strings["published"],
                $strings["comments"],
                $strings["assigned"],
                $strings["assigned_to"]
            )
        );

        foreach ($listTasks as $task) {
            if ($task["tas_assigned_to"] == "0") {
                $task["tas_mem_login"] = $strings["unassigned"];
            }
            $idStatus = $task["tas_status"];
            $idPriority = $task["tas_priority"];
            $idPublish = $task["tas_published"];
            $complValue = ($task["tas_completion"] > 0) ? $task["tas_completion"] . "0 %" : $task["tas_completion"] . " %";

            if ($task["tas_complete_date"] != "" && $task["tas_complete_date"] != "--" && $task["tas_due_date"] != "--") {
                $diff = phpCollab\Util::diffDate($task["tas_complete_date"], $task["tas_due_date"]);
            }
            array_push($export_csv,
                array(
                    $task["tas_name"],
                    $task["tas_description"],
                    $task["tas_mem2_login"],
                    $priority[$idPriority],
                    $status[$idStatus],
                    phpCollab\Util::createDate($task["tas_created"], $session->get("timezone")),
                    $task["tas_start_date"],
                    $task["tas_due_date"],
                    $task["tas_complete_date"],
                    $complValue,
                    $diff,
                    $task["tas_estimated_time"],
                    $task["tas_actual_time"],
                    $statusPublish[$idPublish],
                    $task["tas_comments"],
                    $task["tas_assigned"],
                    $task["tas_mem_login"]
                )
            );
        }
    }
    $filename = <<<FILENAME
{$strings["project"]}_{$projectDetail["pro_name"]}_{$projectDetail["pro_id"]})
FILENAME;
    $separator = ',';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $fp = fopen('php://output', 'wb');
    foreach ($export_csv as $line) {
        fputcsv($fp, $line, $separator);
    }
    fclose($fp);
}


