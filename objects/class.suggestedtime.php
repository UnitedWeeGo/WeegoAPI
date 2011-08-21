<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `suggestedtime` (
	`suggestedtimeid` int(11) NOT NULL auto_increment,
	`timestamp` TIMESTAMP NOT NULL,
	`eventid` int(11) NOT NULL,
	`suggestedtime` TIMESTAMP NOT NULL,
	`email` VARCHAR(255) NOT NULL, INDEX(`eventid`), PRIMARY KEY  (`suggestedtimeid`)) ENGINE=MyISAM;
*/

/**
* <b>SuggestedTime</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=SuggestedTime&attributeList=array+%28%0A++0+%3D%3E+%27timestamp%27%2C%0A++1+%3D%3E+%27Event%27%2C%0A++2+%3D%3E+%27suggestedTime%27%2C%0A++3+%3D%3E+%27email%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
class SuggestedTime extends POG_Base
{
	public $suggestedtimeId = '';

	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	/**
	 * @var INT(11)
	 */
	public $eventId;
	
	/**
	 * @var TIMESTAMP
	 */
	public $suggestedTime;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $email;
	
	public $pog_attribute_type = array(
		"suggestedtimeId" => array('db_attributes' => array("NUMERIC", "INT")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"Event" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
		"suggestedTime" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
		"email" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
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
	
	function SuggestedTime($timestamp='', $suggestedTime='', $email='')
	{
		$this->timestamp = $timestamp;
		$this->suggestedTime = $suggestedTime;
		$this->email = $email;
	}
	
	
	/**
	* Gets object from database
	* @param integer $suggestedtimeId 
	* @return object $SuggestedTime
	*/
	function Get($suggestedtimeId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `suggestedtime` where `suggestedtimeid`=:suggestedtimeId LIMIT 1";
		$this->pog_bind = array(
			':suggestedtimeId' => intval($suggestedtimeId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind);
		while ($row = Database::Read($cursor))
		{
			$this->suggestedtimeId = $row['suggestedtimeid'];
			$this->timestamp = $row['timestamp'];
			$this->eventId = $row['eventid'];
			$this->suggestedTime = $row['suggestedtime'];
			$this->email = $this->Decode($row['email']);
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $suggestedtimeList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `suggestedtime` ";
		$suggestedtimeList = Array();
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
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : $this->Quote($fcv_array[$i][2]);
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2]);
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$value = POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2]);
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
			$sortBy = "suggestedtimeid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query);
		while ($row = Database::Read($cursor))
		{
			$suggestedtime = new $thisObjectName();
			$suggestedtime->suggestedtimeId = $row['suggestedtimeid'];
			$suggestedtime->timestamp = $row['timestamp'];
			$suggestedtime->eventId = $row['eventid'];
			$suggestedtime->suggestedTime = $row['suggestedtime'];
			$suggestedtime->email = $this->Unescape($row['email']);
			$suggestedtimeList[] = $suggestedtime;
		}
		return $suggestedtimeList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $suggestedtimeId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->suggestedtimeId))
		{
			$this->pog_query = "select `suggestedtimeid` from `suggestedtime` where `suggestedtimeid`=".$this->Quote($this->suggestedtimeId)." LIMIT 1";
			$rows = Database::Query($this->pog_query);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `suggestedtime` set 
			`timestamp`=:timestamp,
			`eventid`=:eventId,
			`suggestedtime`=:suggestedtime,
			`email`=:email where `suggestedtimeid`=:suggestedtimeId";
		}
		else
		{
			$this->suggestedtimeId = "";
			$this->pog_query = "insert into `suggestedtime` (`timestamp`,`eventid`,`suggestedtime`,`email`,`suggestedtimeid`) values (
			:timestamp,
			:eventId,
			:suggestedtime,
			:email,
			:suggestedtimeId)";
		}
		$this->pog_bind = array(
			':timestamp' => $this->timestamp,
			':eventId' => intval($this->eventId),
			':suggestedtime' => $this->suggestedTime,
			':email' => $this->Encode($this->email),
			':suggestedtimeId' => intval($this->suggestedtimeId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind);
		if ($this->suggestedtimeId == "")
		{
			$this->suggestedtimeId = $insertId;
		}
		return $this->suggestedtimeId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $suggestedtimeId
	*/
	function SaveNew()
	{
		$this->suggestedtimeId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `suggestedtime` where `suggestedtimeid`=".$this->Quote($this->suggestedtimeId);
		return Database::NonQuery($this->pog_query);
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
			$this->pog_query = "delete from `suggestedtime` where ";
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
							$value = POG_Base::IsColumn($fcv_array[$i][2]) ? "BASE64_DECODE(".$fcv_array[$i][2].")" : $this->Quote($fcv_array[$i][2]);
							$this->pog_query .= "BASE64_DECODE(`".$fcv_array[$i][0]."`) ".$fcv_array[$i][1]." ".$value;
						}
						else
						{
							$value =  POG_Base::IsColumn($fcv_array[$i][2]) ? $fcv_array[$i][2] : $this->Quote($fcv_array[$i][2]);
							$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$value;
						}
					}
					else
					{
						$this->pog_query .= "`".$fcv_array[$i][0]."` ".$fcv_array[$i][1]." ".$this->Quote($fcv_array[$i][2]);
					}
				}
			}
			return Database::NonQuery($this->pog_query);
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
}
?>