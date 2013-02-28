<?php
#Application name: PhpCollab
#Status page: 0
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details
?>
<?php include( "../mantis/core_API.php" ) ?>
<?php
	db_connect( $g_hostname, $g_db_username, $g_db_password, $g_database_name );
	//check_access( ADMINISTRATOR );

	# get all form values from phpcollab
	$f_username = trim($un);
	$f_password = trim($pw);
	$f_email = trim($em);
	$f_protected = 0;  // default value
	$f_enabled = 1;	// default value
	$f_realname = trim($fn);

	// dodane w celu lepszej integracji z mantisem (zrodlo: software 2.0)
	if ($perm == "1") $f_access_level = 70;
		else if ($perm == "2") $f_access_level = 55;
		else if ($perm == "3") $f_access_level = 25;
		else if ($perm == "4") $f_access_level = 0;
		else if ($perm == "5") $f_access_vele = 90;
		else $f_access_level = 0;
	


	# create the almost unique string for each user then insert into the table
	$t_cookie_string = create_cookie_string();
    $query = "INSERT
    		INTO $g_mantis_user_table
    		( id, username, email, realname, password, date_created, last_visit,
    		access_level, enabled, protected, cookie_string )
			VALUES
			( $num, '$f_username', '$f_email', '$f_realname' ,'$f_password', NOW(), NOW(),
			'$f_access_level', '$f_enabled', '$f_protected', '$t_cookie_string')";
    $result = db_query( $query );

   	# Use this for MS SQL: SELECT @@IDENTITY AS 'id'
	$t_user_id = $num;

	# Create preferences

    $query = "INSERT
    		INTO $g_mantis_user_pref_table
    		(id, user_id, project_id,
    		advanced_report, advanced_view, advanced_update,
    		refresh_delay, redirect_delay,
    		email_on_new, email_on_assigned,
    		email_on_feedback, email_on_resolved,
    		email_on_closed, email_on_reopened,
    		email_on_bugnote, email_on_status,
    		email_on_priority, language)
    		VALUES
    		(null, '$t_user_id', '0000000',
    		'$g_default_advanced_report', '$g_default_advanced_view', '$g_default_advanced_update',
    		'$g_default_refresh_delay', '$g_default_redirect_delay',
    		'$g_default_email_on_new', '$g_default_email_on_assigned',
    		'$g_default_email_on_feedback', '$g_default_email_on_resolved',
    		'$g_default_email_on_closed', '$g_default_email_on_reopened',
    		'$g_default_email_on_bugnote', '$g_default_email_on_status',
    		'$g_default_email_on_priority', '$g_default_language')";
    $result = db_query($query);
?>