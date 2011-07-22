<?php

	class SuccessResponse
	{
		// register range
		const RegisterSuccess = '200';
		const LoginSuccess = '201';
		// event range
		const EventAddSuccess = '210';
		const EventUpdateSuccess = '211';
		const EventRemoveSuccess = '212';
		// location range
		const LocationAddSuccess = '220';
		// participant range
		const ParticipantAddSuccess = '230';
		const ParticipantAddRegisteredSuccess = '231';
		const ParticipantRegisteredSuccess = '232';
		const ParticipantNotRegisteredSuccess = '233';
		const ParticipantListGetSuccess = '234';
		// vote range
		const VoteAddSuccess = '240';
		const VoteRemoveSuccess = '241';
		// get event range
		const EventsGetSuccess = '250';
		const EventPostSuccess = '251';
		const EventGetSuccess = '252';
		// device range
		const DeviceAddSuccess = '260';
		const DeviceBadgeResetSuccess = '261';
		const DeviceRemoveSuccess = '262';
		// feed range
		const FeedMessageAddSuccess = '270';
		const FeedMessageGetSuccess = '271';
		const FeedMessageReadSuccess = '272';
		// log range
		const LogWriteSuccess = '280';
		const LogClearSuccess = '281';
		// checkin range
		const CheckinSuccess = '280';
		// report location
		const ReportLocationSuccess = '290';
		const ReportLocationGetSuccess = '291';
		
		/**
		* Generates an success response string
		* @param string $SuccessConst
		* @param string $id
		* @param string $requestId
		* @param string $eventId
		* @param string $responseData
		* @return string
		*/
		function genSuccess($SuccessConst, $id='', $requestId='', $eventId='', $responseData='')
		{
			$code = $SuccessConst;
			
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('response');
			$doc->appendChild($root);
			
			$root->setAttribute('code', $code);
			
			$successNode = $doc->createElement('success');
			$root->appendChild($successNode);
			
			if ($id != '')
			{
				$successNode->setAttribute('id', $id);
			}
			if ($requestId != '')
			{
				$successNode->setAttribute('requestId', $requestId);
			}
			if ($eventId != '')
			{
				$successNode->setAttribute('eventId', $eventId);
			}
			if ($responseData != '')
			{
				$successNode->setAttribute('responseData', $responseData);
			}
			
			return $doc->saveXML();
		}
		
		/**
		* Generates an success response string
		* @param string $SuccessConst
		* @param array $xmlList
		* @param string $requestId
		* @param string $eventId
		* @return string
		*/
		function genSuccessWithXMLArray($SuccessConst, &$xmlList, $requestId='', $eventId='')
		{
			$code = $SuccessConst;
			
			$doc = new DOMDocument('1.0', 'UTF-8');
//			$doc->preserveWhiteSpace = false;
//			$doc->formatOutput   = true;
			$root = $doc->createElement('response');
			$doc->appendChild($root);
			$root->setAttribute('code', $code);
			$root->setAttribute('timestamp', date( 'Y-m-d H:i:s', microtime(true) ) );
			if ($requestId != '')
			{
				$root->setAttribute('requestId', $requestId);
			}
			if ($eventId != '')
			{
				$root->setAttribute('eventId', $eventId);
			}
			for($i=0; $i<count($xmlList); $i++)
			{
				$eventXML = $xmlList[$i];
				$root->appendChild( $doc->importNode($eventXML->firstChild, true)  );
			}
			return $doc->saveXML();
		}
	}

?>