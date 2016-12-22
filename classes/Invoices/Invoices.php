<?php
namespace phpCollab\Invoices;

use phpCollab\Database;


class Invoices
{
    protected $invoices_gateway;
    protected $db;

    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->invoices_gateway = new InvoicesGateway($this->db);
    }

    public function getInvoicesByProjectId($projectId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);

        return $this->invoices_gateway->getInvoicesByProjectId($projectId);
    }

}