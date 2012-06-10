<?php 
@session_start();
if (!defined('AT_INCLUDE_PATH')) { exit; }

	/**
     * Extending the test dates to make them accessible to Calendar Module
     * @param	:	none | course_id(if this function is going to be used for multiple courses)
     * @return	:	array (test dates)
     * @author	:	Anurup Raveendran
     */


function tests_extend_date($course_id=null){

    global $db;
    $tests = array();
    
    if($course_id==null) $course_id = $_SESSION['course_id'];
    
     // get course title
    $sql = "SELECT title 
            FROM ".TABLE_PREFIX."courses 
            WHERE course_id = '".$course_id."'";
            
    $result = mysql_query($sql,$db) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $course_title= $row['title'];
    
    $sql = "SELECT title,test_id,start_date,end_date
            FROM ".TABLE_PREFIX."tests
            WHERE course_id = '".$course_id."'";

    $result = mysql_query($sql,$db) or die(mysql_error());
    $row_count  = mysql_num_rows($result);
    

    if($row_count > 0){
    	$index=0;
        while($row = mysql_fetch_assoc($result)){
            if(  strpos( $row['start_date']."", '0000-00-00' ) !== false ||
				strpos( $row['end_date']."", '0000-00-00' ) !== false )
			{
				continue;
			}
			else
			{
            
				$unix_ts = strtotime($row['start_date']);
				$time = date("h:i A",$unix_ts);
				$tests[$index] = array(
							"id"=>rand(20000,25000)."",
							"title"=> "Start date of ".$row['title']." test",
							"start"=>$row['start_date'],
							"end"=>$row['start_date'],
							"allDay"=>false,
							"color"=>"lime",
							"textColor" => "black",
							"editable"=>false			            
							 ) ;
		
		$unix_ts = strtotime($row['end_date']);		
		$time = date("h:i A",$unix_ts);
				
				$index++;
									   
				$tests[$index] = array(
							"id"=>rand(20000,25000)."",
							"title"=> "End date of ".$row['title']." test",
							"start"=>$row['end_date'],
							"end"=>$row['end_date'],
							"allDay"=>false,
							"color"=>"purple",
							"textColor" => "white",
							"editable"=>false 			             
							) ;
			$index++;
			}
		}
    }
    return $tests;
}
?>
