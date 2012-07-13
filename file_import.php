<?php
    /**
     * This file provides UI for uploading ics file.
     */
    define('AT_INCLUDE_PATH', '../../include/');
    require (AT_INCLUDE_PATH.'vitals.inc.php');
    require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="mods/calendar/import_db.php" method="post" enctype="multipart/form-data">
    <label for="file"><?php echo _AT("at_cal_upload_file"); ?></label>
    <input type="file" name="file" id="file" />
    <br />
    <input type="submit" name="submit" value="<?php echo _AT("at_cal_submit"); ?>" />
</form>

<?php
    require (AT_INCLUDE_PATH.'footer.inc.php');
?>