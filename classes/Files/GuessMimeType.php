<?php


namespace phpCollab\Files;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GuessMimeType
{
//    private $mimeTypes;

    const IMAGES = array('jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif');

    public function __construct()
    {
    }

    /**
     * @param UploadedFile $fileObj
     * @param array $allowedMimeTypes ['extension' => 'mime-type'] ex: ['jpg' => 'image/jpeg']
     * @return false|int|string
     * @throws Exception
     */
    public static function isAllowedMimeType(UploadedFile $fileObj, array $allowedMimeTypes)
    {
        // Let's "guess" the mime type from the provided path
        if (!$allowedMimeTypes) {
            throw new InvalidArgumentException('Invalid parameters');
        }

        $mimeType = $fileObj->getMimeType();

        // Search the array of allowed types and return the "matched" extension if a match is found
        if (false === $ext = array_search(
                $mimeType,
                $allowedMimeTypes,
                true
            )
        ) {
            array(
                'clientOriginalName' => $fileObj->getClientOriginalName(),
                'clientOriginalExtension' => $fileObj->getClientOriginalExtension(),
                'guessedExtension' => $ext
            );
            throw new Exception(sprintf('The mime type of the file is invalid (%s). Data: %s', $mimeType,
                    serialize([
                        'ClientOriginalName' => $fileObj->getClientOriginalName(),
                        'ClientOriginalExtension' => $fileObj->getClientOriginalExtension(),
                        'mimeType' => $mimeType,
                        "allowed" => $allowedMimeTypes
                    ])
                )
            );
        }

        return $ext;
    }
}
