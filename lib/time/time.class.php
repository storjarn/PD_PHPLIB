<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of Time
 *
 * @author TheNursery
 */
class Time extends Base {

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
        parent::__construct();
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
}

?>
