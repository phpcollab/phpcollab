<?php


namespace phpCollab;


use Cezpdf;
use Exception;
use Htpasswd;
use Laminas\Escaper\Escaper;
use LogicException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use phpCollab\Administration\Administration;
use phpCollab\Assignments\Assignments;
use phpCollab\Bookmarks\Bookmarks;
use phpCollab\Bookmarks\DeleteBookmarks;
use phpCollab\Calendars\Calendars;
use phpCollab\Files\ApprovalTracking;
use phpCollab\Files\Files;
use phpCollab\Files\FileUploader;
use phpCollab\Files\GetFile;
use phpCollab\Files\PeerReview;
use phpCollab\Files\UpdateFile;
use phpCollab\Invoices\Invoices;
use phpCollab\Invoices\Publish;
use phpCollab\LoginLogs\LoginLogs;
use phpCollab\Members\Members;
use phpCollab\Members\ResetPassword;
use phpCollab\NewsDesk\NewsDesk;
use phpCollab\Notes\Notes;
use phpCollab\Notifications\AddProjectTeam;
use phpCollab\Notifications\Notifications;
use phpCollab\Notifications\RemoveProjectTeam;
use phpCollab\Notifications\SubtaskNotifications;
use phpCollab\Notifications\TopicNewPost;
use phpCollab\Notifications\TopicNewTopic;
use phpCollab\Organizations\Organizations;
use phpCollab\Phases\Phases;
use phpCollab\Projects\Projects;
use phpCollab\Reports\Reports;
use phpCollab\Services\Services;
use phpCollab\Sorting\Sorting;
use phpCollab\Subtasks\SetStatus;
use phpCollab\Subtasks\Subtasks;
use phpCollab\Support\Support;
use phpCollab\Tasks\SetTaskStatus;
use phpCollab\Tasks\Tasks;
use phpCollab\Teams\Teams;
use phpCollab\Topics\Topics;
use phpCollab\Tasks\TaskUpdates;
use Sabre\VObject\Component\VCard;
use Symfony\Component\HttpFoundation\Session\Session;

class Container
{
    private $database;
    private $configuration;
    private $logger;
    private $csrfHandler;
    private $bookmarkLoader;
    private $deleteBookmarkLoader;
    private $loginLogsLoader;
    private $sortingLoader;
    private $administration;
    private $organizationsManager;
    private $teams;
    private $notificationsManager;
    private $notificationManager;
    private $notificationNewTopic;
    private $assignmentsManager;
    private $projectsLoader;
    private $tasksLoader;
    private $taskUpdateService;
    private $setTaskStatusService;
    private $subTasksLoader;
    private $setStatusService;
    private $notesLoader;
    private $topicsLoader;
    private $membersLoader;
    private $calendarLoader;
    private $newsDeskLoader;
    private $reportsLoader;
    private $filesLoader;
    private $fileUploaderLoader;
    private $fileDownloadService;
    private $fileHandlerService;
    private $fileUpdateService;
    private $peerReviewService;
    private $approvalTrackingService;
    private $supportLoader;
    private $phasesLoader;
    private $invoicesLoader;
    private $servicesLoader;
    private $dataFunctionsService;
    private $resetPasswordService;
    private $htpasswdService;
    private $notificationNewPost;
    private $notificationRemoveProjectTeamService;
    private $notificationAddProjectTeamService;
    private $escaperService;
    private $subtasksNotifications;
    private $invoicePublishService;
    private $exportPDFService;
    private $exportVCardService;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getPDO()
    {
        if (null === $this->database) {
            try {
                $this->database = new Database($this->configuration, $this->getLogger());
            } catch (Exception $exception) {
                error_log($exception->getMessage());
                return $exception;
            }
        }

        return $this->database;
    }

    public function getLogger($level = 400)
    {
        if (null === $this->logger) {
            try {
                if (is_null($level)) {
                    $level = 400;
                }
                $stream = new StreamHandler(APP_ROOT . '/logs/phpcollab.log', $level);
                // create a log channel
                $this->logger = new Logger('phpCollab');
                $this->logger->pushHandler($stream);
                $this->logger->pushProcessor(new IntrospectionProcessor());
            } catch (Exception $e) {
                error_log('library error: ' . $e->getMessage());
            }
        }

        return $this->logger;
    }

    /**
     * Check to see if the CSRF Handler has been instantiated.
     * If not throws an error, otherwise returns the reference.
     * @return mixed
     */
    public function getCSRFHander()
    {
        if (null === $this->csrfHandler) {
            throw new LogicException('CSRF Handler not instantiated. Please refer to setCSRFHandler()');
        }
        return $this->csrfHandler;
    }

    /**
     * Instantiates the CSRF Handler and returns it
     * @param Session $session
     * @return CsrfHandler
     */
    public function setCSRFHandler(Session $session)
    {
        if (null === $this->csrfHandler) {
            $this->csrfHandler = new CsrfHandler($session);
        }
        return $this->csrfHandler;
    }

    public function getBookmarksLoader()
    {
        if ($this->bookmarkLoader === null) {
            $this->bookmarkLoader = new Bookmarks($this->getPDO());
        }
        return $this->bookmarkLoader;
    }

    public function getDeleteBookmarksLoader()
    {
        if ($this->deleteBookmarkLoader === null) {
            $this->deleteBookmarkLoader = new DeleteBookmarks($this->getPDO());
        }
        return $this->deleteBookmarkLoader;
    }

    public function getLoginLogs()
    {
        if ($this->loginLogsLoader === null) {
            $this->loginLogsLoader = new LoginLogs($this->getPDO());
        }
        return $this->loginLogsLoader;
    }

    public function getSortingLoader()
    {
        if ($this->sortingLoader === null) {
            $this->sortingLoader = new Sorting($this->getPDO());
        }
        return $this->sortingLoader;
    }

    public function getAdministration()
    {
        if (null === $this->administration) {
            $this->administration = new Administration($this->getPDO());
        }

        return $this->administration;
    }

    public function getOrganizationsManager()
    {
        if (null === $this->organizationsManager) {
            $this->organizationsManager = new Organizations($this->getPDO());
        }
        return $this->organizationsManager;
    }

    public function getTeams()
    {
        if (null === $this->teams) {
            $this->teams = new Teams($this->getPDO(), $this->getNotification(), $this->getNotificationsManager());
        }
        return $this->teams;

    }

    public function getNotification()
    {
        if (null === $this->notificationManager) {
            $this->notificationManager = new Notification($this);
        }
        return $this->notificationManager;
    }

    public function getNotificationNewTopicManager()
    {
        if (null === $this->notificationNewTopic) {
            $this->notificationNewTopic = new TopicNewTopic($this);
        }
        return $this->notificationNewTopic;
    }

    public function getNotificationNewPostManager()
    {
        if (null === $this->notificationNewPost) {
            $this->notificationNewPost = new TopicNewPost($this);
        }
        return $this->notificationNewPost;
    }

    public function getNotificationsManager()
    {
        if (null === $this->notificationsManager) {
            $this->notificationsManager = new Notifications($this->getPDO());
        }
        return $this->notificationsManager;
    }

    public function getAssignmentsManager()
    {
        if (null === $this->assignmentsManager) {
            $this->assignmentsManager = new Assignments($this->getPDO());
        }
        return $this->assignmentsManager;
    }

    public function getProjectsLoader()
    {
        if (null === $this->projectsLoader) {
            $this->projectsLoader = new Projects($this->getPDO());
        }
        return $this->projectsLoader;
    }

    public function getNotificationAddProjectTeamService()
    {
        if (null === $this->notificationAddProjectTeamService) {
            $this->notificationAddProjectTeamService = new AddProjectTeam($this);
        }
        return $this->notificationAddProjectTeamService;
    }

    public function getNotificationRemoveProjectTeamService()
    {
        if (null === $this->notificationRemoveProjectTeamService) {
            $this->notificationRemoveProjectTeamService = new RemoveProjectTeam($this);
        }
        return $this->notificationRemoveProjectTeamService;
    }

    public function getTasksLoader()
    {
        if (null === $this->tasksLoader) {
            $this->tasksLoader = new Tasks($this->getPDO(), $this);
        }
        return $this->tasksLoader;
    }

    public function getTaskUpdateService()
    {
        if (null === $this->taskUpdateService) {
            $this->taskUpdateService = new TaskUpdates($this->getPDO());
        }
        return $this->taskUpdateService;
    }

    public function getSubtasksLoader()
    {
        if (null === $this->subTasksLoader) {
            $this->subTasksLoader = new Subtasks($this->getPDO(), $this);
        }
        return $this->subTasksLoader;
    }

    public function getSubtasksNotificationsManager()
    {
        if (null === $this->subtasksNotifications) {
            $this->subtasksNotifications = new SubtaskNotifications($this);
        }
        return $this->subtasksNotifications;
    }

    public function getSetStatusService()
    {
        if (null === $this->setStatusService) {
            $this->setStatusService = new SetStatus($this->getPDO(), $this);
        }
        return $this->setStatusService;
    }

    public function getSetTaskStatusServiceService()
    {
        if (null === $this->setTaskStatusService) {
            $this->setTaskStatusService = new SetTaskStatus($this->getPDO(), $this);
        }
        return $this->setTaskStatusService;
    }

    public function getNotesLoader()
    {
        if (null === $this->notesLoader) {
            $this->notesLoader = new Notes($this->getPDO());
        }
        return $this->notesLoader;
    }

    public function getTopicsLoader()
    {
        if (null === $this->topicsLoader) {
            $this->topicsLoader = new Topics($this->getPDO(), $this);
        }
        return $this->topicsLoader;
    }

    public function getMembersLoader()
    {
        if (null === $this->membersLoader) {
            $this->membersLoader = new Members($this->getPDO(), $this->getLogger(), $this);
        }
        return $this->membersLoader;
    }

    public function getCalendarLoader()
    {
        if (null === $this->calendarLoader) {
            $this->calendarLoader = new Calendars($this->getPDO());
        }
        return $this->calendarLoader;
    }

    public function getNewsdeskLoader()
    {
        if (null === $this->newsDeskLoader) {
            $this->newsDeskLoader = new NewsDesk($this->getPDO());
        }
        return $this->newsDeskLoader;
    }

    public function getReportsLoader()
    {
        if (null === $this->reportsLoader) {
            $this->reportsLoader = new Reports($this->getPDO());
        }
        return $this->reportsLoader;
    }

    public function getFilesLoader()
    {
        if (null === $this->filesLoader) {
            $this->filesLoader = new Files($this->getPDO(), $this);
        }
        return $this->filesLoader;
    }

    public function getFileUploadLoader($fileObj)
    {
        if (null === $this->fileUploaderLoader) {
            $this->fileUploaderLoader = new FileUploader($this->getPDO(), $fileObj);
        }
        return $this->fileUploaderLoader;
    }

    public function getFileDownloadService()
    {
        if (null === $this->fileDownloadService) {
            $this->fileDownloadService = new GetFile();
        }
        return $this->fileDownloadService;
    }

    public function getFileHandlerService($type = null)
    {
        if (null === $this->fileHandlerService) {
            $this->fileHandlerService = new FileHandler($type);
        }
        return $this->fileHandlerService;
    }

    public function getFileUpdateService()
    {
        if (null === $this->fileUpdateService) {
            $this->fileUpdateService = new UpdateFile($this->getPDO(), $this);
        }
        return $this->fileUpdateService;
    }

    public function getPeerReviewService()
    {
        if (null === $this->peerReviewService) {
            $this->peerReviewService = new PeerReview($this->getPDO(), $this->getNotification());
        }
        return $this->peerReviewService;
    }

    public function getApprovalTrackingService()
    {
        if (null === $this->approvalTrackingService) {
            $this->approvalTrackingService = new ApprovalTracking($this->getPDO(), $this);
        }
        return $this->approvalTrackingService;
    }

    public function getSupportLoader()
    {
        if (null === $this->supportLoader) {
            $this->supportLoader = new Support($this->getPDO(), $this, $this->getLogger());
        }
        return $this->supportLoader;
    }

    public function getPhasesLoader()
    {
        if (null === $this->phasesLoader) {
            $this->phasesLoader = new Phases($this->getPDO());
        }
        return $this->phasesLoader;
    }

    public function getInvoicesLoader()
    {
        if (null === $this->invoicesLoader) {
            $this->invoicesLoader = new Invoices($this->getPDO(), $this);
        }
        return $this->invoicesLoader;
    }

    public function getServicesLoader()
    {
        if (null === $this->servicesLoader) {
            $this->servicesLoader = new Services($this->getPDO());
        }
        return $this->servicesLoader;
    }

    public function getDatafunctionsService()
    {
        if (null === $this->dataFunctionsService) {
            $this->dataFunctionsService = new DataFunctionsService();
        }
        return $this->dataFunctionsService;

    }

    public function getResetPasswordService()
    {
        if (null === $this->resetPasswordService) {
            $this->resetPasswordService = new ResetPassword($this->getPDO(), $this->getLogger(), $this);
        }
        return $this->resetPasswordService;
    }

    public function getHtpasswdService()
    {
        if (null === $this->htpasswdService) {
            $this->htpasswdService = new Htpasswd();
        }
        return $this->htpasswdService;
    }

    public function getEscaperService()
    {
        if (null === $this->escaperService) {
            $this->escaperService = new Escaper('utf-8');
        }
        return $this->escaperService;
    }

    public function getInvoicePublishService()
    {
        if (null === $this->invoicePublishService) {
            $this->invoicePublishService = new Publish($this->getPDO());
        }
        return $this->invoicePublishService;
    }

    public function getExportPDFService()
    {
        if (null === $this->exportPDFService) {
            $this->exportPDFService = new Cezpdf();
        }
        return $this->exportPDFService;
    }

    public function getExportVCardService()
    {
        if (null === $this->exportVCardService) {
            $this->exportVCardService = new VCard();
        }
        return $this->exportVCardService;
    }
}
