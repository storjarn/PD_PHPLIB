<?php

namespace PHPLIB; 

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";

/**
 * Description of Number
 *
 * @author TheNursery
 */
class Number extends Base {

    //--------------------------------------------------------------------------
    // FIELDS
    //--------------------------------------------------------------------------
    private $id = 0;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    
    private $number = 0;

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
    
    public function __toInt()
    {
        return $this->number;
    }
        
    //--------------------------------------------------------------------------
    // METHODS
    //--------------------------------------------------------------------------
    /**
     *	Convert to decimal number with leading zero
     *  	@param $number
    */	
    public static function ConvertToDecimal($number)
    {
        return (($number < 10) ? "0" : "").$number;
    }
    
    /*
     * Left pad number with zeroes.
     */
    public static function PadZeroes($number, $maxlength = 2) {
        $str = '' . $number;
        while (strlen($str) < $maxlength) {
            $str = '0' . $str;
        }
        return $str;
    }
    
    /**
     * Returns the truncated number without rounding.
     * 
     * @param $value dec The number to be truncated.
     * @param $precision int The number of decimal places requested.
     * @param $commas bool Defaults to True if nothing passed, if FALSE comma formatting will not be applied.
     * @return dec Truncated Number
     */
    public static function TruncateNumber($value, $precision, $commas = TRUE) {
        $multiplier = pow(10, $precision);
        $value = (int)($value * $multiplier);
        if($commas)
            return number_format(($value / $multiplier), $precision);
        else
            return $value / $multiplier;
    } 

    //Returns boolean if value is greater than minimum and less than maximum.
    public static function BetweenRange($val, $min, $max){
        return ((int)$val > (int)$min && (int)$val < (int)$max);
    }

    //Like betweenRange(), but includes the min and max values as possible values.
    public static function WithinRange($val, $min, $max){
        return ((int)$val >= (int)$min && (int)$val <= (int)$max);
    }
}

?>
