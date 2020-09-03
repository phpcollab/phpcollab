<?php


namespace phpCollab\Files;

use phpCollab\Database;

/**
 * Class FilesGateway
 * @package phpCollab\Files
 */
class FilesGateway
{
    protected $db;
    protected $initrequest;

    /**
     * FilesGateway constructor.
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->initrequest = $GLOBALS['initrequest'];

    }

    /**
     * @param $projectId
     * @param $taskId
     * @return mixed
     */
    public function setProjectByTaskId($projectId, $taskId)
    {
        $sql = "UPDATE {$this->db->getTableName("files")} SET project = :project_id WHERE task = :task_id";
        $this->db->query($sql);
        $this->db->bind(":project_id", $projectId);
        $this->db->bind(":task_id", $taskId);
        return $this->db->execute();
    }

    /**
     * @param $fileId
     * @param $phase
     * @return mixed
     */
    public function setPhase($fileId, $phase)
    {
        $sql = "UPDATE {$this->db->getTableName("files")} SET phase = :phase WHERE id = :file_id";
        $this->db->query($sql);
        $this->db->bind(":file_id", $fileId);
        $this->db->bind(":phase", $phase);
        return $this->db->execute();
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFiles($fileId)
    {
        if (strpos($fileId, ',')) {
            $ids = explode(',', $fileId);
            $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
            $sql = $this->initrequest["files"] . " WHERE fil.id IN ($placeholders) OR vc_parent IN ($placeholders)";
            $this->db->query($sql);

            $this->db->execute(array_merge($ids, $ids));

            return $this->db->fetchAll();
        } else {
            $query = $this->initrequest["files"] . " WHERE fil.id IN(:file_id) OR fil.vc_parent IN(:file_id) ORDER BY fil.name";

            $this->db->query($query);

            $this->db->bind(':file_id', $fileId);

            return $this->db->resultset();
        }
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function getFileById($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id = :file_id";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->single();
    }

    /**
     * @param $taskId
     * @param null $sorting
     * @return mixed
     */
    public function getFilesByTaskIdAndVCParentEqualsZero($taskId, $sorting = null)
    {
        $query = $this->initrequest["files"] . " WHERE fil.task = :task_id AND fil.vc_parent = 0" . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':task_id', $taskId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getFilesByProjectIdAndPhaseNotEqualZero($projectId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.project = :project_id AND fil.phase !='0'";
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        return $this->db->resultset();
    }

    /**
     * @param $projectId
     * @param $phaseId
     * @param null $sorting
     * @return mixed
     */
    public function getFilesByProjectAndPhaseWithoutTasksAndParent($projectId, $phaseId, $sorting = null)
    {
        $whereStatement = " WHERE fil.project = :project_id AND fil.phase = :phase_id AND fil.task = 0 AND fil.vc_parent = 0";
        $query = $this->initrequest["files"] . $whereStatement . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':phase_id', $phaseId);
        return $this->db->resultset();

    }

    /**
     * @return mixed
     */
    public function getPublishedFiles()
    {
        $query = $this->initrequest["files"] . " WHERE fil.published = 0";
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @return mixed
     */
    public function getUnPublishedFiles()
    {
        $query = $this->initrequest["files"] . " WHERE fil.published = 1";
        $this->db->query($query);
        return $this->db->resultset();
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function deleteFiles($fileId)
    {

        if (strpos($fileId, ',')) {
            $ids = explode(',', $fileId);
            $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
            $sql = "DELETE FROM {$this->db->getTableName("files")} WHERE id IN ($placeholders) OR vc_parent IN($placeholders)";
            $this->db->query($sql);

            $this->db->execute(array_merge($ids, $ids));

            return $this->db->fetchAll();
        } else {
            $query = "DELETE FROM {$this->db->getTableName("files")} WHERE id IN (:file_id) OR vc_parent IN(:file_id)";

            $this->db->query($query);

            $this->db->bind(':file_id', $fileId);

            return $this->db->execute();
        }
    }

    /**
     * @param $projectIds
     * @return mixed
     */
    public function deleteFilesByProjectId($projectIds)
    {
        $projectId = explode(',', $projectIds);
        $placeholders = str_repeat('?, ', count($projectId) - 1) . '?';
        $sql = "DELETE FROM {$this->db->getTableName("files")} WHERE project IN ($placeholders)";
        $this->db->query($sql);
        return $this->db->execute($projectId);
    }


    /**
     * @param $fileId
     * @param $fileStatus
     * @param null $sorting
     * @return mixed
     */
    public function getFileVersions($fileId, $fileStatus, $sorting = null)
    {
        if (is_null($sorting)) {
            $sorting = 'fil.date DESC';
        }
        $query = $this->initrequest["files"] . " WHERE fil.id = :file_id OR fil.vc_parent = :file_id AND fil.vc_status = :file_status";
        $this->db->query($query . $this->orderBy($sorting));
        $this->db->bind(':file_id', $fileId);
        $this->db->bind(':file_status', $fileStatus);

        return $this->db->resultset();
    }

    /**
     * @param $fileId
     * @param null $sorting
     * @return mixed
     */
    public function getFilePeerReviews($fileId, $sorting = null)
    {
        if (is_null($sorting)) {
            $sorting = 'fil.date';
        }
        $query = $this->initrequest["files"] . " WHERE fil.vc_parent = :file_id AND fil.vc_status != 3 " . $this->orderBy($sorting);
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        return $this->db->resultset();
    }

    /**
     * @param $fileId
     * @param $approverId
     * @param $comment
     * @param $approvalDate
     * @param $status
     * @return mixed
     */
    public function updateApproval($fileId, $approverId, $comment, $approvalDate, $status)
    {
        $query = "UPDATE {$this->db->getTableName("files")} SET comments_approval = :comments_approval, date_approval = :date_approval, approver = :approver, status=:status WHERE id = :file_id";
        $this->db->query($query);
        $this->db->bind(":comments_approval", $comment);
        $this->db->bind(":approver", $approverId);
        $this->db->bind(":status", $status);
        $this->db->bind(":date_approval", $approvalDate);
        $this->db->bind(":file_id", $fileId);
        return $this->db->execute();
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function publishFile($fileId)
    {
        $query = "UPDATE {$this->db->getTableName("files")} SET published = 1 WHERE id = :file OR vc_parent = :file";
        $this->db->query($query);
        $this->db->bind(":file", $fileId);
        return $this->db->execute();
    }

    /**
     * @param $fileId
     * @return mixed
     */
    public function unPublishFile($fileId)
    {
        $query = "UPDATE {$this->db->getTableName("files")} SET published = 0 WHERE id = :file OR vc_parent = :file";
        $this->db->query($query);
        $this->db->bind(":file", $fileId);
        return $this->db->execute();
    }

    /**
     * @param $fileIds
     * @return mixed
     */
    public function publishFiles($fileIds)
    {
        if (strpos($fileIds, ',')) {
            $fileIds = explode(',', $fileIds);
            $placeholders = str_repeat('?, ', count($fileIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("files")} SET published = 0 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($fileIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("files")} SET published = 0 WHERE id = :topic_ids";
            $this->db->query($sql);
            $this->db->bind(':topic_ids', $fileIds);
            return $this->db->execute();
        }
    }

    /**
     * @param $fileIds
     * @return mixed
     */
    public function publishFilesByIdOrInVcParent($fileIds)
    {
        $fileIds = explode(',', $fileIds);
        $placeholders = str_repeat('?, ', count($fileIds) - 1) . '?';
        $placeholders2 = $placeholders;
        $sql = "UPDATE {$this->db->getTableName("files")} SET published = 1 WHERE id IN ($placeholders) OR vc_parent IN ($placeholders2)";
        $this->db->query($sql);
        return $this->db->execute([$fileIds, $fileIds]);
    }

    /**
     * @param $fileIds
     * @return mixed
     */
    public function unPublishFilesByIdOrInVcParent($fileIds)
    {
        $fileIds = explode(',', $fileIds);
        $placeholders = str_repeat('?, ', count($fileIds) - 1) . '?';
        $placeholders2 = $placeholders;
        $sql = "UPDATE {$this->db->getTableName("files")} SET published = 0 WHERE id IN ($placeholders) OR vc_parent IN ($placeholders2)";
        $this->db->query($sql);
        return $this->db->execute([$fileIds, $fileIds]);
    }

    /**
     * @param $fileIds
     * @return mixed
     */
    public function unPublishFiles($fileIds)
    {
        if (strpos($fileIds, ',')) {
            $fileIds = explode(',', $fileIds);
            $placeholders = str_repeat('?, ', count($fileIds) - 1) . '?';
            $sql = "UPDATE {$this->db->getTableName("files")} SET published = 1 WHERE id IN ($placeholders)";
            $this->db->query($sql);
            return $this->db->execute($fileIds);
        } else {
            $sql = "UPDATE {$this->db->getTableName("files")} SET published = 1 WHERE id = :topic_ids";
            $this->db->query($sql);
            $this->db->bind(':topic_ids', $fileIds);
            return $this->db->execute();
        }
    }

    /**
     * @param $owner
     * @param $project
     * @param $phase
     * @param $task
     * @param $comments
     * @param $status
     * @param $vcVersion
     * @param $vcParent
     * @return mixed
     */
    public function addFile($owner, $project, $phase, $task, $comments, $status, $vcVersion, $vcParent)
    {
        $query = <<<SQL
INSERT INTO {$this->db->getTableName("files")} 
(owner, project, phase, task, comments, upload, published, status, vc_version, vc_parent) 
VALUES 
(:owner, :project, :phase, :task, :comments, :upload_date, :published, :status, :vc_version, :vc_parent)
SQL;
        $this->db->query($query);
        $this->db->bind(":owner", $owner);
        $this->db->bind(":project", $project);
        $this->db->bind(":phase", $phase);
        $this->db->bind(":task", $task);
        $this->db->bind(":comments", $comments);
        $this->db->bind(":upload_date", date('Y-m-d h:i'));
        $this->db->bind(":published", 1);
        $this->db->bind(":status", $status);
        $this->db->bind(":vc_version", $vcVersion);
        $this->db->bind(":vc_parent", is_null($vcParent) ? 0 : $vcParent);
        $this->db->execute();
        return $this->db->lastInsertId();

    }

    /**
     * @param $fileId
     * @param $name
     * @param $date
     * @param $size
     * @param $extension
     * @param $vc_version
     * @return mixed
     */
    public function updateFile($fileId, $name, $date, $size, $extension, $vc_version)
    {
        $query = "UPDATE {$this->db->getTableName("files")} SET name = :name, date = :date, size = :size, extension = :extension";
        if (!is_null($vc_version)) {
            $query .= ", vc_version = :vc_version";
        }
        $query .= " WHERE id  = :file_id";

        $this->db->query($query);
        $this->db->bind(":file_id", $fileId);
        $this->db->bind(":name", $name);
        $this->db->bind(":date", $date);
        $this->db->bind(":size", $size);
        $this->db->bind(":extension", $extension);
        if (!is_null($vc_version)) {
            $this->db->bind(":vc_version", $vc_version);
        }
        return $this->db->execute();

    }

    /*
     * Project Site Related Methods
     */

    /**
     * @param $projectId
     * @param null $sorting
     * @return mixed
     */
    public function getProjectSiteFiles($projectId, $sorting = null)
    {
        $query = $this->initrequest["files"] . " WHERE fil.project = :project_id AND fil.published = 0 AND fil.vc_parent = 0";

        $this->db->query($query . $this->orderBy($sorting));
        $this->db->bind(":project_id", $projectId);
        return $this->db->resultset();
    }


    /**
     * @param string $sorting
     * @return string
     */
    private function orderBy($sorting)
    {
        if (!is_null($sorting)) {
            $allowedOrderedBy = [
                "fil.type",
                "fil.name",
                "fil.owner",
                "fil.date",
                "fil.approval_tracking",
                "fil.published"
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
