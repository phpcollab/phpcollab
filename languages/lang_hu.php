<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/lang_hu.php

//translator(s): ct <ct@mailbox.hu>
$byteUnits = array('Byte', 'KB', 'MB', 'GB');

$dayNameArray = array(1 =>"hétfõ", 2 =>"kedd", 3 =>"szerda", 4 =>"csütörtök", 5 =>"péntek", 6 =>"szombat", 7 =>"vasárnap");

$monthNameArray = array(1=> "január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december");

$status = array(0 => "Ügyfél által teljesített", 1 => "Teljesített", 2 => "El nem kezdett", 3 => "Nyitott", 4 => "Felfüggesztett");

$profil = array(0 => "Adminisztrátor", 1 => "Projektvezetõ", 2 => "Felhasználó", 3 => "Ügyfél felhasználó", 4 => "Inaktív", 5 => "Project Manager Administrator");

$priority = array(0 => "Nincs", 1 => "Nagyon alacsony", 2 => "Alacsony", 3 => "Közepes", 4 => "Magas", 5 => "Nagyon magas");

$statusTopic = array(0 => "Lezárt", 1 => "Nyitott");
$statusTopicBis = array(0 => "Igen", 1 => "Nem");

$statusPublish = array(0 => "Igen", 1 => "Nem");

$statusFile = array(0 => "Jóváhagyott", 1 => "Módosításokkal jóváhagyott", 2 => "Jóváhagyásra vár", 3 => "Nem kell jóváhagyni", 4 => "Nem jóváhagyott");

$phaseStatus = array(0 => "El nem kezdett", 1 => "Nyitott", 2 => "Lezárt", 3 => "Felfüggesztett");

$requestStatus = array(0 => "Új", 1 => "Nyitott", 2 => "Lezárt");

$strings["please_login"] = "Jelentkezz be";
$strings["requirements"] = "Rendszerkövetelmények";
$strings["login"] = "Bejelentkezés";
$strings["no_items"] = "Nincs megjeleníthetõ elem";
$strings["logout"] = "Kijelentkezés";
$strings["preferences"] = "Beállítások";
$strings["my_tasks"] = "Saját feladatok";
$strings["edit_task"] = "Feladat szerkesztése";
$strings["copy_task"] = "Feladat másolása";
$strings["add_task"] = "Feladat hozzáadása";
$strings["delete_tasks"] = "Feladatok törlése";
$strings["assignment_history"] = "Hozzárendelések története";
$strings["assigned_on"] = "Hozzárendelve";
$strings["assigned_by"] = "Hozzárendelte";
$strings["to"] = "Részére";
$strings["comment"] = "Megjegyzés";
$strings["task_assigned"] = "A feladat hozzárendeltje ";
$strings["task_unassigned"] = "A feladat nincs senkihez hozzárendelve";
$strings["edit_multiple_tasks"] = "Több feladat szerkesztése";
$strings["tasks_selected"] = "feladatok kiválasztva. Válassz új értékeket vagy jelöld ki a [Nincs változás] mezõt a jelenlegi értékek megtartásához.";
$strings["assignment_comment"] = "Hozzárendelés megjegyzés";
$strings["no_change"] = "[Nincs változás]";
$strings["my_discussions"] = "Saját fórumok";
$strings["discussions"] = "Fórumok";
$strings["delete_discussions"] = "Fórumok törlése";
$strings["delete_discussions_note"] = "Megjegyzés: A fórumokat nem lehet újra megnyitni, ha azok egyszer már törölve lettek.";
$strings["topic"] = "Téma";
$strings["posts"] = "Hozzászólások";
$strings["latest_post"] = "Legutolsó hozzászólás";
$strings["my_reports"] = "Saját riportok";
$strings["reports"] = "Riportok";
$strings["create_report"] = "Riport létrehozása";
$strings["report_intro"] = "Válaszd ki a riport paramétereket itt, és mentsd el a lekérdezést az eredményoldalon a riport futtatása után.";
$strings["admin_intro"] = "Projekt beállítások és konfiguráció.";
$strings["copy_of"] = "Másolata ";
$strings["add"] = "Hozzáad";
$strings["delete"] = "Töröl";
$strings["remove"] = "Eltávolít";
$strings["copy"] = "Másol";
$strings["view"] = "Megtekint";
$strings["edit"] = "Szerkeszt";
$strings["update"] = "Frissít";
$strings["details"] = "Részletek";
$strings["none"] = "Egyik sem";
$strings["close"] = "Bezár";
$strings["new"] = "Új";
$strings["select_all"] = "Mindet kiválaszt";
$strings["unassigned"] = "Nem hozzárendelt";
$strings["administrator"] = "Adminisztrátor";
$strings["my_projects"] = "Saját projektek";
$strings["project"] = "Projekt";
$strings["active"] = "Active";
$strings["inactive"] = "Inaktív";
$strings["project_id"] = "Projekt ID";
$strings["edit_project"] = "Projekt szerkesztése";
$strings["copy_project"] = "Projekt másolása";
$strings["add_project"] = "Projekt hozzáadása";
$strings["clients"] = "Ügyfelek";
$strings["organization"] = "Ügyfél szervezet";
$strings["client_projects"] = "Ügyfél projektek";
$strings["client_users"] = "Ügyfél felhasználók";
$strings["edit_organization"] = "Ügyfél szervezet szerkesztése";
$strings["add_organization"] = "Ügyfél szervezet hozzáadása";
$strings["organizations"] = "Ügyfél szervezetek";
$strings["info"] = "Info";
$strings["status"] = "Státusz";
$strings["owner"] = "Tulajdonos";
$strings["home"] = "Nyitólap";
$strings["projects"] = "Projektek";
$strings["files"] = "File-ok";
$strings["search"] = "Keresés";
$strings["admin"] = "Admin";
$strings["user"] = "Felhasználó";
$strings["project_manager"] = "Projektvezetõ";
$strings["due"] = "Határidõ";
$strings["task"] = "Feladat";
$strings["tasks"] = "Feladatok";
$strings["team"] = "Csapat";
$strings["add_team"] = "Csapattag hozzáadása";
$strings["team_members"] = "Csapattagok";
$strings["full_name"] = "Teljes név";
$strings["title"] = "Megnevezés (titulus)";
$strings["user_name"] = "Felhasználónév";
$strings["work_phone"] = "Munkahelyi telefon";
$strings["priority"] = "Prioritás";
$strings["name"] = "Név";
$strings["id"] = "ID";
$strings["description"] = "Leírás";
$strings["phone"] = "Telefon";
$strings["url"] = "URL";
$strings["address"] = "Cím";
$strings["comments"] = "Megjegyzések";
$strings["created"] = "Létrehozva";
$strings["assigned"] = "Hozzárendelve";
$strings["modified"] = "Módosítva";
$strings["assigned_to"] = "Hozzárendelt";
$strings["due_date"] = "Határidõ dátuma";
$strings["estimated_time"] = "Becsült idõráfordítás";
$strings["actual_time"] = "Tényleges idõráfordítás";
$strings["delete_following"] = "Töröljem a következõket?";
$strings["cancel"] = "Mégsem";
$strings["and"] = "és";
$strings["administration"] = "Adminisztráció";
$strings["user_management"] = "Felhasználók adminisztrációja";
$strings["system_information"] = "Rendszerinformációk";
$strings["product_information"] = "Termékinformációk";
$strings["system_properties"] = "Rendszer tulajdonságai";
$strings["create"] = "Létrehoz";
$strings["report_save"] = "Mentsd el ezt a riportot a nyitólapodra, hogy újra futtathasd.";
$strings["report_name"] = "Riport neve";
$strings["save"] = "Mentés";
$strings["matches"] = "Találat";
$strings["match"] = "Találat";
$strings["report_results"] = "Riport eredmények";
$strings["success"] = "Siker";
$strings["addition_succeeded"] = "Sikeres hozzáadás.";
$strings["deletion_succeeded"] = "Sikeres törlés";
$strings["report_created"] = "Létrehozott riport";
$strings["deleted_reports"] = "Törölt riportok";
$strings["modification_succeeded"] = "Sikeres módosítás";
$strings["errors"] = "Hiba!";
$strings["blank_user"] = "A felhasználó nem található.";
$strings["blank_organization"] = "Az ügyfél szervezet nem található.";
$strings["blank_project"] = "A projekt nem található.";
$strings["user_profile"] = "Felhasználói profil";
$strings["change_password"] = "Jelszómódosítás";
$strings["change_password_user"] = "Felhasználó jelszómódosítása";
$strings["old_password_error"] = "A megadott régi jelszó hibás. Kérlek add meg újra a régi jelszót.";
$strings["new_password_error"] = "A két jelszó nem egyezik. Kérlek add meg újra az új jelszót.";
$strings["notifications"] = "Értesítések";
$strings["change_password_intro"] = "Add meg a régi jelszavad, aztán add meg és erõsítsd meg az új jelszót.";
$strings["old_password"] = "Régi jelszó";
$strings["password"] = "Jelszó";
$strings["new_password"] = "Új jelszó";
$strings["confirm_password"] = "Jelszó megerõsítése";
$strings["email"] = "E-mail";
$strings["home_phone"] = "Otthoni telefonszám";
$strings["mobile_phone"] = "Mobil telefonszám";
$strings["fax"] = "Fax";
$strings["permissions"] = "Jogosultságok";
$strings["administrator_permissions"] = "Adminisztrátori jogosultság";
$strings["project_manager_permissions"] = "Projektvezetõi jogosultság";
$strings["user_permissions"] = "Felhasználói jogosultság";
$strings["account_created"] = "Account létrehozva";
$strings["edit_user"] = "Felhasználó szerkesztése";
$strings["edit_user_details"] = "Felhasználói account részleteinek szerkesztése.";
$strings["change_user_password"] = "Felhasználó jelszavának módosítása.";
$strings["select_permissions"] = "Válaszd ki a felhasználó jogosultságait";
$strings["add_user"] = "Felhasználó hozzáadása";
$strings["enter_user_details"] = "Add meg a felhasználó részleteit";
$strings["enter_password"] = "Add meg a felhasználó jelszavát";
$strings["success_logout"] = "Sikeresen kijelentkeztél. Újra bejelentkezhetsz a felhasználóneved és jelszavad megadásával.";
$strings["invalid_login"] = "A megadott felhasználónév és/vagy jelszó hibás. Kérlek add meg újra a bejelentkezési információkat.";
$strings["profile"] = "Profil";
$strings["user_details"] = "Felhasználói account részletek.";
$strings["edit_user_account"] = "Szerkeszd az account információkat.";
$strings["no_permissions"] = "Ezen tevékenységre nem vagy jogosult.";
$strings["discussion"] = "Fórum";
$strings["retired"] = "Visszavonult";
$strings["last_post"] = "Utolsó hozzászólás";
$strings["post_reply"] = "Hozzászólás: ";
$strings["posted_by"] = "Hozzászóló";
$strings["when"] = "Mikor";
$strings["post_to_discussion"] = "Hozzászólás a fórumhoz";
$strings["message"] = "Üzenet";
$strings["delete_reports"] = "Riportok törlése";
$strings["delete_projects"] = "Projektek törlése";
$strings["delete_organizations"] = "Ügyfél szervezetek törlése";
$strings["delete_organizations_note"] = "Megjegyzés: Ezzel törlöd az ügyfél szervezethez tartozó összes ügyfél felhasználót, valamint az ügyfél szervezethez tartozó nyitott projektek nem lesznek senkihez sem rendelve.";
$strings["delete_messages"] = "Üzenetek törlése";
$strings["attention"] = "Figyelem";
$strings["delete_teamownermix"] = "Sikeres törlés, de a projekt tulajdonosát nem törölhetõ a projekt csapatból.";
$strings["delete_teamowner"] = "A projekt tulajdonosa nem törölhetõ a projekt csapatból.";
$strings["enter_keywords"] = "Kulcsszavak megadása";
$strings["search_options"] = "Kulcsszavak és keresési opciók";
$strings["search_note"] = "A Keresés mezõbe meg kell adni információt.";
$strings["search_results"] = "Keresési eredmények";
$strings["users"] = "Felhasználók";
$strings["search_for"] = "Keresés tárgya";
$strings["results_for_keywords"] = "Keresési eredmények kulcsszavakra";
$strings["add_discussion"] = "Fórum hozzáadása";
$strings["delete_users"] = "Felhasználói accountok törlése";
$strings["reassignment_user"] = "Projekt és feladat újra-hozzárendelése";
$strings["there"] = "Van";
$strings["owned_by"] = "a fenti felhasználók tulajdonában.";
$strings["reassign_to"] = "Mielõtt törlöd a felhasználókat újra rendeld hozzá ezeket neki: ";
$strings["no_files"] = "Nincsenek csatolt file-ok";
$strings["published"] = "Publikálva";
$strings["project_site"] = "Projekt site";
$strings["approval_tracking"] = "Jóváhagyás követés";
$strings["size"] = "Méret";
$strings["add_project_site"] = "Hozzáadás a projekt site-hoz";
$strings["remove_project_site"] = "Eltávolítás a projekt site-ról";
$strings["more_search"] = "További keresési opciók";
$strings["results_with"] = "Eredmények keresése ezzel:";
$strings["search_topics"] = "Témák keresése";
$strings["search_properties"] = "Keresési tulajdonságok";
$strings["date_restrictions"] = "Dátum korlátozások";
$strings["case_sensitive"] = "Kisbetû/nagybetû-érzékenység";
$strings["yes"] = "Igen";
$strings["no"] = "Nem";
$strings["sort_by"] = "Rendezés";
$strings["type"] = "Típus";
$strings["date"] = "Date";
$strings["all_words"] = "minden szó";
$strings["any_words"] = "bármelyik szó";
$strings["exact_match"] = "pontos megfeleltetés";
$strings["all_dates"] = "Minden dátum";
$strings["between_dates"] = "Dátumok között";
$strings["all_content"] = "Minden tartalom";
$strings["all_properties"] = "Minden tulajdonság";
$strings["no_results_search"] = "A keresés nem hozott eredményt.";
$strings["no_results_report"] = "A riport nem hozott eredményt.";
$strings["schema_date"] = "ÉÉÉÉ/HH/NN";
$strings["hours"] = "óra";
$strings["choice"] = "Opció";
$strings["missing_file"] = "Hiányzó file!";
$strings["project_site_deleted"] = "A projekt site sikeresen törölve.";
$strings["add_user_project_site"] = "A felhasználó sikeresen kapott hozzáférést a projekt site-hoz.";
$strings["remove_user_project_site"] = "A felhasználó jogosultsága sikeresen törölve.";
$strings["add_project_site_success"] = "A hozzáadás a projekt site-hoz sikeres.";
$strings["remove_project_site_success"] = "A törlés a projekt site-ról siekeres.";
$strings["add_file_success"] = "Csatolva 1 tartalmi elem.";
$strings["delete_file_success"] = "Lecsatolás sikeres.";
$strings["update_comment_file"] = "A file megjegyzés frissítése sikeres.";
$strings["session_false"] = "Session hiba";
$strings["logs"] = "Log-ok";
$strings["logout_time"] = "Automatikus kijelentkezés";
$strings["noti_foot1"] = "Ezt az értesítõt a a PhpCollab generálta.";
$strings["noti_foot2"] = "A PhpCollab nyitólaphoz kattints ide:";
$strings["noti_taskassignment1"] = "Új feladat:";
$strings["noti_taskassignment2"] = "Új feladatot kaptál:";
$strings["noti_moreinfo"] = "További információkért kattints ide:";
$strings["noti_prioritytaskchange1"] = "A feladat prioritása változott:";
$strings["noti_prioritytaskchange2"] = "Az alábbi feladat prioritása változott:";
$strings["noti_statustaskchange1"] = "A feladat státusza változott:";
$strings["noti_statustaskchange2"] = "Az alábbi feladat státusza változott:";
$strings["login_username"] = "Meg kell adj egy felhasználónevet.";
$strings["login_password"] = "Adj meg egy jelszót.";
$strings["login_clientuser"] = "Ez egy ügyfél account. A PhpCollab nem hozzáférhetõ üyfél account-tal.";
$strings["user_already_exists"] = "Már van ilyen nevû felhasználó. Kérlek válaszd a felhasználónév egy másik variációját.";
$strings["noti_duedatetaskchange1"] = "Feladat határideje változott:";
$strings["noti_duedatetaskchange2"] = "Az alábbi feladat határideje változott:";
$strings["company"] = "Vállalat";
$strings["show_all"] = "Mindet mutat";
$strings["information"] = "Információ";
$strings["delete_message"] = "Üzenet törlése";
$strings["project_team"] = "Projekt csapat";
$strings["document_list"] = "Dokumentum lista";
$strings["bulletin_board"] = "Fórum";
$strings["bulletin_board_topic"] = "Fórum téma";
$strings["create_topic"] = "Új téma";
$strings["topic_form"] = "Téme form";
$strings["enter_message"] = "Üzenet";
$strings["upload_file"] = "file feltöltés";
$strings["upload_form"] = "Feltöltõ form";
$strings["upload"] = "Feltölt";
$strings["document"] = "Dokumentum";
$strings["approval_comments"] = "Megjegyzések jóváhagyása";
$strings["client_tasks"] = "Ügyfél feladatok";
$strings["team_tasks"] = "Csapat feladatok";
$strings["team_member_details"] = "Project csapattagok részletei";
$strings["client_task_details"] = "Ügyfél feladat részletei";
$strings["team_task_details"] = "Csapat feladat részletei";
$strings["language"] = "Nyelv";
$strings["welcome"] = "Üdvözöllek";
$strings["your_projectsite"] = "a projekt site-on";
$strings["contact_projectsite"] = "Ha bármilyen további kérdése van az extranettel vagy az itt található információkkal kapcsolatban, forduljon a projekt vezetõjéhez";
$strings["company_details"] = "Cég részletek";
$strings["database"] = "Adatbázis mentés és visszaállítás";
$strings["company_info"] = "Szerkessze az Ön cégének információit";
$strings["create_projectsite"] = "Projekt site létrehozása";
$strings["projectsite_url"] = "Projekt site URL";
$strings["design_template"] = "Design template";
$strings["preview_design_template"] = "Design temlate elõnézet";
$strings["delete_projectsite"] = "Projekt site törlése";
$strings["add_file"] = "File hozzáadása";
$strings["linked_content"] = "Csatolt tartalom";
$strings["edit_file"] = "File részleteinek szerkesztése";
$strings["permitted_client"] = "Engedélyezett ügyfél felhasználók";
$strings["grant_client"] = "Jogosultság delegálása a projekt site megtekintéséhez";
$strings["add_client_user"] = "Ügyfél felhasználó hozzáadása";
$strings["edit_client_user"] = "Ügyfél felhasználó szerkesztése";
$strings["client_user"] = "Ügyfél felhasználó";
$strings["client_change_status"] = "Vátoztasd meg a státuszt alább, ha teljesítetted ezt a feladatot.";
$strings["project_status"] = "Projekt státusz";
$strings["view_projectsite"] = "Projekt site megtekintése";
$strings["enter_login"] = "Add meg a felhasználóneved, hogy új jelszót kaphass";
$strings["send"] = "Küldés";
$strings["no_login"] = "A felhasználónév nincs az adatbázisban";
$strings["email_pwd"] = "Jelszó elküldve";
$strings["no_email"] = "E-mail cím nélküli felhasználó";
$strings["forgot_pwd"] = "Elfelejtetted a jelszavad?";
$strings["project_owner"] = "Csak a saját projektjeidet módosíthatod.";
$strings["connected"] = "Kapcsolat";
$strings["session"] = "Session";
$strings["last_visit"] = "Utolsó látogatás";
$strings["compteur"] = "Számláló";






$strings["ip"] = "Ip";
$strings["task_owner"] = "Te nem vagy ennek a projektnek a tagja";
$strings["export"] = "Export";
$strings["reassignment_clientuser"] = "Feladat újra-hozzárendelése";
$strings["organization_already_exists"] = "Ez a név már szerepel a rendszerben, kérlek válassz másikat.";
$strings["blank_organization_field"] = "Az ügyfél szervezet nevet meg kell adni.";
$strings["blank_fields"] = "kötelezõ mezõk";
$strings["projectsite_login_fails"] = "Nem tudjuk megerõsíteni ezt a felhasználónév/jelszó kombinációt.";
$strings["start_date"] = "Kezdés dátuma";
$strings["completion"] = "Teljesítés";
$strings["update_available"] = "Van új verzió!";
$strings["version_current"] = "A jelenleg használt verzió";
$strings["version_latest"] = "Az utolsó verzió";
$strings["sourceforge_link"] = "Projekt oldal a Sourceforge-on";
$strings["demo_mode"] = "Demo üzem. A funkció nincs engedélyezve.";
$strings["setup_erase"] = "Töröld a setup.php file-t!";
$strings["no_file"] = "Nincs kiválasztott file";
$strings["exceed_size"] = "Maximális fileméret meghaladva";
$strings["no_php"] = "Php file nem megengedett";
$strings["approval_date"] = "Jóváhagyás dátuma";
$strings["approver"] = "Jóváhagyó";
$strings["error_database"] = "Adatbázis kapcsolat sikertelen";
$strings["error_server"] = "Szerver kapcsolat sikertelen";
$strings["version_control"] = "Verzói ellenõrzés";
$strings["vc_status"] = "Státusz";
$strings["vc_last_in"] = "Utolsó módosítás dátuma";
$strings["ifa_comments"] = "Jóváhagyási megjegyzések";
$strings["ifa_command"] = "Jóváhagyási státusz megváltoztatása";
$strings["vc_version"] = "Verzió";
$strings["ifc_revisions"] = "Verzió felülvizsgálat";
$strings["ifc_revision_of"] = "Verzió felülvizsgálat";
$strings["ifc_add_revision"] = "Verzió felülvizsgálat hozzáadása";
$strings["ifc_update_file"] = "File frissítése";
$strings["ifc_last_date"] = "Utolsó módosítás dátuma";
$strings["ifc_version_history"] = "Verzió történet";
$strings["ifc_delete_file"] = "File, valamint az összes verzió törlése";
$strings["ifc_delete_version"] = "Kiválasztott verzió törlése";
$strings["ifc_delete_review"] = "Kiválasztott verzió törlése";
$strings["ifc_no_revisions"] = "Jelen dokumentumnak nincs felülvizsgálata";
$strings["unlink_files"] = "File lecsatolása";
$strings["remove_team"] = "Csapattagok eltávolítása";
$strings["remove_team_info"] = "Ezen felhasználók eltávolítása a projekt csapatból?";
$strings["remove_team_client"] = "Jogosultság elvétele a projekt site megtekintéséhez";
$strings["note"] = "Megjegyzés";
$strings["notes"] = "Megjegyzések";
$strings["subject"] = "Téma";
$strings["delete_note"] = "Megjegyzések törlése";
$strings["add_note"] = "Megjegyzés hozzáadása";
$strings["edit_note"] = "Megjegyzés szerkesztése";
$strings["version_increm"] = "Válaszd ki az alkalmazandó verzóváltozást:";
$strings["url_dev"] = "Fejlesztési site URL";
$strings["url_prod"] = "Éles site URL";
$strings["note_owner"] = "Csak a saját megjegyzéseidet módosíthatod.";
$strings["alpha_only"] = "A felhasználónévben csak alfanumerikus kaakterek szerepelhetnek";
$strings["edit_notifications"] = "E-mail értesítések szerkesztése";
$strings["edit_notifications_info"] = "Események kiválasztása, melyhez e-mail értesítést kérsz.";
$strings["select_deselect"] = "Kiválaszt/Kiválasztás törlése";
$strings["noti_addprojectteam1"] = "Hozzáadásra került a projekt csapathoz:";
$strings["noti_addprojectteam2"] = "Hozzáadásra kerültél a projekt csapathoz:";
$strings["noti_removeprojectteam1"] = "Kikerült a projekt csapatból:";
$strings["noti_removeprojectteam2"] = "Kikerültél a projekt csapatból:";
$strings["noti_newpost1"] = "Új hozzászólás :";
$strings["noti_newpost2"] = "Új hozzászólás érkezett az alábbi fórumhoz :";
$strings["edit_noti_taskassignment"] = "Új feladathoz lettem hozzárendelve.";
$strings["edit_noti_statustaskchange"] = "Valamelyik feladatom státusza változik.";
$strings["edit_noti_prioritytaskchange"] = "Valamelyik feladatom prioritása változik.";
$strings["edit_noti_duedatetaskchange"] = "Valamelyik feladatom határideje változik.";
$strings["edit_noti_addprojectteam"] = "Bekerültem egy projekt csapatba.";
$strings["edit_noti_removeprojectteam"] = "Kikerültem egy projekt csapatból.";
$strings["edit_noti_newpost"] = "Új hozzászólás érkezett egy fórumba.";
$strings["add_optional"] = "Vegyél fel egy opcionálisan egy";
$strings["assignment_comment_info"] = "Megjegyzés hozzáfûzése jelen feladat hozzárendelése kapcsán";
$strings["my_notes"] = "Saját jegyzetek";
$strings["edit_settings"] = "Beállítások szerkesztése";
$strings["max_upload"] = "Maximális fileméret";
$strings["project_folder_size"] = "Projekt könyvtár mérete";
$strings["calendar"] = "Naptár";
$strings["date_start"] = "Kezdés dátuma";
$strings["date_end"] = "Befejezés dátuma";
$strings["time_start"] = "Kezdés ideje";
$strings["time_end"] = "Befejezés ideje";
$strings["calendar_reminder"] = "Emlékeztetõ";
$strings["shortname"] = "Rövid név";
$strings["calendar_recurring"] = "Az esemény hetente ismétlõdik ezen a napon";
$strings["edit_database"] = "Adatbázis szerkesztése";
$strings["noti_newtopic1"] = "Új fórum:";
$strings["noti_newtopic2"] = "Új fórum lett létrehozva az alábbi projekthez csatolva:";
$strings["edit_noti_newtopic"] = "Új fórum téma lett létrehozva.";
$strings["today"] = "Ma";
$strings["previous"] = "Elõzõ";
$strings["next"] = "Következõ";
$strings["help"] = "Súgó";
$strings["complete_date"] = "Teljesítés dátuma ";
$strings["scope_creep"] = "Határidõhöz képest";
$strings["days"] = "nap";
$strings["logo"] = "Logo";
$strings["remember_password"] = "Jelszó megjegyzése";
$strings["client_add_task_note"] = "Megjegyzés: A hozzáadott feladat bekerült az adatbázisba, de itt csak akkor fog megjelenni, ha egy csapattaghoz hozzárendelésre került!";
$strings["noti_clientaddtask1"] = "A feladatot hozzáadó ügyfél:";
$strings["noti_clientaddtask2"] = "Egy új feladat került hozzáadásra az ügyfél által a projekt siteról az alábbi projekthez:";
$strings["phase"] = "Fázis";
$strings["phases"] = "Fázisok";
$strings["phase_id"] = "Fázis ID";
$strings["current_phase"] = "Akítv fázis(ok)";
$strings["total_tasks"] = "Összes feladat";
$strings["uncomplete_tasks"] = "Lezáratlan feladatok";
$strings["no_current_phase"] = "Nincs aktív fázis";
$strings["true"] = "Igaz";

$strings["false"] = "Hamis";
$strings["enable_phases"] = "Fázisok engedélyezése";
$strings["phase_enabled"] = "Fázis engedélyezve";
$strings["order"] = "Rendezés";
$strings["options"] = "Opciók";
$strings["support"] = "Support";
$strings["support_request"] = "Support kérés";
$strings["support_requests"] = "Support kérések";
$strings["support_id"] = "Kérés ID";
$strings["my_support_request"] = "Saját support kérések";
$strings["introduction"] = "Bemutatkozás";
$strings["submit"] = "Elküld";
$strings["support_management"] = "Support menedzsment";
$strings["date_open"] = "Dátum nyitva";
$strings["date_close"] = "Dátum lezárva";
$strings["add_support_request"] = "Hozz létre support kérést";
$strings["add_support_response"] = "Adj support választ";
$strings["respond"] = "Válaszolj";
$strings["delete_support_request"] = "Support kérés törölve";
$strings["delete_request"] = "Support kérés törlése";
$strings["delete_support_post"] = "Support megyjegyzés törlése";
$strings["new_requests"] = "Új kérések";
$strings["open_requests"] = "Nyitott kérések ";
$strings["closed_requests"] = "Lezárt kérések";
$strings["manage_new_requests"] = "Új kérések menedzselése";
$strings["manage_open_requests"] = "Nyitott kérések menedzselése";
$strings["manage_closed_requests"] = "Lezárt kérések menedzselése";
$strings["responses"] = "Válaszok";
$strings["edit_status"] = "Státusz szerkesztése";
$strings["noti_support_request_new2"] = "Sikeresen létrehoztál egy support kérést az alábbi témában: ";
$strings["noti_support_post2"] = "Új válasz érkezett a support kérésedre. Részletek alább.";
$strings["noti_support_status2"] = "A Te support kérésed frissítésre került. Részletek alább:";
$strings["noti_support_team_new2"] = "Az új support kérés hozzáadásra került: ";
//2.0
$strings["delete_subtasks"] = "Alfeladatok törlése";
$strings["add_subtask"] = "Alfeladat hozzáadása";
$strings["edit_subtask"] = "Alfeladat szerkesztése";
$strings["subtask"] = "Alfeladat";
$strings["subtasks"] = "Alfeladatok";
$strings["show_details"] = "Részletek";
$strings["updates_task"] = "Feladat frissítés története";
$strings["updates_subtask"] = "Alfeladat frissítés története";
//2.1
$strings["go_projects_site"] = "Projekt site megnyitása";
$strings["bookmark"] = "Kedvenc";
$strings["bookmarks"] = "Kedvencek";
$strings["bookmark_category"] = "Kategória";
$strings["bookmark_category_new"] = "Új kategória";
$strings["bookmarks_all"] = "Összes";
$strings["bookmarks_my"] = "Saját kedvencek";
$strings["my"] = "Saját";
$strings["bookmarks_private"] = "Privát";
$strings["shared"] = "Megosztott";
$strings["private"] = "Privát";
$strings["add_bookmark"] = "Kedvenc hozzáadása";
$strings["edit_bookmark"] = "Kedvenc szerkesztés";
$strings["delete_bookmarks"] = "Kedvencek törlése";
$strings["team_subtask_details"] = "Csapat alfeladat részletek";
$strings["client_subtask_details"] = "Ügyfél alfeladat részletek";
$strings["client_change_status_subtask"] = "Változtasd meg a státuszod alább, ha végeztél az alfeladattal.";
$strings["disabled_permissions"] = "Inaktív account";
$strings["user_timezone"] = "Idõzóna (GMT)";
//2.2
$strings["project_manager_administrator_permissions"] = "Project Manager Administrator";
$strings["bug"] = "Bug Tracking";
//2.3
$strings["report"] = "Report";
$strings["license"] = "License";
//2.4
$strings["settings_notwritable"] = "Settings.php file is not writable";
