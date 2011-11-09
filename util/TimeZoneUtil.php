<?php

class TimeZoneUtil
{
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
	
	private static $aTimeOffsetsDST = array(
			  '-1000'=>'HST',				// Hawaii Time 
			  '-0800'=>'AKDT',				// Alaska Time
			  '-0700'=>'PDT', 				// Pacific Time 
			  '-0600'=>'MDT', 				// Mountain Time 
			  '-0500'=>'CDT', 				// Central Time 
			  '-0400'=>'EDT'				// Eastern Time 
	);
	
	private static $aTimeOffsets = array(
				  '-1100'=>'HST',				// Hawaii Time 
				  '-0900'=>'AKDT',				// Alaska Time
				  '-0800'=>'PDT', 				// Pacific Time 
				  '-0700'=>'MDT', 				// Mountain Time 
				  '-0600'=>'CDT', 				// Central Time 
				  '-0500'=>'EDT'				// Eastern Time 
	);
	
	static function getPHPTimeZoneStampForAbbreviation($abbreviation)
	{
		if (key_exists($abbreviation, self::$aTimeZones))return self::$aTimeZones[$abbreviation];
		return null;
	}
	static function getPHPTimeZoneNameForOffset($offset, $isDaylightSavings)
	{
		if (key_exists($offset, self::$aTimeOffsets))
		{
			if ($isDaylightSavings == '1')
			{
				return self::$aTimeOffsetsDST[$offset];
			}
			else {
				return self::$aTimeOffsets[$offset];
			}
		}
		
		
		return null;
	}
}

?>