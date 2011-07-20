<?php

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "class.push.php";
while (true) {
	
	echo 'running script';
	
	$pusher = new Push();
	$pusher->dispatchQueuedInviteNotifications();
	$pusher->dispatchQueuedEventStartNotifications();
	$pusher->dispatchQueuedFeedMessageNotifications();
	$pusher->dispatchQueuedEventUpdateNotifications();
	$pusher->checkEventDecidedStatus();
	
	usleep(2000000); // 2 second delay
}
?>