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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->notes_gateway = new NotesGateway($this->db);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function getNoteById($noteId)
    {
        return $this->notes_gateway->getNoteById($noteId);
    }

    /**
     * @param $noteId
     * @return mixed
     */
    public function getNotesById($noteId)
    {
        return $this->notes_gateway->getNotesById($noteId);
    }

    /**
     * @param $projectId
     * @param null $offset
     * @param null $limit
     * @param null $sorting
     * @return mixed
     * @internal param $noteId
     */
    public function getNoteByProject($projectId, $offset = null, $limit = null, $sorting = null)
    {
        return $this->notes_gateway->getNoteByProject($projectId, $offset, $limit, $sorting);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getNotesCountByProject($projectId)
    {
        return $this->notes_gateway->getNoteByProject($projectId);
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
     * @param $ownerId
     * @param $dateFilter
     * @param null $sorting
     * @return mixed
     */
    public function getMyDateFilteredNotes($ownerId, $dateFilter = null, $sorting = null)
    {
        return $this->notes_gateway->getDateFilteredNotesByOwner($ownerId, $dateFilter, $sorting);
    }

    /**
     * @param $noteData
     * @return string
     */
    public function addNote($noteData): string
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

        return $this->notes_gateway->deleteNotes($noteId);
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

    /**
     * @param $tmpQuery
     * @param null $sorting
     * @param null $limit
     * @param null $rowLimit
     * @return mixed
     */
    public function getSearchNotes($tmpQuery, $sorting = null, $limit = null, $rowLimit = null)
    {
        return $this->notes_gateway->searchResultNotes($tmpQuery, $sorting, $limit, $rowLimit);
    }
}
