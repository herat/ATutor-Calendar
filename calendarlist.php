<?php
    /**
     * This file is used to display all the available
     * calendars in Google Account of a user.
     */
    require_once 'Zend/Loader.php';

    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_AuthSub');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_HttpClient');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');

    $_authSubKeyFile = null; // Example value for secure use: 'mykey.pem'
    $_authSubKeyFilePassphrase = null;

    /**
     * Returns a HTTP client object with the appropriate headers for communicating
     * with Google using AuthSub authentication.
     *
     * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
     * it is obtained.  The single use token supplied in the URL when redirected
     * after the user succesfully authenticated to Google is retrieved from the
     * $_GET['token'] variable.
     *
     * @return Zend_Http_Client
     */
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

    /**
     * Checks validity of a token. If token is valid then proceed ahead
     * otherwise the user will be logged out. To check token a dummy
     * call to function getCalendarListFeed is made. If there are some 
     * problems then the token is not valid.
     *
     * @return void
     */
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

    /**
     * Processes loading of this code through a web browser. Uses AuthSub
     * authentication and outputs a list of a user's calendars if succesfully
     * authenticated.
     *
     * @return void
     */
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

    /**
     * Display list of calendars in the sidemenu with 
     * checkbox ahead of each calendar's title.
     *
     * @return void
     */
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
            echo "\t<input type='text' size='1' name='color' disabled='disabled' style='background-color:".$calendar->color->value."' /><input type='checkbox' name ='calid' value='".
                $calendar->id->text."' ".$selectd.
                " onclick='if(this.checked) $.get(\"mods/calendar/gcalid.php\",
                { calid: this.value, mode: \"add\" },function (data){ refreshevents(); } );
                else $.get(\"mods/calendar/gcalid.php\",
                { calid: this.value, mode: \"remove\" },function (data){ refreshevents(); } );'
                />".$calendar->title->text."<br/>";
        }   
        echo "";
    }

    /**
     * If there are some discrepancies in the session or user
     * wants not to connect his/her Google Calendars with ATutor
     * then this function will securely log out the user.
     *
     * @return void
     */
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