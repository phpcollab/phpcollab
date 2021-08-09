<?php


namespace phpCollab\Exceptions;


use Exception;

class TokenGenerationFailedException extends Exception
{
    public function __construct($message = 'Unable to generate password reset token') {
        parent::__construct($message);
    }
}
