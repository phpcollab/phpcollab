<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_bg.php

//translator(s): Yassen Yotov <cyberoto@abv.bg>, Veselin Malezanov <veselin@trimata.bg>
$help["setup_mkdirMethod"] = "Ако safe-mode е ВКЛЮЧЕН, Вие трябва да направите Ftp акаунт, за да можете да създадете папка с файлово управление.";
$help["setup_notifications"] = "Уведомяване с e-mail (добавяне на задача, нови съобщения, смяна на задачи...)<br/>Валиден smtp/sendmail е необходим.";
$help["setup_forcedlogin"] = "Ако е false, не разрешава външни връзки с login/password в url";
$help["setup_langdefault"] = "Избери език по подразбиране при вход на потребител или оставете blank за да използва auto_detect език на браузъра.";
$help["setup_myprefix"] = "Настройте тази стойност ако имате таблици със еднакво име в база данни.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Оставете празно, за да не използвате префикс.";
$help["setup_loginmethod"] = "Метод на запис на паролите и база данни.<br/>Set to &quot;Crypt&quot; in order CVS authentication and htaccess authentification to work (if CVS support and/or htaccess authentification are enabled).";
$help["admin_update"] = "Спазвайте стриктно реда индикиран за да обновите вашата версия<br/>1. Редактирай настройките (допълнителни нови параметри)<br/>2. Редактирай база данни (обнови в съответствие на предишната версия)";
$help["task_scope_creep"] = "Разликата в дни между дата за плащане и дата на завършване (удебелено ако е положително)";
$help["max_file_size"] = "Максимална големина на файл за изпращане";
$help["project_disk_space"] = "Обща големина на файловете за проекта";
$help["project_scope_creep"] = "Разликата в дни между датите за планирано завършване и реално завършване (удебелено ако е просрочен срока). Общо за всички задачи";
$help["mycompany_logo"] = "Изпратете лого на Вашата компания. Появява се в header, а не в заглавието на сайта";
$help["calendar_shortname"] = "Надпис, който да се появява в месечния календар. Задължителен";
$help["user_autologout"] = "Времето в секунди, за да бъдеш изключен, след определена неактивност. 0 (нула) за да изключиш тази опция";
$help["user_timezone"] = "Настройте вашата времева зона (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filter to see only logged user clients";
$help["setup_projectsfilter"] = "Потребители ще виждат само проектите по който са разпределени";
//2.5
$help["setup_notificationMethod"] = "Настрой метода за изпращане на емайл съобщения: with internal php mail function (need for having a smtp server or sendmail configured in the parameters of php) or with a personalized smtp server";
//2.5 fullo
$help["newsdesk_links"] = "За да добавиш повече линкове, използвай точка и запетая за разделител";
?>