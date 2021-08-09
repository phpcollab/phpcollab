<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenAlreadySentException extends Exception
{
    public function __construct($message = 'Token already sent and has not yet expired') {
        parent::__construct($message);
    }
}
