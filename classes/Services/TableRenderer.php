<?php

namespace phpCollab\Services;

use phpCollab\AppConfig;
use phpCollab\Util;

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

    /**
     * Render opening icon/palette table
     *
     * @return string HTML for opening icons table
     */
    public function renderOpenPaletteIcon(): string
    {
        return '<table class="icons"><tr>';
    }

    /**
     * Render closing icon/palette table
     *
     * @param string $formName Form name for tooltip div IDs
     * @return string HTML for closing icons table
     */
    public function renderClosePaletteIcon(string $formName): string
    {
        return <<<ICON
        <td style="text-align: left; width: 1%;"><img height="26" width="5" src="{$this->themeImgPath}/spacer.gif" alt=""></td>
        <td class="commandDesc" style="text-align: left; width: 99%;">
            <div id="{$formName}tt" class="rel">
                <div id="{$formName}tti" class="abs"><img height="1" width="350" src="{$this->themeImgPath}/spacer.gif" alt=""></div>
            </div>
        </td>
    </tr>
</table>

ICON;
    }

    /**
     * Render individual palette icon (HTML)
     *
     * @param string $formName Form name for JavaScript references
     * @param int $num Icon number
     * @param string $type Icon type (for image file name)
     * @param string $text Alt text for icon
     * @return string HTML for palette icon
     */
    public function renderPaletteIcon(string $formName, int $num, string $type, string $text): string
    {
        $altText = stripslashes($text);
        return <<<palette_icon
        <td style="width: 30px;" class="commandBtn">
        <a href="javascript:var b = MM_getButtonWithName(document.{$formName}Form, '{$formName}{$num}'); if (b) b.click();"
        onMouseOver="var over = MM_getButtonWithName(document.{$formName}Form, '{$formName}{$num}'); if (over) over.over(); return true;"
        onMouseOut="var out = MM_getButtonWithName(document.{$formName}Form, '{$formName}{$num}'); if (out) out.out(); return true; "><img style="border: none;" name="{$formName}{$num}" src="{$this->themeImgPath}/btn_{$type}_norm.gif" alt="{$altText}"></a></td>
palette_icon;
    }

    /**
     * Render opening palette script tag
     *
     * @param string $formName Form name for JavaScript
     * @return string HTML/JavaScript for opening palette script
     */
    public function renderOpenPaletteScript(string $formName): string
    {
        return <<<SCRIPT
        <script type="text/JavaScript">
        document.{$formName}Form.buttons = [];
SCRIPT;
    }

    /**
     * Render individual palette script (JavaScript button registration)
     *
     * @param string $formName Form name for JavaScript
     * @param int $num Icon number
     * @param string $type Icon type (for image files)
     * @param string $link Link URL
     * @param string $options JavaScript options
     * @param string $text Tooltip text
     * @return string JavaScript for palette button
     */
    public function renderPaletteScript(string $formName, int $num, string $type, string $link, string $options, string $text): string
    {
        $link = rtrim($link, '?');
        $link = (strpos($link, '?')) ? $link : $link . '?&';
        $text = stripslashes($text);

        return <<<SCRIPT
    document.{$formName}Form.buttons[
        document.{$formName}Form.buttons.length] = new MMCommandButton(
            '{$formName}{$num}',
            document.{$formName}Form,
            '{$link}',
            '{$this->themeImgPath}/btn_{$type}_norm.gif',
            '{$this->themeImgPath}/btn_{$type}_over.gif',
            '{$this->themeImgPath}/btn_{$type}_down.gif',
            '{$this->themeImgPath}/btn_{$type}_dim.gif',
            {$options},
            '',
            "{$text}",
            false,
            ''
        );
SCRIPT;
    }

    /**
     * Render closing palette script
     *
     * @param string $formName Form name for JavaScript
     * @param int $compt Count of items
     * @param array $values Array of values for checkboxes
     * @return string JavaScript for closing palette script
     */
    public function renderClosePaletteScript(string $formName, int $compt, array $values): string
    {
        $html = "MM_updateButtons(document.{$formName}Form, 0);document.{$formName}Form.checkboxes = new Array();";

        for ($i = 0; $i < $compt; $i++) {
            $html .= <<<SCRIPT

document.{$formName}Form.checkboxes[document.{$formName}Form.checkboxes.length] = new MMCheckbox('{$values[$i]}',document.{$formName}Form,'{$formName}cb{$values[$i]}');
SCRIPT;
        }

        $html .= <<<SCRIPT

document.{$formName}Form.tt = '{$formName}tt';</script>
SCRIPT;

        return $html;
    }

    /**
     * Render opening breadcrumbs tag
     *
     * @return string HTML for opening breadcrumbs
     */
    public function renderOpenBreadcrumbs(): string
    {
        return "<p class='breadcrumbs'>";
    }

    /**
     * Render breadcrumbs items with separator
     *
     * @param array $items Array of breadcrumb content
     * @return string HTML for breadcrumbs items
     */
    public function renderBreadcrumbsItems(array $items): string
    {
        $html = '';
        $total = count($items);
        for ($i = 0; $i < $total; $i++) {
            $html .= stripslashes($items[$i]);
            if ($total - 1 != $i) {
                $html .= " / ";
            }
        }
        return $html;
    }

    /**
     * Render closing breadcrumbs tag
     *
     * @return string HTML for closing breadcrumbs
     */
    public function renderCloseBreadcrumbs(): string
    {
        return "</p>";
    }

    /**
     * Render opening navigation tag
     *
     * @return string HTML for opening navigation
     */
    public function renderOpenNavigation(): string
    {
        return "<nav>";
    }

    /**
     * Render navigation items
     *
     * @param array $items Array of navigation content
     * @return string HTML for navigation items
     */
    public function renderNavigationItems(array $items): string
    {
        return implode('', $items);
    }

    /**
     * Render closing navigation tag
     *
     * @return string HTML for closing navigation
     */
    public function renderCloseNavigation(): string
    {
        return "</nav>";
    }

    /**
     * Render opening account dropdown
     *
     * @param string $userName User name to display
     * @return string HTML for opening account dropdown
     */
    public function renderOpenAccount(string $userName): string
    {
        return <<<ACCOUNT_PROFILE
        <div id="account" class="dropdown">
          <div class="accountButton">{$userName}</div>
          <div class="dropdown-content">
ACCOUNT_PROFILE;
    }

    /**
     * Render account items
     *
     * @param array $items Array of account menu items
     * @return string HTML for account items
     */
    public function renderAccountItems(array $items): string
    {
        return implode('', $items);
    }

    /**
     * Render closing account dropdown
     *
     * @return string HTML for closing account dropdown
     */
    public function renderCloseAccount(): string
    {
        return <<<DROPDOWN
  </div>
</div>
DROPDOWN;
    }

    /**
     * Build HTML link based on type
     *
     * @param string $url URL for the link
     * @param string $label Link text
     * @param string $type Link type (in, out, mail, icone, inblank, powered)
     * @param string|null $class Optional CSS class
     * @return string HTML link
     */
    public function buildLink(string $url, string $label, string $type, ?string $class = null): string
    {
        if (!empty($url)) {
            if ($type == "in") {
                return '<a href="' . $url . '" class="'. $class  .'">' . $label . '</a>';
            } else {
                if ($type == "icone") {
                    return '<a href="' . $url . '&"><img src="../interface/icones/' . $label . '" alt=""></a>';
                } else {
                    if ($type == "inblank") {
                        return '<a href="' . $url . '&" target="_blank">' . $label . '</a>';
                    } else {
                        if ($type == "powered") {
                            return 'Powered by <a href="' . $url . '" target="_blank">' . $label . '</a>';
                        } else {
                            if ($type == "out") {
                                // Verify correct urltyping
                                if (substr($url, 0, 4) != 'http') {
                                    // Add default http on it
                                    $url = "http://" . $url;
                                }
                                return "<a href='$url' target='_blank'>$label</a>";
                            } else {
                                if ($type == "mail") {
                                    return "<a href='mailto:$url'>$label</a>";
                                }
                            }
                        }
                    }
                }
                return '';
            }
        }
        if (!empty($label)) {
            return $label;
        }
        return Util::doubleDash();
    }
}
