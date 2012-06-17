<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_custom_css = $_base_path . 'mods/calendar/fullcalendar/fullcalendar-theme.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');
?>
<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-theme.js"></script>
<link href= "<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-theme.css" rel="stylesheet" type="text/css"/> 
<a href="mods/calendar/file_import.php"><?php echo _AT("at_cal_import_file"); ?></a>
<script>
    $(document).ready(function () {
        /* Get current date for calculations. */
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        
        var activeelem;
        var focusd = false;
        
        var calendar = $('#calendar').fullCalendar({
        
            /* Remove comment when ported to ATutor */
            /*defaultView: 'agendaWeek',*/
            
            /* Apply theme */
            theme: false,
            
            /* Header details */
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            
            /* Allow adding events by selecting cells. */
            selectable: true,
            selectHelper: true,
            
            /* Add tooltip to events after they are rendered. */
            eventAfterRender: function( evento,elemento,viewo ){
                if( !evento.editable )
                {
                    var childo = elemento.children();
                    if( viewo.name == "month" )
                        childo[1].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                    else
                        childo[0].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                    //elemento.append("<div class='fc-unedit-announce'>Uneditable event</div>");
                }
                else
                {
                    fluid.tooltip( elemento, {
                            content: function(){
                                return "Click or press enter to edit event";
                            }
                        });
                }
                if( focusd )
                {
                    if( evento.id+"" == $("#ori-name1").val() )
                    {
                        elemento.focus();
                        focusd = false;
                    }
                }
            },
            
            /* Event is resized. So update db. */
            eventResize: function( event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) { 
                //get new start date, end date and send it to the db
                var newsdate = $.fullCalendar.formatDate(event.start,"yyyy-MM-dd HH:mm") +":00"; 
                var newedate = $.fullCalendar.formatDate(event.end,"yyyy-MM-dd HH:mm") +":00"; 
                $.get("mods/calendar/update.php",{id:event.id, start:newsdate, end:newedate, title:'',allday:'', cmd:"drag"});
            },
            
            /* Add tooltip to cells. */
            viewDisplay: function(view) {
                fluid.tooltip(".fc-view-"+view.name, {
                    content: function () {
                        return "Click or press enter to create event";
                    }
                });
            },
            /* Event is clicked. So open dialog for editing event. */
            eventClick: function(calevent,jsEvent,view){
                if( document.activeElement.tagName == "A" )
                    activeelem = document.activeElement;
                    
                if( !calevent.editable ) //for atutor events
                    return;            
                
                $("#fc-emode1").val("edit");
                $("#dialog1").dialog('open');                    
                //display event name in the event title input box
                $("#name1").val(calevent.title);
                
                var date = calevent.start;
                //display start date
                $("#date-start1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                //store event id for manipulation
                $("#ori-name1").val( calevent.id );
                
                //if allDay is true then no need to display time otherwise display time
                if( calevent.allDay == true )
                {
                    //disable time elements from the form
                    $("#container-fc-tm").html("<input type='text' name='time' id='time-start1' disabled='disabled' class='text ui-widget-content ui-corner-all'>");
                    document.getElementById("date-end1").disabled = false;
                    document.getElementById("date-start1").disabled = false;
                    $("#time-start1").addClass("fc-form-hide");
                    $("#time-end1").addClass("fc-form-hide");                     
                    $("#lbl-end-time1").addClass("fc-form-hide");
                    $("#lbl-start-time1").addClass("fc-form-hide");
                     
                    //add and set datepickers
                    $("#date-start1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#date-start1").focus(
                        function (ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-start1").click(
                        function (ev){
                            scwShow(this,ev);
                        }
                    );
                                        
                    if( calevent.end != null )
                        $("#date-end1").val($.fullCalendar.formatDate(calevent.end, 'yyyy-MM-dd'));
                    else
                        $("#date-end1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#date-end1").focus(
                        function (ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-end1").click(
                        function (ev){
                            scwShow(this,ev);
                        }
                    );
                }
                else
                {
                    //enable time elements
                    $("#container-fc-tm").html("<select name='time' id='time-start1' class='text ui-widget-content ui-corner-all'></select>");
                    $("#time-start1").removeClass("fc-form-hide");
                    $("#time-end1").removeClass("fc-form-hide");
                    $("#lbl-end-time1").removeClass("fc-form-hide");
                    $("#lbl-start-time1").removeClass("fc-form-hide");
                    
                    $("#date-end1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#time-start1").val(date.getHours()+":"+date.getMinutes());
                    
                    //add and set datepickers
                    $("#date-start1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#date-start1").focus(
                        function (ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-start1").click(
                        function (ev){
                            scwShow(this,ev);
                        }
                    );
                    if( calevent.end != null )
                        $("#date-end1").val($.fullCalendar.formatDate(calevent.end, 'yyyy-MM-dd'));
                    else
                        $("#date-end1").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#date-end1").focus(
                        function (ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-end1").click(
                        function (ev){
                            scwShow(this,ev);
                        }
                    );
                    
                    //adjust start time and end time dropdown boxes so that the current values are displayed first
                    select = $('#time-end1');
                    $("#time-end1 > option").each(function() {
                        $(this).remove();
                    });
                    var startpt = date.getHours();
                    var endpt = calevent.end;
                    var bol = true;
                    for(zz=0;zz<=24;zz++)
                    {
                        if( zz == 24 )
                        {
                            select.append("<option value='"+zz+":0' >"+zz+"</option>");
                        }
                        else
                        {
                            if( bol )    
                            {
                                select.append("<option value='"+zz+":0' >"+zz+"</option>");                                    
                            }
                            select.append("<option value='"+zz+":30' >"+zz+":30"+"</option>");                                
                            bol = true;                                
                        }
                    }
                    //console.log(endpt.getHours());
                    select.val(endpt.getHours()+":"+endpt.getMinutes());
                    
                    select = $('#time-start1');
                    bol = true;
                    
                    for(zz=0;zz<=24;zz++)
                    {
                        if( zz == 24 )
                        {
                            select.append("<option value='"+zz+":0' >"+zz+"</option>");
                        }
                        else
                        {
                            if( bol )    
                            {
                                select.append("<option value='"+zz+":0' >"+zz+"</option>");                                    
                            }
                            select.append("<option value='"+zz+":30' >"+zz+":30"+"</option>");                                
                            bol = true;                                
                        }
                    }                    
                    select.val(date.getHours()+":"+date.getMinutes());
                }
                //save allDay value in hidden field
                $("#viewname1").val(""+calevent.allDay);
            },
            /* Cell is clicked. So open dialog for creating new event. */
            select: function (date,end, allDay, jsEvent, view) {
                
                activeelem = document.activeElement;
                
                $("#fc-emode").val("create");                    
                $("#dialog").dialog('open');
                //display event title in the input box
                $("#name").val("[event name]");
                //display start date
                $("#date-start").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                //if allday is true then disable time elements else enable them
                if( allDay == true )
                {
                    //month view or all-day events from other 2 views have allDay value true
                    //hide time elements
                    document.getElementById("date-end").disabled = false;                    
                    $("#time-start").addClass("fc-form-hide");
                    $("#time-end").addClass("fc-form-hide");   
                    $("#lbl-end-time").addClass("fc-form-hide");
                    $("#lbl-start-time").addClass("fc-form-hide");
                    //add and set date pickers
                    $("#date-end").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#date-end").focus(
                        function (ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-end").click(
                        function (ev){
                            scwShow(this,ev);
                        }
                    );                                    
                }
                else
                {
                    //enable time elements and prepare them with initial values
                    document.getElementById("date-end").disabled = true;
                    $("#time-start").removeClass("fc-form-hide");
                    $("#time-end").removeClass("fc-form-hide");
                    $("#lbl-end-time").removeClass("fc-form-hide");
                    $("#lbl-start-time").removeClass("fc-form-hide");
                    $("#date-end").val($.fullCalendar.formatDate(date, 'yyyy-MM-dd'));
                    $("#time-start").val(date.getHours()+":"+date.getMinutes());
                    
                    select = $('#time-end');
                    $("#time-end > option").each(function() {
                        $(this).remove();
                    });
                    var startpt = date.getHours();
                    var bol = false;
                    if( date.getMinutes() == 0 )
                        bol = false;
                    else
                    {
                        startpt++;                            
                        bol = true;
                    }
                    for(zz=startpt;zz<=24;zz++)
                    {
                        if( zz == 24 )
                        {
                            select.append("<option value='"+zz+":0' >"+zz+"</option>");
                        }
                        else
                        {
                            if( bol )    
                            {
                                select.append("<option value='"+zz+":0' >"+zz+"</option>");
                            }
                            select.append("<option value='"+zz+":30' >"+zz+":30"+"</option>");
                            bol = true;
                            
                        }                            
                    }                    
                    if( date.getMinutes() == 0 )
                        $("#time-end").val(date.getHours()+":30");
                    else
                        $("#time-end").val((parseInt(date.getHours())+1)+":0");
                }
                //save view name in hidden field
                $("#viewname").val(view.name);                    
            },
            /* Events are editable. */
            editable: false,
            /* Retrieve events from php file. */
            events: "mods/calendar/json-events.php"
        });
        
        /*Create event jQuery dialog*/
        $("#dialog").dialog({
            autoOpen: false,
            height: 300,
            width: 500,
            modal: true,
                buttons: {
                    'Create event': function () {
                    //get start date
                    var startsplt = $("#date-start").val().split("-");
                    var ends;
                    //get end date and time
                    if( $('#viewname').val() == "month" || document.getElementById("date-end").disabled == false)
                        ends = $("#date-end").val();
                    else
                    {
                        ends =  $("#date-start").val();                        
                        var timestr = $('#time-start').val().split(':');
                        var timestp = $('#time-end').val().split(':');                        
                    }
                    var endsplt = ends.split("-");
                    var newid;
                    //string processing of the date values
                    if( startsplt[1].charAt(0) == '0' )
                    {
                        startsplt[1] = startsplt[1].charAt(1);
                    }
                    if( endsplt[1].charAt(0) == '0' )
                    {
                        endsplt[1] = endsplt[1].charAt(1);
                    }
                    if( startsplt[2].charAt(0) == '0' )
                    {
                        startsplt[2] = startsplt[2].charAt(1);
                    }
                    if( endsplt[2].charAt(0) == '0' )
                    {
                        endsplt[2] = endsplt[2].charAt(1);
                    }
                    //first send new events to db, db will return id and then display events in the calendar
                    if( $('#viewname').val() == "month" || document.getElementById("date-end").disabled == false)
                    {

                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2])), 'yyyy-MM-dd HH:mm')+":00";
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2])), 'yyyy-MM-dd HH:mm')+":00";
                        $.get("mods/calendar/update.php",{id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"true"},function(data){
                            calendar.fullCalendar('refetchEvents');
                        });
                        $(this).dialog('close');
                        activeelem.focus();
                    }
                    else
                    {
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2]),timestr[0],timestr[1]), 'yyyy-MM-dd HH:mm')+":00";
                        var mysqlendd = $.fullCalendar.formatDate( new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2]),timestp[0],timestp[1]), 'yyyy-MM-dd HH:mm')+":00";
                        $.get("mods/calendar/update.php",{id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"false"}, function(data){
                            calendar.fullCalendar('refetchEvents');
                        });
                        $(this).dialog('close');
                        activeelem.focus();
                    }
                },
                Cancel: function () {
                    $(this).dialog('close');
                    activeelem.focus();
                }
            },
            close: function () {
                activeelem.focus();
            }
        });
        
        /* Edit event dialog */
        $("#dialog1").dialog({
            autoOpen: false,
            height: 350,
            width: 700,
            modal: true,
            buttons: {
                'Delete Event': function() {
                    //delete event from db
                    $.get("mods/calendar/update.php",{id:$("#ori-name1").val(),start:'',end:'',title:'',allday:'',cmd:"delete"});
                    calendar.fullCalendar('removeEvents',
                    function( ev ){
                        //remove event data from hidden elements
                        $(".fc-month-vhidden").each(
                        function(index){
                            if( $(this).parent().prev().prev().text().indexOf( '"' +ev.id +'"' ) >= 0 )
                            {
                                $(this).parent().prev().prev().html("");
                                $(this).parent().prev().html("");
                            }
                        });
                        $(".fc-cell-date").each(
                        function(index){
                            if(  $(this).prev().text().indexOf( '"' +ev.id +'"' ) >= 0 )
                            {
                                $(this).prev().html("");
                                $(this).next().html("");
                            }
                        });
                        $(".fc-allday-bhidden").each(
                        function(index){
                            if( $(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0 )
                            {
                                $(this).prev().prev().html("");
                                $(this).prev().html("");
                            }
                        });
                        //matching event found for deleting
                        if( ev.id == $("#ori-name1").val())
                            return true;
                    }                    
                    );
                    $(this).dialog('close');
                    //activeelem.focus();
                },
                'Edit event': function () {
                //get new values of time and date
                var startsplt = $("#date-start1").val().split("-");
                var ends;
                if( $('#viewname1').val() == "true" )
                    ends = $("#date-end1").val();
                else
                {
                    ends =  $("#date-start1").val();
                        
                    var timestr = $('#time-start1').val().split(':');
                    var timestp = $('#time-end1').val().split(':');                        
                }
                var endsplt = ends.split("-");
                
                if( startsplt[1].charAt(0) == '0' )
                {
                    startsplt[1] = startsplt[1].charAt(1);
                }
                if( endsplt[1].charAt(0) == '0' )
                {
                    endsplt[1] = endsplt[1].charAt(1);
                }
                if( startsplt[2].charAt(0) == '0' )
                {
                    startsplt[2] = startsplt[2].charAt(1);
                }
                if( endsplt[2].charAt(0) == '0' )
                {
                    endsplt[2] = endsplt[2].charAt(1);
                }
                //if allDay is true then only use dates otherwise use both dates and time values
                if( $('#viewname1').val() == "true" )
                {
                    var sdat = new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2]));
                    var edat = new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2]));
                    if( edat < sdat )
                    {
                        alert("Enter valid dates");
                        $(this).dialog('close');
                        activeelem.focus();
                        return;
                    }
                }
                else
                {
                    var sdat = new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2]),timestr[0],timestr[1]);
                    var edat = new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2]),timestp[0],timestp[1]);
                    if( edat < sdat )
                    {
                        alert("Enter valid dates");
                        $(this).dialog('close');
                        activeelem.focus();
                        return;
                    }
                }
                //remove old event data
                calendar.fullCalendar('removeEvents',
                    function( ev ){
                        $(".fc-month-vhidden").each(
                        function(index){
                            if( $(this).parent().prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0 )
                            {
                                $(this).parent().prev().prev().html("");
                                $(this).parent().prev().html("");
                            }
                        });
                        $(".fc-cell-date").each(
                        function(index){
                            if( $(this).prev().text().indexOf( '"'+ev.id+'"' ) >= 0 )
                            {
                                $(this).prev().html("");
                                $(this).next().html("");
                            }
                        });
                        $(".fc-allday-bhidden").each(
                        function(index){
                            if( $(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0 )
                            {
                                $(this).prev().prev().html("");
                                $(this).prev().html("");
                            }
                        });
                        if( ev.id == $("#ori-name1").val())
                            return true;
                        
                    }
                );
                //add edited event as a new event and also update db values
                if( $('#viewname1').val() == "true" )
                    {
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2])), 'yyyy-MM-dd HH:mm')+":00";
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2])), 'yyyy-MM-dd HH:mm')+":00";
                        $.get("mods/calendar/update.php",{id:$("#ori-name1").val(),start:mysqlstartd, end:mysqlendd, title:$("#name1").val(), cmd:"update",allday:"true"},function(data){
                                calendar.fullCalendar('refetchEvents'); 
                                focusd = true;
                            });
                        $(this).dialog('close');
                        console.log( activeelem.innerHTML );
                    }
                    else
                    {
                        var mysqlendd = $.fullCalendar.formatDate( new Date(parseInt(endsplt[0]),parseInt(endsplt[1])-1,parseInt(endsplt[2]),timestp[0],timestp[1]), 'yyyy-MM-dd HH:mm')+":00";
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),parseInt(startsplt[1])-1,parseInt(startsplt[2]),timestr[0],timestr[1]), 'yyyy-MM-dd HH:mm')+":00";
                        $.get("mods/calendar/update.php",{id:$("#ori-name1").val(),start:mysqlstartd, end:mysqlendd, title:$("#name1").val(), cmd:"update",allday:"false"},function(data){
                                calendar.fullCalendar('refetchEvents');
                                focusd = true;
                            });
                        $(this).dialog('close');
                    }
                },
                Cancel: function () {
                    $(this).dialog('close');
                    activeelem.focus();
                }
            },
            close: function () {
                activeelem.focus();
            }
        });    
    });
    </script>
    <style type='text/css'>
    #calendar {
        width: 900px;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div id="dialog" class="event-dialog" title="Create Event">
        <div id="dialog-inner">
           <table border="0" cellpadding="5">
            <tr> 
                <td>               
                    <label for="name">Event Title</label>
                </td>
                <td>
                    <input type="text" name="name" id="name">
                </td>
            </tr>                
            <tr>
                <td>
                    <label for="date-start">Start Date (yyyy-mm-dd)</label>
                </td>
                <td>
                    <label id="lbl-start-time" for ="time-start">Start Time (24hours)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="date-start" id="date-start" disabled="disabled">
                </td>
                <td>
                    <input type="text" name="time" id="time-start" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="date-end">End Date (yyyy-mm-dd)</label>
                </td>
                <td>
                    <label id="lbl-end-time" for ="time-end">End Time (24hours)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="date" id="date-end">
                </td>
                <td>
                    <select name="time" id="time-end">
                    </select>
                </td>
            </tr>
            </table> 
            <input type="hidden" id="viewname" />
            <input type="hidden"  id="fc-emode" />
      </div>
    </div>
    <div id="dialog1" class="event-dialog" title="Edit Event">
        <div id="dialog-inner1">
            <table border="0" cellpadding="5">
             <tr> 
                <td>               
                    <label for="name1">Event Title</label>
                </td>
                <td>
                    <input type="text" name="name" id="name1">
                </td>
            </tr>                
            <tr>
                <td>
                    <label for="date-start1">Start Date (yyyy-mm-dd)</label>
                </td>
                <td>
                    <label id="lbl-start-time1" for ="time-start1">Start Time (24hours)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="date-start" id="date-start1">
                </td>
                <td id="container-fc-tm">
                    <input type="text" name="time" id="time-start1" disabled="disabled">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="date-end1">End Date (yyyy-mm-dd)</label>
                </td>
                <td>
                    <label id="lbl-end-time1" for ="time-end1">End Time (24hours)</label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="date" id="date-end1">
                </td>
                <td>
                    <select name="time" id="time-end1">
                    </select>
                </td>
            </tr>    
            </table>
            <input type="hidden" id="viewname1" />
            <input type="hidden"  id="fc-emode1" /> 
            <input type="hidden" id="ori-name1" />              
        </div>
    </div>
    <div id="calendar"></div>
    
<?php
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>