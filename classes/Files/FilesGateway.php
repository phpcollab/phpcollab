<?php


namespace phpCollab\Files;

use phpCollab\Database;

/**
 * Class FilesGateway
 * @package phpCollab\Files
 */
class FilesGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * FilesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    public function getFiles($fileId)
    {
        if ( strpos($fileId, ',') ) {
            $ids = explode(',', $fileId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = $this->initrequest["files"] . " WHERE fil.id IN ($placeholders) OR vc_parent IN ($placeholders)";
            $this->db->query($sql);

            $this->db->execute(array_merge($ids, $ids));

            return $this->db->fetchAll();
        } else {
            $query = $this->initrequest["files"] . " WHERE fil.id IN(:file_id) OR fil.vc_parent IN(:file_id) ORDER BY fil.name";

            $this->db->query($query);

            $this->db->bind(':file_id', $fileId);

            return $this->db->resultset();
        }
    }

    public function getFileById($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id IN(:file_id) OR fil.vc_parent IN(:file_id) ORDER BY fil.name";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->single();
    }

    public function deleteFiles($fileId)
    {

        if ( strpos($fileId, ',') ) {
            $ids = explode(',', $fileId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = "DELETE FROM {$this->tableCollab['files']} WHERE id IN ($placeholders) OR vc_parent IN($placeholders)";
            $this->db->query($sql);

            $this->db->execute(array_merge($ids, $ids));

            return $this->db->fetchAll();
        } else {
            $query = "DELETE FROM {$this->tableCollab['files']} WHERE id IN (:file_id) OR vc_parent IN(:file_id)";

            $this->db->query($query);

            $this->db->bind(':file_id', $fileId);

            return $this->db->execute();
        }
    }

    public function getFileVersions($fileId, $fileStatus)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id = :file_id OR fil.vc_parent = :file_id AND fil.vc_status = :file_status ORDER BY fil.date DESC";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        $this->db->bind(':file_status', $fileStatus);

        return $this->db->resultset();
    }

    public function getFilePeerReviews($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.vc_parent = :file_id AND fil.vc_status != 3 ORDER BY fil.date";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->resultset();
    }

    /**
     * @param string $sorting
     * @return string
     */
/*
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
*/
}