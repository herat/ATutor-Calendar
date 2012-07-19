<?php
    /**
     * This file returns events from database as well as 
     * ATutor events in JSON format.
     */
    //Retrieve all the events.
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    global $db;
    $query = "SELECT * FROM `".TABLE_PREFIX."full_calendar_events` WHERE userid='".$_SESSION['member_id']."'";
    $result = mysql_query($query,$db);

    //Create an empty array and push all the events in it.
    $rows = array();
    while ($row = mysql_fetch_assoc($result)) 
    {
        $row["editable"]=true;
		$row["calendar"]="Personal event";
        array_push( $rows, $row );
    }

    function get_dates($all=null) 
    {
        global $moduleFactory;
        global $rows;
        $coursesmod = $moduleFactory->getModule("_core/courses");
        $courses=$coursesmod->extend_date();    
        if( $courses != "" ) 
        {
            foreach ( $courses as $event )
			{
				$event["calendar"]="ATutor internal";
                array_push( $rows, $event );
			}
        }
        
        $assignmentsmod = $moduleFactory->getModule("_standard/assignments");
        $assignments=$assignmentsmod->extend_date();
        if( $assignments != "" ) 
        {
            foreach ( $assignments as $event )
            {
				$event["calendar"]="ATutor internal";
                array_push( $rows, $event );
			}
        }        
        
        $testsmod = $moduleFactory->getModule("_standard/tests");
        $tests=$testsmod->extend_date();
        if( $tests != "" ) 
        {
            foreach ( $tests as $event )
            {
				$event["calendar"]="ATutor internal";
                array_push( $rows, $event );
			}
        }
    }

    if(isset($_SESSION['valid_user'])) 
    {
         if($_SESSION['valid_user']) 
         {
            /* check if the user is enrolled in the course */
            $sql = "SELECT COUNT(*) FROM
                   `".TABLE_PREFIX."course_enrollment`
                    WHERE `member_id`='".$_SESSION['member_id']."'
                    AND   `course_id`='".$_SESSION['course_id']."'";
            
            $result = mysql_query($sql,$db);
            $row = mysql_fetch_row($result);
            
            if($row[0]>0) 
            {
                $dates = get_dates();      
            }
            else 
            {
            }
         }
     }

    //Encode in JSON format.
    $str =  json_encode( $rows );

    //Replace "true","false" with true,false for javascript.
    $str = str_replace('"true"','true',$str);
    $str = str_replace('"false"','false',$str);

    //Return the events in the JSON format.
    echo $str;
?>