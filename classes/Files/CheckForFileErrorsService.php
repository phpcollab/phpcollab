<?php


namespace phpCollab\Files;


use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CheckForFileErrorsService
{
    /**
     * @param $fileObj
     * @return false
     * @throws Exception Throws an exception if there is an error
     */
    public static function hasErrors(UploadedFile $fileObj)
    {
        if (!
            $fileObj->getError() !== 0 ||
            !is_null($fileObj->getErrorMessage())
        ) {
            throw new InvalidArgumentException('Invalid parameters.');
        }

        switch ($fileObj->getError()) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('Exceeded filesize limit.');
            default:
                throw new Exception('Unknown errors.');
        }

        return false;
    }
}