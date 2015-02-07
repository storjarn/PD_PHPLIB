<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?

//String constants
if (!defined("EMPTYSTRING")){
    define("EMPTYSTRING", "");
}

if (!defined("SPACECHAR")){
    define("SPACECHAR", " ");
}

if (!defined("UNDERSCORE")){
    define("UNDERSCORE", "_");
}

if (!defined("DS")){
    define( 'DS', DIRECTORY_SEPARATOR );
}

if (!defined("NL")){
    define( 'NL', "\n" );
}

if (!defined("PHPSESSIONIDKEY")){
    define("PHPSESSIONIDKEY", "PHPSESSID");
}


//Number constants
if (!defined("SECONDSINADAY")){
    define("SECONDSINADAY", 86400);
}

if (!defined("SECONDSINANHOUR")){
    define("SECONDSINANHOUR", 3600);
}

if (!defined("SECONDSINAMINUTE")){
    define("SECONDSINAMINUTE", 60);
}

if (!defined("MINUTESINANHOUR")){
    define("MINUTESINANHOUR", 60);
}

if (!defined("DAYSINAWEEK")){
    define("DAYSINAWEEK", 7);
}

if (!defined("MONTHSINAYEAR")){
    define("MONTHSINAYEAR", 12);
}

if (!defined("HOURSINADAY")){
    define("HOURSINADAY", 24);
}
?>
