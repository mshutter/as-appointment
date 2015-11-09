<?php

/*
	=========================
	Model - ApptType (Appointment Type)
	=========================
	

	Instance Variables
		$typeID (int)         - Numeric ID identifying ApptType in database
		$title (string)       - Concise label describing this type of appointment to the user
		$description (string) - More complete explanation of this type of appointment


	Static Methods
		::GetApptType( $typeID )
			- Returns TypeID, Title and Description of one ApptType by ID

		::ListApptTypes( [ $onlyFacStaff = false ] )
			- Returns TypeID and Title of each type of appointment
			- Pass true to only return "faculty or staff meeting" appointment types
*/

require_once 'Database.php';

class ApptType {

// ========== Variables ========== //
	private static $conn;

	public $typeID;
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
		$this->typeID      = ( array_key_exists('TypeID', $params) )      ? (int)$params['TypeID'] : null;
		$this->title       = ( array_key_exists('Title', $params) )       ? $params['Title'] : null;
		$this->description = ( array_key_exists('Description', $params) ) ? $params['Description'] : null;
	}


// ========== Static Methods ========== //
	public static function GetApptType ( $typeID ) {
		self::InitConnection();

		//Get ApptType from DB by TypeID
		$stmt = self::$conn->prepare('SELECT `TypeID`, `Title`, `Description`
		                              FROM `ApptType`
		                              WHERE `TypeID` = :typeID');
		$stmt->bindParam(':typeID', $typeID, PDO::PARAM_INT);
		$stmt->execute();

		//If ApptType was retrieved successfully..
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) {

			//return new ApptType object
			return new self( $r );
		}

		//If ApptType could not be retrieved from DB:
		else return false;
	}


	public static function ListApptTypes ( $onlyFacStaff = 0 ) {
		self::InitConnection();

		// If list should only contain faculty/staff appts..
		if ( $onlyFacStaff ) {

			// Get only those types.
			$stmt = self::$conn->query('SELECT `TypeID`, `Title`
		                              FROM `ApptType`
		                              WHERE `IsFacultyOrStaffAppt` > 0');
		}
		else {
			// Or else, get all appointment types.
			$stmt = self::$conn->query('SELECT `TypeID`, `Title` FROM `ApptType`');
		}

		//If ApptTypes were successfully pulled from database..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create an array that will hold them..
			$apptTypeList = [];

			//populate it with ApptType objects..
			foreach ( $arr as $r ) {
				array_push( $apptTypeList, new self($r) );
			}

			//and return it.
			return $apptTypeList;
		}

		//If no ApptTypes were retrieved from DB:
		else return false;
	}

}

?>