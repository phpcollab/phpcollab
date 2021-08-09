<?php


namespace phpCollab\Exceptions;


use Exception;

class TimestampExpiredException extends Exception
{
    public function __construct($message = 'Timestamp has expired') {
        parent::__construct($message);
    }
}
