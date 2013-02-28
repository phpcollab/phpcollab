<?php
#Application name: PhpCollab
#Status page: 0

class block {

function block() {
	$this->iconWidth = "22";
	$this->iconHeight = "21";
	$this->bgColor = "#5B7F93";
	$this->fgColor = "#C4D3DB";
	$this->oddColor = "#F5F5F5";
	$this->evenColor = "#EFEFEF";
	$this->highlightOn = "#DEE7EB";

	$this->class = "odd";
	$this->highlightOff = $this->oddColor;
	$this->theme = THEME;
	$this->pathImg = "../themes";
}

/**
 * Print tooltips
 * @param string $item Text printed in tooltip
 * @access public
 **/
function printHelp($item) {
global $help,$strings;
return " [<a href=\"javascript:void(0);\" onmouseover=\"return overlib('".addslashes($help[$item])."',SNAPX,550,BGCOLOR,'".$this->bgColor."',FGCOLOR,'".$this->fgColor."');\" onmouseout=\"return nd();\">".$strings["help"]."</a>]";
}

/**
 * Add a note
 * @param string $content Text printed in note
 * @access public
 **/
function note($content) {
	echo "<p class=\"note\">".$content."</p>\n\n";
}

/**
 * Print standard heading
 * @param string $title Text printed in heading
 * @access public
 **/
function heading($title) {
	echo "<h1 class=\"heading\">".stripslashes($title)."</h1>\n\n";
}

/**
 * Print toggle heading (with collapse/expande arrow)
 * @param string $title Text printed in heading
 * @see block::closeToggle()
 * @access public
 **/
function headingToggle($title) {
if ($_COOKIE[$this->form] == "c" || $HTTP_COOKIE_VARS[$this->form] == "c") {
	$style = "none";
	$arrow = "closed";
} else {
	$style = "block";
	$arrow = "open";
}
	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><a href=\"javascript:showHideModule('".$this->form."','$this->theme')\" onMouseOver=\"javascript:showHideModuleMouseOver('".$this->form."'); return true; \" onMouseOut=\"javascript:window.status=''; return true;\"><img name=\"".$this->form."Toggle\" border=\"0\" src=\"$this->pathImg/$this->theme/module_toggle_".$arrow.".gif\" alt=\"\"></a></td><td><img width=\"10\" height=\"10\" name=\"".$this->form."tl\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\"></td><td width=\"100%\"><h1 class=\"heading\">".$title."</h1></td></tr></table>

<div id=\"".$this->form."\" style=\"display: $style;\">\n\n";
}

/**
 * Close toggle block
 * @see block::headingToggle()
 * @access public
 **/
function closeToggle() {
	echo "</div>\n\n";
}

/**
 * Print error heading
 * @param string $title Text printed in heading
 * @access public
 **/
function headingError($title) {
	echo "<h1 class=\"headingError\">".$title."</h1>\n";
}

/**
 * Print error message in table
 * @param string $content Text printed in content error table
 * @access public
 **/
function contentError($content) {
	echo "<table class=\"error\"><tr><td>".$content."</td></tr></table>\n";
}

function returnBorne($current) {
global ${'borne'.$current};
if (${'borne'.$current} == "") {
	$borneValue = "0";
} else {
	$borneValue = ${'borne'.$current};
}
return $borneValue;
}

/**
 * Print page-per-page in bottom of list block
 * @param string $current Borne number for concerned block
 * @param string $total Total bornes number
 * @param string $showall Link to page which display all records, with parameters
 * @param string $parameters Optional parameters to transmit between pages
 * @access public
 **/
function bornesFooter($current,$total,$showall,$parameters) {
global $strings;
if ($this->rowsLimit < $this->recordsTotal) {
echo "<table cellspacing=\"0\" width=\"100%\" border=\"0\" cellpadding=\"0\"><tr><td nowrap class=\"footerCell\">&#160;&#160;&#160;&#160;";

$nbpages = ceil($this->recordsTotal/ $this->rowsLimit);
$j = "0";
for($i = 1;$i <= $nbpages;$i ++){
if ($this->borne == $j) {
	echo "<b>$i</b>&#160;";
} else {
	echo "<a href=\"$PHP_SELF?$transmitSid";
	for ($k=1;$k<=$total;$k++) {
		global ${'borne'.$k};
		if ($k != $current) {
			echo "&borne$k=".${'borne'.$k};
		} else if ($k == $current) {
			echo "&borne$k=$j";
		}
	}
	echo "&$parameters&".session_name()."=".session_id()."#".$this->form."Anchor\">$i</a>&#160;";
}
$j = $j + $this->rowsLimit;


}
echo "</td><td nowrap align=\"right\" class=\"footerCell\">";
if ($showall != "") {
echo "<a href=\"$showall&".session_name()."=".session_id()."\">".$strings["show_all"]."</a>";
}
echo "&#160;&#160;&#160;&#160;&#160;</td></tr><tr><td height=\"5\" colspan=\"2\"><img width=\"1\" height=\"5\" border=\"0\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\"></td></tr></table>";
}

}

/**
 * Print Message table
 * @param string $msgLabel Text built with messages.php
 * @access public
 **/
function messageBox($msgLabel) {
echo "<br/><table class=\"message\">
<tr><td>$msgLabel</td></tr>
</table>";
}

/**
 * Open icons table
 * @see block::closePaletteIcon()
 * @see block::paletteIcon()
 * @see block::paletteScript()
 * @access public
 **/
function openPaletteIcon() {
echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"icons\"><tr>\n";
}

/**
 * Close icons table
 * @see block::openPaletteIcon()
 * @see block::paletteIcon()
 * @see block::paletteScript()
 * @access public
 **/
function closePaletteIcon() {
echo "<td align=left width=\"1%\"><img height=\"26\" width=\"5\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\"></td><td class=\"commandDesc\" align=\"left\" width=\"99%\"><div id=\"".$this->form."tt\" class=\"rel\"><div id=\"".$this->form."tti\" class=\"abs\"><img height=\"1\" width=\"350\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\"></div></div></td></tr></table>\n";
}

/**
 * Open icons script
 * @see block::openPaletteScript()
 * @access public
 **/
function openPaletteScript() {
echo "<script type=\"text/JavaScript\">
<!--
document.".$this->form."Form.buttons = new Array();\n";
}

/**
 * Close icons script
 * @param integer $compt Total records
 * @param array $values First row
 * @see block::closePaletteScript()
 * @access public
 **/
function closePaletteScript($compt,$values) {
echo "MM_updateButtons(document.".$this->form."Form, 0);document.".$this->form."Form.checkboxes = new Array();";
for ($i=0;$i<$compt;$i++) {
	echo "document.".$this->form."Form.checkboxes[document.".$this->form."Form.checkboxes.length] = new MMCheckbox('$values[$i]',document.".$this->form."Form,'".$this->form."cb$values[$i]');";
}
echo "document.".$this->form."Form.tt = '".$this->form."tt';
// -->
</script>\n\n";
}

/**
 * Define sorting to apply on a list block
 * @param string $sortingRef Row reference in sorting table
 * @param string $sortingValue Row value in sorting table
 * @param string $sortingDefault Default sorting value
 * @param array $sortingFields Array with sorted fields on each column
 * @access public
 **/
function sorting($sortingRef,$sortingValue,$sortingDefault,$sortingFields) {
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
global $sortingOrders,$sortingFields,$sortingArrows,$sortingStyles,$explode;
if (isset($this->sortingValue) != "") {
$explode = explode(" ",$this->sortingValue);
} else {
$this->sortingValue = $this->sortingDefault;
$explode = explode(" ",$this->sortingValue);
}

for ($i=0;$i<count($sortingFields);$i++) {
if ($sortingFields[$i] == $explode[0] && $explode[1] == "DESC") {
	$sortingOrders[$i] = "ASC";
	$sortingArrows[$i] = "&#160;<img border=\"0\" src=\"$this->pathImg/$this->theme/icon_sort_za.gif\" alt=\"\" width=\"11\" height=\"11\">";
	$sortingStyles[$i] = "active";
} else if ($sortingFields[$i] == $explode[0] && $explode[1] == "ASC") {
	$sortingOrders[$i] = "DESC";
	$sortingArrows[$i] = "&#160;<img border=\"0\" src=\"$this->pathImg/$this->theme/icon_sort_az.gif\" alt=\"\" width=\"11\" height=\"11\">";
	$sortingStyles[$i] = "active";
} else {
	$sortingOrders[$i] = "ASC";
	$sortingArrows[$i] = "";
	$sortingStyles[$i] = "";
}
}
if ($sortingOrders != "") {
	$this->sortingOrders = $sortingOrders;
}
if ($sortingArrows != "") {
	$this->sortingArrows = $sortingArrows;
}
if ($sortingStyles != "") {
	$this->sortingStyles = $sortingStyles;
}
}

/**
 * Open a standard form
 * @param string $address Action form value
 * @see block::closeFormResults()
 * @see block::closeForm()
 * @access public
 **/
function openForm($address) {
echo "<a name=\"".$this->form."Anchor\"></a>\n
<form accept-charset=\"UNKNOWN\" method=\"POST\" action=\"$address\" name=\"".$this->form."Form\" enctype=\"application/x-www-form-urlencoded\">\n\n";
}

/**
 * Close a form used with a list block
 * @access public
 **/
function closeFormResults() {
echo "<input name=\"sor_cible\" type=\"HIDDEN\" value=\"$this->sortingRef\"><input name=\"sor_champs\" type=\"HIDDEN\" value=\"\"><input name=\"sor_ordre\" type=\"HIDDEN\" value=\"\">
</form>\n\n";
}

/**
 * Define column labels in a list block
 * @param array $labels Array with labels strings
 * @param boolean $published Show/hide a published column
 * @param boolean $sorting Disable sorting
 * @param array $sortingOff Array with label number (from $labels) and order (ASC/DESC)
 * @access public
 **/
function labels($labels,$published,$sorting="true",$sortingOff="") {
global $labels,$sortingOrders,$sortingFields,$sortingArrows,$sortingStyles,$strings,$sitePublish;
$sortingFields = $this->sortingFields;
$sortingOrders = $this->sortingOrders;
$sortingArrows = $this->sortingArrows;
$sortingStyles = $this->sortingStyles;

if ($sitePublish == "false" && $published == "true") {
	$comptLabels = count($labels) - 1;
} else {
	$comptLabels = count($labels);
}
for ($i=0;$i<$comptLabels;$i++) {
if ($sorting == "true") {
	echo "<th nowrap class=\"$sortingStyles[$i]\"><a href=\"javascript:document.".$this->form."Form.sor_cible.value='$this->sortingRef';document.".$this->form."Form.sor_champs.value='$sortingFields[$i]';document.".$this->form."Form.sor_ordre.value='$sortingOrders[$i]';document.".$this->form."Form.submit();\" onMouseOver=\"javascript:window.status='".$strings["sort_by"]." ".addslashes($labels[$i])."'; return true;\" onMouseOut=\"javascript:window.status=''; return true\">".$labels[$i]."$sortingArrows[$i]</a></th>\n";
} else {
if ($sortingOff[1] == "ASC") {
	$sortingArrow = "&#160;<img border=\"0\" src=\"$this->pathImg/$this->theme/icon_sort_az.gif\" alt=\"\" width=\"11\" height=\"11\">";

} else if ($sortingOff[1] == "DESC") {
	$sortingArrow = "&#160;<img border=\"0\" src=\"$this->pathImg/$this->theme/icon_sort_za.gif\" alt=\"\" width=\"11\" height=\"11\">";
}
if ($i == $sortingOff[0]) {
	echo "<th nowrap class=\"active\">".$labels[$i]."$sortingArrow";
} else {
	echo "<th nowrap>".$labels[$i];
}
}
}

echo "</tr>\n";
}

/**
 * Open results list
 * @param boolean $checkbox Disable checkbox display
 * @access public
 **/
function openResults($checkbox="true") {
echo "<table class=\"listing\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
<tr>\n";
if ($checkbox == "true") {
echo "<th width=\"1%\" align=\"center\"><a href=\"javascript:MM_toggleSelectedItems(document.".$this->form."Form,'$this->theme')\"><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/$this->theme/checkbox_off_16.gif\" alt=\"\" vspace=\"3\" hspace=\"3\"></a></th>\n";
} else {
echo "<th width=\"1%\" align=\"center\"><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\" vspace=\"3\"></th>\n";
}
}

function closeResults() {
echo "</table>
<hr />\n";
}

function noresults() {
global $strings;
echo "<table cellspacing=\"0\" border=\"0\" cellpadding=\"2\"><tr><td colspan=\"4\">".$strings["no_items"]."</td></tr></table><hr />";
}

/**
 * Display an icon (html)
 * @param integer $num Icon number
 * @param string $type Icon name (used in graphic file name)
 * @param string $text Text used in info-tip
 * @see block::openPaletteIcon()
 * @access public
 **/
function paletteIcon($num,$type,$text) {
echo "<td width=\"30\" class=\"commandBtn\"><a href=\"javascript:var b = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (b) b.click();\" onMouseOver=\"var over = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (over) over.over(); return true; \" onMouseOut=\"var out = MM_getButtonWithName(document.".$this->form."Form, '".$this->form."$num'); if (out) out.out(); return true; \"><img width=\"$this->iconWidth\" height=\"$this->iconHeight\" border=\"0\" name=\"".$this->form."$num\" src=\"$this->pathImg/$this->theme/btn_".$type."_norm.gif\" alt='".stripslashes($text)."'></a></td>\n";
}

/**
 * Display an icon (JavaScript)
 * @param integer $num Icon number
 * @param string $type Icon name (used in graphic file name)
 * @param string $options JavaScript options enableOnNoSelection, enableOnSingleSelection, enableOnMultipleSelection
 * @param string $text Text used in roll-over layer
 * @see block::openPaletteIcon()
 * @access public
 **/
function paletteScript($num,$type,$link,$options,$text) {
echo "document.".$this->form."Form.buttons[document.".$this->form."Form.buttons.length] = new MMCommandButton('".$this->form."$num',document.".$this->form."Form,'".$link."&".session_name()."=".session_id()."','$this->pathImg/$this->theme/btn_".$type."_norm.gif','$this->pathImg/$this->theme/btn_".$type."_over.gif','$this->pathImg/$this->theme/btn_".$type."_down.gif','$this->pathImg/$this->theme/btn_".$type."_dim.gif',$options,'',\"".stripslashes($text)."\",false,'');\n";
}


/**
 * Start a table to display sheet/form
 * @see block::contentRow()
 * @access public
 **/
function openContent() {
	echo "<table class=\"content\" cellspacing=\"0\" cellpadding=\"0\">";
}

/**
 * Display a table line in sheet/form
 * @param string $left Text in left cell
 * @param string $right Text in right cell
 * @param boolean $altern Option to altern background color
 * @access public
 **/
function contentRow($left,$right,$altern="false") {
if ($this->class == "") {
	$this->class = "odd";
}
if ($left != "") {
	echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\">".$left." :</td><td>".$right."&nbsp;</td></tr>\n";
} else {
	echo "<tr class=\"$this->class\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td>".$right."&nbsp;</td></tr>\n";
}
	if ($this->class == "odd" && $altern == "true") {
		$this->class = "even";
	} else if ($this->class == "even" && $altern == "true") {
		$this->class = "odd";
	}
}

function openRow() {
	$change = "true";
	echo "<tr class=\"$this->class\" onmouseover=\"this.style.backgroundColor='".$this->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$this->highlightOff."'\">\n";
	if ($this->class == "odd") {
		$this->class = "even";
		$this->highlightOff = $this->evenColor;
		$change = "false";
	} else if ($this->class == "even" && $change != "false") {
		$this->class = "odd";
		$this->highlightOff = $this->oddColor;
	}
}

function checkboxRow($ref,$checkbox="true") {
if ($checkbox == "true") {
	echo "<td align=\"center\"><a href=\"javascript:MM_toggleItem(document.".$this->form."Form, '".$ref."', '".$this->form."cb".$ref."','$this->theme')\"><img name=\"".$this->form."cb".$ref."\" border=\"0\" src=\"$this->pathImg/$this->theme/checkbox_off_16.gif\" alt=\"\" vspace=\"3\"></a></td>";
} else {
	echo "<td><img height=\"13\" width=\"13\" border=\"0\" src=\"$this->pathImg/$this->theme/spacer.gif\" alt=\"\" vspace=\"3\"></td>";
}
}

function cellRow($content) {
	echo "<td>$content</td>";
}

function closeRow() {
	echo "\n</tr>\n";
}

function contentTitle($title) {
	echo "<tr><th colspan=\"2\">".$title."</th></tr>";
}

function closeContent() {
	echo "</table>\n<hr />\n";
}

function closeForm() {
	echo "</form>\n";
}

function openBreadcrumbs() {
	echo "<p class=\"breadcrumbs\">";
}

function itemBreadcrumbs($content) {
if ($this->breadcrumbsTotal == "") {
	$this->breadcrumbsTotal = "0";
}
	$this->breadcrumbs[$this->breadcrumbsTotal] = stripslashes($content);
	$this->breadcrumbsTotal = $this->breadcrumbsTotal + 1;
}

function closeBreadcrumbs() {
	$items = $this->breadcrumbsTotal;
	for ($i=0;$i<$items;$i++) {
		echo $this->breadcrumbs[$i];
		if ($items-1 != $i) {
			echo " / ";
		}
	}
	echo "</p>\n\n";
}

function openNavigation() {
	echo "<p id=\"navigation\">";
}


function itemNavigation($content) {
if ($this->navigationTotal == "") {
	$this->navigationTotal = "0";
}
	$this->navigation[$this->navigationTotal] = $content;
	$this->navigationTotal = $this->navigationTotal + 1;
}

function closeNavigation() {
	$items = $this->navigationTotal;
	for ($i=0;$i<$items;$i++) {
		echo $this->navigation[$i];
		if ($items-1 != $i) {
			echo "&nbsp;&nbsp;";
		}
	}
	echo "</p>\n\n";
}

function openAccount() {
	echo "<p id=\"account\">";
}

function itemAccount($content) {
if ($this->accountTotal == "") {
	$this->accountTotal = "0";
}
	$this->account[$this->accountTotal] = $content;
	$this->accountTotal = $this->accountTotal + 1;
}

function closeaccount() {
	$items = $this->accountTotal;
	for ($i=0;$i<$items;$i++) {
		echo $this->account[$i];
		if ($items-1 != $i) {
			echo " ";
		}
	}
	echo "</p>\n\n";
}

function buildLink($url,$label,$type) {
	if ($type == "in") {
		return "<a href=\"$url&".session_name()."=".session_id()."\">$label</a>";
	} else if ($type == "icone") {
		return "<a href=\"$url&".session_name()."=".session_id()."\"><img src=\"../interface/icones/$label\" border=\"0\" alt=\"\"></a>";
	} else if ($type == "inblank") {
		return "<a href=\"$url&".session_name()."=".session_id()."\" target=\"_blank\">$label</a>";
	} else if ($type == "powered") {
		return "Powered by <a href=\"$url\" target=\"_blank\">$label</a>";
	} else if ($type == "out") {
	    // Verify correct urltyping
        if (substr($url, 0, 4) != 'http') {
            // Add default http on it
            $url = "http://" . $url;
        } 
		return "<a href=\"$url\" target=\"_blank\">$label</a>";
	} else if ($type == "mail") {
		return "<a href=\"mailto:$url\">$label</a>";
	}
}

}
?>