<?php
#Application name: PhpCollab
#Status page: 0

namespace phpCollab;

use phpCollab\Services\PaginationService;
use phpCollab\Services\SortingService;
use phpCollab\Services\TableRenderer;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class Block - Facade for table/list rendering
 *
 * Refactored to use dedicated services for different responsibilities:
 * - SortingService: Handles sorting logic
 * - PaginationService: Handles pagination logic
 * - TableRenderer: Handles HTML table rendering
 *
 * This class now acts as a facade, maintaining backward compatibility
 * while delegating to focused, testable services.
 *
 * @package phpCollab
 */
class Block
{
    protected $appConfig;
    protected SortingService $sortingService;
    protected PaginationService $paginationService;
    protected TableRenderer $tableRenderer;

    // Legacy properties maintained for BC
    protected $class, $theme, $themeImgPath, $accountTotal, $account, $sortingOrders,
        $sortingFields, $sortingArrows, $sortingStyles, $explode, $labels,
        $sitePublish, $navigation, $navigationTotal, $limit, $rowsLimit,
        $recordsTotal, $limitsNumber, $sortName, $sortingRef, $sortingDefault,
        $breadcrumbsTotal, $breadcrumbs;
    public $form, $sortingValue;

    /**
     * Block constructor.
     *
     * @param AppConfig|null $appConfig Application configuration (optional for BC)
     * @param SortingService|null $sortingService Sorting service (optional, auto-created if null)
     * @param PaginationService|null $paginationService Pagination service (optional, auto-created if null)
     * @param TableRenderer|null $tableRenderer Table renderer (optional, auto-created if null)
     */
    public function __construct(
        AppConfig $appConfig = null,
        SortingService $sortingService = null,
        PaginationService $paginationService = null,
        TableRenderer $tableRenderer = null
    ) {
        // ✅ Support DI while maintaining backward compatibility
        if ($appConfig === null) {
            $appConfig = AppConfig::fromGlobals();
        }
        $this->appConfig = $appConfig;

        // ✅ Create services if not injected (for BC)
        $this->sortingService = $sortingService ?? new SortingService($appConfig);
        $this->paginationService = $paginationService ?? new PaginationService($appConfig);
        $this->tableRenderer = $tableRenderer ?? new TableRenderer($appConfig, $this->sortingService);

        // Initialize legacy properties for BC
        $this->sortingOrders = $appConfig->getSortingOrders();
        $this->sortingFields = $appConfig->getSortingFields();
        $this->sortingArrows = $appConfig->getSortingArrows();
        $this->sortingStyles = $appConfig->getSortingStyles();
        $this->explode = $appConfig->getExplode();

        $this->class = "odd";
        $this->theme = THEME;
        $this->themeImgPath = '../themes/' . $this->theme . '/images';

        $this->sitePublish = $appConfig->isSitePublished();
    }

    /**
     * Get current limit offset
     * @return mixed
     */
    public function getLimit()
    {
        // Delegate to PaginationService
        return $this->paginationService->getLimit() ?? $this->limit;
    }

    /**
     * Set current limit offset
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        // Update both legacy property and service
        $this->limit = $limit;
        $this->paginationService->setLimit($limit);
    }

    /**
     * Get rows per page limit
     * @return mixed
     */
    public function getRowsLimit()
    {
        // Delegate to PaginationService
        return $this->paginationService->getRowsLimit() ?? $this->rowsLimit;
    }

    /**
     * Set rows per page limit
     * @param mixed $rowsLimit
     */
    public function setRowsLimit($rowsLimit)
    {
        // Update both legacy property and service
        $this->rowsLimit = $rowsLimit;
        $this->paginationService->setRowsLimit($rowsLimit);
    }

    /**
     * Get sort name
     * @return mixed
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * Set sort name
     * @param mixed $sortName
     */
    public function setSortName($sortName)
    {
        $this->sortName = $sortName;
    }

    /**
     * Get limits number
     * @return mixed
     */
    public function getLimitsNumber()
    {
        // Delegate to PaginationService
        return $this->paginationService->getLimitsNumber() ?? $this->limitsNumber;
    }

    /**
     * Set limits number
     * @param mixed $limitsNumber
     */
    public function setLimitsNumber($limitsNumber)
    {
        // Update both legacy property and service
        $this->limitsNumber = $limitsNumber;
        $this->paginationService->setLimitsNumber($limitsNumber);
    }

    /**
     * Get total number of records
     * @return mixed
     */
    public function getRecordsTotal()
    {
        // Delegate to PaginationService
        return $this->paginationService->getRecordsTotal() ?? $this->recordsTotal;
    }

    /**
     * Set total number of records
     * @param mixed $recordsTotal
     */
    public function setRecordsTotal($recordsTotal)
    {
        // Update both legacy property and service
        $this->recordsTotal = $recordsTotal;
        $this->paginationService->setRecordsTotal($recordsTotal);
    }

    /**
     * Print tooltips
     * @param string $item Text printed in tooltip
     * @param string|null $additionalPrams
     * @return string
     * @access publics
     */
    public function printHelp(string $item, string $additionalPrams = null)
    {
        $helpText = addslashes($this->appConfig->getHelpItem($item));
        $additionalPrams = (is_null($additionalPrams) ? '' : ',' . $additionalPrams);
        return <<<HELP_DIV
        <a href="javascript:void(0);"
            onmouseover="return overlib('{$helpText}',SNAPX,550,CSSCLASS,TEXTFONTCLASS,'overDivFontClass',CAPTIONFONTCLASS,' overDivCapFontClass',BGCLASS,'overDivBgClass',FGCLASS,'overDivFgClass'{$additionalPrams});"
            onmouseout="return nd();"><i class="icon-help fa fa-question-circle fa-lg" title="{$this->appConfig->getString("help")}"></i></a>
HELP_DIV;

    }

    /**
     * Add a note
     * @param string $content Text printed in note
     * @return void
     * @access public
     */
    public function note(string $content)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderNote($content);
    }

    /**
     * Print standard heading
     * @param string $title Text printed in heading
     * @return void
     * @access public
     */
    public function heading(string $title)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderHeading(stripslashes($title));
    }

    /**
     * Print toggle heading (with collapse/expanded arrow)
     * @param string $title Text printed in heading
     * @param string|null $toggleState
     * @see block::closeToggle()
     * @access public
     */
    public function headingToggle(string $title, string $toggleState = null)
    {
        $toggleClass = null;
        if ($toggleState == "collapse") {
            $arrow = "closed";
            $toggleClass = 'toggle-hide';
        } else {
            $arrow = "open";
        }

        echo <<<HTML
<div class="headingToggle">
    <a href="javascript:showHideModule('{$this->form}','{$this->theme}')" title="Expand">
       <img id="{$this->form}Toggle" alt="{$this->form}Toggle" src="{$this->themeImgPath}/module_toggle_{$arrow}.gif" /></a>
    <span class="heading">{$title}</span>
</div>
<div id="{$this->form}" class="{$toggleClass}">
HTML;
    }

    /**
     * Close toggle block
     * @see block::headingToggle()
     * @access public
     **/
    public function closeToggle()
    {
        echo '</div>';
    }

    /**
     * Print error heading
     * @param string $title Text printed in heading
     * @access public
     */
    public function headingError(string $title)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderHeadingError($title);
    }

    /**
     * Print error message in table
     * @param string $content Text printed in content error table
     * @access public
     */
    public function contentError(string $content)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderContentError($content);
    }

    /**
     * @param $current
     * @return string
     */
    public function returnLimit($current)
    {
        $sanitized = filter_var($current, FILTER_SANITIZE_NUMBER_INT);

        if ($sanitized == "") {
            $limitValue = "0";
        } else {
            $limitValue = $_GET["section" . $sanitized];
            $limitValue = (empty($limitValue)) ? "0" : $limitValue;
        }
        return $limitValue;
    }

    /**
     * Print page-per-page in bottom of list block
     * @param string $current Limit number for concerned block
     * @param string $total Total limits number
     * @param string $showall Link to page which display all records, with parameters
     * @param string $parameters Optional parameters to transmit between pages
     * @access public
     */
    public function limitsFooter(string $current, string $total, string $showall, string $parameters)
    {
        if ($this->rowsLimit < $this->recordsTotal) {
            echo '<table class="pagination"><tr><td nowrap class="footerCell">';

            $nbpages = ceil($this->recordsTotal / $this->rowsLimit);
            $j = 0;
            for ($i = 1; $i <= $nbpages; $i++) {
                if ($this->limit == $j) {
                    echo "<strong>$i</strong>";
                } else {
                    echo '<a href="?';
                    for ($k = 1; $k <= $total; $k++) {
                        if ($k != $current) {
                            echo "&section$k={$k}";
                        } else {
                            if ($k == $current) {
                                echo "&section$k=$j";
                            }
                        }
                    }
                    echo '&' . $parameters . '#' . $this->form . 'Anchor">' . $i . '</a>';
                }
                $j = $j + $this->rowsLimit;


            }
            echo '</td><td nowrap class="footerCell">';
            if ($showall != "") {
                echo '<a href="' . $showall . '">' . $this->appConfig->getString("show_all") . '</a>';
            }
            echo <<<HTML
    </td>
    </tr>
    </table>
HTML;

        }

    }

    /**
     * Print Message table
     * @param string $msgLabel Text built with messages.php
     * @access public
     */
    public function messageBox(string $msgLabel)
    {
        // ✅ Delegate to TableRenderer
        $msgLabel = ($msgLabel) ? $msgLabel : 'Action not allowed.';
        echo $this->tableRenderer->renderMessageBox($msgLabel);
    }

    /**
     * Open icons table
     * @see block::closePaletteIcon()
     * @see block::paletteIcon()
     * @see block::paletteScript()
     * @access public
     **/
    public function openPaletteIcon()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderOpenPaletteIcon();
    }

    /**
     * Close icons table
     * @see block::openPaletteIcon()
     * @see block::paletteIcon()
     * @see block::paletteScript()
     * @access public
     **/
    public function closePaletteIcon()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderClosePaletteIcon($this->form);
    }

    /**
     * Open icons script
     * @see block::openPaletteScript()
     * @access public
     **/
    public function openPaletteScript()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderOpenPaletteScript($this->form);
    }

    /**
     * Close icons script
     * @param $compt
     * @param $values
     * @see block::closePaletteScript()
     * @access public
     **/
    public function closePaletteScript($compt, $values)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderClosePaletteScript($this->form, $compt, $values);
    }

    /**
     * Define sorting to apply on a list block
     * @param string $sortingRef Row reference in sorting table
     * @param mixed $sortingValue Row value in sorting table
     * @param string $sortingDefault Default sorting value
     * @param array $sortingFields Array with sorted fields on each column
     * @access public
     */
    public function sorting(string $sortingRef, $sortingValue, string $sortingDefault, array $sortingFields)
    {
        if ($sortingRef != "") {
            $this->sortingRef = $sortingRef;
        }
        if ($sortingValue != "") {

            $this->sortingValue = $sortingValue;
        }
        if ($sortingDefault != "") {
            $this->sortingDefault = $sortingDefault;
        }
        if ($sortingFields != "") {
            $this->sortingFields = $sortingFields;
        }

        if (isset($this->sortingValue) != "") {
            $explode = explode(" ", $this->sortingValue);
        } else {
            $this->sortingValue = $this->sortingDefault;
            $explode = explode(" ", $this->sortingValue);
        }

        $sortingFieldsCount = count($this->sortingFields);
        for ($i = 0; $i < $sortingFieldsCount; $i++) {
            if ($this->sortingFields[$i] == $explode[0] && $explode[1] == "DESC") {
                $this->sortingOrders[$i] = "ASC";
                $this->sortingArrows[$i] = '&#160;<img border="0" src="' . $this->themeImgPath . '/icon_sort_za.gif" alt="" width="11" height="11">';
                $this->sortingStyles[$i] = "active";
            } else {
                if ($this->sortingFields[$i] == $explode[0] && $explode[1] == "ASC") {
                    $this->sortingOrders[$i] = "DESC";
                    $this->sortingArrows[$i] = '&#160;<img border="0" src="' . $this->themeImgPath . '/icon_sort_az.gif" alt="" width="11" height="11">';
                    $this->sortingStyles[$i] = "active";
                } else {
                    $this->sortingOrders[$i] = "ASC";
                    $this->sortingArrows[$i] = "";
                    $this->sortingStyles[$i] = "";
                }
            }
        }
    }

    /**
     * Open a standard form
     * @param string $address Action form value
     * @param null $additionalAttributes
     * @param CsrfHandler|null $csrfHandler
     * @see block::closeFormResults()
     * @see block::closeForm()
     * @access public
     */
    public function openForm(string $address, $additionalAttributes = null, CsrfHandler $csrfHandler = null)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderOpenForm($this->form, $address, $additionalAttributes, $csrfHandler);
    }

    /**
     * Close a form used with a list block
     * @access public
     **/
    public function closeFormResults()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderCloseFormResults($this->sortingRef);
    }

    /**
     * Define column labels in a list block
     * @param array $labels Array with labels strings
     * @param boolean $published Show/hide a published column
     * @param bool|string $sorting Disable sorting
     * @param array|string $sortingOff Array with label number (from $labels) and order (ASC/DESC)
     * @access public
     */
    public function labels(array $labels, bool $published, $sorting = "true", $sortingOff = "")
    {
        $sortingFields = $this->sortingFields;
        $sortingOrders = $this->sortingOrders;
        $sortingArrows = $this->sortingArrows;
        $sortingStyles = $this->sortingStyles;

        if ($this->sitePublish == "false" && $published == "true") {
            $comptLabels = count($labels) - 1;
        } else {
            $comptLabels = count($labels);
        }
        for ($i = 0; $i < $comptLabels; $i++) {
            if ($sorting == "true") {
                if (isset($sortingFields) && array_key_exists($i, $sortingFields) && $sortingFields[$i] !== 'none') {
                    echo <<<HTML
<th nowrap class="{$sortingStyles[$i]}">
    <a href="javascript:document.{$this->form}Form.sort_target.value='{$this->sortingRef}';
        document.{$this->form}Form.sort_fields.value='{$sortingFields[$i]}';
        document.{$this->form}Form.sort_order.value='{$sortingOrders[$i]}';
        document.{$this->form}Form.submit();" 
        onMouseOver="return true;" onMouseOut="return true">{$labels[$i]}{$sortingArrows[$i]}</a></th>
HTML;
                } else {
                    echo "<th nowrap>{$labels[$i]}</th>";
                }
            } else {
                $sortingArrow = null;
                if ($sortingOff[1] == "ASC") {
                    $sortingArrow = "&#160;<img src='$this->themeImgPath/icon_sort_az.gif' alt='' width='11' height='11'>";

                } else {
                    if ($sortingOff[1] == "DESC") {
                        $sortingArrow = "&#160;<img src='$this->themeImgPath/icon_sort_za.gif' alt='' width='11' height='11'>";
                    }
                }
                if ($i == $sortingOff[0]) {
                    echo "<th nowrap class='active'>" . $labels[$i] . "$sortingArrow";
                } else {
                    echo "<th nowrap>{$labels[$i]}";
                }
            }
        }

        echo "</tr>";
    }

    /**
     * Open results list
     * @param string $checkbox Disable checkbox display
     * @access public
     */
    public function openResults($checkbox = "true")
    {
        // ✅ Delegate to TableRenderer
        $checkboxBool = ($checkbox === "true" || $checkbox === true);

        // Update TableRenderer with current form for backward compatibility
        $this->tableRenderer->setForm($this->form);

        echo $this->tableRenderer->openResults($checkboxBool, $this->form, $this->theme);
    }

    /**
     *
     */
    public function closeResults()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->closeResults();
    }

    /**
     *
     */
    public function noresults()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderNoResults();
    }

    /**
     * Display an icon (html)
     * @param integer $num Icon number
     * @param string $type Icon name (used in graphic file name)
     * @param string $text Text used in info-tip
     * @see block::openPaletteIcon()
     * @access public
     */
    public function paletteIcon(int $num, string $type, string $text)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderPaletteIcon($this->form, $num, $type, $text);
    }

    /**
     * Display an icon (JavaScript)
     * @param integer $num Icon number
     * @param string $type Icon name (used in graphic file name)
     * @param string $link path to link to
     * @param string $options JavaScript options enableOnNoSelection, enableOnSingleSelection, enableOnMultipleSelection
     * @param string $text Text used in roll-over layer
     * @see block::openPaletteIcon()
     * @access public
     */
    public function paletteScript(int $num, string $type, string $link, string $options, string $text)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderPaletteScript($this->form, $num, $type, $link, $options, $text);
    }

    /**
     * Start a table to display sheet/form
     * @see block::contentRow()
     * @access public
     **/
    public function openContent($extraClasses = null)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->openContent($extraClasses);
    }

    /**
     * Display a table line in sheet/form
     * @param string $left Text in left cell
     * @param string|null $right Text in right cell
     * @param string $altern Option to altern background color
     * @access public
     */
    public function contentRow(string $left, ?string $right, $altern = "false")
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderContentRow($left, $right, $altern);

        // Update legacy class property for backward compatibility
        $this->class = $this->tableRenderer->getRowClass();
    }

    /**
     *
     */
    public function openRow()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->openRow();
    }

    /**
     * @param $ref
     * @param string $checkbox
     */
    public function checkboxRow($ref, $checkbox = "true")
    {
        // ✅ Delegate to TableRenderer
        $checkboxBool = ($checkbox === "true" || $checkbox === true);

        // Update TableRenderer with current form for backward compatibility
        $this->tableRenderer->setForm($this->form);

        echo $this->tableRenderer->renderCheckboxCell($ref, $checkboxBool);
    }

    /**
     * @param $content
     */
    public function cellRow($content)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderCellRow($content);
    }

    /**
     *
     */
    public function closeRow()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->closeRow();
    }

    /**
     * @param $title
     */
    public function contentTitle($title)
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderContentTitle($title);
    }

    /**
     *
     */
    public function closeContent()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->closeContent();
    }

    /**
     *
     */
    public function closeForm()
    {
        // ✅ Delegate to TableRenderer
        echo $this->tableRenderer->renderCloseForm();
    }

    /**
     *
     */
    public function openBreadcrumbs()
    {
        echo "<p class='breadcrumbs'>";
    }

    /**
     * @param $content
     */
    public function itemBreadcrumbs($content)
    {
        if ($this->breadcrumbsTotal == "") {
            $this->breadcrumbsTotal = 0;
        }
        $this->breadcrumbs[$this->breadcrumbsTotal] = stripslashes($content);
        $this->breadcrumbsTotal = $this->breadcrumbsTotal + 1;
    }

    /**
     *
     */
    public function closeBreadcrumbs()
    {
        $items = $this->breadcrumbsTotal;
        for ($i = 0; $i < $items; $i++) {
            echo $this->breadcrumbs[$i];
            if ($items - 1 != $i) {
                echo " / ";
            }
        }
        echo "</p>";
    }

    /**
     *
     */
    public function openNavigation()
    {
        echo "<nav>";
    }

    /**
     * @param $content
     */
    public function itemNavigation($content)
    {
        if ($this->navigationTotal == "") {
            $this->navigationTotal = 0;
        }
        $this->navigation[$this->navigationTotal] = $content;
        $this->navigationTotal = $this->navigationTotal + 1;
    }

    /**
     *
     */
    public function closeNavigation()
    {
        $items = $this->navigationTotal;
        for ($i = 0; $i < $items; $i++) {
            echo $this->navigation[$i];
        }
        echo "</nav>";
    }

    /**
     *
     */
    public function openAccount(Session $session)
    {
        echo <<<ACCOUNT_PROFILE
        <div id="account" class="dropdown">
          <div class="accountButton">{$session->get("name")}</div>
          <div class="dropdown-content">
ACCOUNT_PROFILE;
    }

    /**
     * @param $content
     */
    public function itemAccount($content)
    {
        if ($this->accountTotal == "") {
            $this->accountTotal = 0;
        }
        $this->account[$this->accountTotal] = $content;
        $this->accountTotal = $this->accountTotal + 1;
    }

    /**
     *
     */
    public function closeAccount()
    {
        echo <<<DROPDOWN
  </div>
</div>
DROPDOWN;

    }

    /**
     * @param $url
     * @param $label
     * @param $type
     * @return string
     */
    public function buildLink($url, $label, $type, $class = null)
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
