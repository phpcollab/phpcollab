<?php


namespace phpCollab\Invoices;


use Exception;
use InvalidArgumentException;
use phpCollab\Database;

class UpdateInvoiceItem
{
    /**
     * @var Database
     */
    private $database;
    private $tableCollab;

    public function __construct(Database $database, $tableCollab)
    {
        $this->database = $database;
        $this->tableCollab = $tableCollab;
    }

    /**
     * @param int $itemId
     * @param string $rateType
     * @param string $rateValue
     * @param string $exTaxAmount
     * @return mixed
     * @throws Exception
     */
    public function update(int $itemId, string $rateType, string $rateValue, string $exTaxAmount)
    {
        if (empty($itemId) || !is_int($itemId)) {
            throw new InvalidArgumentException('Invoice Item ID is missing or invalid.');
        }

        try {
            return $this->updateItem($itemId, $rateType, $rateValue, $exTaxAmount);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $itemId
     * @param string $rateType
     * @param string $rateValue
     * @param string $exTaxAmount
     * @return mixed
     */
    private function updateItem(int $itemId, string $rateType, string $rateValue, string $exTaxAmount)
    {
        $query = <<< SQL
UPDATE {$this->tableCollab["invoices_items"]}
SET
    rate_type = :rate_type,
    rate_value = :rate_value,
    amount_ex_tax = :amount_ex_tax,
    modified = :modified
WHERE id = :id
SQL;
        $this->database->query($query);
        $this->database->bind(":id", $itemId);
        $this->database->bind(":rate_type", $rateType);
        $this->database->bind(":rate_value", $rateValue);
        $this->database->bind(":amount_ex_tax", $exTaxAmount);
        $this->database->bind(":modified", date('Y-m-d h:i'));
        return $this->database->execute();
    }

}
