<?php
    $_user_location	= 'public';
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    require (AT_INCLUDE_PATH.'header.inc.php');
?>
<div style="left:50%; z-index:20000; position:absolute; top:50%" id="loader"><img src="mods/calendar/img/loader.gif" alt="Loading" /> </div>

<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-original.js"></script>
<link href= "<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-theme.css" rel="stylesheet" type="text/css"/>

<script>
    $.ajaxSetup({ cache: false});

    $(document).ready(function () {
        /* Get current date for calculations. */
                
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        
        var activeelem;
        var focusd = false;
        var viewchangd = false;
		
		var calendar = $('#calendar').fullCalendar({
        
            defaultView: "month", 
                            
            loading: function (isLoading, view){
                if( isLoading )
                    $("#loader").show();
                else
					$("#loader").hide();
            },
            
            /* Apply theme */
            theme: false,
            
            /* Header details */
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            saveView: function() {
            },
            /* Allow adding events by selecting cells. */
            selectable: false,

            selectHelper: false,
            
            /* Add tooltip to events after they are rendered. */
            eventAfterRender: function( evento,elemento,viewo ){
            },
            
            /* Event is resized. So update db. */
            eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
            },
            
            /* Add tooltip to cells. */
            viewDisplay: function(view) {
                viewchangd = true;
                $(".fc-button-firsts").each(
                   function()
                   {
                        if( $(this).text().indexOf( 'Previous' ) >= 0 )
                        {
                            if( view.name == "month" )
                                $(this).text("<?php echo _AT('at_cal_prv_mnth'); ?>");
                            else if( view.name == "agendaWeek" )
                                $(this).text("<?php echo _AT('at_cal_prv_week'); ?>");
                            else
                                $(this).text("<?php echo _AT('at_cal_prv_day'); ?>");
                        }
                        if( $(this).text().indexOf( 'Next' ) >= 0 )
                        {
                            if( view.name == "month" )
                                $(this).text("<?php echo _AT('at_cal_nxt_mnth'); ?>");
                            else if( view.name == "agendaWeek" )
                                $(this).text("<?php echo _AT('at_cal_nxt_week'); ?>");
                            else
                                $(this).text("<?php echo _AT('at_cal_nxt_day'); ?>");
                        }
                   }
                );
            },
            /* Event is clicked. So open dialog for editing event. */
            eventClick: function(calevent,jsEvent,view){
            },
            /* Cell is clicked. So open dialog for creating new event. */
            select: function (date,end, allDay, jsEvent, view) {                    
            },
            /* Events are editable. */
            editable: false,
            /* Retrieve events from php file. */
            events: "mods/calendar/json-events-ori.php"            
        });
    });    
    </script>
    <style type='text/css'>
    #calendar {
        width: 75%;
        margin: 0 auto;
    }
    </style>
    <div style="float:left" id="calendar"></div>
<?php
    require (AT_INCLUDE_PATH.'footer.inc.php');
?>