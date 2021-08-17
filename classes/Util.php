<?php
/**
 * User: mindblender
 * Date: 5/14/15
 * Time: 12:31 AM
 */

namespace phpCollab;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Util
 * @package phpCollab
 */
class Util
{
    protected static $strings;
    protected static $useLDAP;
    protected static $configLDAP;
    protected static $pass_g;
    protected static $mkdirMethod;
    protected static $ftpRoot;
    protected static $byteUnits;
    protected static $databaseType;
    protected static $gmtTimezone;
    protected static $lastId;

    /**
     * Util constructor.
     */
    public function __construct()
    {
        self::$strings = $GLOBALS['strings'];
        self::$useLDAP = $GLOBALS["useLDAP"];
        self::$configLDAP = $GLOBALS["configLDAP"];
        self::$pass_g = $GLOBALS["pass_g"];
        self::$mkdirMethod = $GLOBALS["mkdirMethod"];
        self::$ftpRoot = $GLOBALS["ftpRoot"];
        self::$byteUnits = $GLOBALS["byteUnits"];
        self::$databaseType = $GLOBALS["databaseType"];
        self::$gmtTimezone = $GLOBALS["gmtTimezone"];
        self::$lastId = $GLOBALS["lastId"];
    }

    /**
     * Checks to see if the passed in URL begins with http or not.  If it doesn't,
     * then it adds it.
     * @param string $url
     * @return string
     */
    public static function addHttp($url)
    {
        $url = trim($url);
        if (!empty($url)) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "https://" . $url;
            }
            return $url;
        }
        return '';
    }

    /**
     * Wrapper to make sure null strings display as 0 in sql queries
     * @param string $var An integer represented as a string
     * @return int
     **/
    public static function fixInt($var)
    {
        if ($var == '') {
            return 0;
        } else {
            return $var;
        }
    }

    // replace spec.chars , you can add rule

    /**
     * @param $return
     * @return mixed
     */
    public static function replaceSpecialCharacters($return)
    {
        return str_replace(
            ['"', '\'', '=', '$', '\\'],
            ['&quote', '&#039;', '&#61;', '&#36;', '&#92;'],
            $return
        );
    }

    /**
     * Calculate time to parse page (used with footer.php)
     * @access public
     **/
    public static function getMicroTime()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }

    /**
     * Redirect to specified url
     * @param string $url Path to redirect
     * @access public
     **/
    public static function headerFunction(string $url)
    {
        $response = new RedirectResponse($url);
        $response->send();
        exit();
    }

    /**
     * Automatic links
     * @param string $data Text to parse
     * @access public
     *
     * @return string|string[]|null
     */
    public static function autoLinks($data)
    {
        $newText = '';
        $lines = explode("\n", $data);
        foreach ($lines as $key => $line) {
            $line = preg_replace('/([ \t]|^)www\./', ' http://www.', $line);

            $line = preg_replace('/([ \t]|^)ftp\./', ' ftp://ftp.', $line);
            $line = preg_replace('|(http://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
            $line = preg_replace('|(https://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
            $line = preg_replace('|(ftp://[^ )\r\n]+)|', '<a href="$1" target="_blank">$1</a>', $line);
            $line = preg_replace('|([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))|',
                '<a href="mailto:$1">$1</a>', $line);

            if (empty($newText)) {
                $newText = $line;
            } else {
                $newText .= "\n$line";
            }
        }
        return $newText;
    }


    /**
     * @param $timestamp
     * @return int
     */
    public static function dayOfWeek($timestamp)
    {
        return intval(strftime("%w", $timestamp) + 1);
    }


    /**
     * Return number of day between 2 dates
     * @param string $date1 Date to compare
     * @param string $date2 Date to compare
     * @access public
     *
     * @return float
     */
    public static function diffDate(string $date1, string $date2): float
    {
        $year = substr("$date1", 0, 4);
        $month = substr("$date1", 5, 2);
        $day = substr("$date1", 8, 2);

        $year2 = substr("$date2", 0, 4);
        $month2 = substr("$date2", 5, 2);
        $day2 = substr("$date2", 8, 2);

        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        $timestamp2 = mktime(0, 0, 0, $month2, $day2, $year2);
        return floor(($timestamp - $timestamp2) / (3600 * 24));
    }

    /**
     * @param $formPassword
     * @param $storedPassword
     * @param string $loginMethod
     * @return bool
     */
    public static function passwordMatch($formPassword, $storedPassword, string $loginMethod = "crypt"): bool
    {
        switch (strtolower($loginMethod)) {
            case "md5":
                if (md5($formPassword) == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
            case "crypt":
                $salt = substr($storedPassword, 0, 2);
                if (crypt($formPassword, $salt) == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
            case "plain":
                if ($formPassword == $storedPassword) {
                    return true;
                } else {
                    return false;
                }
            default:
                return false;
        }
    }

    /**
     * Checks for password match using the globally specified login method
     * @param string $formUsername User name to test
     * @param string $formPassword User name password to test
     * @param string $storedPassword Password stored in database
     * @param string $loginMethod
     * @return bool
     * @access public
     *
     */
    public static function doesPasswordMatch($formUsername, $formPassword, $storedPassword, $loginMethod = "crypt")
    {
        if (self::$useLDAP == "true") {
            if ($formUsername == "admin") {
                return self::passwordMatch($formPassword, $storedPassword, $loginMethod);
            }
            $conn = ldap_connect(self::$configLDAP[ldapserver]);
            $sr = ldap_search($conn, self::$configLDAP[searchroot], "uid=$formUsername");
            $info = ldap_get_entries($conn, $sr);
            $user_dn = $info[0]["dn"];
            try {
                return !ldap_bind($conn, $user_dn, $formPassword) ? false : true;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            return self::passwordMatch($formPassword, $storedPassword, $loginMethod);
        }
    }

    /**
     * Return a password using the globally specified method
     * @param string $newPassword Password to transfom
     * @param string $loginMethod
     * @return string
     * @access public
     *
     */
    public static function getPassword($newPassword, $loginMethod = "crypt")
    {
        if (empty($loginMethod)) {
            $loginMethod = "crypt";
        }

        switch ($loginMethod) {
            case "md5":
                return md5($newPassword);
            case "crypt":
                $salt = substr($newPassword, 0, 2);

                return crypt($newPassword, $salt);
            case "plain":
                return $newPassword;
        }
    }

    /**
     * Generate a random password
     * @param int $size Size of generated password
     * @param boolean $with_numbers Option to use numbers
     * @param boolean $with_tiny_letters Option to use tiny letters
     * @param boolean $with_capital_letters Option to use capital letters
     * @return string
     * @access public
     *
     */
    public static function passwordGenerator(
        $size = 8,
        $with_numbers = true,
        $with_tiny_letters = true,
        $with_capital_letters = true
    ) {
        self::$pass_g = "";
        $sizeof_lchar = 0;
        $letter = "";
        $letter_tiny = "abcdefghijklmnopqrstuvwxyz";
        $letter_capital = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $letter_number = "0123456789";

        if ($with_tiny_letters === true) {
            $sizeof_lchar += 26;
            if (isset($letter)) {
                $letter .= $letter_tiny;
            } else {
                $letter = $letter_tiny;
            }
        }

        if ($with_capital_letters === true) {
            $sizeof_lchar += 26;
            if (isset($letter)) {
                $letter .= $letter_capital;
            } else {
                $letter = $letter_capital;
            }
        }

        if ($with_numbers === true) {
            $sizeof_lchar += 10;
            if (isset($letter)) {
                $letter .= $letter_number;
            } else {
                $letter = $letter_number;
            }
        }

        if ($sizeof_lchar > 0) {
            srand((double)microtime() * date("YmdGis"));
            for ($cnt = 0; $cnt < $size; $cnt++) {
                $char_select = rand(0, $sizeof_lchar - 1);
                self::$pass_g .= $letter[$char_select];
            }
        }

        return self::$pass_g;
    }

    /**
     * Move a file in a new destination
     * @param string $source Current path of file
     * @param string $dest New path of file
     * @access public
     **/
    public static function moveFile($source, $dest)
    {
        if (self::$mkdirMethod == "FTP") {
            $ftp = ftp_connect(FTPSERVER);
            ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
            ftp_rename($ftp, self::$ftpRoot . "/" . $source, self::$ftpRoot . "/" . $dest);
            ftp_quit($ftp);
        } else {
            copy("../" . $source, "../" . $dest);
        }
    }

    /**
     * Delete a file with a specified path
     * @param string $source Path of file
     * @access public
     **/
    public static function deleteFile($source)
    {
        if (self::$mkdirMethod == "FTP") {
            $ftp = ftp_connect(FTPSERVER);
            ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
            ftp_delete($ftp, self::$ftpRoot . "/" . $source);
            ftp_quit($ftp);
        } else {
            unlink("../" . $source);
        }
    }

    /**
     * Upload a file to a specified destination
     * @param string $path Path of original file
     * @param string $source Temp file
     * @param string $dest Destination path
     * @access public
     **/
    public static function uploadFile($path, $source, $dest)
    {
        $pathNew = "../{$path}";

        try {
            if (!file_exists($pathNew)) {
                # if there is no project dir first create it
                $path_info = pathinfo($path);
                if ($path != APP_ROOT . '/files/' . $path_info['basename']) {
                    Util::createDirectory($path_info['dirname']);
                    Util::createDirectory($path);
                } else {
                    Util::createDirectory($path);
                }
            }


            if ($GLOBALS["mkdirMethod"] == "FTP") {
                $ftp = ftp_connect(FTPSERVER);
                ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
                ftp_chdir($ftp, $pathNew);
                ftp_put($ftp, $dest, $source, FTP_BINARY);
                ftp_quit($ftp);
            } else {
                move_uploaded_file($source, APP_ROOT . "/" . $path . "/" . $dest);
            }
        } catch (Exception $exception) {
            xdebug_var_dump($exception->getMessage());
        }
    }

    /**
     * Folder creation
     * @param string $path Path to the new directory
     * @access public
     *
     * @return mixed
     */
    public static function createDirectory($path)
    {
        if ($GLOBALS["mkdirMethod"] == "FTP") {
            try {
                $pathNew = self::$ftpRoot . "/" . $path;
                $ftp = ftp_connect(FTPSERVER);
                ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
                ftp_mkdir($ftp, $pathNew);
                ftp_quit($ftp);
                return true;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if ($GLOBALS["mkdirMethod"] == "PHP") {
            try {
                if (!file_exists("../{$path}")) {
                    mkdir("../{$path}", 0755);
                    chmod("../{$path}", 0777);
                    return true;
                }

            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     * Folder recursive deletion
     * @param string $location Path of directory to delete
     * @access public
     *
     * @return mixed
     */
    public static function deleteDirectory($location)
    {
        if (is_dir($location)) {
            $all = opendir($location);
            while ($file = readdir($all)) {
                if (is_dir("$location/$file") && $file != ".." && $file != ".") {
                    self::deleteDirectory("$location/$file");
                    if (file_exists("$location/$file")) {
                        try {
                            return rmdir("$location/$file");
                        } catch (Exception $e) {
                            return $e->getMessage();
                        }
                    }
                    unset($file);
                } else {
                    if (!is_dir("$location/$file")) {
                        if (file_exists("$location/$file")) {
                            try {
                                return unlink("$location/$file");
                            } catch (Exception $e) {
                                return $e->getMessage();
                            }
                        }
                        unset($file);
                    }
                }
            }
            closedir($all);
            try {
                return rmdir($location);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            if (file_exists("$location")) {
                try {
                    return unlink("$location");
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            }
        }
    }

    /**
     * Return recursive folder size
     * @param string $path Path of directory to calculate
     * @param boolean $recursive Option to use recursivity
     * @access public
     *
     * @return int
     */
    public static function folderInfoSize($path, $recursive = true)
    {
        $result = 0;
        if (is_dir($path) || is_readable($path)) {
            $dir = opendir($path);
            while ($file = readdir($dir)) {
                if ($file != "." && $file != "..") {
                    try {
                        if (is_dir("$path$file/")) {
                            $result += $recursive ? Util::folderInfoSize("$path$file/") : 0;
                        } else {
                            $result += filesize("$path$file");
                        }
                        return $result;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                }
            }

            closedir($dir);

            return $result;
        }
    }

    /**
     * Return size converted with units (in the user language)
     * @access public
     *
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public static function convertSize($bytes, $decimals = 2)
    {
        $sizeLabels = array('bytes', 'KB', 'MB', 'GB');
        if (empty($bytes)) {
            return "0 " . $sizeLabels[0];
        } else {
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $sizeLabels[$factor];
        }
    }

    /**
     * Return file size
     * @param string $file File used
     * @return int
     * @access public
     *
     */
    public static function fileInfoSize(string $file)
    {
        return filesize($file);
    }

    /**
     * Read the content of a file
     * @param string $file File used
     * @return bool|string
     * @access public
     *
     */
    public static function getFileContents(string $file)
    {
        $content = '';

        if (!file_exists($file)) {
            echo "File does not exist : " . $file;

            return false;
        }

        $fp = fopen($file, "r");

        if (!$fp) {
            echo "Unable to open file : " . $file;

            return false;
        }

        while (!feof($fp)) {
            $tmpline = fgets($fp, 4096);
            $content .= $tmpline;
        }

        fclose($fp);

        return $content;
    }

    /**
     * Displat date according to timezone (if timezone enabled)
     * @param string $storedDate Date stored in database
     * @param string $gmtUser User timezone
     * @access public
     *
     * @return false|string
     */
    public static function createDate($storedDate, $gmtUser)
    {
        if (self::$gmtTimezone == "true") {
            if ($storedDate != "") {
                $extractHour = substr("$storedDate", 11, 2);
                $extractMinute = substr("$storedDate", 14, 2);
                $extractYear = substr("$storedDate", 0, 4);
                $extractMonth = substr("$storedDate", 5, 2);
                $extractDay = substr("$storedDate", 8, 2);

                return date("Y-m-d H:i",
                    mktime($extractHour + $gmtUser, $extractMinute, 0, $extractMonth, $extractDay, $extractYear));
            }
        } else {
            return $storedDate;
        }
    }

    /**
     * Convert insert data value in form
     * @param string $data Data to convert
     * @access public
     *
     * @return mixed|string
     */
    public static function convertData($data)
    {
        if (self::$databaseType == "sqlserver") {
            $data = str_replace('"', '&quot;', $data);
            $data = str_replace("'", '&#39;', $data);
            $data = str_replace('<', '&lt;', $data);
            $data = str_replace('>', '&gt;', $data);
            $data = stripslashes($data);

            return ($data);
        } elseif (get_magic_quotes_gpc() == 1) {
            $data = str_replace('"', '&quot;', $data);
            $data = str_replace('<', '&lt;', $data);
            $data = str_replace('>', '&gt;', $data);
            $data = str_replace("'", '&#39;', $data);

            return ($data);
        } else {
            $data = str_replace('"', '&quot;', $data);
            $data = str_replace('<', '&lt;', $data);
            $data = str_replace('>', '&gt;', $data);
            $data = str_replace("'", '&#39;', $data);
            $data = addslashes($data);

            return ($data);
        }

    }

    /**
     * @param $projectDetail
     * @param Container $container
     * @return mixed
     *
     * recompute number of completed tasks of the project
     * Do it only if the project name contains [ / ]
     * list tasks of the same project and count the number of completed
     *
     * This gets a count of completed tasks and appends it to the project name
     * ex:
     * "Some Awesome Project" becomes "Some Awesome Project [ completed # / total # ]"
     *
     * I don't think this has been working properly for awhile.
     *
     */
    public static function projectComputeCompletion($projectDetail, Container $container)
    {
        $tableProject = $GLOBALS['tableCollab']["projects"];
        $prj_name = $projectDetail['pro_name'];

        preg_match("/\[([0-9 ]*\/[0-9 ]*)\]/", $prj_name, $findit);

        if ($findit[1] != "") {
            $prj_id = $projectDetail['pro_id'];
            $tasks = $container->getTasksLoader();

            $taskDetails = $tasks->getTaskById($prj_id);

            $tasksNumb = count($taskDetails['tas_id']);

            $tasksCompleted = 0;

            foreach ($taskDetails['tas_status'] as $stat) {
                if ($stat == 1) {
                    $tasksCompleted++;
                }
            }

            $prj_name = preg_replace("/\[[0-9 ]*/[0-9 ]*]/", "[ $tasksCompleted / $tasksNumb ]", $prj_name);
            $tmpquery5 = "UPDATE {$tableProject} SET name=:project_name WHERE id = :project_id";

            $dbParams = [];
            $dbParams['project_name'] = $prj_name;
            $dbParams['project_id'] = $prj_id;

            Util::newConnectSql($tmpquery5, $dbParams);
            unset($dbParams);
        }

        return $prj_name;
    }

    /**
     * check a file name and remove backslash and spaces
     * this function remove also the file path if IE is used for upload
     * @param string $name the name of the file
     * @return mixed|string
     */
    public static function checkFileName($name = '')
    {

        $name = str_replace('\\', '/', $name);
        $name = str_replace(" ", "_", $name);
        $name = str_replace("'", "", $name);

        return basename($name);
    }

    /**
     * @return string
     */
    public static function doubleDash()
    {
        return '<span style="color: #ccc;">--</span>';
    }

    /**
     * @param $value
     * @return string
     */
    public static function isBlank($value = null)
    {
        if (empty($value)) {
            return self::doubleDash();
        } else {
            return $value;
        }
    }

    /**
     * @param $number
     * @return string
     */
    public static function formatFloat($number)
    {
        if (strlen($number) == 1) {
            return sprintf("%0.1f", $number);
        } else {
            return sprintf("%0.2f", $number);
        }
    }

    /**
     * @param string $path
     * @param string $navItem
     * @return string
     */
    public static function setNavActive(string $path, string $navItem): string
    {
        return preg_match("/^\/". $navItem ."\//i", $path) ? 'active' : '';
    }
}
