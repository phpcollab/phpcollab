<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenGenerationFailedException extends Exception
{
    public function getMessageKey()
    {
        return 'Unable to generate password reset token';
    }
}
