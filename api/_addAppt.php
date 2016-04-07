<?php
//call to create mock appointments during development process

var_dump($_POST);

$params = [];


//SET PARAMS
	//ApptTypeID
	if ( isset( $_POST['type'] ) )
		$params['ApptTypeID'] = $_POST['type'];

	//TimeStart && TimeEnd
	if ( isset( $_POST['date'] ) ) {
		$date = date( 'Y-m-d', strtotime( $_POST['date'] ) );
		if ( isset( $_POST['time-start']) )
			$params['TimeStart'] = $date.' '.date( 'H:i:s', strtotime( $_POST['time-start'] ) );
		if ( isset( $_POST['time-end']) )
			$params['TimeEnd'] = $date.' '.date( 'H:i:s', strtotime( $_POST['time-end'] ) );
	}
	

	//CurriculumID
	if ( isset( $_POST['curr'] ) )
		$params['CurriculumID'] = $_POST['curr'];

	//Building
	if ( isset( $_POST['building'] ) ) {
		$bldg = explode('_', $_POST['building']);
		(isset($bldg[0])) ? $params['Building'] = $bldg[0] : null;
		(isset($bldg[1])) ? $params['Room'] = $bldg[1] : null;
	}

	var_dump($params);
//END PARAMS


//Create new ScheduledAppointment
require_once '../models/ScheduledAppointment.php';

//new
$schedAppt = ScheduledAppointment::NewScheduledAppointment( $params );

//push to db
$schedAppt->PushToDB();
//get from db
$schedAppt = ScheduledAppointment::GetBySchedApptID( $schedAppt->schedApptID );
//confirm
var_dump($schedAppt);

?>

<a href="../_apptGen.php"><button>&lt Create another</button></a>