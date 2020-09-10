<?php


namespace phpCollab\Members;


class MemberModel
{
/**
`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
`organization` mediumint(8) unsigned NOT NULL DEFAULT '0',
`login` varchar(155) DEFAULT NULL,
`password` varchar(155) DEFAULT NULL,
`name` varchar(155) DEFAULT NULL,
`title` varchar(155) DEFAULT NULL,
`email_work` varchar(155) DEFAULT NULL,
`email_home` varchar(155) DEFAULT NULL,
`phone_work` varchar(155) DEFAULT NULL,
`phone_home` varchar(155) DEFAULT NULL,
`mobile` varchar(155) DEFAULT NULL,
`fax` varchar(155) DEFAULT NULL,
`comments` text,
`profil` char(1) NOT NULL DEFAULT '',
`created` varchar(16) DEFAULT NULL,
`logout_time` mediumint(8) unsigned NOT NULL DEFAULT '0',
`last_page` varchar(255) DEFAULT NULL,
`timezone` char(3) NOT NULL DEFAULT '',
 */
    private $id;
    private $organizationId;
    private $username;
    private $name;
    private $title;
    private $emailWork;
    private $emailHome;
    private $phoneWork;
    private $phoneHome;
    private $phoneMobile;
    private $phoneFax;
    private $comments;
    private $role; // profile
    private $created;
    private $modified;
    private $logoutTime;
    private $lastPageVisited;
    private $timezone;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getEmailWork()
    {
        return $this->emailWork;
    }

    /**
     * @return mixed
     */
    public function getEmailHome()
    {
        return $this->emailHome;
    }

    /**
     * @return mixed
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * @return mixed
     */
    public function getPhoneHome()
    {
        return $this->phoneHome;
    }

    /**
     * @return mixed
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * @return mixed
     */
    public function getPhoneFax()
    {
        return $this->phoneFax;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @return mixed
     */
    public function getLogoutTime()
    {
        return $this->logoutTime;
    }

    /**
     * @return mixed
     */
    public function getLastPageVisited()
    {
        return $this->lastPageVisited;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }


}