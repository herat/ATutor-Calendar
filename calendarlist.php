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
     * This file is used to display all the available
     * calendars in Google Account of a user.
     */	 
    require_once 'includes/classes/googlecalendar.class.php';

	$gcalobj = new GoogleCalendar();
	global $db;
	$qry = "SELECT * FROM ".TABLE_PREFIX."calendar_google_sync WHERE userid='".$_SESSION['member_id']."'";
	$res = mysql_query($qry,$db);
	if( mysql_num_rows($res) > 0 ) {
		$row = mysql_fetch_assoc($res);
		$_SESSION['sessionToken'] = $row['token'];
		if( $gcalobj->isvalidtoken($_SESSION['sessionToken']) ) {
			$client = $gcalobj->getAuthSubHttpClient();
			$gcalobj->outputCalendarList($client);
		}
	}
?>