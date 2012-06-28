<?php
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_HttpClient');
Zend_Loader::loadClass('Zend_Gdata_Calendar');

$_authSubKeyFile = null; // Example value for secure use: 'mykey.pem'
$_authSubKeyFilePassphrase = null;

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

function outputCalendarListCheck($client)
{
    $gdataCal = new Zend_Gdata_Calendar($client);
    $calFeed = $gdataCal->getCalendarListFeed();
}

function isvalidtoken( $tokent )
{
    try
    {
        $client = getAuthSubHttpClient();
        outputCalendarListCheck($client);
        return true;
    }
    catch( Zend_Gdata_App_HttpException $e )
    {
        logout();
    }
}

function processPageLoad()
{
    global $db;
    $qry = "SELECT * FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
    $res = mysql_query($qry,$db);
    if( mysql_num_rows($res) > 0 )
    {
        $row = mysql_fetch_assoc($res);
        $_SESSION['sessionToken'] = $row['token'];
        if( isvalidtoken($_SESSION['sessionToken']) )
        {
            $client = getAuthSubHttpClient();
            outputCalendarList($client);
        }
    }
}

function outputCalendarList($client)
{
    $gdataCal = new Zend_Gdata_Calendar($client);
    $calFeed = $gdataCal->getCalendarListFeed();
    echo "<ul>\n";
    foreach ($calFeed as $calendar) {
        //state according to browser
        echo "\t<input onclick='if(this.checked) $.get(\"mods/calendar/gcalid.php\", { calid: this.value, mode: \"add\" } );
        else $.get(\"mods/calendar/gcalid.php\", { calid: this.value, mode: \"remove\" } );' type='checkbox' name ='calid' value='".
            $calendar->id->text."'/>".$calendar->title->text."<br/>";
    }
    echo "</ul>\n";
}

processPageLoad();
?>