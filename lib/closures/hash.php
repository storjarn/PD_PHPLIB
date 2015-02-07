<?php

namespace PHPLIB\Closures;
use PHPLIB\Utility;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "static.class.php";


class Hash extends \PHPLIB\StaticBase {
    
    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected static $namespace = __NAMESPACE__;
    protected static $classname = __CLASS__;
    
    //enums
    //state
    public static $EXISTS = 1;
    //functions
    public static $REMOVE = 2;
    public static $POP = 3;
    public static $MERGE = 5;
    public static $DIFF = 6;
    //type(-conversion)
    public static $TOARRAY = 13;
    
    //--------------------------------------------------------------------------
    // Public (Static) METHODS
    //--------------------------------------------------------------------------
    public static function newHashClosure($array = array()) {
        return Hash::instance()->__newHashClosure($array);
    }
    
    public static function toString($outglue, $inglue) {
        return Hash::instance()->___toString($outglue, $inglue);
    }
    
    
    //--------------------------------------------------------------------------
    // Private (Instance) METHODS
    //--------------------------------------------------------------------------
    private function ___toString($outglue, $inglue) {
        return 
            function ($assoc) use ($outglue, $inglue){
                $return = '';
                foreach ($assoc as $tk => $tv) {
                    $return .= $outglue . $tk . $inglue . $tv;
                }
                return substr($return,strlen($outglue));
            };
    }
    
    
    //Hash Closure object
    /*
     * @param $array = array() - starting array to wrap hash functions around.
     * 
     * @returns inside (closure) function:
     * 
     *      (mixed) function( $name = null, $prop = '') use (&$hash)
     * 
     *      @param $name - 
     *          1.  if null, return inner array.  
     *          2.  if is array, set inner array to this.
     *          3.  if is int, run command.  see enums above.  returns [mixed]
     *          4.  otherwise, if $prop exists, use this as key and return the value or an empty string.
     *      @param $prop -
     *          1.  if empty/null, just return the value at the $name key.
     *          2.  if something, set a reference to the value and return the value.
     * 
     *      ex.  $newHash = Hash::newHashClosure(array('first' => 'start'));
     *           $newHash();  //returns array('first' => 'start')
     *           $newHash(Hash::$EXISTS, 'first');  //returns true
     *           $newHash('second');    //returns ''
     *           $newHash('first');     //returns 'start'
     *           $newHash(Hash::$MERGE, array('second' => 'log'));
     *           $newHash();    //returns array('first' => 'start', 'second' => 'log')
     *           $newHash(array('first' => 'start', 'second' => 'log', 'third' => 'lookie!'));
     *           $newHash();    //returns array('first' => 'start', 'second' => 'log', 'third' => 'lookie!')
     */
    private function __newHashClosure($array = array()) {
        $hash = array();
        $hash = array_merge($array, $hash);
        return 
            function ($name = null, $prop = null) use (&$hash) {
                //called with no params, return internal property.
                if ($name == null) return $hash;
                //first param is an array.  Replace internal property.
                if (is_array($name)) {
                    $hash = (array)$name;
                }
                //first param is int, run command on internal property.  other params vary.
                if (is_int($name)) {    
                    switch($name) {
                        case Hash::$EXISTS :            //$prop is string
                            return isset($hash[$prop]);
                            break;
                        case Hash::$POP :               //$prop is string
                            if (isset($hash[$prop])) {
                                $ret = $hash[$prop];
                                unset($hash[$prop]);  
                                return $ret;
                            }
                            return null;
                            break;
                        case Hash::$REMOVE :            //$prop is string
                            if (isset($hash[$prop])) {
                                unset($hash[$prop]); 
                            }  
                            break;
                        case Hash::$MERGE :             //$prop is array
                            $hash = array_merge((array)$hash, (array)$prop);
                            break;
                        case Hash::$DIFF :              //$prop is array
                            $hash = array_diff((array)$hash, (array)$prop);
                            break;
                        case Hash::$TOARRAY :
                            return $hash;
                            break;
                    }
                    return $hash;
                }   //End run command on $hash.  will have returned by this point.
                //  
                //first param is string, second is null.  return named value from internal property.
                if (!$prop) {   
                    return isset($hash[$name]) 
                            ? $hash[$name]
                            : \EMPTYSTRING;
                }
                //first param is string, second is not null.  set named value and return value;
                $hash[$name] =& $prop;
                return $prop;
            };
    }
}

?>
