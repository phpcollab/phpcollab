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
//check_access( ADMINISTRATOR );

# delete account
if ($f_protected != "on") {
    # Remove aaccount
    $query = "DELETE
    			FROM $g_mantis_user_table
    			WHERE id IN($id)";
    $result = db_query($query);

    # Remove associated profiles
    $query = "DELETE
	    		FROM $g_mantis_user_profile_table
	    		WHERE user_id IN($id)";
    $result = db_query($query);

    # Remove associated preferences
    $query = "DELETE
    			FROM $g_mantis_user_pref_table
    			WHERE user_id IN($id)";
    $result = db_query($query);

    $query = "DELETE
    			FROM $g_mantis_project_user_list_table
	    		WHERE user_id IN($id)";
    $result = db_query($query);
}
