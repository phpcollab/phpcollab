<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/db_var.inc.php

$db_mediumint_auto["mysql"] = "mediumint(8) unsigned NOT NULL auto_increment";
$db_mediumint_auto["sqlserver"] = "int IDENTITY (1, 1) NOT NULL";

$db_float["mysql"] = "float(10,2) NOT NULL default '0.00'";
$db_float["postgresql"] = "float NOT NULL default '0.00'";
$db_float["sqlserver"] = "DECIMAL(10,2) NULL default '0.00'";

$db_text["mysql"] = "text";
$db_text["postgresql"] = "text";
$db_text["sqlserver"] = "text NULL";

$db_int["mysql"] = "int(10) unsigned NOT NULL default '0'";
$db_int["postgresql"] = "int";
$db_int["sqlserver"] = "int NULL";

$db_mediumint["mysql"] = "mediumint(8) unsigned NOT NULL default '0'";
$db_mediumint["postgresql"] = "int4";
$db_mediumint["sqlserver"] = "int NULL";

$db_smallint["mysql"] = "smallint(5) unsigned NOT NULL default '0'";
$db_smallint["postgresql"] = "int2";
$db_smallint["sqlserver"] = "int NULL";

$db_char1["mysql"] = "char(1) NOT NULL default ''";
$db_char1["postgresql"] = "char(1)";
$db_char1["sqlserver"] = "char(1) NULL";

$db_char1default0["mysql"] = "char(1) NOT NULL default '0'";
$db_char1default0["postgresql"] = "char(1) NOT NULL default '0'";
$db_char1default0["sqlserver"] = "char(1) NOT NULL default '0'";

$db_char3["mysql"] = "char(3) NOT NULL default ''";
$db_char3["postgresql"] = "char(3)";
$db_char3["sqlserver"] = "char(3) NULL";

$db_varchar10["mysql"] = "varchar(10)";
$db_varchar10["postgresql"] = "varchar(10)";
$db_varchar10["sqlserver"] = "varchar(10) NULL";

$db_varchar16["mysql"] = "varchar(16)";
$db_varchar16["postgresql"] = "varchar(16)";
$db_varchar16["sqlserver"] = "varchar(16) NULL";

$db_varchar35["mysql"] = "varchar(16)";
$db_varchar35["postgresql"] = "varchar(16)";
$db_varchar35["sqlserver"] = "varchar(16) NULL";

$db_varchar155["mysql"] = "varchar(155)";
$db_varchar155["postgresql"] = "varchar(155)";
$db_varchar155["sqlserver"] = "varchar(155) NULL";

$db_varchar255["mysql"] = "varchar(255)";
$db_varchar255["postgresql"] = "varchar(255)";
$db_varchar255["sqlserver"] = "varchar(255) NULL";

$db_varchar255NN["mysql"] = "varchar(255) NOT NULL";
$db_varchar255NN["postgresql"] = "varchar(255) NOT NULL";
$db_varchar255NN["sqlserver"] = "varchar(255) NOT NULL";

$db_varchar255a["mysql"] = "varchar(255) NOT NULL default '0'";
$db_varchar255a["postgresql"] = "varchar(255)";
$db_varchar255a["sqlserver"] = "varchar(255) NULL";

$db_varchar255b["mysql"] = "varchar(255) NOT NULL default '0.0'";
$db_varchar255b["postgresql"] = "varchar(255)";
$db_varchar255b["sqlserver"] = "varchar(255) NULL";
