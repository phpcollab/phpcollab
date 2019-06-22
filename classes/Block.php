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
    protected $help, $strings, $iconWidth, $iconHeight, $bgColor, $fgColor,
        $oddColor, $evenColor, $highlightOn, $class, $highlightOff, $theme,
        $pathImg, $themeImgPath, $accountTotal, $account, $sortingOrders,
        $sortingFields, $sortingArrows, $sortingStyles, $explode, $labels,
        $sitePublish, $navigation, $navigationTotal, $limit, $rowsLimit,
        $recordsTotal, $limitsNumber, $sortName, $sortingRef, $sortingDefault,
        $breadcrumbsTotal, $breadcrumbs;
    public $form, $sortingValue;

    /**
     *
     */
    public function __construct()
    {
        $this->sortingOrders = $GLOBALS['sortingOrders'];
        $this->sortingFields = $GLOBALS['sortingFields'];
        $this->sortingArrows = $GLOBALS['sortingArrows'];
        $this->sortingStyles = $GLOBALS['ortingStyles'];
        $this->explode = $GLOBALS['explode'];

        $this->help = $GLOBALS['help'];
        $this->strings = $GLOBALS['strings'];

        $this->iconWidth = "23";
        $this->iconHeight = "23";
        $this->bgColor = "#5B7F93";
        $this->fgColor = "#C4D3DB";
        $this->oddColor = "#F5F5F5";
        $this->evenColor = "#EFEFEF";
        $this->highlightOn = "#DEE7EB";

        $this->class = "odd";
        $this->highlightOff = $this->oddColor;
        $this->theme = THEME;
        $this->pathImg = "../themes/";
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
     * @return string
     */
    public function getHighlightOn()
    {
        return $this->highlightOn;
    }

    /**
     * @return string
     */
    public function getHighlightOff()
    {
        return $this->highlightOff;
    }

    /**
     * @return string
     */
    public function getFgColor()
    {
        return $this->fgColor;
    }

    /**
     * @return string
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * @return string
     */
    public function getThemeImgPath()
    {
        return $this->themeImgPath;
    }

    /**
     * @return string
     */
    public function getOddColor()
    {
        return $this->oddColor;
    }

    /**
     * @return string
     */
    public function getEvenColor()
    {
        return $this->evenColor;
    }


    /**
     * Print tooltips
     * @param string $item Text printed in tooltip
     * @access public
     * @return string
     **/
    public function printHelp($item)
    {
        return ' [<a href="javascript:void(0);" onmouseover="return overlib(\'' .
            addslashes($this->help[$item]) . '\',SNAPX,550,BGCOLOR,\'' . $this->bgColor .
            '\',FGCOLOR,\'' . $this->fgColor . '\');" onmouseout="return nd();">' .
            $this->strings["help"] . '</a>]';
    }

    /**
     * Add a note
     * @param string $content Text printed in note
     * @access public
     * @return void
     **/
    public function note($content)
    {
        echo '<p class="note">' . $content . '</p>';
        return;
    }

    /**
     * Print standard heading
     * @param string $title Text printed in heading
     * @access public
     * @return void
     **/
    public function heading($title)
    {
        echo '<h1 class="heading">' . stripslashes($title) . '</h1>';
        return;
    }

    /**
     * Print toggle heading (with collapse/expande arrow)
     * @param string $title Text printed in heading
     * @see block::closeToggle()
     * @access public
     **/
    public function headingToggle($title)
    {
        if ($_COOKIE[$this->form] == "c") {
            $arrow = "closed";
        } else {
            $arrow = "open";
        }

        echo <<<HTML
<table cellspacing="0" cellpadding="0" border="0">
<tr>
<td><a href="javascript:showHideModule('{$this->form}','{$this->theme}')" 
       onMouseOver="showHideModuleMouseOver('{$this->form}'); return true;" 
       onMouseOut="window.status=''; return true;"><img name="{$this->form}Toggle" border="0" src="{$this->themeImgPath}/module_toggle_{$arrow}.gif" alt=""></a></td>
<td><img width="10" height="10" name="{$this->form}tl" src="{$this->themeImgPath}/spacer.gif" alt=""></td>
<td width="100%"><h1 class="heading">{$title}</h1></td>
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
     **/
    public function headingError($title)
    {
        echo '<h1 class="headingError">' . $title . '</h1>';
    }

    /**
     * Print error message in table
     * @param string $content Text printed in content error table
     * @access public
     **/
    public function contentError($content)
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
     **/
    public function limitsFooter($current, $total, $showall, $parameters)
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
            echo '</td><td nowrap align="right" class="footerCell">';
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
     **/
    public function messageBox($msgLabel)
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
        echo '<table cellpadding="0" cellspacing="0" border="0" class="icons"><tr>';
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
        echo "<td align=left width=\"1%\"><img height=\"26\" width=\"5\" src=\"$this->themeImgPath/spacer.gif\" alt=\"\"></td><td class=\"commandDesc\" align=\"left\" width=\"99%\"><div id=\"" . $this->form . "tt\" class=\"rel\"><div id=\"" . $this->form . "tti\" class=\"abs\"><img height=\"1\" width=\"350\" src=\"$this->themeImgPath/spacer.gif\" alt=\"\"></div></div></td></tr></table>";
    }

    /**
     * Open icons script
     * @see block::openPaletteScript()
     * @access public
     **/
    public function openPaletteScript()
    {
        echo '<script type="text/JavaScript">
        document.' . $this->form . 'Form.buttons = new Array();';
    }

    /**
     * Close icons script
     * @param integer $compt Total records
     * @param array $values First row
     * @see block::closePaletteScript()
     * @access public
     **/
    public function closePaletteScript($compt, $values)
    {
        echo "MM_updateButtons(document." . $this->form . "Form, 0);document." . $this->form . "Form.checkboxes = new Array();";
        for ($i = 0; $i < $compt; $i++) {
            echo "document." . $this->form . "Form.checkboxes[document." . $this->form . "Form.checkboxes.length] = new MMCheckbox('$values[$i]',document." . $this->form . "Form,'" . $this->form . "cb$values[$i]');";
        }
        echo "document." . $this->form . "Form.tt = '" . $this->form . "tt';</script>";
    }

    /**
     * Define sorting to apply on a list block
     * @param string $sortingRef Row reference in sorting table
     * @param string $sortingValue Row value in sorting table
     * @param string $sortingDefault Default sorting value
     * @param array $sortingFields Array with sorted fields on each column
     * @access public
     **/
    public function sorting($sortingRef, $sortingValue, $sortingDefault, $sortingFields)
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
                $this->sortingArrows[$i] = '&#160;<img border="0" src="'.$this->themeImgPath.'/icon_sort_za.gif" alt="" width="11" height="11">';
                $this->sortingStyles[$i] = "active";
            } else {
                if ($this->sortingFields[$i] == $explode[0] && $explode[1] == "ASC") {
                    $this->sortingOrders[$i] = "DESC";
                    $this->sortingArrows[$i] = '&#160;<img border="0" src="'.$this->themeImgPath.'/icon_sort_az.gif" alt="" width="11" height="11">';
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
     * @see block::closeFormResults()
     * @see block::closeForm()
     * @access public
     **/
    public function openForm($address)
    {
        echo '<a name="' . $this->form . 'Anchor"></a>
<form accept-charset="UNKNOWN" method="POST" action="' . $address . '" name="' . $this->form . 'Form" enctype="application/x-www-form-urlencoded">';
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
    public function labels($labels, $published, $sorting = "true", $sortingOff = "")
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
                    echo "<th nowrap class='$sortingStyles[$i]'><a href=\"javascript:document." . $this->form . "Form.sort_target.value='{$this->sortingRef}';document." . $this->form . "Form.sort_fields.value='{$sortingFields[$i]}';document." . $this->form . "Form.sort_order.value='{$sortingOrders[$i]}';document." . $this->form . "Form.submit();\" onMouseOver=\"javascript:window.status='" . $this->strings["sort_by"] . " " . addslashes($labels[$i]) . "'; return true;\" onMouseOut=\"javascript:window.status=''; return true\">" . $labels[$i] . "$sortingArrows[$i]</a></th>";
                } else {
                    echo "<th nowrap>{$labels[$i]}</th>";
                }
            } else {
                $sortingArrow = null;
                if ($sortingOff[1] == "ASC") {
                    $sortingArrow = "&#160;<img border='0' src='$this->themeImgPath/icon_sort_az.gif' alt='' width='11' height='11'>";

                } else {
                    if ($sortingOff[1] == "DESC") {
                        $sortingArrow = "&#160;<img border='0' src='$this->themeImgPath/icon_sort_za.gif' alt='' width='11' height='11'>";
                    }
                }
                if ($i == $sortingOff[0]) {
                    echo "<th nowrap class='active'>" . $labels[$i] . "$sortingArrow";
                } else {
                    echo "<th nowrap>" . $labels[$i];
                }
            }
        }

        echo "</tr>";
    }

    /**
     * Open results list
     * @param boolean $checkbox Disable checkbox display
     * @access public
     **/
    public function openResults($checkbox = "true")
    {
        echo "<table class='listing striped' cellpadding='0' cellspacing='0' border='0'>
<tr>";
        if ($checkbox == "true") {
            echo '<th width="1%" align="center">';
            echo '<a href="javascript:MM_toggleSelectedItems(document.' . $this->form . 'Form,\'' . $this->theme . '\')">';
            echo '<img height="13" width="13" border="0" src="' . $this->themeImgPath . '/checkbox_off_16.gif" alt="" vspace="3" hspace="3">';
            echo '</a>';
            echo '</th>';
        } else {
            echo "<th width='1%' align='center'><img height='13' width='13' border='0' src='$this->themeImgPath/spacer.gif' alt='' vspace='3'></th>";
        }
    }

    /**
     *
     */
    public function closeResults()
    {
        echo "</table>
<hr />";
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
     **/
    public function paletteIcon($num, $type, $text)
    {
        echo "<td width=\"30\" class=\"commandBtn\"><a href=\"javascript:var b = MM_getButtonWithName(document." . $this->form . "Form, '" . $this->form . "$num'); if (b) b.click();\" onMouseOver=\"var over = MM_getButtonWithName(document." . $this->form . "Form, '" . $this->form . "$num'); if (over) over.over(); return true; \" onMouseOut=\"var out = MM_getButtonWithName(document." . $this->form . "Form, '" . $this->form . "$num'); if (out) out.out(); return true; \"><img width=\"$this->iconWidth\" height=\"$this->iconHeight\" border=\"0\" name=\"" . $this->form . "$num\" src=\"$this->themeImgPath/btn_" . $type . "_norm.gif\" alt='" . stripslashes($text) . "'></a></td>";
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
    public function paletteScript($num, $type, $link, $options, $text)
    {
        $link = rtrim($link,'?');
        $link = (strpos($link, '?')) ? $link : $link . '?&';

        echo "document." . $this->form . "Form.buttons[document." . $this->form . "Form.buttons.length] = new MMCommandButton('" . $this->form . "$num',document." . $this->form . "Form,'" . $link  . "','$this->themeImgPath/btn_" . $type . "_norm.gif','$this->themeImgPath/btn_" . $type . "_over.gif','$this->themeImgPath/btn_" . $type . "_down.gif','$this->themeImgPath/btn_" . $type . "_dim.gif',$options,'',\"" . stripslashes($text) . "\",false,'');";
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
     * @param string $right Text in right cell
     * @param boolean $altern Option to altern background color
     * @access public
     **/
    public function contentRow($left, $right, $altern = "false")
    {
        if ($this->class == "") {
            $this->class = "odd";
        }

        if ($left != "") {
            echo "<tr class='{$this->class}'><td valign='top' class='leftvalue'>" . $left . " :</td><td>" . $right . "&nbsp;</td></tr>";
        } else {
            echo "<tr class='{$this->class}'><td valign='top' class='leftvalue'>&nbsp;</td><td>" . $right . "&nbsp;</td></tr>";
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
        $change = "true";
        echo "<tr class='{$this->class}' onmouseover=\"this.style.backgroundColor='" . $this->highlightOn . "'\" onmouseout=\"this.style.backgroundColor='" . $this->highlightOff . "'\">";
        if ($this->class == "odd") {
            $this->class = "even";
            $this->highlightOff = $this->evenColor;
            $change = "false";
        } else {
            if ($this->class == "even" && $change != "false") {
                $this->class = "odd";
                $this->highlightOff = $this->oddColor;
            }
        }
    }

    /**
     * @param $ref
     * @param string $checkbox
     */
    public function checkboxRow($ref, $checkbox = "true")
    {
        if ($checkbox == "true") {
            echo "<td align='center'><a href=\"javascript:MM_toggleItem(document." . $this->form . "Form, '" . $ref . "', '" . $this->form . "cb" . $ref . "','{$this->theme}')\"><img name='" . $this->form . "cb" . $ref . "' border='0' src='$this->themeImgPath/checkbox_off_16.gif' alt='' vspace='3'></a></td>";
        } else {
            echo "<td><img height='13' width='13' border='0' src='$this->themeImgPath/spacer.gif' alt='' vspace='3'></td>";
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
                    return '<a href="' . $url . '&"><img src="../interface/icones/' . $label . '" border="0" alt=""></a>';
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
