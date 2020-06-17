<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_et.php

//translator(s): Priit Ballot <ballot@neo.ee>, Tanel Põld www.brightside.ee
$help["setup_mkdirMethod"] = "Kui safe-mode on aktiveeritud, siis pead lisama FTP konto info, kuhu süsteem saaks katalooge ning faile lisada.";
$help["setup_notifications"] = "teated kasutajate e-postile (ülesande andmine, uus postitus, ülesanne muutub...)<br/>Vajalik on smtp/sendmail konfigureeerimine.";
$help["setup_forcedlogin"] = "Kui välja lülitatud, siis ära luba sisse logimist väljast ulnud URL-i parooli/kasutajanime kaudu.";
$help["setup_langdefault"] = "Vali logimisel vaikimisi pakutav keel või jäta tühjaks, et brauseri keel automaatselt ära tuntaks.";
$help["setup_myprefix"] = "Määra see valik, kui pareguses andmebaasis on ühe alloleva nimega tabel juba olemas.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Jäta tühjaks, kui ei soovi tabeli nime prefiksit kasutada.";
$help["setup_loginmethod"] = "Kuidas hoida paroole andmebaasis.<br/>Set to &quot;Crypt&quot; in order CVS authentication and htaccess authentification to work (if CVS support and/or htaccess authentification are enabled).";
$help["admin_update"] = "Versiooni uuendamiseks tee asju kindlasti õiges järjekorras: <br/>1. Muuda seadeid(lisa uued parameetrid)<br/>2. Muuda andmebaasi(uuenda varasema versiooniga kooskõlas)";
$help["task_scope_creep"] = "Erinevus planeeritud ja tegeliku valmimistähtaja vahel";
$help["max_file_size"] = "Suurim võimalik projektiga liidetava faili suurus.";
$help["project_disk_space"] = "Kõigile projektidele lisatud failide maht kokku";
$help["project_scope_creep"] = "Erinevus planeeritud ja tegeliku valmimistähtaja vahel. Summa kõikide ülesannete tähtaegade ületamisest.";
$help["mycompany_logo"] = "Sisesta oma firma logo, see ilmub tiitelribale.";
$help["calendar_shortname"] = "Lühike nimi, mida näidatakse kalendri kuu vaates. Kohustuslik";
$help["user_autologout"] = "Aeg sekundites, mille jooksul teid peale viimast tegevust automaatselt välja logitakse.";
$help["user_timezone"] = "Seadista GMT ajavõõnd";
//2.4
$help["setup_clientsfilter"] = "Filter - näita ainult sisse loginud kliendikasutajaid";
$help["setup_projectsfilter"] = "Filter - näita kasutajale projekte mille meeskonnas ta on";
//2.5
$help["setup_notificationMethod"] = "Seadista e-posti saatmise meetod: sisene <i>php mail function</i> (smtp või sendmail peab olema php konfiguratsioonid) või smtp server";
