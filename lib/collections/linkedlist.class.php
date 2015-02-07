<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "base.class.php";

/**
 * Description of LinkedList
 *
 * @author TheNursery
 */
class LinkedList extends Base {

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
