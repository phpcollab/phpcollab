<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_no.php

//translator(s): Wiggo Eriksen <Prosjektledelse.no>
$help["setup_mkdirMethod"] = "Om safe-mode er på=On, du må sette en Ftp konto for å kunne lage en mappe med file management.";
$help["setup_notifications"] = "Bruker e-mail melding (oppgave tildeling, nytt innlegg, oppgave endring...)<br>Validert smtp/sendmail behøves.";
$help["setup_forcedlogin"] = "Om false, ikke tillat med ekstern link med login/password i url";
$help["setup_langdefault"] = "Velg språk som vil bli valgt ved login som standard eller la den være blank for å bruke auto_detect browser språk.";
$help["setup_myprefix"] = "Definer denne verdien om du har tabeller med samme navn i eksisterende database.<br><br>oppgaver<br>bokmerke<br>bokmerke_kategorier<br>kalendar<br>filer<br>logs<br>medlemer<br>notat<br>notifikasjon<br>organisasjon<br>faser<br>posteringer<br>prosjekt<br>rapporter<br>sortering<br>suboppgave<br>suport_postering<br>suport_ønske<br>oppgaver<br>grupper<br>emner<br>updates<br><br>La være blank for ikke å bruke tabel prefix.";
$help["setup_loginmethod"] = "Metode for å lagre passord i database.<br>Sett til &quot;Crypt&quot; for at CVS autentikasjon og htaccess autentikasjon skal virke (om CVS support og/eller htaccess autentikasjon er slått på).";
$help["admin_update"] = "Følg bestemt den rekkefølge indikert for å oppdatere din version<br>1. Endre settings (legg inn nye parameter)<br>2. Endre database (oppdater i samhandel med din tidliger version)";
$help["task_scope_creep"] = "Differanse i dager mellom slutt dato og ferdig dato (fet tekst om positiv)";
$help["max_file_size"] = "Maksimal fil størrelse på fil som skal lastes opp";
$help["project_disk_space"] = "Total fil størrelse for prosjektet";
$help["project_scope_creep"] = "Differanse i dager mellom slutt dato og ferdig dato (fet tekst om positiv). Total for alle oppgaver";
$help["mycompany_logo"] = "Last opp vilken som helst logo for ditt selskap. Vil komme i header, istedenfor i tittel side";
$help["calendar_shortname"] = "Tekst som vil komme på månedlig kalender view. Påkrevd";
$help["user_autologout"] = "Tid i sek. før frakobling etter ingen aktivitet. 0 for å disable";
$help["user_timezone"] = "Sett din GMT tidssone";
//2.4
$help["setup_clientsfilter"] = "Filter for å bare å se loggede bruker klienter";
$help["setup_projectsfilter"] = "Filter for å bare se prosjektet når brukeren er i gruppen";
//2.5
$help["setup_notificationMethod"] = "Sett metode for å sende e-mail notifikasjon: med intern php mail funksjon (trenger en smtp server eller sendmail konfigurert i php parameterene) eller med en personlig smtp server";
//2.5 fullo
$help["newsdesk_links"] = "for å legge til flere linker bruk semikolon";
?>