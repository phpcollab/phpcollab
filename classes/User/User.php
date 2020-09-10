<?php


namespace phpCollab;


class User
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function login()
    {
        echo "login user";
    }

    public function logout()
    {
        echo "logout user";
    }

}