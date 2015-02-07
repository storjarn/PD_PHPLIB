<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of Date
 *
 * @author TheNursery
 */
class Date extends Base {

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
        // echo 'this object has been destroyed';
        parent::__destruct();
    }

    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    /**
     *	Check if parameters is 4-digit year
     *  	@param $year - string to be checked if it's 4-digit year
    */	
    public static function isYear($year = "")
    {
        if(!strlen($year) == 4 || !is_numeric($year)) return false;
        for($i = 0; $i < 4; $i++){
            if(!(isset($year[$i]) && $year[$i] >= 0 && $year[$i] <= 9)){
                return false;	
            }
        }
        return true;
    }

    /**
     *	Check if parameters is month
     *  	@param $month - string to be checked if it's 2-digit month
    */	
    public static function isMonth($month = "")
    {
        if(!strlen($month) == 2 || !is_numeric($month)) return false;
        for($i = 0; $i < 2; $i++){
            if(!(isset($month[$i]) && $month[$i] >= 0 && $month[$i] <= 9)){
                return false;	
            }
        }
        return true;
    }

    /**
     *	Check if parameters is day
     *  	@param $day - string to be checked if it's 2-digit day
    */	
    public static function isDay($day = "")
    {
        if(!strlen($day) == 2 || !is_numeric($day)) return false;
        for($i = 0; $i < 2; $i++){
            if(!(isset($day[$i]) && $day[$i] >= 0 && $day[$i] <= 9)){
                return false;	
            }
        }
        return true;
    }
    
    /*
     * Format time (secs) into a friendly string.
     */
    public static function duration($secs) {

            $vals = array(
                'w' => (int) ($secs / SECONDSINADAY / DAYSINAWEEK),
                'd' => $secs / SECONDSINADAY % DAYSINAWEEK,
                'h' => $secs / SECONDSINANHOUR % HOURSINADAY,
                'm' => $secs / SECONDSINAMINUTE % SECONDSINAMINUTE,
                's' => $secs % SECONDSINAMINUTE
            );

            $ret = array();
            $added = false;
            foreach ($vals as $k => $v) {
                    if ($v > 0 || $added) {
                            $added = true;
                            $ret[] = $v . $k;
                    }
            }

            return join(' ', $ret);
            break;

    }
}

?>
