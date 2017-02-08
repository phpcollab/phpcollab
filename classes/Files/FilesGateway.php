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

    /**
     * @param $fileId
     * @return mixed
     */
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

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFileById($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id IN(:file_id) OR fil.vc_parent IN(:file_id) ORDER BY fil.name";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->single();
    }

    /**
     * @return mixed
     */
    public function getPublishedFiles()
    {
        $query = $this->initrequest["files"] . " WHERE fil.published = 0";
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @return mixed
     */
    public function getUnPublishedFiles()
    {
        $query = $this->initrequest["files"] . " WHERE fil.published = 1";
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @param $fileId
     * @return mixed
     */
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

    /**
     * @param $fileId
     * @param $fileStatus
     * @return mixed
     */
    public function getFileVersions($fileId, $fileStatus)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id = :file_id OR fil.vc_parent = :file_id AND fil.vc_status = :file_status ORDER BY fil.date DESC";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        $this->db->bind(':file_status', $fileStatus);

        return $this->db->resultset();
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFilePeerReviews($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.vc_parent = :file_id AND fil.vc_status != 3 " . $this->orderBy('fil.date');
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->resultset();
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["fil.type", "fil.name", "fil.owner", "fil.date", "fil.approval_tracking", "fil.published"];
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