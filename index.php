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
     * This file provides calendar interface.
     */
    
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require(AT_INCLUDE_PATH.'header.inc.php');
?>

<div style="left:50%; z-index:20000; position:absolute; top:50%" id="loader">
    <img src="mods/calendar/img/loader.gif" alt="Loading" /> 
</div>

<div style="float:right;width:20%" class="box">
    <fieldset>
        <legend>
            <h4>
                <?php echo _AT('calendar_options'); ?>
            </h4>
        </legend>
        <ul class="social_side_menu">
            <li>
                <a  href="mods/calendar/file_import.php"><?php echo _AT('calendar_import_file')?></a> 
            </li>
            <li>
                <a id="export" href="mods/calendar/test_export.php"><?php echo _AT('calendar_export_file')?></a>
            </li>
            <li>
                <a  href="mods/calendar/send_mail.php"><?php echo _AT('calendar_share'); ?></a>
            </li>
    <?php
        global $db;
        $query = "SELECT * FROM ".TABLE_PREFIX."calendar_google_sync WHERE userid='".$_SESSION['member_id']."'";
        $res = mysql_query($query,$db);
        
        if (mysql_num_rows($res) > 0) {
            echo "<li><a href='mods/calendar/google_connect_disconnect.php?logout=yes' target='_blank'>".
                 _AT('calendar_disconnect_gcal') . "</a></li></ul></fieldset>";
            echo "<br/><fieldset><legend><h4>". _AT('calendar_gcals') . "</h4></legend>";
            include('google_calendarlist.php');
            echo "</fieldset>";
        } else {
            echo "<li><a href='mods/calendar/google_connect_disconnect.php' target='_blank'>".
                _AT('calendar_connect_gcal'). "</a></li></ul></fieldset>";
        }
    ?>
    <br/>
    
    <fieldset>
        <legend>
            <h4>
                <?php echo _AT('calendar_internal_events'); ?>
            </h4>
        </legend>
        <div class="fc-square fc-inline-block" style="background-color:rgb(51,102,204)"></div>
        <?php echo _AT('calendar_events_persnl'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:yellow"></div>
        <?php echo _AT('calendar_events_assign_due'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:red"></div>
        <?php echo _AT('calendar_events_assign_cut'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:green"></div>
        <?php echo _AT('calendar_events_course_rel'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:maroon"></div>
        <?php echo _AT('calendar_events_course_end'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:lime"></div>
        <?php echo _AT('calendar_events_test_start'); ?>
        <br/>
        
        <div class="fc-square fc-inline-block" style="background-color:purple"></div>
        <?php echo _AT('calendar_events_test_end'); ?>
        <br/>
    </fieldset>

    <?php
        $query = "SELECT * FROM " . TABLE_PREFIX.
                 "calendar_bookmark WHERE memberid = ".$_SESSION['member_id'];
        $res   = mysql_query($query,$db);
        if (mysql_num_rows( $res ) > 0) {
    ?>
    <fieldset>
    <legend>
        <h4>
            <?php echo _AT('calendar_bookmarkd'); ?>
        </h4>
    </legend>
    <ul class="social_side_menu">
        <?php
            while ($row = mysql_fetch_assoc($res)) {
        ?>
        <li>
            <a  href='mods/calendar/index_public.php?mid=<?php echo $row['ownerid']; ?>&cid=<?php echo $row['courseid']; ?>&calname=<?php echo $row['calname']; ?>'><?php echo $row['calname'];?>
            </a>
        </li>
        <?php
            }
        ?>
        </ul>
    </fieldset>
    <?php
        }
    ?>
</div>
<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF;?>jscripts/infusion/InfusionAll.js"></script>
<?php $_custom_css = $_base_path . 'mods/calendar/fullcalendar/fullcalendar-theme.css'; // use a custom stylesheet ?>

<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-theme.js">
</script>

<link href= "<?php echo AT_BASE_HREF; ?>mods/calendar/fullcalendar/fullcalendar-theme.css" rel="stylesheet" type="text/css"/>

<script>
    $.ajaxSetup({cache: false});

    function changeview(name, year, month, datem) {
        $.ajax({url:"mods/calendar/change_view.php?viewn=" +
                name + "&year=" + year + "&month=" + month + "&date=" + datem});
    }
    
    $(document).ready(function () {
        /* Get current date for calculations. */
        var date       = new Date();
        var d          = date.getDate();
        var m          = date.getMonth();
        var y          = date.getFullYear();               
        var focusd     = false;
        var viewchangd = false;        
        var gmtHours   = -date.getTimezoneOffset() / 60;
        var activeelem;
        
        $("#export").attr("href", "mods/calendar/export.php?hrs="+gmtHours);
        
        var calendar = $('#calendar').fullCalendar({        
            defaultView: 
            <?php 
                if (!isset($_SESSION['fc-viewname'])) {
                    echo "'month'";
                } else {
                    echo $_SESSION['fc-viewname'];
                }
            ?>,
            loading: function (isLoading, view) {
                if (isLoading) {
                    $("#loader").show();
                } else {
                    $("#loader").hide();
                }
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
                var viewo = calendar.fullCalendar('getView');
                if (viewchangd) {
                    changeview(viewo.name, viewo.start.getFullYear(), 
                               viewo.start.getMonth(), viewo.start.getDate() );
                    viewchangd = false;
                }
            },
            /* Allow adding events by selecting cells. */
            selectable: true,
            selectHelper: true,            
            /* Add tooltip to events after they are rendered. */
            eventAfterRender: function(evento, elemento, viewo) {
                if (!evento.editable) {
                    var childo = elemento.children();
                    if( viewo.name == "month" )
                        childo[1].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                    else
                        childo[0].innerHTML += "<div class='fc-unedit-announce'>Uneditable event</div>";
                } else {
                    fluid.tooltip( elemento, {
                            content: function() {
                                return "<?php echo _AT('calendar_tooltip_event'); ?>";
                            }
                        });
                }
                if (focusd) {
                    if (evento.id + "" == $("#ori-name1").val()) {
                        elemento.focus();
                        focusd = false;
                    }
                }
            },
            /* Event is resized. So update db. */
            eventResize: function(event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) { 
                //get new start date, end date and send it to the db
                var newsdate = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm") + ":00"; 
                var newedate = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm") + ":00"; 
                $.get("mods/calendar/update.php",{id:event.id, start:newsdate, end:newedate, title:'',allday:'', cmd:"drag"});
            },            
            /* Add tooltip to cells. */
            viewDisplay: function(view) {
                viewchangd = true;
                $(".fc-button-firsts").each(
                   function() {
                        if ($(this).text().indexOf( 'Previous' ) >= 0) {
                            if (view.name == "month") {
                                $(this).text("<?php echo _AT('calendar_prv_mnth'); ?>");
                            } else if (view.name == "agendaWeek") {
                                $(this).text("<?php echo _AT('calendar_prv_week'); ?>");
                            } else {
                                $(this).text("<?php echo _AT('calendar_prv_day'); ?>");
                            }
                        }
                        if ($(this).text().indexOf( 'Next' ) >= 0) {
                            if (view.name == "month") {
                                $(this).text("<?php echo _AT('calendar_nxt_mnth'); ?>");
                            } else if (view.name == "agendaWeek") {
                                $(this).text("<?php echo _AT('calendar_nxt_week'); ?>");
                            } else {
                                $(this).text("<?php echo _AT('calendar_nxt_day'); ?>");
                            }
                        }
                   }
                );
                fluid.tooltip(".fc-view-" + view.name, {
                    content: function () {
                        return "<?php echo _AT('calendar_tooltip_cell'); ?>";
                    }
                });
            },
            /* Event is clicked. So open dialog for editing event. */
            eventClick: function(calevent,jsEvent,view){
                if (document.activeElement.tagName == "A") {
                    activeelem = document.activeElement;
                }    
                if (!calevent.editable) {
                    return;            
                }
                $("#fc-emode1").val("edit");
                $("#dialog1").dialog('open');                    
                //display event name in the event title input box
                $("#name1").val(calevent.title);
                
                var date = calevent.start;
                //display start date
                $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                //store event id for manipulation
                $("#ori-name1").val( calevent.id );
                
                //if allDay is true then no need to display time otherwise display time
                if (calevent.allDay == true) {
                    //disable time elements from the form
                    $("#container-fc-tm").html("<input type='text' name='time' id='time-start1' disabled='disabled' class='text ui-widget-content ui-corner-all'>");
                    
                    document.getElementById("date-end1").disabled = false;
                    document.getElementById("date-start1").disabled = false;
                    
                    $("#time-start1").addClass("fc-form-hide");
                    $("#time-end1").addClass("fc-form-hide");                     
                    $("#lbl-end-time1").addClass("fc-form-hide");
                    $("#lbl-start-time1").addClass("fc-form-hide");
                     
                    //add and set datepickers
                    $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
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
                                        
                    if (calevent.end != null) {
                        $("#date-end1").val($.fullCalendar.formatDate(calevent.end, "yyyy-MM-dd"));
                    } else {
                        $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    }
                    
                    $("#date-end1").focus(
                        function(ev) {
                            scwShow(this, ev);                            
                        }
                    );
                    $("#date-end1").click(
                        function(ev){
                            scwShow(this, ev);
                        }
                    );
                } else {
                    //enable time elements
                    $("#container-fc-tm").html("<select name='time' id='time-start1' class='text ui-widget-content ui-corner-all'></select>");
                    
                    $("#time-start1").removeClass("fc-form-hide");
                    $("#time-end1").removeClass("fc-form-hide");
                    $("#lbl-end-time1").removeClass("fc-form-hide");
                    $("#lbl-start-time1").removeClass("fc-form-hide");
                    
                    $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    $("#time-start1").val(date.getHours() + ":" + date.getMinutes());
                    
                    //add and set datepickers
                    $("#date-start1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    $("#date-start1").focus(
                        function(ev){
                            scwShow(this,ev);                            
                        }
                    );
                    $("#date-start1").click(
                        function(ev){
                            scwShow(this,ev);
                        }
                    );
                    if (calevent.end != null) {
                        $("#date-end1").val($.fullCalendar.formatDate(calevent.end, "yyyy-MM-dd"));
                    } else {
                        $("#date-end1").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    }
                    $("#date-end1").focus(
                        function(ev) {
                            scwShow(this, ev);                            
                        }
                    );
                    $("#date-end1").click(
                        function(ev) {
                            scwShow(this, ev);
                        }
                    );
                    
                    //adjust start time and end time dropdown boxes so that the current values are displayed first
                    select = $('#time-end1');
                    $("#time-end1 > option").each(function() {
                        $(this).remove();
                    });
                    var startpt = date.getHours();
                    var endpt   = calevent.end;
                    var bol     = true;
                    for (zz=0; zz<=24; zz++) {
                        if (zz == 24) {
                            select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                        } else {
                            if (bol) {
                                select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                            }
                            select.append("<option value='" + zz + ":30' >" + zz + ":30" + "</option>");
                            bol = true;                                
                        }
                    }                    
                    select.val(endpt.getHours() + ":" + endpt.getMinutes());
                    
                    select = $('#time-start1');
                    bol = true;
                    for (zz=0; zz<=24; zz++) {
                        if (zz == 24) {
                            select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                        } else {
                            if (bol) {
                                select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                            }
                            select.append("<option value='"+zz+":30' >"+zz+":30"+"</option>");                                
                            bol = true;                                
                        }
                    }                    
                    select.val(date.getHours() + ":" + date.getMinutes());
                }
                //save allDay value in hidden field
                $("#viewname1").val("" + calevent.allDay);
            },
            /* Cell is clicked. So open dialog for creating new event. */
            select: function (date, end, allDay, jsEvent, view) {                
                activeelem = document.activeElement;
                
                $("#fc-emode").val("create");                    
                $("#dialog").dialog('open');
                //display event title in the input box
                $("#name").val("<?php echo _AT("calendar_form_title_def"); ?>");
                //display start date
                $("#date-start").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                //if allday is true then disable time elements else enable them
                if (allDay == true) {
                    //month view or all-day events from other 2 views have allDay value true
                    //hide time elements
                    document.getElementById("date-end").disabled = false;                    
                    
                    $("#time-start").addClass("fc-form-hide");
                    $("#time-end").addClass("fc-form-hide");   
                    $("#lbl-end-time").addClass("fc-form-hide");
                    $("#lbl-start-time").addClass("fc-form-hide");
                    
                    //add and set date pickers
                    $("#date-end").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    $("#date-end").focus(
                        function(ev) {
                            scwShow(this, ev);                            
                        }
                    );
                    $("#date-end").click(
                        function(ev) {
                            scwShow(this, ev);
                        }
                    );                                    
                } else {
                    //enable time elements and prepare them with initial values
                    document.getElementById("date-end").disabled = true;
                    
                    $("#time-start").removeClass("fc-form-hide");
                    $("#time-end").removeClass("fc-form-hide");
                    $("#lbl-end-time").removeClass("fc-form-hide");
                    $("#lbl-start-time").removeClass("fc-form-hide");
                    $("#date-end").val($.fullCalendar.formatDate(date, "yyyy-MM-dd"));
                    $("#time-start").val(date.getHours() + ":" + date.getMinutes());
                    
                    select = $('#time-end');
                    $("#time-end > option").each( function() {
                        $(this).remove();
                    });
                    
                    var startpt = date.getHours();
                    var bol     = false;
                    if (date.getMinutes() == 0) {
                        bol = false;
                    } else {
                        startpt++;                            
                        bol = true;
                    }
                    
                    for (zz=startpt; zz<=24; zz++) {
                        if (zz == 24) {
                            select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                        } else {
                            if (bol) {
                                select.append("<option value='" + zz + ":0' >" + zz + "</option>");
                            }
                            select.append("<option value='"+zz+":30' >"+zz+":30"+"</option>");
                            bol = true;                            
                        }                            
                    }                    
                    
                    if (date.getMinutes() == 0) {
                        $("#time-end").val(date.getHours() + ":30");
                    } else {
                        $("#time-end").val((parseInt(date.getHours()) + 1) + ":0");
                    }
                }
                //save view name in hidden field
                $("#viewname").val(view.name);                    
            },
            /* Events are editable. */
            editable: false,
            /* Retrieve events from php file. */
            //events: "mods/calendar/json-events-gcal.php"
            eventSources: [
                'mods/calendar/json-events.php?all=1',
                'mods/calendar/json-events-gcal.php'
            ]
        });
        
        /*Create event jQuery dialog*/
        $("#dialog").dialog({
            autoOpen: false,
            height:   300,
            width:    500,
            modal:    true,
            buttons:  {
                '<?php echo _AT('calendar_creat_e'); ?>': function() {
                    //get start date
                    var startsplt = $("#date-start").val().split("-");
                    var ends;
                    //get end date and time
                    if ($('#viewname').val() == "month" 
                        || document.getElementById("date-end").disabled == false) {
                        ends = $("#date-end").val();
                    } else {
                        ends =  $("#date-start").val();                        
                        var timestr = $("#time-start").val().split(":");
                        var timestp = $("#time-end").val().split(":");                        
                    }
                    
                    var endsplt = ends.split("-");
                    var newid;
                    //string processing of the date values
                    if (startsplt[1].charAt(0) == '0') {
                        startsplt[1] = startsplt[1].charAt(1);
                    }
                    if (endsplt[1].charAt(0) == '0') {
                        endsplt[1] = endsplt[1].charAt(1);
                    }
                    if (startsplt[2].charAt(0) == '0') {
                        startsplt[2] = startsplt[2].charAt(1);
                    }
                    if (endsplt[2].charAt(0) == '0') {
                        endsplt[2] = endsplt[2].charAt(1);
                    }
                    //first send new events to db, db will return id and then display events in the calendar
                    if ($('#viewname').val() == "month" 
                        || document.getElementById("date-end").disabled == false) {
                        
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                            parseInt(startsplt[1])-1,
                                            parseInt(startsplt[2])),
                                            "yyyy-MM-dd HH:mm") + ":00";
                        var mysqlendd   = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                            parseInt(endsplt[1])-1,
                                            parseInt(endsplt[2])),
                                            "yyyy-MM-dd HH:mm") + ":00";
                        
                        $.get(
                            "mods/calendar/update.php",
                            {id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"true"}, 
                            function(data) {
                                calendar.fullCalendar("refetchEvents");
                            }
                        );
                        $(this).dialog("close");
                        activeelem.focus();
                    } else {
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                            parseInt(startsplt[1])-1,
                                            parseInt(startsplt[2]),
                                            timestr[0],
                                            timestr[1]), "yyyy-MM-dd HH:mm") + ":00";
                        var mysqlendd = $.fullCalendar.formatDate( new Date(parseInt(endsplt[0]),
                                          parseInt(endsplt[1])-1,
                                          parseInt(endsplt[2]),
                                          timestp[0],
                                          timestp[1]), "yyyy-MM-dd HH:mm") + ":00";
                        $.get(
                            "mods/calendar/update.php",
                            {id:'',start:mysqlstartd, end:mysqlendd, title:$("#name").val(), cmd:"create",allday:"false"}, function(data) {
                                calendar.fullCalendar('refetchEvents');
                            }
                        );
                        $(this).dialog('close');
                        activeelem.focus();
                    }
                },
                <?php echo _AT('calendar_cancel_e'); ?>: function() {
                    $(this).dialog('close');
                    activeelem.focus();
                }
            },
            close: function() {
                scwHide();
                if (activeelem != null) {
                    activeelem.focus();
                }
            }
        });        
        /* Edit event dialog */
        $("#dialog1").dialog({
            autoOpen: false,
            height:   350,
            width:    700,
            modal:    true,
            buttons:  {
                '<?php echo _AT('calendar_del_e'); ?>': function() {
                    if ($("#ori-name1").val().indexOf("http") >= 0) {
                        $.get("mods/calendar/google_calendar_update.php", 
                              {id:$("#ori-name1").val(), cmd:"delete"});
                    } else {
                        //delete event from db
                        $.get("mods/calendar/update.php",
                            {id:$("#ori-name1").val(), start:"", end:"", title:"", allday:"", cmd:"delete"}
                        );
                    }
                    calendar.fullCalendar("removeEvents",
                      function(ev) {
                        //remove event data from hidden elements
                        $(".fc-month-vhidden").each(
                            function(index) {
                                if ($(this).parent().prev().prev().text().indexOf( '"' +ev.id +'"' ) >= 0) {
                                    $(this).parent().prev().prev().html("");
                                    $(this).parent().prev().html("");
                                }
                            }
                        );
                        $(".fc-cell-date").each(
                            function(index) {
                                if ($(this).prev().text().indexOf( '"' +ev.id +'"' ) >= 0) {
                                    $(this).prev().html("");
                                    $(this).next().html("");
                                }
                            }
                        );
                        $(".fc-allday-bhidden").each(
                            function(index) {
                                if ($(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                                    $(this).prev().prev().html("");
                                    $(this).prev().html("");
                                }
                            }
                        );
                        //matching event found for deleting
                        if (ev.id == $("#ori-name1").val()) {
                            return true;
                        }
                      }                    
                    );
                    calendar.fullCalendar('refetchEvents');
                    $(this).dialog('close');
                },
                '<?php echo _AT('calendar_edit_e'); ?>': function() {
                //get new values of time and date
                var startsplt = $("#date-start1").val().split("-");
                var ends;
                if ($('#viewname1').val() == "true") {
                    ends = $("#date-end1").val();
                } else {
                    ends =  $("#date-start1").val();
                        
                    var timestr = $('#time-start1').val().split(':');
                    var timestp = $('#time-end1').val().split(':');                        
                }
                
                var endsplt = ends.split("-");
                
                if (startsplt[1].charAt(0) == '0') {
                    startsplt[1] = startsplt[1].charAt(1);
                }
                if (endsplt[1].charAt(0) == '0') {
                    endsplt[1] = endsplt[1].charAt(1);
                }
                if (startsplt[2].charAt(0) == '0') {
                    startsplt[2] = startsplt[2].charAt(1);
                }
                if (endsplt[2].charAt(0) == '0') {
                    endsplt[2] = endsplt[2].charAt(1);
                }
                //if allDay is true then only use dates otherwise use both dates and time values
                if ($('#viewname1').val() == "true") {
                    var sdat = new Date(parseInt(startsplt[0]),
                                        parseInt(startsplt[1])-1,
                                        parseInt(startsplt[2]));
                    var edat = new Date(parseInt(endsplt[0]),
                                        parseInt(endsplt[1])-1,
                                        parseInt(endsplt[2]));
                    if (edat < sdat) {
                        alert("Enter valid dates");
                        $(this).dialog('close');
                        activeelem.focus();
                        return;
                    }
                } else {
                    var sdat = new Date(parseInt(startsplt[0]),
                                        parseInt(startsplt[1])-1,
                                        parseInt(startsplt[2]),
                                        timestr[0],
                                        timestr[1]);
                    var edat = new Date(parseInt(endsplt[0]),
                                        parseInt(endsplt[1])-1,
                                        parseInt(endsplt[2]),
                                        timestp[0],
                                        timestp[1]);
                    if (edat < sdat) {
                        alert("Enter valid dates");
                        $(this).dialog('close');
                        activeelem.focus();
                        return;
                    }
                }
                //remove old event data
                calendar.fullCalendar("removeEvents",
                    function(ev) {
                        $(".fc-month-vhidden").each(
                            function(index){
                                if ($(this).parent().prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                                    $(this).parent().prev().prev().html("");
                                    $(this).parent().prev().html("");
                                }
                            }
                        );
                        $(".fc-cell-date").each(
                            function(index){
                                if ($(this).prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                                    $(this).prev().html("");
                                    $(this).next().html("");
                                }
                            }
                        );
                        $(".fc-allday-bhidden").each(
                            function(index){
                                if ($(this).prev().prev().text().indexOf( '"'+ev.id+'"' ) >= 0) {
                                    $(this).prev().prev().html("");
                                    $(this).prev().html("");
                                }
                            }
                        );
                        if (ev.id == $("#ori-name1").val()) {
                            return true;
                        }                        
                    }
                );
                //add edited event as a new event and also update db values
                if ($('#viewname1').val() == "true") {
                    if ($("#ori-name1").val().indexOf('http') >= 0) {
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                           parseInt(endsplt[1])-1,
                                                                           parseInt(endsplt[2])),
                                                                           "u");
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                             parseInt(startsplt[1])-1,
                                                                             parseInt(startsplt[2])),
                                                                             "u");
                        $.get(
                            "mods/calendar/google_calendar_update.php",
                            {
                            id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd, 
                            title:$("#name1").val(), cmd:"update"
                            },
                            function(data) {
                            calendar.fullCalendar('refetchEvents'); 
                            focusd = true;
                            }
                        );
                    }
                    else
                    {
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                                    parseInt(endsplt[1])-1,
                                                                                    parseInt(endsplt[2])),
                                                                                    "yyyy-MM-dd HH:mm") + ":00";
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                                      parseInt(startsplt[1])-1,
                                                                                      parseInt(startsplt[2])),
                                                                                      "yyyy-MM-dd HH:mm") + ":00";
                        $.get(
                            "mods/calendar/update.php",
                            {
                            id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd, 
                            title:$("#name1").val(), cmd:"update",allday:"true"
                            },
                            function(data) {
                            calendar.fullCalendar('refetchEvents'); 
                            focusd = true;
                            }
                        );
                    }
                    $(this).dialog('close');
                } else {
                    if ($("#ori-name1").val().indexOf('http') >= 0) {
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                            parseInt(endsplt[1])-1,
                                                                            parseInt(endsplt[2]),
                                                                            timestp[0],
                                                                            timestp[1]), 
                                                                            "u");
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                            parseInt(startsplt[1])-1,
                                                                            parseInt(startsplt[2]),
                                                                            timestr[0],
                                                                            timestr[1]),
                                                                            "u");
                        $.get(
                            "mods/calendar/google_calendar_update.php",
                            {
                            id:$("#ori-name1").val(), start:mysqlstartd, end:mysqlendd,
                            title:$("#name1").val(), cmd:"update"
                            },
                            function(data) {
                                    calendar.fullCalendar('refetchEvents');
                                    focusd = true;
                            }
                        );
                    } else {
                        var mysqlendd = $.fullCalendar.formatDate(new Date(parseInt(endsplt[0]),
                                                                           parseInt(endsplt[1])-1,
                                                                           parseInt(endsplt[2]),
                                                                           timestp[0],
                                                                           timestp[1]),
                                                                           "yyyy-MM-dd HH:mm") + ":00";
                        var mysqlstartd = $.fullCalendar.formatDate(new Date(parseInt(startsplt[0]),
                                                                             parseInt(startsplt[1])-1,
                                                                             parseInt(startsplt[2]),
                                                                             timestr[0],
                                                                             timestr[1]),
                                                                             "yyyy-MM-dd HH:mm") + ":00";
                        $.get(
                            "mods/calendar/update.php",
                            {
                            id:$("#ori-name1").val(),start:mysqlstartd, end:mysqlendd,
                            title:$("#name1").val(), cmd:"update",allday:"false"},
                            function(data) {
                                calendar.fullCalendar('refetchEvents');
                                focusd = true;
                            }
                        );
                    }
                    $(this).dialog('close');
                }
              },
              <?php echo _AT('calendar_cancel_e'); ?>: function() {
                    $(this).dialog('close');
                    activeelem.focus();
              }
            },
            close: function() {
                scwHide();
                if (activeelem != null) {
                    activeelem.focus();
                }
            }
        });
        <?php 
            if (isset( $_SESSION['fc-viewname'] )) {
                echo "calendar.fullCalendar('gotoDate',". $_SESSION['fc-year'] . ",".
                     $_SESSION['fc-month'] . "," . $_SESSION['fc-date'] . ");";
            }
         ?>
    });
    function refreshevents() {
        $("#calendar").fullCalendar("refetchEvents");
    }    
</script>

<style type='text/css'>
    #calendar {
        width: 75%;
        margin: 0 auto;
    }
</style>

<div id="dialog" class="event-dialog initial-hide" title="Create Event">
    <div id="dialog-inner">
       <table border="0" cellpadding="5">
        <tr> 
            <td>               
                <label for="name"><?php echo _AT('calendar_form_title'); ?></label>
            </td>
            <td>
                <input type="text" name="name" id="name" 
                onclick="if(this.value == '<?php echo _AT("calendar_form_title_def"); ?>') { this.value = ''; }" 
                onfocus="if(this.value == '<?php echo _AT("calendar_form_title_def"); ?>') { this.value = ''; }"/>
            </td>
        </tr>                
        <tr>
            <td>
                <label for="date-start"><?php echo _AT('calendar_form_start_d'); ?></label>
            </td>
            <td>
                <label id="lbl-start-time" for ="time-start"><?php echo _AT('calendar_form_start_t'); ?></label>
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
                <label for="date-end"><?php echo _AT('calendar_form_end_d'); ?></label>
            </td>
            <td>
                <label id="lbl-end-time" for ="time-end"><?php echo _AT('calendar_form_end_t'); ?></label>
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
<div id="dialog1" class="event-dialog initial-hide" title="Edit Event">
    <div id="dialog-inner1">
        <table border="0" cellpadding="5">
         <tr> 
            <td>               
                <label for="name1"><?php echo _AT('calendar_form_title'); ?></label>
            </td>
            <td>
                <input type="text" name="name" id="name1">
            </td>
        </tr>                
        <tr>
            <td>
                <label for="date-start1"><?php echo _AT('calendar_form_start_d'); ?></label>
            </td>
            <td>
                <label id="lbl-start-time1" for ="time-start1"><?php echo _AT('calendar_form_start_t'); ?></label>
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
                <label for="date-end1"><?php echo _AT('calendar_form_end_d'); ?></label>
            </td>
            <td>
                <label id="lbl-end-time1" for ="time-end1"><?php echo _AT('calendar_form_end_t'); ?></label>
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

<div style="float:left" id="calendar">
</div>    
<?php
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>