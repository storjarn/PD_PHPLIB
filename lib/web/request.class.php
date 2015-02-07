<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once "response.class.php";
include_once PHPLIB_LIBPATH."url.class.php";

/*
 * Represents the current web server request.  
 * Static.
 */

class Request extends Base {
    
    private $url = null;
    private $response = null;
    private $userAgent = null;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private static $defaultUserAgent = array(
        'browser_name_regex' => '^mozilla/5\.0 (windows; .; windows nt 5\.1; .*rv:.*) gecko/.* firefox/0\.9.*$',
        'browser_name_pattern' => 'Mozilla/5.0 (Windows; ?; Windows NT 5.1; *rv:*) Gecko/* Firefox/0.9*',
        'parent' => 'Firefox 0.9',
        'platform' => 'WinXP',
        'browser' => 'Firefox',
        'version' => '0.9',
        'majorver' => '0',
        'minorver' => '9',
        'cssversion' => '2',
        'frames' => '1',
        'iframes' => '1',
        'tables' => '1',
        'cookies' => '1',
        'backgroundsounds' => '',
        'vbscript' => '',
        'javascript' => '1',
        'javaapplets' => '1',
        'activexcontrols' => '',
        'cdf' => '',
        'aol' => '',
        'beta' => '1',
        'win16' => '',
        'crawler' => '',
        'stripper' => '',
        'wap' => '',
        'netclr' => ''
    );
    
    
    function __construct($url = EMPTYSTRING) {
        parent::__construct();
        if ($url == EMPTYSTRING) {
            $this->url = new Url($this->currentUrl());
            
            $this->urlArray['IPAddress'] = $_SERVER['SERVER_ADDR'];
            $protocol = explode("/", $_SERVER['SERVER_PROTOCOL']);
            if (count($protocol) > 1) {
                $this->urlArray['ProtocolVersion'] = $protocol[1];
            }
            
            $this->urlArray['Path'] = $this->currentPath();
            $this->urlArray['PageName'] = $this->currentPage();
            $this->urlArray['QueryString'] = $this->queryString();
            
            $this->userAgent = get_browser(null, true);
            
            
        } /*else {  //For WebRequest (later), a reusable, in-memory child request class.
            $urlParts = Url::parseUrl($url);
            $this->urlArray = array_merge($this->urlArray, $urlParts);
            
            //$this->urlArray['IPAddress'] = $_SERVER['SERVER_ADDR'];
            $this->urlArray['PageName'] = $this->currentPage($url);
            $this->urlArray['UserAgent'] = self::defaultUserAgent;
            
            //$this->urlArray['isParentRequest'] = false;
        }*/
        $this->url = $url;
        
        //Execute at end of constructor, or onConstructed
        
    }
	
    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        parent::__destruct();
        // echo 'this object has been destroyed';
    }
    
    
    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    function Url() { return $this->urlArray; }
    
    function Response() {
        if (!$this->response) {
            $this->response = new Response(&$this);
        }
        return $this->response;
    }
    
    
    
    //====================================================================================
    //  URI stuff
    //====================================================================================
    ////Returns just the script's name that is the current global context. i.e. webpage
    public function currentPage($url = EMPTYSTRING) {
        $q = EMPTYSTRING;
        if ($url == EMPTYSTRING) {
            $url = $_SERVER['REQUEST_URI'];
        }
        $section = explode("/", $url);
        if (count($section) > 0) {
            $q2 = $section[count($section)-1];
            $q1 = explode("?", $q2);
            $q = $q1[0];
        }
        return $q;
    }

    public function currentUrl() { return $_SERVER['REQUEST_URI']; }

    public function currentPath() { return Path::removeQueryString(currentUrl()); }
    
    public function appRootPath() { return null /*$_SERVER['DOCUMENT_ROOT']*/; }
    
    public function appRootDir() { return $_SERVER['DOCUMENT_ROOT']; }
    
    
    //Gets a request value from teh $_GET array
    public function getVar($fieldname, $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = strip_tags( Utility::getValue($fieldname, $_GET, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }

    //Same as getVar, but from the $_POST array
    public function postVar($fieldname, $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = strip_tags( Utility::getValue($fieldname, $_POST, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }
    
    /*
     * Does the work of both getVar and postVar.  Pulls from the $_REQUEST object.
     */
    public function requestVar($fieldname, $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = strip_tags( Utility::getValue($fieldname, $_REQUEST, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }
    
    //Sets a request value to teh $_GET array
    public function setGetVar($fieldname, $val) 
        { $_GET[$fieldname] = $val; }

    //Same as getVar, but to the $_POST array
    public function setPostVar($fieldname, $val) 
        { $_POST[$fieldname] = $val; }

    /*
     * Does the work of both setGetVar and setPostVar.  Pushes to the $_REQUEST object.
     */
    public function setRequestVar($fieldname, $val) 
        { $_REQUEST[$fieldname] = $val; }
    


    //====================================================================================
    //  Request/Response stuff
    //====================================================================================
    //Same as getVar, postVar, etc. but for $_FILES.  Default value is null.
    public function filesVar($fieldname, $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = ( Utility::getValue($fieldname, $_FILES, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }

    //====================================================================================
    //  Session stuff
    //====================================================================================
    //Same as getVar, postVar, etc. but for $_SESSION.  
    //Default key is "PHPSESSID".  Default value is empty string.
    public function sessVar($fieldname = "PHPSESSID", $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = strip_tags( Utility::getValue($fieldname, $_SESSION, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }
    
    //Same as getVar, postVar, etc. but for $_COOKIE
    //Default key is "PHPSESSID".  Default value is empty string.
    public function cookieVar($fieldname = "PHPSESSID", $defaultVal = EMPTYSTRING, $type = 'string') { 
        $ret = strip_tags( Utility::getValue($fieldname, $_COOKIE, $defaultVal) );
        $ret = Utility::getValueTyped($ret, $type);
        return $ret; 
    }
    
    //Same as setGetVar, setPostVar, etc. but to $_SESSION.  
    //Default key is "PHPSESSID".  Default value is empty string.
    public function setSessionVar($fieldname, $val) 
        { $_SESSION[$fieldname] = $val; }

    //Same as setGetVar, setPostVar, etc. but to $_COOKIE
    //Default key is "PHPSESSID".  Default value is empty string.
    public function setCookieVar($fieldname, $val) 
        { $_COOKIE[$fieldname] = $val; }
        
    
}
?>
