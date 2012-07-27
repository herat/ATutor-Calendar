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
    
    //Retrieve all the personal events.
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
	require('includes/classes/events.class.php');
    
	$eventObj = new Events();
	
    //Create an empty array and push all the events in it.
    $rows = array();
    
	foreach( $eventObj->getPersonalEvents($_SESSION['member_id']) as $event) {
		array_push($rows,$event);
	}
    
    if( $eventObj->getATutorEvents() !== false ) {
		foreach( $eventObj->getATutorEvents() as $event ) {
			array_push($rows,$event);
		}                  
     }

    //Encode in JSON format.
    $str =  json_encode( $rows );

    //Replace "true","false" with true,false for javascript.
    $str = str_replace('"true"','true',$str);
    $str = str_replace('"false"','false',$str);

    //Return the events in the JSON format.
    echo $str;
?>