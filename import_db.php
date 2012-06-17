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
    /*echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
	*/
    if (file_exists("../../content/calendar/" . $_FILES["file"]["name"]))
      {
      //echo $_FILES["file"]["name"] . " already exists. <br/>";
	  	if( unlink("../../content/calendar/".$_FILES["file"]["name"] ) )
	  	{
			  //echo "file deleted<br/>";
	  	}
		else
		{
			//error in deleting files
			header('Location: file_import.php');
		}
	  
      }
      move_uploaded_file($_FILES["file"]["tmp_name"],
      "../../content/calendar/" . $_FILES["file"]["name"]);
      //echo "Stored in: " . "../../content/calendar/" . $_FILES["file"]["name"];
      
    }

require_once('SG_iCal.php');

function dump_t($x) {
	echo "<pre>".print_r($x,true)."</pre>";
}
$ICS = "../../content/calendar/".$_FILES["file"]["name"];
//echo dump_t(file_get_contents($ICS));

$ical = new SG_iCalReader($ICS);
$query = new SG_iCal_Query();

$evts = $ical->getEvents();
//$evts = $query->Between($ical,strtotime('20100901'),strtotime('20101131'));


$data = array();
foreach($evts as $id => $ev) {
	$jsEvt = array(
		"id" => ($id+1),
		"title" => $ev->getProperty('summary'),
		"start" => $ev->getStart(),
		"end"   => $ev->getEnd()-1,
		"allDay" => $ev->isWholeDay()
	);

	if (isset($ev->recurrence)) {
		$count = 0;
		$start = $ev->getStart();
		$freq = $ev->getFrequency();
		if ($freq->firstOccurrence() == $start)
			$data[] = $jsEvt;
		while (($next = $freq->nextOccurrence($start)) > 0 ) {
			if (!$next or $count >= 1000) break;
			$count++;
			$start = $next;
			$jsEvt["start"] = $start;
			$jsEvt["end"] = $start + $ev->getDuration()-1;

			$data[] = $jsEvt;
		}
	} else
		$data[] = $jsEvt;

}
global $db;
if( count($data) > 0 )
{
	$datt = new DateTime();
	echo strtotime("now");
	
	foreach($data as $event)
	{
		//echo Date('Y-m-d H:m:s',$event["start"])."   ". Date('Y-m-d H:m:s',$event["end"]) ."<br/>";
		if( $event["allDay"] == 1 )
		{
			$alld = "true";
		}
		else
		{
			$alld = "false";
		}
		$query = "INSERT INTO `".TABLE_PREFIX."full_calendar_events` (title,start,end,allDay,userid) values".
		" ('".$event["title"]."','".Date('Y-m-d H:m:s',$event["start"])."','".Date('Y-m-d H:m:s',$event["end"])."','".$alld."','".$_SESSION['member_id']."')" ;
		mysql_query( $query, $db );
	}
}

//echo(date('Ymd\n',$data[0][start]));
//echo(date('Ymd\n',$data[1][start]));
//dump_t($data);

//$events = "events:".json_encode($data).',';
header('Location: index.php');
?>