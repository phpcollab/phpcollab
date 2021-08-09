<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenExpiredException extends Exception
{
    public function __construct($message = 'Token has expired') {
        parent::__construct($message);
    }
}
