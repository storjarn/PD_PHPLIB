<?php 

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."constants.php";
//include_once PHPLIB_LIBPATH."singleton.class.php";
//include_once PHPLIB_LIBPATH."static.class.php";

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author TheNursery
 */

class Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct() {

    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        // echo 'this object has been destroyed';
    }
  


    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    
    public function getNamespace() { return $this->namespace; }
    public function getClassname() { return $this->classname; }
}

class KeyGroup extends Base {
    
    private $__properties = array();
    private $__key = EMPTYSTRING;
    
    function __construct($key = EMPTYSTRING) { 
        $this->setKey($key);
    }
    function setKey($key) { 
        if (is_string($key)) { $this->__key = $key; }
    }
    function getKey() { return $this->__key; }
    
    /* Magic methods */
   // Callback method for getting a property
   function __get($prop_name) 
   {
       $realName = $this->__key.$prop_name;
       if (array_key_exists($realName, $this->__properties)) {
           return $this->__properties[$realName];
       } 
       
       $trace = debug_backtrace();
       trigger_error(
            'Undefined property via {KeyGroup class}->__get('.$prop_name.'): ' .
            ' \$this->__key = ' . $this->__key .
            ' \$realName = ' . $realName .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
   }
   
   // Callback method for setting a property
   function __set($prop_name, $prop_value) 
   {
       $this->__properties[$prop_name] = $prop_value;
       return true;
   }
   
   //  As of PHP 5.1.0
    public function __isset($name) {
        //echo "Is '$name' set?\n";
        return isset($this->__properties[$name]);
    }

    //  As of PHP 5.1.0
    public function __unset($name) {
        //echo "Unsetting '$name'\n";
        unset($this->__properties[$name]);
    }
/* end Magic methods */
}


class BaseWithOptions extends Base {
    protected $options = array();
    protected $optionNames = array();
    
    protected function getOptions() { return $this->options; }
    
    protected function getOptionNames() { return $this->optionNames; }
    
    protected function setOptions($arr = array()) {
        //$this->objectProperties = array_merge($this->objectProperties, $arr);
        $this->options = array();
        $this->optionNames = array();
        foreach($arr as $key => $val) {
            $this->options[$key] = $val;
            $this->optionNames[] = $key;
        }
    }
    
    protected function Option($key, $val = EMPTYSTRING) {
        $key = $key.'';
        if (!$val) {   
            return isset($this->options[$key]) 
                    ? $this->options[$key]
                    : \EMPTYSTRING;
        }
        //first param is string, second is not null.  set named value and return value;
        $this->options[$key] =& $val;
        if (!in_array($key, $this->optionNames)) {
            $this->optionNames[] = $key;
        }
        return $val;
    }
}


?>
