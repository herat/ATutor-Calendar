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

    require_once 'includes/classes/googlecalendar.class.php';
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');

    $gcalobj  = new GoogleCalendar();

    $client   = $gcalobj->getAuthSubHttpClient();
    $gdataCal = new Zend_Gdata_Calendar($client);

    $eventURL = $_GET['id'];
    $command  = $_GET['cmd'];

    try {
        $event = $gdataCal->getCalendarEventEntry($eventURL);
        if (strcmp($command,'delete') == 0) {
            $event->delete();
        } else if (strcmp($command,'update') == 0) {
            $event->title    = $gdataCal->newTitle($_GET['title']);
            $when            = $gdataCal->newWhen();
            $when->startTime = $_GET['start'];
            $when->endTime   = $_GET['end'];
            $event->when     = array($when);
            
            $event->save();
        }
        exit();
    } 
    catch (Zend_Gdata_App_Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }
?>