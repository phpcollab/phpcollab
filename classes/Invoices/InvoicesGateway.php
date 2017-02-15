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

    public function getInvoiceById($invoiceId)
    {
        $invoice_id = filter_var($invoiceId, FILTER_VALIDATE_INT);
        $whereStatement = ' WHERE inv.id = :invoice_id ';
        $this->db->query($this->initrequest["invoices"] . $whereStatement);
        $this->db->bind(':invoice_id', $invoice_id);
        return $this->db->single();
    }

    public function getInvoicesByProjectId($projectId)
    {
        $project_id = filter_var($projectId, FILTER_VALIDATE_INT);
        $whereStatement = ' WHERE project = :project_id ';
        $this->db->query($this->initrequest["invoices"] . $whereStatement);
        $this->db->bind(':project_id', $project_id);
        return $this->db->single();
    }

    public function getActiveInvoicesByProjectId($projectId, $status, $sorting = null)
    {
        // Break out the project Ids if there are multiple ones
        xdebug_var_dump($projectId);
        xdebug_var_dump($status);
        xdebug_var_dump($sorting);
//        $ids = explode(',', $projectId);
        $ids = $projectId;
//        xdebug_var_dump($ids);
//        $ids = explode(',', $projectId);
//        $projectId = explode(',', $projectId);
//        xdebug_var_dump(implode(",", $projectId));

        // Generate placeholders
        $placeholders = str_repeat ('?, ', count($ids)-1) . '?';

        // Append the status value
        array_push($ids, $status);


//        $tmpquery = "WHERE inv.project IN($projectsOk) AND inv.active = '1' AND inv.status = '$status' ORDER BY $block1->sortingValue";
        $whereStatement = ' WHERE inv.project IN ('.$placeholders.') AND inv.active = 1 AND inv.status = ?';
//        $whereStatement = ' WHERE inv.project IN (:project_ids) AND inv.active = 1 AND inv.status = :status';

//        xdebug_var_dump($this->initrequest["invoices"] . $whereStatement . $this->orderBy($sorting));

//        xdebug_var_dump($this->initrequest["invoices"] . $whereStatement . $this->orderBy($sorting));
//        xdebug_var_dump($status);

        xdebug_var_dump($projectId);
//        xdebug_var_dump(implode(",", $projectId));
        xdebug_var_dump($status);
xdebug_var_dump($this->initrequest["invoices"] . $whereStatement . $this->orderBy($sorting));

        $this->db->query($this->initrequest["invoices"] . $whereStatement . $this->orderBy($sorting));
//        $this->db->bind(':project_ids', implode(",", $projectId));
//        $this->db->bind(':status', $status);
//        return $this->db->resultset();

        $this->db->execute($ids);
        return $this->db->fetchAll();
    }

    public function getActiveInvoiceItemsByInvoiceId($invoiceId)
    {
        $invoice_id = filter_var($invoiceId, FILTER_VALIDATE_INT);
        $whereStatement = " WHERE invitem.invoice = :invoice_id AND invitem.active = '1' ORDER BY invitem.position ASC";

        $this->db->query($this->initrequest["invoices_items"] . $whereStatement);
        $this->db->bind(':invoice_id', $invoice_id);
        return $this->db->resultset();
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