<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_cs-win1250.php

//translator(s):
$help["setup_mkdirMethod"] = "Pokud je safe-mode On, musíte použít Ftp úèet pro možnost vytváøet adresáøe pøi práci se soubory v PHP Collab.";
$help["setup_notifications"] = "E-mailové oznámení uživatelùm (pøiøazení úkolu, nový pøíspìvek, zmìny úkolu...)<br/>Je potøeba správnì nastavit smtp/sendmail.";
$help["setup_forcedlogin"] = "Pokud není aktivní, nejsou povoleny externí odkazy s login/heslo v url";
$help["setup_langdefault"] = "Vyberte jazyk, který bude defaultnì vybrán pøi pøihlašování nebo nechte prázdné pro autodetekci jazyka v prohlížeèi.";
$help["setup_myprefix"] = "Natavte tuto hodnotu, pokud máte v databázi tabulku se shodným názvem.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Nechte prázdné, pokud nechcete použít prefix tabulek.";
$help["setup_loginmethod"] = "Zpùsob uložení hesla v databázi.<br/>Set to &quot;Crypt&quot; in order htaccess authentificationto work (if htaccess authentificationare enabled).";
$help["admin_update"] = "Respect strictly the order indicated to update your version<br/>1. Upravte nastavení (doplòte nové parametry)<br/>2. Upravte databázi (aktualizace v souladu s vaší pøedchozí verzí)";
$help["task_scope_creep"] = "Rozdíl v dnech mezi termínem dokonèení a datem dokonèení (tuènì jestliže je kladný)";
$help["max_file_size"] = "Maximální velikost souboru pro upload";
$help["project_disk_space"] = "Celková velikost suoborù pro projekt";
$help["project_scope_creep"] = "Rozdíl v dnech mezi termínem dokonèení a datem dokonèení (tuènì jestliže je kladný). Celkem pro všechny úkoly.";
$help["mycompany_logo"] = "Nahrej logo vaší spoleènosti. Objeví se v záhlaví místo nadpisu.";
$help["calendar_shortname"] = "Popisek pro zobrazení v mìsíèním kalendáøi. povinné";
$help["user_autologout"] = "Èas v sekundách pro automatické odpojení pøi neèinnosti. 0 pro deaktivaci";
$help["user_timezone"] = "Nastavte Vaše èasové pásmo (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
