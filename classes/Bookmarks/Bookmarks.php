<?php


namespace phpCollab\Bookmarks;

use phpCollab\Database;

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

    public function getAllBookmarks($ownerId, $sorting) {

        $data = $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);

        return $data;
    }

    public function updateBookmark($bookmarkId, $formData)
    {

    }
}
