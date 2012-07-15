<?php
    /**
     * This file returns events from Google Calendar in JSON format.
     */
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
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
            global $db;
            $qry = "DELETE FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
            mysql_query($qry,$db);
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
                $query = "SELECT * FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
                $res = mysql_query($query);
                $rowval = mysql_fetch_assoc($res);
                $prevval = $rowval['calids'];

                outputCalendarByDateRange($client,$_GET['start'],$_GET['end'],$prevval);
          }
        }
    }

    function outputCalendarListCheck($client)
    {
        $gdataCal = new Zend_Gdata_Calendar($client);
        $calFeed = $gdataCal->getCalendarListFeed();
    }

    function outputCalendarList($client)
    {
        $gdataCal = new Zend_Gdata_Calendar($client);
        $calFeed = $gdataCal->getCalendarListFeed();
        echo "<a href='http://localhost/tmpp/google-calendar-for-atutor/ui.php?logout=true' >Logout</a><br/>
        <form method='post' action='' onsubmit='window.opener.location.reload(false);window.close();'>";
        echo "<h1>" . $calFeed->title->text . "</h1>\n";
        echo "<ul>\n";
        foreach ($calFeed as $calendar) {
            //echo "\t<li>" . $calendar->title->text . "</li>\n";
            echo "\t<input type='radio' name ='calid' value='".$calendar->id->text."'/>".$calendar->title->text."<br/>";
        }
        echo "</ul>\n";
        echo "<input type='submit' value='Submit' />";
        echo "</form>";
    }

    function outputCalendarByDateRange($client, $startDate='2007-05-01', $endDate='2007-08-01',$idsofcal)
    {
        $gdataCal = new Zend_Gdata_Calendar($client);
        $rows = array();

        $idsofcal = explode(',',$idsofcal);
        $calFeed = $gdataCal->getCalendarListFeed();

        foreach( $idsofcal as $idofcal )
        {
            if( $idofcal != '' )
            {
                $query = $gdataCal->newEventQuery();
                $query->setUser(substr($idofcal,strrpos($idofcal,"/")+1));
                $query->setVisibility('private');
                $query->setProjection('full');
                $query->setOrderby('starttime');
                $query->setStartMin($startDate);
                $query->setStartMax($endDate);
                $eventFeed = $gdataCal->getCalendarEventFeed($query);
                
                $color = "#3399FF";
				$accesslevl = true;
                foreach ($calFeed as $calendar) {
                    if( strpos($idofcal,$calendar->id->text) !== false )
                    {
                        $color = $calendar->color->value;
						if( $calendar->accesslevel->value == 'read' )
						{
							$accesslevl = false;
						}
                    }
                }

                foreach ($eventFeed as $event) {

                    /*$eventID = "";
                    for($i=0;$i<7;$i++)
                    {
                        $eventID .= $event->id->text[rand(0,strlen($event->id->text)-1)];
                    }*/

                    foreach ($event->when as $when) {
                        $startD = substr($when->startTime,0,19);
                        $startD = str_replace("T"," ",$startD);

                        $endD = substr($when->endTime,0,19);
                        $endD = str_replace("T"," ",$endD);

                        /*
                         * If both start time and end time are different and their time parts differ then allDay is false
                         */
                        if( ($startD != $endD) && substr($startD,0,10) == substr($endD,0,10) )
                        {
                            $allDay = 'false';
                        }
                        else
                        {
                            $allDay = 'true';
                        }

                        $row = array();
                        $row["title"] = $event->title->text;
                        $row["id"] = $event->id->text;
                        $row["editable"]=$accesslevl;
                        $row["start"] = $startD;
                        $row["end"] = $endD;
                        $row["allDay"] = $allDay;
                        $row["color"] = $color;
                        $row["textColor"] = "white";
						$row["calendar"] = "Google Calendar event";

                        array_push( $rows, $row );
                    }
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
    }

    function logout()
    {
        // Carefully construct this value to avoid application security problems.
        $php_self = htmlentities(substr($_SERVER['PHP_SELF'],
                0,
                strcspn($_SERVER['PHP_SELF'], "\n\r")),
            ENT_QUOTES);

        Zend_Gdata_AuthSub::AuthSubRevokeToken($_SESSION['sessionToken']);
        unset($_SESSION['sessionToken']);
        echo "<script>window.location.reload(false);</script>";
        exit();
    }

    processPageLoad();
?>