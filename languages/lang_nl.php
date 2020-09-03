<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/lang_nl.php

//translator(s): Hendrik Bijlsma, Erwin Wondergem, Dave Liefbroer
$byteUnits = array('Bytes', 'KB', 'MB', 'GB');

$dayNameArray = array(
    1 => "Maandag",
    2 => "Dinsdag",
    3 => "Woensdag",
    4 => "Donderdag",
    5 => "Vrijdag",
    6 => "Zaterdag",
    7 => "Zondag"
);

$monthNameArray = array(
    1 => "Januari",
    "Februari",
    "Maart",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Augustus",
    "September",
    "Oktober",
    "November",
    "December"
);

$status = array(0 => "Client Voltooid", 1 => "Voltooid", 2 => "Niet Gestart", 3 => "Open", 4 => "Opgeschort");

$profil = array(
    0 => "Beheerder",
    1 => "Project Manager",
    2 => "Gebruiker",
    3 => "Client Gebruiker",
    4 => "Disabled",
    5 => "Project Manager Administrator"
);

$priority = array(0 => "Geen", 1 => "Erg laag", 2 => "Laag", 3 => "Gemiddeld", 4 => "Hoog", 5 => "Erg hoog");

$statusTopic = array(0 => "Gesloten", 1 => "Open");
$statusTopicBis = array(0 => "Ja", 1 => "Nee");

$statusPublish = array(0 => "Ja", 1 => "Nee");

$statusFile = array(
    0 => "Goedgekeurd",
    1 => "Goedgekeurd Met Wijziging",
    2 => "Goedkeuring Nodig",
    3 => "Geen Goedkeuring Nodig",
    4 => "Niet Goedgekeurd"
);

$phaseStatus = array(0 => "Niet gestart", 1 => "Open", 2 => "Klaar", 3 => "Uitgesteld");

$requestStatus = array(0 => "Nieuw", 1 => "Open", 2 => "Klaar");

$strings["please_login"] = "Login a.u.b.";
$strings["requirements"] = "Systeem Vereisten";
$strings["login"] = "Inloggen";
$strings["no_items"] = "Geen item om te laten zien";
$strings["logout"] = "Uitloggen";
$strings["preferences"] = "Voorkeuren";
$strings["my_tasks"] = "Mijn Taken";
$strings["edit_task"] = "Taak Aanpassen";
$strings["copy_task"] = "Taak Kopieëren";
$strings["add_task"] = "Taak Toevoegen";
$strings["delete_tasks"] = "Verwijder Taken";
$strings["assignment_history"] = "Toewijzingsgeschiedenis";
$strings["assigned_on"] = "Toegewezen Op";
$strings["assigned_by"] = "Toegewezen Door";
$strings["to"] = "Aan";
$strings["comment"] = "Opmerking";
$strings["task_assigned"] = "Taak toegewezen aan ";
$strings["task_unassigned"] = "Taak toegewezen aan Niet toegewezen (Niet toegewezen)"; // is dit wel prettig leesbaar
$strings["edit_multiple_tasks"] = "Wijzig Meerdere Taken";
$strings["tasks_selected"] = "taken geselecteerd. Kies nieuwe waarden voor deze taken, of selecteer [Geen wijziging] om de huidige waarden te behouden.";
$strings["assignment_comment"] = "Toewijzingsopmerking";
$strings["no_change"] = "[Geen Wijziging]";
$strings["my_discussions"] = "Mijn Discussies";
$strings["discussions"] = "Discussies";
$strings["delete_discussions"] = "Verwijder Discussies";
$strings["delete_discussions_note"] = "Let op: Discussies kunnen niet heropend worden wanneer ze eenmaal verwijderd zijn.";
$strings["topic"] = "Onderwerp";
$strings["posts"] = "Plaatsing";
$strings["latest_post"] = "Laatste Plaatsing";
$strings["my_reports"] = "Mijn Overzichten";
$strings["reports"] = "Overzichten";
$strings["create_report"] = "Overzicht aanmaken";
$strings["report_intro"] = "Selecteer uw taak rapporteer parameters hier en bewaar deze op de rapporten pagina na het draaien van het overzicht.";
$strings["admin_intro"] = "Project instellingen en configuratie.";
$strings["copy_of"] = "Kopie van ";
$strings["add"] = "Toevoegen";
$strings["delete"] = "Wissen";
$strings["remove"] = "Verwijderen";
$strings["copy"] = "Kopieëren";
$strings["view"] = "Bekijken";
$strings["edit"] = "Bewerken";
$strings["update"] = "Vernieuwen";
$strings["details"] = "Details";
$strings["none"] = "Geen";
$strings["close"] = "Sluiten";
$strings["new"] = "Nieuw";
$strings["select_all"] = "Alles Selecteren";
$strings["unassigned"] = "Niet Toegewezen";
$strings["administrator"] = "Beheerder";
$strings["my_projects"] = "Mijn Projecten";
$strings["project"] = "Project";
$strings["active"] = "Actief";
$strings["inactive"] = "Inactief";
$strings["project_id"] = "Project ID"; //dit zou niet nodig moeten zijn
$strings["edit_project"] = "Bewerk Project";
$strings["copy_project"] = "Kopieër Project";
$strings["add_project"] = "Project Toevoegen";
$strings["clients"] = "Cliënten";
$strings["organization"] = "Bedrijfsinformatie van Cliënt";
$strings["client_projects"] = "Projecten van Cliënt";
$strings["client_users"] = "Gebruikers van Cliënt";
$strings["edit_organization"] = "Bewerk Bedrijfsinformatie van Cliënt";
$strings["add_organization"] = "Bedrijf Toevoegen";
$strings["organizations"] = "Cliënt Bedrijven";
$strings["info"] = "Info";
$strings["status"] = "Status";
$strings["owner"] = "Eigenaar";
$strings["home"] = "Begin";
$strings["projects"] = "Projecten";
$strings["files"] = "Bestanden";
$strings["search"] = "Zoeken";
$strings["admin"] = "Beheerder";
$strings["user"] = "Gebruiker";
$strings["project_manager"] = "Project Manager";
$strings["due"] = "Vervalt op";
$strings["task"] = "Taak";
$strings["tasks"] = "Taken";
$strings["team"] = "Groep";
$strings["add_team"] = "Groepsleden Toevoegen";
$strings["team_members"] = "Groepsleden";
$strings["full_name"] = "Volledige naam";
$strings["title"] = "Titel";
$strings["user_name"] = "Gebruikersnaam";
$strings["work_phone"] = "Tel. (werk)";
$strings["priority"] = "Prioriteit";
$strings["name"] = "Naam";
$strings["id"] = "ID";
$strings["description"] = "Omschrijving";
$strings["phone"] = "Tel.";
$strings["url"] = "URL";
$strings["address"] = "Adres";
$strings["comments"] = "Opmerkingen";
$strings["created"] = "Gemaakt op";
$strings["assigned"] = "Toegewezen";
$strings["modified"] = "Aangepast";
$strings["assigned_to"] = "Toegewezen aan";
$strings["due_date"] = "Vervaldatum";
$strings["estimated_time"] = "Geschatte Tijd";
$strings["actual_time"] = "Werkelijke Tijd";
$strings["delete_following"] = "Verwijder het volgende?";
$strings["cancel"] = "Annuleren";
$strings["and"] = "en";
$strings["administration"] = "Administratie";
$strings["user_management"] = "Gebruikersbeheer";
$strings["system_information"] = "Systeem Informatie";
$strings["product_information"] = "Product Informatie";
$strings["system_properties"] = "Systeemeigenschappen";
$strings["create"] = "Maken";
$strings["report_save"] = "Bewaar dit rapport op uw webpagina zodat u deze vaker kunt draaien.";
$strings["report_name"] = "Rapport naam";
$strings["save"] = "Bewaar";
$strings["matches"] = "Overeenkomsten";
$strings["match"] = "Overeenkomst";
$strings["report_results"] = "Rapport Resultaten";
$strings["success"] = "Succes";
$strings["addition_succeeded"] = "Toevoeging geslaagd";
$strings["deletion_succeeded"] = "Verwijderen gelukt";
$strings["report_created"] = "Rapport gemaakt";
$strings["deleted_reports"] = "Rapporten verwijderd";
$strings["modification_succeeded"] = "Wijziging geslaagd";
$strings["errors"] = "Fouten gevonden!";
$strings["blank_user"] = "De gebruiker is niet gevonden.";
$strings["blank_organization"] = "Het cliëntbedrijf is niet gevonden.";
$strings["blank_project"] = "Het project is niet gevonden.";
$strings["user_profile"] = "Gebruikersprofiel";
$strings["change_password"] = "Wijzig wachtwoord";
$strings["change_password_user"] = "Wijzig wachtwoord van gebruiker.";
$strings["old_password_error"] = "Het oude wachtwoord is niet goed, probeer het nog eens.";
$strings["new_password_error"] = "De twee nieuwe wachtwoorden zijn niet identiek, probeer het nog eens.";
$strings["notifications"] = "Kennisgevingen";
$strings["change_password_intro"] = "Typ uw oude wachtwoord, vul daarna het nieuwe in en bevestig deze.";
$strings["old_password"] = "Oude Wachtwoord";
$strings["password"] = "Wachtwoord";
$strings["new_password"] = "Nieuw Wachtwoord";
$strings["confirm_password"] = "Bevestig Wachtwoord";
$strings["email"] = "E-Mail";
$strings["home_phone"] = "Tel. (thuis)";
$strings["mobile_phone"] = "Tel. (Mobiel)";
$strings["fax"] = "Fax";
$strings["permissions"] = "Rechten";
$strings["administrator_permissions"] = "Beheerders rechten";
$strings["project_manager_permissions"] = "Projectbeheerder Rechten";
$strings["user_permissions"] = "Gebruikers Rechten";
$strings["account_created"] = "Profiel aangemaakt";
$strings["edit_user"] = "Bewerk Gebruikersgegevens";
$strings["edit_user_details"] = "Bewerk gebruikerdetails.";
$strings["change_user_password"] = "Verander het gebruikerswachtwoord.";
$strings["select_permissions"] = "Selecteer de rechten voor deze gebruiker";
$strings["add_user"] = "Gebruiker Toevoegen";
$strings["enter_user_details"] = "Vul de details in voor het profiel die u aan het maken bent.";
$strings["enter_password"] = "Geef het gebruikserswachtwoord.";
$strings["success_logout"] = "Afmelden geslaagd, u kunt zich opnieuw aanmelden door gebruikersnaam en wachtwoord hieronder in te vullen.";
$strings["invalid_login"] = "De gebruikersnaam of het wachtwoord is niet correct, probeer het nog eens.";
$strings["profile"] = "Profiel";
$strings["user_details"] = "Profieldetails.";
$strings["edit_user_account"] = "Bewerk uw profielinformatie.";
$strings["no_permissions"] = "U heeft niet voldoende rechten om deze actie uit te voeren.";
$strings["discussion"] = "Discussie";
$strings["retired"] = "Beëindigd";
$strings["last_post"] = "Laatste Plaatsing";
$strings["post_reply"] = "Plaatsing Beantwoorden";
$strings["posted_by"] = "Plaatsing Door";
$strings["when"] = "Wanneer";
$strings["post_to_discussion"] = "Plaatsing naar Discussie";
$strings["message"] = "Bericht";
$strings["delete_reports"] = "Verwijder Rapporten";
$strings["delete_projects"] = "Verwijder Projects";
$strings["delete_organizations"] = "Verwijder Cliënt Bedrijf";
$strings["delete_organizations_note"] = "Let op: Deze actie verwijderd ook alle cliënt gebruikers en koppel alle projecten los van deze bedrijven.";
$strings["delete_messages"] = "Verwijder Berichten";
$strings["attention"] = "Attentie";
$strings["delete_teamownermix"] = "Verwijdering geslaagd, maar de projecteigenaar kan niet worden verwijderd uit de groep.";
$strings["delete_teamowner"] = "U kunt de projecteigenaar niet verwijderen uit de groep.";
$strings["enter_keywords"] = "Kernwoorden invoeren";
$strings["search_options"] = "Kernwoord en Zoek Opties";
$strings["search_note"] = "Er moet informatie in het zoekveld ingevoerd zijn.";
$strings["search_results"] = "Zoek resultaten";
$strings["users"] = "Gebruikers";
$strings["search_for"] = "Zoek naar";
$strings["results_for_keywords"] = "Zoek naar resultaten voor de kernwoorden";
$strings["add_discussion"] = "Discussie Toevoegen";
$strings["delete_users"] = "Gebruiker Verwijderen";
$strings["reassignment_user"] = "Project en Taak Hertoewijzing";
$strings["there"] = "Er zijn";
$strings["owned_by"] = "toegewezen aan de bovenstaande gebruikers.";
$strings["reassign_to"] = "Voor het verwijderen, wijs deze opnieuw toe aan";
$strings["no_files"] = "Geen bestanden gekoppeld";
$strings["published"] = "Gepubliceerd";
$strings["project_site"] = "Project Pagina";
$strings["approval_tracking"] = "Goedkeur Tracering"; //nog eens nakijken
$strings["size"] = "Grootte";
$strings["add_project_site"] = "Aan Project Pagina Toevoegen";
$strings["remove_project_site"] = "Verwijder van Project Pagina";
$strings["more_search"] = "Meer zoekopties";
$strings["results_with"] = "Vind Resultaten Met";
$strings["search_topics"] = "Zoekonderwerpen";
$strings["search_properties"] = "Zoekeigenschappen";
$strings["date_restrictions"] = "Datum Restricties";
$strings["case_sensitive"] = "Hoofdletter gevoelig";
$strings["yes"] = "Ja";
$strings["no"] = "Nee";
$strings["sort_by"] = "Sorteer op";
$strings["type"] = "Soort";
$strings["date"] = "Datum";
$strings["all_words"] = "alle woorden";
$strings["any_words"] = "elk woord";
$strings["exact_match"] = "exacte overeenkomst";
$strings["all_dates"] = "Alle datums";
$strings["between_dates"] = "Tussen datums";
$strings["all_content"] = "Alle inhoud";
$strings["all_properties"] = "Alle eigenschappen";
$strings["no_results_search"] = "Geen data gevonden";
$strings["no_results_report"] = "Het rapport is leeg.";
$strings["schema_date"] = "JJJJ/MM/DD";
$strings["hours"] = "uren";
$strings["choice"] = "Keuze";
$strings["missing_file"] = "Bestand kon niet gevonden worden !"; // of ontbreekt of kon niet gevonden worden...
$strings["project_site_deleted"] = "De project pagina is succesvol verwijderd.";
$strings["add_user_project_site"] = "De gebruiker heeft nu toegang tot de project pagina.";
$strings["remove_user_project_site"] = "De gebruikersrechten zijn verwijderd.";
$strings["add_project_site_success"] = "De toevoeging tot de projectpagina is geslaagd.";
$strings["remove_project_site_success"] = "De verwijdering van de project pagina is geslaagd.";
$strings["add_file_success"] = "1 Inhoudsitem gekoppeld.";
$strings["delete_file_success"] = "Ontkoppeling geslaagd.";
$strings["update_comment_file"] = "Het bestandscommentaar is met succes aangepast.";
$strings["session_false"] = "Sessie fout";
$strings["logs"] = "Log's";
$strings["logout_time"] = "Automatisch Afmelden";
$strings["noti_foot1"] = "Deze berichtgeving is gemaakt door PhpCollab.";
$strings["noti_foot2"] = "Om uw PhpCollab pagina te bekijken, ga naar:";
$strings["noti_taskassignment1"] = "Nieuwe taak:";
$strings["noti_taskassignment2"] = "Aan u is een taak toegewezen";
$strings["noti_moreinfo"] = "Ga voor meer informatie naar:";
$strings["noti_prioritytaskchange1"] = "Taak prioriteit gewijzigd:";
$strings["noti_prioritytaskchange2"] = "De prioriteit van de volgende taak is gewijzigd:";
$strings["noti_statustaskchange1"] = "Taak status gewijzigd:";
$strings["noti_statustaskchange2"] = "De status van de volgende taak is gewijzigd:";
$strings["login_username"] = "U moet een gebruikersnaam invullen.";
$strings["login_password"] = "U moet een wachtwoord invullen.";
$strings["login_clientuser"] = "Dit is een Cliënt profielnaam. U kunt geen toegang krijgen tot PhpCollab met een Cliënt gebruikersnaam.";
$strings["user_already_exists"] = "Er is al een gebruiker met deze naam, kies een andere.";
$strings["noti_duedatetaskchange1"] = "Taak vervaldatum is gewijzigd:";
$strings["noti_duedatetaskchange2"] = "De vervaldatum van de volgende taak is gewijzigd:";
$strings["company"] = "Bedrijf";
$strings["show_all"] = "Bekijk alles";
$strings["information"] = "Informatie";
$strings["delete_message"] = "Verwijder dit bericht";
$strings["project_team"] = "Project groep";
$strings["document_list"] = "Lijst van documenten";
$strings["bulletin_board"] = "Forum";
$strings["bulletin_board_topic"] = "Forum onderwerp";
$strings["create_topic"] = "Start een nieuw onderwerp";
$strings["topic_form"] = "Onderwerp details";
$strings["enter_message"] = "Type hier het bericht";
$strings["upload_file"] = "Upload een bestand";
$strings["upload_form"] = "Upload details";
$strings["upload"] = "Uploaden";
$strings["document"] = "Document";
$strings["approval_comments"] = "Goedkeuring commentaar";
$strings["client_tasks"] = "Taken cliënt";
$strings["team_tasks"] = "Taken projectgroep";
$strings["team_member_details"] = "Details deelnemer projectgroep";
$strings["client_task_details"] = "Details taken cliënt";
$strings["team_task_details"] = "Details taken projectgroep";
$strings["language"] = "Taal";
$strings["welcome"] = "Welkom";
$strings["your_projectsite"] = "op uw projectsite";
$strings["contact_projectsite"] = "Wanneer u vragen heeft over het extranet of de informatie die u hier vindt kunt u contact opnemen met de beheerder(s)";
$strings["company_details"] = "Details bedrijf";
$strings["database"] = "Backup en herstellen database";
$strings["company_info"] = "Bewerk de bedrijfsinformatie";
$strings["create_projectsite"] = "Start een projectsite";
$strings["projectsite_url"] = "Projectsite URL";
$strings["design_template"] = "Ontwerp template"; //term of gewoon ontwerp
$strings["preview_design_template"] = "Bekijk template";
$strings["delete_projectsite"] = "Verwijder Projectsite";
$strings["add_file"] = "Voeg bestand toe";
$strings["linked_content"] = "Bijbehorende informatie";
$strings["edit_file"] = "Bewerk details bestand";
$strings["permitted_client"] = "Toegelaten gebruikers cliënt";
$strings["grant_client"] = "Bekijken van de projectsite toestaan";
$strings["add_client_user"] = "Toevoegen gebruiker cliënt";
$strings["edit_client_user"] = "Bewerk gebruiker cliënt";
$strings["client_user"] = "Gebruiker Cliënt";
$strings["client_change_status"] = "Wijzig uw status wanneer uw taak is volbracht";

$strings["project_status"] = "Project status";
$strings["view_projectsite"] = "Bekijk project pagina";
$strings["enter_login"] = "Geef uw gebruikersnaam op om een nieuw wachtwoord te ontvangen";
$strings["send"] = "Verstuur";
$strings["no_login"] = "Gebruikersnaam niet gevonden";
$strings["email_pwd"] = "Wachtwoord verstuurd";
$strings["no_email"] = "Gebruiker heeft geen e-mail adres";
$strings["forgot_pwd"] = "Wachtwoord vergeten";
$strings["project_owner"] = "U kunt alleen uw eigen projecten aanpassen.";
$strings["connected"] = "Verbonden";
$strings["session"] = "Sessie";
$strings["last_visit"] = "Laatste bezoek";
$strings["compteur"] = "Aantal"; //was Count
$strings["ip"] = "Ip";
$strings["task_owner"] = "U bent geen lid van deze projectgroep";
$strings["export"] = "Exporteren";
$strings["reassignment_clientuser"] = "Taak opnieuw toewijzen";
$strings["organization_already_exists"] = "Deze naam is al in gebruik. Probeer een andere naam.";
$strings["blank_organization_field"] = "Een bedrijfsnaam is verplicht.";
$strings["blank_fields"] = "verplichte velden";
$strings["projectsite_login_fails"] = "De combinatie van deze gebruikersnaam met dit wachtwoord is onbekend.";
$strings["start_date"] = "Begin datum";
$strings["completion"] = "Beëindiging";
$strings["update_available"] = "Er is een update beschikbaar!";
$strings["version_current"] = "U gebruikt momenteel versie";
$strings["version_latest"] = "De laatste versie is";
$strings["sourceforge_link"] = "Ga naar de project pagina op Sourceforge";
$strings["demo_mode"] = "Demo modus. Actie is niet toegestaan.";
$strings["setup_erase"] = "Verwijder het bestand setup.php!!";
$strings["no_file"] = "Geen bestand geselecteerd";
$strings["exceed_size"] = "Overschrijd maximale bestands grootte";
$strings["no_php"] = "Php bestand niet toegestaan";
$strings["approval_date"] = "Goedkeuringsdatum";
$strings["approver"] = "Goedgekeurd door";
$strings["error_database"] = "Kan niet verbinden met de database";
$strings["error_server"] = "Kan niet verbinden met de server";
$strings["version_control"] = "Versie beheer";
$strings["vc_status"] = "Status";
$strings["vc_last_in"] = "Datum laatste aanpassing";
$strings["ifa_comments"] = "Goedkeurings commentaar";
$strings["ifa_command"] = "Verander goedkeuring status";
$strings["vc_version"] = "Versie";
$strings["ifc_revisions"] = "Beoordeling van collega";
$strings["ifc_revision_of"] = "Beoordeling van versie";
$strings["ifc_add_revision"] = "Voeg beoordeling toe";
$strings["ifc_update_file"] = "Update bestand";
$strings["ifc_last_date"] = "Datum laatste aanpassing";
$strings["ifc_version_history"] = "Versie geschiedenis";
$strings["ifc_delete_file"] = "Verwijder bestand en alle onderliggende versies en beoordelingen";
$strings["ifc_delete_version"] = "Verwijder geselecteerde versie";
$strings["ifc_delete_review"] = "Verwijder geselecteerde beoordeling";
$strings["ifc_no_revisions"] = "Er zijn momenteel geen revisies van dit document";
$strings["unlink_files"] = "Ontkoppel bestanden";
$strings["remove_team"] = "Verwijder groeps leden";
$strings["remove_team_info"] = "Deze gebruikers verwijderen uit de projectgroep?";
$strings["remove_team_client"] = "Verwijder recht om de Project Pagina te bekijken";
$strings["note"] = "Notitie";
$strings["notes"] = "Notities";
$strings["subject"] = "Onderwerp";
$strings["delete_note"] = "Verwijder ingevoerde notities";
$strings["add_note"] = "Voeg notitie toe";
$strings["edit_note"] = "Bewerk notitie";
$strings["version_increm"] = "Selecteer de te gebruiken versie verandering:";
$strings["url_dev"] = "URL ontwikkelingssite";
$strings["url_prod"] = "URL uiteindelijke site";
$strings["note_owner"] = "Je kunt alleen uw eigen notities aanpassen.";
$strings["alpha_only"] = "Alleen alpha-numerieke karakters voor login";
$strings["edit_notifications"] = "Pas e-mail notificaties aan";
$strings["edit_notifications_info"] = "Selecteer de gebeurtenissen waarvoor u een e-mail wenst te ontvangen.";
$strings["select_deselect"] = "Selecteer/Deselecteer alles";
$strings["noti_addprojectteam1"] = "Toegevoegd aan projectgroep :";
$strings["noti_addprojectteam2"] = "U bent toegevoegd aan de projectgroep van :";
$strings["noti_removeprojectteam1"] = "Verwijderd uit de projectgroup :";
$strings["noti_removeprojectteam2"] = "U bent verwijderd uit de projectgroep van :";
$strings["noti_newpost1"] = "Nieuwe post :";
$strings["noti_newpost2"] = "Een post is toegevoegd aan de volgende discussie :";
$strings["edit_noti_taskassignment"] = "Ik heb een nieuwe taak toebedeeld gekregen.";
$strings["edit_noti_statustaskchange"] = "De status van één van mijn taken veranderd.";
$strings["edit_noti_prioritytaskchange"] = "De prioriteit van één van mijn taken veranderd.";
$strings["edit_noti_duedatetaskchange"] = "De eind datum van één van mijn taken veranderd.";
$strings["edit_noti_addprojectteam"] = "Ik word toegevoegd aan een projectgroep.";
$strings["edit_noti_removeprojectteam"] = "Ik word verwijderd uit een projectgroep";
$strings["edit_noti_newpost"] = "Een nieuwe post word gedaan in een discussie";
$strings["add_optional"] = "Plaats een optionele";
$strings["assignment_comment_info"] = "Voeg commentaar toe over de toebedeling van deze taak";
$strings["my_notes"] = "Mijn notities";
$strings["edit_settings"] = "Wijzig instellingen";
$strings["max_upload"] = "Maximale bestands grootte";
$strings["project_folder_size"] = "Project map grootte";
$strings["calendar"] = "Kalender";
$strings["date_start"] = "Begin datum";
$strings["date_end"] = "Eind datum";
$strings["time_start"] = "Begin tijd";
$strings["time_end"] = "Eind tijd";
$strings["calendar_reminder"] = "Herinnering";
$strings["shortname"] = "Korte naam";
$strings["calendar_recurring"] = "Gebeurtenis herhaalt zich elke week op deze dag";
$strings["edit_database"] = "Wijzig database";
$strings["noti_newtopic1"] = "Nieuwe discussie :";
$strings["noti_newtopic2"] = "Er is een nieuwe discussie toegevoegd aan het volgende project :";
$strings["edit_noti_newtopic"] = "Er is een nieuw discussie onderwerp toegevoegd.";
$strings["today"] = "Vandaag";
$strings["previous"] = "Vorige";
$strings["next"] = "Volgende";
$strings["help"] = "Help";
$strings["complete_date"] = "Eind datum";
$strings["scope_creep"] = "Scope creep";
$strings["days"] = "Dagen";
$strings["logo"] = "Logo";
$strings["remember_password"] = "Onthoud wachtwoord";
$strings["client_add_task_note"] = "Opmerking: De taak is toegevoegd in de database, het word alleen zichtbaar indien het is toebedeeld aan een groepslid!";
$strings["noti_clientaddtask1"] = "Taak toegevoegd door cliënt :";
$strings["noti_clientaddtask2"] = "Een nieuwe taak is toegevoegd door een cliënt aan het volgende project :";
$strings["phase"] = "Fase";
$strings["phases"] = "Fases";
$strings["phase_id"] = "Fase ID";
$strings["phase_status_open"] = "Open";
$strings["phase_status_complete"] = "Klaar";
$strings["phase_status_not_started"] = "Niet begonnen";
$strings["phase_status_not_suspended"] = "Uitgesteld";
$strings["current_phase"] = "Actieve fase(s)";
$strings["total_tasks"] = "Alle taken";
$strings["uncomplete_tasks"] = "Onvolledige taken";
$strings["no_current_phase"] = "Er zijn momenteel geen actieve fases";
$strings["true"] = "Waar";
$strings["false"] = "Onwaar";
$strings["enable_phases"] = "Pas fases toe";
$strings["phase_enabled"] = "Fases toegepast";
$strings["order"] = "Volgorde";
$strings["options"] = "Opties";
$strings["support"] = "Support";
$strings["support_request"] = "Ondersteunings verzoek";
$strings["support_requests"] = "Ondersteunings verzoeken";
$strings["support_id"] = "Verzoek ID";
$strings["my_support_request"] = "Mijn ondersteunings verzoeken";
$strings["introduction"] = "Introductie";
$strings["submit"] = "Toevoegen";
$strings["support_management"] = "Ondersteunings beheer";
$strings["date_open"] = "Datum geopend";
$strings["date_close"] = "Datum gesloten";
$strings["add_support_request"] = "Voeg ondersteunings verzoek toe";
$strings["add_support_response"] = "Voeg ondersteunings antwoord toe";
$strings["respond"] = "Antwoorden";
$strings["delete_support_request"] = "Ondersteunings verzoek verwijderd";
$strings["delete_request"] = "Verwijder ondersteunings verzoek";
$strings["delete_support_post"] = "Verwijder ondersteunings post";
$strings["new_requests"] = "Nieuwe verzoeken";
$strings["open_requests"] = "Open verzoeken";
$strings["closed_requests"] = "Beëindigde verzoeken";
$strings["manage_new_requests"] = "Beheer nieuwe verzoeken";
$strings["manage_open_requests"] = "Beheer open verzoeken";
$strings["manage_closed_requests"] = "Beheer beëindigde verzoeken";
$strings["responses"] = "Antwoorden";
$strings["edit_status"] = "Wijzig status";
$strings["noti_support_request_new2"] = "U heeft een nieuw ondersteunings verzoek ingediend inzake : ";
$strings["noti_support_post2"] = "Er is een nieuw antwoord toegevoegd aan uw ondersteunings verzoek. Bekijk hieronder de details.";
$strings["noti_support_status2"] = "Uw ondersteunings verzoek is vernieuwd. Bekijk hieronder de details.";
$strings["noti_support_team_new2"] = "Er is een nieuw ondersteunings verzoek toegevoegd aan project : ";
//2.0
$strings["delete_subtasks"] = "Subtaken verwijderen";
$strings["add_subtask"] = "Subtaken toevoegen";
$strings["edit_subtask"] = "Subtaken wijzigen";
$strings["subtask"] = "Subtaak";
$strings["subtasks"] = "Subtaken";
$strings["show_details"] = "Toon dtails";
$strings["updates_task"] = "Historie taken";
$strings["updates_subtask"] = "Historie taak";
//2.1
$strings["go_projects_site"] = "Projectssite";
$strings["bookmark"] = "Bookmark";
$strings["bookmarks"] = "Bookmarks";
$strings["bookmark_category"] = "Categorie";
$strings["bookmark_category_new"] = "Nieuwe categorie";
$strings["bookmarks_all"] = "Alles";
$strings["bookmarks_my"] = "Mijn bookmarks";
$strings["my"] = "Mijn";
$strings["bookmarks_private"] = "Privé";
$strings["shared"] = "Gedeeld";
$strings["private"] = "Privé";
$strings["add_bookmark"] = "Voeg een bookmark toe";
$strings["edit_bookmark"] = "Bewerk een bookmark";
$strings["delete_bookmarks"] = "Bookmarks verwijderen";
$strings["team_subtask_details"] = "Details subtaken team";
$strings["client_subtask_details"] = "Details subtaken cliënt";
$strings["client_change_status_subtask"] = "Verander uw status wanneer uw subtaak uitgevoerd is.";
$strings["disabled_permissions"] = "Disabled account";
$strings["user_timezone"] = "Tijdzone (GMT)";
//2.2
$strings["project_manager_administrator_permissions"] = "Project Manager Administrator";
$strings["bug"] = "Bug Tracking";
//2.3
$strings["report"] = "Report";
$strings["license"] = "License";
//2.4
$strings["settings_notwritable"] = "Settings.php file is not writable";
