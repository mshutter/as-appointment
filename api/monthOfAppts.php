<?php
/**
 * Returns JSON document of apptTypes available on each day of given month.
 * This data will be used to help the user pick a date that satisfies most
 * or all of appt filter criteria they've selected.
 *
 * @param d = Date string indicating a day within the month inquired.
              yyyy-mm-dd format
 */


if ( isset( $_GET['d'] ) && validateDate( $_GET['d'] ) ) {
	//get timeframe of month
	$month     = date( 'n', strtotime( $_GET['d'] ) );
	$year      = date( 'Y', strtotime( $_GET['d'] ) );
	$startTime = $year.'-'.$month.'-01 00:00:00';
	$endTime   = $year.'-'.
	             ( ($month + 1 <= 12) ? $month + 1 : 1 ).
	             '-01 00:00:00';
	echo "start time: ".$startTime;
	echo "end time: ".$endTime;


	/*
	
	LEFT OFF HERE

	*/
}
else {
	echo "failed";
}



//verify validity of a date string
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') == $date;
}

?>