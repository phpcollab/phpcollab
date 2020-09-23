<?php
#Application name: PhpCollab
#Status page: 0

namespace phpCollab;

/**
 * Class Block
 * @package phpCollab
 */
class Block
{
    protected $help, $strings, $class, $theme, $themeImgPath, $accountTotal, $account, $sortingOrders,
        $sortingFields, $sortingArrows, $sortingStyles, $explode, $labels,
        $sitePublish, $navigation, $navigationTotal, $limit, $rowsLimit,
        $recordsTotal, $limitsNumber, $sortName, $sortingRef, $sortingDefault,
        $breadcrumbsTotal, $breadcrumbs;
    public $form, $sortingValue;

    /**
     * Block constructor.
     */
    public function __construct()
    {
        $this->sortingOrders = $GLOBALS['sortingOrders'];
        $this->sortingFields = $GLOBALS['sortingFields'];
        $this->sortingArrows = $GLOBALS['sortingArrows'];
        $this->sortingStyles = $GLOBALS['sortingStyles'];
        $this->explode = $GLOBALS['explode'];

        $this->help = $GLOBALS['help'];
        $this->strings = $GLOBALS['strings'];

        $this->class = "odd";
        $this->theme = THEME;
        $this->themeImgPath = '../themes/' . $this->theme . '/images';

        $this->sitePublish = $GLOBALS["sitePublished"];
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getRowsLimit()
    {
        return $this->rowsLimit;
    }

    /**
     * @param mixed $rowsLimit
     */
    public function setRowsLimit($rowsLimit)
    {
        $this->rowsLimit = $rowsLimit;
    }

    /**
     * @return mixed
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * @param mixed $sortName
     */
    public function setSortName($sortName)
    {
        $this->sortName = $sortName;
    }

    /**
     * @return mixed
     */
    public function getLimitsNumber()
    {
        return $this->limitsNumber;
    }

    /**
     * @param mixed $limitsNumber
     */
    public function setLimitsNumber($limitsNumber)
    {
        $this->limitsNumber = $limitsNumber;
    }

    /**
     * @return mixed
     */
    public function getRecordsTotal()
    {
        return $this->recordsTotal;
    }

    /**
     * @param mixed $recordsTotal
     */
    public function setRecordsTotal($recordsTotal)
    {
        $this->recordsTotal = $recordsTotal;
    }

    /**
     * Print tooltips
     * @param string $item Text printed in tooltip
     * @return string
     * @access public
     */
    public function printHelp(string $item)
    {
        $helpText = addslashes($this->help[$item]);
        return <<<HELP_DIV
        [<a href="javascript:void(0);"
            onmouseover="return overlib('{$helpText}',SNAPX,550);"
            onmouseout="return nd();">{$this->strings["help"]}</a>]
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
        echo '<p class="alert info note">' . $content . '</p>';
    }

    /**
     * Print standard heading
     * @param string $title Text printed in heading
     * @return void
     * @access public
     */
    public function heading(string $title)
    {
        echo '<h1 class="heading">' . stripslashes($title) . '</h1>';
    }

    /**
     * Print toggle heading (with collapse/expande arrow)
     * @param string $title Text printed in heading
     * @see block::closeToggle()
     * @access public
     */
    public function headingToggle(string $title)
    {
        if ($_COOKIE[$this->form] == "c") {
            $arrow = "closed";
        } else {
            $arrow = "open";
        }

        echo <<<HTML
<table class="headingToggle">
    <tr>
        <td><a href="javascript:showHideModule('{$this->form}','{$this->theme}')" 
               onMouseOver="showHideModuleMouseOver('{$this->form}'); return true;" 
               onMouseOut="return true;"><img alt="{$this->form}Toggle" src="{$this->themeImgPath}/module_toggle_{$arrow}.gif" alt=""></a></td>
        <td><img width="10" height="10" alt="{$this->form}tl" src="{$this->themeImgPath}/spacer.gif" alt=""></td>
        <td style="width: 100%;"><h1 class="heading">{$title}</h1></td>
    </tr>
</table>
<div id="{$this->form}">
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
        echo '<h1 class="headingError">' . $title . '</h1>';
    }

    /**
     * Print error message in table
     * @param string $content Text printed in content error table
     * @access public
     */
    public function contentError(string $content)
    {
        echo '<table class="error"><tr><td>' . $content . '</td></tr></table>';
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
                echo '<a href="' . $showall . '">' . $this->strings["show_all"] . '</a>';
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
        echo '<br/><table class="message"><tr><td>';
        echo ($msgLabel) ? $msgLabel : 'Action not allowed.';
        echo '</td></tr></table>';
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
        echo '<table class="icons"><tr>';
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
        echo <<<ICON
        <td style="text-align: left; width: 1%;"><img height="26" width="5" src="{$this->themeImgPath}/spacer.gif" alt=""></td>
        <td class="commandDesc" style="text-align: left; width: 99%;">
            <div id="{$this->form}tt" class="rel">
                <div id="{$this->form}tti" class="abs"><img height="1" width="350" src="{$this->themeImgPath}/spacer.gif" alt=""></div>
            </div>
        </td>
    </tr>
</table>

ICON;
    }

    /**
     * Open icons script
     * @see block::openPaletteScript()
     * @access public
     **/
    public function openPaletteScript()
    {
        echo <<< SCRIPT
        <script type="text/JavaScript">
        document.{$this->form}Form.buttons = [];
SCRIPT;

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
        echo "MM_updateButtons(document." . $this->form . "Form, 0);document." . $this->form . "Form.checkboxes = new Array();";
        for ($i = 0; $i < $compt; $i++) {
            echo <<<SCRIPT
document.{$this->form}Form.checkboxes[document.{$this->form}Form.checkboxes.length] = new MMCheckbox('{$values[$i]}',document.{$this->form}Form,'{$this->form}cb{$values[$i]}');
SCRIPT;
        }
        echo <<<SCRIPT
document.{$this->form}Form.tt = '{$this->form}tt';</script>
SCRIPT;
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
        echo <<<FORM
<a id="{$this->form}Anchor"></a>
<form method="POST" action="{$address}" name="{$this->form}Form" enctype="application/x-www-form-urlencoded" {$additionalAttributes}>
FORM;
        if ($csrfHandler) {
            echo <<<CSRF_INPUT
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}">
CSRF_INPUT;

        }
    }

    /**
     * Close a form used with a list block
     * @access public
     **/
    public function closeFormResults()
    {
        echo '<input name="sort_target" type="HIDDEN" value="' . $this->sortingRef . '"><input name="sort_fields" type="HIDDEN" value=""><input name="sort_order" type="HIDDEN" value=""></form>';
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
        echo "<table class='listing striped foo'><tr>";
        if ($checkbox == "true") {
            echo <<<HTML
            <th class="flooma" style="text-align: center; width: 1%">
                <a href="javascript:MM_toggleSelectedItems(document.{$this->form}Form,'{$this->theme}')"><img height="13" width="13" src="{$this->themeImgPath}/checkbox_off_16.gif" alt=""></a>
            </th>
HTML;
        } else {
            echo '<th class="moomla" style="text-align: center; width: 1%"><img style="width: 13px; height: 13px; margin: 3px 0; border: none" src="' . $this->themeImgPath . '/spacer.gif" alt=""></th>';
        }
    }

    /**
     *
     */
    public function closeResults()
    {
        echo "</table><hr />";
    }

    /**
     *
     */
    public function noresults()
    {
        echo '<div class="noItemsFound">' . $this->strings["no_items"] . '</div>';
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
        $altText = stripslashes($text);
        echo <<<palette_icon
        <td style="width: 30px;" class="commandBtn">
        <a href="javascript:var b = MM_getButtonWithName(document.{$this->form}Form, '{$this->form}{$num}'); if (b) b.click();" 
        onMouseOver="var over = MM_getButtonWithName(document.{$this->form}Form, '{$this->form}{$num}'); if (over) over.over(); return true;" 
        onMouseOut="var out = MM_getButtonWithName(document.{$this->form}Form, '{$this->form}{$num}'); if (out) out.out(); return true; "><img style="border: none;" name="{$this->form}{$num}" src="{$this->themeImgPath}/btn_{$type}_norm.gif" alt="{$altText}"></a></td>
palette_icon;
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
        $link = rtrim($link, '?');
        $link = (strpos($link, '?')) ? $link : $link . '?&';
        $text = stripslashes($text);

        echo <<<SCRIPT
    document.{$this->form}Form.buttons[
        document.{$this->form}Form.buttons.length] = new MMCommandButton(
            '{$this->form}{$num}',
            document.{$this->form}Form,
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
     * Start a table to display sheet/form
     * @see block::contentRow()
     * @access public
     **/
    public function openContent()
    {
        echo "<table class='content' cellspacing='0' cellpadding='0'>";
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
        if ($this->class == "") {
            $this->class = "odd";
        }

        if ($left != "") {
            echo "<tr class='{$this->class}'><td class='leftvalue'>" . $left . " :</td><td>" . $right . "&nbsp;</td></tr>";
        } else {
            echo "<tr class='{$this->class}'><td class='leftvalue'>&nbsp;</td><td>" . $right . "&nbsp;</td></tr>";
        }

        if ($this->class == "odd" && $altern == "true") {
            $this->class = "even";
        } else {
            if ($this->class == "even" && $altern == "true") {
                $this->class = "odd";
            }
        }
    }

    /**
     *
     */
    public function openRow()
    {
        echo "<tr>";
    }

    /**
     * @param $ref
     * @param string $checkbox
     */
    public function checkboxRow($ref, $checkbox = "true")
    {
        if ($checkbox == "true") {
            echo "<td style='text-align: center'><a href=\"javascript:MM_toggleItem(document." . $this->form . "Form, '" . $ref . "', '" . $this->form . "cb" . $ref . "','{$this->theme}')\"><img alt='' name='" . $this->form . "cb" . $ref . "' src='$this->themeImgPath/checkbox_off_16.gif' style='margin: 3px 0'></a></td>";
        } else {
            echo "<td><img height='13' width='13' src='$this->themeImgPath/spacer.gif' alt='' style='margin: 3px 0'></td>";
        }
    }

    /**
     * @param $content
     */
    public function cellRow($content)
    {
        echo "<td>$content</td>";
    }

    /**
     *
     */
    public function closeRow()
    {
        echo "</tr>";
    }

    /**
     * @param $title
     */
    public function contentTitle($title)
    {
        echo "<tr><th colspan='2'>" . $title . "</th></tr>";
    }

    /**
     *
     */
    public function closeContent()
    {
        echo "</table><hr />";
    }

    /**
     *
     */
    public function closeForm()
    {
        echo "</form>";
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
        echo "<p id='navigation'>";
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
            if ($items - 1 != $i) {
                echo "&nbsp;&nbsp;";
            }
        }
        echo "</p>";
    }

    /**
     *
     */
    public function openAccount()
    {
        echo "<p id='account'>";
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
    public function closeaccount()
    {
        $items = $this->accountTotal;
        for ($i = 0; $i < $items; $i++) {
            echo $this->account[$i];
            if ($items - 1 != $i) {
                echo " ";
            }
        }
        echo "</p>";
    }

    /**
     * @param $url
     * @param $label
     * @param $type
     * @return string
     */
    public function buildLink($url, $label, $type)
    {
        if (!empty($url)) {

            if ($type == "in") {
                return '<a href="' . $url . '">' . $label . '</a>';
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
