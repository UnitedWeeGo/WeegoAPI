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
		$eventDate = $this->getFormattedTime($event->eventDate);
		$eventExpireDate = $this->getFormattedTime($event->eventExpireDate);
		$winningLocation = $this->determineWinningLocationForEvent($event);
		$name = $winningLocation ? $winningLocation->name : 'no location name';
		$formatted_address = $winningLocation ? $winningLocation->formatted_address : 'no location address';
		$message = 
<<< EOT
<body bgcolor="#F3F3F3">
<div align="center" style="width: 600px; font-family: Arial, Helvetica, sans-serif; font-size: 10px;">
<div align="center"><img src="images/email_header_01.png" style="height: 170px; width: 600px"></div>
<table width="600" border="0" cellspacing="5" cellpadding="10" bgcolor="#FFF">
	<tr>
		<td colspan="2">
			<br />
			<span style="font-size:32px; color:#666;">You have been invited to...</span>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table width="310" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="13" height="13"><img src="http://www.unitedweego.com/email_images/event_bg_1_tl.png" width="13" height="13"></td>
					<td width="*" height="13"><img src="http://www.unitedweego.com/email_images/event_bg_1_tm.png" width="100%" height="13"></td>
					<td width="13" height="13"><img src="http://www.unitedweego.com/email_images/event_bg_1_tr.png" width="13" height="13"></td>
				</tr>
				<tr>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_1_ml.png" width="13" height="100%"></td>
					<td bgcolor="#F3F3F3">
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" width="60"><img src="$senderAvatarURL" border="1" style="border-color:#CCC"></td>
								<td valign="top">
									<span style="font-size:1.2em; color:#666">$senderFriendlyName</span><br />
									<span style="font-size:1.8em; color:#333; font-weight:bold">$eventTitle</span><br />
									<span style="font-size:1.2em; color:#666">$eventDate</span><br />
									<span style="font-size:1.1em; color:#666; font-weight:bold">Voting ends at </span><span style="font-size:1.1em; color:#690; font-weight:bold">$eventExpireDate</span><br /><br />
								</td>
							</tr>
						</table>
					</td>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_1_mr.png" width="13" height="100%"></td>
				</tr>
				<tr>
					<td width="13" height="1"><img src="http://www.unitedweego.com/email_images/event_bg_2_ml.png" width="13" height="1"></td>
					<td bgcolor="#DEDEDE"></td>
					<td width="13" height="1"><img src="http://www.unitedweego.com/email_images/event_bg_2_mr.png" width="13" height="1"></td>
				</tr>
				<tr>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_3_ml.png" width="13" height="100%"></td>
					<td bgcolor="#F9F9F9">
						<br />
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" width="60"><img src="http://www.unitedweego.com/email_images/button_unlike_default.png" width="50" height="50"></td>
								<td valign="top">
									<span style="font-size:1.8em; color:#333; font-weight:bold">$name</span><br />
									<span style="font-size:1.2em; color:#666">$formatted_address</span><br />
								</td>
							</tr>
						</table>
					</td>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_3_mr.png" width="13" height="100%"></td>
				</tr>
				<tr>
					<td width="13" height="14"><img src="http://www.unitedweego.com/email_images/event_bg_3_bl.png" width="13" height="14"></td>
					<td width="*" height="14"><img src="http://www.unitedweego.com/email_images/event_bg_3_bm.png" width="100%" height="14"></td>
					<td width="13" height="14"><img src="http://www.unitedweego.com/email_images/event_bg_3_br.png" width="13" height="14"></td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<span style="font-size:1.2em; color:#666">*The location shown is likely to change. Actually, thats the joy of the experience.</span><br />
			<br />
			<span style="font-size:1.2em; color:#666">Your vote could decide where everyone will be going.</span><br />
			<br />
			<span style="font-size:1.2em; color:#666; font-weight:bold">OR</span><br />
			<br />
			<span style="font-size:1.2em; color:#666">Add some of your friends &amp; the places you would like to go to. And your friends can do the same.</span>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<a style="display:block; -webkit-border-radius: 3px; -moz-border-radius: 3px; font-size:24px; height:1.6em; color:#FFF; background-color:#690; text-decoration:none; text-align:center; padding-top:0.4em" href="$base_invite_url">Click here to Sign-Up for the Weego Private Beta</a>
			<br />
			<br />
			<br />
			<br />
			<br />
		</td>
	</tr>
</table>

<br />

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
	
	function getFormattedTime($eventDate)
	{
		$dateStr = "";
		$eventTime = new DateTime($eventDate);
		$dateStr = $eventTime->format('D, M j g:i A');
		return $dateStr;
	}
	
}
?>