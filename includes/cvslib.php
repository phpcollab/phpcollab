<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/cvslib.php

//TY
$cvs_log_level = 5;

#=============== USER AUTHORIZATION =========================
function has_access($cvs_user, $cvs_project)
{
    $all_cvs_users = cvs_read_passwd($cvs_project);

    $cvs_fields = $all_cvs_users[$cvs_user];

    if (is_null($cvs_fields)) {
        return false;
    } else {
        return true;
    }
}

#=============== ADD REPOSITORY =========================
function cvs_delete_repository($cvs_project)
{
    global $cvs_root;

    //We really don't want to delete the repository,
    //just erase the passwd file...

    $cvs_passwd_file = $cvs_root . "/" . $cvs_project . "/CVSROOT/passwd";
    unlink($cvs_passwd_file);

    $cvs_deleted_file = $cvs_root . "/" . $cvs_project . "/DELETED";
    touch($cvs_deleted_file);
}

#=============== ADD REPOSITORY =========================
function cvs_add_repository($cvs_user, $cvs_pass, $cvs_project)
{
    global $cvs_root, $cvs_cmd;

    $cvs_dir = $cvs_root . "/" . $cvs_project;

    exec($cvs_cmd . " -d " . $cvs_dir . " init");

    cvs_add_user($cvs_user, $cvs_pass, $cvs_project);
}

#=============== ADD USER =========================
function cvs_add_user($cvs_user, $cvs_pass, $cvs_project)
{
    global $cvs_owner;

    $all_cvs_users = cvs_read_passwd($cvs_project);

    $cvs_fields = $all_cvs_users[$cvs_user];

    if (is_null($cvs_fields)) {
        $cvs_fields[0] = $cvs_pass;
        $cvs_fields[1] = $cvs_owner;
        $all_cvs_users[$cvs_user] = $cvs_fields;
        cvs_write_file($all_cvs_users, $cvs_project);
        cvs_log(1, "Added user $cvs_user");
    } else {
        cvs_log(3, "User $cvs_user already exists");
    }
}


#=============== DELETE USER =========================
function cvs_delete_user($cvs_user, $cvs_project)
{
    $all_cvs_users = cvs_read_passwd($cvs_project);
    $cvs_fields = $all_cvs_users[$cvs_user];
    if (!is_null($cvs_fields)) {
        unset($all_cvs_users[$cvs_user]);
        cvs_write_file($all_cvs_users, $cvs_project);
        cvs_log(1, "Deleted user $cvs_user");
    } else {
        cvs_log(3, "User $cvs_user does not exist");
    }
}


#=============== CHANGE PASSWORD =========================
function cvs_change_password($cvs_user, $new_pass, $cvs_project)
{
    $all_cvs_users = cvs_read_passwd($cvs_project);
    $cvs_fields = $all_cvs_users[$cvs_user];
    if (!is_null($cvs_fields)) {
        $cvs_fields[0] = $new_pass;
        $all_cvs_users[$cvs_user] = $cvs_fields;
        cvs_write_file($all_cvs_users, $cvs_project);
        cvs_log(1, "Updated password for $cvs_user");
    } else {
        cvs_log(3, "No such user- $cvs_user");
    }
}

#=============== READ PASSWD FILE =========================
function cvs_read_passwd($cvs_project)
{
    global $cvs_root;
    $cvs_passwd_file = $cvs_root . "/" . $cvs_project . "/CVSROOT/passwd";

    settype($all_cvs_users, "array");
    if (is_file($cvs_passwd_file)) {
        $fcontents = file($cvs_passwd_file, FILE_TEXT | FILE_SKIP_EMPTY_LINES);
        while (list ($line_num, $line) = each($fcontents)) {
            $line = trim($line);
            if (substr($line, 0, 1) != "#" && strlen($line) > 0) {
                list($cvs_u, $cvs_p, $sys_u) = explode(":", $line, 3);
                $all_cvs_users[$cvs_u] = array($cvs_p, $sys_u);
            }
        }
        cvs_log(1, "Processed $cvs_passwd_file");
    } else {
        cvs_log(3, "No such file- File $cvs_passwd_file!");
    }
    return $all_cvs_users;
}

#=============== WRITE PASSWD FILE =========================
function cvs_write_file($user_array, $cvs_project)
{
    global $cvs_root;
    $cvs_passwd_file = $cvs_root . "/" . $cvs_project . "/CVSROOT/passwd";

    $file_str = "# Last update on " . date("D, M d Y H:i:s T") . "\n\n";

    foreach ($user_array as $name => $pass) {
        $file_str .= "$name:$pass[0]:$pass[1]\n";
    }

    $fp = fopen($cvs_passwd_file, "w");
    fwrite($fp, $file_str);
    fclose($fp);
}


#=============== LOGGING FUNCTION =========================
function cvs_log($level, $message)
{
    global $cvs_log_level;
    $cvs_log_string = array("DEBUG", "INFO", "WARN", "ERROR", "FATAL");
    if ($level >= $cvs_log_level) {
        echo "$cvs_log_string[$level]:  $message\n";
    }
}

?>