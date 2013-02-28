<?php
#Application name: PhpCollab
#Status page: 0
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( "../mantis/core_API.php" ) ?>
<?php //login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	//check_access( MANAGER );

	$f_project_id	= $id;
	$f_enabled = 1;
	$f_name 		= string_prepare_textarea( $pn );
	$f_description 	= string_prepare_textarea( $d );
	$f_status		= 10; // development default phase
	$f_view_state	= 10;  // private =50 or public =10 
	$f_file_path	= "";
	
	# dump file content to the connection.
	if ( DISK == $g_file_upload_method ) {
		$f_file_path	= $g_file_path.$f_name;
		@mkdir("$f_file_path",0755);
		@chmod("$f_file_path",0777);
	}

	# Make sure file path has trailing slash
	if ( $f_file_path[strlen($f_file_path)-1] != "/" ) {
		$f_file_path = $f_file_path."/";
	}

	# Update entry
	$query = "UPDATE $g_mantis_project_table
			SET name='$f_name',
				status='$f_status',
				enabled='$f_enabled',
				view_state='$f_view_state',
				file_path='$f_file_path',
				description='$f_description'
    		WHERE id='$f_project_id'";
    $result = db_query( $query );

?>