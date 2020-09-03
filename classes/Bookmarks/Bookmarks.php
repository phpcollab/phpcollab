<?php


namespace phpCollab\Bookmarks;

use Exception;
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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
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
        return $this->bookmarks_gateway->getMyBookmarks($ownerId, $sorting);
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
        return $this->bookmarks_gateway->getMyHomeBookmarks($ownerId, $sorting);
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
        return $this->bookmarks_gateway->getPrivateBookmarks($ownerId, $sorting);
    }

    /**
     * @param $bookmarkId
     * @return mixed
     */
    public function getBookmarkById($bookmarkId)
    {
        return $this->bookmarks_gateway->getBookmarkById($bookmarkId);
    }

    /**
     * @param $range
     * @return mixed
     */
    public function getBookmarksInRange($range)
    {
        return $this->bookmarks_gateway->getBookmarksInRange($range);
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
        return $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);
    }

    /**
     * @return mixed
     */
    public function getBookmarkCategories()
    {
        return $this->bookmarks_gateway->getAllBookmarkCategories();
    }

    /**
     * @param $categoryName
     * @return mixed
     */
    public function getBookmarkCategoryByName($categoryName)
    {
        $categoryName = filter_var((string)$categoryName, FILTER_SANITIZE_STRING);

        return $this->bookmarks_gateway->getCategoryByName($categoryName);
    }

    /**
     * @param $categoryName
     * @return string
     */
    public function addNewBookmarkCategory($categoryName)
    {
        $categoryName = filter_var((string)$categoryName, FILTER_SANITIZE_STRING);

        return $this->bookmarks_gateway->addNewCategory($categoryName);
    }

    /**
     * @param $formData
     * @return mixed
     */
    public function addBookmark($formData)
    {
        return $this->bookmarks_gateway->addBookmark($formData);
    }

    /**
     * @param $formData
     * @return mixed
     */
    public function updateBookmark($formData)
    {
        return $this->bookmarks_gateway->updateBookmark($formData);
    }

    /**
     * @param $bookmarkId
     * @return string
     */
    public function deleteBookmark($bookmarkId)
    {
        try {
            $bookmarkId = filter_var((string)$bookmarkId, FILTER_SANITIZE_STRING);

            return $this->bookmarks_gateway->deleteBookmark($bookmarkId);
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
        return '';
    }
}
