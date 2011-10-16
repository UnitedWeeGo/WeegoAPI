<?php

class Weego_Request
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
    // suggest time
    const SuggestTimeAddSuccess = '300';
    
    protected $_params = array();

    /*
     * @param $params - set params when making request 'manually', from some other class
     */
    public function __construct($params = array())
    {
        $this->_params = $_REQUEST;
        
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $this->_params[$key] = $value;
            }
        }
    }
    
    public function getInt($key)
    {
        if (!isset($this->_params[$key])) {
            return null;
        } else {
        	return intval($this->_params[$key]);
        }
    }
    
    public function getString($key)
    {
        if (!isset($this->_params[$key])) {
            return null;
        } else {
            if (get_magic_quotes_gpc()) {
                return $this->stripSlashesRecursive($this->_params[$key]);
            } else {
                return $this->_params[$key];
            }
        }
    }
    
    public function stripSlashesRecursive($data)
    {
        if (is_array($data)) {
            return array_map(array($this, stripSlashesRecursive), $data);
        } else {
            return  stripslashes($data);
        }
    }
    
}