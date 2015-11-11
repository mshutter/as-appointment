<?php

/*
	=========================
	Model - ScheduledAppointment
	=========================


	Instance Variables
		$schedApptID (string)  - Unique ID (10 char alphanumeric string)
		$apptTypeID (int)      - Identifies the category of the appointment (e.g. 2 = "Campus Tour")
		$curriculumID (string) - Identifier to link curriculum to event
		                         * Will be required only if ApptType is "department tour"
		$timeStart (string)    - Datetime string representing the exact time the appt is scheduled for
		$timeEnd (string)      - Datetime string representing the exact time the appt is scheduled to end
		$isPrivate (bool)      - Boolean value indicating whether or not this appointment should be hidden
		                         from basic users.
		                         (e.g. an appt specific to a group of faculty members should be marked
		                         "IsPrivate = true" to remain hidden from student users browsing other appointments)
		$building (string)     - Abbreviation identifying the buiding where this appointment will meet
		$room (string)         - RoomNum of the room that will be the location of this appointment
		
		** $actionID (string)  - Not yet integrated, but will serve as a link between multiple appointments
		                         that were created as a result of the same administrative action. This will make
		                         it programatically possible to keep these events linked and allow altering of
		                         them in one process.
	

	Instance Methods
		->GetAppointmentTypeDetails()
			- Return AND append an AppointmentType object to this instance, which details this ScheduledAppointment's
			  appointment type

		->GetCurriculumDetails()
			- Return AND append a Curriculum object to this instance, which details this ScheduledAppointment's curriculum,
			  if it has one

		->GetStudentsAttending( [ $extendedInfo = false ] )
			- Return AND add to this instance a list of Student objects, one for each student expected to attend this appt
			- Will return just basic info (ID, name and email), unless $extendedInfo is set to true


	Static Methods
		::GetBySchedApptID( $schedApptID )
			- Returns a single ScheduledAppointment object matching the specified $schedApptID

		::ListByApptType( $apptType [, $curriculumID = null [, $date = null [, $endDate = null ] ] ] )
			- Passing only $apptType will return a complete list of all appointments with the type specified
			- If ApptType is "department tour", the function will check if a curriculum has been specified (2nd param).
			- Pass a date string (e.g. '2015-11-01') for $date param to get all appointments of the specified
			  type that match that date.
			- Pass an $endDate as well to see all appointments, matching $apptType, between $date and $endDate
*/

require_once 'Database.php';

class ScheduledAppointment {

// ========== Variables ========== //
	private static $conn;

	public $schedApptID;
	public $apptTypeID;
	public $curriculumID;
	public $timeStart;
	public $timeEnd;
	public $building;
	public $room;
	public $isPrivate;

	
// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //	
	private function __construct( $params ) {

		//Assign variables if they exist in $params array
		$this->schedApptID  = ( array_key_exists('SchedApptID', $params) )  ? $params['SchedApptID'] : null;
		$this->apptTypeID   = ( array_key_exists('ApptTypeID', $params) )   ? (int)$params['ApptTypeID'] : null;
		$this->curriculumID = ( array_key_exists('CurriculumID', $params) ) ? $params['CurriculumID'] : null;
		$this->timeStart    = ( array_key_exists('TimeStart', $params) )    ? $params['TimeStart'] : null;
		$this->timeEnd      = ( array_key_exists('TimeEnd', $params) )      ? $params['TimeEnd'] : null;
		$this->building     = ( array_key_exists('Building', $params) )     ? $params['Building'] : null;
		$this->room         = ( array_key_exists('Room', $params) )         ? $params['Room'] : null;
		$this->isPrivate    = ( array_key_exists('IsPrivate', $params) )    ? $params['IsPrivate'] : null;
	}



// ========== Instance Methods ========== //
	public function GetAppointmentTypeDetails () {
		require_once 'AppointmentType.php';
		return $this->AppointmentType = AppointmentType::GetByApptTypeID( $this->apptTypeID );
	}

	public function GetCurriculumDetails () {
		require_once 'Curriculum.php';
		return $this->Curriculum = Curriculum::GetByCurriculumID( $this->curriculumID );
	}

	public function GetStudentsAttending ( $extendedInfo = false ) {
		require_once 'Student.php';
		return $this->StudentsAttending = Student::ListBySchedApptID( $this->schedApptID, $extendedInfo );
	}



// ========== Static Methods ========== //
	public static function GetBySchedApptID ( $schedApptID ) {
		self::InitConnection();

		//query DB for record matching (SchedApptID = $schedApptID)
		$stmt = self::$conn->prepare(
			'SELECT `SchedApptID`, `TimeStart`, `TimeEnd`, `curriculumID`, `ApptTypeID`
		   FROM `ScheduledAppointment`
		   WHERE `SchedApptID` = :schedApptID');
		$stmt->bindParam(':schedApptID', $schedApptID, PDO::PARAM_INT);
		$stmt->execute();

		//if a match was found, return it
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			return new self( $r );
		}

		//If no match was found:
		else return false;
	}


	public static function ListByApptTypeID ( $apptTypeID, $curriculumID = null, $date = null, $endDate = null ) {
		self::InitConnection();

		//create query string 
		$qry = 'SELECT `SchedApptID`, `ApptTypeID`, `TimeStart`, `TimeEnd`
		        FROM `ScheduledAppointment`
		        WHERE `ApptTypeID` = :apptTypeID';


		//if $apptTypeID is 3 (department tour)..
		if ( $apptTypeID == 3 ) {

			//and a curriculum has been passed, append it to the query string
			if ( $curriculumID )
				$qry .= ' AND `CurriculumID` = :currID';
		}


		//if a valid date has been passed
		if ( strtotime( $date ) ) {

			//properly format the date and add filter it to query string
			$date = date( 'Y-m-d', strtotime( $date ) ).' 00:00:00';
			$qry .= " AND `TimeStart` >= :date";


			//if an end date has also been provided
			if ( strtotime( $endDate ) ) {

				//properly format endDate..
				$endDate = date( 'Y-m-d', strtotime( $endDate ) ).' 23:59:59';

				//and append it to query string
				$qry .= ' AND `TimeEnd` <= :endDate';
			}


			//if an end date has not been explicitly provided
			else {

				//set $endDate to equal the end of day: $date 
				$endDate = date( 'Y-m-d', strtotime( $date ) ).' 23:59:59';

				//and append it to the query
				$qry .= ' AND `TimeEnd` <= :endDate';
			}
		}


		//prepare query for execution
		$stmt = self::$conn->prepare($qry);

		//attach params to query and execute
		$stmt->bindParam( ':apptTypeID', $apptTypeID, PDO::PARAM_INT );
		( $curriculumID ) ? $stmt->bindParam( ':currID', $curriculumID, PDO::PARAM_STR ) : null;
		( $date )         ? $stmt->bindParam( ':date', $date, PDO::PARAM_STR )           : null;
		( $endDate )      ? $stmt->bindParam( ':endDate', $endDate, PDO::PARAM_STR )     : null;
		$stmt->execute();


		//On query success..
		if ( $arr = $stmt->fetchAll( PDO::FETCH_ASSOC ) ) {

			//create a new list to hold appts..
			$schedApptList = [];

			//populate it..
			foreach ($arr as $r) {
				array_push( $schedApptList, new self( $r ) );
			}

			//and return it.
			return $schedApptList;
		}

		//Return false on failure.
		else return false;
	}

}

?>