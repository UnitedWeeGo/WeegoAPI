<?php 

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}
require_once '../util/request.base.php';

class InviteEmail extends ReqBase
{
	
	function InviteEmail()
	{
		
	}
	/**
	* Get the invite html message body
	* @param Participant $sender
	* @param Event $event
	* @return string
	*/
	function getInviteHTMLBody($sender, $event)
	{
		$base_invite_url = $GLOBALS['configuration']['base_invite_url'];
		$senderAvatarURL = $sender->avatarURL;
		$senderFriendlyName = $this->getFriendlyName($sender);
		$eventTitle = $event->eventTitle;
		$eventDate = $event->eventDate;
		$eventExpireDate = $event->eventExpireDate;
		$winningLocation = $this->determineWinningLocationForEvent($event);
		$name = $winningLocation->name;
		$formatted_address = $winningLocation->formatted_address;
		$message = 
<<< EOT
<body style="margin: 10px;">
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
<div align="center"><img src="images/email_header_01.png" style="height: 170px; width: 600px"></div><br>
<br>
&nbsp;<strong>You have been invited to an event!</strong><br>
<br>
We are in beta.
<br />
avatar: <img src="$senderAvatarURL">
<br />
sender name: $senderFriendlyName<br />
event title: $eventTitle<br />
event date: $eventDate<br />
event voting end: $eventExpireDate<br />
winning location name: $name<br />
winning location address: $formatted_address<br />
<a href="$base_invite_url">Click here to Sign-Up for the Weego Private Beta</a><br />
</div>
</body>
EOT;
		return $message;
	}
	
	/**
	* Returns a firendly name string
	* @param Participant $participant
	* @return string
	*/
	function getFriendlyName(&$participant)
	{
		$fName = $participant->firstName;
		$lName = $participant->lastName;
		$hasFName = strlen($fName) > 0;
		$hasLName = strlen($lName) > 0;
		if ($hasFName && $hasLName)
		{
			return $fName . " " . $lName;
		}
		else if ($hasFName)
		{
			return $fName;
		}
		else if ($hasLName)
		{
			return $lName;
		}
		else
		{
			return $participant->email;
		}
	}
	
}
?>