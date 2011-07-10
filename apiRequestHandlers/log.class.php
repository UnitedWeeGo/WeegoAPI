<?php

header ("Content-Type:text/xml"); 

require_once "../configuration.php";
require_once '../util/request.base.php';
require_once '../util/class.error.php';
require_once '../util/class.success.php';

class LogClass extends ReqBase
{
	public $dataObj = null;	
	
	function LogClass()
	{
		parent::__construct();
	}
	
	function LogClassGo()
	{
		$this->dataObj = $this->genDataObj();
		
		$logFilePath = $GLOBALS['configuration']['log_location_file'];
		
		$clearLog = false;
		if ( isset($this->dataObj['clearLog']) )
		{
			$clearLog = $this->dataObj['clearLog'] == 'true';
		}
		
		if ($clearLog)
		{
			// clear content of file
			file_put_contents($logFilePath, '<Log Created ' . date('D, d M Y H:i:s') . '>' . PHP_EOL);
			$s = new SuccessResponse();
			echo $s->genSuccess(SuccessResponse::LogClearSuccess);
			die();
		}
		if ( isset($this->dataObj['logMessage']) )
		{
			$logMessage = $this->dataObj['logMessage'];
			if (count($logMessage) > 0)
			{
				// prepend the line to the log
				$current = file_get_contents($logFilePath);
				$new = $logMessage . PHP_EOL . $current;
				file_put_contents($logFilePath, $new, LOCK_EX);
			}
		}
		$s = new SuccessResponse();
		echo $s->genSuccess(SuccessResponse::LogWriteSuccess);
		die();
	}
}

?>