<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/customvalues.php

/**
 * Check to see if the languageSession is set, if it is then
 * check to see if there is a custom_xx file for it.  If not then default
 * to English
 */
if ( empty($session->get("languageSession")) ) {
    $session->set("languageSession", "en");
}

/**
 * Check to see if the languageSession file exists, if not then use English as fallback
 */
$customLanguageFile = "../languages/custom_" . $session->get("languageSession") . ".php";

if (file_exists($customLanguageFile)) {
    include $customLanguageFile;
} else {
    include "../languages/custom_en.php";
}
