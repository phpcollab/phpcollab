<?php


namespace phpCollab\Exceptions;


use Exception;

class UserNotFoundException extends Exception
{
    public function __construct($message = 'User not found') {
        parent::__construct($message);
    }
}
