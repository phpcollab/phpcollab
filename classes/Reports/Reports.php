<?php
/**
 * Created by mindblender.
 * User: mindblender
 * Date: 5/27/16
 * Time: 10:36 PM
 */

namespace Reports;

use phpCollab\Reports\ReportsGateway;

class Reports
{
    protected $reports_gateway;

    public function __construct(ReportsGateway $reports_gateway)
    {
        $this->reports_gateway = $reports_gateway;
    }

    public function getReportsByOwner($ownerId)
    {
        $rows = $this->reports_gateway->getAllByOwner($ownerId);
        return $rows;
    }

}