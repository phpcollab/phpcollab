<?php
/*
** Application name: phpCollab
**
** =============================================================================
**
**               phpCollab - Project Management
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** DESC: Screen: setup file
**
** =============================================================================
*/

use phpCollab\Installation\Installation;

error_reporting(2039);

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$help = [];
require_once '../languages/help_en.php';

$appRoot = dirname(dirname(__FILE__));

define('APP_ROOT', dirname(dirname(__FILE__)));

$step = $_GET["step"];
$redirect = $_GET["redirect"];
$connection = (!empty($_GET["connection"])) ? $_GET["connection"] : $_POST["connection"];

if ($redirect == "true" && $step == "2") {
    header("Location:../installation/setup.php?step=2&connection={$connection}");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST["action"] == "generate") {
        if (empty($_POST["dbServer"])) {
            $error = $help["setup_error_database_server"];
        } elseif (empty($_POST["dbLogin"])) {
            $error = $help["setup_error_database_login"];
        } elseif (empty($_POST["dbName"])) {
            $error = $help["setup_error_database_name"];
        } elseif (empty($_POST["siteUrl"])) {
            $error = $help["setup_error_site_url"];
        } elseif (empty($_POST["adminPassword"])) {
            $error = $help["setup_error_admin_password"];
        }

        if (!$error) {
            try {
                $installation = new Installation([
                    'dbServer' => $_POST["dbServer"],
                    'dbUsername' => $_POST["dbLogin"],
                    'dbPassword' => $_POST["dbPassword"],
                    'dbName' => $_POST["dbName"],
                    'dbType' => $_POST["databaseType"],
                    'tablePrefix' => $_POST["dbTablePrefix"]
                ], $appRoot);

                /**
                 * Further massage the data
                 */
                if ($_POST["installationType"] == "offline" && empty($_POST["updateChecker"])) {
                    $_POST["updateChecker"] = false;
                }

                // Trim off the trailing "/"
                if (substr($_POST["siteUrl"], -1) == "/") {
                    $_POST["siteUrl"] = substr($_POST["siteUrl"], 0, -1);
                }

                if ($_POST["mkdirMethod"] === "FTP") {
                    if (substr($_POST["ftpRoot"], -1) == "/") {
                        $_POST["ftpRoot"] = substr($_POST["ftpRoot"], 0, -1);
                    }
                }

                // Perform the setup process
                $installation->setup($_POST);

                $msg = sprintf($help["setup_success"], '../general/login.php');
            } catch (PDOException $e) {
                $error = $help["setup_error_database"];
            } catch (Exception $e) {
                $error = $help["setup_general_error"];
            }
        } else {
            $error = $help["setup_general_error"];
        }

        // If there was an error, then let's go back to Step 2
        if ($error) {
            $step = 2;
        }
    }
}
if ($step == "") {
    $step = "1";
}

$setTitle = "PhpCollab : Installation";
define('THEME', 'default');
$blank = "true";

require dirname(dirname(__FILE__)) . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs("<a href='../installation/setup.php'>Setup</a>");

if ($step == "1") {
    $blockPage->itemBreadcrumbs("License");
} elseif ($step > "1") {
    $blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=1'>License</a>");

    if ($step == "2") {
        $blockPage->itemBreadcrumbs("Settings");
    } elseif ($step > "2") {
        $blockPage->itemBreadcrumbs("<a href='../installation/setup.php?step=2'>Settings</a>");
        if ($step == "3") {
            $blockPage->itemBreadcrumbs("Success");
        }
    }
}

$blockPage->closeBreadcrumbs();

$block1 = new phpCollab\Block();

if ($step == "1") {
    $block1->heading("License");
}
if ($step == "2") {
    $block1->heading("Settings");
}
if ($step == "3") {
    $block1->heading("Success");
}

if ($step == "1") {
    $block1->openContent();
    $block1->contentTitle("&nbsp;");

    echo <<<HTML
<tr class="odd"><td colspan="2">
    <pre style="margin: 1rem auto; height: 500px; width: 525px; overflow-y: scroll; border: 1px inset; padding: 1em;">
HTML;
    include '../docs/copying.txt';
    echo <<<HTML
</pre>
		</td></tr>
HTML;
    $block1->closeContent();
}

if ($step == "2") {
    $block1->form = "settings";
    $block1->openForm("../installation/setup.php?step=3", null, $csrfHandler);

    $block1->openContent();
    $block1->contentTitle("Details");

    if (isset($error) && !empty($error)) {
        echo <<<HTML
        <tr class="odd">
            <td class="error" colspan="2">
                <div class="alert error" style="margin: 20px 0 20px 50px; width: 30vw;">
                {$error}
                </div>
            </td>
        </tr>
HTML;
    }

    if ($connection == "off" || $_POST["installationType"] == "offline") {
        echo "<input value='false' name='updateChecker' type='hidden'>";
    } elseif (@join('', file("http://www.phpcollab.com/website/version.txt"))) {
        echo "<input value='true' name='updateChecker' type='hidden'>";
    } else {
        echo "<input value='false' name='updateChecker' type='hidden'>";
    }

    echo '<input type="hidden" name="action" value="generate">';

    if ($connection == "off" || $_POST["installationType"] == "offline") {
        $installCheckOffline = "checked";
    } else {
        $installCheckOnline = "checked";
    }

    if ($databaseType == "mysql" || $databaseType == "" || $_POST["databaseType"] == "mysql") {
        $dbCheckMysql = "checked";
    } elseif ($databaseType == "sqlserver" || $_POST["databaseType"] == "sqlserver") {
        $dbCheckSqlserver = "checked";
    } elseif ($databaseType == "postgresql" || $_POST["databaseType"] == "postgresql") {
        $dbCheckPostgresql = "checked";
    }

    $myPrefix = addslashes($help["setup_myprefix"]);

    echo <<<HTML
 	<tr class="odd">
				<td class="leftvalue">* Installation type :</td>
				<td><input type="radio" name="installationType" value="offline" {$installCheckOffline}> Offline (firewall/intranet, no update checker)&nbsp<input type="radio" name="installationType" value="online" {$installCheckOnline}> Online</td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">* Database type :</td>
				<td>
				    <input type="radio" name="databaseType" value="mysql" {$dbCheckMysql}> MySql&nbsp
				    <input type="radio" name="databaseType" value="sqlserver" {$dbCheckSqlserver}> Microsoft Sql Server&nbsp
				    <input type="radio" name="databaseType" value="postgresql" {$dbCheckPostgresql}> PostgreSQL
                </td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">* Database server :</td>
				<td><input size="44" value="{$_POST["dbServer"]}" style="width: 200px" name="dbServer" maxlength="100" type="text" required></td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">* Database login :</td>
				<td><input size="44" value="{$_POST["dbLogin"]}" style="width: 200px" name="dbLogin" maxlength="100" type="text" required></td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">Database password :</td>
				<td><input size="44" value="" style="width: 200px" name="dbPassword" maxlength="100" type="password" autocomplete="off" required></td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">* Database name :</td>
				<td><input size="44" value="{$_POST["dbName"]}" style="width: 200px" name="dbName" maxlength="100" type="text" required></td>
			</tr>
			<tr class="odd">
				<td class="leftvalue">Table prefix :<br/>[<a href="javascript:void(0)" onmouseover="return overlib('{$myPrefix}',ABOVE,SNAPX,550)" onmouseout="return nd()">Help</a>] </td>
				<td><input size="44" value="{$_POST["dbTablePrefix"]}" style="width: 200px" name="dbTablePrefix" maxlength="100" type="text"></td>
			</tr>
HTML;

    $safemodeTest = ini_get("safe_mode");

    if ($safemodeTest == "1") {
        $checked1_a = "checked"; //false
        $safemode = "on";
    } else {
        $checked2_a = "checked"; //true
        $safemode = "off";
    }

    if (function_exists('mail') == "true" || $_POST["notifications"] == "true") {
        $notificationsOn = "checked"; //false
        $mailEnabled = "on";
    } else {
        $notificationsOff = "checked"; //true
        $mailEnabled = "off";
    }

    $setupNotifications = addslashes($help["setup_notifications"]);
    $setupForcedLogin = addslashes($help["setup_forcedlogin"]);
    $setupLangDefault = addslashes($help["setup_langdefault"]);
    echo <<<HTML
    <tr class="odd">
        <td class="leftvalue">* Notifications :<br/>[<a href="javascript:void(0);" onmouseover="return overlib('{$setupNotifications}',SNAPX,550);" onmouseout="return nd();">Help</a>] </td>
        <td><input type="radio" name="notifications" value="false" {$notificationsOff}> False&nbsp;<input type="radio" name="notifications" value="true" {$notificationsOn}> True<br/>[Mail {$mailEnabled}]</td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">* Forced login :<br/>[<a href="javascript:void(0);" onmouseover="return overlib('{$setupForcedLogin}',SNAPX,550);" onmouseout="return nd();">Help</a>] </td>
        <td><input type="radio" name="forcedLogin" value="false" checked> False&nbsp;<input type="radio" name="forcedLogin" value="true"> True</td>
    </tr>
    <tr class="odd">
        <td class="leftvalue">Default language :<br/>[<a href="javascript:void(0);" onmouseover="return overlib('{$setupLangDefault}',SNAPX,550);" onmouseout="return nd();">Help</a>] </td>
        <td>
            <select name="defaultLanguage">
                <option value="ar">Arabic</option>
                <option value="az">Azerbaijani</option>
                <option value="pt-br">Brazilian Portuguese</option>
                <option value="bg">Bulgarian</option>
                <option value="ca">Catalan</option>
                <option value="zh">Chinese simplified</option>
                <option value="zh-tw">Chinese traditional</option>
                <option value="cs-iso">Czech (iso)</option>
                <option value="cs-win1250">Czech (win1250)</option>
                <option value="da">Danish</option>
                <option value="nl">Dutch</option>
                <option value="en" selected>English</option>
                <option value="et">Estonian</option>
                <option value="fr">French</option>
                <option value="de">German</option>
                <option value="hu">Hungarian</option>
                <option value="is">Icelandic</option>
                <option value="in">Indonesian</option>
                <option value="it">Italian</option>
                <option value="ko">Korean</option>
                <option value="lv">Latvian</option>
                <option value="no">Norwegian</option>
                <option value="pl">Polish</option>
                <option value="pt">Portuguese</option>
                <option value="ro">Romanian</option>
                <option value="ru">Russian</option>
                <option value="sk-win1250">Slovak (win1250)</option>
                <option value="es">Spanish</option>
                <option value="tr">Turkish</option>
                <option value="uk">Ukrainian</option>
            </select>
        </td>
    </tr>
HTML;

    $url = $_SERVER["SERVER_NAME"];
    if ($_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != 443) {
        $url .= ":" . $_SERVER["SERVER_PORT"];
    }
    if ($_SERVER["HTTPS"] == "on") {
        $protocol = "https://";
    } else {
        $protocol = "http://";
    }

    $siteUrl = $protocol . $url . dirname($_SERVER["PHP_SELF"]);
    $siteUrl = str_replace("installation", "", $siteUrl);

    $tooltipLoginMethod = addslashes($help["setup_loginmethod"]);
    echo <<<HTML
		<tr class="odd">
			<td class="leftvalue"> * Root :</td>
			<td><input size="44" value="{$siteUrl}" style="width: 200px" name="siteUrl" maxlength="100" type="text" required></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">* Admin password :</td>
			<td><input size="44" value="" style="width: 200px" name="adminPassword" maxlength="100" type="password" required></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">* Admin email :</td>
			<td><input size="44" value="{$_POST["adminEmail"]}" style="width: 200px" name="adminEmail" value="{$adminEmail}" type="email" required></td>
		</tr>
		<tr class="odd">
			<td class="leftvalue">&nbsp;</td>
			<td><input type="submit" value="Save"></td>
		</tr>
HTML;
    $block1->closeContent();
    $block1->closeForm();
}

if ($step == "3") {
    $block1->openContent();
    $block1->contentTitle("Success");

    if (isset($error) && !empty($error)) {
        echo <<<HTML
        <tr class="odd">
            <td class="error" colspan="2">
                <div class="alert error">
                {$error}
                </div>
                <p><button onclick="history.back();">< Back</button></p>
            </td>
        </tr>
HTML;
    }

    if (!$error && $msg) {
        echo <<<HTML
            <tr class="odd">
                <td colspan="2">
                    <div class="alert success" style="width: 25vw;">
                        {$msg}
                    </div>
                </td>
            </tr>
HTML;

    }
    $block1->closeContent();
}

$stepNext = $step + 1;
if ($step < "2") {
    echo <<<FORM
    <form id="license" name="license" action="../installation/setup.php?step=2&redirect=true" method="post" style="text-align: center;">
        <p><input type="submit" value="Step {$stepNext}" style="color: #000; font-weight: bold; background-color: transparent; border: none; text-decoration: underline; cursor: pointer" /></p>
        <label><input type="checkbox" value="off" name="connection"> Offline installation (firewall/intranet, no update checker)</label>
    </form>
FORM;
}

$footerDev = "false";
$siteTitle = "phpCollab";
$copyrightYear = date("Y");

require dirname(dirname(__FILE__)) . '/views/layout/footer.php';
