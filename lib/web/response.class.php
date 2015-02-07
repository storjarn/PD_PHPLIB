<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";
//include_once "request.class.php";

/**
 * Description of Response
 *
 * @author TheNursery
 */
class Response extends Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    private $request = null;

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct(&$requestObj) {
        parent::__construct();
        $this->request = $requestObj;
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        parent::__destruct();
        // echo 'this object has been destroyed';
    }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    function Request() { return $this->request; }
    
    
    //====================================================================================
    //  Request/Response stuff
    //====================================================================================
    public function ToJSON($arr = array()) { return Utility::ToJSON($arr); }
    /**
     * Writes the value to the response as JSON, with the correct response headers.
     * Meant to only be called once in a page's lifetime, b/c of the headers being written.
     * Therefore, consolidate all of your objects into one before calling this.
     */
    public function WriteJSON($arr) { Utility::WriteJSON($arr); }
    
    
    
        
 
}

?>
