<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `eventparticipantmap` (
	`eventid` int(11) NOT NULL,
	`participantid` int(11) NOT NULL,INDEX(`eventid`, `participantid`)) ENGINE=MyISAM;
*/

/**
* <b>EventParticipantMap</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @copyright Free for personal & commercial use. (Offered under the BSD license)
*/
class EventParticipantMap
{
	public $eventId = '';

	public $participantId = '';

	public $pog_attribute_type = array(
		"eventId" => array('db_attributes' => array("NUMERIC", "INT")),
		"participantId" => array('db_attributes' => array("NUMERIC", "INT")));
		public $pog_query;
	
	
	/**
	* Creates a mapping between the two objects
	* @param Event $object 
	* @param Participant $otherObject 
	* @return 
	*/
	function AddMapping($object, $otherObject)
	{
		if ($object instanceof Event && $object->eventId != '')
		{
			$this->eventId = $object->eventId;
			$this->participantId = $otherObject->participantId;
			return $this->Save();
		}
		else if ($object instanceof Participant && $object->participantId != '')
		{
			$this->participantId = $object->participantId;
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
			$this->pog_query = "delete from `eventparticipantmap` where `eventid` = '".$object->eventId."'";
			if ($otherObject != null && $otherObject instanceof Participant)
			{
				$this->pog_query .= " and `participantid` = '".$otherObject->participantId."'";
			}
		}
		else if ($object instanceof Participant)
		{
			$this->pog_query = "delete from `eventparticipantmap` where `participantid` = '".$object->participantId."'";
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
		$this->pog_query = "select `eventid` from `eventparticipantmap` where `eventid`='".$this->eventId."' AND `participantid`='".$this->participantId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows == 0)
		{
			$this->pog_query = "insert into `eventparticipantmap` (`eventid`, `participantid`) values ('".$this->eventId."', '".$this->participantId."')";
		}
		return Database::InsertOrUpdate($this->pog_query, $connection);
	}
}
?>