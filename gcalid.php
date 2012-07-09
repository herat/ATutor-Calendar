<?php
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');

    $newid = $_GET['calid'];
    $mode = $_GET['mode'];

    global $db;
    if( $mode == "add" )
    {
        $query = "SELECT * FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
        $res = mysql_query($query);
        $rowval = mysql_fetch_assoc($res);
        $prevval = $rowval['calids'];
        $prevval .= htmlspecialchars($newid).",";
        $query = "UPDATE ".TABLE_PREFIX."google_sync SET calids='".$prevval."' WHERE userid='".$_SESSION['member_id']."'";
        mysql_query($query,$db);
    }
    else
    {
        $query = "SELECT * FROM ".TABLE_PREFIX."google_sync WHERE userid='".$_SESSION['member_id']."'";
        $res = mysql_query($query);
        $rowval = mysql_fetch_assoc($res);
        $prevval = $rowval['calids'];
        $prevval = str_replace(htmlspecialchars($newid).",","",$prevval);
        $query = "UPDATE ".TABLE_PREFIX."google_sync SET calids='".$prevval."' WHERE userid='".$_SESSION['member_id']."'";
        mysql_query($query,$db);
    }
?>