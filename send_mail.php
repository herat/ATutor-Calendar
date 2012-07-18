<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if ($_POST['cancel']) {
	$msg->addFeedback('CANCELLED');

	header('Location: index.php');
	exit;
} else if ($_POST['submit']) {
	$missing_fields = array();

	$_POST['subject'] = trim($_POST['subject']);
	$_POST['body'] = trim($_POST['body']);

	if (($_POST['to'] == '') || ($_POST['to'] == 0)) {
		$missing_fields[] = _AT('to');
	}

	if ($_POST['subject'] == '') {
		$missing_fields[] = _AT('subject');
	}

	if ($_POST['body'] == '') {
		$missing_fields[] = _AT('body');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}
	if (!$msg->containsErrors()) {
		if ($_POST['to'] == 1) {
			// choose all instructors
			$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_INSTRUCTOR;
		} else if ($_POST['to'] == 2) {
			// choose all students
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_STUDENT;
		} else {
			// choose all members
			$sql 	= "SELECT * FROM ".TABLE_PREFIX."members WHERE status = ".AT_STATUS_INSTRUCTOR." OR status = ".AT_STATUS_STUDENT;
		}
		
		$result = mysql_query($sql,$db);

		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

		$mail = new ATutorMailer;

		while ($row = mysql_fetch_assoc($result)) {
			$mail->AddBCC($row['email']);
		}


		$mail->From     = $_config['contact_email'];
		$mail->FromName = $_config['site_name'];
		$mail->AddAddress($_config['contact_email']);
		$mail->Subject = $stripslashes($_POST['subject']);
		$mail->Body    = $stripslashes($_POST['body']);

		if(!$mail->Send()) {
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

$onload = 'document.form.subject.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members ORDER BY login";
$result = mysql_query($sql,$db);
$row	= mysql_fetch_array($result);
if ($row['cnt'] == 0) {
	$msg->printErrors('NO_MEMBERS');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
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
	    <input type="radio" name="to" value="1" id="all"
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="all">Send to all</label>
        <input type="radio" name="to" value="2" id="list"
        onclick="$('#emails').addClass('fc-forme-hide');$('#selection').removeClass('fc-forme-hide');" />
        <label for="list">Select from list</label>
        <input type="radio" name="to" value="3" id="manual" 
        onclick="$('#emails').removeClass('fc-forme-hide');$('#selection').addClass('fc-forme-hide');" />
        <label for="manual">Enter email address</label>
	</div>
    
    <div class="row">
    	<input type="text" class="fc-forme-hide" id="emails" name="emails" />
        <select class="fc-forme-hide" name="selection" id="selection">
        	<option value="1">ABC</option>
        </select>
    </div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="subject">Title of Calendar</label><br />
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