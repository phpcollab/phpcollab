<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_fr.php

//translator(s): 
$help["setup_mkdirMethod"] = "Si le safe-mode est actif (on), vous avez besoin de définir un compte FTP pour pouvoir créer des dossiers avec le gestionnaire de fichiers.";
$help["setup_notifications"] = "Notification électronique aux utilisateurs (affectation de tâches, nouvelle publication, changement de tâches...)<br/>Un SMPT/SENDMAIL valide est nécessaire.";
$help["setup_forcedlogin"] = "Si faux, n'autorise pas les liens externes avec l'identifiant et le mot de passe dans l'URL.";
$help["setup_langdefault"] = "Définit le langage par défaut du formulaire de connexion.";
$help["setup_myprefix"] = "Définir cette valeur si vous avez des tables de même dans dans la base existante.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Laisser blanc pour ne pas utiliser de préfixe.";
$help["setup_loginmethod"] = "Méthode de sauvegarde des mots de passe dans la base de données.<br/>Utilisez to &quot;Crypt&quot; pour faire fonctionner les authentifications CVS et htaccess (si le support CVS et/ou htaccess sont activés).";
$help["admin_update"] = "Respectez l'ordre des étapes pour mettre à jour votre version.<br/>1. Editer le fichier de paramétrage (pour compléter les nouveaux éléments)<br/>2. Editer la base de donnée (mise à jour en fonction de votre version précédente)";
$help["task_scope_creep"] = "Différence en jours entre la date due et la date d'achévement (en gras si positive)";
$help["max_file_size"] = "Taille maximale d'envoie d'un fichier";
$help["project_disk_space"] = "Taille totale des fichiers du projet";
$help["project_scope_creep"] = "Différence en nombre de jours entre la date due et la date de fin effective (en gras si positif). Total pour toutes les tâches";
$help["mycompany_logo"] = "Attaché un logo pour votre societé. Apparaît dans l'en-tête, à la place du titre du site";
$help["calendar_shortname"] = "Label visible dans la vue mensuelle du calendrier. Obligatoire";
$help["user_autologout"] = "Durée en secondes d'inactivité permise avant déconnection automatique. 0 pour désactivé";
$help["user_timezone"] = "Définissez votre fuseau horaire";
//2.4
$help["setup_clientsfilter"] = "Filtre pour voir seulement les clients d'un utilisateur";
$help["setup_projectsfilter"] = "Filtre pour voir seulement les projets dont l'utilisateur fait parti";
//2.5
$help["setup_notificationMethod"] = "Définissez la méthode de notification électronique : avec les fonctions internes PHP (il est nécessaire d'avoir un serveur SMTP configuré pour) ou avec un serveur SMTP spécifique.";
//2.5b3
$help["newsdesk_links"] = "Utiliser le point-virgule pour ajouter plusieurs liens";
?>