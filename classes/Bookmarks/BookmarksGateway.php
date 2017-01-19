<?php
namespace phpCollab\Bookmarks;

use phpCollab\Database;


/**
 * Class BookmarksGateway
 * @package phpCollab\Bookmarks
 */
class BookmarksGateway
{
    protected $db;
    protected $initrequest;

    /**
     * Bookmarks constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    /**
     * Delete bookmark
     * @param integer $bookmarkId
     * @return bool
     */
    public function deleteBookmark($bookmarkId)
    {
        $query = 'DELETE FROM bookmarks WHERE id IN(:bookmark_id)';

        $this->db->query($query);

        $this->db->bind(':bookmark_id', $bookmarkId);

        return $this->db->execute();
    }

    /**
     * @param integer $ownerId
     * @param string $sorting
     * @return mixed
     */
    public function getMyBookmarks($ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.owner = :owner_id ';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param integer $ownerId
     * @param string $sorting
     * @return mixed
     */
    public function getMyHomeBookmarks($ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.home = 1 AND boo.owner = :owner_id ';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param integer $ownerId
     * @param string $sorting
     * @return mixed
     */
    public function getPrivateBookmarks($ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.users LIKE :owner_id';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', '%|' . $ownerId . '|%');

        return $this->db->resultset();
    }

    /**
     * @param integer $bookmarkId
     * @return mixed
     */
    public function getBookmarkById($bookmarkId)
    {
        $whereStatement = ' WHERE boo.id = :bookmark_id';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement);

        $this->db->bind(':bookmark_id', $bookmarkId);

        return $this->db->single();
    }

    /**
     * @param $range
     * @return mixed
     */
    public function getBookmarksInRange($range)
    {
        $placeholders = str_repeat ('?, ', count($range)-1) . '?';

        $whereStatement = " WHERE boo.id IN ($placeholders)";

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement);
        $this->db->execute($range);
        return $this->db->fetchAll();
    }

    /**
     * @param integer $ownerId
     * @param string $sorting
     * @return mixed
     */
    public function getAllBookmarks($ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.shared = 1 OR boo.owner = :owner_id ';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @return mixed
     */
    public function getAllBookmarkCategories()
    {
        $this->db->query($this->initrequest["bookmarks_categories"] . "ORDER BY name");

        return $this->db->resultset();
    }

    /**
     * @param $categoryName
     * @return mixed
     */
    public function getCategoryByName($categoryName)
    {
        $conditionalStatement = ' WHERE boocat.name = :category_name';

        $this->db->query($this->initrequest["bookmarks_categories"] . $conditionalStatement);

        $this->db->bind(':category_name', $categoryName);

        return $this->db->single();
    }

    /**
     * @param $categoryName
     * @return string
     */
    public function addNewCategory($categoryName)
    {
        $query = "INSERT INTO bookmarks_categories (name) VALUES(:category_name)";

        $this->db->query($query);

        $this->db->bind(':category_name', $categoryName);

        $this->db->execute();

        return $this->db->lastInsertId();
    }

    /**
     * @param $bookmarkData
     * @return mixed
     */
    public function addBookmark($bookmarkData)
    {
        $query = <<<SQL
INSERT INTO bookmarks 
(owner, category, name, url, description, shared, home, comments, users, created) 
VALUES(
  :bookmark_owner, 
  :bookmark_category, 
  :bookmark_name, 
  :bookmark_url, 
  :bookmark_description, 
  :bookmark_shared, 
  :bookmark_home,
  :bookmark_comments,
  :bookmark_users,
  :bookmark_created
)
SQL;

        $this->db->query($query);

        $this->db->bind(':bookmark_owner', $bookmarkData['owner_id']);
        $this->db->bind(':bookmark_url', $bookmarkData['url']);
        $this->db->bind(':bookmark_name', $bookmarkData['name']);
        $this->db->bind(':bookmark_description', $bookmarkData['description']);
        $this->db->bind(':bookmark_comments', $bookmarkData['comments']);
        $this->db->bind(':bookmark_created', $bookmarkData['created']);
        $this->db->bind(':bookmark_category', $bookmarkData['category']);
        $this->db->bind(':bookmark_shared', $bookmarkData['shared']);
        $this->db->bind(':bookmark_home', $bookmarkData['home']);
        $this->db->bind(':bookmark_users', $bookmarkData['users']);

        return $this->db->execute();
    }

    /**
     * @param $bookmarkData
     * @return mixed
     */
    public function updateBookmark($bookmarkData)
    {
        $query = <<<SQL
UPDATE bookmarks 
SET 
url=:bookmark_url, 
name=:bookmark_name, 
description=:bookmark_description, 
modified=:bookmark_modified,
category=:bookmark_category,
shared=:bookmark_shared,
home=:bookmark_home,
comments=:bookmark_comments,
users=:bookmark_users
WHERE id = :bookmark_id
SQL;

        $this->db->query($query);

        $this->db->bind(':bookmark_id', $bookmarkData['id']);
        $this->db->bind(':bookmark_url', $bookmarkData['url']);
        $this->db->bind(':bookmark_category', $bookmarkData['category']);
        $this->db->bind(':bookmark_name', $bookmarkData['name']);
        $this->db->bind(':bookmark_description', $bookmarkData['description']);
        $this->db->bind(':bookmark_modified', $bookmarkData['modified']);
        $this->db->bind(':bookmark_shared', $bookmarkData['shared']);
        $this->db->bind(':bookmark_home', $bookmarkData['home']);
        $this->db->bind(':bookmark_comments', $bookmarkData['comments']);
        $this->db->bind(':bookmark_users', $bookmarkData['users']);

        return $this->db->execute();
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["boo.name", "boo.category", "mem.login"];
            $pieces = explode(' ', $sorting);

            if ($pieces) {
                $key = array_search($pieces[0], $allowedOrderedBy);

                if ($key !== false) {
                    $order = $allowedOrderedBy[$key];
                    return " ORDER BY $order $pieces[1]";
                }
            }
        }

        return '';
    }

}
