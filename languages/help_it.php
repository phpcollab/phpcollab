<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_it.php

//translator(s): Francesco Fullone <fullone@csr.unibo.it>
$help["setup_mkdirMethod"] = "Se il safe-mode è abilitato (On), devi configurare un account Ftp per poter creare cartelle con la gestione dei file.";
$help["setup_notifications"] = "Notifica agli usenti tramite e-mail (assegnazione task, nuove discussioni, modifiche ai task...)<br/>E' necessario un server smtp/sendmail funzionante.";
$help["setup_forcedlogin"] = "Se falso, blocca i link esterno contententi login/password nell'url";
$help["setup_langdefault"] = "Sceglie il linguaggio che sarà automaticamente selezionato in fase di login se lasciato in bianco sarà attivato l'auto_detect del linguaggio del browser.";
$help["setup_myprefix"] = "Assegna questo valore se hai tabelle con lo stesso nome nel database.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Lascialo in bianco per non usare il prefisso sulle tabelle.";
$help["setup_loginmethod"] = "Metodo per memorizzare le passwords nel database.<br/>Seleziona &quot;Crypt&quot; per abilitare le autenticazioni su CVS e htaccess  (se il supporto CVS e/o l'autenticazione htaccess sono abilitati).";
$help["admin_update"] = "Rispetta l'ordine indicato per aggiornare la tua versione<br/>1. Modifica configurazione (aggiungi i nuovi parametri)<br/>2. Modifica il database (aggiorna in accordo con la tua precedente versione)";
$help["task_scope_creep"] = "Differenza in giorni tra la data di scadenza e quella di completamento (grassetto se positivo)";
$help["max_file_size"] = "Grandezza massima dei file da caricare";
$help["project_disk_space"] = "Grandezza totale dei files per il progetto";
$help["project_scope_creep"] = "Differenza in giorni tra la data di scadenza e quella di completamento (grassetto se positivo). Totale per tutti i tasks";
$help["mycompany_logo"] = "Carica un logo per la tua azienda. Apparirà nell'intestazione, sostituendo il titolo del sito";
$help["calendar_shortname"] = "Etichetta che apparirà nella visualizzazione mensile del calendario. Obbligatorio";
$help["user_autologout"] = "Tempo in sec. prima di essere disconnesso dopo nessuna attività. 0 per disabilitare";
$help["user_timezone"] = "Configura il tuo fuso orario (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filtra per vedere solo i clienti autenticati";
$help["setup_projectsfilter"] = "Filtra per vedere solo il progetto quando l'utente è nel gruppo di lavoro";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
//2.5 fullo
$help["newsdesk_links"] = "per aggiungere più links utilizza il punto e virgola";
