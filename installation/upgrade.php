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

use phpCollab\Block;

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
function redirect($msg = "")
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
    $upgraded = rewriteConfig($settingsFile, '2.5.1');

    if (!$upgraded) {
        echo "there was an error.  Please try again.";
    } else {
        redirect('All done. The settings file has been updated.');
    }
}

/**
 * Upgrade 2.4
 */
function upgrade2_4()
{
    include "../languages/help_en.php";
    $setTitle = "PhpCollab Upgrade";
    $blank = "true";
    include '../views/layout/header.php';

    //Get this file..
    $script = "upgrade.php";
    $step = getParameter('step');

    if (empty($step)) {
        $step = 1;
    }

    // Create new block and start the breadcrumbs
    $blockPage = new Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs("<a href='../installation/$script'>Upgrade</a>");

    //Content block
    $block1 = new Block();

    if ($step == 1) {
        $blockPage->itemBreadcrumbs("License");
        $blockPage->closeBreadcrumbs();

        $block1->heading("License");
        $block1->openContent();
        $block1->contentTitle("&nbsp;");

        echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>
    	<pre>";
        include "../docs/copying.txt";
        echo "</pre>
    	</td></tr>";
    } elseif ($step == 2) {
        $myError = null;

        $blockPage->itemBreadcrumbs("<a href='../installation/{$script}?step=1'>License</a>");
        $blockPage->itemBreadcrumbs("Check DB");
        $blockPage->closeBreadcrumbs();

        $block1->heading("Checking Database...");
        $block1->openContent();
        $block1->contentTitle("Database Requirements...");

        echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>
        We are currently checking the database to see if it needs to be updated... <br /><br /><b>Please wait...</b><br />
    	</td></tr>";

        flush();

        echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>";

        if (checkDatabase($myError)) {
            echo "<br />Database looks <font style='color: green'>good</font>.  We are continuing the conversion...<br /><br />";
            echo "<br />Continue on to <a href='../installation/{$script}?step=3&redirect=true'>Step 3</a>.<br /><br /><b>MAKE SURE SETTINGS.PHP IS WRITEABLE!!!</b><Br /><br />";
        } else {
            echo "<fieldset><legend style='font-weight: bold;font-size: large;padding: 5px;color: #ff3300'>";
            echo "Error...</legend>";
            echo "Sorry we could not upgrade your database at this time.. you will have to reinstall or do a manual upgrade...";
            if (!empty($myError)) {
                echo "<pre>$myError</pre>";
            }
            echo "</fieldset>";
        }

        echo "</td></tr>";
    } elseif ($step == 3) {
        $myError = null;

        $blockPage->itemBreadcrumbs("<a href='../installation/{$script}?step=1'>License</a>");
        $blockPage->itemBreadcrumbs("<a href='../installation/{$script}?step=2'>Check DB</a>");
        $blockPage->itemBreadcrumbs("Conversion");
        $blockPage->closeBreadcrumbs();

        $block1->heading("Conversion and update");
        $block1->openContent();
        $block1->contentTitle("Checking Settings.php...");
        echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>
        We are making sure that settings.php is writeable, we can not continue if it's not writeable. <br /><br /><b>Please wait...</b><br />
    	</td></tr>";
        flush();

        echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>";

        //Check file
        $settingsFile = dirname(realpath(__FILE__)) . "/../includes/settings.php";
        unset($goon);
        $goon = false;

        clearstatcache();
        if (!is_writable($settingsFile)) {
            echo "<fieldset><legend style='font-weight: bold;font-size: large;padding: 5px;color: #ff3300'>";
            echo "Error...</legend>";
            echo "Your settings file is not writeable.   You need to either <b>chmod 666</b> the file or <b>chmod o+w</b> the file.  Hit refresh to recheck.";
            if (!empty($myError)) {
                echo "<pre>$myError</pre>";
            }
            echo "</fieldset>";
        } else {
            echo "<br />Okay your settings.php file looks good, we are going to continue.<br />";
            $goon = true;
        }

        echo "<br /></td></tr>";

        if ($goon) {
            //Next
            $block1->contentTitle("Converting...");
            echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>
            We are now upgrading your database and writting the config file. <br /><br /><b>Please wait...</b><br />
        	</td></tr>";
            flush();

            echo "<tr class='odd'><td class='leftvalue'>&nbsp;</td><td>";

            if (convertDB()) {
                echo "<br />Writting out new settings file...";
                rewriteConfig($settingsFile, '2.5');
                //Reload the Config so we can use the root
                include $settingsFile;

                echo "<b> done</b><br /><br />";
                echo "<br /><B>Congratulations... if there was no error writting the file, you are done.<br />Click <a href=\"../\">here</a> to login.";
                echo "<br><B>MAKE SURE TO DELETE  UPGRADE.PHP!";
                echo "</td></tr>";

                echo "<h3>If you are upgrading from 2.4 to the latest version, please re-run this upgrade script to perform additi</h3>";
            }
        }
    }

    $block1->closeContent();

    $stepNext = $step + 1;
    if ($step < "2") {
        echo "<form name='license' action='../installation/{$script}?step=2&redirect=true' method='post'><center><a href=\"javascript:document.license.submit();\"><br /><b>Step $stepNext</b></a><br /><br /></center></form><br/>";
    }

    $footerDev = "false";
    include '../views/layout/footer.php';

    //FOR DEBUG ****
    exit();
    // -----------------------
}


checkUpgrade();


/* End of file upgrade.php */
/* Location: ./installation/upgrade.php */
