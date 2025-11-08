<?php

namespace phpCollab;

/**
 * Class AppConfig
 *
 * Application configuration object that replaces $GLOBALS usage.
 * This class holds all application-wide configuration values and can be
 * injected into services using dependency injection.
 *
 * @package phpCollab
 */
class AppConfig
{
    private array $strings;
    private string $root;
    private string $setTitle;
    private string $supportEmail;
    private array $priority;
    private array $status;
    private array $requestStatus;
    private string $lang;
    private string $notificationMethod;
    private bool $sitePublished;
    private string $databaseType;
    private string $gmtTimezone;
    private array $byteUnits;
    private string $mkdirMethod;
    private string $ftpRoot;
    private bool $useLDAP;
    private array $configLDAP;
    private string $passG;
    private string $lastId;
    private array $tableCollab;
    private $projectsFilter;
    private array $sortingOrders;
    private array $sortingFields;
    private array $sortingArrows;
    private array $sortingStyles;
    private $explode;
    private array $help;

    /**
     * AppConfig constructor.
     *
     * @param array $config Configuration array with all application settings
     */
    public function __construct(array $config = [])
    {
        // Core settings
        $this->strings = $config['strings'] ?? [];
        $this->root = $config['root'] ?? '';
        $this->setTitle = $config['setTitle'] ?? 'phpCollab';
        $this->supportEmail = $config['supportEmail'] ?? '';

        // Status arrays
        $this->priority = $config['priority'] ?? [];
        $this->status = $config['status'] ?? [];
        $this->requestStatus = $config['requestStatus'] ?? [];

        // Language and localization
        $this->lang = $config['lang'] ?? 'en';

        // Notification settings
        $this->notificationMethod = $config['notificationMethod'] ?? 'mail';

        // Site settings
        $this->sitePublished = $config['sitePublished'] ?? true;

        // Database settings
        $this->databaseType = $config['databaseType'] ?? 'mysql';
        $this->gmtTimezone = $config['gmtTimezone'] ?? 'GMT';

        // File/FTP settings
        $this->byteUnits = $config['byteUnits'] ?? ['B', 'KB', 'MB', 'GB', 'TB'];
        $this->mkdirMethod = $config['mkdirMethod'] ?? '0755';
        $this->ftpRoot = $config['ftpRoot'] ?? '';

        // LDAP settings
        $this->useLDAP = $config['useLDAP'] ?? false;
        $this->configLDAP = $config['configLDAP'] ?? [];

        // Security
        $this->passG = $config['passG'] ?? '';
        $this->lastId = $config['lastId'] ?? '';

        // Database table names
        $this->tableCollab = $config['tableCollab'] ?? [];

        // UI/Filtering settings
        $this->projectsFilter = $config['projectsFilter'] ?? false;

        // Sorting configuration (for Block class table sorting)
        $this->sortingOrders = $config['sortingOrders'] ?? [];
        $this->sortingFields = $config['sortingFields'] ?? [];
        $this->sortingArrows = $config['sortingArrows'] ?? [];
        $this->sortingStyles = $config['sortingStyles'] ?? [];
        $this->explode = $config['explode'] ?? ' ';

        // Help text
        $this->help = $config['help'] ?? [];
    }

    /**
     * Create AppConfig from current $GLOBALS
     *
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self([
            'strings' => $GLOBALS['strings'] ?? [],
            'root' => $GLOBALS['root'] ?? '',
            'setTitle' => $GLOBALS['setTitle'] ?? 'phpCollab',
            'supportEmail' => $GLOBALS['supportEmail'] ?? '',
            'priority' => $GLOBALS['priority'] ?? [],
            'status' => $GLOBALS['status'] ?? [],
            'requestStatus' => $GLOBALS['requestStatus'] ?? [],
            'lang' => $GLOBALS['lang'] ?? 'en',
            'notificationMethod' => $GLOBALS['notificationMethod'] ?? 'mail',
            'sitePublished' => $GLOBALS['sitePublished'] ?? true,
            'databaseType' => $GLOBALS['databaseType'] ?? 'mysql',
            'gmtTimezone' => $GLOBALS['gmtTimezone'] ?? 'GMT',
            'byteUnits' => $GLOBALS['byteUnits'] ?? ['B', 'KB', 'MB', 'GB', 'TB'],
            'mkdirMethod' => $GLOBALS['mkdirMethod'] ?? '0755',
            'ftpRoot' => $GLOBALS['ftpRoot'] ?? '',
            'useLDAP' => $GLOBALS['useLDAP'] ?? false,
            'configLDAP' => $GLOBALS['configLDAP'] ?? [],
            'passG' => $GLOBALS['pass_g'] ?? '',
            'lastId' => $GLOBALS['lastId'] ?? '',
            'tableCollab' => $GLOBALS['tableCollab'] ?? [],
            'projectsFilter' => $GLOBALS['projectsFilter'] ?? false,
            'sortingOrders' => $GLOBALS['sortingOrders'] ?? [],
            'sortingFields' => $GLOBALS['sortingFields'] ?? [],
            'sortingArrows' => $GLOBALS['sortingArrows'] ?? [],
            'sortingStyles' => $GLOBALS['sortingStyles'] ?? [],
            'explode' => $GLOBALS['explode'] ?? ' ',
            'help' => $GLOBALS['help'] ?? [],
        ]);
    }

    // Getters

    public function getStrings(): array
    {
        return $this->strings;
    }

    public function getString(string $key): string
    {
        return $this->strings[$key] ?? '';
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    public function getSetTitle(): string
    {
        return $this->setTitle;
    }

    public function getSupportEmail(): string
    {
        return $this->supportEmail;
    }

    public function getPriority(): array
    {
        return $this->priority;
    }

    public function getPriorityLabel(int $priority): string
    {
        return $this->priority[$priority] ?? '';
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function getStatusLabel(int $status): string
    {
        return $this->status[$status] ?? '';
    }

    public function getRequestStatus(): array
    {
        return $this->requestStatus;
    }

    public function getRequestStatusLabel(int $status): string
    {
        return $this->requestStatus[$status] ?? '';
    }

    public function getLang(): string
    {
        return $this->lang;
    }

    public function getNotificationMethod(): string
    {
        return $this->notificationMethod;
    }

    public function isSitePublished(): bool
    {
        return $this->sitePublished;
    }

    public function getDatabaseType(): string
    {
        return $this->databaseType;
    }

    public function getGmtTimezone(): string
    {
        return $this->gmtTimezone;
    }

    public function getByteUnits(): array
    {
        return $this->byteUnits;
    }

    public function getMkdirMethod(): string
    {
        return $this->mkdirMethod;
    }

    public function getFtpRoot(): string
    {
        return $this->ftpRoot;
    }

    public function isUseLDAP(): bool
    {
        return $this->useLDAP;
    }

    public function getConfigLDAP(): array
    {
        return $this->configLDAP;
    }

    public function getPassG(): string
    {
        return $this->passG;
    }

    public function getLastId(): string
    {
        return $this->lastId;
    }

    public function getTableCollab(): array
    {
        return $this->tableCollab;
    }

    public function getTableName(string $tableName): string
    {
        return $this->tableCollab[$tableName] ?? '';
    }

    public function getProjectsFilter()
    {
        return $this->projectsFilter;
    }

    public function getSortingOrders(): array
    {
        return $this->sortingOrders;
    }

    public function getSortingFields(): array
    {
        return $this->sortingFields;
    }

    public function getSortingArrows(): array
    {
        return $this->sortingArrows;
    }

    public function getSortingStyles(): array
    {
        return $this->sortingStyles;
    }

    public function getExplode()
    {
        return $this->explode;
    }

    public function getHelp(): array
    {
        return $this->help;
    }

    public function getHelpItem(string $key): string
    {
        return $this->help[$key] ?? '';
    }
}
