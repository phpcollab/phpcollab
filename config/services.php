<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Cezpdf;
use Htpasswd;
use Laminas\Escaper\Escaper;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use phpCollab\Administration\Administration;
use phpCollab\Assignments\Assignments;
use phpCollab\Bookmarks\Bookmarks;
use phpCollab\Bookmarks\DeleteBookmarks;
use phpCollab\Calendars\Calendars;
use phpCollab\CsrfHandler;
use phpCollab\Database;
use phpCollab\DataFunctionsService;
use phpCollab\FileHandler;
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
use phpCollab\Notification;
use phpCollab\Notifications\AddProjectTeam;
use phpCollab\Notifications\MailNotification;
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
use phpCollab\Tasks\TaskUpdates;
use phpCollab\Teams\Teams;
use phpCollab\Topics\Topics;
use phpCollab\LoggerFactory;
use Sabre\VObject\Component\VCard;

return static function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->autowire()      // Automatically inject dependencies
        ->autoconfigure() // Automatically configure for tags
        ->private();      // Make services private by default

    // Configuration parameters
    $container->parameters()
        ->set('app.root', '%env(APP_ROOT)%')
        ->set('app.log_level', 400)
        ->set('app.log_path', '%app.root%/logs/phpcollab.log')
        ->set('app.language', 'en'); // Default language, can be overridden at runtime

    // Logger Factory
    $services->set(LoggerFactory::class);

    // Core Services
    $services->set(Logger::class)
        ->factory([service(LoggerFactory::class), 'create'])
        ->args([param('app.log_path'), param('app.log_level')])
        ->public();

    $services->set(Database::class)
        ->args([
            param('database.config'),
            service(Logger::class)
        ])
        ->public();

    $services->set(Escaper::class)
        ->args(['utf-8'])
        ->public();

    // Data Services
    $services->set(DataFunctionsService::class);

    // Entity Loaders/Managers (alphabetically organized)
    // These are marked as public because they're accessed via the legacy Container
    $services->set(Administration::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Assignments::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Bookmarks::class)
        ->args([service(Database::class), service(Escaper::class)])
        ->public();

    $services->set(DeleteBookmarks::class)
        ->args([service(Database::class), service(Escaper::class)])
        ->public();

    $services->set(Calendars::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(LoginLogs::class)
        ->args([service(Database::class)])
        ->public();

    // ✅ Refactored to use pure constructor injection
    $services->set(Members::class)
        ->args([
            service(Database::class),
            service(Logger::class),
            service(Notification::class)
        ])
        ->public();

    $services->set(NewsDesk::class)
        ->args([service(Database::class), service(Escaper::class)])
        ->public();

    $services->set(Notes::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Organizations::class)
        ->args([service(Database::class), service(Escaper::class)])
        ->public();

    $services->set(Phases::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Projects::class)
        ->args([service(Database::class), service(Escaper::class)])
        ->public();

    $services->set(Reports::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Sorting::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(Support::class)
        ->args([
            service(Database::class),
            service('service_locator.container'),
            service(Logger::class)
        ])
        ->public();

    // Services (phpCollab\Services namespace)
    $services->set(Services::class)
        ->args([service(Database::class)])
        ->public();

    // ✅ Refactored to use pure constructor injection
    // Task-related Services
    $services->set(Tasks::class)
        ->args([
            service(Database::class),
            service(MailNotification::class),
            param('app.language'),
            service(Projects::class),
            service(Teams::class),
            service(Notifications::class),
            service(Notification::class)
        ])
        ->public();

    $services->set(TaskUpdates::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(SetTaskStatus::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    // ✅ Refactored to use pure constructor injection
    // Subtask Services
    $services->set(Subtasks::class)
        ->args([
            service(Database::class),
            service(Notifications::class),
            service(SubtaskNotifications::class)
        ])
        ->public();

    $services->set(SetStatus::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    // Teams
    $services->set(Teams::class)
        ->args([
            service(Database::class),
            service(Notification::class),
            service(Notifications::class)
        ])
        ->public();

    // Topics
    $services->set(Topics::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    // File Services
    $services->set(Files::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    $services->set(GetFile::class)
        ->public();

    $services->set(FileHandler::class)
        ->args([null]) // Type parameter will be set at runtime if needed
        ->public();

    $services->set(UpdateFile::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    $services->set(PeerReview::class)
        ->args([
            service(Database::class),
            service(Notification::class),
            service('service_locator.container')
        ])
        ->public();

    $services->set(ApprovalTracking::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    // Notification Services
    $services->set(Notification::class)
        ->args([service('service_locator.container')])
        ->public();

    $services->set(Notifications::class)
        ->args([service(Database::class)])
        ->public();

    $services->set(MailNotification::class)
        ->args([
            service('service_locator.container'),
            service(Logger::class)
        ])
        ->public();

    $services->set(TopicNewTopic::class)
        ->args([service('service_locator.container')])
        ->public();

    $services->set(TopicNewPost::class)
        ->args([service('service_locator.container')])
        ->public();

    $services->set(AddProjectTeam::class)
        ->args([service('service_locator.container')])
        ->public();

    $services->set(RemoveProjectTeam::class)
        ->args([service('service_locator.container')])
        ->public();

    $services->set(SubtaskNotifications::class)
        ->args([service('service_locator.container')])
        ->public();

    // Invoices
    $services->set(Invoices::class)
        ->args([
            service(Database::class),
            service('service_locator.container')
        ])
        ->public();

    $services->set(Publish::class)
        ->args([service(Database::class)])
        ->public();

    // Member Services
    $services->set(ResetPassword::class)
        ->args([
            service(Database::class),
            service(Logger::class),
            service('service_locator.container')
        ])
        ->public();

    $services->set(Htpasswd::class)
        ->class('Htpasswd')
        ->public();

    // Export Services
    $services->set(Cezpdf::class)
        ->class('Cezpdf')
        ->public();

    $services->set(VCard::class)
        ->public();

    // Service Locator for backward compatibility with Container pattern
    // This will be replaced gradually as we refactor services
    $services->set('service_locator.container')
        ->synthetic() // Will be set at runtime
        ->public();
};
