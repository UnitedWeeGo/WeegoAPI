<?php

ini_set('soap.wsdl_cache_ttl', 1);

class CacheClearer
{
	function clear()
	{
		$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'] : "http://".$_SERVER['SERVER_NAME'];
		echo $url;
	}
}

$clearer = new CacheClearer();
$clearer->clear();

?>