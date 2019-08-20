<?php
/**
 * User: mindblender
 * Date: 5/14/15
 * Time: 12:31 AM
 */

namespace phpCollab;

use phpCollab\Tasks\Tasks;
use \Exception;
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
    }

    /**
     * Checks to see if the passed in URL begins with http or not.  If it doesn't,
     * then it adds it.
     * @param string $url
     * @return string
     */
    public static function addHttp($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        return $url;
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
     * Return global variable
     * @param string $var Variable name
     * @param string $type Variable type (SERVER, POST, GET, SESSION, REQUEST, COOKIE)
     * @access public
     *
     * @return mixed
     */
    public static function returnGlobal($var, $type = null)
    {
        if ($type == "SERVER") {
            return Util::replaceSpecialCharacters($_SERVER[$var]);
        }
        if ($type == "POST") {
            return Util::replaceSpecialCharacters($_POST[$var]);
        }
        if ($type == "GET") {
            return Util::replaceSpecialCharacters($_GET[$var]);
        }
        if ($type == "SESSION") {
            return Util::replaceSpecialCharacters($_SESSION[$var]);
        }
        if ($type == "REQUEST") {
            return Util::replaceSpecialCharacters($_REQUEST[$var]);
        }
        if ($type == "COOKIE") {
            return Util::replaceSpecialCharacters($_COOKIE[$var]);
        }
        return '';
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
    public static function headerFunction($url)
    {
        $response = new RedirectResponse($url);
        $response->send();
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
    public static function diffDate($date1, $date2)
    {
        $an = substr("$date1", 0, 4);
        $mois = substr("$date1", 5, 2);
        $jour = substr("$date1", 8, 2);

        $an2 = substr("$date2", 0, 4);
        $mois2 = substr("$date2", 5, 2);
        $jour2 = substr("$date2", 8, 2);

        $timestamp = mktime(0, 0, 0, $mois, $jour, $an);
        $timestamp2 = mktime(0, 0, 0, $mois2, $jour2, $an2);
        $diff = floor(($timestamp - $timestamp2) / (3600 * 24));

        return $diff;
    }

    /**
     * @param $formPassword
     * @param $storedPassword
     * @param string $loginMethod
     * @return bool
     */
    public static function passwordMatch($formPassword, $storedPassword, $loginMethod = "crypt") {
        switch ($loginMethod) {
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
    public static function passwordGenerator($size = 8, $with_numbers = true, $with_tiny_letters = true, $with_capital_letters = true)
    {
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
            ftp_rename($ftp, self::$ftpRoot. "/" . $source, self::$ftpRoot . "/" . $dest);
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
        $pathNew = self::$ftpRoot . "/" . $path;

        if (!file_exists($pathNew)) {
            # if there is no project dir first create it
            $path_info = pathinfo($path);
            if ($path != 'files/' . $path_info['basename']) {
                Util::createDirectory($path_info['dirname']);
                Util::createDirectory($path);
            } else {
                Util::createDirectory($path);
            }
        }


        if (self::$mkdirMethod == "FTP") {
            $ftp = ftp_connect(FTPSERVER);
            ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
            ftp_chdir($ftp, $pathNew);
            ftp_put($ftp, $dest, $source, FTP_BINARY);
            ftp_quit($ftp);
        } else {
            move_uploaded_file($source, "../" . $path . "/" . $dest);
        }
    }

    /**
     * Folder creation
     * @param string $path Path to the new directory
     * @access public
     *
     * @return string
     */
    public static function createDirectory($path)
    {
        if (self::$mkdirMethod == "FTP") {
            $pathNew = self::$ftpRoot . "/" . $path;

            $ftp = ftp_connect(FTPSERVER);
            ftp_login($ftp, FTPLOGIN, FTPPASSWORD);
            ftp_mkdir($ftp, $pathNew);
            ftp_quit($ftp);
        }

        if (self::$mkdirMethod == "PHP") {
            try {
                mkdir("../$path", 0755);
                chmod("../$path", 0777);
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
     * @return string
     */
    public static function deleteDirectory($location)
    {
        if (is_dir($location)) {
            $all = opendir($location);
            while ($file = readdir($all)) {
                if (is_dir("$location/$file") && $file != ".." && $file != ".") {
                    \Util::deleteDirectory("$location/$file");
                    if (file_exists("$location/$file")) {
                        try {
                            rmdir("$location/$file");
                        } catch (Exception $e) {
                            return $e->getMessage();
                        }
                    }
                    unset($file);
                } else {
                    if (!is_dir("$location/$file")) {
                        if (file_exists("$location/$file")) {
                            try {
                                unlink("$location/$file");
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
                rmdir($location);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            if (file_exists("$location")) {
                try {
                    unlink("$location");
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
     * @param string $result Result to convert
     * @access public
     *
     * @return string
     */
    public static function convertSize($result)
    {
        if ($result >= 1073741824) {
            $result = round($result / 1073741824 * 100) / 100 . " " . self::$byteUnits[3];
        } else {
            if ($result >= 1048576) {
                $result = round($result / 1048576 * 100) / 100 . " " . self::$byteUnits[2];
            } else {
                if ($result >= 1024) {
                    $result = round($result / 1024 * 100) / 100 . " " . self::$byteUnits[1];
                } else {
                    $result = $result . " " . self::$byteUnits[0];
                }
            }
        }

        if ($result == 0) {
            $result = self::doubleDash();
        }

        return $result;
    }

    /**
     * Return file size
     * @param string $file File used
     * @access public
     *
     * @return int
     */
    public static function fileInfoSize($file)
    {
        $fileSize = filesize($file);

        return $fileSize;
    }

    /**
     * Return file dimensions
     * @param string $file File used
     * @access public
     *
     * @return string
     */
    public static function getImageDimensions($file)
    {
        global $dim;

        $temp = GetImageSize($file);
        $dim = ($temp[0]) . "x" . ($temp[1]);

        return $dim;
    }

    /**
     * Return file date
     * @param string $file File used
     * @access public
     *
     * @return false|string
     */
    public static function getFileDate($file)
    {
        global $dateFile;

        $dateFile = date("Y-m-d", filemtime($file));

        return $dateFile;
    }

    /**
     * Read the content of a file
     * @param string $file File used
     * @access public
     *
     * @return bool|string
     */
    public static function getFileContents($file)
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
        global $gmtTimezone;

        if ($gmtTimezone == "true") {
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
        global $databaseType;

        if ($databaseType == "sqlserver") {
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
     * @param $tmpsql
     * @param $params
     * @return int
     */
    public function newComputeTotal($tmpsql, $params)
    {
        $db = new \phpCollab\Database();

        $db->query($tmpsql);

        foreach ($params as $key => $param) {
            $db->bind(':' . $key, $param);
        }
        return count($db->resultset());
    }


    /**
     * Count total results from a request
     * @param string $tmpsql Sql query
     * @access public
     *
     * @return int
     * @throws Exception
     */
    public static function computeTotal($tmpsql)
    {
        global $comptRequest, $databaseType;

        $comptRequest = $comptRequest + 1;

        if ($databaseType == "mysql") {
            try {
                $res = mysqli_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }

            try {
                $selectedDb = mysqli_select_db($res, MYDATABASE);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = $tmpsql;
            $index = mysqli_query($res, $sql);

            while ($row = mysqli_fetch_row($index)) {
                $countEnreg[] = ($row[0]);
            }
            $countEnregTotal = count($countEnreg);

            try {
                mysqli_free_result($index);
                mysqli_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if ($databaseType == "postgresql") {
            $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
            $sql = "$tmpsql";
            $index = pg_query($res, $sql);

            while ($row = pg_fetch_row($index)) {
                $countEnreg[] = ($row[0]);
            }

            $countEnregTotal = count($countEnreg);
            try {
                pg_free_result($index);
                pg_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if ($databaseType == "sqlserver") {
            try {
                $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }

            try {
                $selectedDb = mssql_select_db(MYDATABASE, $res);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = "$tmpsql";
            $index = mssql_query($sql, $res);

            while ($row = mssql_fetch_row($index)) {
                $countEnreg[] = ($row[0]);
            }

            $countEnregTotal = count($countEnreg);
            try {
                mssql_free_result($index);
                mssql_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return $countEnregTotal;
    }


    /**
     * @param $tmpsql
     * @param $params
     * @return string
     * Makes a connection to the database and returns the last itemId
     */
    public static function newConnectSql($tmpsql, $params)
    {
        $db = new \phpCollab\Database();

        $db->query($tmpsql);

        foreach ($params as $key => $param) {
            $db->bind(':' . $key, $param);
        }

        $db->execute();

        return $db->lastInsertId();
    }


    /**
     * Simple query
     * @param string $tmpsql Sql query
     * @access public
     *
     * @return string
     * @throws Exception
     */
    public static function connectSql($tmpsql)
    {
        global $databaseType;

        if ($databaseType == "mysql") {
            try {
                $link = mysqli_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }

            try {
                $selectedDb = mysqli_select_db($link, MYDATABASE);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = $tmpsql;
            $index = mysqli_query($link, $sql);
            try {
                mysqli_free_result($index);
                mysqli_close($link);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        if ($databaseType == "postgresql") {
            $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
            $sql = $tmpsql;
            $index = pg_query($res, $sql);
            try {
                pg_free_result($index);
                pg_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        if ($databaseType == "sqlserver") {
            try {
                $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }

            try {
                $selectedDb = mssql_select_db(MYDATABASE, $res);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = $tmpsql;
            $index = mssql_query($sql, $res);
            try {
                mssql_free_result($index);
                mssql_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

    }

    /**
     * Return last id from any table
     * @param string $tmpsql Table name
     * @return string
     * @throws Exception
     * @access public
     */
    public static function getLastId($tmpsql)
    {
        global $tableCollab, $databaseType;
        global $lastId;

        if ($databaseType == "mysql") {
            try {
                $res = mysqli_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }


            try {
                $selectedDb = mysqli_select_db($res, MYDATABASE);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
            $index = mysqli_query($res, $sql);
            while ($row = mysqli_fetch_row($index)) {
                $lastId[] = ($row[0]);
            }
            try {
                mysqli_free_result($index);
                mysqli_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        if ($databaseType == "postgresql") {
            $res = pg_connect("host=" . MYSERVER . " port=5432 dbname=" . MYDATABASE . " user=" . MYLOGIN . " password=" . MYPASSWORD);
            global $lastId;
            $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
            $index = pg_query($res, $sql);
            while ($row = pg_fetch_row($index)) {
                $lastId[] = ($row[0]);
            }
            try {
                pg_free_result($index);
                pg_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        if ($databaseType == "sqlserver") {
            global $lastId;

            try {
                $res = mssql_connect(MYSERVER, MYLOGIN, MYPASSWORD);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_server"]);
            }

            try {
                $selectedDb = mssql_select_db(MYDATABASE, $res);
            } catch (Exception $e) {
                throw new \Exception(self::$strings["error_database"]);
            }

            $sql = "SELECT id FROM $tmpsql ORDER BY id DESC";
            $index = mssql_query($sql, $res);
            while ($row = mssql_fetch_row($index)) {
                $lastId[] = ($row[0]);
            }
            try {
                mssql_free_result($index);
                mssql_close($res);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    /**
     * @param $projectDetail
     * @param $tableProject
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
     **/
    public static function projectComputeCompletion($projectDetail, $tableProject)
    {
        $prj_name = $projectDetail['pro_name'];

        preg_match("/\[([0-9 ]*\/[0-9 ]*)\]/", $prj_name, $findit);

        if ($findit[1] != "") {
            $prj_id = $projectDetail['pro_id'];
            $tasks = new Tasks();

            $taskDetails = $tasks->getTaskById($prj_id);

            $tasksNumb = count($taskDetails['tas_id']);

            $tasksCompleted = 0;

            foreach ($taskDetails['tas_status'] as $stat) {
                if ($stat == 1) {
                    $tasksCompleted++;
                }
            }

            $prj_name = preg_replace("/\[[0-9 ]*\/[0-9 ]*\]/", "[ $tasksCompleted / $tasksNumb ]", $prj_name);
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
}
