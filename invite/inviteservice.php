<?php

// This changes the context to the working directory so the cron job runs properly
chdir( dirname ( __FILE__ ) );

require_once "inviteservice.class.php";
while (true) {
	
	$service = new InviteService();
	$service->dispatchUnkownEmailInvites();
	
	usleep(10000000); // 10 second delay
}
?>