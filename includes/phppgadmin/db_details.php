<?php
/* $Id: db_details.php,v 1.2 2003/10/28 02:18:55 fullo Exp $ */

if (!isset($message)) {
	include("header.inc.php");
} else {
	show_message($message);
	unset($sql_query);
}

if (empty($sql_query)) {
	switch($rel_type) {		
		case "view":	// Views
			$sql_get_views = "SELECT viewname FROM pg_views WHERE viewname !~ 'pg_.*' ORDER BY viewname";
			$views = pg_exec($link, $sql_get_views) or pg_die(pg_errormessage(), $sql_get_views, __FILE__, __LINE__);
			$num_views = @pg_numrows($views);
			if ($num_views == 0) {
				echo "<br/><b>$strNo $strViews $strFound</b><br/>";
				echo "<li><a href=\"view_create.php?server=$server&db=$db&goto=main.php\">$strCreateNew $strView</a><br/>";
			} else {
				$i = 0;
			
				echo "
					<table border=$cfgBorder>
						<tr bgcolor=lightgrey>
							<th>$strView</th>
							<th align=center colspan=6>$strAction</th>
							<th>$strRecords</th>
						</tr>
					";
			
				while ($i < $num_views) {
					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;
					$view = pg_result($views, $i, 'viewname');
					$query = "?db=$db&server=$server&view=$view";
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><b><?php echo $view;?></b></td>
					<?php if (!$printview) { ?>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("SELECT * FROM $cfgQuotes$view$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strEmptyResultSet");?>"><?php echo $strBrowse; ?></td>
					<td><a href="tbl_select.php<?php echo $query;?>"><?php echo $strSelect; ?></td>
					<!--td><a href="tbl_change.php<?php echo $query;?>"><?php echo $strInsert; ?></td-->
					<td><a href="tbl_properties.php<?php echo $query;?>"><?php echo $strProperties; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DELETE FROM $cfgQuotes$view$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strView $view $strHasBeenEmptied.");?>"><?php echo $strEmpty; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP VIEW $cfgQuotes$view$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strView $view $strHasBeenDropped.");?>"><?php echo $strDrop; ?></td>
					<td><a href="tbl_privilege.php<?php echo $query;?>&goto=db_details.php&table=<?php echo $view; ?>"><?php echo $strPrivileges; ?></td>
					<?php } // End printview ?>
					<td align=right><?php if ($cfgCountRecs) count_records($link, $view); ?></td>
					</tr>
					<?php
					$i++;
				}
				echo "</table>\n";
			}
			break;
		case "sequence":
			$sql_get_seq = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relkind ='S' ORDER BY relname";
			$sequences = pg_exec($link, pre_query($sql_get_seq)) or pg_die(pg_errormessage(), $sql_get_seq, __FILE__, __LINE__);
			$num_seq = @pg_numrows($sequences);
			if ($num_seq == 0) {
				echo "<br/><b>$strNo $strSequences $strFound</b><br/>";
			} else {
				$i = 0;
				echo "<table border=$cfgBorder><tr bgcolor=lightgrey><th colspan=4>$strSequences</th></tr>\n";
			
				while ($i < $num_seq) {
					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;
					$sequence = pg_result($sequences, $i, 'relname');
					$query = "?db=$db&server=$server&rel_type=$rel_type&sequence=$sequence";
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><b><?php echo $sequence;?></b></td>
					<?php if (!$printview) { ?>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("SELECT * FROM $cfgQuotes$sequence$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strEmptyResultSet");?>"><?php echo $strProperties; ?></td>
					<!--td><a href="tbl_properties.php<?php echo $query;?>">Properties</td-->
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP SEQUENCE $cfgQuotes$sequence$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strSequence $table $strHasBeenDropped.");?>"><?php echo $strDrop; ?></td>
					<td><a href="tbl_privilege.php<?php echo $query;?>&goto=db_details.php&table=<?php echo $sequence; ?>"><?php echo $strPrivileges; ?></td>
					<?php } ?>
					</tr>
					<?php
					$i++;
				}
				echo "</table>\n";
			}
			break;
		case "function":
			$max = $builtin_max;

			$sql_get_func = "
				SELECT 
					pc.oid,
					proname, 
					pt.typname AS result, 
					oidvectortypes(pc.proargtypes) AS arguments
				FROM 
					pg_proc pc, pg_user pu, pg_type pt
				WHERE
					pc.proowner = pu.usesysid
					AND pc.prorettype = pt.oid
					AND pc.oid > '$max'::oid
				UNION
				SELECT 
					pc.oid,
					proname, 
					'OPAQUE' AS result, 
					oidvectortypes(pc.proargtypes) AS arguments
				FROM 
					pg_proc pc, pg_user pu, pg_type pt
				WHERE
					pc.proowner = pu.usesysid
					AND pc.prorettype = 0
					AND pc.oid > '$max'::oid
				ORDER BY
					proname, result
			";
			$funcs = @pg_exec($link, pre_query($sql_get_func)) or pg_die(pg_errormessage(), $sql_get_func, __FILE__, __LINE__);
			$num_funcs = @pg_numrows($funcs);
			if ($num_funcs == 0) {
				echo "<br/><b>$strNo $strFuncs $strFound</b><br/>";
				echo "<li><a href=\"func_edit.php?server=$server&db=$db&goto=main.php&create=1\">$strCreateNew $strFunction</a><br/>";
			} else {
				echo "
					<table border=$cfgBorder>\n
						<tr bgcolor=lightgrey>
							<th align=center>$strRetType</th>
							<th align=center>$strFuncs</th>
							<th colspan=3>$strAction</th>
						</tr>\n
				";
			
				for ($i = 0; $i < $num_funcs; $i++) {
					$func_ary = pg_fetch_array($funcs, $i);

					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;

					if ($common_ver < 7.1) {
						$strArgList = str_replace(" ", ",", trim($func_ary[arguments])); 
					} else {
						$strArgList = $func_ary[arguments];
					}
					$func_disp = "$func_ary[proname]" . "($strArgList)";
					$func_sql = "$cfgQuotes$func_ary[proname]$cfgQuotes" . "($strArgList)";
					$query = "?db=$db&server=$server&rel_type=$rel_type&function_oid=$func_ary[oid]";
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><?php echo $func_ary[result];?></td>
					<td class=data><b><?php echo $func_disp;?></b></td>
					<?php if (!$printview) { ?>
					<td><a href="func_properties.php<?php echo $query; ?>"><?php echo $strProperties; ?></td>
					<td><a href="func_edit.php<?php echo $query; ?>"><?php echo $strChange; ?></a></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP FUNCTION $func_sql");?>&zero_rows=<?php echo urlencode("$strFunc $function $strHasBeenDropped.");?>"><?php echo $strDrop; ?></td>
					<?php } ?>
					</tr>
					<?php
				}
				echo "</table>\n";
			}
			break;
		case "trigger":	// Triggers
			$sql_get_trig = "
				SELECT ptr.*, pt.typname as result, pc.relname as relname
				FROM pg_trigger ptr, pg_type pt, pg_class pc
				WHERE ptr.tgtype = pt.oid 
				AND ptr.tgrelid = pc.oid
				AND tgname !~ 'pg_.*' 
				ORDER BY tgname
			";
			$triggers = @pg_exec($link, pre_query($sql_get_trig)) or pg_die(pg_errormessage(), $sql_get_trig, __FILE__, __LINE__);
			$num_triggers = @pg_numrows($triggers);
			if ($num_triggers == 0) {
				echo "<br/><b>$strNo $strTriggers $strFound</b><br/>";
				echo "<li><a href=\"trig_create.php?server=$server&db=$db&goto=main.php\">$strCreateNew $strTrigger</a><br/>";
			} else {
				echo "<table border=$cfgBorder>\n<tr bgcolor=lightgrey><th align=center>$strRetType</th><th align=center>$strTrigger</th>";
				echo "<th align=center>$strTable</th><th align=center>$strIsConstraint</th><th colspan=2>$strAction</th></tr>\n";
			
				for ($i = 0; $i < $num_triggers; $i++) {
					$trig_ary = pg_fetch_array($triggers, $i);

					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;

					$classname = $trig_ary[relname];
					$trig_disp = "$trig_ary[tgname]";
					$trig_is_const = bool_YesNo($trig_ary[tgisconstraint]);
					// $trig_is_const = ($trig_ary[tgisconstraint]) == 't') ? 'Y' : 'N';
					$trig_sql = "$cfgQuotes$trig_ary[tgname]$cfgQuotes";
					$query = "?db=$db&server=$server&rel_type=$rel_type&trigger=" . urlencode($trig_ary[tgname]);
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><?php echo $trig_ary[result];?></td>
					<td class=data><b><?php echo $trig_disp;?></b></td>
					<td class=data><?php echo $classname;?></td>
					<td class=data align=center><?php echo $trig_is_const;?></td>
					<?php if (!$printview) { ?>
					<td><a href="trig_properties.php<?php echo $query; ?>"><?php echo $strProperties; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP TRIGGER $trig_sql ON $classname");?>&zero_rows=<?php echo urlencode("$strTrigger $trigger $strHasBeenDropped");?>"><?php echo $strDrop; ?></td>
					<?php } ?>
					</tr>
					<?php
				}
				echo "</table>\n";
			}
			break;		
		case "index":
			$sql_get_index = "SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relkind ='i' ORDER BY relname";
			$indexs = @pg_exec($link, $sql_get_index) or pg_die(pg_errormessage(), $sql_get_index, __FILE__, __LINE__);
			$num_indexs = @pg_numrows($indexs);
			if ($num_indexs == 0) {
				echo "<br/><b>$strNo $strIndicies $strFound</b><br/>";
			} else {
				$i = 0;
				echo "<table border=$cfgBorder>\n<tr bgcolor=lightgrey><th align=center colspan=3>$strIndicies</th></tr>\n";
				while ($i < $num_indexs) {
					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorOne;
					$index = pg_result($indexs, $i, 'relname');
					$query = "?db=$db&server=$server&rel_type=$rel_type&index=$index";
					$prop_query = "
						SELECT 
							ic.relname AS index_name, 
							bc.relname AS tab_name, 
							ta.attname AS column_name,
							i.indisunique AS unique_key,
							i.indisprimary AS primary_key
						FROM 
							pg_class bc,
							pg_class ic,
							pg_index i,
							pg_attribute ta,
							pg_attribute ia
						WHERE
							bc.oid = i.indrelid
							AND ic.oid = i.indexrelid
							AND ia.attrelid = i.indexrelid
							AND ta.attrelid = bc.oid
							AND ic.relname = '$index'
							AND ta.attrelid = i.indrelid
							AND ta.attnum = i.indkey[ia.attnum-1]
						ORDER BY
							index_name, tab_name, column_name
					";
			?>
				<tr bgcolor=<?php echo $bgcolor; ?>>
				<td class=data><b><?php echo $index;?></b></td>
				<?php if (!$printview) { ?>
				<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode($prop_query);?>&zero_rows=<?php echo urlencode("$strNo $strProperties.");?>"><?php echo $strProperties; ?></td>
				<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP INDEX $cfgQuotes$index$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strIndex $index $strHasBeenDropped.");?>"><?php echo $strDrop; ?></td>
				<?php } ?>
				</tr>
			<?php
				$i++;
				}
				echo "</table>\n";
			}
			break;
		case "operator":	// Operators
			$max = $builtin_max;

			$sql_get_oper = "
				select
               po.oid,
					po.oprname,
					(select typname from pg_type pt where pt.oid=po.oprleft) as oprleftname,
					(select typname from pg_type pt where pt.oid=po.oprright) as oprrightname,
					(select typname from pg_type pt where pt.oid=po.oprresult) as resultname
				from
					pg_operator po
				where
					po.oid > '$max'::oid
				order by
					po.oprname, po.oid
			";
			$operators = pg_exec($link, $sql_get_oper);
			$num_operators = @pg_numrows($operators);
			if ($num_operators == 0) {
				echo "<br/><b>$strNo $strOperators $strFound</b><br/>";
			} else {
				echo "<table border=$cfgBorder>\n<tr bgcolor=lightgrey><th align=center>$strOperator</th>";
				echo "<th align=center>$strLeft</th><th align=center>$strRight</th><th align=center>$strRetType</th><th colspan=2>$strAction</th></tr>\n";

				for ($i = 0; $i < $num_operators; $i++) {
					$oper_ary = pg_fetch_array($operators, $i);

					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;

					if($oper_ary[oprleftname] == '') $oper_ary[oprleftname] = 'none';
					if($oper_ary[oprrightname] == '') $oper_ary[oprrightname] = 'none';
					if($oper_ary[resultname] == '') $oper_ary[resultname] = 'none';
					$query = "?db=$db&rel_type=$rel_type&operator_oid=" . urlencode($oper_ary[oid]);
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><b><?php echo htmlspecialchars($oper_ary[oprname]);?></b></td>
					<td class=data><b><?php echo $oper_ary[oprleftname];?></b></td>
					<td class=data><b><?php echo $oper_ary[oprrightname];?></b></td>
					<td class=data><b><?php echo $oper_ary[resultname];?></b></td>
					<?php if (!$printview) { ?>
					<td><a href="oper_properties.php<?php echo $query; ?>"><?php echo $strProperties; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DROP OPERATOR $oper_ary[oprname] ($oper_ary[oprleftname], $oper_ary[oprrightname])");?>&zero_rows=<?php echo urlencode("$strOperator $operator $strHasBeenDropped");?>"><?php echo $strDrop; ?></td>
					<?php } ?>
					</tr>
					<?php
				}
				echo "</table>\n";
			}
			break;
      default: // Tables
			$sql_get_tables = "SELECT tablename, tableowner FROM pg_tables WHERE tablename NOT LIKE 'pg%' ORDER BY tablename";
			$tables = @pg_exec($link, $sql_get_tables) or pg_die(pg_errormessage(), $sql_get_tables, __FILE__, __LINE__);

			$num_tables = @pg_numrows($tables);
			if ($num_tables == 0) {
				echo "<br/><b>$strNo $strTables $strFound</b><br/>";
			} else {
				$i = 0;

				echo "
					<table border=$cfgBorder>\n
						<tr bgcolor=lightgrey>
							<th align=center>$strTable</th>
							<th align=center>$strOwner</th>
				";
				if (!$printview) {
					echo "<th colspan=7>$strAction</th>";
				}
				echo "
							<th>$strRecords</th>
							<th>Comments</th>
						</tr>
				";
				
				while ($i < $num_tables) {
					$bgcolor = $cfgBgcolorOne;
					$i % 2 ? 0 : $bgcolor = $cfgBgcolorTwo;
					$table = pg_result($tables, $i, 'tablename');
					$owner = pg_result($tables, $i, 'tableowner');
					$enc_table = urlencode($table);
					$query = "?db=$db&server=$server&rel_type=$rel_type&table=$enc_table&goto=db_details.php";

					$qrComments = "SELECT description FROM pg_class c, pg_description d WHERE c.relname='$table' AND c.oid = d.objoid"; 
					$rsComments = pg_exec($qrComments);
					$comments = pg_numrows($rsComments) ? pg_result($rsComments, 0, 0) : "";
					?>
					<tr bgcolor=<?php echo $bgcolor; ?>>
					<td class=data><b><?php echo $table;?></b></td>
					<td class=data><b><?php echo $owner;?></b></td>
					<?php if (!$printview) { ?>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("SELECT * FROM $cfgQuotes$table$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strEmptyResultSet");?>"><?php echo $strBrowse; ?></td>
					<td><a href="tbl_select.php<?php echo $query;?>"><?php echo $strSelect; ?></td>
					<td><a href="tbl_change.php<?php echo $query;?>"><?php echo $strInsert; ?></td>
					<td><a href="tbl_properties.php<?php echo $query;?>"><?php echo $strProperties; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&sql_query=<?php echo urlencode("DELETE FROM $cfgQuotes$table$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strTable $table $strHasBeenEmptied.");?>"><?php echo $strEmpty; ?></td>
					<td><a href="sql.php<?php echo $query;?>&goto=db_details.php&reload=true&sql_query=<?php echo urlencode("DROP TABLE $cfgQuotes$table$cfgQuotes");?>&zero_rows=<?php echo urlencode("$strTable $table $strHasBeenDropped.");?>"><?php echo $strDrop; ?></td>
					<td><a href="tbl_privilege.php<?php echo $query;?>&goto=db_details.php"><?php echo $strPrivileges; ?></td>
					<?php } ?>
					<td align=right><?php if ($cfgCountRecs) count_records($link, $table); ?></td>
					<td><?php echo $comments; ?></td>
					</tr>
					<?php
					$i++;
				}
				echo "</table>\n";
			}
			break;
		}
} // End If sql_query exists

if (!$printview) {
?>
<ul>
<?php
if (!empty($rel_type)) {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=>$strDisplay $strTables</a> ";
}
if ($rel_type != "view") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=view>$strDisplay $strViews</a> ";
}
if ($rel_type != "sequence") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=sequence>$strDisplay $strSequences</a> ";
}
if ($rel_type != "function") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=function>$strDisplay $strFuncs</a> ";
}
if ($rel_type != "index") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=index>$strDisplay $strIndicies</a> ";
}
if ($rel_type != "trigger") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=trigger>$strDisplay $strTriggers</a> ";
}
if ($rel_type != "operator") {
	echo "<li><a href=db_details.php?db=$db&server=$server&rel_type=operator>$strDisplay $strOperators</a> ";
}

$query = "?db=$db&rel_type=$rel_type&goto=db_details.php";

?>
</ul>
<hr>
<div align="left">
<ul>
<li>
<form method="post" action="db_readdump.php">
<input type="hidden" name="server" value="<?php echo $server;?>">
<input type="hidden" name="db" value="<?php echo $db;?>">
<input type="hidden" name="goto" value="db_details.php">
<input type="hidden" name="zero_rows" value="<?php echo $strSuccess; ?>">
<?php echo $strRunSQLQuery.$db." ".show_docu("sql-select.html");?>:<br/><a name="sql_box"></a>
<textarea name="sql_query" cols="60" rows="6" wrap="VIRTUAL" style="width: <?php echo $cfgMaxTextAreaSize;?>"><?php echo stripslashes($sql_query); ?></textarea>
<input type="submit" name="SQL" value="<?php echo $strGo; ?>">
</form>
<form method="POST" action="file_sql.php" enctype="multipart/form-data">
<input type="hidden" name="server" value="<?php echo $server;?>">
<input type="hidden" name="db" value="<?php echo $db;?>">
<input type="hidden" name="goto" value="db_details.php">
<input type="hidden" name="zero_rows" value="<?php echo $strSuccess; ?>">
<?php echo $strFileLocation?>:<br/>
<input type="file" name="userfile">
<input type="submit" value="<?php echo $strGo; ?>">
</form>

<li><a href="tbl_qbe.php<?php echo $query;?>"><?php echo $strQBE;?></a>
<li><form method="post" action="db_dump.php"><?php echo $strViewDumpDB;?><br/>
<table>
    <tr>
        <td>
            <input type="radio" name="what" value="structure" checked><?php echo $strStrucOnly;?>
        </td>
        <td>
            <input type="checkbox" name="drop" value="1"><?php echo $strStrucDrop;?>
        </td>
        <td colspan="2">
            <input type="submit" value="<?php echo $strGo;?>">
        </td>
    </tr>
    <tr>
        <td>
            <input type="radio" name="what" value="data"><?php echo $strStrucData;?>
        </td>
        <td>
            <input type="checkbox" name="asfile" value="sendit"><?php echo $strSend;?>
        </td>
    </tr>
</table>
<input type="hidden" name="server" value="<?php echo $server;?>">
<input type="hidden" name="db" value="<?php echo $db;?>">
<input type="hidden" name="table" value="<?php echo $table;?>">
</form>

<li>
<form method="post" action="tbl_create.php">
<input type="hidden" name="server" value="<?php echo $server;?>">
<input type="hidden" name="db" value="<?php echo $db;?>">
<?php echo $strCreateNewTable.$db;?>:<br/>
<?php echo $strName.":"; ?> <input type="text" name="table"><br/>
<?php echo $strNumFields.":"; ?> <input type="text" name="num_fields" size=2>
<input type="submit" value="<?php echo $strGo; ?>">
</form>

<li>
<form method="post" action="seq_create.php">
<input type="hidden" name="server" value="<?php echo $server;?>">
<input type="hidden" name="db" value="<?php echo $db;?>">
<?php echo $strCreateNew . $strSequence . $strOnDB . $db;?>:<br/>
<?php echo $strName . ":"; ?> <input type="text" name="seq_name"><br/>
<?php echo $strStart . ":"; ?> <input type="text" name="startval" size="2" value="1">
<input type="submit" value="<?php echo $strGo; ?>">
</form>

<li><a href="db_details.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&rel_type=<?php echo $rel_type; ?>&printview=1"><?php echo $strPrintScreen;?></a>

<li><a href="sql.php?server=<?php echo $server;?>&db=<?php echo $cfgDefaultDB; ?>&sql_query=<?php echo urlencode("DROP DATABASE $cfgQuotes$db$cfgQuotes");?>&zero_rows=<?php echo urlencode($strDatabase." ".$db." ".$strHasBeenDropped);?>&goto=main.php&reload=true"><?php echo $strDropDB." ".$db;?></a>
<br/><br/>
<!--li><a href="reports.php?server=<?php echo $server;?>&dbname=<?php echo $db; ?>&goto=main.php"><?php echo $strReports; ?></a-->
<!-- doj added two links to vacuum the current database -->
<li><a href="sql.php?server=<?php echo $server;?>&db=<?php echo $db;?>&sql_query=VACUUM&zero_rows=<?php echo urlencode($strDatabase." ".$db." ".$strHasBeenVacuumed);?>&goto=db_details.php"><?php echo $strVacuumDB." ".$db;?></a>
<li><a href="sql.php?server=<?php echo $server;?>&db=<?php echo $db;?>&sql_query=VACUUM+ANALYZE&zero_rows=<?php echo urlencode($strDatabase." ".$db." ".$strHasBeenVacuumed);?>&goto=db_details.php"><?php echo $strVacuumAnalyzeDB." ".$db;?></a>
<br/><br/>
<!-- doj -->
<li><a href="oper_create.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&goto=main.php"><?php echo $strCreateNew . $strOperator;?></a>
<li><a href="func_edit.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&goto=main.php&create=1"><?php echo $strCreateNew . $strFunction;?></a>
<li><a href="trig_create.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&goto=main.php"><?php echo $strCreateNew . $strTrigger;?></a>
<li><a href="view_create.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&goto=main.php"><?php echo $strCreateNew . $strView;?></a>
<li><a href="db_privilege.php?server=<?php echo $server;?>&db=<?php echo $db; ?>&goto=main.php"><?php echo $strPrivileges; ?></a>
</ul>
</div>
<?php
} else {
	echo "<script language=\"JavaScript1.2\">window.print()</script>";
} // ending the printview if statement

include ("footer.inc.php");
?>
