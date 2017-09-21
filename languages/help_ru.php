<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_ru.php

//translator(s): Andreu 'Doc' Ponomarev
$help["setup_mkdirMethod"] = "Если в опциях PHP установлено safe-mode как On, необходимо поставить опцию \"создание папок\" как Ftp и настроить ftp установки.";
$help["setup_notifications"] = "Уведомления пользователей по e-mail (задачи и их изменения, новые сообщения...)<br/>Требуется правильная настройка smtp/sendmail.";
$help["setup_forcedlogin"] = "Если установлени как false, в URL не видно логином/пароля";
$help["setup_langdefault"] = "Выберите язык из списка при входе на сайт ил оставьте пустым при использовании автоопределения.";
$help["setup_myprefix"] = "Установите этот параметр (префикс) если у вас есть другие таблицы в базе данных имеющие следующие названия: <br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Оставьте пустым если не хотите использовать префикс.";
$help["setup_loginmethod"] = "Метод сохранения пароля в базе данных.<br/>Установите как &quot;Crypt&quot; в CVS и htaccess авторизации (есливключена поддержка CVS и/или htaccess авторизация требуется).";
$help["admin_update"] = "Строго следуйте следующим правилам при обновлении вашей версии <br/> 1. Редактируйте настройки (добавьте новые параметры) <br/> 2. Отредактируйте базу данных (модернизация вашей предшествующей версии)";
$help["task_scope_creep"] = "Различие в днях между должной датой и полной датой (в целых числлах).";
$help["max_file_size"] = "Максимальный размер файла для загрузки";
$help["project_disk_space"] = "Общий размер файлов проекта";
$help["project_scope_creep"] = "Различие в днях между должной датой и полной датой (в целых числлах). Общее для всех задач";
$help["mycompany_logo"] = "Добавьте логотип Вашей компании. Он будет расположен в заголовке страницы, вместо названия сайта";
$help["calendar_shortname"] = "Отметка появляющаяся в календаре. Принудительная";
$help["user_autologout"] = "Время в секундах до отключения, если пользователь неактивен. 0 для отключения опции";
$help["user_timezone"] = "Установить свой часовой пояс";
//2.4
$help["setup_clientsfilter"] = "Фильтр по активным члиентам";
$help["setup_projectsfilter"] = "Фильтр по проектам где пользователи в группе";
//2.5
$help["setup_notificationMethod"] = "Установите метод для рассылки уведомлений: через функцию php (необходимо иметь сервер smtp или sendmail сконфигурированный на сервере) или через внешний smtp сервер";
