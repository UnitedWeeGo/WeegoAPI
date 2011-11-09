<?php

require_once 'TimeZoneUtil.php';

date_default_timezone_set('GMT');


function getFormattedTime($eventDate, $eventTimeZone=null)
{
	$isRFC822Timezone = intval($eventTimeZone) != 0;
	if ($isRFC822Timezone) // new style, RFC 822 Timezone
	{
		// for php to properly parse the timezone, a + must be added to positive values
		$char0 = substr($eventTimeZone, 0, 1);
		if ($char0 != '+' && $char0 != '-') $eventTimeZone = '+' . $eventTimeZone;
		// now apply the timezone offset to the event date
		$eventTimeGMT = new DateTime($eventDate . ' GMT');
		$eventTimeTZ = new DateTime($eventDate . ' ' . $eventTimeZone);

		$ts1 = $eventTimeGMT->getTimestamp();
		$ts2 = $eventTimeTZ->getTimestamp();
			
		$diff1 = $ts1 - $ts2;
		$d2 = $ts1 + $diff1;
		
		$isDaylightSavings = $eventTimeTZ->format('I');
		$tz = TimeZoneUtil::getPHPTimeZoneNameForOffset($eventTimeZone, $isDaylightSavings);
		$formattedDate = date('D, M j g:i A', $d2) . ' ' . (($tz) ? $tz : $eventTimeZone);
		
		return $formattedDate;
	}
	else
	{
		$tz = TimeZoneUtil::getPHPTimeZoneStampForAbbreviation($eventTimeZone);
		$eventTime = new DateTime($eventDate);
		if ($tz) $eventTime->setTimezone(new DateTimeZone($tz));
		$dateStr = $eventTime->format('D, M j g:i A') . ' ' . (($tz) ? $eventTimeZone : 'GMT');
		return $dateStr;
	}
}

echo getFormattedTime('2011-11-05 01:30:00', '-0800') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', '-0700') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', '-0400') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', '+0800') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', '0800') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', 'PDT') . PHP_EOL;
echo getFormattedTime('2011-11-05 01:30:00', 'EDT') . PHP_EOL;

/*
$eventTime1 = new DateTime('2011-11-05 01:30:00 GMT');
$eventTime2 = new DateTime('2011-11-05 01:30:00 -0700');
$eventTime3 = new DateTime('2011-11-05 01:30:00 +0800');


$ts1 = $eventTime1->getTimestamp();
$ts2 = $eventTime2->getTimestamp();
$ts3 = $eventTime3->getTimestamp();

echo $ts1 . PHP_EOL;
echo $ts2 . PHP_EOL;
echo $ts3 . PHP_EOL . PHP_EOL;

$diff1 = $ts1 - $ts2;
$diff2 = $ts1 - $ts3;

$d2 = $ts1 + $diff1;
$d3 = $ts1 + $diff2;

$date1 = date('Y-m-d H:i:s', $ts1);
$date2 = date('Y-m-d H:i:s', $d2);
$date3 = date('Y-m-d H:i:s', $d3);

echo $date1 . PHP_EOL;
echo $date2 . PHP_EOL;
echo $date3 . PHP_EOL . PHP_EOL;

echo '-0700 intval:' . intval('-0700') . PHP_EOL;
echo '0800 intval:' . intval('0800') . PHP_EOL;
echo 'PDT intval:' . intval('PDT') . PHP_EOL . PHP_EOL;
*/
?>