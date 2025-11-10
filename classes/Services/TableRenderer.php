<?php

namespace phpCollab\Services;

use phpCollab\AppConfig;

/**
 * Service for rendering HTML tables and table components
 *
 * Extracted from Block.php to follow Single Responsibility Principle.
 * Handles all table HTML generation including:
 * - Table headers with labels and sorting
 * - Table rows and cells
 * - Form elements within tables
 * - Checkboxes and row selection
 */
class TableRenderer
{
    private AppConfig $appConfig;
    private SortingService $sortingService;

    private string $class = "odd";
    private string $theme;
    private string $themeImgPath;
    private ?string $form = null;

    /**
     * TableRenderer constructor.
     *
     * @param AppConfig $appConfig Application configuration
     * @param SortingService $sortingService Sorting service for column headers
     */
    public function __construct(AppConfig $appConfig, SortingService $sortingService)
    {
        $this->appConfig = $appConfig;
        $this->sortingService = $sortingService;

        $this->theme = THEME;
        $this->themeImgPath = '../themes/' . $this->theme . '/images';
    }

    /**
     * Set form name for table
     *
     * @param string $formName
     */
    public function setForm(string $formName): void
    {
        $this->form = $formName;
    }

    /**
     * Get current form name
     *
     * @return string|null
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * Toggle row class (odd/even)
     *
     * @return string Current class
     */
    public function toggleRowClass(): string
    {
        $this->class = ($this->class == "odd") ? "even" : "odd";
        return $this->class;
    }

    /**
     * Get current row class
     *
     * @return string
     */
    public function getRowClass(): string
    {
        return $this->class;
    }

    /**
     * Reset row class to default
     */
    public function resetRowClass(): void
    {
        $this->class = "odd";
    }

    /**
     * Render table labels/headers with optional sorting
     *
     * @param array $labels Column labels
     * @param bool $published Is published view
     * @param bool $sorting Enable sorting
     * @param string $sortingOff Columns to disable sorting (comma-separated indices)
     * @return string HTML for table headers
     */
    public function renderLabels(
        array $labels,
        bool $published,
        bool $sorting = true,
        string $sortingOff = ""
    ): string {
        $sortingOffArray = !empty($sortingOff) ? explode(',', $sortingOff) : [];

        $html = '<tr>';

        foreach ($labels as $index => $label) {
            $isSortingDisabled = in_array($index, $sortingOffArray);

            if ($sorting && !$isSortingDisabled) {
                $sortUrl = $this->sortingService->buildSortingUrl($index, $_SERVER['REQUEST_URI']);
                $sortArrow = $this->sortingService->getSortArrowHtml($index);
                $sortClass = $this->sortingService->getSortStyleClass($index);

                $html .= '<th class="' . htmlspecialchars($sortClass) . '">';
                $html .= '<a href="' . htmlspecialchars($sortUrl) . '">';
                $html .= htmlspecialchars($label) . ' ' . $sortArrow;
                $html .= '</a>';
                $html .= '</th>';
            } else {
                $html .= '<th>' . htmlspecialchars($label) . '</th>';
            }
        }

        $html .= '</tr>';

        return $html;
    }

    /**
     * Open results table
     *
     * @param bool $checkbox Include checkbox column
     * @return string HTML for opening results table
     */
    public function openResults(bool $checkbox = true): string
    {
        $this->resetRowClass();

        $html = '<table class="results" cellspacing="0" cellpadding="0">';

        if ($checkbox) {
            $html .= '<col class="checkbox-col" />';
        }

        return $html;
    }

    /**
     * Close results table
     *
     * @return string HTML for closing results table
     */
    public function closeResults(): string
    {
        return '</table>';
    }

    /**
     * Render "no results" message
     *
     * @return string HTML for no results message
     */
    public function renderNoResults(): string
    {
        return '<tr><td colspan="100%" class="no-results">' .
               $this->appConfig->getString('no_results') .
               '</td></tr>';
    }

    /**
     * Open a table row
     *
     * @param string|null $extraClasses Additional CSS classes
     * @return string HTML for opening row
     */
    public function openRow(?string $extraClasses = null): string
    {
        $this->toggleRowClass();

        $classes = $this->class;
        if ($extraClasses) {
            $classes .= ' ' . $extraClasses;
        }

        return '<tr class="' . htmlspecialchars($classes) . '">';
    }

    /**
     * Close a table row
     *
     * @return string HTML for closing row
     */
    public function closeRow(): string
    {
        return '</tr>';
    }

    /**
     * Render checkbox cell for row
     *
     * @param string|int $ref Row reference/ID
     * @param bool $checkbox Show checkbox
     * @return string HTML for checkbox cell
     */
    public function renderCheckboxCell($ref, bool $checkbox = true): string
    {
        if (!$checkbox) {
            return '';
        }

        $formName = $this->form ?? 'form';
        $checkboxId = $formName . 'cb' . $ref;

        return '<td class="checkbox-cell">' .
               '<input type="checkbox" name="' . htmlspecialchars($checkboxId) . '" ' .
               'id="' . htmlspecialchars($checkboxId) . '" value="' . htmlspecialchars($ref) . '" />' .
               '</td>';
    }

    /**
     * Render a table cell
     *
     * @param string $content Cell content
     * @param string|null $extraClasses Additional CSS classes
     * @return string HTML for cell
     */
    public function renderCell(string $content, ?string $extraClasses = null): string
    {
        $classes = $extraClasses ? ' class="' . htmlspecialchars($extraClasses) . '"' : '';
        return '<td' . $classes . '>' . $content . '</td>';
    }

    /**
     * Open content area
     *
     * @param string|null $extraClasses Additional CSS classes
     * @return string HTML for opening content
     */
    public function openContent(?string $extraClasses = null): string
    {
        $classes = 'content';
        if ($extraClasses) {
            $classes .= ' ' . $extraClasses;
        }

        return '<div class="' . htmlspecialchars($classes) . '">';
    }

    /**
     * Close content area
     *
     * @return string HTML for closing content
     */
    public function closeContent(): string
    {
        return '</div>';
    }

    /**
     * Render content row with left/right columns
     *
     * @param string $left Left column content
     * @param string|null $right Right column content
     * @param bool $alternate Use alternating row class
     * @return string HTML for content row
     */
    public function renderContentRow(string $left, ?string $right, bool $alternate = false): string
    {
        if ($alternate) {
            $this->toggleRowClass();
        }

        $html = '<div class="content-row ' . htmlspecialchars($this->class) . '">';
        $html .= '<div class="content-left">' . $left . '</div>';

        if ($right !== null) {
            $html .= '<div class="content-right">' . $right . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render content title
     *
     * @param string $title Title text
     * @return string HTML for content title
     */
    public function renderContentTitle(string $title): string
    {
        return '<h3 class="content-title">' . htmlspecialchars($title) . '</h3>';
    }

    /**
     * Render heading
     *
     * @param string $title Heading text
     * @return string HTML for heading
     */
    public function renderHeading(string $title): string
    {
        return '<h2 class="heading">' . htmlspecialchars($title) . '</h2>';
    }

    /**
     * Render error heading
     *
     * @param string $title Heading text
     * @return string HTML for error heading
     */
    public function renderHeadingError(string $title): string
    {
        return '<h2 class="heading error">' . htmlspecialchars($title) . '</h2>';
    }

    /**
     * Render note box
     *
     * @param string $content Note content
     * @return string HTML for note
     */
    public function renderNote(string $content): string
    {
        return '<div class="note">' . $content . '</div>';
    }

    /**
     * Render message box
     *
     * @param string $msgLabel Message label key
     * @return string HTML for message box
     */
    public function renderMessageBox(string $msgLabel): string
    {
        $message = $this->appConfig->getString($msgLabel);
        return '<div class="message-box">' . htmlspecialchars($message) . '</div>';
    }

    /**
     * Render error content
     *
     * @param string $content Error content
     * @return string HTML for error content
     */
    public function renderContentError(string $content): string
    {
        return '<div class="content-error">' . $content . '</div>';
    }
}
