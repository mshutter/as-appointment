<?php session_start();

//Bring url params into variable scope and SESSION scope.
//SESSION scope values will persist if not replaced by REQUEST scope values
	//date
	if ( isset( $_GET['da'] ) )
		$_SESSION['date'] = strtotime( $_GET['da'] );
	$date = date( 'Y-m-d', $_SESSION['date'] ).' 00:00:00';

	//dateEnd (in this case, datetime at the end of $date)
	$dateEnd = date( 'Y-m-d', $_SESSION['date'] ).' 23:59:59';

	//apptTypeID
	if ( isset( $_GET['ty'] ) )
		$_SESSION['apptTypeID'] = $_GET['ty'];
	$apptTypeID = $_SESSION['apptTypeID'];

	//departmentID
	if ( isset( $_GET['de'] ) )
		$_SESSION['departmentID'] = $_GET['de'];
	$departmentID = $_SESSION['departmentID'];

	//curriculumID
	if ( isset( $_GET['cu'] ) )
		$_SESSION['curriculumID'] = $_GET['cu'];
	$curriculumID = $_SESSION['curriculumID'];


/**
 * Retrieve Data
 */
	

	//response array to hold appt results
	$res = [];

	require_once '../models/AppointmentType.php';
	require_once '../models/ScheduledAppointment.php';


	//For each apptType
	foreach ( $apptTypeID as $typeID ) {

		//For all apptTypes other than 3 (department tour)
		if ($typeID != '3') {

			if ( $arr = ScheduledAppointment::ListByApptTypeID( $typeID, null, $date ) ) {

				//Change datetime strings into strictly time
				foreach ( $arr as &$r ) {
					$r->timeStart = date('g:i a',strtotime($r->timeStart));
					$r->timeEnd = date('g:i a',strtotime($r->timeEnd));
				}

				$apptList = $arr;
			}
			else {
				$apptList = [];
			}

			//Create a group of appts (by apptType) to append to result
			$apptGroup = [
				"apptType" => AppointmentType::GetByApptTypeID( $typeID ),
				"apptList" => $apptList
			];
		}

		//For apptType == '3' (department tour)..
		else {
			require_once '../models/Department.php';
			require_once '../models/Curriculum.php';

			//For each department..
			foreach ( $departmentID as $deptID ) {
	
				//Get data on current department
				$department = Department::GetByDepartmentID( $deptID );

				//List to hold appts
				$apptList = [];

				//For each curriculum that matches this department
				foreach ( $curriculumID as $currID ) {
					$curriculum = Curriculum::GetByCurriculumID( $currID );
					$curriculum->getDepartmentDetails();
					if ( $curriculum->Department->departmentID == $department->departmentID ) {

						//If tours are available, add them to the list.
						if ( $arr = ScheduledAppointment::ListByApptTypeID( 3, $currID, $date ) ) {

							//Add curriculum information to each appt and change datetime strings to time
							foreach ( $arr as &$r ) {
						 		$r->title = $curriculum->title;
								$r->description = $curriculum->description;
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
				$apptGroup = [
					"apptType" => $apptType,
					"apptList" => $apptList
				];

				//Append department title to appointment type title
				$apptGroup['apptType']->title .= " - ".$department->title;
			}
		}

		//If apptGroup contains appts for this day, add it to the results array  
		if ( $apptGroup['apptList'] ) {
			array_push( $res, $apptGroup );
		}
	}

	//Return json encoded result array
	echo json_encode( $res );

?>