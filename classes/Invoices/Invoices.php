<?php

namespace phpCollab\Invoices;

use phpCollab\Database;
use Exception;


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
     * @param $title
     * @param $description
     * @param $invoiceId
     * @param $active
     * @param $completed
     * @param $mod_type
     * @param $mod_value
     * @param $worked_hours
     * @return mixed
     * @throws Exception
     */
    public function addInvoiceItem($title, $description, $invoiceId, $active, $completed, $mod_type, $mod_value,
                                   $worked_hours)
    {
        if ($title) {
            $active = is_null($active) ? 0 : $active;

            return $this->invoices_gateway->addInvoiceItem($title, $description, $invoiceId, date('Y-m-d h:i'), $active, $completed,
                $mod_type, $mod_value, $worked_hours);

        } else {
            throw new Exception('Task name is missing or appear to be empty');
        }
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

    /**
     * @param $invoicing
     * @param $completed
     * @param $hoursWorked
     * @param $taskId
     * @return mixed
     */
    public function updateInvoiceItems($invoicing, $completed, $hoursWorked, $taskId)
    {
        return $this->invoices_gateway->updateInvoiceItems($invoicing, $completed, $hoursWorked, $taskId);
    }

}
