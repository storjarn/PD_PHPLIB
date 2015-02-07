<?php

namespace PHPLIB\Closures;
use PHPLIB\Utility;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "static.class.php";

class String extends \PHPLIB\StaticBase {
    
    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected static $namespace = __NAMESPACE__;
    protected static $classname = __CLASS__;
    
    //--------------------------------------------------------------------------
    // Public (Static) METHODS
    //--------------------------------------------------------------------------
    public static function toHash($outglue, $inglue) {
        return String::instance()->___toHash($outglue, $inglue);
    }
    
    //
    //Returns an html/xml/general ml 'nugget' or element, 
    //which is actually a function as a first-class object that, 
    //when called, takes one parameter:  it's content (which can be other html) 
    //and returns the generated html as a string.
    
    //ex.  $markup = MarkupElement("span", "class='label'");
    //  ... later:  echo $markup("my label content");
    //  generates:  <span class='label'>my label content</span>
    public static function MarkupElement($element = "div", $attribs = EMPTYSTRING) {
        return String::instance()->__markupElement($element, $attribs);
    }
    
    
    //--------------------------------------------------------------------------
    // Private (Instance) METHODS
    //--------------------------------------------------------------------------
    private function ___toHash($outglue, $inglue) {
        return 
            function ($str) use ($outglue, $inglue){
                $hash = array();
                foreach (explode($outglue, $str) as $pair) {           
                    $k2v = explode($inglue, $pair);           
                    $hash[$k2v[0]] = $k2v[1];           
                }
                return $hash;
            };
    }
    
    private function __markupElement($element, $attribs = EMPTYSTRING){
        if (!Utility::isEmpty($attribs)) $attribs = " ".$attribs;
        $open = "<$element$attribs>";
        $close = "</$element>";
        return 
            function ($inner = EMPTYSTRING, $newAttribs = null) 
                use ($element, $attribs, $open, $close){
                
                if ($newAttribs !== null) {    
                    //Allow dev/designer to use closure to add new attributes at runtime.
                    $attribs2 = " ".$newAttribs;
                    $open2 = "<$element$attribs2>";
                    $close2 = "</$element>";
                } else {    //reset
                    $open2 = $open;
                    $close2 = $close;
                }
                return "$open2$inner$close2";
            };
    }
}

?>
