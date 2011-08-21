<?php
/*
	This SQL query will create the table to store your object.

	CREATE TABLE `reportlocation` (
	`reportlocationid` int(11) NOT NULL auto_increment,
	`email` VARCHAR(255) NOT NULL,
	`latitude` VARCHAR(255) NOT NULL,
	`longitude` VARCHAR(255) NOT NULL,
	`timestamp` TIMESTAMP NOT NULL, PRIMARY KEY  (`reportlocationid`)) ENGINE=MyISAM;
*/

/**
* <b>ReportLocation</b> class with integrated CRUD methods.
* @author Php Object Generator
* @version POG 3.0d / PHP5.1 MYSQL
* @see http://www.phpobjectgenerator.com/plog/tutorials/45/pdo-mysql
* @copyright Free for personal & commercial use. (Offered under the BSD license)
* @link http://pog.weegoapp.com/?language=php5.1&wrapper=pdo&pdoDriver=mysql&objectName=ReportLocation&attributeList=array+%28%0A++0+%3D%3E+%27email%27%2C%0A++1+%3D%3E+%27latitude%27%2C%0A++2+%3D%3E+%27longitude%27%2C%0A++3+%3D%3E+%27timestamp%27%2C%0A%29&typeList=array%2B%2528%250A%2B%2B0%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B1%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B2%2B%253D%253E%2B%2527VARCHAR%2528255%2529%2527%252C%250A%2B%2B3%2B%253D%253E%2B%2527TIMESTAMP%2527%252C%250A%2529
*/
include_once('class.pog_base.php');
class ReportLocation extends POG_Base
{
	public $reportlocationId = '';

	/**
	 * @var VARCHAR(255)
	 */
	public $email;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $latitude;
	
	/**
	 * @var VARCHAR(255)
	 */
	public $longitude;
	
	/**
	 * @var TIMESTAMP
	 */
	public $timestamp;
	
	public $pog_attribute_type = array(
		"reportlocationId" => array('db_attributes' => array("NUMERIC", "INT")),
		"email" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"latitude" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"longitude" => array('db_attributes' => array("TEXT", "VARCHAR", "255")),
		"timestamp" => array('db_attributes' => array("NUMERIC", "TIMESTAMP")),
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
	
	function ReportLocation($email='', $latitude='', $longitude='', $timestamp='')
	{
		$this->email = $email;
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->timestamp = $timestamp;
	}
	
	
	/**
	* Gets object from database
	* @param integer $reportlocationId 
	* @return object $ReportLocation
	*/
	function Get($reportlocationId)
	{
		$connection = Database::Connect();
		$this->pog_query = "select * from `reportlocation` where `reportlocationid`=:reportlocationId LIMIT 1";
		$this->pog_bind = array(
			':reportlocationId' => intval($reportlocationId)
		);
		$cursor = Database::ReaderPrepared($this->pog_query, $this->pog_bind);
		while ($row = Database::Read($cursor))
		{
			$this->reportlocationId = $row['reportlocationid'];
			$this->email = $this->Decode($row['email']);
			$this->latitude = $this->Decode($row['latitude']);
			$this->longitude = $this->Decode($row['longitude']);
			$this->timestamp = $row['timestamp'];
		}
		return $this;
	}
	
	
	/**
	* Returns a sorted array of objects that match given conditions
	* @param multidimensional array {("field", "comparator", "value"), ("field", "comparator", "value"), ...} 
	* @param string $sortBy 
	* @param boolean $ascending 
	* @param int limit 
	* @return array $reportlocationList
	*/
	function GetList($fcv_array = array(), $sortBy='', $ascending=true, $limit='')
	{
		$connection = Database::Connect();
		$sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
		$this->pog_query = "select * from `reportlocation` ";
		$reportlocationList = Array();
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
			$sortBy = "reportlocationid";
		}
		$this->pog_query .= " order by ".$sortBy." ".($ascending ? "asc" : "desc")." $sqlLimit";
		$thisObjectName = get_class($this);
		$cursor = Database::Reader($this->pog_query);
		while ($row = Database::Read($cursor))
		{
			$reportlocation = new $thisObjectName();
			$reportlocation->reportlocationId = $row['reportlocationid'];
			$reportlocation->email = $this->Unescape($row['email']);
			$reportlocation->latitude = $this->Unescape($row['latitude']);
			$reportlocation->longitude = $this->Unescape($row['longitude']);
			$reportlocation->timestamp = $row['timestamp'];
			$reportlocationList[] = $reportlocation;
		}
		return $reportlocationList;
	}
	
	
	/**
	* Saves the object to the database
	* @return integer $reportlocationId
	*/
	function Save()
	{
		$connection = Database::Connect();
		$rows = 0;
		if (!empty($this->reportlocationId))
		{
			$this->pog_query = "select `reportlocationid` from `reportlocation` where `reportlocationid`=".$this->Quote($this->reportlocationId)." LIMIT 1";
			$rows = Database::Query($this->pog_query);
		}
		if ($rows > 0)
		{
			$this->pog_query = "update `reportlocation` set 
			`email`=:email,
			`latitude`=:latitude,
			`longitude`=:longitude,
			`timestamp`=:timestamp where `reportlocationid`=:reportlocationId";
		}
		else
		{
			$this->reportlocationId = "";
			$this->pog_query = "insert into `reportlocation` (`email`,`latitude`,`longitude`,`timestamp`,`reportlocationid`) values (
			:email,
			:latitude,
			:longitude,
			:timestamp,
			:reportlocationId)";
		}
		$this->pog_bind = array(
			':email' => $this->Encode($this->email),
			':latitude' => $this->Encode($this->latitude),
			':longitude' => $this->Encode($this->longitude),
			':timestamp' => $this->timestamp,
			':reportlocationId' => intval($this->reportlocationId)
		);
		$insertId = Database::InsertOrUpdatePrepared($this->pog_query, $this->pog_bind);
		if ($this->reportlocationId == "")
		{
			$this->reportlocationId = $insertId;
		}
		return $this->reportlocationId;
	}
	
	
	/**
	* Clones the object and saves it to the database
	* @return integer $reportlocationId
	*/
	function SaveNew()
	{
		$this->reportlocationId = '';
		return $this->Save();
	}
	
	
	/**
	* Deletes the object from the database
	* @return boolean
	*/
	function Delete()
	{
		$connection = Database::Connect();
		$this->pog_query = "delete from `reportlocation` where `reportlocationid`=".$this->Quote($this->reportlocationId);
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
			$this->pog_query = "delete from `reportlocation` where ";
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
}
?>