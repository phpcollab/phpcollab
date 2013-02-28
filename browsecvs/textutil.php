<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../browsecvs/textutil.php
** Authors: Ceam / TY / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: textutil.php
**
** DESC: Library: cvs utility library
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	check template usage
**	check for possible updated library
** =============================================================================
*/

class htmltextsystem
{
	function clear_repeats($text)
	{
		return preg_replace('/(.)\1{2,}/s', "$1$1$1", $text);
	}
	function make_dir($text)
	{
		return strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $text));
	}
	function prep_db($text)
	{
		$retval = $text;
		return addslashes($retval);
	}
	function strip_db($dbval, $strip='', $htmlspecialchars=1, $nl2br=1)
	{
		$retval = strip_tags(stripslashes(trim($dbval)),$strip);
		if ($htmlspecialchars==1) $retval = htmlspecialchars($retval);
		if ($nl2br==1) $retval = nl2br($retval);
		return $retval;
	}
	function is_email($address)
	{
		if (!ereg("@",$address)) return 0;
		if (ereg("\.\.",$address) || ereg(" ",$address)) return 0;
		return preg_match('/\w+(?:[\.\w]+)*\w@(?:\w)+(?:\.\w+)*\.\w{2,3}/',$address);
	}
	function is_url($address)
	{
		if (ereg("\.\.",$address)) return 0;
		if (!strstr($address,"://") || ereg(" ",$address)) return 0;
		return (strlen($address)>10);
 	}
	function formatsize($value)
	{
    		$retval = $value;
	        $unit = " B";
		if ($retval > 1024) {
		        $retval = $retval / 1024;
		        $unit = " KB";
		}
		if ($retval > 1024) {
		        $retval = $retval / 1024;
		        $unit = " MB";
		}
		return str_replace(".00","",sprintf("%0.2f",$retval)).$unit;
	}
	function formattime($formatstring, $secs)
	{
		if ($secs<2) return "very little time";
		$intern = array();
		$desc = array(1 => 'second',
				60 => 'minute',
				3600 => 'hour',
				86400 => 'day',
				604800 => 'week',
				2628000 => 'month',
				31536000 => 'year');
		while (list($k, $s) = each($desc)) {
			$breaks[] = $k;
			$$s=0;
		}
		sort($breaks);

		$i=0;
		while ($i<count($breaks) && $secs>=(2*$breaks[$i])) {
			$i++;
		}
		$i--;
		$break = $breaks[$i];

		$$desc[$break] = intval($secs / $break);
		if ($i > 0) {
			$rest = $secs % $break;
			$break = $breaks[--$i];
			if ($rest > 0) {
				$$desc[$break] = intval($rest/$break);
			}
		}
		$retval = $formatstring;
		if ($year>0) {
			$retval = str_replace("%y", $year." year".(($year>1)?"s":""), $retval);
			$retval = str_replace("%x", $year." year".(($year>1)?"s":""), $retval);
		}
		$retval = str_replace("%y","", $retval);
		if ($month>0) {
			$retval = str_replace("%m", $month." month".(($month>1)?"s":""), $retval);
			$retval = str_replace("%x", $month." month".(($month>1)?"s":""), $retval);
		}
		$retval = str_replace("%m","", $retval);
		if ($week>0) {
			$retval = str_replace("%w", $week." week".(($week>1)?"s":""), $retval);
			$retval = str_replace("%x", $week." week".(($week>1)?"s":""), $retval);
		}
		$retval = str_replace("%w","", $retval);
		if ($day>0) {
			$retval = str_replace("%d", $day." day".(($day>1)?"s":""), $retval);
			$retval = str_replace("%x", $day." day".(($day>1)?"s":""), $retval);
		}
		$retval = str_replace("%d","", $retval);
		if ($hour>0) {
			$retval = str_replace("%h", $hour." hour".(($hour>1)?"s":""), $retval);
			$retval = str_replace("%x", $hour." hour".(($hour>1)?"s":""), $retval);
		}
		$retval = str_replace("%h","", $retval);
		if ($minute>0) {
			$retval = str_replace("%i", $minute." minute".(($minute>1)?"s":""), $retval);
			$retval = str_replace("%x", $minute." minute".(($minute>1)?"s":""), $retval);
		}
		$retval = str_replace("%i","", $retval);
		if ($second>0) {
			$retval = str_replace("%s", $second." second".(($second>1)?"s":""), $retval);
			$retval = str_replace("%x", $second." second".(($second>1)?"s":""), $retval);
		}
		$retval = str_replace("%s","", $retval);
		$retval = str_replace("%x","", $retval);

		if ($month>10) $month = "0".$month;
		if ($day>10) $day = "0".$day;
		if ($hour>10) $hour = "0".$hour;
		if ($minute>10) $minute = "0".$minute;
		if ($second>10) $second = "0".$second;

		$retval = str_replace("%Y", $year, $retval);
		$retval = str_replace("%M", $month, $retval);
		$retval = str_replace("%W", $week, $retval);
		$retval = str_replace("%D", $day, $retval);
		$retval = str_replace("%H", $hour, $retval);
		$retval = str_replace("%I", $minute, $retval);
		$retval = str_replace("%S", $second, $retval);
		return trim($retval);
	}
}

$textutil = new htmltextsystem();

?>