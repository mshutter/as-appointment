<?php session_start();

/**
 * pass uri variables to session and local scope
 */

if ( isset( $_GET['da'] ) )
	$_SESSION['datePref'] = strtotime( $_GET['da'] );
$datePref = date( 'Y-m-d', $_SESSION['datePref'] ).' 00:00:00';

//set end of day for query params
$dateEnd = date( 'Y-m-d', $_SESSION['datePref'] ).' 23:59:59';

if ( isset( $_GET['ty'] ) )
	$_SESSION['apptType'] = $_GET['ty'];
$apptType = $_SESSION['apptType'];

//if apptType is 3 (Department Tour)
	if ( isset( $_GET['de'] ) )
		$_SESSION['deptID'] = $_GET['de'];
	$deptID = $_SESSION['deptID'];

	if ( isset( $_GET['cu'] ) )
		$_SESSION['currID'] = $_GET['cu'];
	$currID = $_SESSION['currID'];


/*
if ( $debug = FALSE ) {
	echo 'datePref: '.$datePref.'<br />';
	echo 'apptType: '.$apptType.'<br />';
	echo 'deptID: '  .$deptID.'<br />';
	echo 'currID: '  .$currID.'<br />';
	echo 'content:<br />';
}
*/


/**
 * Retrieve Data
 */

require_once '../models/ScheduledAppointment.php';

//if apptType is department tour (3)
if ( $apptType == 3 )
	echo json_encode( ScheduledAppointment::ListByApptType( $apptType, $datePref, $dateEnd, $currID ) );

//if apptType is not department tour
else
	echo json_encode( ScheduledAppointment::ListByApptType( $apptType, $datePref ) );

?>