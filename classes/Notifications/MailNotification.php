<?php


namespace phpCollab\Notifications;


use Exception;
use Monolog\Logger;
use phpCollab\Container;
use phpCollab\Exceptions\SendNotificationFailException;
use PHPMailer\PHPMailer\PHPMailer;

class MailNotification extends PHPMailer
{
    use SendEmailTrait;

    /**
     * @var mixed
     */
    private string $notificationMethod;
    private Container $container;
    private Logger $logger;
    private $lang;

    public function __construct(Container $container, Logger $logger, $lang = null, $exceptions = true)
    {
        try {
            parent::__construct($exceptions);
            $this->container = $container;

            $this->logger = $logger;

            $this->logger->debug(__CLASS__, ['Info', __FUNCTION__]);

            if (is_null($lang)) {
                $this->lang = (!empty($GLOBALS["lang"])) ? $GLOBALS["lang"] : "en";
            } else {
                $this->lang = $lang;
            }

            $this->notificationMethod = $GLOBALS["notificationMethod"];

            $this->Mailer = $this->notificationMethod;
            $this->SetLanguage($this->lang);

            if ($this->Mailer == "smtp") {
                $this->logger->debug(__CLASS__, ['Info', 'set isSMTP - ' . SMTPSERVER]);

                $this->isSMTP();
                $this->Host = SMTPSERVER;
                if (defined('SMTPPORT')) {
                    $this->Port = (empty(SMTPPORT)) ? 25 : SMTPPORT;
                } else {
                    $this->Port = 25;
                }
                $this->SMTPAuth = false;
                if (!empty(SMTPLOGIN && !empty(SMTPPASSWORD))) {
                    $this->SMTPAuth = true;
                    $this->Username = SMTPLOGIN;
                    $this->Password = SMTPPASSWORD;
                }
            }

        } catch (Exception $exception) {

        }
    }

    /**
     * @return void
     * @throws SendNotificationFailException
     */
    public function sendEmail()
    {
        $this->logger->debug(__CLASS__, ['Info', __FUNCTION__]);
        try {
            $this->setFrom($this->getFromEmail(), $this->getFromName());
            $this->Subject = $this->getSubject();
            $this->Priority = $this->getPriority();
            $this->Body = $this->getTemplate();
            $this->AddAddress($this->getToEmail(), $this->getToName());
            $this->Send();

            $this->ClearAddresses();
        } catch (Exception $exception) {
            $this->logger->error(__FUNCTION__, ['error' => $exception->getMessage()]);
            throw new SendNotificationFailException($exception->getMessage());
        }
    }
}
