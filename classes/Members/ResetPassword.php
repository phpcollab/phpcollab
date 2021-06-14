<?php


namespace phpCollab\Members;

use DateTime;
use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;
use phpCollab\Exceptions\SendNotificationFailException;
use phpCollab\Exceptions\TokenExpiredException;
use phpCollab\Exceptions\TokenGenerationFailedException;
use phpCollab\Util;
use Symfony\Component\HttpFoundation\Request;

class ResetPassword extends Members
{
    private $userDetails;
    private $token;

    /**
     * ResetPassword constructor.
     * @param Database $database
     * @param Logger $logger
     * @param Container $container
     */
    public function __construct(Database $database, Logger $logger, Container $container)
    {
        parent::__construct($database, $logger, $container);
    }

    /**
     * @param $memberId
     * @return mixed
     * @throws TokenGenerationFailedException
     */
    private function generateToken($memberId)
    {
        $this->logger->notice('Reset Password', ['Method' => 'generateToken', 'memberId' => $memberId]);
        try {
            $this->token = bin2hex(random_bytes(32));
            $date = new DateTime();

            $sql = <<<SQL
UPDATE {$this->db->getTableName("members")} 
SET 
email_home = :token
WHERE id = :member_id
SQL;
            $this->db->query($sql);
            $this->db->bind(":token", $this->token . '|' . $date->getTimestamp());
            $this->db->bind(":member_id", $memberId);
            return $this->db->execute();

        } catch (Exception $exception) {
            $this->logger->error('Generate Token', ['Exception' => $exception->getMessage()]);
            throw new TokenGenerationFailedException();
        }
    }

    /**
     * @param int $memberId
     * @param string $password
     * @throws Exception
     */
    private function resetPassword(int $memberId, string $password)
    {
        $this->logger->notice('Reset Password', ['Method' => 'resetPassword']);
        try {
            if (!isset($memberId) || !isset($password)) {
                throw new InvalidArgumentException('Invalid member id or password');
            } else {
                $memberId = filter_var($memberId, FILTER_VALIDATE_INT);
                $encryptedPassword = Util::getPassword($password);

                $sql = <<<SQL
UPDATE {$this->db->getTableName("members")} 
SET 
password = :password
WHERE id = :member_id
SQL;
                $this->db->query($sql);
                $this->db->bind(":member_id", $memberId);
                $this->db->bind(":password", $encryptedPassword);

                if ($this->db->execute()) {
                    $this->sendSuccessResetEmailNotification();
                }
            }
        } catch (Exception $exception) {
            error_log('Reset Password error: ' . $exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @throws TokenExpiredException
     */
    public function validate(Request $request)
    {
        $this->logger->notice('Reset Password', ['Method' => 'validate']);
        try {
            if (!empty($request) && !empty($request->request->get("token")) && !empty($request->request->get("password")) && !empty($request->request->get("passwordConfirm"))) {

                $sql = <<<SQL
SELECT id, name, login, email_work, email_home as token FROM {$this->db->getTableName("members")} WHERE email_home LIKE :token
SQL;
                $this->db->query($sql);
                $this->db->bind(":token", $request->request->get("token") . '%');
                $this->db->execute();
                $this->userDetails = $this->db->single();

                // If the token is valid, i.e. it matches a record in the DB, then proceed with resetting the password.
                if ($this->userDetails) {

                    $timestamp = explode( '|', $this->userDetails["token"] )[1];

                    if ($this->validateTimestamp($timestamp)) {
                        // Might need to refactor the below code to put into another method.
                        $this->resetPassword($this->userDetails["id"], $request->request->get("password"));
                    } else {
                        throw new TokenExpiredException();
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logger->error('Password reset token validation', [
                'Exception' => $exception->getMessage(),
                'token' => $request->request->get("token")
            ]);
            throw new TokenExpiredException("Token expired");
        }
    }

    /**
     * @param $timestamp
     * @param int $offset
     * @return bool
     */
    private function validateTimestamp($timestamp, $offset = 24)
    {
        $this->logger->notice('Reset Password', ['Method' => 'validateTimestamp']);
        $tokenCreateTime = new DateTime();
        $tokenCreateTime->setTimestamp($timestamp);

        $now = new DateTime('now');

        $interval = date_diff($tokenCreateTime, $now);

        return ($interval->i < $offset);
    }

    /**
     *
     */
    private function sendTokenEmail()
    {
        $this->logger->notice('Reset Password', ['Method' => 'sendTokenEmail']);
        try {
            // Read the email template
            $template = file_get_contents( APP_ROOT . '/templates/email/reset_password_link.html');

            // Replace the % with the actual information
            $template = str_replace('%name%', $this->userDetails["mem_name"], $template);
            $template = str_replace('%email%', $this->userDetails["mem_email_work"], $template);
            $template = str_replace('%username%', $this->userDetails["mem_login"], $template);
            $template = str_replace('%site_name%', $GLOBALS["setTitle"], $template);
            $template = str_replace('%link%', $GLOBALS["root"] . '/general/resetpassword.php?token=' . $this->token, $template);

            $subject = $GLOBALS["setTitle"] . " " . $this->strings["email_forgot_pwd_subject"];

            $this->sendNotification($template, $subject);
        } catch (Exception $exception) {
            error_log('Error sending password email: ' . $exception->getMessage());
            $this->logger->error('Forgotten Password Link Notification', ['userDetails' => $this->userDetails, 'Exception' => $exception->getMessage()]);
        }
    }

    /**
     * @param $username
     */
    public function reset($username)
    {
    }

    /**
     * @param $username
     * @throws TokenGenerationFailedException
     */
    public function forgotPassword($username)
    {
        $this->logger->notice('Reset Password', ['Method' => 'forgotPassword']);
        $this->userDetails = $this->getMemberByLogin($username);

        if ($this->userDetails && $this->userDetails["mem_email_work"] != "") {
            // Generate a token
            $this->logger->info('Reset Password', ['Method' => 'forgotPassword', 'Call' => 'generateToken']);
            if ($this->generateToken($this->userDetails["mem_id"]) && !empty($this->token)) {
                $this->logger->info('Reset Password - call sendTokenEmail');
                $this->sendTokenEmail();
            }
        } else {
            $this->logger->warning('Reset Password', ['Method' => 'forgotPassword', 'Member not found for username:' => $username]);
        }
    }

    /**
     *
     * @throws Exception
     */
    private function sendSuccessResetEmailNotification()
    {
        $this->logger->notice('Reset Password', ['Method' => 'sendSuccessResetEmailNotification']);

        if (!$this->userDetails) {
            $this->logger->error('Reset Password', ['Method' => 'sendSuccessResetEmailNotification']);
            throw new Exception('User details not found. Unable to send email.');
        }

        try {
            // Read the email template
            $template = file_get_contents( APP_ROOT . '/templates/email/forgot_password_success.html');

            // Replace the %xx% with the actual data
            $template = str_replace('%name%', $this->userDetails["name"], $template);
            $template = str_replace('%site_name%', $GLOBALS["setTitle"], $template);

            $subject = sprintf($this->strings["password_reset_confirmation_subject"], $this->userDetails["name"]);

            $this->sendNotification($template, $subject);
        } catch (Exception $exception) {
            $this->logger->error('Password Reset Successful Notification', ['userDetails' => $this->userDetails, 'Exception' => $exception->getMessage()]);
            throw new SendNotificationFailException();
        }
    }

    /**
     * @param $template
     * @param $subject
     * @throws SendNotificationFailException
     */
    private function sendNotification($template, $subject)
    {
        try {
            $mail = $this->container->getNotification();
            $mail->getUserinfo("1", "from", $this->logger);
            $mail->Subject = $subject;
            $mail->Priority = "1";
            $mail->Body = $template;
            $mail->AddAddress($this->userDetails["email_work"], $this->userDetails["name"]);
            $mail->Send();
            $mail->ClearAddresses();
            unset($mail);
        } catch (Exception $exception) {
            throw new SendNotificationFailException();
        }
    }
}
