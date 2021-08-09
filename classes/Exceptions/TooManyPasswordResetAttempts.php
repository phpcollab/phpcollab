<?php


namespace phpCollab\Exceptions;


use Exception;

class TooManyPasswordResetAttempts extends Exception
{
    public function __construct($message = 'Too many password reset attempts') {
        parent::__construct($message);
    }
}
