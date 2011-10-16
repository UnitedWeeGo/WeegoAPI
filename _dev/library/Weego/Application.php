<?php

class Weego_Application
{
	
    /**
     * Bootstrap
     *
     * @var Weego_Application_Bootstrap
     */
    protected $_bootstrap;
	
    /**
     * Constructor
     *
     * Initialize application.
     *
     * @param  string                   $environment
     * @param  string                   $application_ini String path to configuration file
     * @return void
     */
    
    public function __construct($environment, $application_ini)
    {
        $config = new Zend_Config_Ini($application_ini, $environment);
        
        set_error_handler(
		    array(
		        'Weego_Exception', 
		        'errorHandler'
		    )
		);
        
        //$request = new Zend_Controller_Request_Http();
        
        /*$path_info = $request->getPathInfo();
        
        echo $path_info . '<br>';

        echo $request->getRequestUri() . '<br>';
        
        echo $request->getBaseUrl() . '<br>';
        
        var_export($request->getServer());*/
    
    }
    
    /**
     * Get bootstrap object
     *
     * @return Weego_Application_Bootstrap
     */
    public function getBootstrap()
    {
        if (null === $this->_bootstrap) {
            $this->_bootstrap = new Weego_Application_Bootstrap();
        }
        return $this->_bootstrap;
    }
    
    /**
     * Run the application
     *
     * @return void
     */
    public function run()
    {
        try {
            $this->getBootstrap()->dispatch();
        } catch (Weego_Exception $e) {
            $e->getXml();
        }
    }
}