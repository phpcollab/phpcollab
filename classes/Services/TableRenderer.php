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
     * @param string|null $formName Form name for checkbox toggle
     * @param string|null $theme Theme name for images
     * @return string HTML for opening results table
     */
    public function openResults(bool $checkbox = true, ?string $formName = null, ?string $theme = null): string
    {
        $this->resetRowClass();

        $html = "<table class='listing striped foo'><tr>";

        if ($checkbox) {
            $formName = $formName ?? $this->form ?? 'form';
            $theme = $theme ?? $this->theme;

            $html .= <<<HTML
            <th class="flooma" style="text-align: center; width: 1%">
                <a href="javascript:MM_toggleSelectedItems(document.{$formName}Form,'{$theme}')"><img height="13" width="13" src="{$this->themeImgPath}/checkbox_off_16.gif" alt=""></a>
            </th>
HTML;
        } else {
            $html .= '<th class="moomla" style="text-align: center; width: 1%"><img style="width: 13px; height: 13px; margin: 3px 0; border: none" src="' . $this->themeImgPath . '/spacer.gif" alt=""></th>';
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
        return "</table><hr />";
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
        $formName = $this->form ?? 'form';

        if ($checkbox) {
            return "<td style='text-align: center'><a href=\"javascript:MM_toggleItem(document." . $formName . "Form, '" . $ref . "', '" . $formName . "cb" . $ref . "','{$this->theme}')\"><img alt='' name='" . $formName . "cb" . $ref . "' src='$this->themeImgPath/checkbox_off_16.gif' style='margin: 3px 0'></a></td>";
        } else {
            return "<td><img height='13' width='13' src='$this->themeImgPath/spacer.gif' alt='' style='margin: 3px 0'></td>";
        }
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
        return '<table class="content ' . $extraClasses .'">';
    }

    /**
     * Close content area
     *
     * @return string HTML for closing content
     */
    public function closeContent(): string
    {
        return "</table><hr />";
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
        // Initialize class if empty
        if (empty($this->class)) {
            $this->class = "odd";
        }

        // Build the row HTML
        if ($left != "") {
            $html = "<tr class='{$this->class}'><td class='leftvalue'>" . $left . " :</td><td>" . $right . "&nbsp;</td></tr>";
        } else {
            $html = "<tr class='{$this->class}'><td class='leftvalue'>&nbsp;</td><td>" . $right . "&nbsp;</td></tr>";
        }

        // Toggle class for alternating rows if requested
        if ($alternate === "true" || $alternate === true) {
            $this->toggleRowClass();
        }

        return $html;
    }

    /**
     * Render a table cell with content
     *
     * @param string $content Cell content
     * @return string HTML for table cell
     */
    public function renderCellRow(string $content): string
    {
        return '<td>' . $content . '</td>';
    }

    /**
     * Render content title row (table header spanning columns)
     *
     * @param string $title Title text
     * @return string HTML for content title row
     */
    public function renderContentTitle(string $title): string
    {
        return "<tr><th colspan='2'>" . $title . "</th></tr>";
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

    /**
     * Render opening form tag
     *
     * @param string $formName Form name for ID and name attributes
     * @param string $address Action URL
     * @param string|null $additionalAttributes Additional HTML attributes
     * @param mixed|null $csrfHandler CSRF handler for token generation
     * @return string HTML for opening form tag
     */
    public function renderOpenForm(string $formName, string $address, ?string $additionalAttributes = null, $csrfHandler = null): string
    {
        $html = <<<FORM
<form id="{$formName}Anchor" method="POST" action="{$address}" name="{$formName}Form" enctype="application/x-www-form-urlencoded" {$additionalAttributes} class="content-section">
FORM;

        if ($csrfHandler) {
            $html .= <<<CSRF_INPUT

    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
CSRF_INPUT;
        }

        return $html;
    }

    /**
     * Render closing form tag
     *
     * @return string HTML for closing form tag
     */
    public function renderCloseForm(): string
    {
        return '</form>';
    }

    /**
     * Render closing form tag with hidden sorting fields
     *
     * @param string|null $sortingRef Sorting reference field value
     * @return string HTML for closing form with hidden fields
     */
    public function renderCloseFormResults(?string $sortingRef = null): string
    {
        $sortingRefValue = $sortingRef ?? '';
        return '<input name="sort_target" type="HIDDEN" value="' . htmlspecialchars($sortingRefValue) . '"><input name="sort_fields" type="HIDDEN" value=""><input name="sort_order" type="HIDDEN" value=""></form>';
    }
}
