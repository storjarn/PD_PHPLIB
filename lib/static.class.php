<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."constants.php";

/**
 * Description of StaticBase
 *
 * @author TheNursery
 */
class StaticBase {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private static $id = 0;
    private static $count = 0;
    protected static $namespace = __NAMESPACE__;
    protected static $classname = __CLASS__;

    //--------------------------------------------------------------------------
    // PROTECTED CLASS CONSTRUCTOR -> (STATIC)
    //--------------------------------------------------------------------------
    protected function __construct() { }


    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    public static function instance() {
        static $instance;
        
        if (!isset($instance)) {
            $className = static::$classname;
            $instance = new $className();
            self::increment();
        }
        return $instance;
    }
    
    public static function Count() {
        return self::$count;
    }
    private static function increment()
    {
        return ++self::$count;
    }
    
    
    //--------------------------------------------------------------------------
    // MAGIC METHODS
    //--------------------------------------------------------------------------
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }
}

?>
