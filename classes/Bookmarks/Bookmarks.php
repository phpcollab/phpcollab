<?php


namespace phpCollab\Bookmarks;

use phpCollab\Database;
use Respect\Validation\Validator as v;

class Bookmarks
{
    protected $bookmarks_gateway;
    protected $db;

    public function __construct()
    {
//        $db = new phpCollab\Database();
        $this->db = new Database();

        $this->bookmarks_gateway = new BookmarksGateway($this->db);
        // phpCollab\Bookmarks\BookmarksGateway($db);


//        $this->bookmarks_gateway = $bookmarks_gateway;
    }

    public function getBookmarksByOwner($ownerId)
    {
        $data = $this->bookmarks_gateway->getAllByOwner($ownerId);
        return $data;
    }

    public function getMyBookmarks($ownerId, $sorting)
    {
        $data = $this->bookmarks_gateway->getMyBookmarks($ownerId, $sorting);
        return $data;
    }

    public function getPrivateBookmarks($ownerId, $sorting)
    {
        $data = $this->bookmarks_gateway->getPrivateBookmarks($ownerId, $sorting);
        return $data;
    }

    public function getBookmarkById($bookmarkId)
    {
        $data = $this->bookmarks_gateway->getBookmarkById($bookmarkId);
        return $data;
    }

    public function getAllBookmarks($ownerId, $sorting) {

        $data = $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);

        return $data;
    }

    public function updateBookmark($bookmarkId, $formData)
    {

    }

    public function deleteBookmark($bookmarkId) {
        try {
            if (!v::intType()->validate( (int) $bookmarkId)) {
                throw new \Exception("Invalid bookmark id");
            }

            $response = $this->bookmarks_gateway->deleteBookmark( $bookmarkId );
            return $response;
        } catch(\Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }
}
