<?php

/*
	=========================
	Model - Student Agenda
	=========================

	Instance Variables
		$agendaID (string)   - 10 character alphanumeric unique identifier
		$studentID (string)  - 10 character alphanumeric UID linking agenda to student


	Instance Methods
		->GetAgendaItems( [ $extendedInfo = false ] )
			- Append $agendaItems (array) to this instance, containing all StudentAgendaItems
			  linked to this StudentAgenda.
			- Changing $extendedInfo from false will populate $agendaItems with ScheduledAppointment
			  objects (containing more info about the appt) instead.


	Static Methods
		::GetByAgendaID( $agendaID )
			- Returns StudentAgenda object matching $agendaID

		::ListByStudentID ( $studentID )
			- Returns array of all StudentAgenda objects associated with the specified student
*/

require_once 'Database.php';
require_once 'UID.php';

class StudentAgenda {

// ========== Variables ========== //
	private static $conn;
	private static $UID;

	public $agendaID;
	public $studentID;
	public $numGuests;


// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {

		//Assign variables if they exist in $params array
		$this->agendaID  = ( array_key_exists('AgendaID', $params) )  ? $params['AgendaID'] : null;
		$this->studentID = ( array_key_exists('StudentID', $params) ) ? $params['StudentID'] : null;
		$this->numGuests = ( array_key_exists('NumGuests', $params) ) ? $params['NumGuests'] : null;
	}

	private static function construct_multiple( $arr ) {

		$curriculumList = array();    //Create new list,
		foreach ( $arr as $r ) { //populate it with Curriculum objects,
			array_push( $curriculumList, new self($r) );
		}
		return $curriculumList;  //and return it.
	}


// ========== Instance Methods ========== //
	public function PushToDB () {
		self::InitConnection();

		if ( self::GetByAgendaID( $this->agendaID ) ) {
			//this StudentAgenda is already in the database
			//echo 'Update';

			$stmt = self::$conn->prepare('UPDATE `StudentAgenda`
			        SET `StudentID` = :StudentID,
			            `NumGuests` = :NumGuests
			        WHERE `AgendaID` = :AgendaID');
		}

		else {
			//this StudentAgenda is not in the database
			//echo 'Input';
			
			$stmt = self::$conn->prepare('INSERT INTO `StudentAgenda`
			        (`AgendaID`, `StudentID`, `NumGuests`)
			        VALUES (:AgendaID, :StudentID, :NumGuests)');
		}


		///BIND PARAMS
			if ( isset( $this->agendaID ) && $this->agendaID != "" )
				$stmt->bindParam( ':AgendaID', $this->agendaID, PDO::PARAM_STR );
			else return false;

			if ( isset( $this->studentID ) && $this->studentID != "" )
				$stmt->bindParam( ':StudentID', $this->studentID, PDO::PARAM_STR );
			else return false;

			if ( !isset( $this->numGuests ) && !is_numeric($this->numGuests) )
				$this->numGuests = "";
			$stmt->bindParam( ':NumGuests', $this->numGuests, PDO::PARAM_INT );
		///END PARAMS


		$stmt->execute();
		return true;
	}

	public function GetAgendaItems ( $extendedInfo = false ) {
		require_once 'StudentAgendaItem.php';

		//Populate $this->agendaItems with list of StudentAgendaItems and return $this, or return false
		if ( $this->agendaItems = StudentAgendaItem::ListByAgendaID( $this->agendaID, $extendedInfo ) ) {
			return $this;
		}
		else return false;
	}

	public function GetDate () {
		//Code to get date of Appointments
	}


// ========== Static Methods ========== //
	public static function NewStudentAgenda ( $params ) {
		$params['AgendaID'] = self::NewAgendaID();
		return new self( $params );
	}


	private static function NewAgendaID () {

		//set UID generator with callback to verify SchedApptID will be unique
		( !self::$UID ) ? self::$UID = new UID( array('StudentAgenda', 'GetByAgendaID') ) : null;
		return self::$UID->GetUniqueID(); //return uniqure SchedApptID
	}


	public static function GetByAgendaID ( $agendaID ) {
		self::InitConnection();

		$stmt = self::$conn->prepare('SELECT * FROM `StudentAgenda`
		                              WHERE `AgendaID` = :agendaID');
		$stmt->bindParam(':agendaID', $agendaID, PDO::PARAM_STR);
		$stmt->execute();

		//If query was successful, create new agenda object based on results
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ){
			return new self( $r );
		}

		else return false;
	}


	public static function ListByStudentID ( $studentID ) {
		self::InitConnection();

		$stmt = self::$conn->prepare('SELECT * FROM `StudentAgenda`
		                              WHERE `StudentID` = :studentID');
		$stmt->bindParam(':studentID', $studentID, PDO::PARAM_STR);
		$stmt->execute();

		//Query was successful
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {
			
			//Construct and return an array from the data
			return self::construct_multiple( $arr );
		}

		//Query was unsuccessful
		else return false;
	}

}

?>