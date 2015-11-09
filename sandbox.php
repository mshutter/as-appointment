<?php

require_once 'models/ScheduledAppointment.php';

$appt = ScheduledAppointment::ListByApptType(2);
var_dump($appt);

/**
 * GENERATE UID FUNCTION
 */

function UniqueID () {
	//String containing all characters that may be used in unique ID
	$chars = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

	//First character must be alphabetical
	$uid = substr( $chars, mt_rand(0,51), 1 );

	//Shuffle numbers into $chars
	$chars = str_shuffle($chars . '0123456789');

	//Add 9 random alphanumeric characters to $uid
	for ($i = 0; $i < 9; $i++) {
		$x = mt_rand(0,61);
		$uid .= substr( $chars, $x, 1 );
	}

	return $uid;
}

?>