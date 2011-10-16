<?php

class Weego_Request_Facebook extends Weego_Request
{
    
	public function index()
	{
        
        if ($this->getString('access_token')) {
            $this->login();
        } else {
            echo 'Test';
        }
        
	}
	
	public function login()
	{
	    $access_token = $this->getString('access_token');
	    
	    if (is_null($access_token)) {
	        throw new Weego_Exception('', Weego_Exception::MissingParamError);
	    }
	    
	    require_once APPLICATION_PATH . '/../facebook-php-sdk/src/facebook.php';
	    
	    try {
	        $facebook = new Facebook(array(
              'appId'  => '221300981231092',
              'secret' => '9670bee46bf64a4e52a86716df51a8dc',
            ));
            $facebook->setAccessToken($access_token);
            
            // Get User ID
            $user = $facebook->getUser();
            if ($user) {
                $user_profile = $facebook->api('/me');
            }
	    } catch (FacebookApiException $e) {
	        throw new Weego_Exception($e->__toString(), ErrorResponse::FacebookLoginFailed);
	    }
	    
	    if (!$user_profile) {
	        throw new Weego_Exception('', ErrorResponse::FacebookLoginFailed);
	    }
	    
        
        
	}
	
}