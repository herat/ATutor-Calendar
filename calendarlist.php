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
                outputCalendarList($client);
            }
        }
    }

    function outputCalendarList($client)
    {
        $gdataCal = new Zend_Gdata_Calendar($client);
        $calFeed = $gdataCal->getCalendarListFeed();
        
        global $db;
        $query = "SELECT * FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
        $res = mysql_query($query);
        $rowval = mysql_fetch_assoc($res);
        $prevval = $rowval['calids'];
        $selectd = '';
        echo "<br/>"; 
        foreach ($calFeed as $calendar) {
            //state according to browser
            if( strpos($prevval,$calendar->id->text.',') === false )
                $selectd = '';
            else
                $selectd = "checked='checked'";
            echo "\t<input type='checkbox' name ='calid' value='".
                $calendar->id->text."' ".$selectd.
                " onclick='if(this.checked) $.get(\"mods/calendar/gcalid.php\",
                { calid: this.value, mode: \"add\" },function (data){ refreshevents(); } );
                else $.get(\"mods/calendar/gcalid.php\",
                { calid: this.value, mode: \"remove\" },function (data){ refreshevents(); } );'
                />".$calendar->title->text."<br/>";
        }   
        echo "";    
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