<?php


namespace phpCollab\Invoices;


use Exception;
use InvalidArgumentException;
use phpCollab\Database;

class Publish
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $invoiceId
     * @return mixed
     * @throws Exception
     */
    public function add(int $invoiceId)
    {
        if (empty($invoiceId) || !is_int($invoiceId)) {
            throw new InvalidArgumentException('Organization ID is missing or invalid.');
        }

        try {
            return $this->setPublished($invoiceId, true);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $invoiceId
     * @return mixed
     * @throws Exception
     */
    public function remove(int $invoiceId)
    {
        if (empty($invoiceId) || !is_int($invoiceId)) {
            throw new InvalidArgumentException('Organization ID is missing or invalid.');
        }

        try {
            return $this->setPublished($invoiceId, false);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param int $invoiceId
     * @param bool $flag
     * @return mixed
     */
    private function setPublished(int $invoiceId, bool $flag)
    {
        // Due to values being stored inversely in the database, let's flip the flag.
        // This needs to be refactored throughout the application
        // 1 = false, 0 = true
        $flag = ($flag === true) ? '0' : '1';

        $query = <<< SQL
UPDATE {$this->database->getTableName("invoices")}
SET published = :flag 
WHERE id = :invoice_id
SQL;
        $this->database->query($query);
        $this->database->bind(":invoice_id", $invoiceId);
        $this->database->bind(":flag", $flag);
        return $this->database->execute();
    }
}
