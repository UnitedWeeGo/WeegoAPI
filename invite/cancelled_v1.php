<?php 

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}
require_once '../util/request.base.php';
require_once '../util/TimeZoneUtil.php';

class CancelledEmail extends ReqBase
{
	
	function CancelledEmail()
	{
		
	}
	/**
	* Get the invite html message body
	* @param Participant $creator
	* @param Event $event
	* @param string $token
	* @param Boolean $needsPair
	* @return string
	*/
	function getCancelledHTMLBody($creator, $event, $token, $needsPair=false)
	{
		$base_invite_url = $GLOBALS['configuration']['base_invite_url'];
		$creatorAvatarURL = $creator->avatarURL;
		$creatorFriendlyName = $this->getFriendlyName($creator);
		$eventTitle = urldecode($event->eventTitle);
		$eventDate = $this->getFormattedTime($event->eventDate, $event->eventTimeZone);
		$eventExpireDate = $this->getFormattedTime($event->eventExpireDate, $event->eventTimeZone);
		$winningLocation = $this->determineWinningLocationForEvent($event);
		$name = $winningLocation ? urldecode($winningLocation->name) : 'No location added';
		$formatted_address = $winningLocation ? $winningLocation->formatted_address : 'No address added';
		$inviteToken = $token;
		
		$pairHTML =
<<< EOT
		<tr>
			<td colspan="2">
				<a style="display:block; -webkit-border-radius: 3px; -moz-border-radius: 3px; font-size:24px; height:1.6em; color:#FFF; background-color:#690; text-decoration:none; text-align:center; padding-top:0.4em" href="$base_invite_url?$inviteToken">Click here to sign-up for Weego</a>
				<br />
				<br />
			</td>
		</tr>
EOT;
		
		if (!$needsPair) $pairHTML = '';
		
		$aboutHTML = 
<<< EOT

	<tr>
		<td colspan="2">
			<span style="color:#666; font-size:1.8em; font-weight:bold;">What is Weego?</span><br />
			<span style="font-size:1.4em; color:#666">Weego is the simple &amp; private way to decide where to go with your friends.</span><br />
			<span style="font-size:1.4em; color:#666">In short it%27s a group collaboration application that enables you and your friends to plan &amp; decide, as a private group, where to go.</span><br />
			<span style="font-size:1.4em; color:#666">Private Group: Planning + Decision + Conversations</span><br />
			<br />
			<span style="color:#666; font-size:1.8em; font-weight:bold;">Make plans as a group</span><br />
			<span style="font-size:1.4em; color:#666">Add some of your friends and the places you would like to go to. And your friends can do the same.</span><br />
			<br />
			<span style="color:#666; font-size:1.8em; font-weight:bold;">Decide as a group</span><br />
			<span style="font-size:1.4em; color:#666">Everyone picks the place(s) they would like to &ldquo;Go&rdquo; to. Then Weego will let the group know the group%27s decision, including the event time and location.</span><br />
			<br />
			<span style="color:#666; font-size:1.8em; font-weight:bold;">Group conversations &amp; alerts</span><br />
			<span style="font-size:1.4em; color:#666">Stay in sync with your group. With group conversations &amp; alerts, you can rest assured that everyone will be in sync with the event. *No &ldquo;Text Messaging&rdquo; fees.</span><br />
			<br />
			<span style="color:#666; font-size:1.8em; font-weight:bold;">Check-in as a group</span><br />
			<span style="font-size:1.4em; color:#666">Right around the time of the event Weego will let your group know where you are and when you%27ll get there.</span></div>
		</td>
	</tr>

EOT;
		
		if (!$needsPair) $aboutHTML = '';

		$message = 
<<< EOT
<body bgcolor="#F3F3F3">
<div align="center" style="width: 100%; font-family: Arial, Helvetica, sans-serif; font-size: 10px;">
<div align="center"><img src="images/email_header_01.png" style="height: 170px; width: 600px"></div>
<table width="600" border="0" cellspacing="5" cellpadding="10" bgcolor="#FFF">
	<tr>
		<td colspan="2">
			<br />
			<span style="font-size:32px; color:#666;">$eventTitle has been cancelled.</span>
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
								<td valign="top" width="60"><img src="$creatorAvatarURL" border="1" style="border-color:#CCC"></td>
								<td valign="top">
									<span style="font-size:1.2em; color:#666">$creatorFriendlyName</span><br />
									<span style="font-size:1.8em; color:#333; font-weight:bold">$eventTitle</span><br />
									<span style="font-size:1.2em; color:#666">$eventDate</span><br />
									<span style="font-size:1.1em; color:#FF0000; font-weight:bold">Cancelled</span><br /><br />
								</td>
							</tr>
						</table>
					</td>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_1_mr.png" width="13" height="100%"></td>
				</tr>
				<tr>
					<td width="13" height="1"><img src="http://www.unitedweego.com/email_images/event_bg_2_ml.png" width="13" height="1"></td>
					<td bgcolor="#DEDEDE" height="1"><img src="http://www.unitedweego.com/email_images/spacer.gif" width="100%" height="1"></td>
					<td width="13" height="1"><img src="http://www.unitedweego.com/email_images/event_bg_2_mr.png" width="13" height="1"></td>
				</tr>
				<tr>
					<td width="13" height="*"><img src="http://www.unitedweego.com/email_images/event_bg_3_ml.png" width="13" height="100%"></td>
					<td bgcolor="#F9F9F9">
						<br />
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" width="60"><img src="http://www.unitedweego.com/email_images/button_like_default.png" width="50" height="50"></td>
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
	</tr>
	$pairHTML
	$aboutHTML
	<tr>
		<td colspan="2">
			<div style="font-size:1.2em; color: #999;">
				<br />
				<br />
				Copyright &copy; 2011 UnitedWego LLC, All rights reserved.<br />
				You are receiving this email because one of your friends has invited you to an event. You have not been added to any email lists or promotions.&nbsp;<br />
				<br />
				<strong>Our mailing address is:</strong><br />
				UnitedWego LLC<br />
				665 3rd St. Suite 521<br />
				San Francisco,&nbsp;CA&nbsp;94103
			</div>
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
	* Returns a friendly name string
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
	
	/**
	* Get the formatted event time
	* @param string $eventDate
	* @param string $eventTimeZone
	* @return string
	*/
	function getFormattedTime($eventDate, $eventTimeZone=null)
	{
		$tz = TimeZoneUtil::getPHPTimeZoneStampForAbbreviation($eventTimeZone);
		$eventTime = new DateTime($eventDate);
		if ($tz) $eventTime->setTimezone(new DateTimeZone($tz));
		$dateStr = $eventTime->format('D, M j g:i A') . ' ' . (($tz) ? $eventTimeZone : 'GMT');
		return $dateStr;
	}
	
}
?>