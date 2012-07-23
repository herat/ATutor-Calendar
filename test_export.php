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
     * This file is used to generate ics file.
     */
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    global $db;

    //Create ics file in $ical string variable
    $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//ATutor//ATutor Calendar Module//EN";
    
    //Get all the events of a user
    $qry = "SELECT * FROM `".TABLE_PREFIX."full_calendar_events` WHERE userid='".$_SESSION['member_id']."'";
    $result = mysql_query($qry,$db);
    $rows = array();

    while ($row = mysql_fetch_assoc($result)) {
        array_push( $rows, $row );        
    }

    //Get ATutor system events
    global $moduleFactory;

    $coursesmod = $moduleFactory->getModule("_core/courses");
    $courses=$coursesmod->extend_date();    
    if( $courses != "" )
    {
        foreach ( $courses as $event )
            array_push( $rows, $event );
    }

    $assignmentsmod = $moduleFactory->getModule("_standard/assignments");
    $assignments=$assignmentsmod->extend_date();
    if( $assignments != "" ) {
        foreach ( $assignments as $event )
            array_push( $rows, $event );
    }        

    $testsmod = $moduleFactory->getModule("_standard/tests");
    $tests=$testsmod->extend_date();
    if( $tests != "" ) {
        foreach ( $tests as $event )
            array_push( $rows, $event );
    }

    foreach( $rows as $row ) {
        //Timezone manipulation
        $sstamp = strtotime($row["start"])-($_GET['hrs']*60*60);
        $estamp = strtotime($row["end"])-($_GET['hrs']*60*60);
        
        $startdt = gmdate("Y-m-d H:i:s",$sstamp);
        $enddt = gmdate("Y-m-d H:i:s",$estamp);
        
        $parts = explode( " ", $startdt );
        $parts1 = explode(" ", $enddt );

        $s_date_p = explode( "-", $parts[0] );
        $e_date_p = explode( "-", $parts1[0] );

        $s_time_p = explode( ":", $parts[1] );
        $e_time_p = explode( ":", $parts1[1] );
        $ical .= "
BEGIN:VEVENT
UID:" . md5(uniqid(mt_rand(), true))."@atutor.ca
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
DTSTART:".$s_date_p[0].$s_date_p[1].$s_date_p[2]."T".$s_time_p[0].$s_time_p[1]."00Z
DTEND:".$e_date_p[0].$e_date_p[1].$e_date_p[2]."T".$e_time_p[0].$e_time_p[1]."00Z
SUMMARY:". $row["title"] ."
END:VEVENT";
    }

    $ical .= "
END:VCALENDAR";

    //set correct content-type-header
    header('Content-type: text/calendar; charset=utf-8');
    header('Content-Disposition: inline; filename=calendar.ics');
    echo $ical;
    exit;

?>