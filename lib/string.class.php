<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of String
 *
 * @author TheNursery
 */
class String extends Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    public static $Empty = EMPTYSTRING;
    
    protected $string = EMPTYSTRING;

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct($string = EMPTYSTRING) {
        parent::__construct();
        if (is_string($string) || method_exists( $string, "__toString" )) {
          $this->string = $string.'';
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
    // toString
    //--------------------------------------------------------------------------
    public function __toString() { return $this->toString(); }
    public function toString() { return $this->string . ''; }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    //====================================================================================
    //  String functions
    //====================================================================================
     public function replace($find = SPACECHAR, $replace = UNDERSCORE) {
         return new String(str_replace($find, $replace, $this->string));
     }
     
     public function endsWith($needle) {
         return substr_compare(
                 $this->string, 
                 $needle, 
                 strlen($this->string)-strlen($needle), 
                 strlen($needle)
         ) === 0;
     }
     
     /*
      * This is a simple function that is the same as implode except it allows you to specify two glue parameters instead of one so an imploded array would output "this, this, this and this" rather than "this, this, this, this, this".

This is useful if you want to implode arrays into a string to echo as part of a sentence.

It uses the second glue between the last two items and the first glue between all others. It will use the second glue if there are only two items to implode so it would output "this and this".
      * 
      * //example below

$array = array("Monday", "Tuesday");
echo "1: ".implode2(', ', ' and ', $array)."<br />";

$array = array("Mon", "Tue", "Wed", "Thu", "Fri");
echo "2: ".implode2(', ', ' &amp; ', $array)."<br />";

$array = array( 1, 2, 3, 4, 10);
echo "3: ".implode2(' + ', ' = ', $array)."<br />";
      * 
      * ?>

This outputs

1: Monday and Tuesday
2: Mon, Tue, Wed, Thu & Fri
3: 1 + 2 + 3 + 4 = 10
      */
    function syntacticList($glue, $lastGlue, $array) {
        return ((sizeof($array) > 2)
                ? implode($glue, array_slice($array, 0, -2)).$glue 
                : "")
            .implode($lastGlue, array_slice($array, -2));
    }
     
     
    public static function multiImplode($spacer, $array) {
        if (is_object($array)) $array = (array)$array;
        if (!is_array($array)) {
            return ($array);
        }   
        if (empty($spacer)) {
            return (self::multiImplode(array(EMPTYSTRING), $array));
        } else {
            $trenn = array_shift($spacer);
            while (list($key,$val) = each($array)) {
                if (is_array($val)) {
                    $array[$key] = self::multiImplode($spacer, $val);
                }
            }
            $array = implode($trenn,$array);
            return ($array);
        }
    }
}

?>
