<?php
namespace phpCollab\Invoices;

use phpCollab\Database;


class InvoicesGateway
{
    protected $db;
    protected $initrequest;

    /**
     * InvoicesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
    }

    public function getInvoicesByProjectId($projectId)
    {
        $project_id = filter_var($projectId, FILTER_VALIDATE_INT);

        $whereStatement = ' WHERE project = :project_id ';

        $this->db->query($this->initrequest["invoices"] . $whereStatement);

        $this->db->bind(':project_id', $project_id);

        return $this->db->resultset();
    }

}