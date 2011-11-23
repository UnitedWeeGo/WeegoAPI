<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

require_once '../util/XMLUtil.php';
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';
require_once '../push/class.push.php';
require_once '../google/GooglePlaces.php';

class LocationClass extends ReqBase
{
	const TYPE_ADDRESS = 'address';
	const TYPE_PLACE = 'place';
	
	public $dataObj;	
	private $requiredFields = array('registeredId', 'latitude', 'longitude', 'eventId', 'location_type');
	private $allFields = array('latitude', 'longitude', 'name', 'vicinity', 'requestId', 'g_id', 'g_reference', 'location_type', 'formatted_address', 'formatted_phone_number', 'tempId', 'rating', 'review_count', 'mobile_yelp_url');
	
	//g_reference look for this to populate object with google detail data
	
	function LocationClass()
	{
		parent::__construct();
	}
	
	function LocationGo()
	{
		$doSkipResult = true;
		if ($this->dataObj == null) // called from http
		{
			$this->dataObj = $this->genDataObj();
			$doSkipResult = false;
		}
		
		$this->checkProperties($this->requiredFields, $this->dataObj);

		// check if you are a registered user
		$me = $this->checkRegUserId($this->dataObj);
		
		// check to make sure event exists
		$lookup = new Event();
		$event = $lookup->Get($this->dataObj['eventId']);
				
		if ( !$event )
		{
			$e = new ErrorResponse();
			echo $e->genError(ErrorResponse::InvalidParamError, 'eventId invalid');
			die();
		}
		
		// check to ensure I am part of this event. I must be a participant of the event to invite a participant.
		$this->validateUserPartOfEvent($event, $me->email);
		
		$locationUpdate = false;
		$location;
		if (isset($this->dataObj['locationId']))
		{
			$existingLocationSet = $event->GetLocationList( array( array("locationId", "=", $this->dataObj['locationId'] ) ) );
			if (count($existingLocationSet) > 0)
			{
				$location = $existingLocationSet[0];
				$locationUpdate = true;
			}
		}
		/** @var $location Location */
		if (!$locationUpdate) 
		{
			$location = new Location();
			$location->voteCount = 0;
		}
		$location->timestamp = $this->getTimeStamp();
		$location->addedByParticipantId = $me->email;
		
		$this->populateObject($this->allFields, $this->dataObj, $location); // populate the location obj
		
		// get the detail data from google DEPRICATED - using simple geo
		/*
		$refSet = isset($this->dataObj['g_reference']);
		$isPlace = $this->dataObj['location_type'] == LocationClass::TYPE_PLACE;
		if ($refSet && $isPlace)
		{
			// decorates the location with google data
			$location = $this->decorateLocationWithGoogleDetails($location);
		}
		*/
		
		if (!$locationUpdate) $event->AddLocation($location);
		if ($locationUpdate) $location->Save();
		$event->locationReorderTimestamp = $this->getTimeStamp();
		$event->timestamp = $this->getTimeStamp();
		$event->Save(true);
		
		/*
		$push = new Push();
		$push->triggerClientUpdateForEvent($event);
		*/
		
		if (!$doSkipResult) // only give success and kill if not called by http
		{
			$requestId = $this->dataObj['requestId'];
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::LocationAddSuccess, $location->locationId, $requestId);
			die();
		}
		else 
		{
			return $location;
		}
	}
	
	/**
	 * Grabs the google detail data and adds to location
	 * @param Location $location
	 * @return Location
	 */
	
	/* DEPRICATED - using simple geo
	function decorateLocationWithGoogleDetails(&$location)
	{
		$g_reference = $this->dataObj['g_reference'];
		$gplaces = New GooglePlaces;
		$gplaces->SetReference($g_reference);
		$results = $gplaces->Details();
		if (isset($results['result']))
		{
			$result = $results['result'];
			if (isset($result['formatted_address'])) $location->formatted_address = $result['formatted_address'];
			if (isset($result['formatted_phone_number'])) $location->formatted_phone_number = $result['formatted_phone_number'];
			if (isset($result['rating'])) $location->rating = $result['rating'];
			if (isset($result['icon'])) $location->icon = $result['icon'];
		}
		return $location;
	}
	*/
}
?>