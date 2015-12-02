<?php

/*
	=========================
	Model - AppointmentType
	=========================
	

	Instance Variables
		$apptTypeID (int)     - Numeric ID identifying AppointmentType in database
		$title (string)       - Concise label describing this type of appointment to the user
		$description (string) - More complete explanation of this type of appointment


	Static Methods
		::GetByApptTypeID( $apptTypeID )
			- Returns ApptTypeID, Title and Description of one AppointmentType by ID

		::ListAppointmentTypes( [ $onlyFacStaff = false ] )
			- Returns ApptTypeID and Title of each type of appointment
			- Pass true to only return "faculty or staff meeting" appointment types
*/

require_once 'Database.php';

class AppointmentType {

// ========== Variables ========== //
	private static $conn;

	public $apptTypeID;
	public $title;
	public $description;

	
// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {
		
		//Assign variables if they exist in $params array
		$this->apptTypeID  = ( array_key_exists('ApptTypeID', $params) )  ? (int)$params['ApptTypeID'] : null;
		$this->title       = ( array_key_exists('Title', $params) )       ? $params['Title'] : null;
		$this->description = ( array_key_exists('Description', $params) ) ? $params['Description'] : null;
	}

	private static function construct_multiple( $arr ) {

		$apptTypeList = [];      //Create new list,
		foreach ( $arr as $r ) { //populate it with AppointmentType objects,
			array_push( $apptTypeList, new self($r) );
		}
		return $apptTypeList;    //and return it.
	}


// ========== Static Methods ========== //
	public static function GetByApptTypeID ( $apptTypeID ) {
		self::InitConnection();

		//Get ApptType from DB by ApptTypeID
		$stmt = self::$conn->prepare('SELECT `ApptTypeID`, `Title`, `Description`
		                              FROM `AppointmentType`
		                              WHERE `ApptTypeID` = :apptTypeID');
		$stmt->bindParam(':apptTypeID', $apptTypeID, PDO::PARAM_INT);
		$stmt->execute();

		//If ApptType was retrieved successfully, return new AppointmentType object
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			return new self( $r );
		}

		//Query was unsuccessful
		else return false;
	}


	public static function ListAppointmentTypes ( $onlyFacStaff = 0 ) {
		self::InitConnection();

		// If list should only contain faculty/staff appts..
		if ( $onlyFacStaff ) {

			// Get only those types.
			$stmt = self::$conn->query('SELECT `ApptTypeID`, `Title`
		                              FROM `AppointmentType`
		                              WHERE `IsFacultyOrStaffAppt` > 0');
		}
		else {
			// Or else, get all appointment types.
			$stmt = self::$conn->query('SELECT `ApptTypeID`, `Title` FROM `AppointmentType`');
		}

		//If ApptTypes were successfully pulled from database..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//Construct and return an array from the data
			return self::construct_multiple( $arr );
		}

		//If no ApptTypes were retrieved from DB:
		else return false;
	}

}

?>