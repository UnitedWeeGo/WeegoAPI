<?php
class GetListFromSQL
{
	var $sourceObject;
	var $argv;
	var $version = '0.1';

	function Version()
	{
		return $this->version;
	}

	function GetListFromSQL($sourceObject, $argv)
	{
		$this->sourceObject = $sourceObject;
		$this->argv = $argv;
	}

	function Execute()
	{
		$objectName = get_class($this->sourceObject);
		return $this->FetchObjects($this->sql, get_class($this->sourceObject));
	}

	function SetupRender()
	{
		return null;
	}

	function AuthorPage()
	{
		return null;
	}
}
/*
Example:
$obj = new SomeObject();
$sql = "select p.* from property p, antohertable at with a really complex where clause";
$objList = $obj->GetListFromSQL($sql);
*/