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
     * @param $name
     * @param $displayName
     * @param $hourlyRate
     * @return mixed
     */
    public function addService($name, $displayName, $hourlyRate)
    {
        $query = "INSERT INTO {$this->tableCollab["services"]} (name, name_print, hourly_rate) VALUES (:name, :display_name, :hourly_rate)";
        $this->db->query($query);
        $this->db->bind(":name", $name);
        $this->db->bind(":display_name", $displayName);
        $this->db->bind(":hourly_rate", $hourlyRate);
        return $this->db->execute();
    }

    /**
     * @param $serviceId
     * @return mixed
     */
    public function getServiceById($serviceId)
    {
        $query = " WHERE serv.id = :service_id";
        $this->db->query($this->initrequest["services"] . $query);
        $this->db->bind(":service_id", $serviceId);
        return $this->db->single();
    }

    /**
     * Return an array of services
     * @param $serviceIds
     * @param null $sorting
     * @return mixed
     */
    public function getServicesById($serviceIds, $sorting = null)
    {
        $serviceIds = explode(',', $serviceIds);
        $placeholders = str_repeat('?, ', count($serviceIds) - 1) . '?';
        $sql = $this->initrequest["services"] . " WHERE id IN ($placeholders)";
        $this->db->query($sql . $this->orderBy($sorting));
        $this->db->execute($serviceIds);
        return $this->db->resultset();
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
     * @param $serviceId
     * @param $name
     * @param $displayName
     * @param $hourlyRate
     * @return mixed
     */
    public function updateService($serviceId, $name, $displayName, $hourlyRate)
    {
        $query = "UPDATE {$this->tableCollab["services"]} SET name = :name, name_print = :display_name, hourly_rate = :hourly_rate WHERE id = :service_id";

        $this->db->query($query);
        $this->db->bind(":service_id", $serviceId);
        $this->db->bind(":name", $name);
        $this->db->bind(":display_name", $displayName);
        $this->db->bind(":hourly_rate", $hourlyRate);
        return $this->db->execute();
    }

    /**
     * @param $serviceIds
     * @return mixed
     */
    public function deleteServices($serviceIds)
    {
        $serviceIds = explode(',', $serviceIds);
        $placeholders = str_repeat('?, ', count($serviceIds) - 1) . '?';
        $sql = "DELETE FROM {$this->tableCollab['services']} WHERE id IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($serviceIds);
    }

    /**
     * @param $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "tea.id",
                "tea.project",
                "tea.member",
                "tea.published",
                "tea.authorized",
                "mem.id",
                "mem.login",
                "mem.name",
                "mem.email_work",
                "mem.title",
                "mem.phone_work",
                "org.name",
                "pro.id",
                "pro.name",
                "pro.priority",
                "pro.status",
                "pro.published",
                "org2.name",
                "mem2.login",
                "mem2.email_work",
                "org2.id",
                "log.connected",
                "mem.profil",
                "mem.password"
            ];
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
