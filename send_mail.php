<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

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
$to      = 'herat_000@yahoo.co.in';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: abc@gmail.com' . "\r\n" .
    'Reply-To: abc@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
header('Location: index.php');
exit;

?>