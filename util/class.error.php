<?php

	class ErrorResponse
	{
		// user generated errors
		const DuplicateParticipantError = '500';
		const InvalidEmailError = '500';
		const InvalidCredentialsError = '501'; // Facebook get friends lookup failed
		const FacebookLoginFailed = '502'; // Facebook login failed
		
		// debug related errors
		const MissingParamError = '600';
		const InvalidRUIDError = '610';
		const InvalidParamError = '620';
		const InvalidTimestampError = '630';
		const InvalidXMLError = '640';
		const ServerError = '650';
		
		function genError($ErrorConst, $moreInfo='')
		{
			$code = $ErrorConst;
			$doc = new DOMDocument('1.0', 'UTF-8');
			$root = $doc->createElement('response');
			$doc->appendChild($root);
			$root->setAttribute('code', $code);
			
			$titleNode = $doc->createElement('title');
			$root->appendChild($titleNode);
			$title = $doc->createCDATASection('Error');
			$titleNode->appendChild($title);
			
			$moreInfoNode = $doc->createElement('moreInfo');
			$root->appendChild($moreInfoNode);
			$moreInfo = $doc->createCDATASection($moreInfo);
			$moreInfoNode->appendChild($moreInfo);
			
			return $doc->saveXML();
		}
	}

?>