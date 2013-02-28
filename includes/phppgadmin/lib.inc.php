<?php

/* $Id: lib.inc.php,v 1.3 2004/12/13 11:55:18 fullo Exp $ */

if (!defined("LIB_INC")) {
error_reporting(E_ALL ^ E_NOTICE);

// ------- Workaround for register_globals = Off ------ //
// ------  borrowed from phpMyAdmin and modified ------ //
if (!($reg_globals = ini_get("register_globals"))) {
	if (!empty($HTTP_GET_VARS)) {
		extract($HTTP_GET_VARS);
	} elseif (!empty($_GET)) {
		extract($_GET);
	}
	
	if (!empty($HTTP_POST_VARS)) {
		extract($HTTP_POST_VARS);
	} elseif (!empty($_POST)) {
		extract($_POST);
	}
	
	if (!empty($HTTP_COOKIE_VARS)) {
		extract($HTTP_COOKIE_VARS);
	} elseif (!empty($_COOKIE)) {
		extract($_COOKIE);
	}

	if (!empty($_FILES)) {
		while (list($name, $value) = each($_FILES)) {
		    $$name = $value['tmp_name'];
		}
	} elseif (!empty($_FILES)) {
		while (list($name, $value) = each($_FILES)) {
		    $$name = $value['tmp_name'];
		}
	}

	//echo "Globals: $reg_globals";
} else {
	//print_r($reg_globals);
}
// ----------------------- end ----------------------- //


if (file_exists("config.inc.php")) { 
	include("config.inc.php");
}
else {
	echo "Configuration Error: You must rename/copy config.inc.php-dist to config.inc.php and set your appropriate settings"; 
	exit;
	}

if (!function_exists("pg_connect")) {
	echo "You do not have postgresql support built into your PHP Web Server.<br/>";
	echo "phpPgAdmin requires postgresql support to function properly!<br/>";
	echo "Please check the PHP documentation for corrective action.";
	exit;
	}

if ($cfgQuotes) {
	$cfgQuotes = "\"";
} else {
	unset($cfgQuotes);
}

//***************************************************************************************
//      Function:       build_connstr($settings)
//      Puspose:        Build the connection string
//      Params:         $settings -- Associative array containing all info for connection.

function build_connstr($settings) {
	$conn['host'] = $GLOBALS['cfgServer']['host']; // Even if it's a local connection, we need this so it doesn't use $PGHOST env-var
	if (!empty($GLOBALS['cfgServer']['port'])) $conn['port'] = $GLOBALS['cfgServer']['port'];
	$conn['dbname']		= $settings['dbname'];
	$conn['user']		= $settings['user'];
	if (isset($settings['password'])) $conn['password'] = $settings['password'];

	while (list($param, $value) = each($conn)) {
		$conn_str .= "$param='$value' ";
	}
	return $conn_str;
}


//***************************************************************************************
//      Function:       select_box($settings)
//      Puspose:        Display a drop-down box
//      Params:         $settings -- Associative array containing all info about the select.
//                                      - selected      -- The selected/default option
//                                      - values        -- array containing all options.  Associative or normal array
//                                      - multiple      -- specifies wether is a multiple select or not

function select_box($settings) {
	// Compile the attributes such as size etc.
	while (list($thiskey, $thisval) = each($settings)) {
		if ($thiskey == "multiple") {
			$attribs .= " multiple";
		} else if (($thiskey != "values") && ($thiskey != "name") && ($thiskey != "selected")) {
			$attribs .= " $thiskey=\"$thisval\"";
		} 
	}
	if (ereg("multiple", $attribs)) {
		$name = "$settings[name][]";
	} else {
		$name = "$settings[name]";
	}
	
	// Start the selectbox tag
	$strReturn .= "\n<select name=\"$name\" $attribs>";
	// Check if the valuse are in associative array or not
	if (count($settings[values]) > 0) {
		if (!isset($settings[values][0])) {
			while (list($disp, $val) = each($settings[values])) {
				$strReturn .= "\n<option value=\"".htmlentities(chop($val))."\"";
				// Check if the selected is an array
				if (is_array($settings[selected])) {
					// Loop through the selected array
					for ($iSelIndex = 0; $iSelIndex < count($settings[selected]); $iSelIndex++) {
						if ($val == chop($settings[selected][$iSelIndex]))  {
							$strReturn .= " selected";
						}
					}
				} else {
					if ($val == chop($settings[selected]))  {
						$strReturn .= " selected";
					}
				}
				$strReturn .= ">$disp";
			}
		} else { // If not in associative array
			for ($index = 0; $index < count($settings[values]); $index++) {
				$strReturn .= "\n<option value=\"".htmlentities(chop($settings[values][$index]))."\"";
				// Check if the selected is an array
				if (is_array($settings[selected])) {
					for ($iSelIndex = 0; $iSelIndex < count($settings[selected]); $iSelIndex++) {
						if (chop($settings[values][$index]) == chop($settings[selected][$iSelIndex])) {
							$strReturn .= " selected";
						}
					}
				} else {
					if (chop($settings[values][$index]) == chop($settings[selected])) {
						$strReturn .= " selected";
					}
				}
				$strReturn .= ">".$settings[values][$index];
			}
		}
	} else {
		$strReturn .= "<option value=\"0\">No Values";
	}
	$strReturn .= "\n</select>\n";
	return $strReturn;
}

// -----------------------------------------------------------------
// Function: 	pg_die($error, $query)
// Params:		$error -- The displayable error text.  Usually passed as pg_errormessage()
//				$query -- The query which was attempted
function pg_die($error = "", $query = "", $err_file = __FILE__, $err_line = __LINE__) {
	global $strError,$strMySQLSaid, $strBack, $sql_query, $HTTP_REFERER, $SCRIPT_FILENAME, $link, $db, $table, $cfgDebug, $server;
	
	echo "<b><font face=\"arial,helvetican,sans-serif\" color=\"red\">$strError - $err_file -- Line: $err_line</font></b><p>";
	if (empty($error)) {
		$error = @pg_errormessage();
	}
	echo "<font face=\"arial,helvetican,sans-serif\" color=\"red\">", $strMySQLSaid . $error;
	if (empty($query)) {
		$query = $sql_query;
	}
	if (!empty($query)) {
		echo "<br/>Your query: <br/><b>",  nl2br(htmlentities($query)), "</b>";
	}
	echo "<br/><a href=\"javascript:history.back()\">$strBack</a></font>";

	if ($cfgDebug) {
		echo "<br/>Link: $link<br/>DB: $db<br/>Table: $table<br/>Server: $server";
	}

	echo "<p>";
	include ("footer.inc.php");
	exit;
}

// -----------------------------------------------------------------
// Function: 	pre_query($query)
// Params:		$query -- Query String to prepare
// Note:		This is usually called within the pg_exec function -- pg_exec($dbh, pre_query($sql_get_auth));
function pre_query($query) {
	global $common_ver;
	if ($common_ver < 7 || empty($version)) { 
	//	$query = str_replace("\n", "", $query);
	}
	return $query;
}


reset($cfgServers);
while(list($key, $val) = each($cfgServers)) {
	// Don't use servers with no hostname and not local
	if (empty($val['host']) && !$val['local']) {
		unset($cfgServers[$key]);
	}
}

if (empty($server) || !isset($cfgServers[$server]) || !is_array($cfgServers[$server])) {
	$server = $cfgServerDefault;
}

// If no database is selected, set it to default db
if (empty($db))
	$db = $cfgDefaultDB;

if ($server == 0) {
	// If no server is selected, make sure that $cfgServer is empty
	// (so that nothing will work), and skip server authentication.
	$cfgServer = array();
} else {
	// Otherwise, set up $cfgServer and do the usual login stuff.
	$cfgServer = $cfgServers[$server];

	if (isset($cfgServer['only_db']) && !isset($db)) {
		$db = $cfgServer['only_db'];
	}

	$ver_realm = "$cfgProgName $cfgVersion";
	if ($cfgServer['local']) {
		$short_realm .= "$HTTP_HOST:local";
		if (!empty($cfgServer['port'])) {
			$short_realm .= ":$cfgServer[port]";
		}
	} else {
		$short_realm .= "$HTTP_HOST";
		if (!empty($cfgServer['host'])) {
			$short_realm .= ":$cfgServer[host]";
		}
		if (!empty($cfgServer['port'])) {
			$short_realm .= ":$cfgServer[port]";
		}
	}

	if ($cfgPersistentConnections) {
		$connect = "pg_pconnect";
	} else {
		$connect = "pg_connect";
	}

	if ($cfgServer['adv_auth']) {
		if (isset($set_username) && isset($set_password)) {
			$PHP_PGADMIN_USER = $set_username;
			$PHP_PGADMIN_PW = $set_password;
		}
	
		if (isset($mode) && $mode == "logout") {
			unset($PHP_PGADMIN_USER);
			unset($PHP_PGADMIN_PW);
			setcookie("PHP_PGADMIN_PW"); // Delete password cookie 
		}
		
		if (empty($PHP_PGADMIN_USER)) {
			$no_include = true;
			include("header.inc.php");
			include("login.inc.php");
			include("footer.inc.php");
			exit;
		} else if (isset($PHP_PGADMIN_USER)) {
			$conn_str = build_connstr(array("dbname"=>$db, "user"=>$PHP_PGADMIN_USER, "password"=>$PHP_PGADMIN_PW));
			
			if ($link = @$connect($conn_str)) {
				setcookie("PHP_PGADMIN_USER", $PHP_PGADMIN_USER, time() + (60 * 60 * 24 * 30)); // One month
				setcookie("PHP_PGADMIN_PW", $PHP_PGADMIN_PW); // Till the browser is closed
			} else {
				$strMsg = $strWrongUser;
				$no_include = true;
				include("header.inc.php");
				include("login.inc.php");
				include("footer.inc.php");
				exit;
			}
		}
		
		$realm = $ver_realm . " " . $short_realm;
	} else {
		$conn_str = build_connstr(array("dbname"=>$db, "user"=>$cfgServer['user'], "password"=>$cfgServer['password']));
	
		if ($link = @$connect($conn_str)) {
			setcookie("PHP_PGADMIN_USER", $cfgServer['user'], time() + (60 * 60 * 24 * 30)); // One month
			setcookie("PHP_PGADMIN_PW", $cfgServer['password']); // Till the browser is closed
			$PHP_PGADMIN_USER = $cfgServer['user'];
			$PHP_PGADMIN_PW = $cfgServer['password'];
		}
	}
	
	if (!$link) {
		if ($cfgServer['adv_auth']) {
			$strMsg = $strWrongUser;
			$no_include = true;
			include("header.inc.php");
			include("login.inc.php");
			include("footer.inc.php");
			exit;
		} else {
			pg_die(@pg_errormessage(), "Unable to connect with settings: $conn_str", __FILE__, __LINE__);
		}
	}
	
	$sql_get_userperm = "SELECT usecreatedb FROM pg_user WHERE usename = '$PHP_PGADMIN_USER'";
	$rs_perm = @pg_exec($link, pre_query($sql_get_userperm)) or pg_die(pg_errormessage(), $sql_get_userperm, __FILE__, __LINE__);
	$create_db = @pg_result($rs_perm, 0, "usecreatedb");
	
	$res_version = @pg_exec($link, "SELECT version() as version") or pg_die(pg_errormessage(), "", __FILE__, __LINE__);
	$row_version = @pg_result($res_version, 0, "version");
	
	// Split out the version numbers for later use  $ver_num[0] contains the entire version
	ereg("([6-9]).([0-9A-Za-z])[.]?([0-9])?", $row_version, $ver_num);
	// ereg("([6-9]).([0-9]).([0-9])", $row_version, $ver_num);
	$version = $ver_num[0];
	$major_version	= $ver_num[1];
	$minor_version	= $ver_num[2];
	$minmin_version	= $ver_num[3];
	$common_ver		= "$major_version.$minor_version";
	
	// Set the maximum built-in ID.  This assumes the oid of template1 for versions < 7.1, and gets it from
	// the database for 7.1 and above.
	if ($common_ver >= 7.1) {
		$qrDB = "SELECT datlastsysoid FROM pg_database WHERE datname='$db'";
		$rs = @pg_exec($link, $qrDB) or pg_die(pg_errormessage(), $qrDB, __FILE__, __LINE__);
		$builtin_max = @pg_result($rs, 0, "datlastsysoid");
	} else {
		$rs = @pg_exec($link, "SELECT oid FROM pg_database WHERE datname='template1'") or pg_die(pg_errormessage(), "", __FILE__, __LINE__);
		$builtin_max = @pg_result($rs, 0, "oid");
	}
	
	// Let's determine whether the user logged in is a superuser
	
	$strBigSuper = $PHP_PGADMIN_USER;
	$qrIsSuper = "SELECT usesuper FROM pg_user WHERE usename = '$strBigSuper' AND usesuper = 't'";
	$rsIsSuper = @pg_exec($link, pre_query($qrIsSuper));
	if (@pg_numrows($rsIsSuper)) {
		$bSuperUser = true;
	}
}
	
function display_table ($qry_res, $limited = false) {
	global $cfgBorder, $cfgBgcolorOne, $cfgBgcolorTwo, $cfgMaxRows, $pos, $server, $link, $db, $table, $sql_query, $sql_order, $cfgOrder, $cfgShowBlob;
	global $strShowingRecords,$strTotal,$strEdit,$strPrevious,$strNext,$strAction,$strDelete,$strDeleted,$strPos1,$strEnd,$pri_keys,$goto;
	global $cfgQuotes, $cfgMaxPages, $strMore, $cfgMaxText, $link;

	if (!isset($pos))
		$pos = 0;

	$pos_next = $pos + $cfgMaxRows;
	$pos_prev = $pos - $cfgMaxRows;
	
	if (!$limited) { 
		$dt_result = $qry_res;

		$num_rows = @pg_numrows($dt_result);
		$num_fields = @pg_numfields($dt_result);
		$iNumRows = $num_rows;
	} else {
		if (!$cnt_result = @pg_exec($link, pre_query($qry_res))) {
			include("header.inc.php");
			pg_die(pg_errormessage($link), $cnt_result, __FILE__, __LINE__);
		} else {
			$num_rows = pg_numrows($cnt_result);
			$full_query = $qry_res . " LIMIT $pos, $cfgMaxRows";
			if (!$dt_result = @pg_exec($link, pre_query($full_query))) {
				include("header.inc.php");
				pg_die(pg_errormessage($link), $full_query, __FILE__, __LINE__);
			}
		}
		$iNumRows = $cgMaxRows;
	}

	if ($num_rows < $pos_next) {
		$pos_next = $num_rows;
	}
	
	$strNavigation .= "<table border=0>\n";
	$strNavigation .=  "<td align=left>";
	if ($pos >= $cfgMaxRows) {
		//doj added link to beginning of results
		$strNavigation .= "<a href=\"sql.php?server=$server&db=$db&table=$table&sql_query=" . urlencode($sql_query) . "&sql_order=" . urlencode($sql_order) . "&pos=0&goto=$goto\">&lt;&lt; $strPos1 </a> | ";

		$strNavigation .= "	<a href=\"sql.php?server=$server&db=$db&table=$table&sql_query=" . urlencode($sql_query) . "&sql_order=" . urlencode($sql_order) . "&pos=$pos_prev&goto=$goto\">&lt; $strPrevious </a> | ";
	} else {
		$strNavigation .= "&nbsp;";
	}
	$strNavigation .= "</td>\n";

	$strNavigation .= "<td align=center>\n";

	//doj now only the previous and next $cfgMaxPages pages will be shown
	if (empty($cfgMaxPages)) {
		$cfgMaxPages = 9;
	}

	$iCount = $pos - ($cfgMaxRows * $cfgMaxPages);
	if ($iCount < 0) 
		$iCount = 0;

	$iPageCnt = (int)($iCount / $cfgMaxRows); 
	$iPageStop = $pos + ($cfgMaxRows * ($cfgMaxPages + 1));

	for (; $iCount < $iNumRows && $iCount < $iPageStop; $iCount += $cfgMaxRows) {
	  //doj until here
		$iPageCnt++;
		if ($iCount != $pos) {
			$strPages .= "<a href=\"sql.php?server=$server&db=$db&table=$table&sql_query=".urlencode($sql_query)."&sql_order=".urlencode($sql_order)."&pos=$iCount&goto=$goto\">$iPageCnt</a> | ";
		} else {
			if ($iNumRows > $cfgMaxRows) {
				$strPages .= "$iPageCnt | ";
			}
		}
	}
	$strNavigation .= ereg_replace(" \| $", "", $strPages);
	
	$strNavigation .= "</td>\n";
	
	$strNavigation .= "<td align=right>";
	if ($pos_next < $num_rows) {
		$strNavigation .= " | <a href=\"sql.php?server=$server&db=$db&table=$table&sql_query=".urlencode($sql_query)."&sql_order=".urlencode($sql_order)."&pos=$pos_next&goto=$goto\"> $strNext &gt;</a>";

		//doj link to end of results
		$pos_end = $num_rows - $cfgMaxRows;
		$strNavigation .= " | <a href=\"sql.php?server=$server&db=$db&table=$table&sql_query=".urlencode($sql_query)."&sql_order=".urlencode($sql_order)."&pos=$pos_end&goto=$goto\"> $strEnd &gt;&gt;</a>";
	} else {
		$strNavigation .= "&nbsp;";
	}
	$strNavigation .= "</td>\n";
	$strNavigation .= "</table>\n";

	if ($num_rows > 1) { 
		echo "$strShowingRecords $pos - $pos_next ($num_rows $strTotal)";
	}

	echo $strNavigation;
	echo "<table border=$cfgBorder><tr>";
  
	for ($i_field = 0; $i_field < $num_fields; $i_field++) {
		$field_name = pg_fieldname($dt_result, $i_field);
        if (@pg_numrows($dt_result) > 1) {
            $sort_order = urlencode(" ORDER BY $cfgQuotes$field_name$cfgQuotes $cfgOrder");
            echo "<th><A HREF=\"sql.php?server=$server&db=$db&pos=$pos&sql_query=".urlencode($sql_query)."&sql_order=$sort_order&table=$table&goto=$goto\">$field_name</a></th>\n";
		} else {
			echo "<th>$field_name</th>";
		}
	} 
	$priCnt = count($pri_keys);
	if ($priCnt > 0) {
		// echo "<th colspan=\"2\">$strAction : $priCnt</th>";
	}
	echo "</tr>\n"; 
	
	for ($i_row = $pos; $i_row < $pos_next; $i_row++) {
		$row = pg_fetch_row($dt_result, $i_row);
		unset($primary_key);
		$bgcolor = $cfgBgcolorOne;
		$i_row % 2  ? 0: $bgcolor = $cfgBgcolorTwo;
		echo "<tr bgcolor=$bgcolor>"; 
		for ($i_field = 0; $i_field < pg_numfields($dt_result); $i_field++) { 
			if (!isset($row[$i_field])) 
				unset($row[$i_field]);
			$field_type = pg_fieldtype($dt_result, $i_field);
			$field_name = pg_fieldname($dt_result, $i_field);
			if (eregi("int|numeric", $field_type)) {
			
				echo "<td align=right valign=top>&nbsp;$row[$i_field]&nbsp;</td>\n";
			} elseif ($cfgShowBlob == false && eregi("BLOB", $field_type)) {
				echo "<td align=right valign=top>&nbsp;[BLOB]&nbsp;</td>\n"; 
			} elseif (eregi("text|char", $field_type)) {
				// If the text is longer than $cfgMaxText characters, let's cut it short
				if (strlen($row[$i_field]) > $cfgMaxText && !empty($cfgMaxText)) {
					$strLgText =  nl2br(htmlspecialchars(substr($row[$i_field], 0, $cfgMaxText))) . " <br/><b>... $strMore ...</b>";
				} else {
					$strLgText = nl2br(htmlspecialchars($row[$i_field]));
				}
				echo "<td valign=top>&nbsp;$strLgText</td>\n"; 
			} elseif ($field_type == "bool") {
				echo "<td valign=top>&nbsp;", bool_YesNo($row[$i_field]), "&nbsp;</td>\n"; 
			} else {
				echo "<td valign=top>&nbsp;", nl2br(htmlspecialchars($row[$i_field])), "&nbsp;</td>\n"; 
			}
			for ($i_pri_keys = 0; $i_pri_keys < count($pri_keys); $i_pri_keys++) {
				if ($field_name == $pri_keys[$i_pri_keys]) {
					if (eregi("text|name|char|inet|bool", $field_type)) {
						$strQuote = "'";
						$add_null = empty($row[$i_field]) ? true : false;
					} elseif (eregi("date|time", $field_type)) {
						if (empty($row[$i_field])) {
							$strQuote = "";
							$row[$i_field] = "NULL";
						} else {
							$strQuote = "'";
						}
					} else {
						unset($strQuotes);
					}
					if ($add_null) $primary_key .= " (";
					$primary_key .= "$cfgQuotes$field_name$cfgQuotes = ";
					$primary_key .= $strQuote . $row[$i_field] . $strQuote;
					if ($add_null) 	$primary_key .= " OR $cfgQuotes$field_name$cfgQuotes = NULL)";
					$primary_key .= " AND ";
					break; // This breaks us out in case the primary key column also has a seperate unique key index created on it
				}
			}
		} 

		if (!empty($primary_key)) {
			if (!empty($GLOBALS[QUERY_STRING])) {
				$extras = $GLOBALS[QUERY_STRING];
			} else {
				$extras = "server=$server&db=$db&table=$table&sql_query=" . urlencode($sql_query) . "&goto=$goto";
			}
			$primary_key = urlencode(ereg_replace("AND $", "", $primary_key));
			$query = "&server=$server&db=$db&table=$table&goto=" . urlencode("sql.php?" . $extras);
			
			$query_vars = explode("&", $query);
			reset($query_vars);
			while(list(, $qr_var) = each($query_vars)) {
				$var_parts = explode("=", $qr_var);
				$strHiddens .= "\n<input type=\"hidden\" name=\"$var_parts[0]\" value=\"$var_parts[1]\">";
			}
			$strHiddens .= "\n<input type=\"hidden\" name=\"edit\" value=\"1\">";
			$strHiddens .= "\n<input type=\"hidden\" name=\"primary_key\" value=\"$primary_key\">";
			
			echo "
				<form action=\"tbl_change.php\" method=\"POST\">
				<td>
					$strHiddens
					<input type=\"submit\" name=\"edit_action\" value=\"$strEdit\">
				</td>
				</form>
			";

			$strHiddens .= "\n<input type=\"hidden\" name=\"sql_query\" value=\"" . urlencode("DELETE FROM $cfgQuotes$table$cfgQuotes WHERE ") . $primary_key . "\">";
			$strHiddens .= "\n<input type=\"hidden\" name=\"zero_rows\" value=\"" . urlencode($strDeleted) . "\">";			
			
			echo "
				<form action=\"sql.php\" method=\"POST\">
				<td>
					$strHiddens
					<input type=\"submit\" name=\"edit_action\" value=\"$strDelete\">
				</td>
				</form>
				</td>
			";
			unset($strHiddens);
		}
	} 

	echo "</table>\n"; 
	echo $strNavigation;
	return $query;
}


// Return $tables CREATE definition 
// Returns a string containing the CREATE statement on success
// "
function get_table_def($link, $table, $crlf) {
	global $drop, $drop_field, $cfgQuotes, $noACL;
	unset($schema_create);
	if (!empty($drop)) {
//		$schema_create .= "DROP TABLE IF EXISTS $table;$crlf";
	}
	$schema_create .= "CREATE TABLE $cfgQuotes$table$cfgQuotes ($crlf";
	
	$sql_get_fields = "
		SELECT 
			a.attnum,
			a.attname AS field, 
			t.typname AS type, 
			a.attlen AS length,
			a.atttypmod AS lengthvar,
			a.attnotnull AS notnull
		FROM 
			pg_class c, 
			pg_attribute a, 
			pg_type t
		WHERE 
			c.relname = '$table'
			and a.attnum > 0
			and a.attrelid = c.oid
			and a.atttypid = t.oid
		ORDER BY a.attnum
	";

	$result = pg_exec($link, pre_query($sql_get_fields)) or pg_die(pg_errormessage(), $sql_get_fields, __FILE__, __LINE__);
	$i = 0;
	while ($row = @pg_fetch_array($result, $i++)) {
		if ($row[field] != $drop_field) {
			$sql_get_default = "
				SELECT d.adsrc AS rowdefault
				FROM pg_attrdef d, pg_class c 
				WHERE 
					c.relname = '$table' 
					AND c.oid = d.adrelid 
					AND d.adnum = $row[attnum]
			";
			$def_res = pg_exec($link, pre_query($sql_get_default)) or pg_die(pg_errormessage(), $sql_get_default, __FILE__, __LINE__);
			if (!$def_res) {
				unset($row[rowdefault]);
			} else {
				$row[rowdefault] = @pg_result($def_res, 0, "rowdefault");
			}
	
			if ($row[type] == "bpchar") {
				// Internally stored as bpchar, but isn't accepted in a CREATE TABLE
				$row[type] = "char";
			}
			
			$schema_create .= "   $cfgQuotes$row[field]$cfgQuotes $row[type]";
			if (eregi("char", $row[type])) {
				if ($row[lengthvar] > 0) {
					$schema_create .= "(" . ($row[lengthvar] - 4) . ")";
				}
			}
			if (eregi("numeric", $row[type])) {
				//Marcellus fixed problem on 5-25-00
				$schema_create .= "(";
				$schema_create .= sprintf("%s,%s", ($row[lengthvar] >> 16) & 0xffff, ($row[lengthvar] - 4) & 0xffff);
				$schema_create .= ")";
	
			}
			if (!empty($row[rowdefault])) {
				if (eregi("text|name|char|date|time|bool", $row[type])) {
					//$delim = "'";
				} else {
					unset($delim);
				}
				$schema_create .= " DEFAULT ". $delim . $row[rowdefault] . $delim;
			}
			if ($row["notnull"] == "t") {
				$schema_create .= " NOT NULL";
			}
			$schema_create .= ",$crlf";
		}
	}

	// Generate constraint clauses for UNIQUE and PRIMARY KEY constraints
	$sql_pri_keys = "
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
			AND bc.relname = '$table'
			AND ta.attrelid = i.indrelid
			AND ta.attnum = i.indkey[ia.attnum-1]
		ORDER BY 
			index_name, tab_name, column_name
	";

	$result = @pg_exec($link, pre_query($sql_pri_keys)) or pg_die(pg_errormessage(), $sql_pri_keys, __FILE__, __LINE__);

	$i = 0;
	while ($row = @pg_fetch_array($result, $i++)) {
		if ($row[column_name] != $drop_field) {
			if ($row[primary_key] == "t") {
				if (!empty($primary_key)) {
					$primary_key .= ", ";
				}
				$primary_key .= "$cfgQuotes$row[column_name]$cfgQuotes";
				$primary_key_name = $row[index_name];
			} else {
				// We have to store all this info becuase it's possible that there is a multi-column key.. 
				// .. we can then loop through it again and build the statement
				$index_rows[$row[index_name]][table] = $table;
				$index_rows[$row[index_name]][unique] = ($row[unique_key] == "t") ? " UNIQUE" : "";
				$index_rows[$row[index_name]][column_names] .= "$cfgQuotes$row[column_name]$cfgQuotes, ";
			}
		}
	}
	if (!empty($index_rows)) {
		while (list($idx_name, $props) = each($index_rows)) {
			$props[column_names] = ereg_replace(", $", "", $props[column_names]);
			$index_create .= "CREATE " . $props[unique] . " INDEX $cfgQuotes$idx_name$cfgQuotes ON $cfgQuotes$table$cfgQuotes (" . $props[column_names] . ");$crlf";
		}
	}
	
	if (!empty($primary_key)) {
		$schema_create .= "   CONSTRAINT $cfgQuotes$primary_key_name$cfgQuotes PRIMARY KEY ($primary_key),$crlf";
	}

	// Generate constraint clauses for CHECK constraints
	$sql_checks = "
		SELECT 
			rcname as index_name, 
			rcsrc 
		FROM 
			pg_relcheck,
			pg_class bc
		WHERE 
			rcrelid = bc.oid 
			and bc.relname = '$table'
			and not exists 
			(select * from pg_relcheck as c, pg_inherits as i 
			where i.inhrelid = pg_relcheck.rcrelid 
			and c.rcname = pg_relcheck.rcname 
			and c.rcsrc = pg_relcheck.rcsrc 
			and c.rcrelid = i.inhparent)
	";

	$result = @pg_exec($link, $sql_checks) or pg_die(pg_errormessage(), $sql_checks, __FILE__, __LINE__);

	$i = 0;
	while ($row = @pg_fetch_array($result, $i++)) {
		$schema_create .= "   CONSTRAINT $cfgQuotes$row[index_name]$cfgQuotes CHECK $row[rcsrc],$crlf";
	}

	$schema_create = ereg_replace(",".$crlf."$", "", $schema_create);
	$index_create = ereg_replace(",".$crlf."$", "", $index_create);

	$schema_create .= "$crlf);$crlf";
	
	if (!empty($index_create)) {
		$schema_create .= $index_create;
	}
	
	if (!$noACL) {
		$sql_get_privilege = "SELECT relacl FROM pg_class WHERE relname = '$table'";
		if (!$res = @pg_exec($link, $sql_get_privilege)) {
			pg_die(pg_errormessage($link), $sql_get_privilege, __FILE__, __LINE__);
		} else {
			// query must return exactely one row (check this ?)
			$row = pg_fetch_array($res, 0);
			if (!empty($row[relacl])) {
				$priv = trim(ereg_replace("[\{\"]", "", $row[relacl]));
				$users = explode(",", $priv);
				for ($iUsers = 0; $iUsers < count($users); $iUsers++) {
					$aryUser = explode("=", $users[$iUsers]);
					if ($aryUser[0] == "") {
						$user = "PUBLIC";
					} else {
						if (eregi("^group", $aryUser[0])) {
							$user = eregi_replace("^group ([[:alnum:]]+)", "GROUP $cfgQuotes\\1$cfgQuotes", $aryUser[0]);
						} else {
							$user = "$cfgQuotes$aryUser[0]$cfgQuotes";
						}
					}
					$privileges = $aryUser[1]; 
					unset($acl_priv);	
					if (strchr($privileges, "a")) {
						$acl_priv .= "INSERT,";
					}
					if (strchr($privileges, "r")) {
						$acl_priv .= "SELECT,";
					}
					if (strchr($privileges, "w")) {
						$acl_priv .= "UPDATE,DELETE,";
					}
					if (strchr($privileges, "R")) {
						$acl_priv .= "RULE,";
					}
	
					$acl_priv = ereg_replace(",$", "", $acl_priv);
					
					if ($acl_priv == "INSERT,SELECT,UPDATE,DELETE,RULE") {
						$acl_priv = "ALL";
					}
					
					if (!empty($acl_priv)) {
						$acl_schema .= "GRANT $acl_priv ON $cfgQuotes$table$cfgQuotes TO $user;$crlf";
					} else {
						// $acl_schema .= "REVOKE ALL ON $cfgQuotes$table$cfgQuotes FROM $cfgQuotes$user$cfgQuotes;$crlf";
					}
					
				} // Close for loop
			}
			$schema_create .= $acl_schema;
		}
		
	}
	
	
	return (stripslashes($schema_create));
} 

// Get the content of $table as a series of INSERT statements.
// After every row, a custom callback function $handler gets called.
// $handler must accept one parameter ($sql_insert);
function get_table_content($link, $table, $handler) {
	global $cfgQuotes;
	$result = @pg_exec($link, "SELECT * FROM $cfgQuotes$table$cfgQuotes") or pg_die(pg_errormessage(), "", __FILE__, __LINE__);
	$iNumFields = pg_numfields($result);

	// Gather info about each column in the table	
	for ($iField = 0; $iField < $iNumFields; $iField++) {
		$aryType[] = pg_fieldtype($result, $iField);
		$aryName[] = pg_fieldname($result, $iField);
	}
	
	$iRec = 0;
	while ($row = @pg_fetch_array($result, $iRec++)) {
		unset($schema_vals);
		unset($schema_fields);
		unset($schema_insert);
		
		for ($iFieldVal = 0; $iFieldVal < $iNumFields; $iFieldVal++) {
			$strVal = $row[$aryName[$iFieldVal]];
			if (eregi("char|text", $aryType[$iFieldVal])) {
				$strQuote = "'";
				$strEmpty = "";
				$strVal = addslashes($strVal);
			} elseif (eregi("date|time|inet|bool", $aryType[$iFieldVal])) {
				if (empty($strVal)) {
					$strQuote = "";
				} else {
					$strQuote = "'";
				}
				$strEmpty = "NULL";
			} else {
				$strQuote = "";
				$strEmpty = "NULL";
			}
			if (empty($strVal) && $strVal != "0") {
				$strVal = $strEmpty;
			}
			$schema_vals .= " $strQuote$strVal$strQuote,";
			$schema_fields .= " $cfgQuotes$aryName[$iFieldVal]$cfgQuotes,";
		}
		
		$schema_vals = ereg_replace(",$", "", $schema_vals);
		$schema_vals = ereg_replace("^ ", "", $schema_vals);
		$schema_fields = ereg_replace(",$", "", $schema_fields);
		$schema_fields = ereg_replace("^ ", "", $schema_fields);
		$schema_insert = "INSERT INTO $cfgQuotes$table$cfgQuotes ($schema_fields) VALUES($schema_vals)";
		$handler(trim($schema_insert));
	}
	return (true);
}
 
function count_records ($link, $table) {
	global $strNoAuth, $cfgQuotes;
	$result = @pg_exec($link, "SELECT COUNT(*) AS num FROM $cfgQuotes$table$cfgQuotes");
	$num = @pg_result($result, 0 ,"num");
	if (!empty($num) || $num == 0) {
		echo $num;
	} else {
		echo $strNoAuth;
	}
}

// Get the content of $table as a CSV output.
// $sep contains the separation string.
// After every row, a custom callback function $handler gets called.
// $handler must accept one parameter ($sql_insert);

function get_table_csv($link, $table, $sep, $handler) {
	global $cfgQuotes;
	$result = pg_exec($link, "SELECT * FROM $cfgQuotes$table$cfgQuotes") or pg_die(pg_errormessage(), "", __FILE__, __LINE__);
	$i = 0;
	if (pg_numrows($result)) {
		while ($row = @pg_fetch_row($result, $i++)) {
			unset($schema_insert);
			for ($j = 0; $j < pg_numfields($result); $j++) {
				if (!isset($row[$j])) {
					$schema_insert .= "NULL".$sep;
				} elseif ($row[$j] != "") {
					$schema_insert .= "$row[$j]".$sep;
				} else {
					$schema_insert .= "".$sep;
				}
			}
			$schema_insert = ereg_replace($sep."$", "", $schema_insert);
			//       $schema_insert .= ")";
			$handler(trim($schema_insert));
		}
	} else {
		echo "$strNoData $strFound";
	}
	return (true);
}

function show_docu($link) {
	global $cfgManualBase, $strDocu;
	if (!empty($cfgManualBase)) {
		return("[<a href=\"$cfgManualBase?$link\" target=\"_new\">$strDocu</a>]");
	}
}

function show_message($message) {
	if (!empty($GLOBALS['reload']) && ($GLOBALS['reload'] == "true")) { ?>
	    <script language="JavaScript1.2">
	    parent.frames.nav.location.reload();
	    </script>
<?php } ?>

	<div align="left">
	<table border="<?php echo $GLOBALS['cfgBorder'];?>">
		<tr>
			<td bgcolor="<?php echo $GLOBALS['cfgThBgcolor'];?>">
				<b><?php echo $message; ?><b> 
				- <a href="db_details.php?<?php echo "server=", $GLOBALS['server'], "&db=", $GLOBALS['db'], "&sql_query=", urlencode($GLOBALS['sql_query']. " " . $GLOBALS['sql_order']); ?>"><?php echo $GLOBALS['strEdit']; ?></a><br/>
			</td>
		</tr>
<?php if ($GLOBALS['cfgShowSQL'] == true && !empty($GLOBALS['sql_query'])) { ?>
		<tr>
			<td bgcolor="<?php echo $GLOBALS['cfgBgcolorOne'];?>">
			<?php 
				if (isset($GLOBALS['affected_rows'])) {
					echo $GLOBALS['strAffected'].": " . $GLOBALS['affected_rows'] . "<br/>\n"; 
				}
			?>
			<?php echo $GLOBALS['strSQLQuery'].":\n<br/>", stripslashes(nl2br(htmlentities($GLOBALS['sql_query'] . " " . $GLOBALS['sql_order'])));?>
			</td>
		</tr>
<?php } ?>
	</table>
	</div>
<?php
}

//***************************************************************************************
//      Function:       isInArray($value, $chkArray)
//      Puspose:        Check to see if $value is in $chkArray
//      Params:         $value, $chkArray

function isInArray($value, $chkArray) {
	for ($iTrav = 0; $iTrav < count($chkArray); $iTrav++) {
		if ($value == $chkArray[$iTrav]) {
			return 1;
		}
	}
	return 0;
}


function bool_YesNo($boolVal) {
	global $strNo, $strYes;
	if (eregi("^t|1|^y", $boolVal)) {
		return $strYes;
	} elseif (eregi("^f|0|^n", $boolVal)) {
		return $strNo;
	}
}

function noQuoteSplit($string, $sep, $delim) {
	$curpos = 0;
	$in = false;
	unset($accum);
	$string = trim($string); 
	
	for ($i = 0, $n = strlen($string); $i < $n; $i++) {
		$char = $string[$i];
		$accum .= $char;
		if (($char == $delim)) {
			$prev = substr($string, $i - 3, 3);
			if ($prev != "\\\\\\") { // This is to account for the already escaped backslash
				$in = $in == true ? false : true;
			}
		} elseif ($char == $sep) {
			if (!$in) {
				$found[] = trim($accum);
				unset($accum);
			}
		}
	}
	if ($accum) {
		$found[] = $accum;
	}
	
	return $found;
}

// -----------------------------------------------------------------

// Array of standard postgres functions
$cfgFunctions = array(
	"CURVAL",
	"NEXTVAL",
	"TEXTCAT",
	"TEXTLEN",
	"COALESCE",
	"NULLIF",
	"ABS",
	"DEGREES",
	"EXP",
	"LN",
	"LOG",
	"PI",
	"POW",
	"RADIANS",
	"ROUND",
	"SQRT",
	"CBRT",
	"TRUNC",
	"FLOAT",
	"FLOAT4",
	"INTERGER",
	"ACOS",
	"ASIN",
	"ATAN",
	"ATAN2",
	"COS",
	"COT",
	"SIN",
	"TAN",
	"CHAR_LENGTH",
	"LOWER",
	"OCTET_LENGTH",
	"POSITION",
	"SUBSTRING",
	"TRIM",
	"UPPER",
	"CHAR",
	"INITCAP",
	"LPAD",
	"LTRIM",
	"TEXTPOS",
	"RPAD",
	"RTRIM",
	"SUBSTR",
	"TEXT",
	"TRANSLATE",
	"VARCHAR",
	"ABSTIME",
	"AGE",
	"DATE_PART",
	"DATE_TRUNC",
	"INTERVAL",
	"ISFINITE",
	"RELTIME",
	"TIMESTAMP",
	"TO_CHAR",
	"TO_DATE",
	"TO_TIMESTAMP",
	"TO_NUMBER",
	"AREA",
	"BOX",
	"CENTER",
	"DIAMETER",
	"HEIGHT",
	"ISCLOSED",
	"ISOPEN",
	"LENGTH",
	"PCLOSE",
	"NPOINT",
	"POPEN",
	"RADIUS",
	"WIDTH",
	"CIRCLE",
	"LSEG",
	"PATH",
	"POINT",
	"POLYGON",
	"ISOLDPATH",
	"REVERTPOLY",
	"UPGRADEPATH",
	"UPGRADEPOLY",
	"BROADCAST",
	"HOST",
	"MASKLEN",
	"NETMASK",
	"MD5 -- PHP"
	);

	define("LIB_INC", 1);
}
?>
