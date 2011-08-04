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
		$inviteLookup = new Invite();
		
		$eventHasLocations = count($event->GetLocationList()) > 0;
		
		// only send out decided emails if event has locations added, otherwise abort
		if (!$eventHasLocations) return;
		
		$participants = $event->GetParticipantList();
		for ($j=0; $j<count($participants); $j++)
		{
			/** @var $participant Participant */
			$receiver = $participants[$j];
			
			// skip any user that has declined the event
			if ($this->getHasDeclinedEvent($event, $receiver->email)) continue;
			
			$creatorEmail = $event->creatorId;
			
			$creatorLookup = new Participant();
			$creatorList = $creatorLookup->GetList( array( array("email", "=", $creatorEmail ) ) );
				
			/** @var $creator Participant */
			$creator = $creatorList[0];
			$receiverEmail = $receiver->email;
			
			// get the receivers invite if one exists (that has not been removed), to check if pairing is still needed
			$token = '';
			$needsPair = 0;			
			$inviteList = $event->GetInviteList( array( array("inviteeId", "=", $receiverEmail ), array("pending", "=", 1 ) ) );
			
			if (count($inviteList) > 0)
			{
				/** @var $invite Invite */
				$invite = $inviteList[0];
				$token = $invite->token;
				$needsPair = 1;
			}
			
			$bodyGen = new DecidedEmail();
			$body = $bodyGen->getDecidedHTMLBody($creator, $event, $token, $needsPair);
			
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
		// get the pending invite list
		$inviteLookup = new Invite();
		$inviteList = $inviteLookup->GetList( array( array("sent", "=", 0 ) ) );		
		
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
			$body = $bodyGen->getInviteHTMLBody($creator, $event, $invite->token, $invite->pending);
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
			  
			  //$invite->pending = 0;
			  $invite->sent = 1;
			  $invite->Save();
			  
			} catch (phpmailerException $e) {
			  echo $e->errorMessage(); //Pretty error messages from PHPMailer
			} catch (Exception $e) {
			  echo $e->getMessage(); //Boring error messages from anything else!
			}
		}
	}
	
	/**
	* Determines if the user has accepted the event
	* @param Event $event
	* @param string $email
	* @return Boolean
	*/
	function getHasAcceptedEvent(&$event, $email)
	{
		$acceptedParticipantList = explode(',', $event->acceptedParticipantList);
		$hasAccepted = in_array($email, $acceptedParticipantList);
	
		return $hasAccepted;
	}
	
	/**
	* Determines if the user has declined the event
	* @param Event $event
	* @param string $email
	* @return Boolean
	*/
	function getHasDeclinedEvent(&$event, $email)
	{
		$declinedParticipantList = explode(',', $event->declinedParticipantList);
		$hasDeclined = in_array($email, $declinedParticipantList);
	
		return $hasDeclined;
	}
}

?>