<?php

require_once('../PHPMailer-Lite_v5.1/class.phpmailer-lite.php');

$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)
$mail->IsMail(); // telling the class to use native PHP mail()

try {
  $mail->SetFrom('nick@velloff.com', 'Nick Velloff');
  $mail->AddAddress('nick.velloff@gmail.com');
  $mail->Subject = 'You have been invited to happy hour.';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML( '<strong>hi nick!</strong>' );
  $mail->Send();
  echo "Message Sent OK" . PHP_EOL;
  
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}

?>