<?php
/* $Id: db_dump.php,v 1.1 2003/07/02 14:47:06 fullo Exp $ */

$crlf="\n";
if (empty($asfile)) {
	include("header.inc.php");
	print "<div align=left><pre>\n";
} else {
	include("lib.inc.php");
	header("Content-disposition: attachment; filename=\"$db.sql\"");
	header("Content-type: application/octetstream");
	header("Pragma: no-cache");
	header("Expires: 0");

	// doing some DOS-CRLF magic...
	$client=getenv("HTTP_USER_AGENT");
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

print "$crlf/* -------------------------------------------------------- $crlf"; 
print "  $cfgProgName $cfgVersion DB Dump$crlf";
print "  http://sourceforge.net/projects/phppgadmin/$crlf";
print "  $strHost: " . $cfgServer['host'];

if (!empty($cfgServer['port'])) {
	print ":" . $cfgServer['port'];
}
print "$crlf  $strDatabase : $cfgQuotes$db$cfgQuotes$crlf";
print "  " . date("Y-m-d H:m:i") . $crlf;
print "-------------------------------------------------------- */ $crlf";

$get_seq_sql = "
	SELECT relname 
	FROM pg_class 
	WHERE 
		NOT relname ~ 'pg_.*' 
		AND relkind ='S' 
	ORDER BY relname
	";

$seq = @pg_exec($link, pre_query($get_seq_sql));
if (!$num_seq = @pg_numrows($seq)) {
	print "/* $strNo $strSequences $strFound */";
} else {
	print "$crlf/* -------------------------------------------------------- $crlf";
	print "  $strSequences $crlf";
	print "-------------------------------------------------------- */ $crlf";

	while ($i_seq < $num_seq) {
		$sequence = @pg_result($seq, $i_seq, "relname");
		
		$sql_get_props = "SELECT * FROM $cfgQuotes$sequence$cfgQuotes";
		$seq_props = @pg_exec($link, pre_query($sql_get_props));
		if (@pg_numrows($seq_props)) {
			$row = @pg_fetch_array($seq_props, 0);
			if ($what != "data") {
				$row[last_value] = 1;
			}
			if ($drop) print "DROP SEQUENCE $cfgQuotes$sequence$cfgQuotes;$crlf";
			print "CREATE SEQUENCE $cfgQuotes$sequence$cfgQuotes START $row[last_value] INCREMENT $row[increment_by] MAXVALUE $row[max_value] MINVALUE $row[min_value] CACHE $row[cache_value]; $crlf";
		}
		if (($row[last_value] > 1) && ($what == "data")) {
			print "SELECT NEXTVAL('$sequence'); $crlf";
			unset($row[last_value]);
		}
		$i_seq++;
	}
}

$tables = @pg_exec($link, "SELECT tablename FROM pg_tables WHERE tablename !~ 'pg_.*' ORDER BY tablename");

$num_tables = @pg_numrows($tables);
if (!$num_tables) {
	echo $strNoTablesFound;
} else {
	
	for ($i = 0; $i < $num_tables; $i++) {
		$table = pg_result($tables, $i, "tablename");
	
		print "$crlf/* -------------------------------------------------------- $crlf";
		print "  $strTableStructure $cfgQuotes$table$cfgQuotes $crlf";
		print "-------------------------------------------------------- */";

		echo $crlf;
		if ($drop) print "DROP TABLE $cfgQuotes$table$cfgQuotes;$crlf";
		if (!$asfile) {
			echo htmlentities(get_table_def($link, $table, $crlf));
		} else {
			echo get_table_def($link, $table, $crlf);
		}
		echo $crlf;
		
		if ($what == "data") {
		
			print "$crlf/* -------------------------------------------------------- $crlf";
			print "  $strDumpingData $cfgQuotes$table$cfgQuotes $crlf";
			print "-------------------------------------------------------- */ $crlf";
		
			get_table_content($link, $table, "my_handler");
		}
	}
}

// tablename !~ 'pg_.*'
$sql_get_views = "SELECT * FROM pg_views WHERE viewname !~ 'pg_.*'";

$views = @pg_exec($link, pre_query($sql_get_views));
if (!$num_views = @pg_numrows($views)) {
	print "$crlf/* $strNo $strViews $strFound */$crlf";
} else {
	print "$crlf/* -------------------------------------------------------- $crlf";
	print "  $strViews $crlf";
	print "-------------------------------------------------------- */ $crlf";

	for ($i_views = 0; $i_views < $num_views; $i_views++) {
		$view = pg_fetch_array($views, $i_views);
		if ($drop) print "DROP VIEW $cfgQuotes$view[viewname]$cfgQuotes;$crlf";
		print "CREATE VIEW $cfgQuotes$view[viewname]$cfgQuotes AS $view[definition] $crlf";
	}
}

// Output functions

// Max built-in oidi
//$sql_get_max = "SELECT oid FROM pg_database WHERE datname = 'template1'";  
//$maxes = pg_exec($link, $sql_get_max);
//$max = pg_result($maxes, 0, "oid");
//$max = $row[datlastsysoid];
//$max = 16384;

$max = $builtin_max;

// Skips system functions

if ($common_ver < 7.1)
{
	$sql_get_funcs = "
	SELECT 
		pc.oid,
		proname, 
		lanname as language,
		t.typname as return_type,
		prosrc as source,
		probin as binary,
		oidvectortypes(pc.proargtypes) AS arguments
	FROM 
		pg_proc pc, pg_language pl, pg_type t
	WHERE 
		pc.oid > '$max'::oid
		AND pc.prolang = pl.oid
	";
}

else
{
	$sql_get_funcs = "
	SELECT 
		pc.oid,
		proname, 
		lanname as language,
		format_type(prorettype, NULL) as return_type,
		prosrc as source,
		probin as binary,
		oidvectortypes(pc.proargtypes) AS arguments
	FROM 
		pg_proc pc, pg_language pl
	WHERE 
		pc.oid > '$max'::oid
		AND pc.prolang = pl.oid
	";
}

print $crlf;

$funcs = pg_exec($link, pre_query($sql_get_funcs)) or pg_die(pg_errormessage(), $sql_get_funcs, __FILE__, __LINE__);

if (!$num_funcs = pg_numrows($funcs)) {
	print "/* $strNo $strFuncs $strFound */$crlf";
} else {
	print "$crlf/* -------------------------------------------------------- $crlf";
	print "  $strFuncs $crlf";
	print "-------------------------------------------------------- */ $crlf";

	for ($i_funcs = 0; $i_funcs < $num_funcs; $i_funcs++) {
		$func_info = @pg_fetch_array($funcs, $i_funcs);

		if ($common_ver < 7.1) {
			$strArgList = ereg_replace(" ", ", ", $func_info[arguments]);
		} else {
			$strArgList = $func_info[arguments];
		}
		
		if ($func_info[binary] != "-") {
			$strBin = "'$func_info[binary]',";
		} else {
			unset($strBin);
		}

		if ($func_info[return_type] == "-") {
			$func_info[return_type] = "OPAQUE";
		}
		if ($drop) print "DROP FUNCTION $cfgQuotes$func_info[proname]$cfgQuotes($strArgList);$crlf";
		echo "CREATE FUNCTION $cfgQuotes$func_info[proname]$cfgQuotes($strArgList) RETURNS $func_info[return_type] AS $strBin'$func_info[source]' LANGUAGE '$func_info[language]'; $crlf";
	}
}

// Output triggers

// Some definitions
$TRIGGER_TYPE_ROW			=	(1 << 0);
$TRIGGER_TYPE_BEFORE		=	(1 << 1);
$TRIGGER_TYPE_INSERT		=	(1 << 2);
$TRIGGER_TYPE_DELETE		=	(1 << 3);
$TRIGGER_TYPE_UPDATE		=	(1 << 4);

$sql_get_triggers = "
	SELECT 
		pt.*, pp.proname, pc.relname
	FROM 
		pg_trigger pt, pg_proc pp, pg_class pc
	WHERE 
		pp.oid=pt.tgfoid
		and pt.tgrelid=pc.oid
		and relname !~ '^pg_'
";

$triggers = @pg_exec($link, pre_query($sql_get_triggers));
if (!$num_triggers = @pg_numrows($triggers)) {
	print "$crlf/* $strNo $strTriggers $strFound */$crlf";
} else {
	print "$crlf/* -------------------------------------------------------- $crlf";
	print "  $strTriggers $crlf";
	print "-------------------------------------------------------- */ $crlf";

	for ($i_triggers = 0; $i_triggers < $num_triggers; $i_triggers++) {
		$trigger = pg_fetch_array($triggers, $i_triggers);
		// Constraint or not
		if ($trigger[tgisconstraint] == 't')
			print "CREATE CONSTRAINT TRIGGER";
		else
			print "CREATE TRIGGER";
		// Name
		print " $cfgQuotes$trigger[tgname]$cfgQuotes";

		// before/after
		if ($trigger[tgtype] & $TRIGGER_TYPE_BEFORE)
			print " BEFORE";
		else
			print " AFTER";

		// Insert
		$findx = 0;
		if ($trigger[tgtype] & $TRIGGER_TYPE_INSERT) {
			print " INSERT";
			$findx++;
		}

		// Delete
		if ($trigger[tgtype] & $TRIGGER_TYPE_DELETE) {
			if ($findx > 0)
				print " OR DELETE";
			else
				print " DELETE";
			$findx++;
		}
		
		// Update
		if ($trigger[tgtype] & $TRIGGER_TYPE_UPDATE) {
			if ($findx > 0)
				print " OR UPDATE";
			else
				print " UPDATE";
		}

		// On
		print " ON $cfgQuotes$trigger[relname]$cfgQuotes";

		// Contraints, deferrable
		if ($trigger[tgisconstraint] == 't') {
			if ($trigger[tgdeferrable] == 'f') print " NOT";
			print " DEFERRABLE INITIALLY ";

			if ($trigger[tginitdeferred] == 't')
				print "DEFERRED";
			else
				print "IMMEDIATE";
		}
		echo " FOR EACH ROW";
		echo " EXECUTE PROCEDURE $cfgQuotes$trigger[proname]$cfgQuotes ('";

		// Strip of trailing delimiter
		$tgargs = trim(substr($trigger[tgargs], 0, strlen($trigger[tgargs]) - 4));
		$params = explode('\000', $tgargs);

		for ($i = 0; $i < sizeof($params); $i++) {
			$params[$i] = str_replace("'", "\\'", $params[$i]);
		}
		$params = implode("', '", $params);
		if ($asfile) {
			echo htmlspecialchars($params), "');$crlf";
		} else {
			echo $params, "');$crlf";
		}
	}
}

if(empty($asfile)) {
	print "</pre></div>\n";
	include ("footer.inc.php");
}
?>
