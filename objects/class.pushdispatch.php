<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `pushdispatch` (
	`pushdispatchid` int(11) NOT NULL auto_increment,
	`lastdispatch` TIMESTAMP NOT NULL,
	`generaleventupdateidlist` TEXT NOT NULL,
	`decidednotificationdispatcheventidlist` TEXT NOT NULL,
	`cancelledeventidlist` TEXT NOT NULL,
	`startednotificationdispatcheventidlist` TEXT NOT NULL, PRIMARY KEY  (`pushdispatchid`)) ENGINE=MyISAM;
*/

/**
* <b>PushDispatch</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=PushDispatch&attributeList=array+%28%0A++0+%3D%3E+%27Invite%27%2C%0A++1+%3D%3E+%27lastDispatch%27%2C%0A++2+%3D%3E+%27FeedMessage%27%2C%0A++3+%3D%3E+%27generalEventUpdateIdList%27%2C%0A++4+%3D%3E+%27decidedNotificationDispatchEventIdList%27%2C%0A++5+%3D%3E+%27cancelledEventIdList%27%2C%0A++6+%3D%3E+%27startedNotificationDispatchEventIdList%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527TEXT%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527TEXT%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527TEXT%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527TEXT%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
include_once('class.invitepushdispatchmap.php');
include_once('class.feedmessagepushdispatchmap.php');
class PushDispatch extends POG_Base
{
	public $pushdispatchId = '';

	/**
	 * @var private array of Invite objects
	 */
	private $_inviteList = array();
	
	/**
	 * @var TIMESTAMP
	 */
	public $lastDispatch;
	
	/**
	 * @var private array of FeedMessage objects
	 */
	private $_feedmessageList = array();
	
	/**
	 * @var TEXT
	 */
	public $generalEventUpdateIdList;
	
	/**
	 * @var TEXT
	 */
	public $decidedNotificationDispatchEventIdList;
	
	/**
	 * @var TEXT
	 */
	public $cancelledEventIdList;
	
	/**
	 * @var TEXT
	 */
	public $startedNotificationDispatchEventIdList;
	
	public $pog_attribute_type = array(
		"pushdispatchId" => array('db_attributes' => array("NUMERIC", "INT")),
		"Invite" => array('db_attributes' => array("OBJECT", "JOIN")),
		"lastDispatch" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"FeedMessage" => array('db_attributes' => array("OBJECT", "JOIN")),
		"generalEventUpdateIdList" => array('db_attributes' => array("TEXT", "TEXT")),
		"decidedNotificationDispatchEventIdList" => array('db_attributes' => array("TEXT", "TEXT")),
		"cancelledEventIdList" => array('db_attributes' => array("TEXT", "TEXT")),
		"startedNotificationDispatchEventIdList" => array('db_attributes' => array("TEXT", "TEXT")),
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
	
	function PushDispatch($lastDispatch='', $generalEventUpdateIdList='', $decidedNotificationDispatchEventIdList='', $cancelledEventIdList='', $startedNotificationDispatchEventIdList='')
	{
		$this->_inviteList = array();
		$this->lastDispatch = $lastDispatch;
		$this->_feedmessageList = array();
		$this->generalEventUpdateIdList = $generalEventUpdateIdList;
		$this->decidedNotificationDispatchEventIdList = $decidedNotificationDispatchEventIdList;
		$this->cancelledEventIdList = $cancelledEventIdList;
		$this->startedNotificationDispatchEventIdList = $startedNotificationDispatchEventIdList;
	}
	
	
	/**
	* Gets object from database
	* @param integer $pushdispatchId 
	* @return object $PushDispatch
	*/
	function Get($pushdispatchId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `pushdispatch` where `pushdispatchid`=:pushdispatchId LIMIT 1";
		$this->pog_bind = array(
			':pushdispatchId' => intval($pushdispatchId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->pushdispatchId = $row['pushdispatchid'];
			$this->lastDispatch = $row['lastdispatch'];
			$this->generalEventUpdateIdList = $this->Decode($row['generaleventupdateidlist']);
			$this->decidedNotificationDispatchEventIdList = $this->Decode($row['decidednotificationdispatcheventidlist']);
			$this->cancelledEventIdList = $this->Decode($row['cancelledeventidlist']);
			$this->startedNotificationDispatchEventIdList = $this->Decode($row['startednotificationdispatcheventidlist']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $pushdispatchList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `pushdispatch` ";
		$pushdispatchList = Array();
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
			$sortBy = "pushdispatchid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$pushdispatch = new $thisObjectName();
			$pushdispatch->pushdispatchId = $row['pushdispatchid'];
			$pushdispatch->lastDispatch = $row['lastdispatch'];
			$pushdispatch->generalEventUpdateIdList = $this->Unescape($row['generaleventupdateidlist']);
			$pushdispatch->decidedNotificationDispatchEventIdList = $this->Unescape($row['decidednotificationdispatcheventidlist']);
			$pushdispatch->cancelledEventIdList = $this->Unescape($row['cancelledeventidlist']);
			$pushdispatch->startedNotificationDispatchEventIdList = $this->Unescape($row['startednotificationdispatcheventidlist']);
			$pushdispatchList[] = $pushdispatch;
		}
		return $pushdispatchList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $pushdispatchId
	*/
	function Save($deep = true)
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->pushdispatchId))
		{
			$this->pog_query = "select `pushdispatchid` from `pushdispatch` where `pushdispatchid`=".$this->Quote($this->pushdispatchId, $connection)." LIMIT 1";
			$rows = Database::Query($this->pog_query, $connection);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `pushdispatch` set 
			`lastdispatch`=:lastdispatch,
			`generaleventupdateidlist`=:generaleventupdateidlist,
			`decidednotificationdispatcheventidlist`=:decidednotificationdispatcheventidlist,
			`cancelledeventidlist`=:cancelledeventidlist,
			`startednotificationdispatcheventidlist`=:startednotificationdispatcheventidlist where `pushdispatchid`=:pushdispatchId";
		}
		else
		{
			$this->pushdispatchId = "";
			$this->pog_query = "insert into `pushdispatch` (`lastdispatch`,`generaleventupdateidlist`,`decidednotificationdispatcheventidlist`,`cancelledeventidlist`,`startednotificationdispatcheventidlist`,`pushdispatchid`) values (
			:lastdispatch,
			:generaleventupdateidlist,
			:decidednotificationdispatcheventidlist,
			:cancelledeventidlist,
			:startednotificationdispatcheventidlist,
			:pushdispatchId)";
		}
		$this->pog_bind = array(
			':lastdispatch' => $this->lastDispatch,
			':generaleventupdateidlist' => $this->Encode($this->generalEventUpdateIdList),
			':decidednotificationdispatcheventidlist' => $this->Encode($this->decidedNotificationDispatchEventIdList),
			':cancelledeventidlist' => $this->Encode($this->cancelledEventIdList),
			':startednotificationdispatcheventidlist' => $this->Encode($this->startedNotificationDispatchEventIdList),
			':pushdispatchId' => intval($this->pushdispatchId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind, $connection);
		if ($this->pushdispatchId == "")
		{
			$this->pushdispatchId = $insertId;
		}
		if ($deep)
		{
			foreach ($this->_inviteList as $invite)
			{
				$invite->Save();
				$map = new InvitePushDispatchMap();
				$map->AddMapping($this, $invite);
			}
			foreach ($this->_feedmessageList as $feedmessage)
			{
				$feedmessage->Save();
				$map = new FeedMessagePushDispatchMap();
				$map->AddMapping($this, $feedmessage);
			}
		}
		return $this->pushdispatchId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $pushdispatchId
	*/
	function SaveNew($deep = false)
	{
		$this->pushdispatchId = '';
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
			$inviteList = $this->GetInviteList();
			$map = new InvitePushDispatchMap();
			$map->RemoveMapping($this);
			foreach ($inviteList as $invite)
			{
				$invite->Delete($deep, $across);
			}
			$feedmessageList = $this->GetFeedmessageList();
			$map = new FeedMessagePushDispatchMap();
			$map->RemoveMapping($this);
			foreach ($feedmessageList as $feedmessage)
			{
				$feedmessage->Delete($deep, $across);
			}
		}
		else
		{
			$map = new InvitePushDispatchMap();
			$map->RemoveMapping($this);
			$map = new FeedMessagePushDispatchMap();
			$map->RemoveMapping($this);
		}
		$connection = Database::Connect();
		$this->pog_query = "delete from `pushdispatch` where `pushdispatchid`=".$this->Quote($this->pushdispatchId, $connection);
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
				$this->pog_query = "delete from `pushdispatch` where ";
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
	* Creates mappings between this and all objects in the Invite List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetInviteList(&$inviteList)
	{
		$map = new InvitePushDispatchMap();
		$map->RemoveMapping($this);
		$this->_inviteList = $inviteList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $pushdispatchList
	*/
	function GetInviteList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$invite = new Invite();
		$inviteList = Array();
		$this->pog_query = "select distinct * from `invite` a INNER JOIN `invitepushdispatchmap` m ON m.inviteid = a.inviteid where m.pushdispatchid = '$this->pushdispatchId' ";
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
					if (isset($invite->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $invite->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $invite->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
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
			if (isset($invite->pog_attribute_type[$sortBy]['db_attributes']) && $invite->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $invite->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
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
			$sortBy = "a.inviteid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$cursor = Database::Reader($this->pog_query, $connection);
		while($rows = Database::Read($cursor))
		{
			$invite = new Invite();
			foreach ($invite->pog_attribute_type as $attribute_name => $attrubute_type)
			{
				if ($attrubute_type['db_attributes'][1] != "HASMANY" && $attrubute_type['db_attributes'][1] != "JOIN")
				{
					if ($attrubute_type['db_attributes'][1] == "BELONGSTO")
					{
						$invite->{strtolower($attribute_name).'Id'} = $rows[strtolower($attribute_name).'id'];
						continue;
					}
					$invite->{$attribute_name} = $this->Unescape($rows[strtolower($attribute_name)]);
				}
			}
			$inviteList[] = $invite;
		}
		return $inviteList;
	}
	
	
	/**
	* Associates the Invite object to this one
	* @return 
	*/
	function AddInvite(&$invite)
	{
		if ($invite instanceof Invite)
		{
			if (in_array($this, $invite->pushdispatchList, true))
			{
				return false;
			}
			else
			{
				$found = false;
				foreach ($this->_inviteList as $invite2)
				{
					if ($invite->inviteId > 0 && $invite->inviteId == $invite2->inviteId)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$this->_inviteList[] = $invite;
				}
			}
		}
	}
	
	
	/**
	* Creates mappings between this and all objects in the FeedMessage List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetFeedmessageList(&$feedmessageList)
	{
		$map = new FeedMessagePushDispatchMap();
		$map->RemoveMapping($this);
		$this->_feedmessageList = $feedmessageList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $pushdispatchList
	*/
	function GetFeedmessageList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$feedmessage = new FeedMessage();
		$feedmessageList = Array();
		$this->pog_query = "select distinct * from `feedmessage` a INNER JOIN `feedmessagepushdispatchmap` m ON m.feedmessageid = a.feedmessageid where m.pushdispatchid = '$this->pushdispatchId' ";
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
					if (isset($feedmessage->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $feedmessage->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $feedmessage->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
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
			if (isset($feedmessage->pog_attribute_type[$sortBy]['db_attributes']) && $feedmessage->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $feedmessage->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
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
			$sortBy = "a.feedmessageid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$cursor = Database::Reader($this->pog_query, $connection);
		while($rows = Database::Read($cursor))
		{
			$feedmessage = new FeedMessage();
			foreach ($feedmessage->pog_attribute_type as $attribute_name => $attrubute_type)
			{
				if ($attrubute_type['db_attributes'][1] != "HASMANY" && $attrubute_type['db_attributes'][1] != "JOIN")
				{
					if ($attrubute_type['db_attributes'][1] == "BELONGSTO")
					{
						$feedmessage->{strtolower($attribute_name).'Id'} = $rows[strtolower($attribute_name).'id'];
						continue;
					}
					$feedmessage->{$attribute_name} = $this->Unescape($rows[strtolower($attribute_name)]);
				}
			}
			$feedmessageList[] = $feedmessage;
		}
		return $feedmessageList;
	}
	
	
	/**
	* Associates the FeedMessage object to this one
	* @return 
	*/
	function AddFeedmessage(&$feedmessage)
	{
		if ($feedmessage instanceof FeedMessage)
		{
			if (in_array($this, $feedmessage->pushdispatchList, true))
			{
				return false;
			}
			else
			{
				$found = false;
				foreach ($this->_feedmessageList as $feedmessage2)
				{
					if ($feedmessage->feedmessageId > 0 && $feedmessage->feedmessageId == $feedmessage2->feedmessageId)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$this->_feedmessageList[] = $feedmessage;
				}
			}
		}
	}
}
?>