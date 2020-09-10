<?php


namespace phpCollab\Login\Help;


class Help
{
    private $help;

    public function __construct()
    {
        $this->help = "";
//        xdebug_var_dump(dirname(dirname(__FILE__)));
        include dirname(dirname(__FILE__)) . "../../languages/help_en.php";
    }

    public function getHelp()
    {
        return $this->help;
    }

}