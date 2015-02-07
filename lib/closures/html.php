<?php

namespace PHPLIB\Closures;
//use PHPLIB\Utility;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "static.class.php";

/**
 * Description of Html
 *
 * @author TheNursery
 */
class HTML extends \PHPLIB\StaticBase {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected static $namespace = __NAMESPACE__;
    protected static $classname = __CLASS__;
    
    //--------------------------------------------------------------------------
    // Public (Static) METHODS
    //--------------------------------------------------------------------------
    //
    //Returns an html/xml/general ml 'nugget' or element, 
    //which is actually a function as a first-class object that, 
    //when called, takes one parameter:  it's content (which can be other html) 
    //and returns the generated Element object.
    public static function MarkupElement($element = "div", $attribs = EMPTYSTRING) {
    }
    
    
    //--------------------------------------------------------------------------
    // Private (Instance) METHODS
    //--------------------------------------------------------------------------
    private function __markupElement($element, $attribs = EMPTYSTRING){
    }
}

?>
