<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_en.php

//translator(s):
$help["setup_mkdirMethod"] = "If safe-mode is On, you need to set a Ftp account to be able to create folder with file management.";
$help["setup_notifications"] = "Users e-mail notifications (task assignment, new post, task changes...)<br/>Valid smtp/sendmail needed.";
$help["setup_forcedlogin"] = "If false, disallow external link with login/password in url";
$help["setup_langdefault"] = "Choose language to be selected on login by default or leave blank to use auto_detect browser language.";
$help["setup_myprefix"] = "Set this value if you have tables with same name in existing database.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Leave blank for not use table prefix.";
$help["setup_loginmethod"] = "Method to store passwords in database.<br/>Set to &quot;Crypt&quot; in order htaccess authentificationto work (if htaccess authentificationare enabled).";
$help["admin_update"] = "Respect strictly the order indicated to update your version<br/>1. Edit settings (supplement the new parameters)<br/>2. Edit database (update in agreement with your preceding version)";
$help["task_scope_creep"] = "Difference in days between due date and complete date (bold if positive)";
$help["max_file_size"] = "Maximum file size of a file to upload";
$help["project_disk_space"] = "Total size of files for the project";
$help["project_scope_creep"] = "Difference in days between due date and complete date (bold if positive). Total for all tasks";
$help["mycompany_logo"] = "Upload any logo of your company. Appears in header, instead of title site";
$help["calendar_shortname"] = "Label to appear in monthly calendar view. Mandatory";
$help["user_autologout"] = "Time in sec. to be disconnected after no activity. 0 to disable";
$help["user_timezone"] = "Set your GMT timezone";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
//2.5 fullo
$help["newsdesk_links"] = "to add multiple links use semicolon";
//2.6.x
$help["setup_error_database_server"] = "You must enter the database Server";
$help["setup_error_database_login"] = "Must be insert the database Login";
$help["setup_error_database_name"] = "Must be insert the database Name";
$help["setup_error_site_url"] = "Must be insert the Root path";
$help["setup_error_admin_password"] = "Must be insert the Admin password";
$help["setup_error_database"] = "Error connecting to database.  Please check that your information is correct and try again.";
$help["setup_general_error"] = "We had a problem completing the request. Please check and try again.";
$help["setup_success"] = '<p><strong>phpCollab has successfully been installed.</strong></p><p><a href="%s">Please log in</a></p>';
$help["logLevels"] = '<div><strong>DEBUG (100)</strong>: Detailed debug information.</div><div><strong>INFO (200)</strong>: Interesting events.<br /><em>Examples: User logs in, SQL logs.</em></div><div><strong>NOTICE (250)</strong>: Normal but significant events.</div><div><strong>WARNING (300)</strong>: Exceptional occurrences that are not errors.<br /><em>Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.</em></div><div><strong>ERROR (400)</strong>: Runtime errors that do not require immediate action but should typically be logged and monitored.</div><div><strong>CRITICAL (500)</strong>: Critical conditions.<br /><em>Example: Application component unavailable, unexpected exception.</em></div><div><strong>ALERT (550)</strong>: Action must be taken immediately.<br /><em>Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.</em></div><div><strong>EMERGENCY (600)</strong>: Emergency: system is unusable.</div>';