<?php
    /**
     * This file used to save calendar's state
     * that is current view and starting date.
     * So that when user refreshes the page, he/she
     * will get the same state.
     */
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');

    $_SESSION['fc-viewname'] = "'".$_GET['viewn']."'";
    $_SESSION['fc-year'] = $_GET['year'];
    $_SESSION['fc-month'] = $_GET['month'];
    $_SESSION['fc-date'] = $_GET['date'];
    
    exit();
?>