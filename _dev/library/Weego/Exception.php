<?php

class Weego_Exception extends Zend_Exception
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
    
    protected $_default_error_messages = array(
        self::MissingParamError => 'Missing some parameters',
        self::FacebookLoginFailed => 'Facebook connect failed, please try again.',
    );
    
    /**
     * Construct the exception
     *
     * @param  string $msg
     * @param  int $code
     * @return void
     */
    public function __construct($msg = '', $code = Weego_Exception::ServerError)
    {
        if ('' == $msg && isset($this->_default_error_messages[$code])) {
            $msg = $this->_default_error_messages[$code];
        }
    	parent::__construct($msg, (int) $code);
    }
    
    public function getXml()
    {
        $xml = new XMLWriter();
        
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        
        $xml->startElement('response');
        $xml->writeAttribute('code', $this->getCode());
        
        $xml->startElement('title');
        $xml->writeCData('Error');
        $xml->endElement();
        
        $xml->startElement('moreInfo');
        $xml->writeCData($this->__toString());
        $xml->endElement();
        
        $xml->endElement();
        
        header('Content-type: text/xml');
        echo $xml->flush();
        exit;
    }
    
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
    	$error = new Weego_Exception("(severity: $errno) " . $errstr);
    	$error->getXml();
    }
    
}