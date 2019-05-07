# INSTALL

## Update (starting from version 1.0)
- Since 2.2, copy your inc/settings to includes/settings.php
- Save your includes/settings.php and backup your database
- Replace all files in your PhpCollab folder by new files in the last package you download
- Use Update in admin page
1. edit settings (to regenerate new settings.php with eventual new parameters)
2. edit database (to dump new commands according to precedent version)

## Install (only for first install, not for update)
- Extract in one folder, "phpcollab" for example (with Xoops integration, extract as "phpcollab" in modules folder)
- Unix/Linux: rename "includes/settings_blank.php" to "includes/settings.php"
- Unix/Linux: chmod 777 + chown apache "includes/settings.php" file, "files" folder and "logos_clients" folder
- Create a new MySql, PostgreSQL or Sql Server database "phpcollab" or use existing database
- Start at installation/setup.php
- Set all parameters
- Delete installation/setup.php file after successfull install
- Login at index.php
- With Xoops integration, go to admin, edit settings and set Xoops integration to "true" and set full path to your Xoops folder
- Avanced users can edit generated file includes/settings.php

## Modules
- Ssl authentification with e-mail certificate (/docs/modules/ssl.zip)
- Mantis bugtracking sytem integration (/docs/modules/mantis.zip)
