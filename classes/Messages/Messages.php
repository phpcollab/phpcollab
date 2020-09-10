<?php


namespace phpCollab\Login\Messages;


class Messages
{
    protected $messageLabel;
    protected $strings;

    public function __construct()
    {
        $this->strings = $GLOBALS["strings"];
    }

    public function getMessage($identifier)
    {
        switch ($identifier) {
            case "add":
                return $this->addCommentLink();
                break;

        }


    }

    private function addCommentLink()
    {
        return "addComment link goes here";
    }
}