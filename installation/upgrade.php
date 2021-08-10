<?php
/*
** Application name: phpCollab
** Path by root: ../installation/upgrade.php
** Since: 2.5 rc3
** Authors: Norman77 / Mindblender
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: upgrade.php
**
** DESC: Upgrade script
**
** =============================================================================
** Note: This file is horrible.  It will be re-written in the next major release.
** =============================================================================
*/

error_reporting(2039);
// Include all needed files here.. show the license as well.. we want to be legal
include("../includes/settings.php");
include("../includes/upgrade_funcs.inc.php");

/**
 *
 */
function checkUpgrade()
{
    global $version;

    switch ($version) {
        case '2.5.1':
            redirect("All up to date!.");
            break;
        case '2.5':
            upgrade2_5_1();
            break;
        case '2.4':
            break;
        default:
            echo "There appears to be an error.  Please try and perform a manual upgrade.";
    }
}

/**
 * @param string $msg
 */
function redirect(string $msg = "")
{
    echo "<h3>$msg</h3>";
    echo '<p>Please <a href="../">click here</a> to return to the site.</p>';
}

/**
 * Upgrade 2.5.1
 */
function upgrade2_5_1()
{
    echo "upgrade2_5_1()";
    $settingsFile = dirname(realpath(__FILE__)) . "/../includes/settings.php";
    $upgraded = rewriteConfig($settingsFile);

    if (!$upgraded) {
        echo "there was an error.  Please try again.";
    } else {
        redirect('All done. The settings file has been updated.');
    }
}

checkUpgrade();


/* End of file upgrade.php */
/* Location: ./installation/upgrade.php */
