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
     * This file returns events from database as well as 
     * ATutor events in JSON format.
     */
     if( $_GET['pub'] == 1 )
	 	$_user_location = "public";
    //Retrieve all the personal events.
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    require('includes/classes/events.class.php');
    
	$eventObj = new Events();	

    //Create an empty array and push all the events in it.
    $rows = array();
    foreach( $eventObj->getPersonalEvents($_GET['mid']) as $event) {
		$event["editable"] = false;
		array_push($rows,$event);
	}

    echo $eventObj->caledarEncode($rows);
?>