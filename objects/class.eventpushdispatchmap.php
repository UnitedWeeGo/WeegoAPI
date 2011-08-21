<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `eventpushdispatchmap` (
	`eventid` int(11) NOT NULL,
	`pushdispatchid` int(11) NOT NULL,INDEX(`eventid`, `pushdispatchid`)) ENGINE=MyISAM;
*/

/**
* <b>EventPushDispatchMap</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @copyright Free for personal & commercial use. (Offered under the BSD license)
*/
class EventPushDispatchMap
{
	public $eventId = '';

	public $pushdispatchId = '';

	public $pog_attribute_type = array(
		"eventId" => array('db_attributes' => array("NUMERIC", "INT")),
		"pushdispatchId" => array('db_attributes' => array("NUMERIC", "INT")));
		public $pog_query;
	
	
	/**
	* Creates a mapping between the two objects
	* @param Event $object 
	* @param PushDispatch $otherObject 
	* @return 
	*/
	function AddMapping($object, $otherObject)
	{
		if ($object instanceof Event && $object->eventId != '')
		{
			$this->eventId = $object->eventId;
			$this->pushdispatchId = $otherObject->pushdispatchId;
			return $this->Save();
		}
		else if ($object instanceof PushDispatch && $object->pushdispatchId != '')
		{
			$this->pushdispatchId = $object->pushdispatchId;
			$this->eventId = $otherObject->eventId;
			return $this->Save();
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	* Removes the mapping between the two objects
	* @param Object $object 
	* @param Object $object2 
	* @return 
	*/
	function RemoveMapping($object, $otherObject = null)
	{
		$connection = Database::Connect();
		if ($object instanceof Event)
		{
			$this->pog_query = "delete from `eventpushdispatchmap` where `eventid` = '".$object->eventId."'";
			if ($otherObject != null && $otherObject instanceof PushDispatch)
			{
				$this->pog_query .= " and `pushdispatchid` = '".$otherObject->pushdispatchId."'";
			}
		}
		else if ($object instanceof PushDispatch)
		{
			$this->pog_query = "delete from `eventpushdispatchmap` where `pushdispatchid` = '".$object->pushdispatchId."'";
			if ($otherObject != null && $otherObject instanceof Event)
			{
				$this->pog_query .= " and `eventid` = '".$otherObject->eventId."'";
			}
		}
		Database::NonQuery($this->pog_query, $connection);
	}
	
	
	/**
	* Physically saves the mapping to the database
	* @return 
	*/
	function Save()
	{
		$connection = Database::Connect();
		$this->pog_query = "select `eventid` from `eventpushdispatchmap` where `eventid`='".$this->eventId."' AND `pushdispatchid`='".$this->pushdispatchId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows == 0)
		{
			$this->pog_query = "insert into `eventpushdispatchmap` (`eventid`, `pushdispatchid`) values ('".$this->eventId."', '".$this->pushdispatchId."')";
		}
		return Database::InsertOrUpdate($this->pog_query, $connection);
	}
}
?>