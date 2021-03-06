<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `location` (
	`locationid` int(11) NOT NULL auto_increment,
	`name` VARCHAR(255) NOT NULL,
	`vicinity` VARCHAR(255) NOT NULL,
	`icon` VARCHAR(255) NOT NULL,
	`latitude` VARCHAR(255) NOT NULL,
	`longitude` VARCHAR(255) NOT NULL,
	`eventid` int(11) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`addedbyparticipantid` VARCHAR(255) NOT NULL,
	`formatted_phone_number` VARCHAR(255) NOT NULL,
	`formatted_address` VARCHAR(255) NOT NULL,
	`rating` VARCHAR(255) NOT NULL,
	`g_id` VARCHAR(255) NOT NULL,
	`votecount` INT NOT NULL,
	`location_type` VARCHAR(255) NOT NULL,
	`tempid` VARCHAR(255) NOT NULL,
	`hasbeenremoved` TINYINT NOT NULL,
	`mobile_yelp_url` VARCHAR(255) NOT NULL,
	`review_count` VARCHAR(255) NOT NULL, INDEX(`eventid`), PRIMARY KEY  (`locationid`)) ENGINE=MyISAM;
*/

/**
* <b>Location</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=Location&attributeList=array+%28%0A++0+%3D%3E+%27name%27%2C%0A++1+%3D%3E+%27vicinity%27%2C%0A++2+%3D%3E+%27icon%27%2C%0A++3+%3D%3E+%27latitude%27%2C%0A++4+%3D%3E+%27longitude%27%2C%0A++5+%3D%3E+%27Event%27%2C%0A++6+%3D%3E+%27timestamp%27%2C%0A++7+%3D%3E+%27addedByParticipantId%27%2C%0A++8+%3D%3E+%27formatted_phone_number%27%2C%0A++9+%3D%3E+%27formatted_address%27%2C%0A++10+%3D%3E+%27rating%27%2C%0A++11+%3D%3E+%27g_id%27%2C%0A++12+%3D%3E+%27voteCount%27%2C%0A++13+%3D%3E+%27location_type%27%2C%0A++14+%3D%3E+%27tempId%27%2C%0A++15+%3D%3E+%27hasBeenRemoved%27%2C%0A++16+%3D%3E+%27mobile_yelp_url%27%2C%0A++17+%3D%3E+%27review_count%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B8%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B9%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B10%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B11%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B12%2B%253D%253E%2B%2527INT%2527%252C%250A%2B%2B13%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B14%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B15%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B16%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B17%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
class Location extends POG_Base
{
	public $locationId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $name;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $vicinity;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $icon;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $latitude;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $longitude;
	
	/**
	 * @var INT(11)
	 */
	public $eventId;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $addedByParticipantId;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $formatted_phone_number;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $formatted_address;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $rating;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $g_id;
	
	/**
	 * @var INT
	 */
	public $voteCount;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $location_type;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $tempId;
	
	/**
	 * @var TINYINT
	 */
	public $hasBeenRemoved;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $mobile_yelp_url;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $review_count;
	
	public $pog_attribute_type = array(
		"locationId" => array('db_attributes' => array("NUMERIC", "INT")),
		"name" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"vicinity" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"icon" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"latitude" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"longitude" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Event" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"addedByParticipantId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"formatted_phone_number" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"formatted_address" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"rating" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"g_id" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"voteCount" => array('db_attributes' => array("NUMERIC", "INT")),
		"location_type" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"tempId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"hasBeenRemoved" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"mobile_yelp_url" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"review_count" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		);
	public $pog_query;
	public $pog_bind = array();
	
	
	/**
	* Getter for some private attributes
	* @return mixed $attribute
	*/
	public function __get($attribute)
	{
		if (isset($this->{"_".$attribute}))
		{
			return $this->{"_".$attribute};
		}
		else
		{
			return false;
		}
	}
	
	function Location($name='', $vicinity='', $icon='', $latitude='', $longitude='', $timestamp='', $addedByParticipantId='', $formatted_phone_number='', $formatted_address='', $rating='', $g_id='', $voteCount='', $location_type='', $tempId='', $hasBeenRemoved='', $mobile_yelp_url='', $review_count='')
	{
		$this->name = $name;
		$this->vicinity = $vicinity;
		$this->icon = $icon;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->timestamp = $timestamp;
		$this->addedByParticipantId = $addedByParticipantId;
		$this->formatted_phone_number = $formatted_phone_number;
		$this->formatted_address = $formatted_address;
		$this->rating = $rating;
		$this->g_id = $g_id;
		$this->voteCount = $voteCount;
		$this->location_type = $location_type;
		$this->tempId = $tempId;
		$this->hasBeenRemoved = $hasBeenRemoved;
		$this->mobile_yelp_url = $mobile_yelp_url;
		$this->review_count = $review_count;
	}
	
	
	/**
	* Gets object from database
	* @param integer $locationId 
	* @return object $Location
	*/
	function Get($locationId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `location` where `locationid`=:locationId LIMIT 1";
		$this->pog_bind = array(
			':locationId' => intval($locationId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->locationId = $row['locationid'];
			$this->name = $this->Decode($row['name']);
			$this->vicinity = $this->Decode($row['vicinity']);
			$this->icon = $this->Decode($row['icon']);
			$this->latitude = $this->Decode($row['latitude']);
			$this->longitude = $this->Decode($row['longitude']);
			$this->eventId = $row['eventid'];
			$this->timestamp = $row['timestamp'];
			$this->addedByParticipantId = $this->Decode($row['addedbyparticipantid']);
			$this->formatted_phone_number = $this->Decode($row['formatted_phone_number']);
			$this->formatted_address = $this->Decode($row['formatted_address']);
			$this->rating = $this->Decode($row['rating']);
			$this->g_id = $this->Decode($row['g_id']);
			$this->voteCount = $this->Decode($row['votecount']);
			$this->location_type = $this->Decode($row['location_type']);
			$this->tempId = $this->Decode($row['tempid']);
			$this->hasBeenRemoved = $this->Decode($row['hasbeenremoved']);
			$this->mobile_yelp_url = $this->Decode($row['mobile_yelp_url']);
			$this->review_count = $this->Decode($row['review_count']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $locationList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `location` ";
		$locationList = Array();
		if (sizeof($fcv_array) > 0)
		{
			$this->pog_query .= " where ";
			for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
			{
				if (sizeof($fcv_array[$i]) == 1)
				{
					$this->pog_query .= " ".$fcv_array[$i][0]." ";
					continue;
				}
				else
				{
					if ($i > 0 && sizeof($fcv_array[$i-1]) != 1)
					{
						$this->pog_query .= " AND ";
					}
					if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
					{
						if ($GLOBALS['configuration']['db_encoding'] == 1)
						{
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2], $connection);
						$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
					}
				}
			}
		}
		if ($sortBy != '')
		{
			if (isset($this->pog_attribute_type[$sortBy]['db_attributes']) && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
			{
				if ($GLOBALS['configuration']['db_encoding'] == 1)
				{
					$sortBy = "BASE64_DECODE($sortBy) ";
				}
				else
				{
					$sortBy = "$sortBy ";
				}
			}
			else
			{
				$sortBy = "$sortBy ";
			}
		}
		else
		{
			$sortBy = "locationid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$location = new $thisObjectName();
			$location->locationId = $row['locationid'];
			$location->name = $this->Unescape($row['name']);
			$location->vicinity = $this->Unescape($row['vicinity']);
			$location->icon = $this->Unescape($row['icon']);
			$location->latitude = $this->Unescape($row['latitude']);
			$location->longitude = $this->Unescape($row['longitude']);
			$location->eventId = $row['eventid'];
			$location->timestamp = $row['timestamp'];
			$location->addedByParticipantId = $this->Unescape($row['addedbyparticipantid']);
			$location->formatted_phone_number = $this->Unescape($row['formatted_phone_number']);
			$location->formatted_address = $this->Unescape($row['formatted_address']);
			$location->rating = $this->Unescape($row['rating']);
			$location->g_id = $this->Unescape($row['g_id']);
			$location->voteCount = $this->Unescape($row['votecount']);
			$location->location_type = $this->Unescape($row['location_type']);
			$location->tempId = $this->Unescape($row['tempid']);
			$location->hasBeenRemoved = $this->Unescape($row['hasbeenremoved']);
			$location->mobile_yelp_url = $this->Unescape($row['mobile_yelp_url']);
			$location->review_count = $this->Unescape($row['review_count']);
			$locationList[] = $location;
		}
		return $locationList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $locationId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->locationId))
		{
			$this->pog_query = "select `locationid` from `location` where `locationid`=".$this->Quote($this->locationId, $connection)." LIMIT 1";
			$rows = Database::Query($this->pog_query, $connection);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `location` set 
			`name`=:name,
			`vicinity`=:vicinity,
			`icon`=:icon,
			`latitude`=:latitude,
			`longitude`=:longitude,
			`eventid`=:eventId,
			`timestamp`=:timestamp,
			`addedbyparticipantid`=:addedbyparticipantid,
			`formatted_phone_number`=:formatted_phone_number,
			`formatted_address`=:formatted_address,
			`rating`=:rating,
			`g_id`=:g_id,
			`votecount`=:votecount,
			`location_type`=:location_type,
			`tempid`=:tempid,
			`hasbeenremoved`=:hasbeenremoved,
			`mobile_yelp_url`=:mobile_yelp_url,
			`review_count`=:review_count where `locationid`=:locationId";
		}
		else
		{
			$this->locationId = "";
			$this->pog_query = "insert into `location` (`name`,`vicinity`,`icon`,`latitude`,`longitude`,`eventid`,`timestamp`,`addedbyparticipantid`,`formatted_phone_number`,`formatted_address`,`rating`,`g_id`,`votecount`,`location_type`,`tempid`,`hasbeenremoved`,`mobile_yelp_url`,`review_count`,`locationid`) values (
			:name,
			:vicinity,
			:icon,
			:latitude,
			:longitude,
			:eventId,
			:timestamp,
			:addedbyparticipantid,
			:formatted_phone_number,
			:formatted_address,
			:rating,
			:g_id,
			:votecount,
			:location_type,
			:tempid,
			:hasbeenremoved,
			:mobile_yelp_url,
			:review_count,
			:locationId)";
		}
		$this->pog_bind = array(
			':name' => $this->Encode($this->name),
			':vicinity' => $this->Encode($this->vicinity),
			':icon' => $this->Encode($this->icon),
			':latitude' => $this->Encode($this->latitude),
			':longitude' => $this->Encode($this->longitude),
			':eventId' => intval($this->eventId),
			':timestamp' => $this->timestamp,
			':addedbyparticipantid' => $this->Encode($this->addedByParticipantId),
			':formatted_phone_number' => $this->Encode($this->formatted_phone_number),
			':formatted_address' => $this->Encode($this->formatted_address),
			':rating' => $this->Encode($this->rating),
			':g_id' => $this->Encode($this->g_id),
			':votecount' => $this->Encode($this->voteCount),
			':location_type' => $this->Encode($this->location_type),
			':tempid' => $this->Encode($this->tempId),
			':hasbeenremoved' => $this->Encode($this->hasBeenRemoved),
			':mobile_yelp_url' => $this->Encode($this->mobile_yelp_url),
			':review_count' => $this->Encode($this->review_count),
			':locationId' => intval($this->locationId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind, $connection);
		if ($this->locationId == "")
		{
			$this->locationId = $insertId;
		}
		return $this->locationId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $locationId
	*/
	function SaveNew()
	{
		$this->locationId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `location` where `locationid`=".$this->Quote($this->locationId, $connection);
		return Database::NonQuery($this->pog_query, $connection);
	}
	
	
	/**
	* Deletes a list of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param bool $deep 
	* @return 
	*/
	function DeleteList($fcv_array)
	{
		if (sizeof($fcv_array) > 0)
		{
			$connection = Database::Connect();
			$this->pog_query = "delete from `location` where ";
			for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
			{
				if (sizeof($fcv_array[$i]) == 1)
				{
					$this->pog_query .= " ".$fcv_array[$i][0]." ";
					continue;
				}
				else
				{
					if ($i > 0 && sizeof($fcv_array[$i-1]) !== 1)
					{
						$this->pog_query .= " AND ";
					}
					if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
					{
						if ($GLOBALS['configuration']['db_encoding'] == 1)
						{
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$this->Quote($fcv_array[$i][2], $connection);
					}
				}
			}
			return Database::NonQuery($this->pog_query, $connection);
		}
	}
	
	
	/**
	* Associates the Event object to this one
	* @return boolean
	*/
	function GetEvent()
	{
		$event = new Event();
		return $event->Get($this->eventId);
	}
	
	
	/**
	* Associates the Event object to this one
	* @return 
	*/
	function SetEvent(&$event)
	{
		$this->eventId = $event->eventId;
	}
}
?>