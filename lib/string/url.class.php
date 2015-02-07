<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."string".DS."path.class.php";
include_once PHPLIB_LIBPATH."collections".DS."querystring.class.php";


/**
 * Description of Url
 *
 * @author TheNursery
 */
class Url extends Path {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private $urlArray = null;

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct($url = EMPTYSTRING) {
        parent::__construct($url);
        $this->setUrl($url);
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        parent::__destruct();
    }
    
    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    
    function setUrl($url = EMPTYSTRING) { $this->string = $url; $this->parseUrl(); }
    
    public function parseUrl() {
        $this->urlArray = ((array)$this->getPathInfo());
        return $this->urlArray;
    }
    //====================================================================================
    //  URI/Path stuff stuff
    //====================================================================================
    public function getPath($url) {
        if ($url == EMPTYSTRING) {
            return $this->urlArray['Path'];
        }
        $url = $this->parseUrl();
        return $url->Path;
    }
    
}



?>
