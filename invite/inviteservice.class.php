<?php

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}
require_once('../PHPMailer-Lite_v5.1/class.phpmailer-lite.php');

require_once '../util/request.base.php';

require_once 'invite_v2.php';

date_default_timezone_set('GMT');

class InviteService extends ReqBase
{
	
	function InviteService()
	{
		parent::__construct();
	}
	/**
	 * Processes the Queued Feed Message Notifications
	 */
	function dispatchUnkownEmailInvites()
	{
		$base_invite_url = $GLOBALS['configuration']['base_invite_url'];
		
		// get the pending invite list
		$inviteLookup = new Invite();
		$inviteList = $inviteLookup->GetList( array( array("pending", "=", 1 ) ) );
		for ($i=0; $i<count($inviteList); $i++)
		{
			/** @var $invite Invite */
			$invite = $inviteList[$i];
			$event = $invite->GetEvent();
			$senderEmail = $invite->inviterId;
			
			$senderLookup = new Participant();
			$senderList = $senderLookup->GetList( array( array("email", "=", $senderEmail ) ) );
			
			/** @var $sender Participant */
			$sender = $senderList[0];
			$receiverEmail = $invite->inviteeId;
			
			$bodyGen = new InviteEmail();
			$body = $bodyGen->getInviteHTMLBody($sender, $event, $invite->token);
			$senderName = $bodyGen->getFriendlyName($sender);
			
			$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)

			$mail->IsMail(); // telling the class to use native PHP mail()
			
			try {
			  //$mail->SetFrom('beta@unitedweego.com', 'Nick Velloff');
			  $mail->AddAddress($receiverEmail);
			  $mail->Subject = 'You have been invited to ' . $event->eventTitle;
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			  $mail->MsgHTML( $body );
			  $mail->AddAttachment('images/email_header_01.png');      // attachment
			  $mail->Send();
			  echo "Message Sent OK" . PHP_EOL;
			  
			  $invite->pending = 0;
			  $invite->Save();
			  
			} catch (phpmailerException $e) {
			  echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
			  echo $e->getMessage(); //Boring error messages from anything else!
			}
		}
	}
}

?>