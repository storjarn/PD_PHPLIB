<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once "dictionary.class.php";

/**
 * Description of Querystring
 *
 * @author TheNursery
 */
class QueryString extends Dictionary {
    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private $qs = EMPTYSTRING;
    
    private $pairSep = "&";
    private $keyValSep = "=";
    
    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct($url) {
        parent::__construct();
        $this->parseString($url);
    }

    //--------------------------------------------------------------------------
    // Methods
    //--------------------------------------------------------------------------
    public function __toString() { return $this->toString(); }
    
    
    public function toString() {
        $qs = array();
        $items = $this->Items();
        foreach( $items as $key => $val) {
            if (is_array($val)) {
                $val = implode(",", $val);
            }
            $qs[] = "$key=$val";
            
        }
        return implode($this->pairSep, $qs);
    }
    
    public function parseString($qs) { 
        $this->qs = trim($qs);
        //Utility::debug( $qs, "parseQueryString() \$qs" );
        if (strrpos($this->qs, "/") !== false) {
            $url = new Url($this->qs);
            $url = $url->parseUrl();
            $this->qs = $url['QueryString'];
        }
        $this->qs = str_replace("?", EMPTYSTRING, $this->qs);
        return parent::parseString($this->qs, $this->pairSep, $this->keyValSep); 
    }
}

?>
