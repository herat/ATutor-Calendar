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
    
    $_user_location	= 'users';
    
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    
    global $db;
    
    //Check if patch is installed or not
    require('includes/classes/events.class.php');
    
    $eventObj = new Events();
    if($eventObj->get_atutor_events($_SESSION['member_id'],$_SESSION['course_id']) == "error") {
        require(AT_INCLUDE_PATH.'header.inc.php');
        echo _AT('calendar_patch_error');
        require(AT_INCLUDE_PATH.'footer.inc.php');
        exit();
    }
    
    //Change status of email notifications
    if (isset($_GET['noti']) && $_GET['noti'] == 1) {
        $sql = "UPDATE " . TABLE_PREFIX . "calendar_notification SET status = 1 WHERE 
                 memberid = " . $_SESSION['member_id'];
        mysql_query($sql,$db);
    } else if (isset($_GET['noti']) && $_GET['noti'] == 0) {
        $sql = "UPDATE " . TABLE_PREFIX . "calendar_notification SET status = 0 WHERE 
                 memberid = " . $_SESSION['member_id'];
        mysql_query($sql,$db);
    }

    //Change view according to session value
    if (!isset($_SESSION['fc-viewname'])) {
        $view_name = '\'month\'';
    } else {
        $view_name = $_SESSION['fc-viewname'];
    }
    $session_view_on = 0;
    if (isset($_SESSION['fc-viewname'])) {
        $session_view_on = 1;
    }
    $global_js_vars = "
        var view_name               = $view_name;
        var calendar_tooltip_event  = '" . _AT('calendar_tooltip_event') . "';
        var calendar_prv_mnth       = '" . _AT('calendar_prv_mnth') . "';
        var calendar_prv_week       = '" . _AT('calendar_prv_week') . "';
        var calendar_prv_day        = '" . _AT('calendar_prv_day') . "';
        var calendar_nxt_mnth       = '" . _AT('calendar_nxt_mnth') . "';
        var calendar_nxt_week       = '" . _AT('calendar_nxt_week') . "';
        var calendar_nxt_day        = '" . _AT('calendar_nxt_day') . "';
        var calendar_tooltip_cell   = '" . _AT('calendar_tooltip_cell') . "';
        var calendar_form_title_def = '" . _AT('calendar_form_title_def') . "';
        var calendar_creat_e        = '" . _AT('calendar_creat_e') . "';
        var calendar_cancel_e       = '" . _AT('calendar_cancel_e') . "';
        var calendar_del_e          = '" . _AT('calendar_del_e') . "';
        var calendar_edit_e         = '" . _AT('calendar_edit_e') . "';
        var calendar_uneditable     = '" . _AT('calendar_uneditable') . "';
        var session_view_on         = " . $session_view_on . ";";
        if ($session_view_on == 1) {
            $global_js_vars .= "
            var fc_year                 = " . $_SESSION['fc-year'] . ";
            var fc_month                = " . $_SESSION['fc-month'] . ";
            var fc_date                 = " . $_SESSION['fc-date'] . ";
            ";
        }
    $_custom_head .= 
    '<script language="javascript" type="text/javascript">' . $global_js_vars . '</script>
    <script language="javascript" type="text/javascript" src="' . AT_BASE_HREF .
     'mods/calendar/js/index_start.js"></script>';
    $_custom_css = $_base_path . 'mods/calendar/lib/fullcalendar/fullcalendar-theme.css'; // use a custom stylesheet
    require(AT_INCLUDE_PATH.'header.inc.php');
?>
<!-- Loader wheel to indicate on-going transfer of data -->
<div style="left:50%; z-index:20000; position:absolute; top:50%" id="loader">
    <img src="mods/calendar/img/loader.gif" alt="Loading" /> 
</div>

<script language="javascript" type="text/javascript" src="<?php echo AT_BASE_HREF; ?>mods/calendar/lib/fullcalendar/fullcalendar-theme.js">
</script>

<style type='text/css'>
    #calendar {
        width: 100%;
        margin: 0 auto;
    }
</style>

<div style="float:left" id="calendar">
</div>    

<?php
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>