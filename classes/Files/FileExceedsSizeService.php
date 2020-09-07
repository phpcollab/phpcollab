<?php


namespace phpCollab\Files;


use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileExceedsSizeService
{
    /**
     * @param UploadedFile $fileObj
     * @throws Exception
     */
    public static function exceedsSize(UploadedFile $fileObj)
    {
        if ($fileObj->getSize() > 10000000) {
            throw new Exception('Exceeded filesize limit.');
        }
    }
}
