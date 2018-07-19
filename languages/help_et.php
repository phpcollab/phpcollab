<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_et.php

//translator(s): Priit Ballot <ballot@neo.ee>
$help["setup_mkdirMethod"] = "Kui safe-mode on aktiveeritud, siis pead lisama FTP konto info, kuhu s&uuml;steem saaks katalooge ning faile lisada.";
$help["setup_notifications"] = "teated kasutajate e-postile (&uuml;lesande andmine, uus postitus, &uuml;lesanne muutub...)<br/>Vajalik on smtp/sendmail konfigureeerimine.";
$help["setup_forcedlogin"] = "Kui v&auml;lja l&uuml;litatud, siis &auml;ra luba sisse logimist v&auml;ljast ulnud URL-i parooli/kasutajanime kaudu.";
$help["setup_langdefault"] = "Vali logimisel vaikimisi pakutav keel v&otilde;i j&auml;ta t&uuml;hjaks, et brauseri keel automaatselt &auml;ra tuntaks.";
$help["setup_myprefix"] = "M&auml;&auml;ra see valik, kui pareguses andmebaasis on &uuml;he alloleva nimega tabel juba olemas.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>J&auml;ta t&uuml;hjaks, kui ei soovi tabeli nime prefiksit kasutada.";
$help["setup_loginmethod"] = "Kuidas hoida paroole andmebaasis.<br/>Set to &quot;Crypt&quot; in order htaccess authentificationto work (if htaccess authentificationare enabled).";
$help["admin_update"] = "Versiooni uuendamiseks tee asju kindlasti &otilde;iges j&auml;rjekorras: <br/>1. Muuda seadeid(lisa uued parameetrid)<br/>2. Muuda andmebaasi(uuenda varasema versiooniga koosk&otilde;las)";
$help["task_scope_creep"] = "Erinevus planeeritud ja tegeliku valmimistähtaja vahel";
$help["max_file_size"] = "Suurim võimalik projektiga liidetava faili suurus.";
$help["project_disk_space"] = "Kõigi projektiga seotud failide maht kokku";
$help["project_scope_creep"] = "Erinevus planeeritud ja tegeliku valmimistähtaja vahel. Summa kõikide ülesannete tähtaegade ületamisest.";
$help["mycompany_logo"] = "Saada oma firma logo, see ilmub tiitelribale.";
$help["calendar_shortname"] = "Lühike nimi, mida näidatakse kalendri kuu vaates. Kohustuslik";
$help["user_autologout"] = "Aeg sekundites, mille jooksul teid peale viimast tegevust automaatselt välja logitakse.";
$help["user_timezone"] = "Set your GMT timezone";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
