<?php
// Required Libraries
include dirname(realpath(__FILE__)) . '/../includes/adodb/adodb.inc.php';
global $ourDBType;

$ourDBType = null; // Default

// Start the output and conversion here

startOutput();
$settingsFile = dirname(realpath(__FILE__)) . "/../includes/settings.php";

showInfoBox('We are attempting to open and reads your settings.php file.', 'Reading Settings');
if (!file_exists($settingsFile)) {
    showInfoBox('<span style="color: #ff0000;font-weight: bold;">There was an error trying to read the settings.php file...\nPlease make sure that you have created your settings.php file already...\nAttempting to read file: $settingsFile</span>', 'ERROR');
    die();
}

$contents = file_get_contents($settingsFile);
showInfoBox('Successfully opened and read the settings.php file, we are attempting to parse now....', 'Parsing');

if (!isConverted()) {

    $keyval = parseSettings($contents);

    showInfoBox('Populating database.....', 'Database');
    //populateDatabase($keyval);
    writeToDB($keyval);
    if (createFile()) {
        showInfoBox('All done.. Please replace settings.php with the settings-new.php file that was created.');
    }
} else {
    showInfoBox("You appear to already be converted.  Congratulations.\nIf your settings are not working, please try to run the DB Fix tool.");
}

endOutput();

//-------------------------------------------------------------------------------------------//
/**
 * writeToDb writes the OBJECT to the database
 * @param Array $data
 */
function writeToDB($data)
{
//    echo print_r(array_keys($data), true);
    require_once(dirname(realpath(__FILE__)) . '/../includes/classes/settings.class.php');
    $settings = new Settings();
    $settings->writeObject($data);

}

/**
 * createFile creates the new Config file
 * @return bool Returns true on good file write
 */
function createFile()
{
    global $ourDBType;

    $inc_dir = dirname(realpath(__FILE__)) . '/../includes/';

    $str = '
<?php

/** Settings file created as part of the conversion process **/
define(\'CONVERTED\', TRUE);
define(\'MYSERVER\', \'' . MYSERVER . '\');
define(\'MYLOGIN\', \'' . MYLOGIN . '\');
define(\'MYPASSWORD\', \'' . MYPASSWORD . '\');
define(\'MYDATABASE\', \'' . MYDATABASE . '\');

$databaseType = \'' . $ourDBType . '\';

/** EOF **/

?>';

    //Attemp to write the new file.. 
    $fh = fopen($inc_dir . 'settings-new.php', 'a+');
    if (!$fh) {
        showInfoBox("Could not create file, make sure you have permissions...\nYou can also replace your settings.php file with this:\n<textarea rows=10 cols=80>$str</textarea>", ' :: ERROR ::');

    } else {
        fwrite($fh, $str);
        fclose($fh);
        return true;
    }

    return false;
}

function isConverted()
{
    require_once(dirname(realpath(__FILE__)) . '/../includes/settings.php');

    if (!defined('CONVERTED')) {
        return false;
    }
    return CONVERTED;
}

function populateDatabase($data)
{
    //First let's empty the table, we don't want anything in here..
    //include(dirname(realpath(__FILE__)) . '/../includes/settings.php'); // So we can hit the DB
    //attempt to get the DB Vars
    $databaseType = '';
    $SERVER = $USERNAME = $PASSWORD = $DATABASE = '';
    foreach ($data as $key => $val) {
        if (substr($key, 0, 2) == "MY") {
            $work = substr($key, 2);
            eval("\$$work = '$val';");
        }

        if ($key == "databaseType") {
            $databaseType = $val;
        }
    }

    $db = &ADONewConnection($databaseType);
    $db->Connect($SERVER, $LOGIN, $PASSWORD, $DATABASE);
    $tableName = "new_config"; //This would be dynamic..

    $ret = $db->Execute("delete from $tableName where 1=1");
    if (!$ret) die($db->ErrorMsg());

    foreach ($data as $key => $val) {
        $sql = "insert into $tableName (?, ?, 1);";
        if (!$db->Execute($sql, array($key, val))) {
            die('DB Error: ' . $db->ErrorMsg());
        }
    }

}

function parseSettings($contents)
{
    global $ourDBType;
    $settingsArray = array();

    foreach (explode("\n", $contents) as $line) {
        $skipAssign = false;
        $line = trim($line);
        $doit = true;
        $key = $val = "";

        if ($line == '' || $line[0] == '#' || substr($line, 0, 5) == "<?php" || substr($line, 0, 2) == "?>" ||
            substr($line, 0, 2) == "//"
        ) $doit = false;
        if ($doit) {
            //showInfoBox("DEBUG: Parse this line..<br />" . $line, "Info...");
            //Clean the line, remove any ;'s or comments and then parse it..
            //We work on two style of lines, defines and variable assignments
            if ($line[0] == '$') {
                // Variable
                $work = substr($line, 1);

                // Find hte end of line
                $end = strpos($work, ';');
                $work = substr($work, 0, $end);
                $work = preg_replace('/[\'";()]*/', '', $work); // Clean

                list($key, $val) = explode('=', $work, 2);
                $key = trim($key);
                $val = trim($val);

                //Save DB Type 
                if ($key == "databaseType") $ourDBType = $val;

                // ** Special rules ** \\                
                if (substr($key, 0, 5) == "table" && (strpos($key, "Prefix") === FALSE)) {
                    //Do something special here if it has table in it.. 
                    $work = substr($key, 5);
                    // Now find wher the key is
                    $pos = strpos($work, '[');
                    $epos = strpos($work, ']');

                    $tableName = substr($work, 0, $pos);
                    $keyName = substr($work, ($pos + 1), (($epos - $pos) - 1));
                    if ($tableName != '') $settingsArray[$tableName][$keyName] = $val;
                    $skipAssign = true;
                } elseif (substr($key, 0, 6) == "config") {
                    //Do something special here if it has table in it.. 
                    $work = substr($key, 6);
                    // Now find wher the key is
                    $pos = strpos($work, '[');
                    $epos = strpos($work, ']');

                    $tableName = substr($work, 0, $pos);
                    $keyName = substr($work, ($pos + 1), (($epos - $pos) - 1));
                    if ($tableName != '') $settingsArray[$tableName][$keyName] = $val;
                    $skipAssign = true;
                }

                //showInfoBox("Key: $key - Value: $val", "DEBUG");
            } elseif (substr($line, 0, 6) == "define") {
                //CONSTANTS
                $work = substr($line, 6);
                $work = preg_replace('/[\'";()]*/', '', $work);
                list($key, $val) = explode(',', $work, 2);
                $key = trim($key);
                $val = trim($val);

                //showInfoBox("KEY: $key - VAL: $val");
            }

            if (!$skipAssign) $settingsArray[$key] = $val;
        }
    }

//    showInfoBox('Returning.. ' . print_r($settingsArray, true));
    return $settingsArray;
}

// Functions to aid in displaying  output to the browser
function showInfoBox($message, $title = '.:: DEBUG ::.')
{
    $message = nl2br($message);

    //special for the text area 
    if (strpos($message, "<textarea") !== false) {
        $str = strpos($message, "<textarea");
        $end = strpos($message, "</textarea>");

        $replStr = substr($message, $str, ($end - $str));
        $replStr = preg_replace("/<br\W*?\/>/", "", $replStr);
        $message = substr_replace($message, $replStr, $str, ($end - $str));
    }
    echo '
        <fieldset><legend>' . $title . '</legend>' . $message . '</fieldset>
';
}

function startOutput()
{
    echo '
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
    <head>
        <title>Import Settings to DB</title>
        
        <style type="text/css">
            * { margin: 0; padding: 0 }
            #contentWrapper { margin: 15px; }
            #contentWrapper fieldset { padding: 10px; background: #f5f5f5;border: 1px solid #999;}
            #contentWrapper fieldset legend { padding: 7px;  color: #821400; font-weight: bold;}
        </style>
    </head>
  
    <body>
        <div id="contentWrapper"><h2>Attempting to import settings....</h2>
        We are starting to import settings from your local file to the databse...<br /><br />
  
';

}

function endOutput()
{
    echo '
        </div>
    </body>
</html>
';

}

// END //
?>
