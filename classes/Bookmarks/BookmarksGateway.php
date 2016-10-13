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
    public function deleteBookmark( $bookmarkId )
    {
        $query = 'DELETE FROM bookmarks WHERE id IN(:bookmark_id)';

        $this->db->query($query);

        $this->db->bind(':bookmark_id', $bookmarkId);

        return $this->db->execute();
    }

}
