<?php

namespace phpCollab\Services;

use phpCollab\AppConfig;

/**
 * Service for handling sorting logic
 *
 * Extracted from Block.php to follow Single Responsibility Principle.
 * Handles all sorting-related operations including:
 * - Parsing sorting parameters
 * - Generating sorting URLs
 * - Managing sorting state
 */
class SortingService
{
    private AppConfig $appConfig;
    private array $sortingOrders;
    private array $sortingFields;
    private array $sortingArrows;
    private array $sortingStyles;

    private ?string $sortingRef = null;
    private ?string $sortingValue = null;
    private ?string $sortingDefault = null;
    private array $currentSortingFields = [];

    /**
     * SortingService constructor.
     *
     * @param AppConfig $appConfig Application configuration
     */
    public function __construct(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
        $this->sortingOrders = $appConfig->getSortingOrders();
        $this->sortingFields = $appConfig->getSortingFields();
        $this->sortingArrows = $appConfig->getSortingArrows();
        $this->sortingStyles = $appConfig->getSortingStyles();
    }

    /**
     * Initialize sorting parameters
     *
     * @param string $sortingRef Row reference in sorting table
     * @param mixed $sortingValue Current sorting value
     * @param string $sortingDefault Default sorting value
     * @param array $sortingFields Array with sorted fields on each column
     */
    public function initialize(
        string $sortingRef,
        $sortingValue,
        string $sortingDefault,
        array $sortingFields
    ): void {
        if ($sortingRef != "") {
            $this->sortingRef = $sortingRef;
        }
        if ($sortingValue != "") {
            $this->sortingValue = $sortingValue;
        }
        if ($sortingDefault != "") {
            $this->sortingDefault = $sortingDefault;
        }
        if (!empty($sortingFields)) {
            $this->currentSortingFields = $sortingFields;
        }

        if (!empty($this->sortingValue)) {
            $explode = explode(" ", $this->sortingValue);
        } else {
            $this->sortingValue = $this->sortingDefault;
            $explode = explode(" ", $this->sortingDefault);
        }
    }

    /**
     * Get current sorting value
     *
     * @return string|null
     */
    public function getSortingValue(): ?string
    {
        return $this->sortingValue;
    }

    /**
     * Get current sorting reference
     *
     * @return string|null
     */
    public function getSortingRef(): ?string
    {
        return $this->sortingRef;
    }

    /**
     * Get sorting orders configuration
     *
     * @return array
     */
    public function getSortingOrders(): array
    {
        return $this->sortingOrders;
    }

    /**
     * Get sorting arrows configuration
     *
     * @return array
     */
    public function getSortingArrows(): array
    {
        return $this->sortingArrows;
    }

    /**
     * Get sorting styles configuration
     *
     * @return array
     */
    public function getSortingStyles(): array
    {
        return $this->sortingStyles;
    }

    /**
     * Get current sorting fields
     *
     * @return array
     */
    public function getCurrentSortingFields(): array
    {
        return $this->currentSortingFields;
    }

    /**
     * Build sorting URL for a column
     *
     * @param int $columnIndex Column index
     * @param string $baseUrl Base URL for sorting links
     * @return string Sorting URL with parameters
     */
    public function buildSortingUrl(int $columnIndex, string $baseUrl): string
    {
        $sortingField = $this->currentSortingFields[$columnIndex] ?? '';

        if (empty($sortingField)) {
            return $baseUrl;
        }

        $currentSort = $this->sortingValue ?? $this->sortingDefault;
        $explode = explode(" ", $currentSort);

        // Toggle sort order if same field
        if (!empty($explode[0]) && $explode[0] == $sortingField) {
            $newOrder = ($explode[1] == "DESC") ? "ASC" : "DESC";
        } else {
            $newOrder = "ASC";
        }

        $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
        return $baseUrl . $separator . $this->sortingRef . "=" . $sortingField . " " . $newOrder;
    }

    /**
     * Get sort arrow HTML for a column
     *
     * @param int $columnIndex Column index
     * @return string HTML for sort arrow
     */
    public function getSortArrowHtml(int $columnIndex): string
    {
        $sortingField = $this->currentSortingFields[$columnIndex] ?? '';

        if (empty($sortingField)) {
            return '';
        }

        $currentSort = $this->sortingValue ?? $this->sortingDefault;
        $explode = explode(" ", $currentSort);

        // Show arrow only if this column is currently sorted
        if (!empty($explode[0]) && $explode[0] == $sortingField) {
            $order = $explode[1] ?? 'ASC';
            $arrowIndex = ($order == "DESC") ? 0 : 1;
            return $this->sortingArrows[$arrowIndex] ?? '';
        }

        return '';
    }

    /**
     * Get sort style class for a column
     *
     * @param int $columnIndex Column index
     * @return string CSS class for sorted column
     */
    public function getSortStyleClass(int $columnIndex): string
    {
        $sortingField = $this->currentSortingFields[$columnIndex] ?? '';

        if (empty($sortingField)) {
            return '';
        }

        $currentSort = $this->sortingValue ?? $this->sortingDefault;
        $explode = explode(" ", $currentSort);

        // Return style class if this column is currently sorted
        if (!empty($explode[0]) && $explode[0] == $sortingField) {
            $order = $explode[1] ?? 'ASC';
            $styleIndex = ($order == "DESC") ? 0 : 1;
            return $this->sortingStyles[$styleIndex] ?? '';
        }

        return '';
    }
}
