<?php

namespace phpCollab;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use phpCollab\Members\Members;

class Notification extends phpmailer
{
    private $lang;
    private $notificationMethod;
    private $strings;
    private $root;
    public $partMessage;
    public $footer;
    public $partSubject;

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);
        $this->lang = $GLOBALS["lang"];
        $this->notificationMethod = $GLOBALS["notificationMethod"];
        $this->strings = $GLOBALS["strings"];
        $this->root = $GLOBALS["root"];

        $this->Mailer = $this->notificationMethod;
        $this->SetLanguage($this->lang);

        if ($this->Mailer == "smtp") {
            $this->isSMTP();
            $this->Host = SMTPSERVER;
            $this->Port = 25;                                   // TCP port to connect to
            $this->SMTPAuth = false;
            if (!empty(SMTPLOGIN && !empty(SMTPPASSWORD))) {
                $this->SMTPAuth = true;
                $this->Username = SMTPLOGIN;                    // SMTP username
                $this->Password = SMTPPASSWORD;                 // SMTP password
//                $this->SMTPSecure = 'tls';                      // Enable TLS encryption, `ssl` also accepted
            }
        }

        $this->footer = "--\n" . $this->strings["noti_foot1"] . "\n\n" . $this->strings["noti_foot2"] . "\n$this->root/";

    }

    function getUserinfo($idUser, $type)
    {
        $detailUser = (new Members)->getMemberById($idUser);
        try {
            if ($type == "from") {
                $this->setFrom($detailUser["mem_email_work"], $detailUser["mem_name"]);
            }

            if ($type == "to") {
                $this->AddAddress(
                    $detailUser["mem_email_work"], $detailUser["mem_name"]
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
