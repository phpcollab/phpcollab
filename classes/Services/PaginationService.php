<?php

namespace phpCollab\Services;

use phpCollab\AppConfig;

/**
 * Service for handling pagination logic
 *
 * Extracted from Block.php to follow Single Responsibility Principle.
 * Handles all pagination-related operations including:
 * - Calculating page offsets
 * - Generating pagination links
 * - Managing result limits
 */
class PaginationService
{
    private AppConfig $appConfig;

    private ?int $limit = null;
    private ?int $rowsLimit = null;
    private ?int $recordsTotal = null;
    private ?int $limitsNumber = null;

    /**
     * PaginationService constructor.
     *
     * @param AppConfig $appConfig Application configuration
     */
    public function __construct(AppConfig $appConfig)
    {
        $this->appConfig = $appConfig;
    }

    /**
     * Get current limit offset
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Set current limit offset
     *
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Get rows per page limit
     *
     * @return int|null
     */
    public function getRowsLimit(): ?int
    {
        return $this->rowsLimit;
    }

    /**
     * Set rows per page limit
     *
     * @param int $rowsLimit
     */
    public function setRowsLimit(int $rowsLimit): void
    {
        $this->rowsLimit = $rowsLimit;
    }

    /**
     * Get total number of records
     *
     * @return int|null
     */
    public function getRecordsTotal(): ?int
    {
        return $this->recordsTotal;
    }

    /**
     * Set total number of records
     *
     * @param int $recordsTotal
     */
    public function setRecordsTotal(int $recordsTotal): void
    {
        $this->recordsTotal = $recordsTotal;
    }

    /**
     * Get limits number
     *
     * @return int|null
     */
    public function getLimitsNumber(): ?int
    {
        return $this->limitsNumber;
    }

    /**
     * Set limits number
     *
     * @param int $limitsNumber
     */
    public function setLimitsNumber(int $limitsNumber): void
    {
        $this->limitsNumber = $limitsNumber;
    }

    /**
     * Calculate pagination offset for SQL LIMIT clause
     *
     * @param int $currentPage Current page number (0-based)
     * @return int Offset for SQL query
     */
    public function calculateOffset(int $currentPage): int
    {
        if ($this->rowsLimit === null || $this->rowsLimit <= 0) {
            return 0;
        }

        return $currentPage * $this->rowsLimit;
    }

    /**
     * Get total number of pages
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        if ($this->recordsTotal === null || $this->rowsLimit === null || $this->rowsLimit <= 0) {
            return 1;
        }

        return (int)ceil($this->recordsTotal / $this->rowsLimit);
    }

    /**
     * Get current page number (0-based)
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        if ($this->limit === null || $this->rowsLimit === null || $this->rowsLimit <= 0) {
            return 0;
        }

        return (int)floor($this->limit / $this->rowsLimit);
    }

    /**
     * Check if there is a next page
     *
     * @return bool
     */
    public function hasNextPage(): bool
    {
        return $this->getCurrentPage() < ($this->getTotalPages() - 1);
    }

    /**
     * Check if there is a previous page
     *
     * @return bool
     */
    public function hasPreviousPage(): bool
    {
        return $this->getCurrentPage() > 0;
    }

    /**
     * Get offset for next page
     *
     * @return int
     */
    public function getNextPageOffset(): int
    {
        if (!$this->hasNextPage()) {
            return $this->limit ?? 0;
        }

        return ($this->getCurrentPage() + 1) * $this->rowsLimit;
    }

    /**
     * Get offset for previous page
     *
     * @return int
     */
    public function getPreviousPageOffset(): int
    {
        if (!$this->hasPreviousPage()) {
            return 0;
        }

        return ($this->getCurrentPage() - 1) * $this->rowsLimit;
    }

    /**
     * Get range of records being displayed
     *
     * @return array ['start' => int, 'end' => int]
     */
    public function getDisplayRange(): array
    {
        if ($this->recordsTotal === null || $this->recordsTotal === 0) {
            return ['start' => 0, 'end' => 0];
        }

        $start = ($this->limit ?? 0) + 1;
        $end = min(
            ($this->limit ?? 0) + ($this->rowsLimit ?? 10),
            $this->recordsTotal
        );

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Build pagination URL
     *
     * @param string $baseUrl Base URL
     * @param int $offset Offset value
     * @return string Complete URL with offset parameter
     */
    public function buildPaginationUrl(string $baseUrl, int $offset): string
    {
        $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
        return $baseUrl . $separator . "offset=" . $offset;
    }

    /**
     * Generate HTML for pagination footer
     *
     * @param int $current Current offset
     * @param int $total Total records
     * @param bool $showAll Show "all" option
     * @param string $baseUrl Base URL for pagination links
     * @return string HTML for pagination footer
     */
    public function renderFooter(int $current, int $total, bool $showAll, string $baseUrl): string
    {
        $this->setLimit($current);
        $this->setRecordsTotal($total);

        $range = $this->getDisplayRange();
        $totalPages = $this->getTotalPages();

        $html = '<div class="pagination">';
        $html .= '<span class="pagination-info">';
        $html .= "Showing {$range['start']} to {$range['end']} of {$total} entries";
        $html .= '</span>';

        if ($totalPages > 1) {
            $html .= '<span class="pagination-links">';

            // Previous link
            if ($this->hasPreviousPage()) {
                $prevUrl = $this->buildPaginationUrl($baseUrl, $this->getPreviousPageOffset());
                $html .= '<a href="' . htmlspecialchars($prevUrl) . '" class="pagination-prev">Previous</a>';
            }

            // Page numbers
            for ($i = 0; $i < $totalPages; $i++) {
                $offset = $i * $this->rowsLimit;
                $pageNum = $i + 1;

                if ($i == $this->getCurrentPage()) {
                    $html .= '<span class="pagination-current">' . $pageNum . '</span>';
                } else {
                    $pageUrl = $this->buildPaginationUrl($baseUrl, $offset);
                    $html .= '<a href="' . htmlspecialchars($pageUrl) . '" class="pagination-page">' . $pageNum . '</a>';
                }
            }

            // Next link
            if ($this->hasNextPage()) {
                $nextUrl = $this->buildPaginationUrl($baseUrl, $this->getNextPageOffset());
                $html .= '<a href="' . htmlspecialchars($nextUrl) . '" class="pagination-next">Next</a>';
            }

            // Show all link
            if ($showAll) {
                $allUrl = $this->buildPaginationUrl($baseUrl, 0) . '&showall=true';
                $html .= '<a href="' . htmlspecialchars($allUrl) . '" class="pagination-all">Show All</a>';
            }

            $html .= '</span>';
        }

        $html .= '</div>';

        return $html;
    }
}
