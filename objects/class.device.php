<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `device` (
	`deviceid` int(11) NOT NULL auto_increment,
	`devicetoken` VARCHAR(255) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`participantid` int(11) NOT NULL,
	`deviceuuid` VARCHAR(255) NOT NULL,
	`devicename` VARCHAR(255) NOT NULL,
	`devicemodel` VARCHAR(255) NOT NULL,
	`devicesystemversion` VARCHAR(255) NOT NULL,
	`pushbadge` VARCHAR(255) NOT NULL,
	`pushalert` VARCHAR(255) NOT NULL,
	`pushsound` VARCHAR(255) NOT NULL,
	`issandbox` TINYINT NOT NULL,
	`badgecount` INT NOT NULL, INDEX(`participantid`), PRIMARY KEY  (`deviceid`)) ENGINE=MyISAM;
*/

/**
* <b>Device</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=Device&attributeList=array+%28%0A++0+%3D%3E+%27deviceToken%27%2C%0A++1+%3D%3E+%27timestamp%27%2C%0A++2+%3D%3E+%27Participant%27%2C%0A++3+%3D%3E+%27deviceUuid%27%2C%0A++4+%3D%3E+%27deviceName%27%2C%0A++5+%3D%3E+%27deviceModel%27%2C%0A++6+%3D%3E+%27deviceSystemVersion%27%2C%0A++7+%3D%3E+%27pushBadge%27%2C%0A++8+%3D%3E+%27pushAlert%27%2C%0A++9+%3D%3E+%27pushSound%27%2C%0A++10+%3D%3E+%27isSandbox%27%2C%0A++11+%3D%3E+%27badgeCount%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B8%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B9%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B10%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B11%2B%253D%253E%2B%2527INT%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
class Device extends POG_Base
{
	public $deviceId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $deviceToken;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var INT(11)
	 */
	public $participantId;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $deviceUuid;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $deviceName;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $deviceModel;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $deviceSystemVersion;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $pushBadge;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $pushAlert;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $pushSound;
	
	/**
	 * @var TINYINT
	 */
	public $isSandbox;
	
	/**
	 * @var INT
	 */
	public $badgeCount;
	
	public $pog_attribute_type = array(
		"deviceId" => array('db_attributes' => array("NUMERIC", "INT")),
		"deviceToken" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"Participant" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
		"deviceUuid" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"deviceName" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"deviceModel" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"deviceSystemVersion" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"pushBadge" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"pushAlert" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"pushSound" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"isSandbox" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"badgeCount" => array('db_attributes' => array("NUMERIC", "INT")),
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
	
	function Device($deviceToken='', $timestamp='', $deviceUuid='', $deviceName='', $deviceModel='', $deviceSystemVersion='', $pushBadge='', $pushAlert='', $pushSound='', $isSandbox='', $badgeCount='')
	{
		$this->deviceToken = $deviceToken;
		$this->timestamp = $timestamp;
		$this->deviceUuid = $deviceUuid;
		$this->deviceName = $deviceName;
		$this->deviceModel = $deviceModel;
		$this->deviceSystemVersion = $deviceSystemVersion;
		$this->pushBadge = $pushBadge;
		$this->pushAlert = $pushAlert;
		$this->pushSound = $pushSound;
		$this->isSandbox = $isSandbox;
		$this->badgeCount = $badgeCount;
	}
	
	
	/**
	* Gets object from database
	* @param integer $deviceId 
	* @return object $Device
	*/
	function Get($deviceId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `device` where `deviceid`=:deviceId LIMIT 1";
		$this->pog_bind = array(
			':deviceId' => intval($deviceId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->deviceId = $row['deviceid'];
			$this->deviceToken = $this->Decode($row['devicetoken']);
			$this->timestamp = $row['timestamp'];
			$this->participantId = $row['participantid'];
			$this->deviceUuid = $this->Decode($row['deviceuuid']);
			$this->deviceName = $this->Decode($row['devicename']);
			$this->deviceModel = $this->Decode($row['devicemodel']);
			$this->deviceSystemVersion = $this->Decode($row['devicesystemversion']);
			$this->pushBadge = $this->Decode($row['pushbadge']);
			$this->pushAlert = $this->Decode($row['pushalert']);
			$this->pushSound = $this->Decode($row['pushsound']);
			$this->isSandbox = $this->Decode($row['issandbox']);
			$this->badgeCount = $this->Decode($row['badgecount']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $deviceList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `device` ";
		$deviceList = Array();
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
			$sortBy = "deviceid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$device = new $thisObjectName();
			$device->deviceId = $row['deviceid'];
			$device->deviceToken = $this->Unescape($row['devicetoken']);
			$device->timestamp = $row['timestamp'];
			$device->participantId = $row['participantid'];
			$device->deviceUuid = $this->Unescape($row['deviceuuid']);
			$device->deviceName = $this->Unescape($row['devicename']);
			$device->deviceModel = $this->Unescape($row['devicemodel']);
			$device->deviceSystemVersion = $this->Unescape($row['devicesystemversion']);
			$device->pushBadge = $this->Unescape($row['pushbadge']);
			$device->pushAlert = $this->Unescape($row['pushalert']);
			$device->pushSound = $this->Unescape($row['pushsound']);
			$device->isSandbox = $this->Unescape($row['issandbox']);
			$device->badgeCount = $this->Unescape($row['badgecount']);
			$deviceList[] = $device;
		}
		return $deviceList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $deviceId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->deviceId))
		{
			$this->pog_query = "select `deviceid` from `device` where `deviceid`=".$this->Quote($this->deviceId, $connection)." LIMIT 1";
			$rows = Database::Query($this->pog_query, $connection);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `device` set 
			`devicetoken`=:devicetoken,
			`timestamp`=:timestamp,
			`participantid`=:participantId,
			`deviceuuid`=:deviceuuid,
			`devicename`=:devicename,
			`devicemodel`=:devicemodel,
			`devicesystemversion`=:devicesystemversion,
			`pushbadge`=:pushbadge,
			`pushalert`=:pushalert,
			`pushsound`=:pushsound,
			`issandbox`=:issandbox,
			`badgecount`=:badgecount where `deviceid`=:deviceId";
		}
		else
		{
			$this->deviceId = "";
			$this->pog_query = "insert into `device` (`devicetoken`,`timestamp`,`participantid`,`deviceuuid`,`devicename`,`devicemodel`,`devicesystemversion`,`pushbadge`,`pushalert`,`pushsound`,`issandbox`,`badgecount`,`deviceid`) values (
			:devicetoken,
			:timestamp,
			:participantId,
			:deviceuuid,
			:devicename,
			:devicemodel,
			:devicesystemversion,
			:pushbadge,
			:pushalert,
			:pushsound,
			:issandbox,
			:badgecount,
			:deviceId)";
		}
		$this->pog_bind = array(
			':devicetoken' => $this->Encode($this->deviceToken),
			':timestamp' => $this->timestamp,
			':participantId' => intval($this->participantId),
			':deviceuuid' => $this->Encode($this->deviceUuid),
			':devicename' => $this->Encode($this->deviceName),
			':devicemodel' => $this->Encode($this->deviceModel),
			':devicesystemversion' => $this->Encode($this->deviceSystemVersion),
			':pushbadge' => $this->Encode($this->pushBadge),
			':pushalert' => $this->Encode($this->pushAlert),
			':pushsound' => $this->Encode($this->pushSound),
			':issandbox' => $this->Encode($this->isSandbox),
			':badgecount' => $this->Encode($this->badgeCount),
			':deviceId' => intval($this->deviceId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind, $connection);
		if ($this->deviceId == "")
		{
			$this->deviceId = $insertId;
		}
		return $this->deviceId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $deviceId
	*/
	function SaveNew()
	{
		$this->deviceId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `device` where `deviceid`=".$this->Quote($this->deviceId, $connection);
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
			$this->pog_query = "delete from `device` where ";
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
	* Associates the Participant object to this one
	* @return boolean
	*/
	function GetParticipant()
	{
		$participant = new Participant();
		return $participant->Get($this->participantId);
	}
	
	
	/**
	* Associates the Participant object to this one
	* @return 
	*/
	function SetParticipant(&$participant)
	{
		$this->participantId = $participant->participantId;
	}
}
?>