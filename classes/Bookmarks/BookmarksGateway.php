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
     * @param int $bookmarkId
     * @return bool
     */
    public function deleteBookmark(int $bookmarkId): bool
    {
        $query = "DELETE FROM {$this->db->getTableName("bookmarks")} WHERE id IN(:bookmark_id)";

        $this->db->query($query);

        $this->db->bind(':bookmark_id', $bookmarkId);

        return $this->db->execute();
    }

    /**
     * @param int $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getMyBookmarks(int $ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.owner = :owner_id ';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param int $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getMyHomeBookmarks(int $ownerId, $sorting = null)
    {
        $whereStatement = " WHERE boo.home = '1' AND boo.owner = :owner_id ";

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @param int $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getPrivateBookmarks(int $ownerId, $sorting = null)
    {
        $whereStatement = ' WHERE boo.users LIKE :owner_id';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', '%|' . $ownerId . '|%');

        return $this->db->resultset();
    }

    /**
     * @param int $bookmarkId
     * @return mixed
     */
    public function getBookmarkById(int $bookmarkId)
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
        $placeholders = str_repeat('?, ', count($range) - 1) . '?';

        $whereStatement = " WHERE boo.id IN ($placeholders)";

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement);
        $this->db->execute($range);
        return $this->db->fetchAll();
    }

    /**
     * @param int $ownerId
     * @param null $sorting
     * @return mixed
     */
    public function getAllBookmarks(int $ownerId, $sorting = null)
    {
        $whereStatement = " WHERE boo.shared = '1' OR boo.owner = :owner_id ";

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    /**
     * @return mixed
     */
    public function getAllBookmarkCategories()
    {
        $this->db->query($this->initrequest["bookmarks_categories"] . " ORDER BY name");

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
    public function addNewCategory($categoryName): string
    {
        $query = "INSERT INTO " . $this->db->getTableName("bookmarks_categories") . " (name) VALUES(:category_name)";

        $this->db->query($query);

        $this->db->bind(':category_name', $categoryName);

        $this->db->execute();

        return $this->db->lastInsertId();
    }

    /**
     * @param int $owner
     * @param string $name
     * @param string $url
     * @param string $description
     * @param int $category
     * @param int $shared
     * @param int $home
     * @param int $comments
     * @param string|null $sharedWith
     * @param string $created
     * @return mixed
     */
    public function addBookmark(
        int $owner,
        string $name,
        string $url,
        string $description,
        int $category,
        int $shared,
        int $home,
        int $comments,
        ?string $sharedWith,
        string $created)
    {
        $query = <<<SQL
INSERT INTO {$this->db->getTableName("bookmarks")} 
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

        $this->db->bind(':bookmark_owner', $owner);
        $this->db->bind(':bookmark_name', $name);
        $this->db->bind(':bookmark_url', $url);
        $this->db->bind(':bookmark_description', $description);
        $this->db->bind(':bookmark_comments', $comments);
        $this->db->bind(':bookmark_category', $category);
        $this->db->bind(':bookmark_shared', $shared);
        $this->db->bind(':bookmark_home', $home);
        $this->db->bind(':bookmark_users', $sharedWith);
        $this->db->bind(':bookmark_created', $created);

        return $this->db->execute();
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $url
     * @param string $description
     * @param int $category
     * @param int $shared
     * @param int $home
     * @param int $comments
     * @param string|null $sharedWith
     * @param string $modified
     * @return mixed
     */
    public function updateBookmark(
        int $id,
        string $name,
        string $url,
        string $description,
        int $category,
        int $shared,
        int $home,
        int $comments,
        ?string $sharedWith,
        string $modified
    )
    {
        $query = <<<SQL
UPDATE {$this->db->getTableName("bookmarks")} 
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

        $this->db->bind(':bookmark_id', $id);
        $this->db->bind(':bookmark_url', $url);
        $this->db->bind(':bookmark_category', $category);
        $this->db->bind(':bookmark_name', $name);
        $this->db->bind(':bookmark_description', $description);
        $this->db->bind(':bookmark_shared', $shared);
        $this->db->bind(':bookmark_home', $home);
        $this->db->bind(':bookmark_comments', $comments);
        $this->db->bind(':bookmark_users', $sharedWith);
        $this->db->bind(':bookmark_modified', $modified);

        return $this->db->execute();
    }

    /**
     * @param string|null $sorting
     * @return string
     */
    private function orderBy(string $sorting = null): string
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
