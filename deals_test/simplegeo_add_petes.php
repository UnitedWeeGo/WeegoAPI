<?php

require_once "../configuration.php";
foreach(glob("../objects/*.php") as $class_filename) {
	require_once($class_filename);
}

set_include_path(
get_include_path() . PATH_SEPARATOR . '/Applications/MAMP/bin/php5/lib/php'
);

require_once 'Services/SimpleGeo.php';

$key = 'bcRryckAyj5YT3ZrSGraENdxqdLJRz9Q';
$secret = 'q2AMUDLyHpcPuaKkETchSqxQaPrY2fD9';

$client = new Services_SimpleGeo($key, $secret);

// Petes tavern id
//$existingPlaceHandle = 'SG_1J66yIxSElIpKWsHpMTYRP_37.779507_-122.390710@1303263314';

// Long bar id
$existingPlaceHandle = 'SG_4YZBUkiLgcObCceSGj02Gk_37.790616_-122.433905@1303263324';

// check to make sure the place is valid

try {
	$existingPlaceResult = $client->getFeature($existingPlaceHandle);
	echo 'place exists, updating feature with check deal' . PHP_EOL;
	print_r($existingPlaceResult);

} catch (Services_SimpleGeo_Exception $e) {
	echo "ERROR: " . $e->getMessage() . " (#" . $e->getCode() . ")";
}

$feature = array("properties"=> array("hasDeal" => "true"));

try {	
	$editingResult = $client->editPlace($existingPlaceHandle, $feature, true);
	echo 'place successfully updated' . PHP_EOL;
	print_r($editingResult);
	
} catch (Services_SimpleGeo_Exception $e) {
	echo "ERROR: " . $e->getMessage() . " (#" . $e->getCode() . ")";
}

echo 'adding deal to db' . PHP_EOL;

$dealObj = new Deal();
$existingDeals = $dealObj->GetList( array( array("featureId", "=", $existingPlaceHandle )));

if (count($existingDeals) > 0)
{
	echo 'deal already in db' . PHP_EOL;
}
else
{
	echo 'deal does not exist for feature, add to db' . PHP_EOL;
	
	$dealObj->featureId = $existingPlaceHandle;
	$dealObj->Save();
}

?>