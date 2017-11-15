<?php
namespace phpCollab\Invoices;

use phpCollab\Database;


/**
 * Class Invoices
 * @package phpCollab\Invoices
 */
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

    /**
     * @param $invoiceId
     * @return mixed
     */
    public function getInvoiceById($invoiceId)
    {
        $invoiceId = filter_var($invoiceId, FILTER_VALIDATE_INT);

        return $this->invoices_gateway->getInvoiceById($invoiceId);
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getInvoicesByProjectId($projectId)
    {
        $projectId = filter_var($projectId, FILTER_VALIDATE_INT);
        return $this->invoices_gateway->getInvoicesByProjectId($projectId);
    }

    /**
     * @param $projectId
     * @param $status
     * @param null $sorting
     * @return mixed
     */
    public function getActiveInvoicesByProjectId($projectId, $status, $sorting = null)
    {
        return $this->invoices_gateway->getActiveInvoicesByProjectId($projectId, $status, $sorting);
    }

    /**
     * @param $invoiceId
     * @return mixed
     */
    public function getActiveInvoiceItemsByInvoiceId($invoiceId)
    {
        $invoiceId = filter_var($invoiceId, FILTER_VALIDATE_INT);

        return $this->invoices_gateway->getActiveInvoiceItemsByInvoiceId($invoiceId);
    }

    /**
     * @param $invoiceItemId
     * @return mixed
     */
    public function getInvoiceItemById($invoiceItemId)
    {
        $invoiceItemId = filter_var($invoiceItemId, FILTER_VALIDATE_INT);

        return $this->invoices_gateway->getInvoiceItemById($invoiceItemId);
    }

}
