<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_ca.php

//translator(s): Sergi Nadal <sergi@espintime.net>
$help["setup_mkdirMethod"] = "Si teniu activat el mode-segur, cal que doneu d'alta un compte de Ftp per tal de poder crear subdirectoris amb el gestor de fitxers.";
$help["setup_notifications"] = "Notificacions a usuaris per correu-e (assignació de tasques, nous missatges, canvis de tasques...)<br/>Cal un smtp/sendmail vàlid.";
$help["setup_forcedlogin"] = "Si és fals, desactiva l'enllaç extern a la url amb login/clau";
$help["setup_langdefault"] = "Escolliu l'idioma que apareixerà per defecte a l'entrada o deixeu-lo en blanc si voleu que aquest sigui el que el navegador té assignat per defecte.";
$help["setup_myprefix"] = "Establiu aquest valor si teniu taules amb el mateix nom a la Base de Dades.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Deixeu-ho en blanc si no voleu usar prefixos per a les taules.";
$help["setup_loginmethod"] = "Mètode per guardar claus a la Base de Dades.<br/>Establiu-ho en &quot;Crypt&quot; per tal que funcioni l'autentificació CVS i la del htaccess (això sempre que estigui habilitada l'autentificació CVS i/o htaccess).";
$help["admin_update"] = "Respecteu estrictament l'ordre indicat per actualitzar la vostra versió<br/>1. Editar preferències (imposició dels nous paràmetres)<br/>2. Editar la Base de Dades (actualització d'acord amb la vostra versió anterior)";
$help["task_scope_creep"] = "Diferència en dies entre la data de finalització i la de lliurament (en negreta si és positiva)";
$help["max_file_size"] = "Tamany màxim que ha de tenir un fitxer per poder-lo pujar";
$help["project_disk_space"] = "Tamany total dels fitxers del projecte";
$help["project_scope_creep"] = "Diferència en dies entre la data de finalització i la de lliurament (en negreta si és positiva). Total per a totes les tasques";
$help["mycompany_logo"] = "Pugeu el logotip de l'empresa. Apareixerà a la capçalera, enlloc del títol del lloc";
$help["calendar_shortname"] = "Etiqueta que apareixerà a la vista del calendari mensual. És obligatòria";
$help["user_autologout"] = "Temps en segons per desconnectar-se en cas de no activitat. Poseu 0 per deshabilitar aquesta opció";
$help["user_timezone"] = "Establiu la vostra zona horària GMT";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Filter to see only the project when the user are in the team";
//2.5
$help["setup_notificationMethod"] = "Set method to send email notifications: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
?>