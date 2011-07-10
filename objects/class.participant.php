<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `participant` (
	`participantid` int(11) NOT NULL auto_increment,
	`firstname` VARCHAR(255) NOT NULL,
	`lastname` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`registeredid` VARCHAR(255) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`avatarurl` VARCHAR(255) NOT NULL,
	`facebookid` VARCHAR(255) NOT NULL, PRIMARY KEY  (`participantid`)) ENGINE=MyISAM;
*/

/**
* <b>Participant</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0f / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://www.phpobjectgenerator.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=Participant&attributeList=array+%28%0A++0+%3D%3E+%27firstName%27%2C%0A++1+%3D%3E+%27lastName%27%2C%0A++2+%3D%3E+%27email%27%2C%0A++3+%3D%3E+%27Event%27%2C%0A++4+%3D%3E+%27password%27%2C%0A++5+%3D%3E+%27registeredId%27%2C%0A++6+%3D%3E+%27timestamp%27%2C%0A++7+%3D%3E+%27avatarURL%27%2C%0A++8+%3D%3E+%27Device%27%2C%0A++9+%3D%3E+%27facebookId%27%2C%0A++10+%3D%3E+%27AltEmail%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B8%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B9%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B10%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
include_once('class.eventparticipantmap.php');
class Participant extends POG_Base
{
	public $participantId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $firstName;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $lastName;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $email;
	
	/**
	 * @var private array of Event objects
	 */
	private $_eventList = array();
	
	/**
	 * @var VARCHAR(255)
	 */
	public $password;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $registeredId;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $avatarURL;
	
	/**
	 * @var private array of Device objects
	 */
	private $_deviceList = array();
	
	/**
	 * @var VARCHAR(255)
	 */
	public $facebookId;
	
	/**
	 * @var private array of AltEmail objects
	 */
	private $_altemailList = array();
	
	public $pog_attribute_type = array(
		"participantId" => array('db_attributes' => array("NUMERIC", "INT")),
		"firstName" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"lastName" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"email" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Event" => array('db_attributes' => array("OBJECT", "JOIN")),
		"password" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"registeredId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"avatarURL" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Device" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"facebookId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"AltEmail" => array('db_attributes' => array("OBJECT", "HASMANY")),
		);
	public $pog_query;
	
	
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
	
	function Participant($firstName='', $lastName='', $email='', $password='', $registeredId='', $timestamp='', $avatarURL='', $facebookId='')
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->_eventList = array();
		$this->password = $password;
		$this->registeredId = $registeredId;
		$this->timestamp = $timestamp;
		$this->avatarURL = $avatarURL;
		$this->_deviceList = array();
		$this->facebookId = $facebookId;
		$this->_altemailList = array();
	}
	
	
	/**
	* Gets object from database
	* @param integer $participantId 
	* @return object $Participant
	*/
	function Get($participantId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `participant` where `participantid`='".intval($participantId)."' LIMIT 1";
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->participantId = $row['participantid'];
			$this->firstName = $this->Unescape($row['firstname']);
			$this->lastName = $this->Unescape($row['lastname']);
			$this->email = $this->Unescape($row['email']);
			$this->password = $this->Unescape($row['password']);
			$this->registeredId = $this->Unescape($row['registeredid']);
			$this->timestamp = $row['timestamp'];
			$this->avatarURL = $this->Unescape($row['avatarurl']);
			$this->facebookId = $this->Unescape($row['facebookid']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $participantList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `participant` ";
		$participantList = Array();
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
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : "'".$fcv_array[$i][2]."'";
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$this->Escape($fcv_array[$i][2])."'";
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$fcv_array[$i][2]."'";
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
			$sortBy = "participantid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$participant = new $thisObjectName();
			$participant->participantId = $row['participantid'];
			$participant->firstName = $this->Unescape($row['firstname']);
			$participant->lastName = $this->Unescape($row['lastname']);
			$participant->email = $this->Unescape($row['email']);
			$participant->password = $this->Unescape($row['password']);
			$participant->registeredId = $this->Unescape($row['registeredid']);
			$participant->timestamp = $row['timestamp'];
			$participant->avatarURL = $this->Unescape($row['avatarurl']);
			$participant->facebookId = $this->Unescape($row['facebookid']);
			$participantList[] = $participant;
		}
		return $participantList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $participantId
	*/
	function Save($deep = true)
	{
		$connection = Database::Connect();
		$this->pog_query = "select `participantid` from `participant` where `participantid`='".$this->participantId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows > 0)
		{
			$this->pog_query = "update `participant` set 
			`firstname`='".$this->Escape($this->firstName)."', 
			`lastname`='".$this->Escape($this->lastName)."', 
			`email`='".$this->Escape($this->email)."', 
			`password`='".$this->Escape($this->password)."', 
			`registeredid`='".$this->Escape($this->registeredId)."', 
			`timestamp`='".$this->timestamp."', 
			`avatarurl`='".$this->Escape($this->avatarURL)."', 
			`facebookid`='".$this->Escape($this->facebookId)."'where `participantid`='".$this->participantId."'";
		}
		else
		{
			$this->pog_query = "insert into `participant` (`firstname`, `lastname`, `email`, `password`, `registeredid`, `timestamp`, `avatarurl`, `facebookid`) values (
			'".$this->Escape($this->firstName)."', 
			'".$this->Escape($this->lastName)."', 
			'".$this->Escape($this->email)."', 
			'".$this->Escape($this->password)."', 
			'".$this->Escape($this->registeredId)."', 
			'".$this->timestamp."', 
			'".$this->Escape($this->avatarURL)."', 
			'".$this->Escape($this->facebookId)."')";
		}
		$insertId = Database::InsertOrUpdate($this->pog_query, $connection);
		if ($this->participantId == "")
		{
			$this->participantId = $insertId;
		}
		if ($deep)
		{
			foreach ($this->_eventList as $event)
			{
				$event->Save();
				$map = new EventParticipantMap();
				$map->AddMapping($this, $event);
			}
			foreach ($this->_deviceList as $device)
			{
				$device->participantId = $this->participantId;
				$device->Save($deep);
			}
			foreach ($this->_altemailList as $altemail)
			{
				$altemail->participantId = $this->participantId;
				$altemail->Save($deep);
			}
		}
		return $this->participantId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $participantId
	*/
	function SaveNew($deep = false)
	{
		$this->participantId = '';
		return $this->Save($deep);
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete($deep = false, $across = false)
	{
		if ($deep)
		{
			$deviceList = $this->GetDeviceList();
			foreach ($deviceList as $device)
			{
				$device->Delete($deep, $across);
			}
			$altemailList = $this->GetAltemailList();
			foreach ($altemailList as $altemail)
			{
				$altemail->Delete($deep, $across);
			}
		}
		if ($across)
		{
			$eventList = $this->GetEventList();
			$map = new EventParticipantMap();
			$map->RemoveMapping($this);
			foreach ($eventList as $event)
			{
				$event->Delete($deep, $across);
			}
		}
		else
		{
			$map = new EventParticipantMap();
			$map->RemoveMapping($this);
		}
		$connection = Database::Connect();
		$this->pog_query = "delete from `participant` where `participantid`='".$this->participantId."'";
		return Database::NonQuery($this->pog_query, $connection);
	}
	
	
	/**
	* Deletes a list of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param bool $deep 
	* @return 
	*/
	function DeleteList($fcv_array, $deep = false, $across = false)
	{
		if (sizeof($fcv_array) > 0)
		{
			if ($deep || $across)
			{
				$objectList = $this->GetList($fcv_array);
				foreach ($objectList as $object)
				{
					$object->Delete($deep, $across);
				}
			}
			else
			{
				$connection = Database::Connect();
				$pog_query = "delete from `participant` where ";
				for ($i=0, $c=sizeof($fcv_array); $i<$c; $i++)
				{
					if (sizeof($fcv_array[$i]) == 1)
					{
						$pog_query .= " ".$fcv_array[$i][0]." ";
						continue;
					}
					else
					{
						if ($i > 0 && sizeof($fcv_array[$i-1]) !== 1)
						{
							$pog_query .= " AND ";
						}
						if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
						{
							$pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." '".$this->Escape($fcv_array[$i][2])."'";
						}
						else
						{
							$pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." '".$fcv_array[$i][2]."'";
						}
					}
				}
				return Database::NonQuery($pog_query, $connection);
			}
		}
	}
	
	
	/**
	* Creates mappings between this and all objects in the Event List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetEventList(&$eventList)
	{
		$map = new EventParticipantMap();
		$map->RemoveMapping($this);
		$this->_eventList = $eventList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $participantList
	*/
	function GetEventList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$event = new Event();
		$eventList = Array();
		$this->pog_query = "select distinct * from `event` a INNER JOIN `eventparticipantmap` m ON m.eventid = a.eventid where m.participantid = '$this->participantId' ";
		if (sizeof($fcv_array) > 0)
		{
			$this->pog_query .= " AND ";
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
					if (isset($event->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $event->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $event->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
					{
						if ($GLOBALS['configuration']['db_encoding'] == 1)
						{
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : "'".$fcv_array[$i][2]."'";
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$this->Escape($fcv_array[$i][2])."'";
							$this->pog_query .= "a.`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : "'".$fcv_array[$i][2]."'";
						$this->pog_query .= "a.`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
					}
				}
			}
		}
		if ($sortBy != '')
		{
			if (isset($event->pog_attribute_type[$sortBy]['db_attributes']) && $event->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $event->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
			{
				if ($GLOBALS['configuration']['db_encoding'] == 1)
				{
					$sortBy = "BASE64_DECODE(a.$sortBy) ";
				}
				else
				{
					$sortBy = "a.$sortBy ";
				}
			}
			else
			{
				$sortBy = "a.$sortBy ";
			}
		}
		else
		{
			$sortBy = "a.eventid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$cursor = Database::Reader($this->pog_query, $connection);
		while($rows = Database::Read($cursor))
		{
			$event = new Event();
			foreach ($event->pog_attribute_type as $attribute_name => $attrubute_type)
			{
				if ($attrubute_type['db_attributes'][1] != "HASMANY" && $attrubute_type['db_attributes'][1] != "JOIN")
				{
					if ($attrubute_type['db_attributes'][1] == "BELONGSTO")
					{
						$event->{strtolower($attribute_name).'Id'} = $rows[strtolower($attribute_name).'id'];
						continue;
					}
					$event->{$attribute_name} = $this->Unescape($rows[strtolower($attribute_name)]);
				}
			}
			$eventList[] = $event;
		}
		return $eventList;
	}
	
	
	/**
	* Associates the Event object to this one
	* @return 
	*/
	function AddEvent(&$event)
	{
		if ($event instanceof Event)
		{
			if (in_array($this, $event->participantList, true))
			{
				return false;
			}
			else
			{
				$found = false;
				foreach ($this->_eventList as $event2)
				{
					if ($event->eventId > 0 && $event->eventId == $event2->eventId)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$this->_eventList[] = $event;
				}
			}
		}
	}
	
	
	/**
	* Gets a list of Device objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of Device objects
	*/
	function GetDeviceList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$device = new Device();
		$fcv_array[] = array("participantId", "=", $this->participantId);
		$dbObjects = $device->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all Device objects in the Device List array. Any existing Device will become orphan(s)
	* @return null
	*/
	function SetDeviceList(&$list)
	{
		$this->_deviceList = array();
		$existingDeviceList = $this->GetDeviceList();
		foreach ($existingDeviceList as $device)
		{
			$device->participantId = '';
			$device->Save(false);
		}
		$this->_deviceList = $list;
	}
	
	
	/**
	* Associates the Device object to this one
	* @return 
	*/
	function AddDevice(&$device)
	{
		$device->participantId = $this->participantId;
		$found = false;
		foreach($this->_deviceList as $device2)
		{
			if ($device->deviceId > 0 && $device->deviceId == $device2->deviceId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_deviceList[] = $device;
		}
	}
	
	
	/**
	* Gets a list of AltEmail objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of AltEmail objects
	*/
	function GetAltemailList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$altemail = new AltEmail();
		$fcv_array[] = array("participantId", "=", $this->participantId);
		$dbObjects = $altemail->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all AltEmail objects in the AltEmail List array. Any existing AltEmail will become orphan(s)
	* @return null
	*/
	function SetAltemailList(&$list)
	{
		$this->_altemailList = array();
		$existingAltemailList = $this->GetAltemailList();
		foreach ($existingAltemailList as $altemail)
		{
			$altemail->participantId = '';
			$altemail->Save(false);
		}
		$this->_altemailList = $list;
	}
	
	
	/**
	* Associates the AltEmail object to this one
	* @return 
	*/
	function AddAltemail(&$altemail)
	{
		$altemail->participantId = $this->participantId;
		$found = false;
		foreach($this->_altemailList as $altemail2)
		{
			if ($altemail->altemailId > 0 && $altemail->altemailId == $altemail2->altemailId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_altemailList[] = $altemail;
		}
	}
}
?>