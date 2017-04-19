<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_hu.php

//translator(s): 
$help["setup_mkdirMethod"] = "Ha a safe-mode be van kapcsolva, szükség van egy ftp hozzáférésre, hogy filekezelésre alkalmas könyvtárat lehessen létrehozni.";
$help["setup_notifications"] = "Felhasználói e-mail értesítések (feladat-hozzárendelések, új hozzászólás, új hozzászólás, feladat változás, ...)<br/>Valid smtp/sendmail szükséges.";
$help["setup_forcedlogin"] = "Ha hamis, akkor ne engedd külsõ linket felhasználónévvel/jelszóval az URL-ben";
$help["setup_langdefault"] = "Válaszd ki az alapértelmezett bejelentkezési nyelvet, vagy hagyd üresen, hogy a böngészõ detektálja automatikusan.";
$help["setup_myprefix"] = "Állítsd be ezt az értéket, ha már van azonos nevû táblád az adatbázisban.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Hagyd üresen tábla prefix nem-használata esetén.";
$help["setup_loginmethod"] = "Jelszavak tárolási metódusa az adatbázisban.<br/>Állíts &quot;Crypt&quot;-re a CVS és htaccess autentikáció mûködéséhez (ha a CVS support és/vagy htaccess autentikáció engedélyezett).";
$help["admin_update"] = "Szigorúan tartsd be a sorrendet a verziófrissítéskor<br/>1. Beállítások szerkesztése (egészítsd ki az új paramétereket)<br/>2. Adatbázis szerkesztése (frissítsd a korábbi verziódhoz igazodva)";
$help["task_scope_creep"] = "Különbség a határidõ és a teljesítés dátuma között (félkövér, ha pozitív)";
$help["max_file_size"] = "Maximum file méret feltöltéshez";
$help["project_disk_space"] = "A projekthez tartozó file-ok összmérete";
$help["project_scope_creep"] = "Különbség a határidõ és a teljesítés dátuma között (félkövér, ha pozitív). Az összes feladatra vonatkozóan.";
$help["mycompany_logo"] = "Tölts fel vállalatod tetszõleges logóját. Megjelenik a fejlécben. ";
$help["calendar_shortname"] = "A naptár havi nézetében megjelenõ felirat. Kötelezõ";
$help["user_autologout"] = "Az az idõintervallum másodpercekben, amely után aktivitás hiányában kijelentkezteti a felhasználót. Állíts be 0-át, ha ki akarod iktatni a funkciót.";
$help["user_timezone"] = "Állítsd be a GMT idõzónát";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
?>