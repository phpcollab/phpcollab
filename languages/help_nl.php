<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_nl.php

//translator(s): Erwin Wondergem
$help["setup_mkdirMethod"] = "Wanneer safe-mode geactiveerd is dient u een Ftp account te creëren om een map met bestandsmanagement aan te maken.";
$help["setup_notifications"] = "Email notificatie voor gebruikers (taak toewijzingen, nieuwe berichten, taakwijzigingen etc.)<br/>Een geldige SMTP/Sendmail toepassing is vereist.";
$help["setup_forcedlogin"] = "Wanneer niet geactiveerd (<i>false</i>) zullen passwords en loginnamen niet in de url getoond worden.";
$help["setup_langdefault"] = "Kies de taal die standaard wordt gekozen bij een nieuwe login, of laat deze leeg om de taal te auto-detecteren.";
$help["setup_myprefix"] = "Plaats hier een voorvoegsel wanneer u de database -tabellen in een bestaande database wilt plaatsen.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Plaats geen waarde wanneer u geen gebruik wilt maken van een voorvoegsel.";
$help["setup_loginmethod"] = "Methoden om de passwords op te slaan in de database.<br/>Kies voor &quot;Crypt&quot; om CVS én htaccess authenticatie te kunnen gebruiken (wanneer CVS support en/of htaccess authenticatie zijn geactiveerd).";
$help["admin_update"] = "Respect strictly the order indicated to update your version<br/>1. Edit settings (supplement the new parameters)<br/>2. Edit database (update in agreement with your preceding version)";
$help["task_scope_creep"] = "Verschil in dagen tussen de vervaldatum en de afrondingsdatum (vet wanneer positief)";
$help["max_file_size"] = "Maximale bestandsgrootte van een bestand bij het uploaden";
$help["project_disk_space"] = "Totale grootte aan bestanden voor een project";
$help["project_scope_creep"] = "Verschil in dagen tussen de vervaldatum en de afrondingsdatum (vet wanneer positief). Het totaal voor alle taken.";
$help["mycompany_logo"] = "Upload een logo van uw bedrijf. Wordt getoond in de header, in plaats van de titel van de site";
$help["calendar_shortname"] = "Verkorte tekst welke wordt getoond in de kalender (overzicht). Dit is een verplicht veld!";
$help["user_autologout"] = "Tijd in seconden, wanneer u uitgelogd wordt na geen activiteit. type 0 om te deactiveren";
$help["user_timezone"] = "Geef uw Tijdzone aan (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
