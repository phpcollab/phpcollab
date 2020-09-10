<?php

$handle = fopen(__DIR__ . "/settings.php", "r");
//$handle = file_get_contents(__DIR__ . "/settings.php", "r");
if ($handle) {
    $settings = [];

    while (($line = fgets($handle)) !== false) {
//        if (preg_match('/^\$(?<key>\w*)([= \'"]*)(?<value>\w*)([ "\';]*)/', $line, $matches)) {
        if (preg_match('/^\$(?<key>\w*["\'\w[\]]*)([= \'"]*)(?<value>\w*)([ "\';]*)/', $line, $matches)) {

//            var_dump($matches["key"]);

            if ($matches["key"] == "databaseType") {
                $settings["database"]['type'] = $matches["value"];
            } else if ( strpos($matches["key"], 'tableCollab') === 0 ) { // if the key starts with tableCollab then we need to stuff those into its own array
                $settings["tableCollab"][preg_replace('/^tableCollab\[[\'"](\w*)["\'\]]*/i', '$1', $matches["key"])] = $matches["value"];
            } else if ( strpos($matches["key"], 'configLDAP') === 0 ) { // if the key starts with tableCollab then we need to stuff those into its own array
                $settings["LDAP"][preg_replace('/^configLDAP\[[\'"](\w*)["\'\]]*/i', '$1', $matches["key"])] = $matches["value"];
            } else {
                $settings[$matches["key"]] = $matches["value"];
            }
        }

        // Look for define() statements
        if (preg_match('/^define\(["\'](?<key>\w*)[\'", ]*(?<value>\w*)[\'", ]*\);/', $line, $matches)) {
            // Look for database settings
            if (in_array($matches["key"], ['MYSERVER', 'MYLOGIN', 'MYPASSWORD', 'MYDATABASE'])) {
                $key = preg_replace('/^my/i', '', $matches["key"]);
                $settings["database"][strtolower( $key )] = $matches["value"];
            }
            // SMTP Settings
            if (in_array($matches["key"], ['SMTPSERVER', 'SMTPLOGIN', 'SMTPPASSWORD', 'SMTPPORT'])) {
//                $key = preg_replace('/smtp/i', '', $matches["key"]);
                $settings["smtp"][strtolower( preg_replace('/^smtp/i', '', $matches["key"]) )] = $matches["value"];
            }
            // FTP Settings
            if (in_array($matches["key"], ['FTPSERVER', 'FTPLOGIN', 'FTPPASSWORD'])) {
                $settings["ftp"][strtolower( preg_replace('/^ftp/i', '', $matches["key"]) )] = $matches["value"];
            }

            if ($matches["key"] == "THEME") {
                $settings[strtolower($matches["key"])] = $matches["value"];
            }

        }
//        echo $line;
    }
    fclose($handle);

    xdebug_var_dump($settings);

} else {
    // ERROR
    echo "Error reading file!";
}