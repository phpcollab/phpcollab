<?php


namespace phpCollab\Bookmarks;

use InvalidArgumentException;
use phpCollab\Database;
use phpCollab\Util;


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
     * @param $bookmarkData
     * @return array
     */
    public function validateData($bookmarkData)
    {
        $category = '';

        if (empty($bookmarkData)) {
            throw new InvalidArgumentException('Bookmark data is invalid');
        }


        if (!empty($bookmarkData["piecesNew"])) {
            $bookmarkData["piecesNew"] = "|" . implode("|", $bookmarkData["piecesNew"]) . "|";
        }
        if (!empty($bookmarkData["category_new"])) {
            /**
             * Check to see if the category exists
             */
            $category = $this->getBookmarkCategoryByName($bookmarkData["category_new"]);

            /**
             * If category is false, hence it doesn't exist, then add it
             */
            if (!$category) {
                $category = $this->addNewBookmarkCategory(Util::convertData($bookmarkData["category_new"]));
            } else {
                $category = $category["boocat_id"];
            }
        }

        if (empty($bookmarkData["shared"]) || !empty($users)) {
            $bookmarkData["shared"] = 0;
        }
        if ($bookmarkData["home"] == "") {
            $bookmarkData["home"] = 0;
        }
        if ($bookmarkData["comments"] == "") {
            $bookmarkData["comments"] = 0;
        }

        /**
         * Filter/Sanitize form data
         */
        $filteredData = array();
        $filteredData['url'] = filter_var((string)Util::addHttp($bookmarkData["url"]),
            FILTER_SANITIZE_URL);
        $filteredData['name'] = filter_var((string)Util::convertData($bookmarkData["name"]),
            FILTER_SANITIZE_STRING);
        $filteredData['description'] = filter_var((string)Util::convertData($bookmarkData["description"]),
            FILTER_SANITIZE_STRING);
        $filteredData['comments'] = filter_var(Util::convertData($bookmarkData["comments"]), FILTER_SANITIZE_STRING);
        $filteredData['timestamp'] = date('Y-m-d h:i');
        $filteredData['category'] = filter_var((int)$category, FILTER_VALIDATE_INT);
        $filteredData['shared'] = filter_var((int)$bookmarkData["shared"], FILTER_VALIDATE_INT);
        $filteredData['home'] = filter_var((int)$bookmarkData["home"], FILTER_VALIDATE_INT);
        $filteredData['users'] = $bookmarkData["piecesNew"];

        return $filteredData;
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
}
