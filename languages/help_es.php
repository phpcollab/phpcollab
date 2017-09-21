<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../languages/help_es.php

//translator(s):
$help["setup_mkdirMethod"] = "Si safe-mode esta activado, usted necesita crear una cuenta FTP que esté autorizada para crear carpetas con administración de archivos.";
$help["setup_notifications"] = "Notificaciones vía correo electrónico (Asignación de tareas, nuevos temas, cambios en tareas...)<br/>Se requiere smtp/sendmail valido.";
$help["setup_forcedlogin"] = "Si es falso, deshabilita que se autorice la entrada desde un url que contenga el login/password incluido.";
$help["setup_langdefault"] = "Escoja el idioma que se seleccionará como predeterminado en el momento de logearse, o deje en blanco para que sea autodetectado por el navegador.";
$help["setup_myprefix"] = "Ingrese este valor si usted tiene tablas en la base de datos con el mismo nombre.<br/><br/>assignments<br/>bookmarks<br/>bookmarks_categories<br/>calendar<br/>files<br/>logs<br/>members<br/>notes<br/>notifications<br/>organizations<br/>phases<br/>posts<br/>projects<br/>reports<br/>sorting<br/>subtasks<br/>support_posts<br/>support_requests<br/>tasks<br/>teams<br/>topics<br/>updates<br/><br/>Deje en blanco si no quiere utilizar un prefijo.";
$help["setup_loginmethod"] = "Método para almacenar passwords en la base de datos.<br/>Seleccione &quot;Crypt&quot; si quiere que la autenticación por el método CVS y htaccess funcionen (Si autenticación y/o htaccess están activados).";
$help["admin_update"] = "Respetar estrictamente el orden indicado para actualizar su versión<br/>1. Edite sus preferencias (sustituya con los nuevos parámetros)<br/>2. Edite la base de datos (actualice de acuerdo con su versión anterior)";
$help["task_scope_creep"] = "Diferencia en días entre los la fecha de entrega y la fecha de completada (Negrilla si es positiva)";
$help["max_file_size"] = "Máximo peso de un archivo permitido para ser publicado";
$help["project_disk_space"] = "Tamaño total de los archivos publicados en el proyecto";
$help["project_scope_creep"] = "Diferencia en días entre los la fecha de entrega y la fecha de completada (Negrilla si es positiva). Total para todas las tareas.";
$help["mycompany_logo"] = "Publique el logo de su compañía. Aparece en el encabezado, en vez de el título en texto";
$help["calendar_shortname"] = "Título que aparece en la vista del calendario mensual. Obligatorio";
$help["user_autologout"] = "Tiempo, en segundos para ser desconetado del sistema si no hay actividad (Time Out). 0 para desactivar esta opción.";
$help["user_timezone"] = "Seleccione su zona de tiempo global (GMT)";
//2.4
$help["setup_clientsfilter"] = "Filtro :  Solo se veran los clientes conectados.";
$help["setup_projectsfilter"] = "Filtro : Los usuarios solo podran ver los proyectos a los que pertenezcan.";
//2.5
$help["setup_notificationMethod"] = "Selecciona el metodo para enviar las notificaciones por correo  : Con las funciones propias de php ( necesita tener php configurado para enviar correo) o  con un servidor SMTP externo";
//2.5 fullo
$help["newsdesk_links"] = "Para añadir multiples enlaces hagalo separandolos por comas";
