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
    protected $tableCollab;

    /**
     * InvoicesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param $project
     * @param $status
     * @param $active
     * @param $published
     * @param $created
     * @return string
     */
    public function addInvoice($project, $status, $active, $published, $created)
    {
        $sql = <<<SQL
INSERT INTO {$this->tableCollab["invoices"]} 
(project, status, active, published, created) 
VALUES 
(:project, :status, :active, :published, :created)
SQL;
        $this->db->query($sql);
        $this->db->bind(":project", $project);
        $this->db->bind(":status", $status);
        $this->db->bind(":active", $active);
        $this->db->bind(":published", $published);
        $this->db->bind(":created", $created);
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    /**
     * @param $title
     * @param $description
     * @param $invoiceId
     * @param $created
     * @param $active
     * @param $completed
     * @param $mod_type
     * @param $mod_value
     * @param $worked_hours
     * @return mixed
     */
    public function addInvoiceItem($title, $description, $invoiceId, $created, $active, $completed, $mod_type, $mod_value,
                                   $worked_hours)
    {
        $sql = <<< SQL
INSERT INTO {$this->tableCollab["invoices_items"]} (
title,description,invoice,created,active,completed,mod_type,mod_value,worked_hours
) VALUES (
:title,:description,:invoice,:created,:active,:completed,:mod_type,:mod_value,:worked_hours)
SQL;
        $this->db->query($sql);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':invoice', $invoiceId);
        $this->db->bind(':created', $created);
        $this->db->bind(':active', $active);
        $this->db->bind(':completed', $completed);
        $this->db->bind(':mod_type', $mod_type);
        $this->db->bind(':mod_value', $mod_value);
        $this->db->bind(':worked_hours', $worked_hours);

        $this->db->execute();
        return $this->db->lastInsertId();

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
        $sql = <<<SQL
UPDATE {$this->tableCollab["invoices_items"]} 
SET 
active = :active,
completed = :completed,
worked_hours = :worked_hours 
WHERE mod_type = 1 AND mod_value = :mod_value
SQL;

        $this->db->query($sql);
        $this->db->bind('active', $invoicing);
        $this->db->bind('completed', $completed);
        $this->db->bind('worked_hours', $hoursWorked );
        $this->db->bind('mod_value', $taskId );

        return $this->db->execute();

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
     * @param $projectId
     * @param $activeFlag
     * @return mixed
     */
    public function setActive($projectId, $activeFlag)
    {
        $sql = "UPDATE {$this->tableCollab["invoices"]} SET active = :active WHERE project = :project_id";
        $this->db->query($sql);
        $this->db->bind(":project_id", $projectId);
        $this->db->bind(":active", $activeFlag);
        return $this->db->execute();
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
