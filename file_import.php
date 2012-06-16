<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="mods/calendar/import_db.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>

<?php
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>