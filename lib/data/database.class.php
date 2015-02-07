<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of Database
 *
 * @author TheNursery
 */
class Database extends BaseWithOptions {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    protected $connection;
    
    //Abstract :: must override
    //returns true and sets dbname option if selected, false if not.
    public function SelectDatabase($dbname = null); //
    

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct(
            $dbname, 
            $host = 'localhost', 
            $user = 'root', 
            $pass = EMPTYSTRING, 
            $conn = null
        ) {
        
        parent::__construct();
        
        $config = new Config();
        $this->setOptions( $config->defaultDatabaseSettings );
        
        if (!is_string($dbname)) {
            $this->setOptions(array_merge($this->getOptions(), (array)$dbname));
            $dbname = $this->Option('dbname');
            $this->Option('dbname', $dbname);
            $host = $this->Option('host');
            $user = $this->Option('user');
            $pass = $this->Option('pass');
            
            $this->connection = new Connection($host, $user, $pass);
        } else if ($conn instanceof Connection) {
            $this->Option('dbname', $dbname);
            $this->Option('host', $host);
            $this->Option('user', $user);
            $this->Option('pass', $pass);
            
            $this->connection = $conn;
            $conn->Host($host);
            $conn->User($user);
            $conn->Password($pass);
        } else {
            $this->Option('dbname', $dbname);
            $this->Option('host', $host);
            $this->Option('user', $user);
            $this->Option('pass', $pass);
            
            $this->connection = new Connection($host, $user, $pass);
        }
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        // echo 'this object has been destroyed';
        parent::__destruct();
    }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    
   
    
    public function Connection($link = null) {
        if (!$link) {
            return $this->connection;
        }
        $this->connection = $link;
        return $link;
    }
    
    public function Name($dbname = EMPTYSTRING) {
        if (!$dbname) {
            return $this->Option('dbname');
        }
        $this->Option('dbname', $dbname);
        return $dbname;
    }
    
    /*Data Functions*/
    public function SetInfo($params){
        $this->setOptions($params);
    }
    public function Connect(){
        
        if ($this->connection == null) {
            $this->connection = new Connection(); 
        }
        
        $params = $this->getOptions();
        //$host = "localhost"; // MySQL Host
        $host = $this->getValue('host', $params, null);
        //$user = "root"; // MySQL User
        $user = $this->getValue('user', $params, null);
        //$pass = ""; // MySQL password
        $pass = $this->getValue('pass', $params, null);
        //$db_name = $dbName; // Database name
        $db_name = $this->getValue('dbname', $params, null);

        $config = new Config();
        // Shorten timeout
        if ($config->useDatabaseTimeoutHandler) {
            $shortTimeoutVal = $config->databaseTimeoutSeconds;
            $origTimeoutVal = ini_get("mysql.connect_timeout");
            ini_set('mysql.connect_timeout',$shortTimeoutVal);
        }

        // Connect to server
        //$msg = "cannot connect to database";
        //if ($this->isAdmin()) { $msg = "cannot connect to database '$host'"; }
        $success = $this->Connection()->Host($host)->User($user)->Password($pass)->Connect();
        
        // Select database
        //$msg = "cannot select database";
        //if ($this->isAdmin()) { $msg = "cannot select DB '$db_name'"; }
        $success = $this->Select("$db_name"); 

        // Reset timeout
        if ($config->useDatabaseTimeoutHandler) {
            ini_set('mysql.connect_timeout',$origTimeoutVal);
        }
          
    }
    
    public function Close() {
        return $this->Connection()->Close();
    }
    
    
    
    
    
    //==================================================================================================
    
    
    
    

    public static function ToObjectList($mysqlRes) {
        $ret = array();
        if ($mysqlRes) {
            while ($rec = mysql_fetch_array($mysqlRes, MYSQL_ASSOC)) {
                $ret[] = (object)($rec);
            }
        }
        return $ret;
    }

    public static function ToObject($mysqlRes) {
        $ret = null;
        if ($mysqlRes && $rec = mysql_fetch_array($mysqlRes, MYSQL_ASSOC)) {
            $ret = ($rec);
        }
        return $ret ? (object)$ret : null;

        //UTF-8 packaging tip:
        //Transmit:  json_encode(array_map('base64_encode', $array));
        //Recieve:  array_map('base64_decode', json_decode($array);
    }
    
    public static function CleanDBParameter($val) {
        $val = $val.'';
        $val = str_replace("'", "''", $val); //string delimiter
        $val = str_replace("--", "", $val); //sql comments
        $val = str_replace(";", "", $val);  //sql eol
        return $val;
    }

    public static function remove_bad_chars($str_words) {
        $found = false;
        $bad_string = array("select", "drop", ";", "--", "insert","delete", "xp_", "%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://", "onmouseover=", "onmouseout=");
        for ($i = 0; $i < count($bad_string); $i++){
            $str_words = str_replace($bad_string[$i], EMPTYSTRING, $str_words);
        }
        return $str_words;            
    }
    
    /*
     *  Handle when MySQL db is not available
     *  Only redirects when the error is specific to 
     *  mysql_connect(), mysql_select_db(), and mysql_query() timeouts.
     */

    public static function MySqlTimeoutHandler() {
        
        $pagesToStayOn = array($this->GetPage());
        $invalidConnectMsgs = array(
            /*Add any new message patterns here that match mysql connection timeout messages*/
            "A connection attempt failed because the connected party did not (trying to connect",
            "A connection attempt failed because the connected party did not properly respond after a period of time, or established connection failed because connected host has failed to respond.",
            "A link to the server could not be established"
        );
        $error = error_get_last();
        if($error !== NULL){
            $msg = $error['message'];
            $maintenancePage = "/";

            //timeout?
            for($i = 0; $i < count($invalidConnectMsgs); ++$i) {
                $val = $invalidConnectMsgs[$i];
                $pos = strrpos($msg, $val);
                if ($pos !== false && 
                        (
                            strrpos($msg, "mysql_connect()") !== false
                            || strrpos($msg, "mysql_select_db()") !== false
                            || strrpos($msg, "mysql_query()") !== false
                        )
                    ) 
                { 
                    
                    if (in_array($this->currentPath(), $pagesToStayOn)) {
                        //if /services/ping.php is loading this, do not redirect.
                        //Instead, spit out some message.
                        //http://reboot.lbn/services/ping.php?type=2
                        ob_clean();     //This keeps any previous php warnings from showing up in the response.
                        $lastLevel = error_reporting();
                        error_reporting(0);
                        $obj = new stdClass();
                        $obj->message = "Database is unavailable";
                        header('Cache-Control: no-cache, must-revalidate');
                        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                        header('Content-type: application/json');
                        echo json_encode( $obj );
                        error_reporting($lastLevel);
                        exit();
                    } else {
                        header("Location: ".$maintenancePage);
                    }
                }
            }
        }
    }
}

?>
