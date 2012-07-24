<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /**
     * This file is used to associate user's Google account with
     * ATutor Calendar module. First this page will display a login
     * screen, after login is successful or user is already logged in
     * a consent screen is displaed. After user gives consent, the
     * pop-up window closes and the caledar page gets refreshed.
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

    /**
     * Returns the full URL of the current page, based upon env variables
     *
     * Env variables used:
     * $_SERVER['HTTPS'] = (on|off|)
     * $_SERVER['HTTP_HOST'] = value of the Host: header
     * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
     * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
     *
     * @return string Current URL
     */
    function getCurrentUrl() {
        global $_SERVER;

        /**
         * Filter php_self to avoid a security vulnerability.
         */
        $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, 
                           strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $host = $_SERVER['HTTP_HOST'];
        if ($_SERVER['SERVER_PORT'] != '' &&
            (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
                ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        } else {
            $port = '';
        }
        return $protocol . $host . $port . $php_request_uri;
    }

    /**
     * Returns the AuthSub URL which the user must visit to authenticate requests
     * from this application.
     *
     * Uses getCurrentUrl() to get the next URL which the user will be redirected
     * to after successfully authenticating with the Google service.
     *
     * @return string AuthSub URL
     */
    function getAuthSubUrl() {
        global $_authSubKeyFile;
        $next = getCurrentUrl();
        $scope = 'http://www.google.com/calendar/feeds/';
        $session = true;
        if ($_authSubKeyFile != null) {
            $secure = true;
        } else {
            $secure = false;
        }
        return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
            $session);
    }

    /**
     * Outputs a request to the user to login to their Google account, including
     * a link to the AuthSub URL.
     *
     * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
     *
     * @return void
     */
    function requestUserLogin($linkText) {
        $authSubUrl = getAuthSubUrl();
        echo "<a href='javascript:void(0)' onclick=\"window.open('{$authSubUrl}','Authentication','height=500,width=600');\">{$linkText}</a>";
    }

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
    function getAuthSubHttpClient() {
        global $_SESSION, $_GET, $_authSubKeyFile, $_authSubKeyFilePassphrase;
        $client = new Zend_Gdata_HttpClient();
        if ($_authSubKeyFile != null) {
            // set the AuthSub key
            $client->setAuthSubPrivateKeyFile($_authSubKeyFile, $_authSubKeyFilePassphrase, true);
        }
        if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
            $_SESSION['sessionToken'] =
                Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token'], $client);
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
    function isvalidtoken($tokent) {
        try {
            $client = getAuthSubHttpClient();
            outputCalendarListCheck($client);
            return true;
        }
        catch(Zend_Gdata_App_HttpException $e) {
               global $db;
            $qry = "DELETE FROM ".TABLE_PREFIX."at_cal_google_sync WHERE userid='".$_SESSION['member_id']."'";
            mysql_query($qry,$db);
            logout();
        }
    }
    /**
     * Processes loading of this code through a web browser.
     *
     * @return void
     */
    function processPageLoad() {
        global $db;
        if( isset($_GET['logout']) ) {
            $qry = "DELETE FROM ".TABLE_PREFIX."at_cal_google_sync WHERE userid='".$_SESSION['member_id']."'";
            mysql_query($qry,$db);
            logout();
        }
        else {
            if (!isset($_GET['token'])) {
                $qry = "DELETE FROM ".TABLE_PREFIX."at_cal_google_sync WHERE userid='".$_SESSION['member_id']."'";
                mysql_query($qry,$db);
                unset($_SESSION['sessionToken']);
                $authSubUrl = getAuthSubUrl();
                header("Location:".$authSubUrl);
            }
            else {
                $client = getAuthSubHttpClient();
                $qry = "INSERT INTO ".TABLE_PREFIX."at_cal_google_sync (token,userid,calids) values ('".$_SESSION['sessionToken']."','".$_SESSION['member_id']."','')";
                mysql_query($qry,$db);
                echo "<script>window.opener.location.reload(true);window.close();</script>";
            }
        }
    }

    /**
     * If there are some discrepancies in the session or user
     * wants not to connect his/her Google Calendars with ATutor
     * then this function will securely log out the user.
     *
     * @return void
     */
    function logout() {
        // Carefully construct this value to avoid application security problems.
        $php_self = htmlentities(substr($_SERVER['PHP_SELF'],
                0,
                strcspn($_SERVER['PHP_SELF'], "\n\r")),
            ENT_QUOTES);

        Zend_Gdata_AuthSub::AuthSubRevokeToken($_SESSION['sessionToken']);
        unset($_SESSION['sessionToken']);
        echo "<script>window.opener.location.reload(true);window.close();</script>";
        exit();
    }

    processPageLoad();
?>