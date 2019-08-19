# INSTALL

## Non-Developer Install (only for first install, not for update)
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

## Developer Install
1. Clone repo
2. cd into repo directory
3. run `composer install`
4. Setup web server to point to the repo directory
5. Launch site in browser


## Modules
- deprecated - Ssl authentification with e-mail certificate (/docs/modules/ssl.zip)
- deprecated -  Mantis bugtracking sytem integration (/docs/modules/mantis.zip)
