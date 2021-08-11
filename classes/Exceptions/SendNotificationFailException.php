<?php


namespace phpCollab\Exceptions;


use Exception;

class SendNotificationFailException extends Exception
{
    public function __construct($message = 'Send Notification Failed') {
        parent::__construct($message);
    }

}
