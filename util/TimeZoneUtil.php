<?php

class TimeZoneUtil
{
	/*
	static $aTimeZones = array(
	  'America/Puerto_Rico'=>'AST', 
	  'America/New_York'=>'EDT', 
	  'America/Chicago'=>'CDT', 
	  'America/Boise'=>'MDT', 
	  'America/Phoenix'=>'MST', 
	  'America/Los_Angeles'=>'PDT', 
	  'America/Juneau'=>'AKDT', 
	  'Pacific/Honolulu'=>'HST', 
	  'Pacific/Guam'=>'ChST', 
	  'Pacific/Samoa'=>'SST', 
	  'Pacific/Wake'=>'WAKT', 
	);
	*/
	private static $aTimeZones = array(
		  'AST'=>'America/Puerto_Ric', 
		  'EDT'=>'America/New_York', 
		  'CDT'=>'America/Chicago', 
		  'MDT'=>'America/Boise', 
		  'MST'=>'America/Phoenix', 
		  'PDT'=>'America/Los_Angeles', 
		  'AKDT'=>'America/Juneau', 
		  'HST'=>'Pacific/Honolulu', 
		  'ChST'=>'Pacific/Guam', 
		  'SST'=>'Pacific/Samoa', 
		  'WAKT'=>'Pacific/Wake', 
	);
	
	static function getPHPTimeZoneStampForAbbreviation($abbreviation)
	{
		if (key_exists($abbreviation, self::$aTimeZones))return self::$aTimeZones[$abbreviation];
		return null;
	}
	
}

?>