<?php


namespace phpCollab\Exceptions;


use Exception;

class MissingTemplateException extends Exception
{
    public function __construct($message = 'Template not found') {
        parent::__construct($message);
    }

}
