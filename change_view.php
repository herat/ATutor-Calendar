<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_SESSION['fc-viewname'] = "'".$_GET['viewn']."'";
$_SESSION['fc-year'] = $_GET['year'];
$_SESSION['fc-month'] = $_GET['month'];
$_SESSION['fc-date'] = $_GET['date'];
?>