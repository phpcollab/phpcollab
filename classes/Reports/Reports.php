<?php
namespace phpCollab\Reports;

use phpCollab\Database;

/**
 * Class Reports
 * @package phpCollab\Reports
 */
class Reports
{
    protected $reports_gateway;
    protected $db;

    /**
     * Reports constructor.
     */
    public function __construct()
    {
        $this->db = new Database();
        $this->reports_gateway = new ReportsGateway($this->db);
    }

    /**
     * @param $ownerId
     * @return mixed
     */
    public function getReportsByOwner($ownerId, $sorting)
    {
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        $rows = $this->reports_gateway->getAllByOwner($ownerId, $sorting);
        return $rows;
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function getReportsById($reportId)
    {
        $report = $this->reports_gateway->getReportById($reportId);
        return $report;
    }

}