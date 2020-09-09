<?php


namespace phpCollab\Exceptions;


use Exception;

class SendNotificationFailException extends Exception
{
    public function getMessageKey()
    {
        return 'Unable to send email notification';
    }

}