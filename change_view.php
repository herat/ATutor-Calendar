<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$viewname = $_GET['viewn'];
$_SESSION['fc-viewname'] = $viewname;
?>