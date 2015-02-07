<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."presenter.class.php";
/**
 * Description of Page
 *
 * @author TheNursery
 */
class Page extends Presenter {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    private $users = array();
    private $pageName = "";
    
    
    

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct() {
        parent::__construct();
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        // echo 'this object has been destroyed';
        parent::__destruct();
    }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    //Request()
    //Response()
    
    
    
    function SetPage($pageName = "/"){
        $this->pageName = $pageName;
    }
    
    function GetPage(){
        if (!isset($this->pageName)) {
            $this->pageName = Utility::currentPage(); 
        }
        return $this->pageName;
    }
    
    function GetPath(){ return Utility::currentPath(); }

    function SetAdmin($name = "", $pass = "") {
        if ($name != "") {
            $this->users[$name] = $pass;
        }
    }
}

?>
