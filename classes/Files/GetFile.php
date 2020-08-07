<?php


namespace phpCollab\Files;

use Exception;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;

class GetFile
{
    /**
     * @var
     */
    private $mimeType;
    /**
     * @var
     */
    protected $filesPath;
    /**
     * @var
     */
    protected $fileName;

    /**
     * @param mixed $filesPath
     */
    public function setFilesPath($filesPath): void
    {
        $this->filesPath = $filesPath;
    }

    /**
     *
     */
    private function setMimeType() {
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        if ($mimeTypeGuesser->isSupported()) {
            $this->mimeType = $mimeTypeGuesser->guess($this->filesPath);
        } else {
            $this->mimeType = "text/plain";
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function fileExists()
    {
        if (file_exists($this->filesPath)) {
            return true;
        } else {
            throw new Exception("file does not exist");
        }
    }

    /**
     * @param $fileName
     * @return Exception|void
     * @throws Exception
     */
    public function viewFile($fileName)
    {
        try {
            if ($this->fileExists()) {
                $this->setMimeType();
                header("Content-Length: " . filesize($this->filesPath));
                header("Content-Type: {$this->mimeType}");
                header('Content-Disposition: inline;filename="' . $fileName . '"');
                header("Last-Modified: " . date("D, j M Y G:i:s T", filemtime($this->filesPath)));
                readfile($this->filesPath);
            }
            return;
        } catch (Exception $exception) {
            error_log('Error accessing file: ' . $exception);
            throw new Exception('Error: File does not exist.');
        }
    }

    /**
     * @param $fileName
     * @return Exception|void
     */
    public function downloadFile($fileName)
    {
        try {
            if ($this->fileExists()) {
                $this->setMimeType();
                header("Content-Length: " . filesize($this->filesPath));
                header("Content-Type: {$this->mimeType}");
                header('Content-Disposition: attachment;filename="'.$fileName.'"');
                readfile($this->filesPath);
            }
            return;
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
