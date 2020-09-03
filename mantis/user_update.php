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

# get all form values from phpcollab
$f_id = $id;
$f_username = trim($un);
$f_password = trim($pw);
$f_realname = trim($fn);
$f_email = trim($em);

$f_protected = 0;  // default value
$f_enabled = 1;    // default value

# update action
# administrator is not allowed to change access level or enabled
# this is to prevent screwing your own account
if ($pw != "" && ($pw != $pwa || $pwa == "")) {

    if (ON == $f_protected) {
        $query = "UPDATE $g_mantis_user_table
		    		SET username='$f_username', email='$f_email', realname='$f_realname',
		    			password= '$pw', protected='$f_protected'
		    		WHERE id='$f_id'";
    } else {
        $query = "UPDATE $g_mantis_user_table
		    		SET username='$f_username', email='$f_email',
		    			access_level='$f_access_level', enabled='$f_enabled', realname='$f_realname',
		    			password= '$pw',protected='$f_protected'
		    		WHERE id='$f_id'";
    }
} else {
    if (ON == $f_protected) {
        $query = "UPDATE $g_mantis_user_table
		    		SET username='$f_username', email='$f_email', realname='$f_realname',
		    			protected='$f_protected' 
		    		WHERE id='$f_id'";
    } else {
        $query = "UPDATE $g_mantis_user_table
		    		SET username='$f_username', email='$f_email',
		    			access_level='$f_access_level', enabled='$f_enabled', realname='$f_realname',
		    			protected='$f_protected'
		    		WHERE id='$f_id'";
    }
}

$result = db_query($query);
