<?php
    /* utility.class.php
     * Defines PHPLIB\Utility and PHPLIB\MemoryResource classes
     */
namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";
//include_once PHPLIB_LIBPATH."closures".DS."string.php";      //type closures
//include_once PHPLIB_LIBPATH."closures".DS."hash.php";


/*
 * The simplest library of helper functions.
 * No dependencies (db, file, other libs, ui, business logic, etc.) except native PHP.
 */

//Static class
class Utility /*extends Singleton */ {

    private function __construct() { parent::__construct(); }

    private static $libPath = "lib/";
    private static $logArr = array();
    private static $timer = null;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;


    //====================================================================================
    //  Basic stuff
    //====================================================================================

    public static function debug
    (
            $msg = "Utility::debug() called",
            $title = EMPTYSTRING,
            $verbose = false,
            $html = true,   //wrap in markupWrapElement; also displays html if using the pre or default xmp tags to wrap in.
            $markupWrapElement = "xmp"
    ) {
        $open = "<$markupWrapElement>";
        $close = "</$markupWrapElement>";
        if ($title != EMPTYSTRING) {
            if ($html) echo "<div>";
            echo "$title";
            if ($html) echo "</div>";
        }

        if ($verbose) {
            if ($html) echo $open;
            var_dump($msg);
            echo NL;
            if ($html) echo $close;
        } else if (is_array($msg)) {
            if ($html) echo $open;
            print_r($msg);
            echo NL;
            if ($html) echo $close;
        } else if (is_object($msg) && method_exists($msg , "toString" )) {
            if ($html) echo $open;
            echo $msg->toString();
            echo NL;
            if ($html) echo $close;
        } else if (is_object($msg) && method_exists($msg , "__toString" )) {
            if ($html) echo $open;
            echo $msg->__toString();
            echo NL;
            if ($html) echo $close;
        } else if (is_object($msg)) {
            if ($html) echo $open;
            print_r ((array)$msg);
            echo NL;
            if ($html) echo $close;
        } else {
            if ($html) echo $open;
            echo($msg);
            echo NL;
            if ($html) echo $close;
        }
    }
    /*
     * Checks if a variable is empty.
     * FALSE and 0 are not counted as 'empty' and will return false, unlike php empty()
     *
     * @param $val mixed The variable to check.
     * @return bool Returns true if the value is not set, null, or an empty string (will be trimmed).
     */
    public static function isEmpty($val) {
        if (is_array($val)) { return empty($val); }
        return (!isset($val) || $val === NULL || (is_string($val) && trim($val) == EMPTYSTRING ));
    }

    /*
     * Get a value safely from a collection
     * Collections can be indexed arrays, associative arrays,
     * or objects (both class-based and literal).
     *
     * @param $keyname string/int The key or index of an item in the collection.
     * @param &$collection mixed The collection to look in for the key/value.
     * @param $defaultVal mixed Default value if key doesn't exist.  Defaults to empty string.
     * @return mixed The value at the key position or the default value if that key doesn't exist.
     */
    public static function getValue($keyname, &$collection, $defaultVal = EMPTYSTRING) {
        $ret = null;
        if (is_array($collection)) {
            $ret = (isset($collection[$keyname])) ? ($collection[$keyname]) : $defaultVal;
        } else if (is_object($collection)) {
            $ret = (isset($collection->$keyname)) ? ($collection->$keyname) : $defaultVal;
        }
        return $ret;
    }

    public static function getValueTyped($val, $type = 'string') {
        switch ($type) {
            case 'int' :
            case 'integer' :
                return intval($val); break;
            case 'bool' :
            case 'boolean' :
                return (bool)$val; break;
            case 'float' :
            case 'double' :
            case 'real' :
                return (float)$val; break;
            case 'array' :
                return (array)$val; break;
            case 'object' :
                return (object)$val; break;
            case 'unset' :
                return (unset)$val; break;
            case 'string' :
            default :
                return $val.'';
        }
    }

    public static function string_limiter($x,$y) {
        $length = strlen($x);
        if ($length > $y) {
            return substr($x,0,$y)."...";
        } else {
            return $x;
        }
    }

    //Alternative to native php array_diff().
    public static function array_diff_($old_array,$new_array) {
        $r = array();
        foreach($new_array as $i=>$l){
            if (!in_array($l, $old_array) && !in_array($l, $r)) {
                $r[] = $l;
            }
        }
        foreach($old_array as $i=>$l){
            if (!in_array($l, $new_array) && !in_array($l, $r)) {
                $r[] = $l;
            }
        }

        return $r;
    }

    public static function newObject($props = null) {
        if ($props != null && is_array($props)) return (object)$props;
        return new \stdClass();
    }

    public static function deepCopy($object){
        return unserialize(serialize((object)$object));
    }// End Function

    public static function emptyFunc() { /*...*/ }
    public static function emptyReturnFunc(&$val = null) { return $val; }
    public static function echoString($str = EMPTYSTRING) { echo $str; }

    /**
     * Returns the truncated number without rounding.
     *
     * @param $value dec The number to be truncated.
     * @param $precision int The number of decimal places requested.
     * @param $commas bool Defaults to True if nothing passed, if FALSE comma formatting will not be applied.
     * @return dec Truncated Number
     */
    public static function numb_trunc($value, $precision, $commas = TRUE) {
        $multiplier = pow(10, $precision);
        $value = (int)($value * $multiplier);
        if($commas)
            return number_format(($value / $multiplier), $precision);
        else
            return $value / $multiplier;
    }

    //Returns boolean if value is greater than minimum and less than maximum.
    public static function betweenRange($val, $min, $max){
        return ((int)$val > (int)$min && (int)$val < (int)$max);
    }

    //Like betweenRange(), but includes the min and max values as possible values.
    public static function withinRange($val, $min, $max){
        return ((int)$val >= (int)$min && (int)$val <= (int)$max);
    }

    //Multidimensional array implosion
    public static function implode_r($glue, $pieces){
        $return = "";

        if(!is_array($glue)){
            $glue = array($glue);
        }

        $thisLevelGlue = array_shift($glue);

        if(!count($glue)) $glue = array($thisLevelGlue);

        if(!is_array($pieces)){
            return (string) $pieces;
        }

        foreach($pieces as $sub){
            $return .= Utility::implode_r($glue, $sub) . $thisLevelGlue;
        }

        if(count($pieces)) $return = substr($return, 0, strlen($return) -strlen($thisLevelGlue));

        return $return;
    }

    public static function duration($secs) {

        $vals = array('w' => (int) ($secs / 86400 / 7),
        'd' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'm' => $secs / 60 % 60,
        's' => $secs % 60);

        $ret = array();
        $added = false;
        foreach ($vals as $k => $v) {
            if ($v > 0 || $added) {
                $added = true;
                $ret[] = $v . $k;
            }
        }

        return join(' ', $ret);
        break;

    }



    //====================================================================================
    //  URI/Path stuff
    //====================================================================================

    public function removeQueryString($path = EMPTYSTRING) {
        $ret = explode("?", $path);
        return $ret[0];
    }

    //Same as strip_and_get_get, but instead returns the value.
    public static function getVar($fieldname, $defaultVal = EMPTYSTRING) {
        return Utility::stripTags( Utility::getValue($fieldname, $_GET, $defaultVal) );
    }

    //Same as strip_and_get_post, but instead returns the value.
    public static function postVar($fieldname, $defaultVal = EMPTYSTRING) {
        return Utility::stripTags( Utility::getValue($fieldname, $_POST, $defaultVal) );
    }

    /*
     * Does the work of both getVar and postVar.  Pulls from the $_REQUEST object.
     */
    public static function requestVar($fieldname, $defaultVal = EMPTYSTRING) {
        return Utility::stripTags( Utility::getValue($fieldname, $_REQUEST, $defaultVal) );
    }

    //Returns just the script's name that is the current global context. i.e. webpage
    public static function currentPage() {
        $q = EMPTYSTRING;
        $uri_request_id = $_SERVER['REQUEST_URI'];
        $section = explode("/", $uri_request_id);
        if (count($section) > 0) {
            $q2 = $section[count($section)-1];
            $q1 = explode("?", $q2);
            $q = $q1[0];
        }
        return $q;
    }

    public static function currentUrl(){
        return $_SERVER['REQUEST_URI'];
    }

    public static function currentPath(){
        return $_SERVER['SCRIPT_NAME'];
        //$parts = explode("?", self::currentUrl());
        //return $parts[0];
    }

    public static function queryString($url = EMPTYSTRING) {
        if ($url == EMPTYSTRING) {
            return $_GET;
        }

        $qs = array();

        $qss = EMPTYSTRING;  //querystring as string
        if (strrpos($url, "?") !== false) {
            $parts = explode("?", rawurldecode($url));
            $qss = $parts[1];
        } else if (strrpos($url, "=") !== false) {
            $qss = $url;
        }
        if ($qss != EMPTYSTRING) {
            $params = explode("&", $qss);
            for($i = 0; $i < count($params); ++$i) {
                $pair = explode("=", $params[$i]);
                $qs[$pair[0]] = isset($pair[1]) ? $pair[1] : EMPTYSTRING;
            }
        }
        return $qs;
    }


    //====================================================================================
    //  File/Folder stuff
    //====================================================================================
    //


    //====================================================================================
    //  Request/Response stuff
    //====================================================================================
    public static function ToJSON($arr = array()) { return json_encode($arr); }
    /**
     * Writes the value to the response as JSON, with the correct response headers.
     * Meant to only be called once in a page's lifetime, b/c of the headers being written.
     * Therefore, consolidate all of your objects into one before calling this.
     */
    public static function WriteJSON($arr) {
        //ob_clean(); //This makes response clean, but debugging json services more tedious.
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo self::ToJSON( $arr );
        exit();
    }

    public static function Redirect301($url) {
        Utility::log( "Utility::Redirect301(\$url = '$url')" );
        //TODO:  write log somewhere.  maybe put that in a shutdown handler :)
        Header( "HTTP/1.1 301 Moved Permanently" );
        Header( "Location: $url" );
        exit();
    }

    public static function Redirect302($url) {
        //Utility::log( "Utility::Redirect302(\$url = '$url')" );
        //TODO:  write log somewhere.  maybe put that in a shutdown handler :)
        Header( "HTTP/1.1 302 Found" );
        Header( "Location: $url" );
        exit();
    }

    public static function Redirect($url) {
        self::Redirect302($url);
    }

    //Same as getVar, postVar, etc. but for $_FILES.  Default value is null.
    public static function filesVar($fieldname, $defaultVal = null) {
        return Utility::getValue($fieldname, $_FILES, $defaultVal);
    }

    public static function referer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : EMPTYSTRING;
    }


    //====================================================================================
    //  Library stuff
    //====================================================================================
    //
    //returns a config settings object based on the server's ip address or host address.
    //These are defined in config.php.
    public static function getConfig(){

        static $instance;

        if ($instance === null) {
            $servAddr = $_SERVER['SERVER_ADDR'];
            $hostAddr = $_SERVER['HTTP_HOST'];
            $prefixes = Environments::$SitePrefixes;
            $classname = 'Config';
            for($i = 0; $i < count($prefixes); ++$i) {
                $prefix = $prefixes[$i];
                $ips = Utility::getValue($prefix, Environments::$IPs, array());
                $hosts = Utility::getValue($prefix, Environments::$Hosts, array());
                if  ( in_array($servAddr, $ips)
                    || in_array($hostAddr, $hosts)
                )
                {
                    $classname = $prefix.$classname;
                    break;
                }
            }
            $instance = new $classname();
        }

        return $instance;
    }

    public static function getSitePrefix($configObj = null) {
        if ($configObj == null) {
            $configObj = Utility::getConfig();
        }
        $classname = get_class($configObj);
        if (strpos($classname, 'Config') !== false) {
            return str_replace('Config', '', $classname);
        }
        return EMPTYSTRING;
    }

    public static function dash_title($value)
    {
        strip_tags($value);
        if ($value == "-")
            return "title='100% Packet Loss' style='cursor:pointer;'";
        elseif ($value == "--")
            return "title='Not Configured' style='cursor:pointer;'";
        elseif ($value == "---")
            return "title='' style='cursor:pointer;'";
        elseif ($value == "----")
            return "title='Invalid Data' style='cursor:pointer;'";
        else
            return "";
    }
    //work in progress function for new legend
    public static function error_legend($value)
    {
        strip_tags($value);
        if ($value == "-")
            //return "title='100% Packet Loss or Time Sync Issue' style='cursor:pointer;'";
            return "style='cursor:pointer;'";
        elseif ($value == "--")
            return "title='Missing Regulator, Policy, or Other Value' style='cursor:pointer;'";
        elseif ($value == "---")
            return "title='Database Error' style='cursor:pointer;'";
        elseif ($value == "----")
            return "title='Existing Records are Invalid' style='cursor:pointer;'";
        else
            return "";
    }

    public static function loadLib($className) {
        static $included;
        if ($included === null) { $included = array(); }
        if (is_array($className)) {
            $className = implode(DS, $className);
        }
        $path = self::$libPath;
        $includePath = $path."$className.php";

        if (!in_array($includePath, $included)) {
            $included[] = $includePath;
            include_once $includePath;
            Utility::log( "Utility::loadLib() called \$includePath = '$includePath'" );
        }

        //return self;    //can(not) chain with setLibPath
    }

    public static function setLibPath($path) {
        Utility::log( "Utility::setLibPath() \$path = '$path'" );
        self::$libPath = $path;
        //return self;    //can(not) chain with loadLib()
    }

    public static function getLibPath() {
        return self::$libPath;
    }

    public static function log($msg = EMPTYSTRING) {
        if (!$msg) {
            return self::$logArr;
        }
        $arr = self::$logArr;
        list($sec, $usec) = explode(".", microtime(true));
        $date = Utility::getLocalServerTime();
        //$date = date('l jS \of F Y h:i:s A', time());
        //$date = date('H:i:s A  /  D, M jS, Y', time());
        $arr[] = "[".$date." u"./*strval*/($usec)."]  ".$msg;
        self::$logArr = $arr;
    }

    public static function getLocalServerTime($timeZone = null, $format = 'H:i:s A  /  D, M jS, Y') {
        return date($format, time());
        if (!$timeZone) {
            $timeZone = date_default_timezone_get ( void );     //"Europe/London"
        }
        $timezone = new \DateTimeZone( $timeZone );
        $date = new \DateTime();
        $date->setTimezone( $timezone );
        return $date->format( $format );
    }

    public static function getConfig($name = "FrameworkConfig") {
        if (strpos($name, "Config") === false) {
            $name .= "Config";
        }
        //TODO:  Look up real config files if not found in MemoryResource.
        return Utility::getMemoryResource($name);
    }

    //====================================================================================
    //  Session stuff
    //====================================================================================
    //Same as getVar, postVar, etc. but for $_SESSION.
    //Default key is "PHPSESSID".  Default value is empty string.
    public static function sessVar($fieldname = "PHPSESSID", $defaultVal = EMPTYSTRING) {
        return Utility::getValue($fieldname, $_SESSION, $defaultVal);
    }

    //Same as getVar, postVar, etc. but for $_COOKIE
    //Default key is "PHPSESSID".  Default value is empty string.
    public static function cookieVar($fieldname = "PHPSESSID", $defaultVal = EMPTYSTRING) {
        return Utility::getValue($fieldname, $_COOKIE, $defaultVal);
    }

    //====================================================================================
    //  Cool utility stuff
    //====================================================================================
    public static function getMemoryResource($key) {
        $ret =& MemoryResource::Data($key);
        return $ret;
    }

    public static function setMemoryResource($key, &$val) {
        MemoryResource::Data($key, $val);
    }

    //====================================================================================
    //  Database stuff
    //====================================================================================
    //
    public static function CleanDBParameter($val) {
        $val = $val.'';
        $val = str_replace("'", "''", $val); //string delimiter
        $val = str_replace("--", EMPTYSTRING, $val); //sql comments
        $val = str_replace(";", EMPTYSTRING, $val);  //sql eol
        return $val;
    }

    public static function remove_bad_chars($str_words) {
        $found = false;
        $bad_string = array("select", "drop", ";", "--", "insert","delete", "xp_", "%20union%20", "/*", "*/union/*", "+union+", "load_file", "outfile", "document.cookie", "onmouse", "<script", "<iframe", "<applet", "<meta", "<style", "<form", "<img", "<body", "<link", "_GLOBALS", "_REQUEST", "_GET", "_POST", "include_path", "prefix", "http://", "https://", "ftp://", "smb://", "onmouseover=", "onmouseout=");
        for ($i = 0; $i < count($bad_string); $i++){
            $str_words = str_replace($bad_string[$i], "", $str_words);
        }
        return $str_words;
    }

    public static function stripTags($val) {
        if (!is_string($val)) {
            return $val;
        }
        return strip_tags($val);
    }

    //====================================================================================
    //  Markup/UI stuff
    //====================================================================================
    //
    //PHP closure object, since PHP 5.3
    //Create a closure like so:
    //      $div = WrapHTML('div', "class='text'");
    //Use it like so:
    //      echo $div(" Hello! ");  //prints <div class='text'> Hello! </div>
    //      echo $div(" Hello back! ", "class='response'");
    //      //prints <div class='response'> Hello back! </div>
    /*
     * Useful for writing code-based templates, like so:
     *
     *      echo $page(
     *          $header(
     *              $headerContent()    //, and so on....
     *          ) .
     *          $body(
     *              $bodyContent()      //, and so on....
     *          )
     *      );
     */
    public static function WrapHTML($element, $attribs = EMPTYSTRING){
        //TODO:
        if (!Utility::isEmpty($attribs)) $attribs = " ".$attribs;
        $open = "<$element$attribs>";
        $close = "</$element>";
        return
            function ($inner = EMPTYSTRING, $newAttribs = null)
                use ($element, $attribs, $open, $close){
//TODO:  check if $inner is path (ext or dirsep), if so include file as returned string.
//use output buffering to include it as a string.
//$outputSoFar = ob_get_contents();
//ob_end_clean();
//include $inner;
//$includedOutput = ob_get_contents();
//print $outputSoFar;
//return $includedOutput;
                if ($newAttribs !== null) {
                    //Allow dev/designer to use closure to add new attributes at runtime.
                    $attribs2 = " ".$newAttribs;
                    $open2 = "<$element$attribs2>";
                    $close2 = "</$element>";
                } else {    //reset
                    $open2 = $open;
                    $close2 = $close;
                }
                return "$open2$inner$close2";
            };
    }
}

/*
 * Static (global) data encapsulation, for easy retrieval anywhere in the script lifecycle.
 * The method ::Data is a getter when called with only one parameter, and setter with two.
 * When used as a setter, it will (optionally) still return the value, for convenience.
 * ex.
 *      $var = MemoryResource::Data('MyNum', 6);
 *      ...
 *      //later, in another scope or maybe even in another file....
 *      ...
 *      $otherVar = (int)MemoryResource::Data('MyNum');
 *      print ($otherVar * 3.1);    // = '18.6'
 */
class MemoryResource {
    private static $properties;
    private function __construct() { parent::__construct(); }
    //Both getter and setter.  Use one param to get, two to set.  ;)  optionally returns second param if passed.
    public static function Data($name, &$prop = null) {

        if ($prop === null) {
            return isset(self::$properties[$name])
                    ? self::$properties[$name]
                    : EMPTYSTRING;
        }
        self::$properties[$name] =& $prop;
        return $prop;
    }
    //Works like a pop(), returns value, or null.
    public static function RemoveData($name) {
        if (isset(self::$properties[$name])) {
            $ret =& self::$properties[$name];
            unset(self::$properties[$name]);
            return $ret;
        }
        return null;
    }
}

/**
 * Fills your reference variable with $_GET[$fieldname']
 *
 * When filling with $_GET[$fieldname'], we perform a strip_tags on it
 * if it is set. Otherwise we fill it with an empty string.
 *
 * @param Mixed $fill_to Reference to the variable you want to populate
 * @param Mixed $fieldname Fieldname in $_GET you want to populate with.
 * @param Mixed $defaultVal Default value that gets set if fieldname does not exist in $_GET.  Defaults to an empty string.
 */
function strip_and_get_get(&$fill_to, $fieldname, $defaultVal = "")
{
    $fill_to = (isset($_GET[$fieldname])) ? strip_tags($_GET[$fieldname]) : $defaultVal;
    //$fill_to = strip_tags( getValue($fieldname, $_GET, $defaultVal) );
}   //  ends strip_and_get_get()

/**
 * Fills your reference variable with $_POST[$fieldname']
 *
 * When filling with $_POST[$fieldname'], we perform a strip_tags on it
 * if it is set. Otherwise we fill it with an empty string.
 *
 * @param Mixed $fill_to Reference to the variable you want to populate
 * @param Mixed $fieldname Fieldname in $_POST you want to populate with.
 * @param Mixed $defaultVal Default value that gets set if fieldname does not exist in $_POST.  Defaults to an empty string.
 */
function strip_and_get_post(&$fill_to, $fieldname, $defaultVal = "")
{
    $fill_to = (isset($_POST[$fieldname])) ? strip_tags($_POST[$fieldname]) : $defaultVal;
    //$fill_to = strip_tags( getValue($fieldname, $_POST, $defaultVal) );
}   //  ends strip_and_get_post()


/*
Utility::debug( $_SERVER );
$config = Utility::getConfig();
Utility::debug( Utility::getSitePrefix($config) );

$out2 = ob_get_contents();
ob_end_clean();
 *
*/
