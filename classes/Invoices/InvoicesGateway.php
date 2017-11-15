<?php
namespace phpCollab\Invoices;

use phpCollab\Database;


/**
 * Class InvoicesGateway
 * @package phpCollab\Invoices
 */
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

    /**
     * @param $invoiceId
     * @return mixed
     */
    public function getInvoiceById($invoiceId)
    {
        $invoice_id = filter_var($invoiceId, FILTER_VALIDATE_INT);
        $whereStatement = ' WHERE inv.id = :invoice_id ';
        $this->db->query($this->initrequest["invoices"] . $whereStatement);
        $this->db->bind(':invoice_id', $invoice_id);
        return $this->db->single();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getInvoicesByProjectId($projectId)
    {
        $project_id = filter_var($projectId, FILTER_VALIDATE_INT);
        $whereStatement = ' WHERE project = :project_id ';
        $this->db->query($this->initrequest["invoices"] . $whereStatement);
        $this->db->bind(':project_id', $project_id);
        return $this->db->single();
    }

    /**
     * @param $projectId
     * @param $status
     * @param null $sorting
     * @return mixed
     */
    public function getActiveInvoicesByProjectId($projectId, $status, $sorting = null)
    {
        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($projectId)-1) . '?';

        // Append the status value
        array_push($projectId, $status);
        $whereStatement = " WHERE inv.project IN ({$placeholders}) AND inv.active = 1 AND inv.status = ?";
        $this->db->query($this->initrequest["invoices"] . $whereStatement . $this->orderBy($sorting));
        $this->db->execute($projectId);
        return $this->db->fetchAll();
    }

    /**
     * @param $invoiceId
     * @return mixed
     */
    public function getActiveInvoiceItemsByInvoiceId($invoiceId)
    {
        $invoice_id = filter_var($invoiceId, FILTER_VALIDATE_INT);
        $whereStatement = " WHERE invitem.invoice = :invoice_id AND invitem.active = '1' ORDER BY invitem.position ASC";

        $this->db->query($this->initrequest["invoices_items"] . $whereStatement);
        $this->db->bind(':invoice_id', $invoice_id);
        return $this->db->resultset();
    }

    /**
     * @param $invoiceItemId
     * @return mixed
     */
    public function getInvoiceItemById($invoiceItemId)
    {
        $invoice_item_id = filter_var($invoiceItemId, FILTER_VALIDATE_INT);
        $whereStatement = " WHERE invitem.id = :invoice_item_id";

        $this->db->query($this->initrequest["invoices_items"] . $whereStatement);
        $this->db->bind(':invoice_item_id', $invoice_item_id);
        return $this->db->single();
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["inv.id","inv.project","inv.date_sent","inv.due_date","inv.status","inv.total_inc_tax","inv.active","inv.published","invitem.position","pro.name"];
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
