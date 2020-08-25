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

	//check_access( ADMINISTRATOR );

	# get all form values from phpcollab
	$f_id	= $session->get("id");
	$f_email = trim($em);

	//echo "f_id	= ".$f_id;
	//echo "new email	= ".$f_email;

	# create the almost unique string for each user then insert into the table
	$t_cookie_string = create_cookie_string();

	// Modify it's update action , so new cookie string will created.
	# update action
	$query = "UPDATE $g_mantis_user_table
		    	SET email='$f_email', cookie_string = '$t_cookie_string'
		    		WHERE id='$f_id'";

    	$result = db_query( $query );
?>