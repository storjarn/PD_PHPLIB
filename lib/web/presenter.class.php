<?php

namespace PHPLIB;

defined('PHPLIB_') or die("c'mon now ;)");               //PHPLIB defined?
include_once PHPLIB_LIBPATH."base.class.php";
include_once "request.class.php";

/**
 * Description of Presenter
 *
 * @author TheNursery
 */
class Presenter extends Base {
	
    private $request = null;
    private $response = null;
    protected $namespace = __NAMESPACE__;
    protected $classname = __CLASS__;
    

    //--- PUBLIC DATA MEMBERS --------------------------------------------------
    public $error;

    //--- PROTECTED DATA MEMBERS -----------------------------------------------
    protected $arrParameters;

    //--- PRIVATE DATA MEMBERS -------------------------------------------------
    private  $arrViewTypes;
    private  $defaultView;
    private  $defaultAction;

		
    //--------------------------------------------------------------------------
    // CLASS CONSTRUCTOR
    //--------------------------------------------------------------------------
    function __construct()
    {
        session_start();
        
        parent::__construct();
        
        //TODO:  Get request params and populate.
        $this->request = new Request();
        $this->response = $this->request->Response();
        
    }
    
    function Response() { return $this->response; }
    function Request() { return $this->request; }
	
    //--------------------------------------------------------------------------
    // CLASS DESTRUCTOR
    //--------------------------------------------------------------------------
    function __destruct() {
        // echo 'this object has been destroyed';
        parent::__destruct();
    }

	
    //==========================================================================
    // PUBLIC DATA FUNCTIONS / Actions
    //==========================================================================			
    /**
     *	Show Calendar 
     *
    */	
    function Display()
    {
        include "calendar.template.php";
    }
        
        
    /**
     *	Get calendar data, determined by the parameters in the url.
    */
    function Data(){
        
        $id = (int)$this->getVar('id', 0);
        
        if ($id !== 0) {
            //Single event
            $event = $this->getEvent($id);
            $ret = new stdClass();
            $ret->event = $event;
            $ret->message = "Success";
            $this->WriteJSON($ret); 
            
        } else {

            //Day's events
            $day = $this->getVar('day', date('d'));
            $month = $this->getVar('month', date('m'));
            $year = $this->getVar('year', date('Y'));
            $date = "$year-$month-$day";
            $events = $this->getEventsOnStart($day, $month, $year);
            $ret = new stdClass();
            $ret->events = $events;
            $ret->date = $date;
            $this->WriteJSON($ret); 
        }
        /**/
    }
    
    function Edit() { 
        $ret = new stdClass();
        $ret->message = "Nothing saved.";
        if ($this->isAdmin()) {
            $process = (bool)$this->getVar('process', false);
            
            if ($process) {
                
                $format = $this->getVar('format');
                
                $id = $this->CleanDBParameter($this->postVar("Id"));
                $name = $this->CleanDBParameter($this->postVar("Name"));
                $desc = $this->CleanDBParameter($this->postVar("Description"));
                $notes = $this->CleanDBParameter($this->postVar("Notes"));
                $startDate = $this->CleanDBParameter($this->postVar("StartDate"));
                $startTime = $this->CleanDBParameter($this->postVar("StartTime"));
                $endDate = $this->CleanDBParameter($this->postVar("EndDate"));
                $endTime = $this->CleanDBParameter($this->postVar("EndTime"));
                $active = $this->CleanDBParameter($this->postVar("Active"));
                
                $this->ConnectToDB();
                
                if (!$this->isEmpty($name)){
                    
                    $dStartDate = date("y-m-d", strtotime($startDate));
                    $dEndDate = date("y-m-d", strtotime($endDate));
                    
                    if ($dStartDate > $dEndDate) { 
                        $ret->message = "The start date is past the end date."; 
                    } else if (
                            $dStartDate == $dEndDate
                            && strtotime($startTime) > strtotime($endTime)) { 
                        $ret->message = "The start time is past the end time."; 
                    } else if (
                            $dStartDate == $dEndDate
                            && strtotime($startTime) == strtotime($endTime)) { 
                        $ret->message = "The start time is the same as the end time."; 
                    } else if ($this->isEmpty($id)) {
                        
                        $sql = "INSERT INTO `".$this->db_info['tablename']."` (`Name`, `Description`, `Notes`, `StartDate`, `StartTime`, `EndDate`, `EndTime`, `Active`) VALUES ('$name', '$desc', '$notes', '$startDate', '$startTime', '$endDate', '$endTime', $active) ";
                        $res = mysql_query($sql);
                        //echo $sql; exit();
                    } else {
                        
                        $sql = "UPDATE `".$this->db_info['tablename']."` SET Name = '$name', Description = '$desc', Notes = '$notes', StartDate = '$startDate', StartTime = '$startTime', EndDate = '$endDate', EndTime = '$endTime', Active = $active  WHERE Id = '$id'";
                        //echo $sql; exit();
                        $res = mysql_query($sql);
                    }

                    $affected = mysql_affected_rows();
                    if ($affected > 0) {
                        $ret->message = "Success";
                    } else {
                        if ($this->getVar('debug', false)) $ret->message = $sql;
                    }
                }
            }
        }
        $this->WriteJSON($ret);
    }

    function Login(){
        $process = (bool)$this->getVar('process', false);
        if ($process == true) {
            $user = $this->CleanDBParameter($this->getVar("user"));
            $pass = $this->CleanDBParameter($this->getVar("pass"));

            $_SESSION['admin'] = (isset($this->users[$user]) && $this->users[$user] === $pass);
        }
        $format = $this->getVar('format');
        $ret = new stdClass();
        $ret->message = "That username and/or password are not recognized!";
        if (isset($_SESSION['admin']) && $_SESSION['admin'] == true) {
            $ret->message = "Success";
            $_SESSION['user'] = array(
                'username' => $user
            );
        }
        if ($format == 'json') {
            $this->WriteJSON($ret);
        } else {
            $this->Refresh();
        }
    }
    function Logout(){
        $_SESSION['admin'] = false;
        session_unset();     // unset $_SESSION variable for the runtime
        session_destroy();   // destroy session data in storage
        $format = $this->getVar('format', '');
        if ($format == 'json') {
            $ret = new stdClass();
            $ret->message = "Success";
            $this->WriteJSON($ret);
        } else {
            $this->Refresh();
        }
    }

    /**
     *	Return the detected or default action
    */	
    protected function GetAction()
    {
        return $this->arrParameters["action"];
    }
    


    //==========================================================================
// PRIVATE DATA FUNCTIONS
    //==========================================================================		
    /**
     *	Set default parameters
     *
    */	
    function SetDefaultParameters()
    {
        $this->arrParameters["year"]  = @date("Y");
        $this->arrParameters["month"] = @date("m");
        $this->arrParameters["month_full_name"] = @date("F");
        $this->arrParameters["day"]   = @date("d");
        $this->arrParameters["view"] = $this->defaultView;
        $this->arrParameters["action"] = $this->defaultAction;
        $this->arrToday = @getdate();

        // get current file
        $this->arrParameters["current_file"] = $_SERVER["SCRIPT_NAME"];
        $parts = explode('/', $this->arrParameters["current_file"]);
        $this->arrParameters["current_file"] = $parts[count($parts) - 1];
        
        if ($this->useMysqlTimeoutHandler) {
            register_shutdown_function(array(&$this, "MySqlTimeoutHandler")); 
        }
    }

    /**
     *	Get current parameters - read them from URL
     *
    */	
    function GetCurrentParameters()
    {
        $action 	 = $this->getVar('action', $this->defaultAction, $_GET);

        $year            = $this->getVar('year', @date("Y"), $_GET);
        if (!$this->isYear($year))
            $year = @date("Y");

        $month           = $this->getVar('month', @date("m"), $_GET);
        if (!$this->isMonth($month))
            $month = @date("m");

        if (isset($_GET['day'])) {
            $_GET['day'] = str_pad($_GET['day'], 2, "0", STR_PAD_LEFT);
        }
        $day             = $this->getVar('day', @date("d"), $_GET);

        $view       = $this->getVar('view', $this->defaultView, $_GET);
        if (!array_key_exists($view, $this->arrViewTypes)) 
            $view   = $this->defaultView;

        $cur_date = @getdate(mktime(0,0,0,$month,$day,$year));

        ///echo "<br>3--";
        ///print_r($cur_date);

        $this->arrParameters["year"]  = $cur_date['year'];
        $this->arrParameters["month"] = $this->ConvertToDecimal($cur_date['mon']);
        $this->arrParameters["month_full_name"] = $cur_date['month'];
        $this->arrParameters["day"]   = $day;
        $this->arrParameters["view"] = $view;
        $this->arrParameters["action"] = $action;
        $this->arrToday = @getdate();

        $this->prevYear = @getdate(mktime(0,0,0,$this->arrParameters['month'],$this->arrParameters["day"],$this->arrParameters['year']-1));
        $this->nextYear = @getdate(mktime(0,0,0,$this->arrParameters['month'],$this->arrParameters["day"],$this->arrParameters['year']+1));

        $this->prevMonth = @getdate(mktime(0,0,0,$this->arrParameters['month']-1,$this->arrParameters["day"],$this->arrParameters['year']));
        $this->nextMonth = @getdate(mktime(0,0,0,$this->arrParameters['month']+1,$this->arrParameters["day"],$this->arrParameters['year']));
    }
    
    
    
    //====================================================================================
    //  Model stuff
    //====================================================================================
    //
    private function getEventsOnStart($day, $month, $year){
        //print( $day."-".$month."-".$year );
        $date = $this->PadZeroes($year).'-'.$this->PadZeroes($month).'-'.$this->PadZeroes($day);
        $date = $this->CleanDBParameter($date);
        $date = strtotime($date);
        $date = date("Y-m-d", $date);
        
        $this->ConnectToDB();
        $sql = "SELECT * FROM `".$this->db_info['tablename']."` WHERE StartDate = '$date'";
        if (!$this->isAdmin()) { 
            $sql .= " AND Active = 1"; 
        }
        $sql .= " ORDER BY StartDate, StartTime, EndDate, EndTime LIMIT 0 , 30"; 
        $res = mysql_query($sql);
        /**/
        $events =  $this->ToObjectList($res);
        return $events;
    }
    
    private function getEventsOnEnd($day, $month, $year){
        //print( $day."-".$month."-".$year );
        $date = $this->PadZeroes($year).'-'.$this->PadZeroes($month).'-'.$this->PadZeroes($day);
        $date = $this->CleanDBParameter($date);
        $date = strtotime($date);
        $date = date("Y-m-d", $date);
        
        $this->ConnectToDB();
        $sql = "SELECT * FROM `".$this->db_info['tablename']."` WHERE EndDate = '$date'";
        if (!$this->isAdmin()) { 
            $sql .= " AND Active = 1"; 
        }
        $sql .= " ORDER BY StartDate, StartTime, EndDate, EndTime LIMIT 0 , 30"; 
        $res = mysql_query($sql);
        /**/
        $events =  $this->ToObjectList($res);
        return $events;
    }
    
    private function getEvent($id = 0) {
        $this->ConnectToDB();
        $id = $this->CleanDBParameter($id);
        $sql = "SELECT * FROM `".$this->db_info['tablename']."` WHERE Id = '$id'";
        if (!$this->isAdmin()) { 
            $sql .= " AND Active = 1"; 
        }
        $sql .= " ORDER BY StartDate, StartTime, EndDate, EndTime LIMIT 1"; 
        $res = mysql_query( $sql );
        /**/
        $event =  $this->ToObject($res);
        return $event;
    }


    
    //====================================================================================
    //  View stuff
    //====================================================================================
    //
    
    
    

    ////////////////////////////////////////////////////////////////////////////
    // Auxilary
    ////////////////////////////////////////////////////////////////////////////
    
    
    //====================================================================================
    //  Utility stuff
    //====================================================================================



    //====================================================================================
    //  File/Folder stuff
    //====================================================================================
    //
    
    
    
    //====================================================================================
    //  Database stuff
    //====================================================================================
    

    
    //====================================================================================
    //  Business-logic stuff
    //====================================================================================
    //
    private function Refresh() {
        $parts = explode("?", $_SERVER['REQUEST_URI']);
        $path = $parts[0];
        $querystring = count($parts) > 1 ? $parts[1] : '';
        $querystring = explode("&", $querystring);
        $newQs = array();
        for ($i = 0; $i < count($querystring); ++$i) {
            $pair = explode("=", $querystring[$i]);
            if (count($pair) == 1) {
                array_push($pair, "");
            }
            foreach($pair as $key => $val) {
                if ($key != "action" && $val != "logout" && trim($val) != "") {
                    $newQs[] = "$key=$val";
                }
            }
            if (count($newQs) > 0) {
                $path = $path . "?" . implode("&", $newQs);
            }
        }
        //echo $path; exit();
        header("Location: ".$path); exit();
    }
    
    /*private*/ function isAdmin() {
        return ((bool)$this->sessVar('admin')) === true;
    }
    
    
    /*private*/ function getItemName() {
        return $this->itemname;
    }
    
    /*private*/ function getItemsName() {
        return $this->itemsname;
    }
}
?>