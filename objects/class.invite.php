<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `invite` (
	`inviteid` int(11) NOT NULL auto_increment,
	`inviterid` VARCHAR(255) NOT NULL,
	`inviteeid` VARCHAR(255) NOT NULL,
	`eventid` int(11) NOT NULL,
	`token` VARCHAR(255) NOT NULL,
	`pending` TINYINT NOT NULL,
	`hasbeenremoved` TINYINT NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`sent` TINYINT NOT NULL, INDEX(`eventid`), PRIMARY KEY  (`inviteid`)) ENGINE=MyISAM;
*/

/**
* <b>Invite</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=Invite&attributeList=array+%28%0A++0+%3D%3E+%27inviterId%27%2C%0A++1+%3D%3E+%27inviteeId%27%2C%0A++2+%3D%3E+%27Event%27%2C%0A++3+%3D%3E+%27PushDispatch%27%2C%0A++4+%3D%3E+%27token%27%2C%0A++5+%3D%3E+%27pending%27%2C%0A++6+%3D%3E+%27hasBeenRemoved%27%2C%0A++7+%3D%3E+%27timestamp%27%2C%0A++8+%3D%3E+%27sent%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B8%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
include_once('class.invitepushdispatchmap.php');
class Invite extends POG_Base
{
	public $inviteId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $inviterId;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $inviteeId;
	
	/**
	 * @var INT(11)
	 */
	public $eventId;
	
	/**
	 * @var private array of PushDispatch objects
	 */
	private $_pushdispatchList = array();
	
	/**
	 * @var VARCHAR(255)
	 */
	public $token;
	
	/**
	 * @var TINYINT
	 */
	public $pending;
	
	/**
	 * @var TINYINT
	 */
	public $hasBeenRemoved;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var TINYINT
	 */
	public $sent;
	
	public $pog_attribute_type = array(
		"inviteId" => array('db_attributes' => array("NUMERIC", "INT")),
		"inviterId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"inviteeId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Event" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
		"PushDispatch" => array('db_attributes' => array("OBJECT", "JOIN")),
		"token" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"pending" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"hasBeenRemoved" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"sent" => array('db_attributes' => array("NUMERIC", "TINYINT")),
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
	
	function Invite($inviterId='', $inviteeId='', $token='', $pending='', $hasBeenRemoved='', $timestamp='', $sent='')
	{
		$this->inviterId = $inviterId;
		$this->inviteeId = $inviteeId;
		$this->_pushdispatchList = array();
		$this->token = $token;
		$this->pending = $pending;
		$this->hasBeenRemoved = $hasBeenRemoved;
		$this->timestamp = $timestamp;
		$this->sent = $sent;
	}
	
	
	/**
	* Gets object from database
	* @param integer $inviteId 
	* @return object $Invite
	*/
	function Get($inviteId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `invite` where `inviteid`=:inviteId LIMIT 1";
		$this->pog_bind = array(
			':inviteId' => intval($inviteId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->inviteId = $row['inviteid'];
			$this->inviterId = $this->Decode($row['inviterid']);
			$this->inviteeId = $this->Decode($row['inviteeid']);
			$this->eventId = $row['eventid'];
			$this->token = $this->Decode($row['token']);
			$this->pending = $this->Decode($row['pending']);
			$this->hasBeenRemoved = $this->Decode($row['hasbeenremoved']);
			$this->timestamp = $row['timestamp'];
			$this->sent = $this->Decode($row['sent']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $inviteList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `invite` ";
		$inviteList = Array();
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
			$sortBy = "inviteid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$invite = new $thisObjectName();
			$invite->inviteId = $row['inviteid'];
			$invite->inviterId = $this->Unescape($row['inviterid']);
			$invite->inviteeId = $this->Unescape($row['inviteeid']);
			$invite->eventId = $row['eventid'];
			$invite->token = $this->Unescape($row['token']);
			$invite->pending = $this->Unescape($row['pending']);
			$invite->hasBeenRemoved = $this->Unescape($row['hasbeenremoved']);
			$invite->timestamp = $row['timestamp'];
			$invite->sent = $this->Unescape($row['sent']);
			$inviteList[] = $invite;
		}
		return $inviteList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $inviteId
	*/
	function Save($deep = true)
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->inviteId))
		{
			$this->pog_query = "select `inviteid` from `invite` where `inviteid`=".$this->Quote($this->inviteId, $connection)." LIMIT 1";
			$rows = Database::Query($this->pog_query, $connection);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `invite` set 
			`inviterid`=:inviterid,
			`inviteeid`=:inviteeid,
			`eventid`=:eventId,
			`token`=:token,
			`pending`=:pending,
			`hasbeenremoved`=:hasbeenremoved,
			`timestamp`=:timestamp,
			`sent`=:sent where `inviteid`=:inviteId";
		}
		else
		{
			$this->inviteId = "";
			$this->pog_query = "insert into `invite` (`inviterid`,`inviteeid`,`eventid`,`token`,`pending`,`hasbeenremoved`,`timestamp`,`sent`,`inviteid`) values (
			:inviterid,
			:inviteeid,
			:eventId,
			:token,
			:pending,
			:hasbeenremoved,
			:timestamp,
			:sent,
			:inviteId)";
		}
		$this->pog_bind = array(
			':inviterid' => $this->Encode($this->inviterId),
			':inviteeid' => $this->Encode($this->inviteeId),
			':eventId' => intval($this->eventId),
			':token' => $this->Encode($this->token),
			':pending' => $this->Encode($this->pending),
			':hasbeenremoved' => $this->Encode($this->hasBeenRemoved),
			':timestamp' => $this->timestamp,
			':sent' => $this->Encode($this->sent),
			':inviteId' => intval($this->inviteId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind, $connection);
		if ($this->inviteId == "")
		{
			$this->inviteId = $insertId;
		}
		if ($deep)
		{
			foreach ($this->_pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Save();
				$map = new InvitePushDispatchMap();
				$map->AddMapping($this, $pushdispatch);
			}
		}
		return $this->inviteId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $inviteId
	*/
	function SaveNew($deep = false)
	{
		$this->inviteId = '';
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
			$map = new InvitePushDispatchMap();
			$map->RemoveMapping($this);
			foreach ($pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Delete($deep, $across);
			}
		}
		else
		{
			$map = new InvitePushDispatchMap();
			$map->RemoveMapping($this);
		}
		$connection = Database::Connect();
		$this->pog_query = "delete from `invite` where `inviteid`=".$this->Quote($this->inviteId, $connection);
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
				$this->pog_query = "delete from `invite` where ";
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
		$map = new InvitePushDispatchMap();
		$map->RemoveMapping($this);
		$this->_pushdispatchList = $pushdispatchList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $inviteList
	*/
	function GetPushdispatchList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$pushdispatch = new PushDispatch();
		$pushdispatchList = Array();
		$this->pog_query = "select distinct * from `pushdispatch` a INNER JOIN `invitepushdispatchmap` m ON m.pushdispatchid = a.pushdispatchid where m.inviteid = '$this->inviteId' ";
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
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2], $connection);
							$this->pog_query .= "a.`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2], $connection);
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
			if (in_array($this, $pushdispatch->inviteList, true))
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