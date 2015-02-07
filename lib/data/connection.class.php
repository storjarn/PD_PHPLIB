<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH . "base.class.php";

/**
 * Description of Connection
 *
 * @author TheNursery
 */
class Connection extends BaseWithOptions {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private $dblink;
    private $connected = false;
    

    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct($host = 'localhost', $user = 'root', $pass = EMPTYSTRING) {
        parent::__construct();
        $config = new Config();
        //$this->setOptions( $config->defaultDatabaseSettings );
        $this->Option('host', $config->dbHost );
        $this->Option('user', $config->dbUser );
        $this->Option('pass', $config->dbPass );
    }

    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        $this->Close();
        parent::__destruct();
    }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    public function LinkObject() { return $this->dblink; }
    
    public function Connect($params = array()) {
        if (!$this->isConnected()) {
            if (!empty($params)) {
                $this->setOptions(array_merge($this->getOptions(), $params));
            }
            $host = $this->Option('host');
            $user = $this->Option('user');
            $pass = $this->Option('pass');

            $this->dblink = mysql_connect($host, $user, $pass) or die("Error connecting to database host");
            
            $this->connected = !!(bool)$this->dblink;
            return $this->connected;
        }
        return false;
    }
    public function isConnected() { return $this->connected; }
    
    public function Close() {
        if ($this->isConnected()) {
            try { 
                if (!Utility::isEmpty($this->dblink)) {
                    $this->connected = !(bool)mysql_close($this->dblink); 
                    return !$this->connected;
                }
            } catch(Exception $ex) { return false; } 
        } 
        return false;
    }
    
    public function Host($host = EMPTYSTRING) {
        if ($host === null) return $this;
        if (!$host) {
            return $this->Option('host');
        }
        $this->Option('host', $host);
        return $this;
    }
    public function User($user = EMPTYSTRING) {
        if ($user === null) return $this;
        if (!$user) {
            return $this->Option('user');
        }
        $this->Option('user', $user);
        return $this;
    }
    public function Password($pass = EMPTYSTRING) {
        if ($pass === null) return $this;
        if (!$pass) {
            return $this->Option('pass');
        }
        $this->Option('pass', $pass);
        return $this;
    }
    
    public function Settings() {
        return $this->getOptions();
    }
    
}

?>
