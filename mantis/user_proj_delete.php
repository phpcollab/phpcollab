<?php
#Application name: PhpCollab
#Status page: 0
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php //include( "mantis/core_API.php" ) ?>
<?php //login_cookie_check() ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	//check_access( MANAGER );
	//echo "f_project_id=".$f_project_id;
	//echo "f_user_id=".$f_user_id;
	$result = proj_user_delete( $f_project_id, $f_user_id );

?>