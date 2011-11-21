<?php

//
// From http://non-diligent.com/articles/yelp-apiv2-php-example/
//
header ("Content-Type:application/json");

// Enter the path that the oauth library is in relation to the php file
require_once ('../lib/OAuth.php');
require_once '../util/request.base.php';

class SearchYelpClass extends ReqBase
{
	
	private $requiredFields = array('bounds', 'term', 'registeredId');
	public $dataObj = null;
	
	function SearchYelpClass()
	{
		parent::__construct();
	}
	
	function SearchYelpGo()
	{
		$this->dataObj = $this->genDataObj();
		
		$this->checkProperties($this->requiredFields, $this->dataObj);
		$me = $this->checkRegUserId($this->dataObj);
		
		// For example, request business with id 'the-waterboy-sacramento'
		//$unsigned_url = "http://api.yelp.com/v2/business/the-waterboy-sacramento";
		
		// For examaple, search for 'tacos' in 'sf'
		
		$unsigned_url = "http://api.yelp.com/v2/search?term=" . $this->dataObj['term'] . "&bounds=" . $this->dataObj['bounds'];
		
		//$unsigned_url = "http://api.yelp.com/v2/search?term=petes%20tavern&bounds=37.329108,-122.034652%7C37.335557,-122.027786";
		
		// Set your keys here
		$consumer_key = "MRgCPmRplDC1JJEmPnTykg";
		$consumer_secret = "1zZPJCuqmCqBOdCboPEGwLMMICQ=";
		$token = "DRVA5JDqUagRLV8wmnV7vpIm5qMjwz0q";
		$token_secret = "wueZ5gAPHSCUjOqdYy-6AdC9U-g";
		
		// Token object built using the OAuth library
		$token = new OAuthToken($token, $token_secret);
		
		// Consumer object built using the OAuth library
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret);
		
		// Yelp uses HMAC SHA1 encoding
		$signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		
		// Build OAuth Request using the OAuth PHP library. Uses the consumer and token object created above.
		$oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url);
		
		// Sign the request
		$oauthrequest->sign_request($signature_method, $consumer, $token);
		
		// Get the signed URL
		$signed_url = $oauthrequest->to_url();
		
		// Send Yelp API Call
		$ch = curl_init($signed_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch); // Yelp response
		curl_close($ch);
		
		// Handle Yelp response data
		$response = json_decode($data);
		
		//print_r($response);
		
		echo $data;
	}

}

?>