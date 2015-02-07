<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "base.class.php";

/**
 * Description of User
 *
 * @author TheNursery
 */
class User extends Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    
    var $id = 0;        //resource (db, file, ...) reference id
    var $gid = 0;       //global level id       //Administrator-level
    var $aid = 0;       //access group level id //General user-level
    var $firstname = EMPTYSTRING;
    var $lastname = EMPTYSTRING;
    var $username = EMPTYSTRING; 
    var $email = EMPTYSTRING; 
    var $password = EMPTYSTRING; 
    var $password_clear = EMPTYSTRING; 
    var $usertype = EMPTYSTRING;        //Friendly name for aid above.
    var $active = false; 
    var $sendEmail = false; 
    var $registerDate = EMPTYSTRING; 
    var $lastvisitDate = EMPTYSTRING; 
    var $activation = EMPTYSTRING; 
    var $params = array(); 
    var $guest = 1;
    
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
