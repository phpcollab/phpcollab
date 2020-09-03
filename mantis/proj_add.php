<?php
#Application name: PhpCollab
#Status page: 0
# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php include("../mantis/core_API.php") ?>
<?php //login_cookie_check() ?>
<?php
db_connect($g_hostname, $g_db_username, $g_db_password, $g_database_name);
//check_access( MANAGER );

$f_project_id = $num; // project Id from phpcollab
$f_name = string_prepare_textarea($pn);
$f_description = string_prepare_textarea($d);
$f_status = 10; // development default phase
$f_view_state = 10;  // private =50 or public =10
$f_user_id = $pown;
$f_access_level = $team_user_level; // default developer
$f_file_path = "";

# dump file content to the connection.
if (DISK == $g_file_upload_method) {
    $f_file_path = $g_file_path . $f_name;
    @mkdir("$f_file_path", 0755);
    @chmod("$f_file_path", 0777);
}

# Make sure file path has trailing slash
if ($f_file_path[strlen($f_file_path) - 1] != "/") {
    $f_file_path = $f_file_path . "/";
}

$result = 0;
$duplicate = is_duplicate_project($f_name);

if (!empty($f_name) && !$duplicate) {
    # Add item
    $query = "INSERT
				INTO $g_mantis_project_table
				( id, name, status, enabled, view_state, file_path, description )
				VALUES
				( '$f_project_id', '$f_name', '$f_status', '1', '$f_view_state', '$f_file_path', '$f_description' )";
    $result = db_query($query);
}

// add current user(i.e creator of this project) to this project to synchronize with phpcollab

# Add a user to project(s)
$result = 0;
$result = proj_user_add($f_project_id, $f_user_id, $f_access_level);
