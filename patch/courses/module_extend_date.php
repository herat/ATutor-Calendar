<?php
@session_start();
if (!defined('AT_INCLUDE_PATH')) { exit; }

    /**
     * Extending the course dates to make them accessible to Calendar Module
     * @param    :    none | course_id(if this function is going to be used for multiple courses)
     * @return    :    array (course dates)
     * @author    :    Anurup Raveendran
     */

function courses_extend_date($course_id=null){
    
    global $db;
    $course = array();      
    
    if($course_id==null) $course_id = $_SESSION['course_id'];
    
    // get the course details along with the relevant dates
    $sql = "SELECT M.first_name, M.last_name, C.title, C.release_date, C.end_date
            FROM ".TABLE_PREFIX."courses C , ".TABLE_PREFIX."members M , ".TABLE_PREFIX."course_enrollment E
            WHERE C.course_id = '".$course_id."' 
            AND M.member_id = '".$_SESSION['member_id']."' 
            AND E.member_id = M.member_id";   
    
    
    $result = mysql_query($sql,$db) or die(mysql_error());
    $row_count  = mysql_num_rows($result);     
    
    if($row_count > 0){
    $index=0; 
            $row = mysql_fetch_assoc($result);
            
            /*if(  strpos( $row['release_date']."", '0000-00-00' ) !== false ||
                strpos( $row['end_date']."", '0000-00-00' ) !== false )
            {
                return $course;
            }
            else
            {*/
            if(  strpos( $row['release_date']."", '0000-00-00' ) === false )
            {
                $unix_ts = strtotime($row['release_date']);
                $time = date("h:i A",$unix_ts);
                // release_date
                $course[$index] =  array(
                            "id"=>rand(10000,15000)."",
                            "title"=> "Release date of ".$row['title']." course",
                            "start"=>$row['release_date'],
                            "end"=>$row['release_date'],
                            "allDay"=>false,
                            "color"=>"green",
                            "textColor" => "black",
                            "editable"=>false                        
                            ) ;
                          
                $index++;
            }
                //end date
               if( strpos( $row['end_date']."", '0000-00-00' ) === false )
               {
                $unix_ts = strtotime($row['end_date']);
                $time = date("h:i A",$unix_ts);
                $course[$index] = array(
                            "id"=>rand(10000,15000)."",
                            "title"=> "End date of ".$row['title']." course",
                            "start"=>$row['end_date'],
                            "end"=>$row['end_date'],
                            "allDay"=>false,
                            "color"=>"maroon",
                            "textColor" => "white",
                            "editable"=>false 
                            ) ;
                $index++;
            }
    }
    
return $course;
}
?>
