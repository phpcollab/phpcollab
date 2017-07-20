<?php


namespace phpCollab\setup;


class Setup
{
    /**
     * Setup constructor.
     * @param $viewLoader
     */
    public function __construct($viewLoader){

    }

    /**
     *
     */
    public function checkForSettingsFile(){
        /* Do Setup Check */
        /**
         * Refactor to check for loaded configuration.
         */
        if (!file_exists("includes/settings.php")) {
            header('Location: installation/setup.php');
        }
    }
}