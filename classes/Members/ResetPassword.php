<?php


namespace phpCollab\Members;

use Exception;
use phpCollab\Notification;
use phpCollab\Util;

class ResetPassword extends Members
{
    private $userDetails;
    private $newPassword;

    public function reset($username)
    {
        $this->userDetails = $this->getMemberByLogin($username);

        if ($this->userDetails && $this->userDetails["mem_email_work"] != "") {
            $this->newPassword = Util::getPassword(Util::passwordGenerator());

            if (!empty($this->newPassword)) {
                try {
                    $this->setPassword($this->userDetails['mem_id'], $this->newPassword);

                    $this->sendEmailNotification();

                } catch (Exception $exception) {
                    error_log('Reset Password error: ' . $exception->getMessage());
                }

            }
        }
    }

    private function sendEmailNotification()
    {
        try {
            $mail = new Notification(true);

            $body = <<<BODY
{$this->strings["user_name"]} : {$this->userDetails["mem_login"]}

{$this->strings["password"]} : {$this->newPassword}
BODY;

            $mail->getUserinfo("1", "from");

            $subject = $GLOBALS["setTitle"] . " " . $this->strings["password"];

            $mail->Subject = $subject;
            $mail->Priority = "1";
            $mail->Body = $body;
            $mail->AddAddress($this->userDetails["mem_email_work"], $this->userDetails["mem_name"]);
            $mail->Send();
            $mail->ClearAddresses();
        } catch (Exception $exception) {
            error_log('Error sending password email: ' . $exception->getMessage());
        }
    }
}
