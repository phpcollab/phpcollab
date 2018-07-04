<?php
namespace phpCollab\Sorting;

use phpCollab\Database;
use phpDocumentor\Reflection\Types\Integer;


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
     * @param String $target
     * @param String $value
     * @param Integer $userId
     * @return
     */
    public function updateSortingTargetByUserId($target, $value, $userId)
    {
        return $this->sorting_gateway->updateSortingTargetByUserId($target, $value, $userId);
    }

    /**
     * @param $sorting
     * @return string
     */
    public function bookmarkSorting($sorting)
    {
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
