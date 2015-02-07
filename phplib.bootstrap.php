<?php
//====== Define the library =========
define ("PHPLIB_", true);
define ('PHPLIB_DEBUG', true );
//===============================

    if (PHPLIB_DEBUG) { 
        error_reporting(E_ALL); 
    } else {
        error_reporting(0);
    }

if (!defined("DS")){
define( 'DS', DIRECTORY_SEPARATOR ); }
define( 'PHPLIB_ROOTPATH',dirname(__FILE__).DS );
define( 'PHPLIB_CSSPATH', PHPLIB_ROOTPATH.'css'.DS );
define( 'PHPLIB_IMGPATH', PHPLIB_ROOTPATH.'images'.DS );
define( 'PHPLIB_INCPATH', PHPLIB_ROOTPATH.'includes'.DS );
define( 'PHPLIB_JSPATH',  PHPLIB_ROOTPATH.'js'.DS );
define( 'PHPLIB_LIBFOLDER', 'lib' );
define( 'PHPLIB_LIBPATH', PHPLIB_ROOTPATH.PHPLIB_LIBFOLDER.DS );
define( 'PHPLIB_CLSRPATH', PHPLIB_ROOTPATH.PHPLIB_LIBFOLDER.DS.'closures'.DS );

//Get the framework initial files.
//Includes {Constants}, Utility, MemoryResource, Base
include_once ( PHPLIB_LIBPATH.'constants.php' );              //PHPLIB constants
include_once ( PHPLIB_ROOTPATH.'phplib.config.php' );         //PHPLIB config.class(es)
include_once ( PHPLIB_LIBPATH.'base.class.php' );             //PHPLIB base.class
include_once ( PHPLIB_LIBPATH.'utility.class.php' );          //PHPLIB utility.class (& memoryresource.class)

use PHPLIB\Utility;  
Utility::log("PHPLIB loaded successfully!");
Utility::setLibPath(PHPLIB_LIBPATH);

$config = new PHPLIB\LibConfig();
Utility::setMemoryResource("FrameworkConfig", $config);

if ($config->debug && $config->dev) {
    error_reporting(E_ALL);
}

if ($config->timer) {
    Utility::loadLib(array('time',"timer.class"));
    //use PHPLIB\Timer;
    $timer = new PHPLIB\Timer();
    if ($timer) {
        Utility::setMemoryResource("FrameworkTimer", $timer);
        $timer->start();
        Utility::log("timer started");
    }
}

?>
