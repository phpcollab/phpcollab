<?php
/**
 * Remove Files
 * @author David Bates (norman77)
 * @since 2008-11-20
 *
 * This file will attempt to remove the setup.php file.
 */

require_once '../includes/library.php';

if (file_exists("setup.php")) {
    @unlink("setup.php");
}

phpCollab\Util::headerFunction("../administration/admin.php");
