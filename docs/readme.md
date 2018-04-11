README

# Update (starting from version 1.0)
- Since 2.2, copy your inc/settings to includes/settings.php
- Save your includes/settings.php and backup your database
- Replace all files in your PhpCollab folder by new files in the last package you download
- Use Update in admin page
1. edit settings (to regenerate new settings.php with eventual new parameters)
2. edit database (to dump new commands according to precedent version)
- If your version is lower than 1.8, launch script installation/fixFilesSize.php (to remove size units in database) 

# Description
Groupware module. Manage web projects with team collaboration, users management, tasks and projects tracking, files approval tracking, project sites clients access, customer relationship management (Php / Mysql, PostgreSQL or Sql Server).

# Requirements
- Php 4.1.x or superior (4.2.x or superior recommended)
- register_globals not-dependent (php >= 4.1.0)
- register_globals dependent (php < 4.1.0)
- magic_quotes_gpc not-dependent
- session support enabled
- file_uploads On
- MySql, PostgreSQL or Sql Server

# Website (forums, news, ...)
http://www.phpcollab.com

# Developed on
- Windows XP Professional / IIS 5.1 / Php 4.2.2 / MySql 3.23.49
- Windows 2000 Professional / IIS 5.0(:80) - Apache 1.3.27(:82) - Apache 2.0.43(:83) / Php 4.3.0 / MySql 3.23.53 - Sql Server 7
- Windows 2000 Server / IIS 5.0(:80) - Apache 2.0.35(:82) / Php 4.2.2 / MySql 3.23.49 - Sql Server 7
- Linux / Apache 1.3.26 / Php 4.3.0 safe-mode / MySql 3.23.53
- Linux / Apache 1.3.20 / Php 4.1.2 / MySql 3.23.37
- FreeBSD / Apache 1.3.27 / Php 4.2.3 / PostgreSQL 7.1.3

# External scripts integrated
- phpMyAdmin dump stuff to backup/restore MySql
	http://phpwizard.net/projects/phpMyAdmin
- phpPgAdmin dump stuff to backup/restore PostgreSQL
	http://phppgadmin.sourceforge.net
- JpGraph to generate Gantt graph with tasks (due date, progress...)
	http://www.aditus.nu/jpgraph
- PHP vCard class v2.0
	www.bitfolge.de/en
- Mantis bugtracking system
	http://mantisbt.sourceforge.net
- The coolest DHTML calendar widget
	http://students.infoiasi.ro/~mishoo/site/calendar.epl
- OverLIB: popup information boxes (tooltips)
	http://www.bosrup.com/web/overlib
- phpmailer email transfer class
	http://phpmailer.sourceforge.net
- htmlArea: WYSIWYG js html editor
	http://www.interactivetools.com/
- R&OS PDF Class: pdf render class
	http://www.ros.co.nz/pdf

# Developers 2.5
- Stéphane Dion (Software Author, Developer)
- Francesco Fullone (Lead Developer)
- Michelle Feldman (Project manager)
- Jennifer Brola (Developer)
- Ed Kelly (Developer)

# Contributors 2.5
- jayherrick (postgres 7.3.x patch)
- Mariano Barcia (Mantis update and patch)
- Martin Lanser 

# Old Developers
- Luca Mercuri (Teton project site template and setup script)
- Tolga Yalcinkaya (login encryption method, browse cvs)
- Cameron Lee (version control, phases, support requests)
- Rene Kluwen (ssl authentification with e-mail certificate)
- Chris Kacerguis (Ldap authentification)
- Henning Saul (htaccess protection on files directory and script to view/download files)
- Gopal Patwa (Mantis bugtrakcing system integration)

# Translators
- English: Stéphane Dion
- Italian: Luca Mercuri, Francesco Fullone
- Spanish: Felipe Jaramillo, Pep Pujadó Mateo, Jesus Corotero
- French: Stéphane Dion
- Portuguese: Carlos Figueiredo
- Norwegian: Vladimir Petrov, Wiggo Eriksen
- Danish: Mark Petersen
- Dutch: Hendrik Bijlsma, Erwin Wondergem, Dave Liefbroer
- German: Jochen Bünnagel, Wolfram Lamm, Andreas Nagler
- Chinese simplified: Patrick C. Wang, deepin
- Ukrainian: Roman Zinchenko
- Polish: Dariusz Kowalski
- Indonesian: Rachman Chavik
- Russian: Andrey 'Doc' Ponomarev
- Azerbaijani: Metin Amiroff
- Korean: Andy Choi
- Chinese traditional: Fu-Yuan Zheng
- Catalan: Sergi Nadal
- Brazilian Portuguese: Herbert G. Fischer
- Estonian: Priit Ballot
- Bulgarian: Veselin Malezanov, Yassen Yotov
- Romanian: Adi
- Hungarian: ct
- Czech (iso): Pavel Dostal
- Czech (win1250): Pavel Dostal
- Icelandic: Jónas Sigurðsson
- Slovak (win1250): Marek Tomèík
- Turkish: Irfan Uygur
- Latvian: Krisjanis Berzins

# Design
- theme "default": William from Styrofirm.com
- theme "xp-blue": Francesco Fullone
- icons in "default" theme: Thomas Dubus
- icons in "ordinarylife" theme: James Buckley from Ordinary-Life.net
- graphics in project site: James Buckley from Ordinary-Life.net

# Documentation
- administrator manual: Michelle Feldman
- user manual: Michelle Feldman
- installation guide: Michelle Feldman
