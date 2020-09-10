<?php


namespace phpCollab\Login\Reports;


class ExportReport extends Reports
{

    protected $projectSelection;
    protected $orgSelection;
    protected $assignedToSelection;
    protected $statusSelection;
    protected $prioritySelection;
    protected $startDateRange = []; // [start, end]
    protected $dueDateRange = []; // [start, end]
    protected $completedDateRange = [];
    protected $sql;

    /**
     * @return mixed
     */
    public function getProjectSelection()
    {
        return $this->projectSelection;
    }

    /**
     * @param mixed $projectSelection
     */
    public function setProjectSelection($projectSelection): void
    {
        $this->projectSelection = $projectSelection;

    }

    public function addProjects()
    {
        $this->sql .= "tas.project IN($this->projectSelection)";
    }

    /**
     * @return mixed
     */
    public function getOrgSelection()
    {
        return $this->orgSelection;
    }

    /**
     * @param mixed $orgSelection
     */
    public function setOrgSelection($orgSelection): void
    {
        $this->orgSelection = $orgSelection;
    }

    /**
     * @return mixed
     */
    public function getAssignedToSelection()
    {
        return $this->assignedToSelection;
    }

    /**
     * @param mixed $assignedToSelection
     */
    public function setAssignedToSelection($assignedToSelection): void
    {
        $this->assignedToSelection = $assignedToSelection;
    }

    /**
     * @return mixed
     */
    public function getStatusSelection()
    {
        return $this->statusSelection;
    }

    /**
     * @param mixed $statusSelection
     */
    public function setStatusSelection($statusSelection): void
    {
        $this->statusSelection = $statusSelection;
    }

    /**
     * @return mixed
     */
    public function getPrioritySelection()
    {
        return $this->prioritySelection;
    }

    /**
     * @param mixed $prioritySelection
     */
    public function setPrioritySelection($prioritySelection): void
    {
        $this->prioritySelection = $prioritySelection;
    }

    /**
     * @return array
     */
    public function getStartDateRange(): array
    {
        return $this->startDateRange;
    }

    /**
     * @param array $startDateRange
     */
    public function setStartDateRange(array $startDateRange): void
    {
        $this->startDateRange = $startDateRange;
    }

    /**
     * @return array
     */
    public function getDueDateRange(): array
    {
        return $this->dueDateRange;
    }

    /**
     * @param array $dueDateRange
     */
    public function setDueDateRange(array $dueDateRange): void
    {
        $this->dueDateRange = $dueDateRange;
    }

    /**
     * @return array
     */
    public function getCompletedDateRange(): array
    {
        return $this->completedDateRange;
    }

    /**
     * @param array $completedDateRange
     */
    public function setCompletedDateRange(array $completedDateRange): void
    {
        $this->completedDateRange = $completedDateRange;
    } // [start, end]

    public function outputReport()
    {
        xdebug_var_dump($this->sql);
    }
}