<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `event` (
	`eventid` int(11) NOT NULL auto_increment,
	`eventtitle` VARCHAR(255) NOT NULL,
	`eventdescription` VARCHAR(255) NOT NULL,
	`creatorid` VARCHAR(255) NOT NULL,
	`readparticipantlist` BLOB NOT NULL,
	`timestamp` TIMESTAMP NOT NULL,
	`infotimestamp` TIMESTAMP NOT NULL,
	`guestlistopen` TINYINT NOT NULL,
	`locationlistopen` TINYINT NOT NULL,
	`checkedinparticipantlist` BLOB NOT NULL,
	`locationreordertimestamp` TIMESTAMP NOT NULL,
	`acceptedparticipantlist` BLOB NOT NULL,
	`declinedparticipantlist` BLOB NOT NULL,
	`eventdate` DATETIME NOT NULL,
	`eventexpiredate` DATETIME NOT NULL,
	`removedparticipantlist` BLOB NOT NULL,
	`eventtimezone` VARCHAR(255) NOT NULL,
	`cancelled` TINYINT NOT NULL, PRIMARY KEY  (`eventid`)) ENGINE=MyISAM;
*/

/**
* <b>Event</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0f / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://www.phpobjectgenerator.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=Event&attributeList=array+%28%0A++0+%3D%3E+%27eventTitle%27%2C%0A++1+%3D%3E+%27eventDescription%27%2C%0A++2+%3D%3E+%27creatorId%27%2C%0A++3+%3D%3E+%27Location%27%2C%0A++4+%3D%3E+%27Participant%27%2C%0A++5+%3D%3E+%27Vote%27%2C%0A++6+%3D%3E+%27readParticipantList%27%2C%0A++7+%3D%3E+%27timestamp%27%2C%0A++8+%3D%3E+%27infoTimestamp%27%2C%0A++9+%3D%3E+%27Invite%27%2C%0A++10+%3D%3E+%27guestListOpen%27%2C%0A++11+%3D%3E+%27locationListOpen%27%2C%0A++12+%3D%3E+%27PushDispatch%27%2C%0A++13+%3D%3E+%27FeedMessage%27%2C%0A++14+%3D%3E+%27checkedInParticipantList%27%2C%0A++15+%3D%3E+%27ReportLocation%27%2C%0A++16+%3D%3E+%27locationReorderTimestamp%27%2C%0A++17+%3D%3E+%27acceptedParticipantList%27%2C%0A++18+%3D%3E+%27declinedParticipantList%27%2C%0A++19+%3D%3E+%27eventDate%27%2C%0A++20+%3D%3E+%27eventExpireDate%27%2C%0A++21+%3D%3E+%27removedParticipantList%27%2C%0A++22+%3D%3E+%27SuggestedTime%27%2C%0A++23+%3D%3E+%27eventTimeZone%27%2C%0A++24+%3D%3E+%27cancelled%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B4%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B5%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B6%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B7%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B8%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B9%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B10%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B11%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2B%2B12%2B%253D%253E%2B%2527JOIN%2527%252C%250A%2B%2B13%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B14%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B15%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B16%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B17%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B18%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B19%2B%253D%253E%2B%2527DATETIME%2527%252C%250A%2B%2B20%2B%253D%253E%2B%2527DATETIME%2527%252C%250A%2B%2B21%2B%253D%253E%2B%2527BLOB%2527%252C%250A%2B%2B22%2B%253D%253E%2B%2527HASMANY%2527%252C%250A%2B%2B23%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B24%2B%253D%253E%2B%2527TINYINT%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
include_once('class.eventparticipantmap.php');
include_once('class.eventpushdispatchmap.php');
class Event extends POG_Base
{
	public $eventId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $eventTitle;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $eventDescription;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $creatorId;
	
	/**
	 * @var private array of Location objects
	 */
	private $_locationList = array();
	
	/**
	 * @var private array of Participant objects
	 */
	private $_participantList = array();
	
	/**
	 * @var private array of Vote objects
	 */
	private $_voteList = array();
	
	/**
	 * @var BLOB
	 */
	public $readParticipantList;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var TIMESTAMP
	 */
	public $infoTimestamp;
	
	/**
	 * @var private array of Invite objects
	 */
	private $_inviteList = array();
	
	/**
	 * @var TINYINT
	 */
	public $guestListOpen;
	
	/**
	 * @var TINYINT
	 */
	public $locationListOpen;
	
	/**
	 * @var private array of PushDispatch objects
	 */
	private $_pushdispatchList = array();
	
	/**
	 * @var private array of FeedMessage objects
	 */
	private $_feedmessageList = array();
	
	/**
	 * @var BLOB
	 */
	public $checkedInParticipantList;
	
	/**
	 * @var private array of ReportLocation objects
	 */
	private $_reportlocationList = array();
	
	/**
	 * @var TIMESTAMP
	 */
	public $locationReorderTimestamp;
	
	/**
	 * @var BLOB
	 */
	public $acceptedParticipantList;
	
	/**
	 * @var BLOB
	 */
	public $declinedParticipantList;
	
	/**
	 * @var DATETIME
	 */
	public $eventDate;
	
	/**
	 * @var DATETIME
	 */
	public $eventExpireDate;
	
	/**
	 * @var BLOB
	 */
	public $removedParticipantList;
	
	/**
	 * @var private array of SuggestedTime objects
	 */
	private $_suggestedtimeList = array();
	
	/**
	 * @var VARCHAR(255)
	 */
	public $eventTimeZone;
	
	/**
	 * @var TINYINT
	 */
	public $cancelled;
	
	public $pog_attribute_type = array(
		"eventId" => array('db_attributes' => array("NUMERIC", "INT")),
		"eventTitle" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"eventDescription" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"creatorId" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Location" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"Participant" => array('db_attributes' => array("OBJECT", "JOIN")),
		"Vote" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"readParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"infoTimestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"Invite" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"guestListOpen" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"locationListOpen" => array('db_attributes' => array("NUMERIC", "TINYINT")),
		"PushDispatch" => array('db_attributes' => array("OBJECT", "JOIN")),
		"FeedMessage" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"checkedInParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"ReportLocation" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"locationReorderTimestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"acceptedParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"declinedParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"eventDate" => array('db_attributes' => array("TEXT", "DATETIME")),
		"eventExpireDate" => array('db_attributes' => array("TEXT", "DATETIME")),
		"removedParticipantList" => array('db_attributes' => array("TEXT", "BLOB")),
		"SuggestedTime" => array('db_attributes' => array("OBJECT", "HASMANY")),
		"eventTimeZone" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"cancelled" => array('db_attributes' => array("NUMERIC", "TINYINT")),
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
	
	function Event($eventTitle='', $eventDescription='', $creatorId='', $readParticipantList='', $timestamp='', $infoTimestamp='', $guestListOpen='', $locationListOpen='', $checkedInParticipantList='', $locationReorderTimestamp='', $acceptedParticipantList='', $declinedParticipantList='', $eventDate='', $eventExpireDate='', $removedParticipantList='', $eventTimeZone='', $cancelled='')
	{
		$this->eventTitle = $eventTitle;
		$this->eventDescription = $eventDescription;
		$this->creatorId = $creatorId;
		$this->_locationList = array();
		$this->_participantList = array();
		$this->_voteList = array();
		$this->readParticipantList = $readParticipantList;
		$this->timestamp = $timestamp;
		$this->infoTimestamp = $infoTimestamp;
		$this->_inviteList = array();
		$this->guestListOpen = $guestListOpen;
		$this->locationListOpen = $locationListOpen;
		$this->_pushdispatchList = array();
		$this->_feedmessageList = array();
		$this->checkedInParticipantList = $checkedInParticipantList;
		$this->_reportlocationList = array();
		$this->locationReorderTimestamp = $locationReorderTimestamp;
		$this->acceptedParticipantList = $acceptedParticipantList;
		$this->declinedParticipantList = $declinedParticipantList;
		$this->eventDate = $eventDate;
		$this->eventExpireDate = $eventExpireDate;
		$this->removedParticipantList = $removedParticipantList;
		$this->_suggestedtimeList = array();
		$this->eventTimeZone = $eventTimeZone;
		$this->cancelled = $cancelled;
	}
	
	
	/**
	* Gets object from database
	* @param integer $eventId 
	* @return object $Event
	*/
	function Get($eventId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `event` where `eventid`='".intval($eventId)."' LIMIT 1";
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->eventId = $row['eventid'];
			$this->eventTitle = $this->Unescape($row['eventtitle']);
			$this->eventDescription = $this->Unescape($row['eventdescription']);
			$this->creatorId = $this->Unescape($row['creatorid']);
			$this->readParticipantList = $this->Unescape($row['readparticipantlist']);
			$this->timestamp = $row['timestamp'];
			$this->infoTimestamp = $row['infotimestamp'];
			$this->guestListOpen = $this->Unescape($row['guestlistopen']);
			$this->locationListOpen = $this->Unescape($row['locationlistopen']);
			$this->checkedInParticipantList = $this->Unescape($row['checkedinparticipantlist']);
			$this->locationReorderTimestamp = $row['locationreordertimestamp'];
			$this->acceptedParticipantList = $this->Unescape($row['acceptedparticipantlist']);
			$this->declinedParticipantList = $this->Unescape($row['declinedparticipantlist']);
			$this->eventDate = $row['eventdate'];
			$this->eventExpireDate = $row['eventexpiredate'];
			$this->removedParticipantList = $this->Unescape($row['removedparticipantlist']);
			$this->eventTimeZone = $this->Unescape($row['eventtimezone']);
			$this->cancelled = $this->Unescape($row['cancelled']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $eventList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `event` ";
		$eventList = Array();
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
			$sortBy = "eventid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$event = new $thisObjectName();
			$event->eventId = $row['eventid'];
			$event->eventTitle = $this->Unescape($row['eventtitle']);
			$event->eventDescription = $this->Unescape($row['eventdescription']);
			$event->creatorId = $this->Unescape($row['creatorid']);
			$event->readParticipantList = $this->Unescape($row['readparticipantlist']);
			$event->timestamp = $row['timestamp'];
			$event->infoTimestamp = $row['infotimestamp'];
			$event->guestListOpen = $this->Unescape($row['guestlistopen']);
			$event->locationListOpen = $this->Unescape($row['locationlistopen']);
			$event->checkedInParticipantList = $this->Unescape($row['checkedinparticipantlist']);
			$event->locationReorderTimestamp = $row['locationreordertimestamp'];
			$event->acceptedParticipantList = $this->Unescape($row['acceptedparticipantlist']);
			$event->declinedParticipantList = $this->Unescape($row['declinedparticipantlist']);
			$event->eventDate = $row['eventdate'];
			$event->eventExpireDate = $row['eventexpiredate'];
			$event->removedParticipantList = $this->Unescape($row['removedparticipantlist']);
			$event->eventTimeZone = $this->Unescape($row['eventtimezone']);
			$event->cancelled = $this->Unescape($row['cancelled']);
			$eventList[] = $event;
		}
		return $eventList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $eventId
	*/
	function Save($deep = true)
	{
		$connection = Database::Connect();
		$this->pog_query = "select `eventid` from `event` where `eventid`='".$this->eventId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows > 0)
		{
			$this->pog_query = "update `event` set 
			`eventtitle`='".$this->Escape($this->eventTitle)."', 
			`eventdescription`='".$this->Escape($this->eventDescription)."', 
			`creatorid`='".$this->Escape($this->creatorId)."', 
			`readparticipantlist`='".$this->Escape($this->readParticipantList)."', 
			`timestamp`='".$this->timestamp."', 
			`infotimestamp`='".$this->infoTimestamp."', 
			`guestlistopen`='".$this->Escape($this->guestListOpen)."', 
			`locationlistopen`='".$this->Escape($this->locationListOpen)."', 
			`checkedinparticipantlist`='".$this->Escape($this->checkedInParticipantList)."', 
			`locationreordertimestamp`='".$this->locationReorderTimestamp."', 
			`acceptedparticipantlist`='".$this->Escape($this->acceptedParticipantList)."', 
			`declinedparticipantlist`='".$this->Escape($this->declinedParticipantList)."', 
			`eventdate`='".$this->eventDate."', 
			`eventexpiredate`='".$this->eventExpireDate."', 
			`removedparticipantlist`='".$this->Escape($this->removedParticipantList)."', 
			`eventtimezone`='".$this->Escape($this->eventTimeZone)."', 
			`cancelled`='".$this->Escape($this->cancelled)."' where `eventid`='".$this->eventId."'";
		}
		else
		{
			$this->pog_query = "insert into `event` (`eventtitle`, `eventdescription`, `creatorid`, `readparticipantlist`, `timestamp`, `infotimestamp`, `guestlistopen`, `locationlistopen`, `checkedinparticipantlist`, `locationreordertimestamp`, `acceptedparticipantlist`, `declinedparticipantlist`, `eventdate`, `eventexpiredate`, `removedparticipantlist`, `eventtimezone`, `cancelled` ) values (
			'".$this->Escape($this->eventTitle)."', 
			'".$this->Escape($this->eventDescription)."', 
			'".$this->Escape($this->creatorId)."', 
			'".$this->Escape($this->readParticipantList)."', 
			'".$this->timestamp."', 
			'".$this->infoTimestamp."', 
			'".$this->Escape($this->guestListOpen)."', 
			'".$this->Escape($this->locationListOpen)."', 
			'".$this->Escape($this->checkedInParticipantList)."', 
			'".$this->locationReorderTimestamp."', 
			'".$this->Escape($this->acceptedParticipantList)."', 
			'".$this->Escape($this->declinedParticipantList)."', 
			'".$this->eventDate."', 
			'".$this->eventExpireDate."', 
			'".$this->Escape($this->removedParticipantList)."', 
			'".$this->Escape($this->eventTimeZone)."', 
			'".$this->Escape($this->cancelled)."' )";
		}
		$insertId = Database::InsertOrUpdate($this->pog_query, $connection);
		if ($this->eventId == "")
		{
			$this->eventId = $insertId;
		}
		if ($deep)
		{
			foreach ($this->_locationList as $location)
			{
				$location->eventId = $this->eventId;
				$location->Save($deep);
			}
			foreach ($this->_participantList as $participant)
			{
				$participant->Save();
				$map = new EventParticipantMap();
				$map->AddMapping($this, $participant);
			}
			foreach ($this->_voteList as $vote)
			{
				$vote->eventId = $this->eventId;
				$vote->Save($deep);
			}
			foreach ($this->_inviteList as $invite)
			{
				$invite->eventId = $this->eventId;
				$invite->Save($deep);
			}
			foreach ($this->_pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Save();
				$map = new EventPushDispatchMap();
				$map->AddMapping($this, $pushdispatch);
			}
			foreach ($this->_feedmessageList as $feedmessage)
			{
				$feedmessage->eventId = $this->eventId;
				$feedmessage->Save($deep);
			}
			foreach ($this->_reportlocationList as $reportlocation)
			{
				$reportlocation->eventId = $this->eventId;
				$reportlocation->Save($deep);
			}
			foreach ($this->_suggestedtimeList as $suggestedtime)
			{
				$suggestedtime->eventId = $this->eventId;
				$suggestedtime->Save($deep);
			}
		}
		return $this->eventId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $eventId
	*/
	function SaveNew($deep = false)
	{
		$this->eventId = '';
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
			$locationList = $this->GetLocationList();
			foreach ($locationList as $location)
			{
				$location->Delete($deep, $across);
			}
			$voteList = $this->GetVoteList();
			foreach ($voteList as $vote)
			{
				$vote->Delete($deep, $across);
			}
			$inviteList = $this->GetInviteList();
			foreach ($inviteList as $invite)
			{
				$invite->Delete($deep, $across);
			}
			$feedmessageList = $this->GetFeedmessageList();
			foreach ($feedmessageList as $feedmessage)
			{
				$feedmessage->Delete($deep, $across);
			}
			$reportlocationList = $this->GetReportlocationList();
			foreach ($reportlocationList as $reportlocation)
			{
				$reportlocation->Delete($deep, $across);
			}
			$suggestedtimeList = $this->GetSuggestedtimeList();
			foreach ($suggestedtimeList as $suggestedtime)
			{
				$suggestedtime->Delete($deep, $across);
			}
		}
		if ($across)
		{
			$participantList = $this->GetParticipantList();
			$map = new EventParticipantMap();
			$map->RemoveMapping($this);
			foreach ($participantList as $participant)
			{
				$participant->Delete($deep, $across);
			}
			$pushdispatchList = $this->GetPushdispatchList();
			$map = new EventPushDispatchMap();
			$map->RemoveMapping($this);
			foreach ($pushdispatchList as $pushdispatch)
			{
				$pushdispatch->Delete($deep, $across);
			}
		}
		else
		{
			$map = new EventParticipantMap();
			$map->RemoveMapping($this);
			$map = new EventPushDispatchMap();
			$map->RemoveMapping($this);
		}
		$connection = Database::Connect();
		$this->pog_query = "delete from `event` where `eventid`='".$this->eventId."'";
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
				$pog_query = "delete from `event` where ";
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
	* Gets a list of Location objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of Location objects
	*/
	function GetLocationList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$location = new Location();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $location->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all Location objects in the Location List array. Any existing Location will become orphan(s)
	* @return null
	*/
	function SetLocationList(&$list)
	{
		$this->_locationList = array();
		$existingLocationList = $this->GetLocationList();
		foreach ($existingLocationList as $location)
		{
			$location->eventId = '';
			$location->Save(false);
		}
		$this->_locationList = $list;
	}
	
	
	/**
	* Associates the Location object to this one
	* @return 
	*/
	function AddLocation(&$location)
	{
		$location->eventId = $this->eventId;
		$found = false;
		foreach($this->_locationList as $location2)
		{
			if ($location->locationId > 0 && $location->locationId == $location2->locationId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_locationList[] = $location;
		}
	}
	
	
	/**
	* Creates mappings between this and all objects in the Participant List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetParticipantList(&$participantList)
	{
		$map = new EventParticipantMap();
		$map->RemoveMapping($this);
		$this->_participantList = $participantList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $eventList
	*/
	function GetParticipantList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$participant = new Participant();
		$participantList = Array();
		$this->pog_query = "select distinct * from `participant` a INNER JOIN `eventparticipantmap` m ON m.participantid = a.participantid where m.eventid = '$this->eventId' ";
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
					if (isset($participant->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && $participant->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'NUMERIC' && $participant->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] != 'SET')
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
			if (isset($participant->pog_attribute_type[$sortBy]['db_attributes']) && $participant->pog_attribute_type[$sortBy]['db_attributes'][0] != 'NUMERIC' && $participant->pog_attribute_type[$sortBy]['db_attributes'][0] != 'SET')
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
			$sortBy = "a.participantid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$cursor = Database::Reader($this->pog_query, $connection);
		while($rows = Database::Read($cursor))
		{
			$participant = new Participant();
			foreach ($participant->pog_attribute_type as $attribute_name => $attrubute_type)
			{
				if ($attrubute_type['db_attributes'][1] != "HASMANY" && $attrubute_type['db_attributes'][1] != "JOIN")
				{
					if ($attrubute_type['db_attributes'][1] == "BELONGSTO")
					{
						$participant->{strtolower($attribute_name).'Id'} = $rows[strtolower($attribute_name).'id'];
						continue;
					}
					$participant->{$attribute_name} = $this->Unescape($rows[strtolower($attribute_name)]);
				}
			}
			$participantList[] = $participant;
		}
		return $participantList;
	}
	
	
	/**
	* Associates the Participant object to this one
	* @return 
	*/
	function AddParticipant(&$participant)
	{
		if ($participant instanceof Participant)
		{
			if (in_array($this, $participant->eventList, true))
			{
				return false;
			}
			else
			{
				$found = false;
				foreach ($this->_participantList as $participant2)
				{
					if ($participant->participantId > 0 && $participant->participantId == $participant2->participantId)
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					$this->_participantList[] = $participant;
				}
			}
		}
	}
	
	
	/**
	* Gets a list of Vote objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of Vote objects
	*/
	function GetVoteList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$vote = new Vote();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $vote->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all Vote objects in the Vote List array. Any existing Vote will become orphan(s)
	* @return null
	*/
	function SetVoteList(&$list)
	{
		$this->_voteList = array();
		$existingVoteList = $this->GetVoteList();
		foreach ($existingVoteList as $vote)
		{
			$vote->eventId = '';
			$vote->Save(false);
		}
		$this->_voteList = $list;
	}
	
	
	/**
	* Associates the Vote object to this one
	* @return 
	*/
	function AddVote(&$vote)
	{
		$vote->eventId = $this->eventId;
		$found = false;
		foreach($this->_voteList as $vote2)
		{
			if ($vote->voteId > 0 && $vote->voteId == $vote2->voteId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_voteList[] = $vote;
		}
	}
	
	
	/**
	* Gets a list of Invite objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of Invite objects
	*/
	function GetInviteList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$invite = new Invite();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $invite->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all Invite objects in the Invite List array. Any existing Invite will become orphan(s)
	* @return null
	*/
	function SetInviteList(&$list)
	{
		$this->_inviteList = array();
		$existingInviteList = $this->GetInviteList();
		foreach ($existingInviteList as $invite)
		{
			$invite->eventId = '';
			$invite->Save(false);
		}
		$this->_inviteList = $list;
	}
	
	
	/**
	* Associates the Invite object to this one
	* @return 
	*/
	function AddInvite(&$invite)
	{
		$invite->eventId = $this->eventId;
		$found = false;
		foreach($this->_inviteList as $invite2)
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
	
	
	/**
	* Creates mappings between this and all objects in the PushDispatch List array. Any existing mapping will become orphan(s)
	* @return null
	*/
	function SetPushdispatchList(&$pushdispatchList)
	{
		$map = new EventPushDispatchMap();
		$map->RemoveMapping($this);
		$this->_pushdispatchList = $pushdispatchList;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $eventList
	*/
	function GetPushdispatchList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$connection = Database::Connect();
		$pushdispatch = new PushDispatch();
		$pushdispatchList = Array();
		$this->pog_query = "select distinct * from `pushdispatch` a INNER JOIN `eventpushdispatchmap` m ON m.pushdispatchid = a.pushdispatchid where m.eventid = '$this->eventId' ";
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
			if (in_array($this, $pushdispatch->eventList, true))
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
	
	
	/**
	* Gets a list of FeedMessage objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of FeedMessage objects
	*/
	function GetFeedmessageList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$feedmessage = new FeedMessage();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $feedmessage->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all FeedMessage objects in the FeedMessage List array. Any existing FeedMessage will become orphan(s)
	* @return null
	*/
	function SetFeedmessageList(&$list)
	{
		$this->_feedmessageList = array();
		$existingFeedmessageList = $this->GetFeedmessageList();
		foreach ($existingFeedmessageList as $feedmessage)
		{
			$feedmessage->eventId = '';
			$feedmessage->Save(false);
		}
		$this->_feedmessageList = $list;
	}
	
	
	/**
	* Associates the FeedMessage object to this one
	* @return 
	*/
	function AddFeedmessage(&$feedmessage)
	{
		$feedmessage->eventId = $this->eventId;
		$found = false;
		foreach($this->_feedmessageList as $feedmessage2)
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
	
	
	/**
	* Gets a list of ReportLocation objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of ReportLocation objects
	*/
	function GetReportlocationList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$reportlocation = new ReportLocation();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $reportlocation->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all ReportLocation objects in the ReportLocation List array. Any existing ReportLocation will become orphan(s)
	* @return null
	*/
	function SetReportlocationList(&$list)
	{
		$this->_reportlocationList = array();
		$existingReportlocationList = $this->GetReportlocationList();
		foreach ($existingReportlocationList as $reportlocation)
		{
			$reportlocation->eventId = '';
			$reportlocation->Save(false);
		}
		$this->_reportlocationList = $list;
	}
	
	
	/**
	* Associates the ReportLocation object to this one
	* @return 
	*/
	function AddReportlocation(&$reportlocation)
	{
		$reportlocation->eventId = $this->eventId;
		$found = false;
		foreach($this->_reportlocationList as $reportlocation2)
		{
			if ($reportlocation->reportlocationId > 0 && $reportlocation->reportlocationId == $reportlocation2->reportlocationId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_reportlocationList[] = $reportlocation;
		}
	}
	
	
	/**
	* Gets a list of SuggestedTime objects associated to this one
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array of SuggestedTime objects
	*/
	function GetSuggestedtimeList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$suggestedtime = new SuggestedTime();
		$fcv_array[] = array("eventId", "=", $this->eventId);
		$dbObjects = $suggestedtime->GetList($fcv_array, $sortBy, $ascending, $limit);
		return $dbObjects;
	}
	
	
	/**
	* Makes this the parent of all SuggestedTime objects in the SuggestedTime List array. Any existing SuggestedTime will become orphan(s)
	* @return null
	*/
	function SetSuggestedtimeList(&$list)
	{
		$this->_suggestedtimeList = array();
		$existingSuggestedtimeList = $this->GetSuggestedtimeList();
		foreach ($existingSuggestedtimeList as $suggestedtime)
		{
			$suggestedtime->eventId = '';
			$suggestedtime->Save(false);
		}
		$this->_suggestedtimeList = $list;
	}
	
	
	/**
	* Associates the SuggestedTime object to this one
	* @return 
	*/
	function AddSuggestedtime(&$suggestedtime)
	{
		$suggestedtime->eventId = $this->eventId;
		$found = false;
		foreach($this->_suggestedtimeList as $suggestedtime2)
		{
			if ($suggestedtime->suggestedtimeId > 0 && $suggestedtime->suggestedtimeId == $suggestedtime2->suggestedtimeId)
			{
				$found = true;
				break;
			}
		}
		if (!$found)
		{
			$this->_suggestedtimeList[] = $suggestedtime;
		}
	}
}
?>