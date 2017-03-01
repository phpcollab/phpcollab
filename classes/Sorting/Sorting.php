<?php
namespace phpCollab\Sorting;

use phpCollab\Database;


/**
 * Class Sorting
 * @package phpCollab\Sorting
 */
class Sorting
{
    protected $sorting_gateway;
    protected $db;

    /**
     * Sorting constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->sorting_gateway = new SortingGateway($this->db);
    }

    /**
     * @param $ownerId
     */
    public function getSortingValues($ownerId)
    {
    }

    /**
     * @param $sorting
     * @return string
     */
    public function bookmarkSorting($sorting)
    {
        xdebug_var_dump($sorting);
        if (!is_null($sorting)) {
            $allowedOrderedBy = ["boo.name", "boo.category", "mem.login"];
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

    /**
     * @param $memberId
     */
    public function deleteByMember($memberId)
    {
        $this->sorting_gateway->deleteByMember($memberId);
    }

}