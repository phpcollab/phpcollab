# INSTALL

## Install
- Extract in one folder, "phpcollab" for example
- Unix/Linux: rename "includes/settings_blank.php" to "includes/settings.php"

- Change permissions for the following items:
    > Unix/Linux: chmod 777 + chown apache 

  - `includes/settings.php` file
  - `files` folder
  - `logs` folder
  - `logos_clients` folder

- Create a new MySql, PostgreSQL or Sql Server database "phpcollab" or use existing database
- Start at installation/setup.php
- Follow the screens, setting all parameters
- Delete installation/setup.php file after successful install
- Login at index.php

> Advanced users can edit generated file includes/settings.php

### Update (starting from version 1.0) - (deprecated)
- Since 2.2, copy your inc/settings to includes/settings.php
- Save your includes/settings.php and backup your database
- Replace all files in your PhpCollab folder by new files in the last package you download
- Use Update in admin page
1. edit settings (to regenerate new settings.php with eventual new parameters)
2. edit database (to dump new commands according to precedent version)


### Modules - deprecated
- Ssl authentication with e-mail certificate (/docs/modules/ssl.zip)
- Mantis bug tracking system integration (/docs/modules/mantis.zip)
