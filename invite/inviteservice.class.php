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
require_once 'decided_v1.php';

date_default_timezone_set('GMT');

class InviteService extends ReqBase
{
	
	function InviteService()
	{
		parent::__construct();
	}
	
	
	/**
	* Send out the event decided email to participants
	* @param Event $event
	*/
	function dispatchEventDecidedEmailForEvent(&$event)
	{
		$eventHasLocations = count($event->GetLocationList()) > 0;
		
		// only send out decided emails if event has locations added, otherwise abort
		if (!$eventHasLocations) return;
		
		$participants = $event->GetParticipantList();
		for ($j=0; $j<count($participants); $j++)
		{
			/** @var $participant Participant */
			$receiver = $participants[$j];
			$creatorEmail = $event->creatorId;
			
			$creatorLookup = new Participant();
			$creatorList = $creatorLookup->GetList( array( array("email", "=", $creatorEmail ) ) );
				
			/** @var $creator Participant */
			$creator = $creatorList[0];
			$receiverEmail = $receiver->email;
			
			$bodyGen = new DecidedEmail();
			$body = $bodyGen->getDecidedHTMLBody($creator, $event);
			
			$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)
			$mail->IsMail(); // telling the class to use native PHP mail()
			
			try {
				$mail->SetFrom('beta@unitedweego.com', 'Weego Admin');
				$mail->AddAddress($receiverEmail);
				$mail->Subject = $event->eventTitle . ' has been decided!';
				$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
				$mail->MsgHTML( $body );
				$mail->AddAttachment('images/email_header_01.png');      // attachment
				$mail->Send();
				echo "Message Sent OK" . PHP_EOL;
			} catch (phpmailerException $e) {
				echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
				echo $e->getMessage(); //Boring error messages from anything else!
			}
			
		}
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
			$creatorEmail = $event->creatorId;
			
			$creatorLookup = new Participant();
			$creatorList = $creatorLookup->GetList( array( array("email", "=", $creatorEmail ) ) );
			
			/** @var $creator Participant */
			$creator = $creatorList[0];
			$receiverEmail = $invite->inviteeId;
			
			$bodyGen = new InviteEmail();
			$body = $bodyGen->getInviteHTMLBody($creator, $event, $invite->token);
			//$creatorName = $bodyGen->getFriendlyName($creator);
			
			$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)
			$mail->IsMail(); // telling the class to use native PHP mail()
			
			try {
			  $mail->SetFrom('beta@unitedweego.com', 'Weego Admin');
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