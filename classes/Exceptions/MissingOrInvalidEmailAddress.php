<?php


namespace phpCollab\Exceptions;


use Exception;

class MissingOrInvalidEmailAddress extends Exception
{
    public function __construct($message = 'Email is missing or invalid') {
        parent::__construct($message);
    }
}
