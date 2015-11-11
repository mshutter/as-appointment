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

class StudentAgenda {

// ========== Variables ========== //
	private static $conn;

	public $agendaID;
	public $studentID;


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
	}


// ========== Instance Methods ========== //
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
			$agendaList = [];

			foreach ( $arr as $r ) {
				array_push( $agendaList, new self( $r ) );
			}

			return $agendaList;
		}

		//Query was unsuccessful
		else return false;
	}

}

?>