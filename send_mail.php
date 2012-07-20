<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

	if ($_POST['cancel']) {
		
		header('Location: index.php');
		exit;
	} 
	else if ($_POST['submit']) {
	
	$missing_fields = array();

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		$missing_fields[] = _AT('to');
	}
	
	if( $_POST['to'] == 3 && $_POST['emails'] == '' ) {
		$missing_fields[] = 'email';		
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	
	if( $_POST['to'] == 3 && $_POST['emails'] != '' ) {
		if( filter_var($_POST['emails'], FILTER_VALIDATE_EMAIL) ) {
		}
		else {
			$msg->addError('INVALID_EMAIL');
		}
	}
	
	if (!$msg->containsErrors()) {
		if ($_POST['to'] == 1) {
			// choose all members associated with course
			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id IN (SELECT member_id FROM ".TABLE_PREFIX.
				"course_enrollment WHERE course_id=".$_SESSION['course_id']." and member_id <> ".$_SESSION['member_id']." )";
		} else if ($_POST['to'] == 2) {
			// choose particular login
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id = ".$_POST['selection'];
		} else {
			//user entered email address
		}
		
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$mail = new ATutorMailer;
		
		if( $_POST['to'] == 1 || $_POST['to'] == 2 )
		{
			$result = mysql_query($sql,$db);
			while ($row = mysql_fetch_assoc($result)) 
			{
				$mail->AddBCC($row['email']);
			}
	
		}
		else
		{
			$mail->AddBCC($_POST['emails']);
		}
		if( isset($_POST['subject']) && $_POST['subject'] != "" )
			$calname = $_POST['subject'];
		else
			$calname = "Calendar of ".get_display_name($_SESSION['member_id']);
			
		$body = "";
		$body .= "<a target='_blank' href = '".AT_BASE_HREF."mods/calendar/shared_cal.php?mid=".$_SESSION['member_id'].
		"&email=1&calname=".$calname."'>View shared calendar</a>";
		//$body .= AT_BASE_HREF."mods/calendar/shared_cal.php?mid=".$_SESSION['member_id']."&email=1&calname=TestCal";
		//echo $body;
		//exit;
				
		$mail->From     = $_config['contact_email'];
		$mail->FromName = $_config['site_name'];
		$mail->AddAddress($_config['contact_email']);
		$mail->Subject = $stripslashes("Shared Calendar");
		$mail->Body    = $body;

		if(!$mail->Send()) 
		{
		   //echo 'There was an error sending the message';
		   $msg->printErrors('SENDING_ERROR');
		   exit;
		}
		unset($mail);
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: index.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
?>

<style type="text/css">
	.fc-forme-hide{
		display:none;
	}
</style>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<div class="input-form">
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo  _AT('to'); ?><br />
	    <input type="radio" name="to" value="1" id="all" <?php if( $_POST['to'] == 1 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="all">Send to all</label>
        <input type="radio" name="to" value="2" id="list" <?php if( $_POST['to'] == 2 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').removeClass('fc-forme-hide');" />
        <label for="list">Select from list</label>
        <input type="radio" name="to" value="3" id="manual" <?php if( $_POST['to'] == 3 ) echo "checked = 'checked'"; ?>
        onclick="$('#emails').removeClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="manual">Enter email address</label>
	</div>
    
    <div class="row">
    	
        <span id="emails" <?php if( $_POST['to'] != 3 ) echo "class='fc-forme-hide'"; ?> >
        <label for="emails1">Enter email:</label>
    	<input type="text" id="emails1" name="emails" value="<?php echo $_POST['emails']; ?>"/>
        </span>
        
        <span id="selection" <?php if( $_POST['to'] != 2 ) echo "class='fc-forme-hide'"; ?>>
        <label for="selection1">Select member:</label>
        <select name="selection" id="selection1">
        	<?php
				global $db;
				$sql = "SELECT login,member_id FROM ".TABLE_PREFIX."members WHERE member_id IN (SELECT member_id FROM ".TABLE_PREFIX.
				"course_enrollment WHERE course_id=".$_SESSION['course_id']." and member_id <> ".$_SESSION['member_id']." )";
				$result = mysql_query($sql, $db);
				while ($row = mysql_fetch_assoc($result)) {
					echo "<option value='".$row['member_id']."'>". $row['login'] ."</option>";					
				}
			?>
        </select>
        </span>
    
    </div>

	<div class="row">
		<label for="subject">Title of Calendar</label><br />
		<input type="text" name="subject" size="40" id="subject" value="<?php echo $_POST['subject']; ?>" />
	</div>	

	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('send'); ?>" accesskey="s" /> 
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
</div>
</form>

<?php 
require(AT_INCLUDE_PATH.'footer.inc.php'); 
/*require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

$mail = new ATutorMailer;
$mail->From     = '07bit012@nirmauni.ac.in';
$mail->FromName = 'admin';
$mail->AddAddress('07bit012@nirmauni.ac.in');
$mail->AddBCC('herat_000@yahoo.co.in');
$mail->Subject = 'Subject- ATutor';
$mail->Body    = 'BoDY';

if(!$mail->Send()) {
   //echo 'There was an error sending the message';
   $msg->printErrors('SENDING_ERROR');
   exit;
}
unset($mail);

$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');*/
/*$to      = 'herat_000@yahoo.co.in';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: abc@gmail.com' . "\r\n" .
    'Reply-To: abc@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
header('Location: index.php');
exit; */
?>