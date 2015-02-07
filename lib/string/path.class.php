<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."string.class.php";

/**
 * Description of PathInfo
 *
 * @author TheNursery
 */
class Path extends String {
    
    protected $separator = "/";
    private $pathInfo = null;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private static function getNameReplacers(){
        return array(
            'scheme' => 'Protocol',
            'host' => 'Host',
            'user' => 'User',
            'pass' => 'Password',
            'path' => 'Path',
            'query' => 'QueryString',
            'fragment' => 'Hash',
            'dirname' => 'DirPath',
            'basename' => 'PageName',
            'extension' => 'Extension',
            'filename' => 'FileName' 
        );
    }
    
    public static function getEmptyPathInfo() {
        return array(
            //'isParentRequest' => true,
            'IPAddress' => '',
            'Port' => '',
            'Protocol' => '',
            'ProtocolVersion' => '',
            'Host' => '',
            'DirPath' => '',
            'Path' => '',
            'PageName' => '',
            'Extension' => '',
            'QueryString' => '',    //null
            'UserAgent' => null,
            'Hash' => '',    //Not used in Request objects
            'User' => '',
            'Password' => ''
        );
    }
    public function getPathInfo($refresh = false) {
        if ($this->pathInfo == null || $refresh) {
            //$url = 'http://pd.microcms/do/includes/damnit/index.php?debug=1#yo';
            $ret = Utility::newObject();
            $newPathInfo = Path::getEmptyPathInfo();
            $parsedUrl = parse_url($this->string);
            $parsedUrl = array_merge( pathinfo($this->string), $parsedUrl );
            $matchArray = Path::getNameReplacers();
            //Populate object.
            foreach($parsedUrl as $key => $val) {
                if (isset($matchArray[$key])) {
                    $newKey = $matchArray[$key];
                    $ret->$newKey = $val;
                }
            }
            
            $ret = (object) array_merge($newPathInfo, (array) $ret);
            
            //Utility::debug( $ret, "getPathInfo($refresh) \$ret" );
            
            //Fix some values
            $ret->PageName = Utility::removeQueryString($ret->PageName);
            $ret->Extension = Utility::removeQueryString($ret->Extension);
            $ret->FileName = Utility::removeQueryString($ret->FileName);
            //DirPath is full.  we only want root absolute, to be consistent with Path.
            $dirPath = $ret->DirPath;
            $dirPathArr = explode('//', $dirPath);
            if (count($dirPathArr) > 1) {
                $dirPath = $dirPathArr[1];
                $dirPathArr = explode("/", $dirPath);
                if (count($dirPathArr) > 1) {
                    unset($dirPathArr[0]);
                    $dirPath = "/".implode("/", $dirPathArr)."/";
                } else {
                    $dirPath = $dirPath . "/";
                }
                $ret->DirPath = $dirPath;
            }
            //$parsedUrl->Path = $parsedUrl->Path;
            $this->pathInfo = $ret;
            
            //Utility::debug( $this->pathInfo, "getPathInfo($refresh) \$this->pathInfo" );
        }
        
        return $this->pathInfo;
    }
    
    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct($path = EMPTYSTRING) {
        parent::__construct($path);
        if (strpos($path, $this->separator) === false) {
            $this->separator = DS;
        }
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        // echo 'this object has been destroyed';
        parent::__destruct();
    }
    
    
    //====================================================================================
    //  URI/Path stuff stuff
    //====================================================================================
    
    public function removeQueryString() 
        { return new String(Utility::removeQueryString($this->string)); }

    public function goUpOneLevel() {
        $separator = $this->separator;
        $url = $this->getPathInfo();
        //Utility::debug($url, "goUpOneLevel() \$url");
        if ( !Utility::isEmpty($url->Path) ) {
            
            if ( strpos($url->Path, $separator) === false ) 
                return $separator;
            
            $hasExt = !Utility::isEmpty($url->Extension);
            if ($hasExt) {
                $path = new String(str_replace($url->PageName, EMPTYSTRING, $url->Path));
                if ($path->endsWith($separator)) {
                    $path = substr($path, 0, strlen($path)-1);
                }
                //Utility::debug($path, "goUpOneLevel() path");
            } else {
                $path = new String($url->DirPath);
                if ($path->endsWith($separator)) {
                    $path = substr($path, 0, strlen($path)-1);
                }
                //Utility::debug($path, "goUpOneLevel() path");
            }
            
            $parts = explode($separator, $path);
            $count = count($parts);
            if ($count > 1) {
                unset( $parts[$count-1] );
                return new String(implode($separator, $parts).$separator);
            }
        } 
        return new String($separator);
    }
    
    public function getLastPathPart() {
        $separator = $this->separator;
        $url = $this->getPathInfo();
        $path = trim($url->Path);
        $path = trim($path, $separator);
        $parts = explode($separator, $path);
        $count = count($parts);
        if ($count > 2) {
            return new String($parts[count($parts)-1]);
        } else if ($count == 2) {
            return new String($parts[1]);
        } else if ($count == 1) {
            return null;
        }
    }
}

?>
