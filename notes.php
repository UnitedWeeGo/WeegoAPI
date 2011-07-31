<?php 

$server = $_SERVER['SERVER_NAME'];

echo "Notes ---------
-----------
create or modify an event:
required - registeredId, eventDate(format: 0000-00-00 00:00:00), eventTitle
optional - eventDescription, guestListOpen(1 or 0), locationListOpen(1 or 0)

create a new event:
http://$server/public/mod.event.php?registeredId=859091f4-6451-308a-577b-813fd9594283&eventDate=2010-06-12%2001:00:00&eventTitle=SomeTitle

update an event: (eventId must be correct)
http://$server/public/mod.event.php?registeredId=859091f4-6451-308a-577b-813fd9594283&eventDate=2010-06-12%2001:00:00&eventTitle=SomeTitleUpdate&eventId=929
or with (optional) eventDescription
http://$server/public/mod.event.php?registeredId=859091f4-6451-308a-577b-813fd9594283&eventDate=2010-06-12%2001:00:00&eventTitle=SomeTitleUpdate&eventDescription=This%20is%20da%20bomb&eventId=929

notes: 
you can pass an empty param to clear a field (like eventDescription), not passing it leaves it alone

-----------
add a participant:
http://$server/public/mod.participant.php
required - registeredId, email, eventId
optional - 

-----------
add a location: (will issue a vote for this location)
http://$server/public/mod.location.php
required - registeredId, latitude, longitude, eventId
optional - establishment, locality, route, street_number

-----------
vote:
http://$server/public/mod.vote.php
required - registeredId, locationId, eventId
optional - removeVote ('true')

-----------
get events:
http://$server/public/get.event.php?registeredId=3818c0fa-6c98-2b2b-a558-4b9bf4cba9f8
required - registeredId
optional - timestamp (format: 2011-02-10 19:30:09), eventId (will only return that event)
-----------
get events (dashboard):
http://$server/public/get.event.dashboard.php?registeredId=3818c0fa-6c98-2b2b-a558-4b9bf4cba9f8
required - registeredId
optional - timestamp (format: 2011-02-10 19:30:09), eventId (will only return that event)

------------
get participant info (for adding an event name lookup for registered user):
http://$server/public/get.participantinfo.php
required - registeredId, email

------------
utils:
delete all data
http://$server/util/deletedata.php

generate some fake data (you'll want to tweak this to your liking)
http://$server/util/gendata.php

------------
post xml:
1. Do not add yourself to the participants nodes - you are added automatically to the event when creating
2. for the vote:
	if you are adding a new location(s), use <vote selectedLocationIndex=\"0\"/>
	if you are casting a vote for a location you have the id for, then use <vote locationId=\"74\"/>
3. generates const EventPostSuccess = '251';
4. pass eventId to update an event <event eventId=\"1754\">
5. test your xml here - http://$server/postform.html
6. if you pass a timestamp you will only get new stuff back
7. see this file for xml test examples - http://$server/xmlPostExamples.txt
8. optional requestId attribute in event node will pass through

-------
errors:
		// user generated errors
		const DuplicateParticipantError = '500';
		const InvalidEmailError = '500';
		const InvalidCredentialsError = '500';
		
		// debug related errors
		const MissingParamError = '600';
		const InvalidRUIDError = '610';
		const InvalidParamError = '620';
		const InvalidTimestampError = '630';
		const InvalidXMLError = '640';
		const ServerError = '650';

success:
		// register range
		const RegisterSuccess = '200';
		const LoginSuccess = '201';
		// event range
		const EventAddSuccess = '210';
		const EventUpdateSuccess = '211';
		// location range
		const LocationAddSuccess = '220';
		// participant range
		const ParticipantAddSuccess = '230';
		const ParticipantAddRegisteredSuccess = '231';
		const ParticipantRegisteredSuccess = '232';
		const ParticipantNotRegisteredSuccess = '233';
		// vote range
		const VoteAddSuccess = '240';
		const VoteRemoveSuccess = '241';
		// get event range
		const EventsGetSuccess = '250';
		const EventPostSuccess = '251';
		const EventGetSuccess = '252'; - for a single event request
		// device range
		const DeviceAddSuccess = '260';
"

?>