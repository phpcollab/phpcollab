<?php


namespace phpCollab\Notes;

use phpCollab\Database;

/**
 * Class NotesGateway
 * @package phpCollab\Notes
 */
class NotesGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * NotesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function getNoteById($noteId)
    {
        if ( strpos($noteId, ',') ) {
            $ids = explode(',', $noteId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = $this->initrequest["notes"] . " WHERE note.id IN ($placeholders) ORDER BY note.subject";
            $this->db->query($sql);
            $this->db->execute($ids);
            return $this->db->fetchAll();
        } else {
            $query = $this->initrequest["notes"] . " WHERE note.id IN (:note_id) ORDER BY note.subject";
            $this->db->query($query);
            $this->db->bind(':note_id', $noteId);
            return $this->db->resultset();
        }
    }

    /**
     * @param $projectId
     * @param $sorting
     * @return mixed
     * @internal param $noteId
     */
    public function getNoteByProject($projectId, $sorting)
    {
        $query = $this->initrequest["notes"] . " WHERE note.project = :project_id" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function deleteNotes($noteId)
    {
        if ( strpos($noteId, ',') ) {
            $ids = explode(',', $noteId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = "DELETE FROM {$this->tableCollab['notes']} WHERE id IN ($placeholders)";
            $this->db->query($sql);

            $this->db->execute($ids);

            return $this->db->fetchAll();
        } else {
            $query = "DELETE FROM {$this->tableCollab['notes']} WHERE id IN (:note_id)";
            $this->db->query($query);
            $this->db->bind(':note_id', $noteId);
            return $this->db->execute();
        }
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function publishNoteToSite($noteId)
    {
        if ( strpos($noteId, ',') ) {
            $ids = explode(',', $noteId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = "UPDATE {$this->tableCollab["notes"]} SET published=0 WHERE id IN($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($ids);
        } else {
            $query = "UPDATE {$this->tableCollab["notes"]} SET published=0 WHERE id = :note_id";
            $this->db->query($query);
            $this->db->bind(':note_id', $noteId);
            return $this->db->execute();
        }
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function unPublishNoteFromSite($noteId)
    {
        if ( strpos($noteId, ',') ) {
            $ids = explode(',', $noteId);
            $placeholders = str_repeat ('?, ', count($ids)-1) . '?';
            $sql = "UPDATE {$this->tableCollab["notes"]} SET published=1 WHERE id IN($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($ids);
        } else {
            $query = "UPDATE {$this->tableCollab["notes"]} SET published=1 WHERE id = :note_id";
            $this->db->query($query);
            $this->db->bind(':note_id', $noteId);
            return $this->db->execute();
        }
    }

    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["note.id", "note.project", "note.owner", "note.topic", "note.subject", "note.description", "note.date", "note.published", "mem.id", "mem.login", "mem.name", "mem.email_work"];
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