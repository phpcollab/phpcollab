<?php


namespace phpCollab\Exceptions;


use Exception;

class TimestampInvalidException extends Exception
{
    public function __construct($message = 'Timestamp is invalid') {
        parent::__construct($message);
    }
}
