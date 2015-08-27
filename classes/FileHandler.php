<?php
/**
 * Class FileHandler
 **/
class FileHandler
{
    protected $type;

    /**
     * Constructor method
     * @param null $type nada
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * Gets the document type, based on the extension, and returns the appropriate
     * icon image.
     * @param String $extension the string to determing the return graphic
     * @return null|string
     */
    public function fileInfoType($extension)
    {
        switch ($extension) {
        case "doc":
            $this->type = "doc.gif";
            break;
        case "mdb":
            $this->type = "mdb.gif";
            break;
        case "ppt":
            $this->type = "ppt.gif";
            break;
        case "xls":
            $this->type = "xls.gif";
            break;
        case "pdf":
            $this->type = "pdf.gif";
            break;
        case "ai":
            $this->type = "ai.gif";
            break;
        case "eps":
            $this->type = "ai.gif";
            break;
        case "ttf":
            $this->type = "ttf.gif";
            break;
        case "gif":
            $this->type = "gif.gif";
            break;
        case "jpg":
            $this->type = "jpg.gif";
            break;
        case "png":
            $this->type = "png.gif";
            break;
        case "psd":
            $this->type = "psd.gif";
            break;
        case "txt":
            $this->type = "txt.gif";
            break;
        case "js":
            $this->type = "js.gif";
            break;
        case "htm":
            $this->type = "htm.gif";
            break;
        case "html":
            $this->type = "htm.gif";
            break;
        case "php":
            $this->type = "php.gif";
            break;
        case "php3":
            $this->type = "php.gif";
            break;
        case "zip":
            $this->type = "zip.gif";
            break;
        case "rar":
            $this->type = "rar.gif";
            break;
        case "swf":
            $this->type = "swf.gif";
            break;
        case "rm":
            $this->type = "rm.gif";
            break;
        case "sxd":
            $this->type = "sxd.gif";
            break;
        case "std":
            $this->type = "std.gif";
            break;
        case "sxw":
            $this->type = "sxw.gif";
            break;
        case "stw":
            $this->type = "stw.gif";
            break;
        case "sxi":
            $this->type = "sxi.gif";
            break;
        case "sti":
            $this->type = "sti.gif";
            break;
        case "sxc":
            $this->type = "sxc.gif";
            break;
        case "stc":
            $this->type = "stc.gif";
            break;
        case "sxg":
            $this->type = "sxg.gif";
            break;
        case "sxm":
            $this->type = "sxm.gif";
            break;
        default:
            $this->type = "fic.gif";
        }

        return $this->type;
    }
}
