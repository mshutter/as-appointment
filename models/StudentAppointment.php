<?php

require_once 'Database.php';

class StudentAppointment {
	private static $conn;
	private $apptID;
	private $studentID;
	private $schedApptID;


	// ********** Constructor **********
	public function __construct( $apptID = -1 ) {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;

		// If $apptID is not provided, generate a new one
		if ( $apptID < 0 ) {
			$this->CreateAppointment();
		}

		// Else, attempt to populate this with an existing appt in DB
		else {

			//and if an existing record is not found..
			if ( !$this->GetAppointment( $apptID ) )

				//create a new one.
				$this->CreateAppointment();
		}
	}


	// ********** Accessors **********
	public function getApptID() {
		return $this->apptID; }

	public function getStudentID() {
		return $this->studentID; }
	public function setStudentID($value) {
		$this->studentID = $value; }

	public function getSchedApptID() {
		return $this->schedApptID; }
	public function setSchedApptID ($value) {
		$this->schedApptID = $value; }


	// ********** Create/Get Appointment **********
	private function CreateAppointment () {
		// Generate a new UID..
		$this->apptID = self::ValidUID();

		// and create a new record in the database.
		$stmt = self::$conn->prepare( 'INSERT INTO `StudentAppointment` (`StudentApptID`)
		                               VALUES (:apptID)' );
		$stmt->bindParam( ':apptID', $this->apptID, PDO::PARAM_STR );
		$stmt->execute();
		return;
	}

	private function GetAppointment ( $apptID ) {
		$stmt = self::$conn->prepare( 'SELECT `StudentApptID`, `StudentID`, `SchedApptID`
		                               FROM `StudentAppointment`
		                               WHERE `StudentApptID` = :apptID
		                                   AND `cancelled` = 0');
		$stmt->bindParam( ':apptID', $apptID, PDO::PARAM_STR );
		$stmt->execute();

		// If record was found in the database..
		if ( $r = $stmt->fetch( PDO::FETCH_OBJ ) ) {

			// populate this object..
			$this->apptID      = $r->StudentApptID;
			$this->studentID   = $r->StudentID;
			$this->schedApptID = $r->SchedApptID;

			// and return.
			return true;
		}

		else {
			return false;
		}
	}


	// Generate a 10 character UID and return it if it matches no pre-existing records in the database
	private function ValidUID () {
		$UID = substr( md5( rand() ), 0, 10 );

		//while matches are found in the database that already use this UID
		//generate new UIDs to keep each unique
		while ( true ) {
			$stmt = self::$conn->prepare( 'SELECT `StudentApptID`
			                               FROM `StudentAppointment`
			                               WHERE `StudentApptID` = :UID');
			$stmt->bindParam( ':UID', $UID, PDO::PARAM_STR );
			$stmt->execute();

			if ( $stmt->fetch() )
				$UID = substr( md5( rand() ), 0, 10 );			
			else 
				return $UID;
		}
	}


	// ********** Display Method **********
	public function __toString () {
		$str = 'Appointment ID: ' . $this->getApptID() . '<br />';
		if ( $this->getStudentID() )
			$str += 'Student ID: ' . $this->getStudentID() . '<br />';
		if ( $this->getSchedApptID() )
			$str += 'Scheduled Appointment ID: ' . $this->getSchedApptID() . '<br />';
		return $str;
	}
}

?>