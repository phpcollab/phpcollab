<?php
#Application name: PhpCollab
#Status page: 0
include_once('../includes/settings.php');
	
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000, 2001  Kenzaburo Ito - kenito@300baud.org
	# This program is distributed under the terms and conditions of the GPL
	# See the files README and LICENSE for details

	# This sample file contains the essential files that you MUST
	# configure to your specific settings.  You may override settings
	# from default/config_inc1.php by assigning new values in this file

	# Rename this file to config_inc.php after configuration.

	###########################################################################
	# CONFIGURATION VARIABLES
	###########################################################################

	# In general a value of 0 means the feature is disabled and 1 means the
	# feature is enabled.  Any other cases will have an explanation.

	# Look in configuration.html or default/config_inc1.php for more
	# detailed comments.

	# --- database variables ---------

	# set these values to match your setup
	$g_hostname      = MYSERVER;
	$g_port          = 3306;         # 3306 is default
	$g_db_username   = MYLOGIN;
	$g_db_password   = MYPASSWORD;
	$g_database_name = MYDATABASE;

	##################################
	# Mantis Default Preferences
	##################################

	# --- signup default ---------------
	# look in constant_inc.php for values
	$g_default_new_account_access_level = REPORTER;

	# --- viewing defaults ------------
	# site defaults for viewing preferences
	$g_default_limit_view         = 50;
	$g_default_show_changed       = 6;
	$g_hide_closed_default        = ON;

	# make sure people aren't refreshing too often
	$g_min_refresh_delay          = 10;    # in minutes

	# --- account pref defaults -------
	# BOTH, SIMPLE_ONLY, ADVANCED_ONLY
	$g_default_advanced_report    = BOTH;
	$g_default_advanced_view      = BOTH;
	$g_default_advanced_update    = BOTH;
	$g_default_refresh_delay      = 30;    # in minutes
	$g_default_redirect_delay     = 2;     # in seconds
	$g_default_email_on_new       = ON;
	$g_default_email_on_assigned  = ON;
	$g_default_email_on_feedback  = ON;
	$g_default_email_on_resolved  = ON;
	$g_default_email_on_closed    = ON;
	$g_default_email_on_reopened  = ON;
	$g_default_email_on_bugnote   = ON;
	$g_default_email_on_status    = 0; # @@@ Unused
	$g_default_email_on_priority  = 0; # @@@ Unused
	# default_language - is set to site language

	$g_default_language = 'english';
	
	#######################################
	# Mantis Database Table Variables
	#######################################

	# --- table prefix ----------------
	# if you change this remember to reflect the changes in the database
	$g_db_table_prefix = "mantis";

	# --- table names -----------------
	$g_mantis_bug_file_table          = $g_db_table_prefix."_bug_file_table";
	$g_mantis_bug_table               = $g_db_table_prefix."_bug_table";
	$g_mantis_bug_text_table          = $g_db_table_prefix."_bug_text_table";
	$g_mantis_bugnote_table           = $g_db_table_prefix."_bugnote_table";
	$g_mantis_bugnote_text_table      = $g_db_table_prefix."_bugnote_text_table";
	$g_mantis_news_table              = $g_db_table_prefix."_news_table";
	$g_mantis_project_category_table  = $g_db_table_prefix."_project_category_table";
	$g_mantis_project_file_table      = $g_db_table_prefix."_project_file_table";
	$g_mantis_project_table           = $g_db_table_prefix."_project_table";
	$g_mantis_project_user_list_table = $g_db_table_prefix."_project_user_list_table";
	$g_mantis_project_version_table   = $g_db_table_prefix."_project_version_table";
	$g_mantis_user_table              = $g_db_table_prefix."_user_table";
	$g_mantis_user_profile_table      = $g_db_table_prefix."_user_profile_table";
	$g_mantis_user_pref_table         = $g_db_table_prefix."_user_pref_table";
	$g_mantis_bug_monitor_table       = $g_db_table_prefix."_bug_monitor_table";

	# Upload destination: specify actual location in project settings
	# DISK or DATABASE
	$g_file_upload_method   = DATABASE;

	# ---- Customise variable for phpCollab -----
	// specify files upload directory if $g_file_upload_method = DISK, make sure it has correct permission
	$g_file_path = $fullPath."/"; // add slash at end
	$client_user_level = 25; // Reporter
	$team_user_level = 55; // Developer


?>