<?php
    /* This file returns the string based on language token. */
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    $token = $_GET["token"];
    echo _AT( $token );
?>