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
	require_once 'includes/classes/googlecalendar.class.php';
	define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');

	$gcalobj = new GoogleCalendar();
	global $db;
	if( isset($_GET['logout']) ) {
		$qry = "DELETE FROM ".TABLE_PREFIX."calendar_google_sync WHERE userid='".$_SESSION['member_id']."'";
		mysql_query($qry,$db);
		$gcalobj->logout();
	}
	else {
		if (!isset($_GET['token'])) {
			$qry = "DELETE FROM ".TABLE_PREFIX."calendar_google_sync WHERE userid='".$_SESSION['member_id']."'";
			mysql_query($qry,$db);
			unset($_SESSION['sessionToken']);
			$authSubUrl = $gcalobj->getAuthSubUrl();
			header("Location:".$authSubUrl);
		}
		else {
			$client = $gcalobj->getAuthSubHttpClient();
			$qry = "INSERT INTO ".TABLE_PREFIX."calendar_google_sync (token,userid,calids) values ('".$_SESSION['sessionToken']."','".$_SESSION['member_id']."','')";
			mysql_query($qry,$db);
			echo "<script>window.opener.location.reload(true);window.close();</script>";
		}
	}    
?>