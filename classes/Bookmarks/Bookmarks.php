<?php


namespace phpCollab\Bookmarks;

use InvalidArgumentException;
use Laminas\Escaper\Escaper;
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
    protected $escaper;

    /**
     * Bookmarks constructor.
     * @param Database $database
     * @param Escaper $escaper
     */
    public function __construct(Database $database, Escaper $escaper)
    {
        $this->db = $database;
        $this->escaper = $escaper;
        $this->bookmarks_gateway = new BookmarksGateway($this->db);
    }

    /**
     * @param int $ownerId
     * @param string|null $type
     * @param string|null $sorting
     * @return mixed
     */
    public function getBookmarks(int $ownerId, string $type = null, string $sorting = null)
    {
        if (!filter_var($ownerId, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('User ID not valid');
        }

        if (isset($sorting)) {
            $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        }

        switch ($type) {
            case 'private':
                $bookmarks = $this->bookmarks_gateway->getPrivateBookmarks($ownerId, $sorting);
                break;
            case 'home':
                $bookmarks = $this->bookmarks_gateway->getMyHomeBookmarks($ownerId, $sorting);
                break;
            case 'my':
                $bookmarks = $this->bookmarks_gateway->getMyBookmarks($ownerId, $sorting);
                break;
            case 'all':
            default:
                $bookmarks = $this->bookmarks_gateway->getAllBookmarks($ownerId, $sorting);
                break;
        }
        if ($bookmarks) {
            foreach ($bookmarks as $key => $bookmark) {
                $bookmarks[$key] = $this->escapeBookmarkOutput($bookmark);
            }
        }

        return $bookmarks;
    }

    /**
     * @param int $bookmarkId
     * @return mixed
     */
    public function getBookmarkById(int $bookmarkId)
    {
        $bookmark = $this->bookmarks_gateway->getBookmarkById($bookmarkId);

        return $this->escapeBookmarkOutput($bookmark);
    }

    /**
     * @param $range
     * @return mixed
     */
    public function getBookmarksInRange($range)
    {
        $bookmarks = $this->bookmarks_gateway->getBookmarksInRange($range);

        foreach ($bookmarks as $key => $bookmark) {
            $bookmarks[$key] = $this->escapeBookmarkOutput($bookmark);
        }

        return $bookmarks;

    }

    /**
     * @return mixed
     */
    public function getBookmarkCategories()
    {
        $categories = $this->bookmarks_gateway->getAllBookmarkCategories();

        foreach ($categories as $key => $category) {
            $categories[$key] = $this->escapeCategoryOutput($category);
        }

        return $categories;
    }

    /**
     * @param Bookmark $bookmark
     * @return mixed
     */
    public function update(Bookmark $bookmark)
    {
        if ($bookmark->get('category') != 0) {
            if (!(int)$bookmark->get('category')) {
                $bookmark->setCategory( $this->checkCategory($bookmark->get('category')) );
            }
        }

        if (empty($bookmark->get('id'))) {
            return $this->bookmarks_gateway->addBookmark(
                $bookmark->get('owner'),
                $bookmark->get('name'),
                $bookmark->get('url'),
                $bookmark->get('description'),
                $bookmark->get('category'),
                $bookmark->get('shared'),
                $bookmark->get('home'),
                $bookmark->get('comments'),
                $bookmark->get('sharedWith'),
                date('Y-m-d h:i')
            );
        }

        return $this->bookmarks_gateway->updateBookmark(
            $bookmark->get('id'),
            $bookmark->get('name'),
            $bookmark->get('url'),
            $bookmark->get('description'),
            $bookmark->get('category'),
            $bookmark->get('shared'),
            $bookmark->get('home'),
            $bookmark->get('comments'),
            $bookmark->get('sharedWith'),
            date('Y-m-d h:i')
        );

    }

    /**
     * @param string $categoryName
     * @return mixed
     */
    private function getBookmarkCategoryByName(string $categoryName)
    {
        $categoryName = filter_var($categoryName, FILTER_SANITIZE_STRING);

        return $this->bookmarks_gateway->getCategoryByName($categoryName);
    }

    /**
     * @param $categoryName
     * @return string
     */
    private function addNewBookmarkCategory($categoryName): string
    {
        $categoryName = filter_var((string)$categoryName, FILTER_SANITIZE_STRING);

        return $this->bookmarks_gateway->addNewCategory($categoryName);
    }

    /**
     * @param $category
     * @return mixed|string|void
     */
    private function checkCategory($category)
    {
        if (!empty($category)) {
            /**
             * Check to see if the category exists
             */
            $categoryCheck = $this->getBookmarkCategoryByName($category);

            /**
             * If category is false, hence it doesn't exist, then add it
             */
            if (!$categoryCheck) {
                return $this->addNewBookmarkCategory(Util::convertData($category));
            } else {
                return $categoryCheck["boocat_id"];
            }
        }
    }

    /**
     * @param $bookmark
     * @return array|mixed
     */
    private function escapeBookmarkOutput($bookmark)
    {
        if (is_array($bookmark)) {
            if (!empty($bookmark["boo_name"])) {
                $bookmark["boo_name"] = $this->escaper->escapeHtml($bookmark["boo_name"]);
            }

            if (!empty($bookmark["boo_description"])) {
                $bookmark["boo_description"] = $this->escaper->escapeHtml($bookmark["boo_description"]);
            }

            if (!empty($bookmark["boo_url"])) {
                $bookmark["boo_url"] = $this->escaper->escapeHtml($bookmark["boo_url"]);
            }

            if (!empty($bookmark["boo_boocat_name"])) {
                $bookmark["boo_boocat_name"] = $this->escaper->escapeHtml($bookmark["boo_boocat_name"]);
            }
        }

        return $bookmark;
    }

    /**
     * @param $category
     * @return array|mixed
     */
    private function escapeCategoryOutput($category)
    {
        if (is_array($category)) {
            if (!empty($category["boocat_name"])) {
                $category["boocat_name"] = $this->escaper->escapeHtml($category["boocat_name"]);
            }
        }

        return $category;
    }
}
