<?php


namespace phpCollab\Services;

use phpCollab\Database;

/**
 * Class ServicesGateway
 * @package phpCollab\Services
 */
class ServicesGateway
{
    protected $db;
    protected $initrequest;
    protected $tableCollab;

    /**
     * ServicesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];
        $this->tableCollab = $GLOBALS['tableCollab'];
    }

    /**
     * @param null $sorting
     * @return mixed
     */
    public function getAllServices($sorting = null)
    {
        $query = $this->initrequest["services"];
        $this->db->query($query . $this->orderBy($sorting));
        return $this->db->resultset();

    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["tea.id", "tea.project", "tea.member", "tea.published", "tea.authorized", "mem.id", "mem.login", "mem.name", "mem.email_work", "mem.title", "mem.phone_work", "org.name", "pro.id", "pro.name", "pro.priority", "pro.status", "pro.published", "org2.name", "mem2.login", "mem2.email_work", "org2.id", "log.connected", "mem.profil", "mem.password"];
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