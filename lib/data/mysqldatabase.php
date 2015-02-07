<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH.DS."data".DS."database.class.php";

/**
 * Description of MySQLDatabase
 *
 * @author TheNursery
 */
class MySQLDatabase extends Database {

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
    public function SelectDatabase($dbname = null){
        if ($dbname !== null) {
            $success = mysql_select_db($dbname) or die("Error selecting database");
            if ($success) {
                $this->Option('dbname', $dbname);
                return true;
            }
        }
        return false;
    }
}

?>
