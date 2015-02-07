<?php

namespace PHPLIB;

include_once "lib/defined.php";
include_once "lib/date.class.php";
include_once "lib/time.class.php";

/**
 * Description of datetime.class
 *
 * @author TheNursery
 */
class DateTime extends Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    
    private $date = null;
    private $time = null;

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
    function Date() { 
        if (!isset($this->date)) {
            $this->date = new Date();
        }
        return $this->date; 
    }
    function Time() { 
        if (!isset($this->time)) {
            $this->time = new Time();
        }
        return $this->time; 
    }
}
?>
