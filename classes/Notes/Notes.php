<?php


namespace phpCollab\Notes;

use phpCollab\Database;

/**
 * Class Notes
 * @package phpCollab\Notes
 */
class Notes
{
    protected $notes_gateway;
    protected $db;

    /**
     * Notes constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->notes_gateway = new NotesGateway($this->db);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function getNoteById($noteId)
    {
        if ( strpos($noteId, ',') ) {
            return $this->notes_gateway->getNotesById($noteId);
        } else {
            return $this->notes_gateway->getNoteById($noteId);
        }
    }

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     * @internal param $noteId
     */
    public function getNoteByProject($projectId, $sorting = null)
    {
        return $this->notes_gateway->getNoteByProject($projectId, $sorting);
    }

    /**
     * @param $memberId
     * @return mixed
     */
    public function getMyNotesWhereProjectIsNotCompletedOrSuspended($memberId)
    {
        $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
        return $this->notes_gateway->getMyNotesWhereProjectIsNotCompletedOrSuspended($memberId);
    }

    /**
     * @param $noteData
     * @return string
     */
    public function addNote($noteData)
    {
        return $this->notes_gateway->insertNote($noteData);
    }

    /**
     * @param $noteId
     * @param $noteData
     * @return mixed
     */
    public function updateNote($noteId, $noteData)
    {
        return $this->notes_gateway->updateNote($noteId, $noteData);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function deleteNotes($noteId)
    {
        $noteId = filter_var((string)$noteId, FILTER_SANITIZE_STRING);

        $response = $this->notes_gateway->deleteNotes($noteId);
        return $response;
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteNotesByProjectId($projectIds)
    {
        return $this->notes_gateway->deleteNotesByProjectId($projectIds);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function publishToSite($noteId)
    {
        $noteId = filter_var((string)$noteId, FILTER_SANITIZE_STRING);
        return $this->notes_gateway->publishNoteToSite($noteId);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function unPublishFromSite($noteId)
    {
        $noteId = filter_var((string)$noteId, FILTER_SANITIZE_STRING);
        return $this->notes_gateway->unPublishNoteFromSite($noteId);
    }

}
