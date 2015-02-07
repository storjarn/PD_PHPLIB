<?php
/*
 * 
 * This file is an example of using the PHPLIB framework in your own scripts.
 * The phplib.bootstrap.php file is the one you want to include.
 * Make sure to check the config settings in phplib.config.php,
 * that everything there is accurate.
 * 
 * Currently, this file runs debug tests as part of the development process.
 */
define( 'DS', DIRECTORY_SEPARATOR );
define( 'ROOTPATH', dirname(__FILE__).DS );

//Get the framework bootstrap file.
include_once ROOTPATH."phplib.bootstrap.php";   ////Defines constants, Utility, MemoryResource, Base

use PHPLIB\Utility;
use PHPLIB\EMPTYSTRING;
    
function debug($msgObj, $title = EMPTYSTRING, $verbose = false) 
    { Utility::debug($msgObj, $title, $verbose); }

//$PHPLibConfig = new PHPLIB\LibConfig();
$PHPLibConfig = Utility::getMemoryResource("FrameworkConfig");

//debug
if ($PHPLibConfig->debug) {
    
    $obj = Utility::newObject();
    $obj->go = function() { debug("GO!!!!"); };
    //debug( $obj->go() );
    
    /* OS/Server */
    debug( php_uname("s"), "php_uname('s')");
    debug( PHP_OS, "PHP_OS");
    debug( $_SERVER['SERVER_SOFTWARE'], "\$_SERVER['SERVER_SOFTWARE']");
    //debug( ucfirst("browser name pattern") );
    /**/


    /* isEmpty test */
    //$testVar = "0";
    //if (Utility::isEmpty($testVar)) { debug( "'$testVar' is empty" ); } 
    //else { debug( "'$testVar' is not empty" ); }

    $testVar = array();
    debug( empty($testVar), "empty(array())" );
    $testVar = Utility::newObject(array("test"=>true));
    debug( $testVar, "Utility::newObject(\$newParams)" );
    $testVar = (object)(array("test"=>true));
    debug( $testVar, "(object)(\$newParams)" );
    /**/
    
    
    /* File path debugs */
    debug( __FILE__, "__FILE__" );
    debug( $_SERVER, "\$_SERVER" );
    
    //debug( pathinfo( 'http://microcms/?debug=1#yo' ), "pathinfo('http://microcms/?debug=1#yo')" );
    //debug( pathinfo( 
            //'C:\\pd.microcms\\index.php?debug=1#yo' ), 
            //"pathinfo('C:\\pd.microcms\\index.php?debug=1#yo')" 
        //);

    //debug( dirname($_SERVER['SCRIPT_NAME']), "dirname(\$_SERVER['SCRIPT_NAME'])" );
    debug( basename($_SERVER['SCRIPT_NAME']), "basename(\$_SERVER['SCRIPT_NAME'])" );
    /**/
    
    
    /*  Test urls */ 
    $url = "http://username:password@pd.microcms/index.php?debug=1#yo";
    $url = 'http://username:password@pd.microcms/do/includes/damnit/index.php?debug=1#yo';
    //$url = __FILE__;
    //$url = $_SERVER['REQUEST_URI'];
    debug( $url, "current testing url" );
    /**/
    
    
    /* Url Parsing tests 
    $parseUrl = parse_url($url);
    $pathInfo = pathinfo($url);
    $parseUrl = array_merge($parseUrl, $pathInfo);
    debug( $parseUrl, "parse_url()+pathinfo()" );
    */
    
    
    /* Higher level Path processing */
    Utility::loadLib(array("string", "url.class"));  //Includes String, Path, Dictionary, Querystring
    //use PHPLIB\Path;
    //use PHPLIB\Url;
    //use PHPLIB\Querystring;
    
    
    $path = new PHPLIB\Path($url);
    debug( $path->getPathInfo(), "\$path->getPathInfo()" );
    debug( $path->goUpOneLevel(), "\$path->goUpOneLevel()" );
    debug( $path->getLastPathPart(), "\$path->getLastPathPart()" );
    debug( $path, "\$path[->toString()]" );
    debug( $path->getClassname(), "\$path->getClassname()" );
    debug( PHPLIB\String::$Empty, "PHPLIB\String::\$Empty" );
    /**/


    /* Querystring processing */
    $qs = new PHPLIB\QueryString($url);
    debug($qs, "QueryString[->toString()]");
    debug($qs->toString(), "QueryString->toString()");
    debug($qs->Items(), "QueryString->Items()");
    /**/
    
    
    /* Closures processing */
    Utility::loadLib(array("closures", "hash"));
    Utility::loadLib(array("closures", "string"));
    $testQsArr = array(
        'test' => 'Var',
        'test1' => 'Var1'
    );
    debug( $testQsArr, "\$testQsArr" );
    $QSImploder = PHPLIB\Closures\Hash::toString( "&", "=" );
    $QSExploder = PHPLIB\Closures\String::toHash( "&", "=" );
    
    $qs = $QSImploder( $testQsArr );
    debug( $qs, "PHPLIB\Closures\Hash::toString( \"&\", \"=\" )( \$testQsArr )" );
    
    $testQsArr = $QSExploder( $qs );
    debug( $testQsArr, "PHPLIB\Closures\String::toHash( \"&\", \"=\" )( \$qs )" );
    /**/
    
    /* String class tests */
    $spacers = array(
        "AND", 
        'OR', 
        );
    $attribs = array(
        "UPDATE tablename set active = '1' where (id <> 1 ",                        //AND
        ' id <> 6 ',                        //AND
        array( ' id <> 23) ',                //OR
            ' (StartDate = \'6/12/31\' ',      //AND
            ' EndDate = \'6/13/31\' ',          //AND
            ),             
        array(  
            ' (h <> NULL ',                   //OR      
            ' g == NULL)) '
            )
        );
    
    debug( $attribs, "\$attribs" );
    debug( $spacers, "\$attribs spacers" );
    $attribs = PHPLIB\String::multiImplode($spacers, $attribs);
    debug( $attribs, "String::multiImplode(\$attribs)" );
    /**/
    
    
    /* HTML class tests */
    Utility::loadLib(array("closures", "string"));
    //$attribs = array("a",'b','c','d','e', array('f', array('g', array('h'))));
    //$spacers = array("...", '55', 'tt', 'xx');
    $div = PHPLIB\Closures\String::MarkupElement("div", "class='yeah'");
    debug( $div("Cool!!!"), "PHPLIB\Closures\String::MarkupElement(\"div\", \"class='yeah'\")(\"Cool!!!\");" );
    debug( $div("Wow!!!", "class='nope'"), "PHPLIB\Closures\String::MarkupElement(\"div\", \"class='yeah'\")(\"Wow!!!\", \"class='nope'\");" );
    /**/

    
    
    /* Closure namespace tests */
    Utility::loadLib(array('closures','hash'));
    $testVar = array(
        'one' => 1,
        'two' => 2,
        'three' => 3
    );
    debug( $testVar, "\$testVar" );
    $testVar = PHPLIB\Closures\Hash::newHashClosure($testVar);
    $testVar( PHPLIB\Closures\Hash::$REMOVE, "one" );
    debug( $testVar(), "\$testVar after Hash-enclosed and 'one' removed" );
    /**/
    
    
    
    /* Data namespace tests */
    Utility::loadLib(array('data','connection.class'));
    $conn = new PHPLIB\Connection();
    $conn->Host('localhost2');
    debug( $conn->Settings(), "PHPLIB\Connection->Settings()" );
    /**/
    
    
    
    /* Timer tests */
    if ($PHPLibConfig->timer) {
        //$startTime = (int)$_SERVER['REQUEST_TIME'];
        //debug( (time(true) - $startTime) . " seconds", "time() in seconds" );
        /**/
        $timer = Utility::getMemoryResource("FrameworkTimer");
        if ($timer) {
            $timer->stop();
            debug( $timer->get(PHPLIB\Timer::MICROSECONDS), "timer() total time in microseconds");
            debug( $timer->get(PHPLIB\Timer::MILLISECONDS), "timer() total time in milliseconds");
        }
        /* */
    }
    /**/
    
    /* Script's end, spit out log */
    Utility::log("script's end");
    debug( Utility::log(), "Utility::log()" );
    /**/
}

?>
