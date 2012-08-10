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
     * This php file is used for side menu. When instructor allows students to
     * access this module as a course tool then in side menu of course's home page
     * one entry will be created.
     */
    /* start output buffering: */
    ob_start(); 
    global $savant;
?>
<div id='mini-calendar'></div>

<script type='text/javascript' src="<?php echo AT_BASE_HREF; ?>mods/calendar/lib/fullcalendar/fullcalendar-original.js">
</script>

<link href= "<?php echo AT_BASE_HREF; ?>mods/calendar/lib/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
<link href= "<?php echo AT_BASE_HREF; ?>mods/calendar/lib/fullcalendar/miniCal.css" rel="stylesheet" type="text/css"/>

<script type='text/javascript'>
    $(document).ready(function() {
        //get current date
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        $('#mini-calendar').fullCalendar({
            theme: false,
            /* ToDo: Remove week and day views when ported to ATutor */
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            /* Events are editable */
            editable: false,     
            /* ToDo: Replace with fluid tooltip */
            eventMouseover: function(event, jsEvent, view) {
                if (view.name !== 'agendaDay') {
                    $(jsEvent.target).attr('title', event.title);
                }
            },
            events: "<?php echo AT_BASE_HREF; ?>mods/calendar/json-events.php?mini=1"
        }); 
    });
</script>

<?php
    $savant->assign('dropdown_contents', ob_get_contents());
    ob_end_clean();
    
    $savant->assign('title', _AT('calendar_header')); //The box title
    $savant->display('include/box.tmpl.php');
?>