<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `invitepushdispatchmap` (
	`inviteid` int(11) NOT NULL,
	`pushdispatchid` int(11) NOT NULL,INDEX(`inviteid`, `pushdispatchid`)) ENGINE=MyISAM;
*/

/**
* <b>InvitePushDispatchMap</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @copyright Free for personal & commercial use. (Offered under the BSD license)
*/
class InvitePushDispatchMap
{
	public $inviteId = '';

	public $pushdispatchId = '';

	public $pog_attribute_type = array(
		"inviteId" => array('db_attributes' => array("NUMERIC", "INT")),
		"pushdispatchId" => array('db_attributes' => array("NUMERIC", "INT")));
		public $pog_query;
	
	
	/**
	* Creates a mapping between the two objects
	* @param Invite $object 
	* @param PushDispatch $otherObject 
	* @return 
	*/
	function AddMapping($object, $otherObject)
	{
		if ($object instanceof Invite && $object->inviteId != '')
		{
			$this->inviteId = $object->inviteId;
			$this->pushdispatchId = $otherObject->pushdispatchId;
			return $this->Save();
		}
		else if ($object instanceof PushDispatch && $object->pushdispatchId != '')
		{
			$this->pushdispatchId = $object->pushdispatchId;
			$this->inviteId = $otherObject->inviteId;
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
		if ($object instanceof Invite)
		{
			$this->pog_query = "delete from `invitepushdispatchmap` where `inviteid` = '".$object->inviteId."'";
			if ($otherObject != null && $otherObject instanceof PushDispatch)
			{
				$this->pog_query .= " and `pushdispatchid` = '".$otherObject->pushdispatchId."'";
			}
		}
		else if ($object instanceof PushDispatch)
		{
			$this->pog_query = "delete from `invitepushdispatchmap` where `pushdispatchid` = '".$object->pushdispatchId."'";
			if ($otherObject != null && $otherObject instanceof Invite)
			{
				$this->pog_query .= " and `inviteid` = '".$otherObject->inviteId."'";
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
		$this->pog_query = "select `inviteid` from `invitepushdispatchmap` where `inviteid`='".$this->inviteId."' AND `pushdispatchid`='".$this->pushdispatchId."' LIMIT 1";
		$rows = Database::Query($this->pog_query, $connection);
		if ($rows == 0)
		{
			$this->pog_query = "insert into `invitepushdispatchmap` (`inviteid`, `pushdispatchid`) values ('".$this->inviteId."', '".$this->pushdispatchId."')";
		}
		return Database::InsertOrUpdate($this->pog_query, $connection);
	}
}
?>