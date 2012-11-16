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
     * This file provides UI for uploading ics file.
     */
    define('AT_INCLUDE_PATH', '../../include/');
    require(AT_INCLUDE_PATH.'vitals.inc.php');
    require(AT_INCLUDE_PATH.'header.inc.php');
?>
<div class="input-form">
    <br />
    <form action="mods/calendar/import_ics.php" method="post" enctype="multipart/form-data">
        <label for="file"><?php echo _AT('calendar_upload_file'); ?></label>
        <input type="file" name="file" id="file" />
        <br /> <br /> <br />
        <input type="submit" name="submit" value="<?php echo _AT('calendar_submit'); ?>" />
    </form>
    <form action="mods/calendar/index.php" method="post">
        <input type="submit" name="cancel" value="<?php echo _AT('calendar_cancel_e'); ?>" />
    </form>
    <br />
</div>
<?php
    require(AT_INCLUDE_PATH.'footer.inc.php');
?>