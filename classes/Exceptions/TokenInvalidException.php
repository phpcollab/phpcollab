<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenInvalidException extends Exception
{
    public function __construct($message = 'Token is invalid') {
        parent::__construct($message);
    }
}
