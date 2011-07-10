<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `feedmessage` (
	`feedmessageid` int(11) NOT NULL auto_increment,
	`timestamp` TIMESTAMP NOT NULL,
	`eventid` int(11) NOT NULL,
	`message` VARCHAR(255) NOT NULL,
	`imageurl` VARCHAR(255) NOT NULL,
	`type` VARCHAR(255) NOT NULL,
	`senderid` VARCHAR(255) NOT NULL,
	`readparticipantlist` BLOB NOT NULL, INDEX(`eventid`), PRIMARY KEY  (`feedmessageid`)) ENGINE=MyISAM;
*/

/**
* <b>FeedMessage</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0f / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://www.phpobjectgenerator.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=FeedMessage&attributeList=array+%28%0A++0+%3D%3E+%27timestamp%27%2C%0A++1+%3D%3E+%27Event%27%2C%0A++2+%3D%3E+%27message%27%2C%0A++3+%3D%3E+%27imageURL%27%2C%0A++4+%3D%3E+%27type%27%2C%0A++5+%3D%3E+%27senderId%27%2C%0A++6+%3D%3E+%27readParticipantList%27%2C%0A++7+%3D%3E+%27PushDispatch%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
include_once('class.feedmessagepushdispatchmap.php');
class FeedMessage extends POG_Base
{
	public $feedmessageId = '';

	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var INT(11)
	 */
	public $eventId;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $message;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $imageURL;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $type;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $senderId;
	
	/**
	 * @var BLOB
	 */
	public $readParticipantList;
	
	/**
	 * @var private array of PushDispatch objects
	 */
	private $_pushdispatchList = array();
	
	public $pog_attribute_type = array(
		"feedmessageId" => array('db_attributes' => array("NUMERIC", "INT")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"Event" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
		"message" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"imageURL" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"type" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"senderId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"readParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"PushDispatch" => array('db_attributes' => array("OBJECT", "JOIN")),
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
	
	function FeedMessage($timestamp='', $message='', $imageURL='', $type='', $senderId='', $readParticipantList='')
	{
		$this->timestamp = $timestamp;
		$this->message = $message;
		$this->imageURL = $imageURL;
		$this->type = $type;
		$this->senderId = $senderId;
		$this->readParticipantList = $readParticipantList;
		$this->_pushdispatchList = array();
	}
	
	
	/**
	* Gets object from database
	* @param integer $feedmessageId 
	* @return object $FeedMessage
	*/
	function Get($feedmessageId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `feedmessage` where `feedmessageid`='".intval($feedmessageId)."' LIMIT 1";
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->feedmessageId = $row['feedmessageid'];
			$this->timestamp = $row['timestamp'];
			$this->eventId = $row['eventid'];
			$this->message = $this->Unescape($row['message']);
			$this->imageURL = $this->Unescape($row['imageurl']);
			$this->type = $this->Unescape($row['type']);
			$this->senderId = $this->Unescape($row['senderid']);
			$this->readParticipantList = $this->Unescape($row['readparticipantlist']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $feedmessageList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `feedmessage` ";
		$feedmessageList = Array();
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
			$sortBy = "feedmessageid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$feedmessage = new $thisObjectName();
			$feedmessage->feedmessageId = $row['feedmessageid'];
			$feedmessage->timestamp = $row['timestamp'];
			$feedmessage->eventId = $row['eventid'];
			$feedmessage->message = $this->Unescape($row['message']);
			$feedmessage->imageURL = $this->Unescape($row['imageurl']);
			$feedmessage->type = $this->Unescape($row['type']);
			$feedmessage->senderId = $this->Unescape($row['senderid']);
			$feedmessage->readParticipantList = $this->Unescape($row['readparticipantlist']);
			$feedmessageList[] = $feedmessage;
		}
		return $feedmessageList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $feedmessageId
	*/
	function Save($deep = true)
	{
		$connection = Database::Connect();
		$this->pog_query = "select `feedmessageid` from `feedmessage` where `feedmessageid`='".$this->feedmessageId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows > 0)
		{
			$this->pog_query = "update `feedmessage` set 
			`timestamp`='".$this->timestamp."', 
			`eventid`='".$this->eventId."', 
			`message`='".$this->Escape($this->message)."', 
			`imageurl`='".$this->Escape($this->imageURL)."', 
			`type`='".$this->Escape($this->type)."', 
			`senderid`='".$this->Escape($this->senderId)."', 
			`readparticipantlist`='".$this->Escape($this->readParticipantList)."'where `feedmessageid`='".$this->feedmessageId."'";
		}
		else
		{
			$this->pog_query = "insert into `feedmessage` (`timestamp`, `eventid`, `message`, `imageurl`, `type`, `senderid`, `readparticipantlist`) values (
			'".$this->timestamp."', 
			'".$this->eventId."', 
			'".$this->Escape($this->message)."', 
			'".$this->Escape($this->imageURL)."', 
			'".$this->Escape($this->type)."', 
			'".$this->Escape($this->senderId)."', 
			'".$this->Escape($this->readParticipantList)."')";
		}
		$insertId = Database::InsertOrUpdate($this->pog_query, $connection);
		if ($this->feedmessageId == "")
		{
			$this->feedmessageId = $insertId;
		}
		if ($deep)
		{
			foreach ($this->_pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Save();
				$map = new FeedMessagePushDispatchMap();
				$map->AddMapping($this, $pushdispatch);
			}
		}
		return $this->feedmessageId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $feedmessageId
	*/
	function SaveNew($deep = false)
	{
		$this->feedmessageId = '';
		return $this->Save($deep);
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete($deep = false, $across = false)
	{
		if ($across)
		{
			$pushdispatchList = $this->GetPushdispatchList();
			$map = new FeedMessagePushDispatchMap();
			$map->RemoveMapping($this);
			foreach ($pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Delete($deep, $across);
			}
		}
		else
		{
			$map = new FeedMessagePushDispatchMap();
			$map->RemoveMapping($this);
		}
		$connection = Database::Connect();
		$this->pog_query = "delete from `feedmessage` where `feedmessageid`='".$this->feedmessageId."'";
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
				$pog_query = "delete from `feedmessage` where ";
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
	
	
	/**
	* Creates mappings between this and all objects in the PushDispatch List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetPushdispatchList(&$pushdispatchList)
	{
		$map = new FeedMessagePushDispatchMap();
		$map->RemoveMapping($this);
		$this->_pushdispatchList = $pushdispatchList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $feedmessageList
	*/
	function GetPushdispatchList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$pushdispatch = new PushDispatch();
		$pushdispatchList = Array();
		$this->pog_query = "select distinct * from `pushdispatch` a INNER JOIN `feedmessagepushdispatchmap` m ON m.pushdispatchid = a.pushdispatchid where m.feedmessageid = '$this->feedmessageId' ";
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
					if (isset($pushdispatch->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $pushdispatch->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $pushdispatch->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
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
			if (isset($pushdispatch->pog_attribute_type[$sortBy]['db_attributes']) && $pushdispatch->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $pushdispatch->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
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
			$sortBy = "a.pushdispatchid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$cursor = Database::Reader($this->pog_query, $connection);
		while($rows = Database::Read($cursor))
		{
			$pushdispatch = new PushDispatch();
			foreach ($pushdispatch->pog_attribute_type as $attribute_name => $attrubute_type)
			{
				if ($attrubute_type['db_attributes'][1] != "HASMANY" && $attrubute_type['db_attributes'][1] != "JOIN")
				{
					if ($attrubute_type['db_attributes'][1] == "BELONGSTO")
					{
						$pushdispatch->{strtolower($attribute_name).'Id'} = $rows[strtolower($attribute_name).'id'];
						continue;
					}
					$pushdispatch->{$attribute_name} = $this->Unescape($rows[strtolower($attribute_name)]);
				}
			}
			$pushdispatchList[] = $pushdispatch;
		}
		return $pushdispatchList;
	}
	
	
	/**
	* Associates the PushDispatch object to this one
	* @return 
	*/
	function AddPushdispatch(&$pushdispatch)
	{
		if ($pushdispatch instanceof PushDispatch)
		{
			if (in_array($this, $pushdispatch->feedmessageList, true))
			{
				return false;
			}
			else
			{
				$found = false;
				foreach ($this->_pushdispatchList as $pushdispatch2)
				{
					if ($pushdispatch->pushdispatchId > 0 && $pushdispatch->pushdispatchId == $pushdispatch2->pushdispatchId)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$this->_pushdispatchList[] = $pushdispatch;
				}
			}
		}
	}
}
?>