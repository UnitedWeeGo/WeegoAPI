<?php

class Weego_Application_Bootstrap
{
	
	protected $_weego_request;
	
	protected $_weego_action;
	
    public function __construct()
    {
    	$this->_setWeegoRequest();
    }
	
    public function dispatch()
    {
        if (!empty($this->_weego_request)) {
            
            $req_class = "Weego_Request_" . ucfirst($this->_weego_request);
            
            try {
                $request = new $req_class();
            } catch (Exception $e) {
                throw new Weego_Exception('Request ' . $req_class . ' is not valid.');
            }
            
            if (empty($this->_weego_action)) {
                $this->_weego_action = 'index';
            }
            
            $func = array($request, $this->_weego_action);
            if (is_callable($func)) {
                $output = call_user_func_array($func, array());
            } else {
                throw new Weego_Exception('Function ' . $req_class . '::' . $this->_weego_action . ' not found');
            }
        } else {
            throw new Weego_Exception('Request is not set.');
        }
    }
	
    protected function _setWeegoRequest()
    {
    	$request = new Zend_Controller_Request_Http();
        
        $pathInfo = explode('/', $request->getPathInfo());
        
        if (isset($pathInfo[1])) {
            $this->_weego_request = $pathInfo[1];
        }
        
        if (isset($pathInfo[2])) {
            $this->_weego_action = $pathInfo[2];
        }
    }
    
}