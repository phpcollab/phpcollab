<?php


namespace phpCollab\Login\Files;


use Exception;
use phpCollab\Login\Database;

class File
{

    /**
     * WIP: what do I want to do with this?  Should the file object only return the details of the file, and nothing
     * else or should it return additional information?  Currently the initrequest files AQL returns additional data
     * using joins.
     *
     * What is the "proper" way of handling OOP like this?  Seems OOP examples always use a car analogy.
     *
     *
     * Fields returned from initrequest["file"]
     * 'fil_id' => string '1' (length=1)
     * 'fil_owner' => string '3' (length=1)
     * 'fil_project' => string '1' (length=1)
     * 'fil_task' => string '0' (length=1)
     * 'fil_name' => string '1--phpcollab_README.zip' (length=23)
     * 'fil_date' => string '2019-04-16 04:10' (length=16)
     * 'fil_size' => string '1132' (length=4)
     * 'fil_extension' => string 'zip' (length=3)
     * 'fil_comments' => string 'Testing file_info_type refactor' (length=31)
     * 'fil_comments_approval' => null
     * 'fil_approver' => string '0' (length=1)
     * 'fil_date_approval' => null
     * 'fil_upload' => string '2015-08-11 23:06' (length=16)
     * 'fil_published' => string '0' (length=1)
     * 'fil_status' => string '0' (length=1)
     * 'fil_vc_status' => string '0' (length=1)
     * 'fil_vc_version' => string '0.0' (length=3)
     * 'fil_vc_parent' => string '0' (length=1)
     * 'fil_phase' => string '0' (length=1)
     * Note the extra fields:
     * 'fil_mem_id' => string '3' (length=1)
     * 'fil_mem_login' => string 'jsittler' (length=8)
     * 'fil_mem_name' => string 'Jeff' (length=4)
     * 'fil_mem_email_work' => string 'jsittler@mindblender.com' (length=24)
     * 'fil_mem2_id' => null
     * 'fil_mem2_login' => null
     * 'fil_mem2_name' => null
     * 'fil_mem2_email_work' => null
     *
     *
     *
     */


    /**
     * File properties
     */

    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $owner;
    /**
     * @var int
     */
    private $project;
    /**
     * @var int
     */
    private $task;
    /**
     * @var string
     */
    private $name;
    private $date;
    /**
     * @var int
     */
    private $size;
    /**
     * @var string
     */
    private $extension;
    /**
     * @var string
     */
    private $comments;
    /**
     * @var int
     */
    private $approver;
    /**
     * @var string
     */
    private $approvalComments;
    private $approvalDate;
    private $upload;
    /**
     * @var boolean
     */
    private $published;
    /**
     * @var int
     */
    private $status;
    /**
     * @var int
     */
    private $vc_status;
    /**
     * @var double | null
     */
    private $vc_version;
    /**
     * @var int
     */
    private $vc_parent;
    /**
     * @var int
     */
    private $phase;

    /*
     * If an ID is passed to the File object when instantiated, then we retrieve the details for that file.
     *
     * If no ID is passed, then we are creating a blank file object to be used for adding a file.
     */

    public function __construct(int $fileId = null)
    {
        /* Anything needed for the __construct? */
    }

    private function getFile($fileId)
    {
        $query = $this->initrequest["files"] . " WHERE fil.id = :file_id";
        $this->db->query($query);
        $this->db->bind(':file_id', $fileId);
        $fileDetails = $this->db->single();

        /*
         * If file exists, then set the properties
         *

         *
         *
         */

        if ($fileDetails) {
            foreach ($fileDetails as $key => $value) {
//                xdebug_var_dump(substr($key, 4));
                $key = substr($key, 4);
                $this->$key = $value;
            }
        } else {
            return new Exception('File not found');
        }


//        xdebug_var_dump($file);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project): void
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task): void
    {
        $this->task = $task;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size): void
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension): void
    {
        $this->extension = $extension;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getApprover()
    {
        return $this->approver;
    }

    /**
     * @param mixed $approver
     */
    public function setApprover($approver): void
    {
        $this->approver = $approver;
    }

    /**
     * @return mixed
     */
    public function getApprovalComments()
    {
        return $this->approvalComments;
    }

    /**
     * @param mixed $approvalComments
     */
    public function setApprovalComments($approvalComments): void
    {
        $this->approvalComments = $approvalComments;
    }

    /**
     * @return mixed
     */
    public function getApprovalDate()
    {
        return $this->approvalDate;
    }

    /**
     * @param mixed $approvalDate
     */
    public function setApprovalDate($approvalDate): void
    {
        $this->approvalDate = $approvalDate;
    }

    /**
     * @return mixed
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * @param mixed $upload
     */
    public function setUpload($upload): void
    {
        $this->upload = $upload;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published): void
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getVcStatus()
    {
        return $this->vc_status;
    }

    /**
     * @param mixed $vc_status
     */
    public function setVcStatus($vc_status): void
    {
        $this->vc_status = $vc_status;
    }

    /**
     * @return mixed
     */
    public function getVcVersion()
    {
        return $this->vc_version;
    }

    /**
     * @param mixed $vc_version
     */
    public function setVcVersion($vc_version): void
    {
        $this->vc_version = $vc_version;
    }

    /**
     * @return mixed
     */
    public function getVcParent()
    {
        return $this->vc_parent;
    }

    /**
     * @param mixed $vc_parent
     */
    public function setVcParent($vc_parent): void
    {
        $this->vc_parent = $vc_parent;
    }

    /**
     * @return mixed
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * @param mixed $phase
     */
    public function setPhase($phase): void
    {
        $this->phase = $phase;
    }

}