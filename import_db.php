<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if ($_FILES["file"]["error"] > 0)
{
    //error
    header('Location: file_import.php');
}
else
{
    if (file_exists("../../content/calendar/" . $_FILES["file"]["name"]))
    {
        //File already exists so replace it with new version
        if( unlink("../../content/calendar/".$_FILES["file"]["name"] ) )
        {
            //File successfully deleted
        }
        else
        {
            //error in deleting files
            header('Location: file_import.php');
        }
    }
    move_uploaded_file($_FILES["file"]["tmp_name"],
    "../../content/calendar/" . $_FILES["file"]["name"]);
}

require_once('SG_iCal.php');

function dump_t($x) 
{
    echo "<pre>".print_r($x,true)."</pre>";
}
$ICS = "../../content/calendar/".$_FILES["file"]["name"];
$ical = new SG_iCalReader($ICS);
$query = new SG_iCal_Query();
$evts = $ical->getEvents();
$data = array();
foreach($evts as $id => $ev) 
{
    $jsEvt = array(
        "id" => ($id+1),
        "title" => $ev->getProperty('summary'),
        "start" => $ev->getStart(),
        "end"   => $ev->getEnd()-1,
        "allDay" => $ev->isWholeDay()
    );

    if (isset($ev->recurrence))
    {
        $count = 0;
        $start = $ev->getStart();
        $freq = $ev->getFrequency();
        if ($freq->firstOccurrence() == $start)
            $data[] = $jsEvt;
        while (($next = $freq->nextOccurrence($start)) > 0 )
        {
            if (!$next or $count >= 1000) 
                break;
            $count++;
            $start = $next;
            $jsEvt["start"] = $start;
            $jsEvt["end"] = $start + $ev->getDuration()-1;
            $data[] = $jsEvt;
        }
    } 
    else
        $data[] = $jsEvt;
}
global $db;
if( count($data) > 0 )
{
    foreach($data as $event)
    {
        if( $event["allDay"] == 1 )
        {
            $alld = "true";
        }
        else
        {
            $alld = "false";
        }
        $query = "INSERT INTO `".TABLE_PREFIX."full_calendar_events` (title,start,end,allDay,userid) values".
        " ('".$event["title"]."','".Date('Y-m-d H:m:s',$event["start"])."','".Date('Y-m-d H:m:s',$event["end"]).
        "','".$alld."','".$_SESSION['member_id']."')" ;
        mysql_query( $query, $db );
    }
}
header('Location: index.php');
?>