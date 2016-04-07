<?php
/**
 * Returns JSON document of apptTypes available on each day of given month.
 * This data will be used to help the user pick a date that satisfies most
 * or all of appt filter criteria they've selected.
 *
 * @param d = Date string indicating a day within the month inquired.
 *            !IMPORTANT: Y-m-j format, no leading 0's
 */

//begin and (if needed) update session
session_start();
require_once './updateSession.php';
require_once '../models/Database.php';



//if date (?d) is provided and is valid
if ( isset( $_GET['d'] ) && validateDate( $_GET['d'] ) ) {
	//get timeframe of month
	$month     = date( 'n', strtotime( $_GET['d'] ) );
	$year      = date( 'Y', strtotime( $_GET['d'] ) );
	$timeStart = $year.'-'.$month.'-01 00:00:00';
	$timeEnd   = $year.'-'.
	             ( ($month + 1 <= 12) ? $month + 1 : 1 ).
	             '-01 00:00:00';

	//connect to database
	$conn = Database::Connect();

	//write string to query for appts within timeframe
	$qry = "SELECT `TimeStart`, `ApptTypeID`, `CurriculumID`
	        FROM `ScheduledAppointment`
	        WHERE (`TimeStart` >= :timeStart
	          AND `TimeEnd` <= :timeEnd)";

	//append query params to $qry string
	if ( isset( $_SESSION['apptTypeID'] ) ) {
		$qry .= " AND (";
		for ($i = 0; $i < count($_SESSION['apptTypeID']); $i++) {
			$qry .= "`ApptTypeID` = :apptTypeID".$i;
			if (array_key_exists($i+1, $_SESSION['apptTypeID']))
				$qry .= " OR ";
		}
		$qry .= ")";
	}

	//prepare SQL statement
	$stmt = $conn->prepare($qry);

	//bind time params
	$stmt->bindParam(':timeStart', $timeStart, PDO::PARAM_STR);
	$stmt->bindParam(':timeEnd', $timeEnd, PDO::PARAM_STR);

	//bind apptTypeID params
	if ( isset( $_SESSION['apptTypeID'] ) ) {
		for ($i = 0; $i < count($_SESSION['apptTypeID']); $i++) {
			$stmt->bindParam(':apptTypeID'.$i, $_SESSION['apptTypeID'][$i], PDO::PARAM_INT);
		}
	}

	//execute and read results
	$stmt->execute();
	$monthOfAppts = []; //will hold results of query
	while ( $r = $stmt->fetch() ) {

		// var_dump($r);

		//take time away from $r['TimeStart'] (leaving only date)
		$r['TimeStart'] = date( 'Y-m-d', strtotime( $r['TimeStart'] ) );

		if ( $r['ApptTypeID'] != '2' || checkForCurrID($r['CurriculumID']) ) {

			//if this is a new day, add it to $monthOfAppts
			if ( !array_key_exists($r['TimeStart'], $monthOfAppts) ) {
				$monthOfAppts[$r['TimeStart']] = [$r['ApptTypeID']];
			}

			else {
				array_push($monthOfAppts[$r['TimeStart']], $r['ApptTypeID']);
			}
		}
	}

	//erase duplicated ApptTypeIDs and resent keys in each day's array
	foreach ($monthOfAppts as &$dayArr) {
		$dayArr = array_unique($dayArr);
		$dayArr = array_values($dayArr);
	}

	// var_dump($monthOfAppts);
	// var_dump($_SESSION);
	echo json_encode($monthOfAppts);
}

else {
	//something has failed
	// echo ( isset( $_GET['d'] ) )?'true':'false';
	// echo json_encode($_SESSION);
}



//verify validity of a date string
function validateDate($date)
{
  $d = DateTime::createFromFormat('Y-n-j', $date);
  return $d && $d->format('Y-n-j') == $date;
}

//verify that this department tour fits user-selected filters (in $_SESSION)
function checkForCurrID ( $id ) {
	foreach ( $_SESSION['curriculumID'] as $idChk ) {
		if ( $id == $idChk )
			return true;
	}
	return false;
}

?>