<?php

	/**
	 * TEST DAYS: Nov 1 - Nov 5
	 */

/**
 * This page will be used during the development process
 * to create mock appointments for debugging.
 */

include 'models/Database.php';


function addAppt ($schedApptID, $apptType, $timeStart, $timeEnd, $currID = '') {
	$conn = Database::Connect();
	$stmt = $conn->prepare('INSERT INTO `ScheduledAppointment`
	                        (`SchedApptID`, `ApptType`, `TimeStart`, `TimeEnd`, `CurriculumID`, `IsPrivate`)
	                        VALUES
	                        (:schedApptID, :apptType, :timeStart, :timeEnd, :currID, 0)');
	$stmt->bindParam(':schedApptID', $schedApptID, PDO::PARAM_STR);
	$stmt->bindParam(':apptType', $apptType, PDO::PARAM_INT);
	$stmt->bindParam(':timeStart', $timeStart, PDO::PARAM_STR);
	$stmt->bindParam(':timeEnd', $timeEnd, PDO::PARAM_STR);
	$stmt->bindParam(':currID', $currID, PDO::PARAM_STR);
	$stmt->execute();
}

$apptID = substr( md5( rand() ), 0, 10 );
$type   = 2;
$strt   = '2015-11-05 18:00:00';
$end    = '2015-11-05 19:00:00';

addAppt($apptID, $type, $strt, $end);

?>