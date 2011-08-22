<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `altemail` (
	`altemailid` int(11) NOT NULL auto_increment,
	`email` VARCHAR(255) NOT NULL,
	`participantid` int(11) NOT NULL, INDEX(`participantid`), PRIMARY KEY  (`altemailid`)) ENGINE=MyISAM;
*/

/**
* <b>AltEmail</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=AltEmail&attributeList=array+%28%0A++0+%3D%3E+%27email%27%2C%0A++1+%3D%3E+%27Participant%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527BELONGSTO%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
class AltEmail extends POG_Base
{
	public $altemailId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $email;
	
	/**
	 * @var INT(11)
	 */
	public $participantId;
	
	public $pog_attribute_type = array(
		"altemailId" => array('db_attributes' => array("NUMERIC", "INT")),
		"email" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"Participant" => array('db_attributes' => array("OBJECT", "BELONGSTO")),
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
	
	function AltEmail($email='')
	{
		$this->email = $email;
	}
	
	
	/**
	* Gets object from database
	* @param integer $altemailId 
	* @return object $AltEmail
	*/
	function Get($altemailId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `altemail` where `altemailid`=:altemailId LIMIT 1";
		$this->pog_bind = array(
			':altemailId' => intval($altemailId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind, $connection);
		while ($row = Database::Read($cursor))
		{
			$this->altemailId = $row['altemailid'];
			$this->email = $this->Decode($row['email']);
			$this->participantId = $row['participantid'];
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $altemailList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `altemail` ";
		$altemailList = Array();
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
			$sortBy = "altemailid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query, $connection);
		while ($row = Database::Read($cursor))
		{
			$altemail = new $thisObjectName();
			$altemail->altemailId = $row['altemailid'];
			$altemail->email = $this->Unescape($row['email']);
			$altemail->participantId = $row['participantid'];
			$altemailList[] = $altemail;
		}
		return $altemailList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $altemailId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->altemailId))
		{
			$this->pog_query = "select `altemailid` from `altemail` where `altemailid`=".$this->Quote($this->altemailId, $connection)." LIMIT 1";
			$rows = Database::Query($this->pog_query, $connection);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `altemail` set 
			`email`=:email,
			`participantid`=:participantId where `altemailid`=:altemailId";
		}
		else
		{
			$this->altemailId = "";
			$this->pog_query = "insert into `altemail` (`email`,`participantid`,`altemailid`) values (
			:email,
			:participantId,
			:altemailId)";
		}
		$this->pog_bind = array(
			':email' => $this->Encode($this->email),
			':participantId' => intval($this->participantId),
			':altemailId' => intval($this->altemailId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind, $connection);
		if ($this->altemailId == "")
		{
			$this->altemailId = $insertId;
		}
		return $this->altemailId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $altemailId
	*/
	function SaveNew()
	{
		$this->altemailId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `altemail` where `altemailid`=".$this->Quote($this->altemailId, $connection);
		return Database::NonQuery($this->pog_query, $connection);
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
			$this->pog_query = "delete from `altemail` where ";
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
	
	
	/**
	* Associates the Participant object to this one
	* @return boolean
	*/
	function GetParticipant()
	{
		$participant = new Participant();
		return $participant->Get($this->participantId);
	}
	
	
	/**
	* Associates the Participant object to this one
	* @return 
	*/
	function SetParticipant(&$participant)
	{
		$this->participantId = $participant->participantId;
	}
}
?>