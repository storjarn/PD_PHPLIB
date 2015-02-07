<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of Dictionary
 *
 * @author TheNursery
 */
class Dictionary extends Base {
    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    private $NULLKEY = "PHPLIB\Dictionary\NULL";
    
    private $dict = array();
    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct(&$hash = null) {
        parent::__construct();
        if ($hash != null && is_array($hash)) {
            $this->dict = $hash;
        }
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        parent::__destruct();
        // echo 'this object has been destroyed';
    }
    
    //--------------------------------------------------------------------------
    // Methods
    //--------------------------------------------------------------------------
    
    public function Items() { return $this->dict; }
    public function Set($key, $val) { 
        if (isset($this->dict[$key]) && $val === null) {    //Remove
            unset($this->dict[$key]);
        } else {
            $this->dict[$key] = $val; 
        }
    }
    
    public function Get($key) 
        { return Utility::getValue($key, $this->dict, null); }
        
    public function Has($key) { return isset($this->dict[$key]); }
    
    protected function parseString($qs, $pairSep, $keyValSep) {
        $ret = array();
        if (strpos($qs, $keyValSep) !== false) {
            $pairs = explode($pairSep, $qs);
            
            for($i =0; $i < count($pairs); ++$i) {
                $pair = explode($keyValSep, $pairs[$i]);
                if (count($pair) == 2) {
                    $this->stackKey($pair[0], $pair[1]);
                } else if (count($pair) == 1) {
                    $this->stackKey($pair[0], EMPTYSTRING);
                }
            }
        }
        //Utility::debug($this->Items(), "parseString() \$this->Items()");
        if (count($this->Items()) == 0) { }
        return $ret;
    }
    
    protected function stackKey($key, $val) {
        if (!Utility::isEmpty( $this->Get($key) )) {
            $dictVal = $this->Get($key);
            if (is_array( $dictVal )) {
                $dictVal[] = $val;
            } else {
                $dictVal = array( $dictVal, $val );
            }
            $this->Set($key, $dictVal);
        } else {
            $this->Set($key, $val);
        }
    }
}

?>
