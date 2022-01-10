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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->sorting_gateway = new SortingGateway($this->db);
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function addMember($userId)
    {
        return $this->sorting_gateway->addMember($userId);
    }

    /**
     * @param $ownerId
     * @return mixed
     */
    public function getSortingValues($ownerId)
    {
        return $this->sorting_gateway->getSortingValues($ownerId);
    }

    /**
     * @param String $target
     * @param String $value
     * @param int $userId
     * @return mixed
     */
    public function updateSortingTargetByUserId(string $target, string $value, int $userId)
    {
        return $this->sorting_gateway->updateSortingTargetByUserId($target, $value, $userId);
    }

    /**
     * @param $sorting
     * @return string
     */
    public function bookmarkSorting($sorting): string
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
