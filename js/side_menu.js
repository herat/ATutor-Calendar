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
        events: path+"mods/calendar/json-events.php?mini=1"
    }); 
});