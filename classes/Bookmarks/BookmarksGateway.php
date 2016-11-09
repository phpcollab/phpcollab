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
//    protected $stmt = <<<SQL
//SELECT
//  boo.id AS boo_id,
//  boo.owner AS boo_owner,
//  boo.category AS boo_category,
//  boo.name AS boo_name,
//  boo.url AS boo_url,
//  boo.description AS boo_description,
//  boo.shared AS boo_shared,
//  boo.home AS boo_home,
//  boo.comments AS boo_comments,
//  boo.users AS boo_users,
//  boo.created AS boo_created,
//  boo.modified AS boo_modified,
//  mem.login AS boo_mem_login,
//  mem.email_work AS boo_mem_email_work,
//  boocat.name AS boo_boocat_name
//FROM bookmarks boo
//LEFT OUTER JOIN bookmarks_categories boocat ON boocat.id = boo.category
//LEFT OUTER JOIN members mem ON mem.id = boo.owner
//SQL;

    /**
     * Reports constructor.
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
    public function getMyBookmarks($ownerId, $sorting)
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
    public function getPrivateBookmarks($ownerId, $sorting)
    {
        $whereStatement = ' WHERE boo.users LIKE :owner_id';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', '%|' . $ownerId . '|%');

        return $this->db->resultset();
    }

    /**
     * @param integer $ownerId
     * @param string $sorting
     * @return mixed
     */
    public function getAllBookmarks($ownerId, $sorting)
    {
        $whereStatement = ' WHERE boo.shared = 1 OR boo.owner = :owner_id ';

        $this->db->query($this->initrequest["bookmarks"] . $whereStatement . $this->orderBy($sorting));

        $this->db->bind(':owner_id', $ownerId);

        return $this->db->resultset();
    }

    public function getCategory($categoryName)
    {
        $conditionalStatement = ' WHERE boocat.name = :category_name';

        $this->db->query($this->initrequest["bookmarks_categories"] . $conditionalStatement);

        $this->db->bind(':category_name', $categoryName);

        return $this->db->single();
    }

    public function addNewCategory($categoryName)
    {
        $query = "INSERT INTO bookmarks_categories (name) VALUES(:category_name)";

        $this->db->query($query);

        $this->db->bind(':category_name', $categoryName);

        $this->db->execute();

        return $this->db->lastInsertId();
    }

    public function updateBookmark($bookmarkId, $formData)
    {

        xdebug_var_dump($formData);
        die();
        $query = <<<SQL
UPDATE bookmarks 
SET 
url=:url, 
name=:name, 
description=:description, 
modified=:modified,
category=:category,
shared=:shared,
home=:home,
comments=:comments,
users=:users
WHERE id = :id
SQL;
        $this->db->query($query);

        $this->db->bind(':url', $_POST['url']);
        $this->db->bind(':category', $categoryName);

        $this->db->execute();
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        return (!is_null($sorting)) ? ' ORDER BY ' . $sorting : '';
    }

}
