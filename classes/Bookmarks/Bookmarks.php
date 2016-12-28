<?php


namespace phpCollab\Bookmarks;

use phpCollab\Database;


/**
 * Class Bookmarks
 * @package phpCollab\Bookmarks
 */
class Bookmarks
{
    protected $bookmarks_gateway;
    protected $db;

    /**
     * Bookmarks constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->bookmarks_gateway = new BookmarksGateway($this->db);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getMyBookmarks($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $data = $this->bookmarks_gateway->getMyBookmarks($ownerId, $sorting);
        return $data;
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getMyHomeBookmarks($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $data = $this->bookmarks_gateway->getMyHomeBookmarks($ownerId, $sorting);
        return $data;
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getPrivateBookmarks($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $data = $this->bookmarks_gateway->getPrivateBookmarks($ownerId, $sorting);
        return $data;
    }

    /**
     * @param $bookmarkId
     * @return mixed
     */
    public function getBookmarkById($bookmarkId)
    {
        $data = $this->bookmarks_gateway->getBookmarkById($bookmarkId);
        return $data;
    }

    /**
     * @param $range
     * @return mixed
     */
    public function getBookmarksInRange($range)
    {
        $data = $this->bookmarks_gateway->getBookmarksInRange($range);
        return $data;
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getAllBookmarks($ownerId, $sorting)
    {
        $ownerId = filter_var($ownerId, FILTER_VALIDATE_INT);
        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }
        $data = $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);

        return $data;
    }

    /**
     * @return mixed
     */
    public function getBookmarkCategories()
    {
        $categories = $this->bookmarks_gateway->getAllBookmarkCategories();

        return $categories;
    }

    /**
     * @param $categoryName
     * @return mixed
     */
    public function getBookmarkCategoryByName($categoryName)
    {
        $categoryName = filter_var((string)$categoryName, FILTER_SANITIZE_STRING);

        $data = $this->bookmarks_gateway->getCategoryByName($categoryName);
        return $data;
    }

    /**
     * @param $categoryName
     * @return string
     */
    public function addNewBookmarkCategory($categoryName)
    {
        $categoryName = filter_var((string)$categoryName, FILTER_SANITIZE_STRING);

        $category = $this->bookmarks_gateway->addNewCategory($categoryName);
        return $category;
    }

    /**
     * @param $formData
     * @return mixed
     */
    public function addBookmark($formData)
    {
        $bookmark = $this->bookmarks_gateway->addBookmark($formData);

        return $bookmark;
    }

    /**
     * @param $formData
     * @return mixed
     */
    public function updateBookmark($formData)
    {
        $bookmark = $this->bookmarks_gateway->updateBookmark($formData);

        return $bookmark;
    }

    /**
     * @param $bookmarkId
     * @return string
     */
    public function deleteBookmark($bookmarkId)
    {
        try {
            $bookmarkId = filter_var((string)$bookmarkId, FILTER_SANITIZE_STRING);

            $response = $this->bookmarks_gateway->deleteBookmark($bookmarkId);
            return $response;
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
        return '';
    }
}
