<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `feedmessagepushdispatchmap` (
	`feedmessageid` int(11) NOT NULL,
	`pushdispatchid` int(11) NOT NULL,INDEX(`feedmessageid`, `pushdispatchid`)) ENGINE=MyISAM;
*/

/**
* <b>FeedMessagePushDispatchMap</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @copyright Free for personal & commercial use. (Offered under the BSD license)
*/
class FeedMessagePushDispatchMap
{
	public $feedmessageId = '';

	public $pushdispatchId = '';

	public $pog_attribute_type = array(
		"feedmessageId" => array('db_attributes' => array("NUMERIC", "INT")),
		"pushdispatchId" => array('db_attributes' => array("NUMERIC", "INT")));
		public $pog_query;
	
	
	/**
	* Creates a mapping between the two objects
	* @param FeedMessage $object 
	* @param PushDispatch $otherObject 
	* @return 
	*/
	function AddMapping($object, $otherObject)
	{
		if ($object instanceof FeedMessage && $object->feedmessageId != '')
		{
			$this->feedmessageId = $object->feedmessageId;
			$this->pushdispatchId = $otherObject->pushdispatchId;
			return $this->Save();
		}
		else if ($object instanceof PushDispatch && $object->pushdispatchId != '')
		{
			$this->pushdispatchId = $object->pushdispatchId;
			$this->feedmessageId = $otherObject->feedmessageId;
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
		if ($object instanceof FeedMessage)
		{
			$this->pog_query = "delete from `feedmessagepushdispatchmap` where `feedmessageid` = '".$object->feedmessageId."'";
			if ($otherObject != null && $otherObject instanceof PushDispatch)
			{
				$this->pog_query .= " and `pushdispatchid` = '".$otherObject->pushdispatchId."'";
			}
		}
		else if ($object instanceof PushDispatch)
		{
			$this->pog_query = "delete from `feedmessagepushdispatchmap` where `pushdispatchid` = '".$object->pushdispatchId."'";
			if ($otherObject != null && $otherObject instanceof FeedMessage)
			{
				$this->pog_query .= " and `feedmessageid` = '".$otherObject->feedmessageId."'";
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
		$this->pog_query = "select `feedmessageid` from `feedmessagepushdispatchmap` where `feedmessageid`='".$this->feedmessageId."' AND `pushdispatchid`='".$this->pushdispatchId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows == 0)
		{
			$this->pog_query = "insert into `feedmessagepushdispatchmap` (`feedmessageid`, `pushdispatchid`) values ('".$this->feedmessageId."', '".$this->pushdispatchId."')";
		}
		return Database::InsertOrUpdate($this->pog_query, $connection);
	}
}
?>