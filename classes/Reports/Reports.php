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
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->reports_gateway = new ReportsGateway($this->db);
    }

    /**
     * @param $owner
     * @param $name
     * @param $projects
     * @param $clients
     * @param $members
     * @param $priorities
     * @param $status
     * @param $dateDueStart
     * @param $dateDueEnd
     * @param $dateCompleteStart
     * @param $dateCompleteEnd
     * @return mixed
     */
    public function addReport(
        $owner,
        $name,
        $projects,
        $clients,
        $members,
        $priorities,
        $status,
        $dateDueStart,
        $dateDueEnd,
        $dateCompleteStart,
        $dateCompleteEnd
    ) {
        $newReportId = $this->reports_gateway->addReport(
            $owner, $name, $projects, $clients, $members, $priorities, $status, $dateDueStart, $dateDueEnd,
            $dateCompleteStart, $dateCompleteEnd, date('Y-m-d h:i')
        );
        return $this->getReportsById($newReportId);
    }

    /**
     * @param $ownerId
     * @param $sorting
     * @return mixed
     */
    public function getReportsByOwner($ownerId, $sorting)
    {
        $sorting = filter_var($sorting, FILTER_SANITIZE_STRING);
        return $this->reports_gateway->getAllByOwner($ownerId, $sorting);
    }

    /**
     * @param $reportId
     * @return mixed
     */
    public function getReportsById($reportId)
    {
        return $this->reports_gateway->getReportById($reportId);
    }

    /**
     * @param mixed $reportIds
     * @param string $sorting
     * @return mixed
     */
    public function getReportsByIds($reportIds, $sorting = null)
    {
        return $this->reports_gateway->getReportsByIds($reportIds, $sorting);
    }

    /**
     * @param $reportIds
     * @return mixed
     */
    public function deleteReports($reportIds)
    {
        $reportIds = filter_var($reportIds, FILTER_SANITIZE_STRING);
        return $this->reports_gateway->deleteReports($reportIds);
    }

}
