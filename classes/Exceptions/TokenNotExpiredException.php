<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenNotExpiredException extends Exception
{
    public function __construct($message = 'Token is not expired') {
        parent::__construct($message);
    }
}
