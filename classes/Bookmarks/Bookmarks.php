<?php


namespace phpCollab\Bookmarks;

use phpCollab\Database;


class Bookmarks
{
    protected $bookmarks_gateway;
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
        $this->bookmarks_gateway = new BookmarksGateway($this->db);
    }

    public function getBookmarksByOwner($ownerId, $sorting = null)
    {
        if (isset($sorting)) {

        }

        $data = $this->bookmarks_gateway->getAllByOwner($ownerId, $sorting);
        return $data;
    }

    public function getMyBookmarks($ownerId, $sorting)
    {
        $data = $this->bookmarks_gateway->getMyBookmarks($ownerId, $sorting);
        return $data;
    }

    public function getMyHomeBookmarks($ownerId, $sorting)
    {
        $data = $this->bookmarks_gateway->getMyHomeBookmarks($ownerId, $sorting);
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

    public function getBookmarksInRange($range)
    {
        $data = $this->bookmarks_gateway->getBookmarksInRange($range);
        return $data;
    }

    public function getAllBookmarks($ownerId, $sorting) {

        $data = $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);

        return $data;
    }

    public function getBookmarkCategories() {
        $categories = $this->bookmarks_gateway->getAllBookmarkCategories();

        return $categories;
    }

    public function getBookmarkCategoryByName($categoryName) {
        $categoryName = filter_var( (string) $categoryName, FILTER_SANITIZE_STRING);

        $data = $this->bookmarks_gateway->getCategoryByName($categoryName);
        return $data;
    }

    public function addNewBookmarkCategory($categoryName) {
        $categoryName = filter_var( (string) $categoryName, FILTER_SANITIZE_STRING);

        $category = $this->bookmarks_gateway->addNewCategory($categoryName);
        return $category;
    }

    public function addBookmark($formData)
    {
        $bookmark = $this->bookmarks_gateway->addBookmark($formData);

        return $bookmark;
    }

    public function updateBookmark($formData)
    {
        $bookmark = $this->bookmarks_gateway->updateBookmark($formData);

        return $bookmark;
    }

    public function deleteBookmark($bookmarkId) {
        try {
            $bookmarkId = filter_var( (string) $bookmarkId, FILTER_SANITIZE_STRING);

            $response = $this->bookmarks_gateway->deleteBookmark( $bookmarkId );
            return $response;
        } catch(\Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }
}
