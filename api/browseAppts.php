<?php session_start();

//Bring url params to session scope
	require_once 'updateSession.php';

//Bring session variables to variable scope
	$date = date( 'Y-m-d', $_SESSION['date'] ).' 00:00:00';
	$dateEnd = date( 'Y-m-d', $_SESSION['date'] ).' 23:59:59'; //datetime at the end of $date
	$apptTypeID = $_SESSION['apptTypeID'];
	$curriculumID = isset($_SESSION['curriculumID']) ? $_SESSION['curriculumID'] : array();
	$currList = array(); //Curriculum objects for each selected 
	$deptList = array(); //List of deptIDs that house the curriculum

//==== Get curriculum object and departmentIDs (patched from version 0.2)
	require_once '../models/Curriculum.php';
	foreach ($curriculumID as $curr) {

		//get each curriculum object and push to $currList
		$tmp = Curriculum::GetByCurriculumID($curr);
		$tmp->GetDepartmentDetails();
		array_push( $currList, $tmp );

		//push relevant deptIDs to $deptList
		if ( !in_array( $tmp->Department->departmentID, $deptList ) )
			array_push( $deptList, $tmp->Department->departmentID );
	}
//===


	//response array to hold appt results
	$res = array();

	require_once '../models/AppointmentType.php';
	require_once '../models/ScheduledAppointment.php';
	
	//make apptTypeID an array if it is not already
	( !is_array( $apptTypeID ) ) ? $apptTypeID = array( $apptTypeID ) : null;

	//For each apptType
	foreach ( $apptTypeID as $typeID ) {

		//For all apptTypes other than 3 (department tour)
		if ($typeID != '2') {

			if ( $arr = ScheduledAppointment::ListByApptTypeID( $typeID, null, $date ) ) {

				//Change datetime strings into strictly time
				foreach ( $arr as &$r ) {
					$r->timeStart = date('g:i a',strtotime($r->timeStart));
					$r->timeEnd = date('g:i a',strtotime($r->timeEnd));
				}

				$apptList = $arr;
			}
			else {
				$apptList = array();
			}

			//Create a group of appts (by apptType) to append to result
			$apptGroup = array(
				"apptType" => AppointmentType::GetByApptTypeID( $typeID ),
				"apptList" => $apptList
			);

			if ( $apptGroup['apptList'] ) {
				array_push( $res, $apptGroup );
			}
		}

		//For apptType == '3' (department tour)..
		else {
			require_once '../models/Department.php';

			//For each department..
			foreach ( $deptList as $deptID ) {
	
				//Get data on current department
				$department = Department::GetByDepartmentID( $deptID );

				//List to hold appts
				$apptList = array();

				//For each curriculum that matches this department
				foreach ( $curriculumID as $currID ) {
					$curriculum = Curriculum::GetByCurriculumID( $currID );
					$curriculum->getDepartmentDetails();
					if ( $curriculum->Department->departmentID == $department->departmentID ) {

						//If tours are available, add them to the list.
						if ( $arr = ScheduledAppointment::ListByApptTypeID( 2, $currID, $date ) ) {

							//Add curriculum information to each appt and change datetime strings to time
							foreach ( $arr as &$r ) {
						 		$r->title = $curriculum->title;
								$r->timeStart = date('g:i a',strtotime($r->timeStart));
								$r->timeEnd = date('g:i a',strtotime($r->timeEnd));
							}

							$apptList = array_merge( $apptList, $arr );
						}
					}
				}

				//Get appointment type and alter its description to match that of the department
				$apptType = AppointmentType::GetByApptTypeID( $typeID );
				$apptType->description = $department->description;

				//Create a new appointment group
				$apptGroup = array(
					"apptType" => $apptType,
					"apptList" => $apptList
				);

				//Append department title to appointment type title
				$apptGroup['apptType']->title .= " - ".$department->title;


				if ( $apptGroup['apptList'] ) {
					array_push( $res, $apptGroup );
				}
			}
		}
	}
	
	//Return json encoded result array
	echo json_encode( $res );
?>