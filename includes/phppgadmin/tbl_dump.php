<?php
/* $Id: tbl_dump.php,v 1.1 2003/07/02 14:47:06 fullo Exp $ */
@set_time_limit(600);
$crlf="\n";

if (empty($asfile)) { 
	include("header.inc.php");
	print "<div align=left><pre>\n";
} else {
	include("lib.inc.php");
	if ($what == "csv") $ext = "csv"; else $ext = "sql";
	header("Content-disposition: attachment; filename=\"$table.$ext\"");
	header("Content-type: application/octetstream");
	header("Pragma: no-cache");
	header("Expires: 0");

	// doing some DOS-CRLF magic...
	$client = getenv("HTTP_USER_AGENT");
	if (ereg('[^(]*\((.*)\)[^)]*',$client,$regs)) {
		$os = $regs[1];

		// this looks better under WinX
		if (eregi("Win",$os)) $crlf="\r\n";
	}
}

function my_handler($sql_insert) {
	global $crlf, $asfile;
	if (empty($asfile)) {
		echo htmlspecialchars("$sql_insert;$crlf");
	} else {
		echo "$sql_insert;$crlf";
	}
}

function my_csvhandler($sql_insert) {
	global $crlf, $asfile;
	if (empty($asfile)) {
		echo htmlspecialchars("$sql_insert;$crlf");
	} else {
		echo "$sql_insert;$crlf";
	}
}

$rel_text = $rel_type == "table" ? $strTable : $strView;

if ($what != "csv") {
	print "$crlf/* -------------------------------------------------------- $crlf";
	print "  $cfgProgName $cfgVersion DB Dump$crlf";
	print "  http://www.sourceforge.net/projects/phppgadmin/$crlf";
	print "  $strHost: " . $cfgServer['host'];
	
	if (!empty($cfgServer['port'])) {
	        print ":" . $cfgServer['port'];
	}
	print "$crlf  $strDatabase: $cfgQuotes$db$cfgQuotes$crlf";
	print "  $rel_text : $cfgQuotes$table$cfgQuotes $crlf";
	print "  " . date("Y-m-d H:m:i") . $crlf;
	print "-------------------------------------------------------- */ $crlf $crlf";

	if ($rel_type == "table") {	
		if ($drop) print "DROP TABLE $cfgQuotes$table$cfgQuotes;$crlf";
		print get_table_def($link, $table, $crlf)."$crlf";
	} else {
		if ($drop) print "DROP VIEW $cfgQuotes$table$cfgQuotes;$crlf";
		$sql_get_views = "SELECT * FROM pg_views WHERE viewname = '$table'";
		$views = @pg_exec($link, pre_query($sql_get_views));
		if (pg_numrows($views)) {
			$view = pg_fetch_array($views, 0);
			print "CREATE VIEW $cfgQuotes$table$cfgQuotes AS $crlf  $view[definition] $crlf";
		}
	}
	
	if ($what == "data") {
		if ($rel_type == "table") {
			print "$crlf/* -------------------------------------------------------- $crlf";
			print "  $strDumpingData $cfgQuotes$table$cfgQuotes $crlf";
			print "-------------------------------------------------------- */ $crlf";
	
			get_table_content($link, $table, "my_handler");
		} else {
			print "$crlf/* -----------------  No Data In Views  ---------------- */ $crlf";
		}
	}
} else { // $what != "csv"
	get_table_csv($link, $table, $separator, "my_csvhandler");
}

if (empty($asfile)) {
	print "</pre></div>\n";
	include ("footer.inc.php");
}
?>
