<?php


namespace phpCollab\Members;

use DateTime;
use Exception;
use InvalidArgumentException;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Database;
use phpCollab\Exceptions\MissingOrInvalidEmailAddress;
use phpCollab\Exceptions\SendNotificationFailException;
use phpCollab\Exceptions\TimestampInvalidException;
use phpCollab\Exceptions\TooManyPasswordResetAttempts;
use phpCollab\Exceptions\UserNotFoundException;
use phpCollab\Exceptions\TokenNotExpiredException;
use phpCollab\Util;
use Symfony\Component\HttpFoundation\Request;

class ResetPassword extends Members
{
    private ?array $userDetails;
    private ?string $token;
    private ?DateTime $timestamp;

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
     * @param string $username
     * @param array $times
     * @return bool
     * @throws TooManyPasswordResetAttempts
     * @throws Exception
     */
    public function forgotPassword(string $username, array $times): bool
    {
        $this->logger->notice('Reset Password', ['Method' => 'forgotPassword']);

        if (empty($username)) {
            throw new InvalidArgumentException('Username is invalid');
        }

        try {
            // Retrieve the member record by their username
            $this->userDetails = $this->getByLogin($username);

            if ( empty($this->userDetails) ) {
                $this->logger->warning('Reset Password',
                    ['Method' => 'forgotPassword', 'Member not found for username:' => $username]);
                throw new UserNotFoundException();
            }

            // If there is no email, or it is invalid, then no need to proceed since we have no way of contacting the user.
            if (
                empty($this->userDetails["email_work"])
                || !filter_var($this->userDetails["email_work"], FILTER_VALIDATE_EMAIL))
            {
                throw new MissingOrInvalidEmailAddress();
            }

            // If there is a token, then populate the token and timestamp
            if ($this->userDetails["token"]) {
                $this->token = $this->populateToken();
                $this->timestamp = $this->populateTimestamp();
            }

            /*
             * Check to see if there is a token, if so, then we need to validate the timestamp from the
             * previous token to see if it has expired or not.  If it has expired, then generate a new token.
             * If it has not expired, then display message saying an email has already been sent and to check their
             * mailbox
             */
            if ( empty($this->token) || $this->isTimestampExpired($this->timestamp, $times['tokenLifespan'])) {
                // Generate a token
                $this->logger->info('Reset Password', [
                    'user' => [
                        'userId' => $this->userDetails["id"],
                        'userName' => $this->userDetails["login"],
                    ],
                    'Method' => 'forgotPassword',
                    'Caller' => 'generateToken'
                ]);

                if ($this->generateToken($this->userDetails["id"]) && !empty($this->token)) {
                    $this->logger->info('Reset Password - call sendTokenEmail');
                    $this->sendTokenEmail();
                }

                return true;
            }

            throw new TooManyPasswordResetAttempts();

        } catch (TooManyPasswordResetAttempts $tooManyPasswordResetAttempts) {
            throw $tooManyPasswordResetAttempts;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param DateTime $date
     * @param int $duration
     * @return bool
     * @throws TimestampInvalidException
     * @throws Exception
     */
    public function checkTimestamp(DateTime $date, int $duration = 15): bool
    {
        if (empty($date)) {
            throw new TimestampInvalidException();
        }

        try {
            return $this->isTimestampExpired($date, $duration);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * This method takes in the Request object and uses the token to retrieve the user information
     * @param Request $request
     * @throws TooManyPasswordResetAttempts
     */
    public function validate(Request $request)
    {
        $this->logger->notice('Reset Password', ['Method' => 'validate']);

        try {
            if (
                empty($request)
                || (
                    empty($request->request->get("token"))
                    || empty($request->request->get("password"))
                    || empty($request->request->get("passwordConfirm"))
                )
            ) {
                throw new InvalidArgumentException('missing token, password, or passwordConfirm');
            }

            $this->userDetails = $this->getByToken($request->request->get("token"));

            $this->logger->info('Reset Password', [
                'user' => [
                    'userId' => $this->userDetails["id"],
                    'userName' => $this->userDetails["login"],
                ],
                'Method' => 'validate',
            ]);

            if (
                !empty($this->userDetails["email_work"])
                && !filter_var($this->userDetails["email_work"], FILTER_VALIDATE_EMAIL))
            {
                throw new MissingOrInvalidEmailAddress();
            }

            if ($this->userDetails["token"]) {
                $this->token = $this->populateToken();
                $this->timestamp = $this->populateTimestamp();

                if (!$this->isTimestampExpired($this->timestamp)) {
                    $this->resetPassword($this->userDetails["id"], $request->request->get("password"));
                } else {
                    throw new TooManyPasswordResetAttempts();
                }
            }
        } catch (Exception $exception) {
            $this->logger->error('Password reset token validation', [
                'Exception' => $exception->getMessage(),
                'token' => $request->request->get("token")
            ]);
            throw new TooManyPasswordResetAttempts("Token expired");
        }
    }

    /**
     * @param int $memberId
     * @return mixed
     * @throws UserNotFoundException
     */
    private function generateToken(int $memberId)
    {
        if (empty($memberId)) {
            throw new InvalidArgumentException('Member ID is invalid');
        }

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
            throw new UserNotFoundException();
        }
    }

    /**TokenAlreadySentException
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
password = :password,
email_home = null
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
     * @param string $token
     * @return false|mixed
     * @throws TokenNotExpiredException
     */
    private function getByToken(string $token)
    {
        try {
            $sql = <<<SQL
SELECT id, name, login, email_work, email_home as token FROM {$this->db->getTableName("members")} WHERE email_home LIKE :token
SQL;
            $this->db->query($sql);
            $this->db->bind(":token", $token . '%');
            $this->db->execute();
            $userDetails = $this->db->single();

            if ($userDetails && $userDetails["token"]) {
                return $userDetails;
            }
            return false;
        } catch (Exception $exception) {
            $this->logger->error('Unable to retrieve information by login', [
                'Exception' => $exception->getMessage(),
                'token' => $token
            ]);
            throw new TokenNotExpiredException("Token invalid");
        }
    }

    /**
     * @param string $login
     * @return mixed|void
     * @throws TokenNotExpiredException
     */
    private function getByLogin(string $login)
    {
        try {
            $sql = <<<SQL
SELECT id, name, login, email_work, email_home as token FROM {$this->db->getTableName("members")} WHERE login = :login
SQL;
            $this->db->query($sql);
            $this->db->bind(":login", $login);
            $this->db->execute();
            $userDetails = $this->db->single();

            if ($userDetails) {
                return $userDetails;
            }
        } catch (Exception $exception) {
            $this->logger->error('Unable to retrieve information by login', [
                'Exception' => $exception->getMessage(),
                'login' => $login
            ]);
            throw new TokenNotExpiredException("User login invalid");
        }
    }

    /**
     * @return false|mixed|string
     */
    private function populateToken()
    {
        // Check to see if there is an existing token, if so then populate the token property
        if ($this->userDetails["token"]) {
            return explode('|', $this->userDetails["token"])[0];
        }
        return false;
    }

    /**
     * @return DateTime|false
     */
    private function populateTimestamp()
    {
        // Check to see if there is an existing token, if so then populate the timestamp property
        if ($this->userDetails["token"]) {
            $timestamp = new DateTime();
            $timestamp->setTimestamp( explode('|', $this->userDetails["token"])[1] );

            return $timestamp;
        }
        return false;
    }

    /**
     * @param DateTime $timestamp
     * @param int $offset
     * @return bool
     */
    private function isTimestampExpired(DateTime $timestamp, int $offset = 60): bool
    {
        $this->logger->notice('Reset Password', ['Method' => 'validateTimestamp']);
        $now = new DateTime('now');
        return (floor( abs( $now->getTimestamp() - $timestamp->getTimestamp() ) / 60) > $offset);
    }

    /************************
     * Notification Methods
     ************************/

    /**
     * This sends an email with a token link for the user to click to reset their password
     */
    private function sendTokenEmail()
    {
        $this->logger->notice('Reset Password', ['Method' => 'sendTokenEmail']);
        try {
            // Read the email template
            $template = file_get_contents(APP_ROOT . '/templates/email/' . $this->container->getLanguage() . '/reset_password_link.html');

            // Replace the % with the actual information
            $template = str_replace('%name%', $this->userDetails["mem_name"], $template);
            $template = str_replace('%email%', $this->userDetails["mem_email_work"], $template);
            $template = str_replace('%username%', $this->userDetails["mem_login"], $template);
            $template = str_replace('%site_name%', $GLOBALS["setTitle"], $template);
            $template = str_replace('%link%', $GLOBALS["root"] . '/general/resetpassword.php?token=' . $this->token,
                $template);

            $subject = $GLOBALS["setTitle"] . " " . $this->strings["email_forgot_pwd_subject"];

            $this->sendNotification($template, $subject);
        } catch (Exception $exception) {
            error_log('Error sending password email: ' . $exception->getMessage());
            $this->logger->error('Forgotten Password Link Notification',
                ['userDetails' => $this->userDetails, 'Exception' => $exception->getMessage()]);
        }
    }

    /**
     * This sends an email upon successful password reset
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
            $template = file_get_contents(APP_ROOT . '/templates/email/' . $this->container->getLanguage() . '/forgot_password_success.html');

            // Replace the %xx% with the actual data
            $template = str_replace('%name%', $this->userDetails["name"], $template);
            $template = str_replace('%site_name%', $GLOBALS["setTitle"], $template);

            $subject = sprintf($this->strings["password_reset_confirmation_subject"], $this->userDetails["name"]);

            $this->sendNotification($template, $subject);
        } catch (Exception $exception) {
            $this->logger->error('Password Reset Successful Notification',
                ['userDetails' => $this->userDetails, 'Exception' => $exception->getMessage()]);
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
