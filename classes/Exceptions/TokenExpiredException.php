<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenExpiredException extends Exception
{
    public function getMessageKey()
    {
        return 'Token has expired';
    }
}
