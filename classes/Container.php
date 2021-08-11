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
    private $language;
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

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return Database
     * @throws Exception
     */
    public function getPDO(): Database
    {
        if (null === $this->database) {
            try {
                $this->database = new Database($this->configuration, $this->getLogger());
            } catch (Exception $exception) {
                error_log($exception->getMessage());
                throw new Exception('Error connecting to database');
            }
        }
        return $this->database;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language ?? 'en';
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    /**
     * @param int $level
     * @return Logger
     */
    public function getLogger(int $level = 400): Logger
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
    public function getCSRFHandler()
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
    public function setCSRFHandler(Session $session): CsrfHandler
    {
        if (null === $this->csrfHandler) {
            $this->csrfHandler = new CsrfHandler($session);
        }
        return $this->csrfHandler;
    }

    /**
     * @return Bookmarks
     * @throws Exception
     */
    public function getBookmarksLoader(): Bookmarks
    {
        if ($this->bookmarkLoader === null) {
            $this->bookmarkLoader = new Bookmarks($this->getPDO(), $this->getEscaperService());
        }
        return $this->bookmarkLoader;
    }

    /**
     * @return DeleteBookmarks
     * @throws Exception
     */
    public function getDeleteBookmarksLoader(): DeleteBookmarks
    {
        if ($this->deleteBookmarkLoader === null) {
            $this->deleteBookmarkLoader = new DeleteBookmarks($this->getPDO(), $this->getEscaperService());
        }
        return $this->deleteBookmarkLoader;
    }

    /**
     * @return LoginLogs
     * @throws Exception
     */
    public function getLoginLogs(): LoginLogs
    {
        if ($this->loginLogsLoader === null) {
            $this->loginLogsLoader = new LoginLogs($this->getPDO());
        }
        return $this->loginLogsLoader;
    }

    /**
     * @return Sorting
     * @throws Exception
     */
    public function getSortingLoader(): Sorting
    {
        if ($this->sortingLoader === null) {
            $this->sortingLoader = new Sorting($this->getPDO());
        }
        return $this->sortingLoader;
    }

    /**
     * @return Administration
     * @throws Exception
     */
    public function getAdministration(): Administration
    {
        if (null === $this->administration) {
            $this->administration = new Administration($this->getPDO());
        }

        return $this->administration;
    }

    /**
     * @return Organizations
     * @throws Exception
     */
    public function getOrganizationsManager(): Organizations
    {
        if (null === $this->organizationsManager) {
            $this->organizationsManager = new Organizations($this->getPDO(), $this->getEscaperService());
        }
        return $this->organizationsManager;
    }

    /**
     * @return Teams
     * @throws Exception
     */
    public function getTeams(): Teams
    {
        if (null === $this->teams) {
            $this->teams = new Teams($this->getPDO(), $this->getNotification(), $this->getNotificationsManager());
        }
        return $this->teams;

    }

    /**
     * @return Notification
     */
    public function getNotification(): Notification
    {
        if (null === $this->notificationManager) {
            $this->notificationManager = new Notification($this);
        }
        return $this->notificationManager;
    }

    /**
     * @return TopicNewTopic
     */
    public function getNotificationNewTopicManager(): TopicNewTopic
    {
        if (null === $this->notificationNewTopic) {
            $this->notificationNewTopic = new TopicNewTopic($this);
        }
        return $this->notificationNewTopic;
    }

    /**
     * @return TopicNewPost
     */
    public function getNotificationNewPostManager(): TopicNewPost
    {
        if (null === $this->notificationNewPost) {
            $this->notificationNewPost = new TopicNewPost($this);
        }
        return $this->notificationNewPost;
    }

    /**
     * @return Notifications
     * @throws Exception
     */
    public function getNotificationsManager(): Notifications
    {
        if (null === $this->notificationsManager) {
            $this->notificationsManager = new Notifications($this->getPDO());
        }
        return $this->notificationsManager;
    }

    /**
     * @return Assignments
     * @throws Exception
     */
    public function getAssignmentsManager(): Assignments
    {
        if (null === $this->assignmentsManager) {
            $this->assignmentsManager = new Assignments($this->getPDO());
        }
        return $this->assignmentsManager;
    }

    /**
     * @return Projects
     * @throws Exception
     */
    public function getProjectsLoader(): Projects
    {
        if (null === $this->projectsLoader) {
            $this->projectsLoader = new Projects($this->getPDO(), $this->getEscaperService());
        }
        return $this->projectsLoader;
    }

    /**
     * @return AddProjectTeam
     */
    public function getNotificationAddProjectTeamService(): AddProjectTeam
    {
        if (null === $this->notificationAddProjectTeamService) {
            $this->notificationAddProjectTeamService = new AddProjectTeam($this);
        }
        return $this->notificationAddProjectTeamService;
    }

    /**
     * @return RemoveProjectTeam
     */
    public function getNotificationRemoveProjectTeamService(): RemoveProjectTeam
    {
        if (null === $this->notificationRemoveProjectTeamService) {
            $this->notificationRemoveProjectTeamService = new RemoveProjectTeam($this);
        }
        return $this->notificationRemoveProjectTeamService;
    }

    /**
     * @return Tasks
     * @throws Exception
     */
    public function getTasksLoader(): Tasks
    {
        if (null === $this->tasksLoader) {
            $this->tasksLoader = new Tasks($this->getPDO(), $this);
        }
        return $this->tasksLoader;
    }

    /**
     * @return TaskUpdates
     * @throws Exception
     */
    public function getTaskUpdateService(): TaskUpdates
    {
        if (null === $this->taskUpdateService) {
            $this->taskUpdateService = new TaskUpdates($this->getPDO());
        }
        return $this->taskUpdateService;
    }

    /**
     * @return Subtasks
     * @throws Exception
     */
    public function getSubtasksLoader(): Subtasks
    {
        if (null === $this->subTasksLoader) {
            $this->subTasksLoader = new Subtasks($this->getPDO(), $this);
        }
        return $this->subTasksLoader;
    }

    /**
     * @return SubtaskNotifications
     */
    public function getSubtasksNotificationsManager(): SubtaskNotifications
    {
        if (null === $this->subtasksNotifications) {
            $this->subtasksNotifications = new SubtaskNotifications($this);
        }
        return $this->subtasksNotifications;
    }

    /**
     * @return SetStatus
     * @throws Exception
     */
    public function getSetStatusService(): SetStatus
    {
        if (null === $this->setStatusService) {
            $this->setStatusService = new SetStatus($this->getPDO(), $this);
        }
        return $this->setStatusService;
    }

    /**
     * @return SetTaskStatus
     * @throws Exception
     */
    public function getSetTaskStatusServiceService(): SetTaskStatus
    {
        if (null === $this->setTaskStatusService) {
            $this->setTaskStatusService = new SetTaskStatus($this->getPDO(), $this);
        }
        return $this->setTaskStatusService;
    }

    /**
     * @return Notes
     * @throws Exception
     */
    public function getNotesLoader(): Notes
    {
        if (null === $this->notesLoader) {
            $this->notesLoader = new Notes($this->getPDO());
        }
        return $this->notesLoader;
    }

    /**
     * @return Topics
     * @throws Exception
     */
    public function getTopicsLoader(): Topics
    {
        if (null === $this->topicsLoader) {
            $this->topicsLoader = new Topics($this->getPDO(), $this);
        }
        return $this->topicsLoader;
    }

    /**
     * @return Members
     * @throws Exception
     */
    public function getMembersLoader(): Members
    {
        if (null === $this->membersLoader) {
            $this->membersLoader = new Members($this->getPDO(), $this->getLogger(), $this);
        }
        return $this->membersLoader;
    }

    /**
     * @return Calendars
     * @throws Exception
     */
    public function getCalendarLoader(): Calendars
    {
        if (null === $this->calendarLoader) {
            $this->calendarLoader = new Calendars($this->getPDO());
        }
        return $this->calendarLoader;
    }

    /**
     * @return NewsDesk
     * @throws Exception
     */
    public function getNewsdeskLoader(): NewsDesk
    {
        if (null === $this->newsDeskLoader) {
            $this->newsDeskLoader = new NewsDesk($this->getPDO(), $this->getEscaperService());
        }
        return $this->newsDeskLoader;
    }

    /**
     * @return Reports
     * @throws Exception
     */
    public function getReportsLoader(): Reports
    {
        if (null === $this->reportsLoader) {
            $this->reportsLoader = new Reports($this->getPDO());
        }
        return $this->reportsLoader;
    }

    /**
     * @return Files
     * @throws Exception
     */
    public function getFilesLoader(): Files
    {
        if (null === $this->filesLoader) {
            $this->filesLoader = new Files($this->getPDO(), $this);
        }
        return $this->filesLoader;
    }

    /**
     * @param $fileObj
     * @return FileUploader
     * @throws Exception
     */
    public function getFileUploadLoader($fileObj): FileUploader
    {
        if (null === $this->fileUploaderLoader) {
            $this->fileUploaderLoader = new FileUploader($this->getPDO(), $fileObj);
        }
        return $this->fileUploaderLoader;
    }

    /**
     * @return GetFile
     */
    public function getFileDownloadService(): GetFile
    {
        if (null === $this->fileDownloadService) {
            $this->fileDownloadService = new GetFile();
        }
        return $this->fileDownloadService;
    }

    /**
     * @param null $type
     * @return FileHandler
     */
    public function getFileHandlerService($type = null): FileHandler
    {
        if (null === $this->fileHandlerService) {
            $this->fileHandlerService = new FileHandler($type);
        }
        return $this->fileHandlerService;
    }

    /**
     * @return UpdateFile
     * @throws Exception
     */
    public function getFileUpdateService(): UpdateFile
    {
        if (null === $this->fileUpdateService) {
            $this->fileUpdateService = new UpdateFile($this->getPDO(), $this);
        }
        return $this->fileUpdateService;
    }

    /**
     * @return PeerReview
     * @throws Exception
     */
    public function getPeerReviewService(): PeerReview
    {
        if (null === $this->peerReviewService) {
            $this->peerReviewService = new PeerReview($this->getPDO(), $this->getNotification(), $this);
        }
        return $this->peerReviewService;
    }

    /**
     * @return ApprovalTracking
     * @throws Exception
     */
    public function getApprovalTrackingService(): ApprovalTracking
    {
        if (null === $this->approvalTrackingService) {
            $this->approvalTrackingService = new ApprovalTracking($this->getPDO(), $this);
        }
        return $this->approvalTrackingService;
    }

    /**
     * @return Support
     * @throws Exception
     */
    public function getSupportLoader(): Support
    {
        if (null === $this->supportLoader) {
            $this->supportLoader = new Support($this->getPDO(), $this, $this->getLogger());
        }
        return $this->supportLoader;
    }

    /**
     * @return Phases
     * @throws Exception
     */
    public function getPhasesLoader(): Phases
    {
        if (null === $this->phasesLoader) {
            $this->phasesLoader = new Phases($this->getPDO());
        }
        return $this->phasesLoader;
    }

    /**
     * @return Invoices
     * @throws Exception
     */
    public function getInvoicesLoader(): Invoices
    {
        if (null === $this->invoicesLoader) {
            $this->invoicesLoader = new Invoices($this->getPDO(), $this);
        }
        return $this->invoicesLoader;
    }

    /**
     * @return Services
     * @throws Exception
     */
    public function getServicesLoader(): Services
    {
        if (null === $this->servicesLoader) {
            $this->servicesLoader = new Services($this->getPDO());
        }
        return $this->servicesLoader;
    }

    /**
     * @return DataFunctionsService
     */
    public function getDatafunctionsService(): DataFunctionsService
    {
        if (null === $this->dataFunctionsService) {
            $this->dataFunctionsService = new DataFunctionsService();
        }
        return $this->dataFunctionsService;

    }

    /**
     * @return ResetPassword
     * @throws Exception
     */
    public function getResetPasswordService(): ResetPassword
    {
        if (null === $this->resetPasswordService) {
            $this->resetPasswordService = new ResetPassword($this->getPDO(), $this->getLogger(), $this);
        }
        return $this->resetPasswordService;
    }

    /**
     * @return Htpasswd
     */
    public function getHtpasswdService(): Htpasswd
    {
        if (null === $this->htpasswdService) {
            $this->htpasswdService = new Htpasswd();
        }
        return $this->htpasswdService;
    }

    /**
     * @return Escaper
     */
    public function getEscaperService(): Escaper
    {
        if (null === $this->escaperService) {
            $this->escaperService = new Escaper('utf-8');
        }
        return $this->escaperService;
    }

    /**
     * @return Publish
     * @throws Exception
     */
    public function getInvoicePublishService(): Publish
    {
        if (null === $this->invoicePublishService) {
            $this->invoicePublishService = new Publish($this->getPDO());
        }
        return $this->invoicePublishService;
    }

    /**
     * @return Cezpdf
     */
    public function getExportPDFService(): Cezpdf
    {
        if (null === $this->exportPDFService) {
            $this->exportPDFService = new Cezpdf();
        }
        return $this->exportPDFService;
    }

    /**
     * @return VCard
     */
    public function getExportVCardService(): VCard
    {
        if (null === $this->exportVCardService) {
            $this->exportVCardService = new VCard();
        }
        return $this->exportVCardService;
    }
}
