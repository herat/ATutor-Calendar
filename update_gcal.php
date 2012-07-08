<?php

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

function getAuthSubHttpClient()
{
    global $_SESSION, $_GET, $_authSubKeyFile, $_authSubKeyFilePassphrase;
    $client = new Zend_Gdata_HttpClient();
    if ($_authSubKeyFile != null) {
        // set the AuthSub key
        $client->setAuthSubPrivateKeyFile($_authSubKeyFile, $_authSubKeyFilePassphrase, true);
    }
    $client->setAuthSubToken($_SESSION['sessionToken']);
    return $client;
}

$client = getAuthSubHttpClient();
$gdataCal = new Zend_Gdata_Calendar($client);

$eventURL = $_GET['id'];
$command = $_GET["cmd"];

try 
{
	$event = $gdataCal->getCalendarEventEntry($eventURL);
	if( strcmp($command,"delete") == 0 )
	{
		$event->delete();
	}
	else if( strcmp($command,"update") == 0 )
	{
		$event->title = $gdataCal->newTitle($_GET['title']);
		$when = $gdataCal->newWhen();
		$when->startTime = $_GET['start'];
		$when->endTime=$_GET['end'];		
		// Apply the when property to an event
		$event->when = array($when);
		
		$event->save();
	}
	exit();
} 
catch (Zend_Gdata_App_Exception $e) 
{
	echo "Error: " . $e->getMessage();
	exit();
}
?>