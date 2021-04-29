<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/lang_bg.php

//translator(s): Veselin Malezanov <veselin@trimata.bg>,Yassen Yotov <cyberoto@abv.bg>
$byteUnits = array('Байта', 'KB', 'MB', 'GB');

$dayNameArray = array(
    1 => "Понеделник",
    2 => "Вторник",
    3 => "Сряда",
    4 => "Четвъртък",
    5 => "Петък",
    6 => "Събота",
    7 => "Неделя"
);

$monthNameArray = array(
    1 => "Януари",
    "Февруари",
    "Март",
    "Април",
    "Май",
    "Юни",
    "Юли",
    "Август",
    "Септември",
    "Октомври",
    "Ноември",
    "Декември"
);

$status = array(0 => "Предаден", 1 => "Завършен", 2 => "Незапочнат", 3 => "Започнат", 4 => "Преустановен");

$profil = array(
    0 => "Администратор",
    1 => "Проджект Менаджер",
    2 => "Потребител",
    3 => "Клиент",
    4 => "Disabled",
    5 => "Project Manager Administrator"
);

$priority = array(0 => "Няма", 1 => "Много нисък", 2 => "Нисък", 3 => "Среден", 4 => "Висок", 5 => "Много висок");

$statusTopic = array(0 => "Закрит", 1 => "Открит");
$statusTopicBis = array(0 => "Да", 1 => "Не");

$statusPublish = array(0 => "Да", 1 => "Не");

$statusFile = array(
    0 => "Одобрен",
    1 => "Одобрен с корекции",
    2 => "Чака одобрение",
    3 => "Без одобрение",
    4 => "Отхвърлен"
);

$phaseStatus = array(0 => "Не е започнат", 1 => "Започнат", 2 => "Завършен", 3 => "Прекратен");

$requestStatus = array(0 => "Нов", 1 => "Започнат", 2 => "Завършен");

$strings["please_login"] = "Моля, влезте";
$strings["requirements"] = "Системни изисквания";
$strings["login"] = "Вход";
$strings["no_items"] = "Няма нищо въведено";
$strings["logout"] = "Изход";
$strings["preferences"] = "Настройки";
$strings["my_tasks"] = "Мойте задачи";
$strings["edit_task"] = "Редактирай Задачата";
$strings["copy_task"] = "Копирай Задача";
$strings["add_task"] = "Добави Задача";
$strings["delete_tasks"] = "Изтрий Задачите";
$strings["assignment_history"] = "Възлагателна история";
$strings["assigned_on"] = "Възложена на";
$strings["assigned_by"] = "Възложена от";
$strings["to"] = "До";
$strings["comment"] = "Коментар";
$strings["task_assigned"] = "Задачата е възложена на ";
$strings["task_unassigned"] = "Задачата е към невъзложени";
$strings["edit_multiple_tasks"] = "Редактирай няколко задачи";
$strings["tasks_selected"] = "избрани задачи. Изберете нови стойности за тези задачи, или изберете [без промяна]Change], за да оставите текущите стойности.";
$strings["assignment_comment"] = "Приложени Коментари";
$strings["no_change"] = "[Без Промяна]";
$strings["my_discussions"] = "Мойте Дискусии";
$strings["discussions"] = "Дискусии";
$strings["delete_discussions"] = "Изтрий Дискусиите";
$strings["delete_discussions_note"] = "Забележка: Дискусиите не могат да бъдат отворени след като са изтрити.";
$strings["topic"] = "Тема";
$strings["posts"] = "Реплика";
$strings["latest_post"] = "Последна Реплика";
$strings["my_reports"] = "Мойте Отчети";
$strings["reports"] = "Отчети";
$strings["create_report"] = "Създай Отчет";
$strings["report_intro"] = "Изберете параметрите на задачите за отчета и запишете запитването  nd save the query от страницата с резултата след като създадете своя отчет.";
$strings["admin_intro"] = "Настройки на проекта.";
$strings["copy_of"] = "Копие на ";
$strings["add"] = "Добави";
$strings["delete"] = "Изтрий";
$strings["remove"] = "Премахни";
$strings["copy"] = "Копирай";
$strings["view"] = "Виж";
$strings["edit"] = "редактирай";
$strings["update"] = "Редактиране";
$strings["details"] = "Подробности";
$strings["none"] = "Няма";
$strings["close"] = "Затвори";
$strings["new"] = "Ново";
$strings["select_all"] = "Избери всички";
$strings["unassigned"] = "Неопределени";
$strings["administrator"] = "Администратор";
$strings["my_projects"] = "Мойте Проекти";
$strings["project"] = "Проект";
$strings["active"] = "Активен";
$strings["inactive"] = "Неактивен";
$strings["project_id"] = "Номер на проекта";
$strings["edit_project"] = "Редактирай проекта";
$strings["copy_project"] = "Копирай проекта";
$strings["add_project"] = "Добави проект";
$strings["clients"] = "Клиенти";
$strings["organization"] = "Фирма-клиент";
$strings["client_projects"] = "Клиентски проекти";
$strings["client_users"] = "Клиент";
$strings["edit_organization"] = "Редактирай фирма-клиент";
$strings["add_organization"] = "Добави фирма-клиент";
$strings["organizations"] = "Фирми-Клиенти";
$strings["info"] = "Информация";
$strings["status"] = "Състояние";
$strings["owner"] = "Собственик";
$strings["home"] = "Начало";
$strings["projects"] = "Проекти";
$strings["files"] = "Файлове";
$strings["search"] = "Търсене";
$strings["admin"] = "Административни";
$strings["user"] = "Потребител";
$strings["project_manager"] = "Ръководител проект";
$strings["due"] = "Платено";
$strings["task"] = "Задача";
$strings["tasks"] = "Задачи";
$strings["team"] = "Работна група";
$strings["add_team"] = "Добави Член в Работната група";
$strings["team_members"] = "Членове на Работната група";
$strings["full_name"] = "Пълно име";
$strings["title"] = "Титла";
$strings["user_name"] = "Потребителско Име";
$strings["work_phone"] = "Телефон - работа";
$strings["priority"] = "Приоритет";
$strings["name"] = "Име";
$strings["id"] = "Номер";
$strings["description"] = "Описание";
$strings["phone"] = "Телефон";
$strings["address"] = "Адрес";
$strings["comments"] = "Коментари";
$strings["created"] = "Създаден";
$strings["assigned"] = "Възложен";
$strings["modified"] = "Променен";
$strings["assigned_to"] = "Възложен на";
$strings["due_date"] = "Планирано завършване";
$strings["estimated_time"] = "Определено време";
$strings["actual_time"] = "Реално Време";
$strings["delete_following"] = "Изтрий следващите?";
$strings["cancel"] = "Отказ";
$strings["and"] = "и";
$strings["administration"] = "Администрация";
$strings["user_management"] = "Управление на потребители";
$strings["system_information"] = "Информация за системата";
$strings["product_information"] = "Инфомация за продукта";
$strings["system_properties"] = "Настройки на системата";
$strings["create"] = "Създай";
$strings["report_save"] = "Запази тази настройки за отчет, за да можете да ги използвате пак.";
$strings["report_name"] = "Отчет име";
$strings["save"] = "Запиши";
$strings["matches"] = "Съвпадащи";
$strings["match"] = "Съпадащ";
$strings["report_results"] = "Резултат от отчета";
$strings["success"] = "Успешен";
$strings["addition_succeeded"] = "Добавянето е успешно";
$strings["deletion_succeeded"] = "Изтриването е успешно";
$strings["report_created"] = "Създай Отчет";
$strings["deleted_reports"] = "Изтрити Отчети";
$strings["modification_succeeded"] = "Успешна промяна";
$strings["errors"] = "Открити грешки!";
$strings["blank_user"] = "Този потребител не може да бъде намерен.";
$strings["blank_organization"] = "Тази организация не може да бъде намерена.";
$strings["blank_project"] = "Този проект не може да бъде намерен.";
$strings["user_profile"] = "Потребителско досие";
$strings["change_password"] = "Смени паролата";
$strings["change_password_user"] = "Смяна на потребителска парола.";
$strings["old_password_error"] = "Старата парола, която въведохте, е некоректна . Моля, въведете пак старата парола.";
$strings["new_password_error"] = "Новата парола е въведена 2 пъти различно. Моля, въведете пак новата парола.";
$strings["notifications"] = "Уведомявания";
$strings["change_password_intro"] = "Въведете старата си парола, след това въведете и повторете новата парола.";
$strings["old_password"] = "Стара парола";
$strings["password"] = "Парола";
$strings["new_password"] = "Нова парола";
$strings["confirm_password"] = "Повтори паролата";
$strings["home_phone"] = "Домашен телефон";
$strings["mobile_phone"] = "мобилен телефон";
$strings["fax"] = "Факс";
$strings["permissions"] = "Права";
$strings["administrator_permissions"] = "Администратор";
$strings["project_manager_permissions"] = "Ръководител Проекти";
$strings["user_permissions"] = "Потребителски права";
$strings["account_created"] = "Потребителя е създаден";
$strings["edit_user"] = "Редактирай Потребител";
$strings["edit_user_details"] = "Редактирай детайлите за потребителя.";
$strings["change_user_password"] = "Смени паролата на потребителя";
$strings["select_permissions"] = "Избери права за този потребител";
$strings["add_user"] = "Добави Потребител";
$strings["enter_user_details"] = "Въведи детайли за акаунта, който създадохте.";
$strings["enter_password"] = "Въведи парола за потребителя.";
$strings["success_logout"] = "Вие успешно излязохте. Вие можете да влезете пак като попълните потребителско име и парола тук.";
$strings["invalid_login"] = "Потребителското име и/или парола, които въведохте, са невалидни. Моля, въведете ги пак.";
$strings["profile"] = "Профил";
$strings["user_details"] = "Потребителски детайли.";
$strings["edit_user_account"] = "Редактирайте информацията за своя акаунт.";
$strings["no_permissions"] = "Нямате достатъчно права за да изпълните това действие.";
$strings["discussion"] = "Дискусия";
$strings["retired"] = "Напуснал";
$strings["last_post"] = "Последна реплика";
$strings["post_reply"] = "Изпратете отговор";
$strings["posted_by"] = "Изпратено от";
$strings["when"] = "Кога";
$strings["post_to_discussion"] = "Изпрати в Дискусии";
$strings["message"] = "Съобщение";
$strings["delete_reports"] = "Изтрий Отчетите";
$strings["delete_projects"] = "Изтрий Проектите";
$strings["delete_organizations"] = "Изтрий Организациите";
$strings["delete_organizations_note"] = "забележка: Това действие ще изтрие всички клиенти от тази организация и ще раздели всичко отворени проекти от тези клиенти.";
$strings["delete_messages"] = "Изтрий съобщенията";
$strings["attention"] = "Внимание";
$strings["delete_teamownermix"] = "Изтриването е успешно, но собственика на проекта не може да бъде изхвърлен от работната група.";
$strings["delete_teamowner"] = "Не можете да изхвърлите собственика на проекта от работната група.";
$strings["enter_keywords"] = "Въведи ключови думи";
$strings["search_options"] = "ключови думи и опции за търсене";
$strings["search_note"] = "Трябва да въведете фраза в полето 'Търси за'.";
$strings["search_results"] = "Резултати от търсенето";
$strings["users"] = "Потребители";
$strings["search_for"] = "Търси за";
$strings["results_for_keywords"] = "Резултати от търсенето за ";
$strings["add_discussion"] = "Добави Дискусия";
$strings["delete_users"] = "Изтрий Потребителските Акаунти";
$strings["reassignment_user"] = "Прехвърляне на проект и задача";
$strings["there"] = "Те са";
$strings["owned_by"] = "собственост на потребители отгоре.";
$strings["reassign_to"] = "Преди да изтриете потребители, прехвърлете тези на ";
$strings["no_files"] = "Няма свързани файлове";
$strings["published"] = "Публикувани";
$strings["project_site"] = "Сайт на проекта";
$strings["approval_tracking"] = "Текущо състояние";
$strings["size"] = "Размер";
$strings["add_project_site"] = "Добави в сайта на проекта";
$strings["remove_project_site"] = "Изтрий от сайта на проекта";
$strings["more_search"] = "Повече опции за търсене";
$strings["results_with"] = "Търси в резултата";
$strings["search_topics"] = "Теми за търсене";
$strings["search_properties"] = "Настройки за търсене";
$strings["date_restrictions"] = "Ограничение по дати";
$strings["case_sensitive"] = "Без значение малки/големи";
$strings["yes"] = "Да";
$strings["no"] = "Не";
$strings["sort_by"] = "Подреди по";
$strings["type"] = "Тип";
$strings["date"] = "Дата";
$strings["all_words"] = "Всички думи";
$strings["any_words"] = "Някоя от думите";
$strings["exact_match"] = "Точно съвпадение";
$strings["all_dates"] = "Всички дати";
$strings["between_dates"] = "Между датите";
$strings["all_content"] = "Цялото съдържание";
$strings["all_properties"] = "Всички настройки";
$strings["no_results_search"] = "Търсенето завърши без намерен резултат.";
$strings["no_results_report"] = "Правенето на отчет завърши без намерен резултат.";
$strings["hours"] = "часове";
$strings["choice"] = "избор";
$strings["missing_file"] = "Липсващ файл !";
$strings["project_site_deleted"] = "Сайта на проекта е успешно изтрит.";
$strings["add_user_project_site"] = "На потребителя са дадени успешно права за достъп до сайта на проекта.";
$strings["remove_user_project_site"] = "Потребител permission was successfully removed.";
$strings["add_project_site_success"] = "Добавянето към сайта на проекта е успешно.";
$strings["remove_project_site_success"] = "Изтриването от сайта на проекта е успешно.";
$strings["add_file_success"] = "Прикачването на файла е успешно.";
$strings["delete_file_success"] = "Изтриването е успешно.";
$strings["update_comment_file"] = "Коментарите към файла са обновени успешно.";
$strings["session_false"] = "Грешка, свързана със сигурността";
$strings["logs"] = "История";
$strings["logout_time"] = "Автоматичен изход";
$strings["noti_foot1"] = "Това уведомление е генерирано от PhpCollab.";
$strings["noti_foot2"] = "За да видите своята PhpCollab страница, посетете:";
$strings["noti_taskassignment1"] = "Нова задача:";
$strings["noti_taskassignment2"] = "На Вас е възложена задача:";
$strings["noti_moreinfo"] = "За повече информация, посетете:";
$strings["noti_prioritytaskchange1"] = "Приоритета на задачата е променен";
$strings["noti_prioritytaskchange2"] = "Приоритета на тази задача е променен:";
$strings["noti_statustaskchange1"] = "Състоянието на задачата е променено:";
$strings["noti_statustaskchange2"] = "Състоянието на тази задача е променено:";
$strings["login_username"] = "Трябва да въведете потребителско име.";
$strings["login_password"] = "Моля въведете парола.";
$strings["login_clientuser"] = "Това е акаунт на клиент. Не можете да влезете в PhpCollab с клиентски акаунт.";
$strings["user_already_exists"] = "Вече има потребител с това име.Моля, въведете друго потребителско име.";
$strings["noti_duedatetaskchange1"] = "Крайната дата е променена:";
$strings["noti_duedatetaskchange2"] = "Крайната дата на следната задача е променена:";
$strings["company"] = "компания";
$strings["show_all"] = "Покажи всички";
$strings["information"] = "Информация";
$strings["delete_message"] = "Изтрий това съобщение";
$strings["project_team"] = "Работна група по проекта";
$strings["document_list"] = "Списък с документи";
$strings["bulletin_board"] = "Дъска с обяви";
$strings["bulletin_board_topic"] = "Тема от дъската с обяви";
$strings["create_topic"] = "Въведи нова тема";
$strings["topic_form"] = "Тема";
$strings["enter_message"] = "Въведи своето съобщение";
$strings["upload_file"] = "Прикачи файл";
$strings["upload_form"] = "Прикачване на файл";
$strings["upload"] = "Прикачи";
$strings["document"] = "Документ";
$strings["approval_comments"] = "Коментари по одобрението";
$strings["client_tasks"] = "Задачи за клиента";
$strings["team_tasks"] = "Задачи за работната група";
$strings["team_member_details"] = "Подробности за членовете на работната група";
$strings["client_task_details"] = "Подробности за задачите на клиента";
$strings["team_task_details"] = "Подробности за задачите на работната група";
$strings["language"] = "Език";
$strings["welcome"] = "Добре дошли";
$strings["your_projectsite"] = "във вашия сайт на проекта";
$strings["contact_projectsite"] = "Ако имате някакви въпроси за информацията намерана тук , моля, свържете се с ръководителя на проекта";
$strings["company_details"] = "Компания - подробности";
$strings["database"] = "Архивиране и възстановяване на данните";
$strings["company_info"] = "Редактирай информация за компанията";
$strings["create_projectsite"] = "Създай сайта на проекта";
$strings["projectsite_url"] = "Сайта на проекта URL";
$strings["design_template"] = "Темплейти";
$strings["preview_design_template"] = "Преглед на темплейтите";
$strings["delete_projectsite"] = "Изтрий сайта на проекта";
$strings["add_file"] = "Добави файл";
$strings["linked_content"] = "Прикачено съдържание";
$strings["edit_file"] = "Редактирай файла";
$strings["permitted_client"] = "Разрешен достъп за клиенти";
$strings["grant_client"] = "Даване на права за преглед на сайта на проекта";
$strings["add_client_user"] = "Добави Клиент-Потребител";
$strings["edit_client_user"] = "Редактирай Клиент-Потребител";
$strings["client_user"] = "Клиент-Потребител";
$strings["client_change_status"] = "Сменете състоянието на задачата след като я завършите.";
$strings["project_status"] = "Състояние на проекта";
$strings["view_projectsite"] = "Вижте Сайта на проекта";
$strings["enter_login"] = "Въведете своето потребителско име, за да ви изпратим нова парола";
$strings["send"] = "Изпрати";
$strings["no_login"] = "Потребитеското име не е открито в базата";
$strings["email_pwd"] = "паролата е изпратена";
$strings["no_email"] = "Потребителят няма email";
$strings["forgot_pwd"] = "Забравена парола ?";
$strings["project_owner"] = "Можете да правите промени само по свойте проекти.";
$strings["connected"] = "Свързан";
$strings["session"] = "Сесия";
$strings["last_visit"] = "Последно посещение";
$strings["compteur"] = "Брой";
$strings["ip"] = "IP";
$strings["task_owner"] = "Вие не сте влен на работната група на този проект";
$strings["reassignment_clientuser"] = "Преразпредели задачата";
$strings["organization_already_exists"] = "това име вече се използва. Моля въведете друго.";
$strings["blank_organization_field"] = "Трябва да въведете име на организацията.";
$strings["blank_fields"] = "задължителни полета";
$strings["projectsite_login_fails"] = "Не е възможно да се потвърдят името и паролата.";
$strings["start_date"] = "Започнато на";
$strings["completion"] = "Изпълнен";
$strings["update_available"] = "Има нова версия!";
$strings["version_current"] = "В момента използвате версия";
$strings["version_latest"] = "Последната версия е ";
$strings["sourceforge_link"] = "Вижте сайта на Phpcolab в Sourceforge";
$strings["demo_mode"] = "Демо версия. Това действие не е разрешено.";
$strings["setup_erase"] = "Изтрийте файла setup.php!!";
$strings["no_file"] = "Няма избрани файлове";
$strings["exceed_size"] = "Превишен максимален размер на файл";
$strings["no_php"] = "PHP файлове не са разрешени";
$strings["approval_date"] = "Дата на одобрение";
$strings["approver"] = "Одобрител";
$strings["error_database"] = "Не може да се осъществи връзка с базата";
$strings["error_server"] = "Не може да се осъществи връзка със сървъра";
$strings["version_control"] = "контрол на версиите";
$strings["vc_status"] = "Състояние";
$strings["vc_last_in"] = "Дата на последна промяна";
$strings["ifa_comments"] = "Коментари по одобрението";
$strings["ifa_command"] = "Смяна на състояние - одобрение";
$strings["vc_version"] = "Версия";
$strings["ifc_revisions"] = "Преглед";
$strings["ifc_revision_of"] = "Преглед на версиите";
$strings["ifc_add_revision"] = "Добави версия";
$strings["ifc_update_file"] = "Прикачи файл";
$strings["ifc_last_date"] = "Дата на последна промяна";
$strings["ifc_version_history"] = "История на версиите";
$strings["ifc_delete_file"] = "Изтрий файла и всички версии";
$strings["ifc_delete_version"] = "Изтрий избраните версии";
$strings["ifc_delete_review"] = "Изтрий избраните прегледи";
$strings["ifc_no_revisions"] = "Няма прегледи за този документ";
$strings["unlink_files"] = "Откачи файловете";
$strings["remove_team"] = "Отдели член на работната група";
$strings["remove_team_info"] = "Отдели тези членове от работната група?";
$strings["remove_team_client"] = "Махни правата за виждане на сайта на проекта";
$strings["note"] = "Бележка";
$strings["notes"] = "Бележки";
$strings["subject"] = "Тема";
$strings["delete_note"] = "Изтрий бележките";
$strings["add_note"] = "Добави бележка";
$strings["edit_note"] = "Редактирай бележка";
$strings["version_increm"] = "Избери версия за нанасяне на изменения:";
$strings["url_dev"] = "Работен сайт url";
$strings["url_prod"] = "Краен сайт url";
$strings["note_owner"] = "Можете да правите промени само на свойте бележки.";
$strings["alpha_only"] = "Допускат се само букви и цифри в потребителското име";
$strings["edit_notifications"] = "Редактирай E-mail уведомлението";
$strings["edit_notifications_info"] = "Изберете събития за които искате да получавате уведомление по E-mail.";
$strings["select_deselect"] = "Избери/Откажи всички";


$strings["noti_addprojectteam1"] = "Добавен към работната група:";
$strings["noti_addprojectteam2"] = "Все сте добавен към работна група :";
$strings["noti_removeprojectteam1"] = "Отделен от работна група :";
$strings["noti_removeprojectteam2"] = "Вие сте отделен от работна група :";
$strings["noti_newpost1"] = "Нова реплика :";
$strings["noti_newpost2"] = "Нова реплика е добавена към следната дискусия:";
$strings["edit_noti_taskassignment"] = "Потвърждавам новата задача";
$strings["edit_noti_statustaskchange"] = "Състоянието на една от мойте задачи е променено.";
$strings["edit_noti_prioritytaskchange"] = "Приоритета на една от мойте задачи е променен.";
$strings["edit_noti_duedatetaskchange"] = "Крайната дата на една от мойте задачи е променен.";
$strings["edit_noti_addprojectteam"] = "Добавен съм към работна група.";
$strings["edit_noti_removeprojectteam"] = "Отделен съм от работна група.";
$strings["edit_noti_newpost"] = "Направена е нова реплика в дискусията.";
$strings["add_optional"] = "Добави опция";
$strings["assignment_comment_info"] = "Добави коментар отностно тази задача";
$strings["my_notes"] = "Мойте бележки";
$strings["edit_settings"] = "Редактирай настройките";
$strings["max_upload"] = "Макс. размер на файл";
$strings["project_folder_size"] = "Макс. размер на Папка за сайтовете на Проекти";
$strings["calendar"] = "Календар";
$strings["date_start"] = "Започнато на";
$strings["date_end"] = "Завършено на";
$strings["time_start"] = "Начало";
$strings["time_end"] = "Край";
$strings["calendar_reminder"] = "Подсещане";
$strings["shortname"] = "Кратко име";
$strings["calendar_recurring"] = "Събитието се повтаря всяка седмица на този ден.";
$strings["edit_database"] = "Редактирай името на базата";
$strings["noti_newtopic1"] = "Нова дискусия :";
$strings["noti_newtopic2"] = "Нова дискусия е добавена към следния проект:";
$strings["edit_noti_newtopic"] = "Създадена нова тема в дискусията.";
$strings["today"] = "Днес";
$strings["previous"] = "Предишен";
$strings["next"] = "Следващ";
$strings["help"] = "Помощ";
$strings["complete_date"] = "Завършено на";
$strings["scope_creep"] = "Разлика м/у сроковете";
$strings["days"] = "Дни";
$strings["logo"] = "Лого";
$strings["remember_password"] = "Запомни паролата";

$strings["client_add_task_note"] = "Бележка: Въведената задача е регистрирана в база данните, за да се вижда тук е необходимо да се назначи на член на екип!";
$strings["noti_clientaddtask1"] = "Задачата е добавена от клиент :";
$strings["noti_clientaddtask2"] = "Клиент е добавил нова задача от проектния сайт към следния проект :";
$strings["phase"] = "Фаза";
$strings["phases"] = "Фази";
$strings["phase_id"] = "Фаза ID";
$strings["current_phase"] = "Активни фази";
$strings["total_tasks"] = "Общо Задачи";
$strings["uncomplete_tasks"] = "Недовършени Задачи";
$strings["no_current_phase"] = "Не е активна фаза в момента";
$strings["enable_phases"] = "Активна Фаза";
$strings["phase_enabled"] = "Фазите са активирани";
$strings["order"] = "Ред";
$strings["options"] = "Опции";
$strings["support"] = "Поддръжка";
$strings["support_request"] = "Заявка за Поддръжка";
$strings["support_requests"] = "Заявки за Поддръжки";
$strings["support_id"] = "Заявка ID";
$strings["my_support_request"] = "Моите Заявки за Поддръжка";
$strings["introduction"] = "Въведение";
$strings["submit"] = "Изпрати";
$strings["support_management"] = "Управление на Поддръжките";
$strings["date_open"] = "Дата Започнат";
$strings["date_close"] = "Дата Приключен";
$strings["add_support_request"] = "Добави Заявка за Поддръжка";
$strings["add_support_response"] = "Добави Отговор на Поддръжка";
$strings["respond"] = "Отговор";
$strings["delete_support_request"] = "Заявка за Поддръжка е изтрита";
$strings["delete_request"] = "Изтрий Зявказа за Поддръжка";
$strings["delete_support_post"] = "Изтрий съобщение за поддръжка";
$strings["new_requests"] = "Нови заявки";
$strings["open_requests"] = "Приети заявки";
$strings["closed_requests"] = "Завършени заявки";
$strings["manage_new_requests"] = "Управление на нови заявки";
$strings["manage_open_requests"] = "Управление на приети заявки";
$strings["manage_closed_requests"] = "Управление на завършени заявки";
$strings["responses"] = "Отговори";
$strings["edit_status"] = "Редактирай статус";
$strings["noti_support_request_new2"] = "Вие изпратихте заявка за поддръжка относно: ";
$strings["noti_support_post2"] = "Нов отговор е добавен на Вашата заявка за поддръжка. Моля, вижте детайлите по-долу.";
$strings["noti_support_status2"] = "Вашата заявка за поддръжка е обновена. Моля, вижте детайлите по-долу.";
$strings["noti_support_team_new2"] = "Нова заявка за поддръжка е добавена към проект: ";
//2.0
$strings["delete_subtasks"] = "Изтрий подзадачи";
$strings["add_subtask"] = "Добави подзадача";
$strings["edit_subtask"] = "Редактирай подзадача";
$strings["subtask"] = "Подзадача";
$strings["subtasks"] = "Подзадачи";
$strings["show_details"] = "Покажи детайли";
$strings["updates_task"] = "История на обновяване на задачи";
$strings["updates_subtask"] = "История на обновяване на подзадачи";
//2.1
$strings["go_projects_site"] = "Към сайта на проекта";
$strings["bookmark"] = "Полезна връзка";
$strings["bookmarks"] = "Полезни връзки";
$strings["bookmark_category"] = "Категория";
$strings["bookmark_category_new"] = "Нова категория";
$strings["bookmarks_all"] = "Всички";
$strings["bookmarks_my"] = "Мойте полезни връзки";
$strings["my"] = "Лични";
$strings["bookmarks_private"] = "Лични връзки";
$strings["shared"] = "Предоставени";
$strings["private"] = "Лични";
$strings["add_bookmark"] = "Добави връзка";
$strings["edit_bookmark"] = "редактирай връзка";
$strings["delete_bookmarks"] = "Изтрий връзка";
$strings["team_subtask_details"] = "Подробности за групова подзадача";
$strings["client_subtask_details"] = "Подробности за клиентска подзадача";
$strings["client_change_status_subtask"] = "Сменете своя статус отдолу, когато завършите тази задача";
$strings["disabled_permissions"] = "Заключен акаунт";
$strings["user_timezone"] = "Часова зона (GMT)";
//2.2
$strings["project_manager_administrator_permissions"] = "Ръководител Проек - админ";
//2.3
$strings["report"] = "Доклад";
$strings["license"] = "Лиценз";
//2.4
$strings["settings_notwritable"] = "Settings.php е заключен за промяна";
//2.5
$strings["invoicing"] = "Фактуриране";
$strings["invoice"] = "Фактура";
$strings["invoices"] = "Фактури";
$strings["date_invoice"] = "Дата";
$strings["header_note"] = "Хедър";
$strings["footer_note"] = "Футер";
$strings["total_ex_tax"] = "Общо (без ДДС)";
$strings["total_inc_tax"] = "Общо (с ДДС)";
$strings["tax_rate"] = "ДДС(%)";
$strings["tax_amount"] = "ДДС";
$strings["invoice_items"] = "Списък Услуги";
$strings["amount_ex_tax"] = "Сума";
$strings["completed"] = "Завършено";
$strings["service"] = "Услуга";
$strings["name_print"] = "Име (print)";
$strings["edit_invoice"] = "Редактирай фактура";
$strings["edit_invoiceitem"] = "Редактирай артикул";
$strings["calculation"] = "Изчисли";
$strings["items"] = "Артикули";
$strings["position"] = "Позиция";
$strings["service_management"] = "Управление на услуги";
$strings["hourly_rate"] = "Часова ставка";
$strings["add_service"] = "Добави услуга";
$strings["edit_service"] = "Редактирай услуга";
$strings["delete_services"] = "Изтрий услуга";
$strings["worked_hours"] = "Изработени часове";
$strings["rate_type"] = "Тип тарифа";
$strings["rate_value"] = "Стойност на тарифа";
$strings["note_invoice_items_notcompleted"] = "Не всички точки са приключени";

$rateType = array(
    0 => "Нормална тарифа",
    1 => "Тарифа по проект",
    2 => "Тарифа по организация",
    3 => "Тарифа по услуга"
);

//HACKS

$strings["newsdesk"] = "Новини";
$strings["newsdesk_list"] = "Списък";
$strings["article_newsdesk"] = "Новина";
$strings["update_newsdesk"] = "Редактирай";
$strings["my_newsdesk"] = "Мойте новини";
$strings["edit_newsdesk"] = "Редактирай новина";
$strings["copy_newsdesk"] = "Копирай Новина";
$strings["add_newsdesk"] = "Добави новина";
$strings["del_newsdesk"] = "Изтрий новината";
$strings["delete_news_note"] = "Забележка: Това ще изтрие всички коментари по избраните статии";
$strings["author"] = "Автор";
$strings["blank_newsdesk_title"] = "Празно заглавие";
$strings["blank_newsdesk"] = "Новината не може да бъде намерена.";
$strings["blank_newsdesk_comment"] = "Празен коментар";
$strings["remove_newsdesk"] = "Новината беше успешно изтрита заедно с всички коментари";
$strings["add_newsdesk_comment"] = "Добави коментар по новината";
$strings["edit_newsdesk_comment"] = "Редактирай коментар по новината";
$strings["del_newsdesk_comment"] = "Изтрий коментар(ите) по новината";
$strings["remove_newsdesk_comment"] = "Коментарите по новината бяха успешно изтрити";
$strings["errorpermission_newsdesk"] = "Нямате право да редактирате новините";
$strings["errorpermission_newsdesk_comment"] = "Нямате право да редактирате коментари";
$strings["newsdesk_related"] = "Свързани проекти";
$strings["newsdesk_related_generic"] = "Обща новина (няма свързани проекти)";
$strings["newsdesk_related_links"] = "Свързани линкове към новината";
$strings["newsdesk_rss"] = "Разреши RSS тази новина";
$strings["newsdesk_rss_enabled"] = "RSS е разрешен за тази новина";

$strings["noti_memberactivation1"] = "Акаунта е активиран";
$strings["noti_memberactivation2"] = "Вие сте добавен в системата за управление на клиенти.  Системата е създадена за ваше улеснение за да можете да следните статуса на свойте проекти.\n\nЗа да влезете в системата , насовече своя браузер (вероятно Internet Explorer 6.x или Netscape Navigator 7.x) към $root и въведете:";
$strings["noti_memberactivation3"] = "потребителско име:";
$strings["noti_memberactivation4"] = "парола:";

//BEGIN email project users mod
$strings["email_following"] = "Изпрати съобщение на следните";
$strings["email_sent"] = "Вашия емайл е изпратен успешно.";
//END email project users mod


//2.5b4
$strings["Total_Hours_Worked"] = "Общо изработени часове";
$strings["Pct_Complete"] = "Pct завършени";

$strings["noti_filepost1"] = "Нов файл е качен в системата";
$strings["noti_filepost2"] = "Нов файл е качен в проект:";
$strings["noti_newfile1"] = "Нов файл е качен в системата";
$strings["noti_newfile2"] = "Нов файл е качен в проект:";

//2.5rc1
$strings["location"] = "Местоположение";
$strings["calendar_broadcast"] = "Разпращане";

//2.5rc2
$strings["edit_noti_clientaddtask"] = "Добавена е задача от клиент.";
$strings["edit_noti_uploadfile"] = "Добавен е прикачен файл.";
