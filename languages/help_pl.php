<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_pl.php

//translator(s): Marcin Kawalerowicz (mkawalerowicz@poczta.onet.pl)

$help["setup_mkdirMethod"] = "Je¶li serwer dzia³a w trybie safe-moed, musisz zdefiniowaæ konto ftp do zarz±dzania plikami w phpCollab.";
$help["setup_notifications"] = "Powiadamiania za pomoc± poczty elektronicznej (zmiana w zadaniach, nowa wiadomo¶æ, ...)<br/>Smtp/sendmail konieczny";
$help["setup_forcedlogin"] = "Je¶li False, nie zezwalaj na zdalne logowanie przez login i has³o zapisane w URL";
$help["setup_langdefault"] = "Wybierz jêzyk, który ma byæ domy¶lnie wybierany podczas logowania lub pozostaw je¶li chcesz. by aktualny jêzyk by³ rozpoznawany automatycznie.";
$help["setup_myprefix"] = "Ustaw t± zmienn± je¶li chcesz by nazwy tabel w bazie danych zosta³y poprzedzone prefiksem. <br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Pozostaw to pole puste je¶li nie chcesz u¿ywaæ prefiksu.";
$help["setup_loginmethod"] = "Metoda zapisu hase³ w bazie danych.<br/>Ustaw &quot;Crypt&quot; je¶lic chesz by dzia³a³a autentykacja w CVS i za pomoc± htaccess (w przypadku gdy w³±czone jest u¿ywanie CVS i/lub htaccess).";
$help["admin_update"] = "W przypadku aktualizowania wersji postêpuj dok³adnie w tej kolejno¶ci:<br/>1. Ustawienia (wprowad¼ nowe parametry)<br/>2. Ustawienia bazy danych (uaktualnij zgodnie z now± wersj±)";
$help["task_scope_creep"] = "Ró¿nica dni pomiêdzy planowana dat± zakoñczenia i rzeczywist± dat± zakoñczenia (pogrubione dodatnia).";
$help["max_file_size"] = "Maksymalna wielko¶æ pliku.";
$help["project_disk_space"] = "Sumaryczna maksymalna wielko¶æ foldera z plikami.";
$help["project_scope_creep"] = "Ró¿nica dni pomiêdzy planowana dat± zakoñczenia i rzeczywist± dat± zakoñczenia (pogrubione dodatnia). Dla wszystkich zadañ.";
$help["mycompany_logo"] = "Za³aduj logo swojej firmy. Logo poka¿e siê w lewym górnym rogu okna.";
$help["calendar_shortname"] = "Etykieta, która poka¿e siê w kalendarzu. Wymagane.";
$help["user_autologout"] = "Czas w sekundach do automatycznego wylogowania w przypadku braku aktywno¶ci, 0 = wy³±czone.";
$help["user_timezone"] = "Ustaw strefê  GMT.";
//2.4
$help["setup_clientsfilter"] = "Filtruj tylko zalogowanych u¿ytkowników klienta.";
$help["setup_projectsfilter"] = "Filtruj, by pokazywaæ jedynie projekty, do których u¿ytkownik zosta³ przypisany.";
//2.5
$help["setup_notificationMethod"] = "Ustal metodê dla wysy³ania poczty elektronicznej: za pomoc± funkcji mail jêzyka php (konieczny w³asny serwer smtp lub sendmail i skonfigurowany php) lub za pomoc± innego serwera smtp.";
?>