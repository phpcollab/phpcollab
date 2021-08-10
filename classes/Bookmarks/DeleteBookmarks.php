<?php


namespace phpCollab\Bookmarks;


use Exception;
use InvalidArgumentException;
use Laminas\Escaper\Escaper;
use phpCollab\Database;

class DeleteBookmarks extends Bookmarks
{
    public function __construct(Database $database, Escaper $escaper)
    {
        parent::__construct($database, $escaper);
    }

    /**
     * @param $bookmarkIds
     * @return bool|Exception
     * @throws Exception
     */
    public function delete($bookmarkIds)
    {
        if ($bookmarkIds) {
            try {
                $bookmarkIds = filter_var((string)$bookmarkIds, FILTER_SANITIZE_STRING);

                return $this->bookmarks_gateway->deleteBookmark($bookmarkIds);
            } catch (Exception $exception) {
                error_log('DeleteBookmarks: ' . $exception->getMessage());
                return $exception;
            }
        } else {
            if (empty($bookmarkIds)) {
                throw new InvalidArgumentException('Bookmark(s) id is missing or empty.');
            } else {
                throw new Exception('Error sending file uploaded notification');
            }

        }
    }
}
