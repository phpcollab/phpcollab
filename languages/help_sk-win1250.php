<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_sk-win1250.php

//translator(s): 
$help["setup_mkdirMethod"] = "Pokia¾ je safe-mode On, musíte poui Ftp úèet pre monos vytvára adresáre pri práci so súbormi v PHP Collab.";
$help["setup_notifications"] = "E-mailové oznámenie uívate¾om (priradenie úlohy, novı príspevok, zmeny úlohy...)<br/>Je potrebné správne nastavi smtp/sendmail.";
$help["setup_forcedlogin"] = "Pokia¾ nie je aktívna, nie sú povolené externé odkazy s login/heslo v url";
$help["setup_langdefault"] = "Vyberte jazyk, ktorı bude predurèene vybranı pri kadom prihlasovaní alebo nechajte prázdne pre autodetekciu jazyka v prehliadaèi.";
$help["setup_myprefix"] = "Nastavte túto hodnotu, pokia¾ máte v databáze tabu¾ku so zhodnım názvom.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Nechajte prázdne, pokia¾ nechcete poui prefix tabuliek.";
$help["setup_loginmethod"] = "Spôsob uloenia hesla v databáze.<br/>Set to &quot;Crypt&quot; in order CVS authentication and htaccess authentification to work (if CVS support and/or htaccess authentification are enabled).";
$help["admin_update"] = "Rešpektuj striktne príkaz indikujúci aktualizáciu Vašej verzie<br/>1. Upravte nastavenia (doplòte nové parametre)<br/>2. Upravte databázu (aktualizácia v súlade s Vašou predchádzajúcou verziou)";
$help["task_scope_creep"] = "Rozdiel v dòoch medzi termínom dokonèenia a dátumom dokonèenia (tuène ak je kladnı)";
$help["max_file_size"] = "Maximálna ve¾kos súboru pre upload";
$help["project_disk_space"] = "Celková ve¾kos súborov pre projekt";
$help["project_scope_creep"] = "Rozdiel v dòoch medzi termínom dokonèenia a dátumom dokonèenia (tuène ak je kladnı). Celkom pre všetky úlohy.";
$help["mycompany_logo"] = "Nahraj logo Vašej spoloènosti. Objaví sa v záhlavı namiesto nadpisu.";
$help["calendar_shortname"] = "Popis pre zobrazenie v mesaènom kalendári. Povinné";
$help["user_autologout"] = "Èas v sekundách pre automatické odpojenie pri neèinnosti. 0 pre deaktiváciu";
$help["user_timezone"] = "Nastavte Vaše èasové pásmo (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
?>