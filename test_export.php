<?php

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
global $db;

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN";

//$db = mysql_connect("localhost","root","root");
//mysql_select_db("atutor",$db);

$qry = "SELECT * FROM `".TABLE_PREFIX."full_calendar_events` WHERE userid='".$_SESSION['member_id']."'";
$result = mysql_query($qry,$db);

$rows = array();

while ($row = mysql_fetch_assoc($result)) 
{
    array_push( $rows, $row );        
}

global $moduleFactory;

$coursesmod = $moduleFactory->getModule("_core/courses");
$courses=$coursesmod->extend_date();    
if( $courses != "" )
{
    foreach ( $courses as $event )
        array_push( $rows, $event );
}

$assignmentsmod = $moduleFactory->getModule("_standard/assignments");
$assignments=$assignmentsmod->extend_date();
if( $assignments != "" )
{
    foreach ( $assignments as $event )
        array_push( $rows, $event );
}        

$testsmod = $moduleFactory->getModule("_standard/tests");
$tests=$testsmod->extend_date();
if( $tests != "" )
{
    foreach ( $tests as $event )
        array_push( $rows, $event );
}

foreach( $rows as $row )
{
    $parts = explode( " ", $row["start"] );
    $parts1 = explode(" ", $row["end"] );

    $s_date_p = explode( "-", $parts[0] );
    $e_date_p = explode( "-", $parts1[0] );

    $s_time_p = explode( ":", $parts[1] );
    $e_time_p = explode( ":", $parts1[1] );
    $ical .= "
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true))."@atutor.ca
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".$s_date_p[0].$s_date_p[1].$s_date_p[2]."T".$s_time_p[0].$s_time_p[1]."00Z
DTEND:".$e_date_p[0].$e_date_p[1].$e_date_p[2]."T".$e_time_p[0].$e_time_p[1]."00Z
SUMMARY:". $row["title"] ."
END:VEVENT";
}

$ical .= "
END:VCALENDAR";

mysql_close($db);

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');
echo $ical;
exit;

/*$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true)) . "@yourhost.test
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:19970714T170000Z
DTEND:19970715T035959Z
SUMMARY:Bastille Day Party
END:VEVENT
END:VCALENDAR";

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');
echo $ical;
exit;
*/

?>