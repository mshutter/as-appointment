<?php

/*
	=========================
	Model - Student
	=========================

	Instance Variables
		$studentID - Unique identifier / 10 randomly generated alphanumeric characters
		$email
		$lastName
		...


	Static Methods
		::GetByStudentID( $studentID [, $extendedInfo = false ] )
			- Returns basic information (ID, email, name) of student matching $studentID
			- Pass true to $extendedInfo to return all information regarding this student

		::ListAllStudents( [ $extendedInfo = false ] )
			- Returns basic information (ID, email, name) of all students
			- Pass true to $extendedInfo to return all information for all students

		::ListBySchedApptID( $schedApptID [, $extendedInfo = false ] )
			- Returns basic information on Students that have registered to attend a scheduled appointment
			- Pass true to $extendedInfo to return all information for all students
*/

require_once 'Database.php';
require_once 'UID.php';

class Student {

// ========== Variables ========== //
	private static $conn;
	private static $UID;

	public $studentID;
	public $email;
	public $lastName;
	public $firstName;
	public $middleInitial;
	public $birthDate;
	public $addressLine1;
	public $addressLine2;
	public $city;
	public $state;
	public $zip;
	public $primaryPhone;
	public $primaryIsMobile;
	public $secondaryPhone;
	public $secondaryIsMobile;
	public $highSchool;
	public $gradYear;


// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {

		//Assign variables if they exist in $params array
		$this->studentID      = ( array_key_exists('StudentID', $params) )      ? $params['StudentID'] : null;
		$this->email          = ( array_key_exists('Email', $params) )          ? $params['Email'] : null;
		$this->lastName       = ( array_key_exists('LastName', $params) )       ? $params['LastName'] : null;
		$this->firstName      = ( array_key_exists('FirstName', $params) )      ? $params['FirstName'] : "";
		$this->middleInitial  = ( array_key_exists('MiddleInitial', $params) )  ? $params['MiddleInitial'] : null;
		$this->birthDate      = ( array_key_exists('BirthDate', $params) )      ? $params['BirthDate'] : null;
		$this->addressLine1   = ( array_key_exists('AddressLine1', $params) )   ? $params['AddressLine1'] : null;
		$this->addressLine2   = ( array_key_exists('AddressLine2', $params) )   ? $params['AddressLine2'] : "";
		$this->city           = ( array_key_exists('City', $params) )           ? $params['City'] : null;
		$this->state          = ( array_key_exists('State', $params) )          ? $params['State'] : null;
		$this->zip            = ( array_key_exists('Zip', $params) )            ? $params['Zip'] : null;
		$this->primaryPhone   = ( array_key_exists('PrimaryPhone', $params) )   ? $params['PrimaryPhone'] : null;
		$this->secondaryPhone = ( array_key_exists('SecondaryPhone', $params) ) ? $params['SecondaryPhone'] : null;
		$this->primaryIsMobile = ( array_key_exists('PrimaryIsMobile', $params) ) ? $params['PrimaryIsMobile'] : 0;
		$this->secondaryIsMobile = ( array_key_exists('SecondaryIsMobile', $params) ) ? $params['SecondaryIsMobile'] : 0;
		$this->highSchool     = ( array_key_exists('HighSchool', $params) )     ? $params['HighSchool'] : null;
		$this->gradYear       = ( array_key_exists('GradYear', $params) )       ? $params['GradYear'] : null;
	}

	private static function construct_multiple( $arr ) {

		$studentList = [];       //Create new list,
		foreach ( $arr as $r ) { //populate it with Curriculum objects,
			array_push( $studentList, new self($r) );
		}
		return $studentList;     //and return it.
	}

// ========== Instance Methods ========== //
	public function PushToDB () {
		self::InitConnection();

		if ( self::GetByStudentID( $this->studentID ) ) {
			//this Student is already in the database
			//echo 'Update';

			$stmt = self::$conn->prepare('UPDATE `Student`
			        SET `StudentID`     = :StudentID,
			            `Email`         = :Email,
			            `LastName`      = :LastName,
			            `FirstName`     = :FirstName,
			            `MiddleInitial` = :MiddleInitial,
			            `BirthDate`     = :BirthDate,
			            `AddressLine1`  = :AddressLine1,
			            `AddressLine2`  = :AddressLine2,
			            `City`          = :City,
			            `State`         = :State,
			            `Zip`           = :Zip,
			            `PrimaryPhone`  = :PrimaryPhone,
			            `SecondaryPhone` = :SecondaryPhone,
			            `PrimaryIsMobile` = :PrimaryIsMobile,
			            `SecondaryIsMobile` = :SecondaryIsMobile,
			            `HighSchool`    = :HighSchool,
			            `GradYear`      = :GradYear
			        WHERE `StudentID` = :StudentID');
		}

		else {
			//this Student is not in the database
			//echo 'Input';
			
			$stmt = self::$conn->prepare('INSERT INTO `Student`
			        (`StudentID`, `Email`, `LastName`, `FirstName`, `MiddleInitial`, `BirthDate`, `AddressLine1`,
			         `AddressLine2`, `City`, `State`, `Zip`, `PrimaryPhone`, `SecondaryPhone`, `PrimaryIsMobile`,
			         `SecondaryIsMobile`, `HighSchool`, `GradYear`)
			        VALUES (:StudentID, :Email, :LastName, :FirstName, :MiddleInitial, :BirthDate, :AddressLine1,
			         :AddressLine2, :City, :State, :Zip, :PrimaryPhone, :SecondaryPhone, :PrimaryIsMobile,
			         :SecondaryIsMobile, :HighSchool, :GradYear)');
		}

		///BIND PARAMS
			//confirm and bind studentID
			if ( isset( $this->studentID ) && $this->studentID != "" )
				$stmt->bindParam( ':StudentID', $this->studentID, PDO::PARAM_STR );
			else return false;

			//confirm and bind email
			if ( isset( $this->email ) && filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) )
				$stmt->bindParam( ':Email', $this->email, PDO::PARAM_STR );
			else return false;

			//bind name
			$stmt->bindParam( ':LastName', $this->lastName, PDO::PARAM_STR );
			$stmt->bindParam( ':FirstName', $this->firstName, PDO::PARAM_STR );
			$stmt->bindParam( ':MiddleInitial', $this->middleInitial, PDO::PARAM_STR );

			//bind birth date
			$stmt->bindParam( ':BirthDate', $this->birthDate, PDO::PARAM_STR );

			//bind address
			$stmt->bindParam( ':AddressLine1', $this->addressLine1, PDO::PARAM_STR );
			$stmt->bindParam( ':AddressLine2', $this->addressLine2, PDO::PARAM_STR );
			$stmt->bindParam( ':City', $this->city, PDO::PARAM_STR );
			$stmt->bindParam( ':State', $this->state, PDO::PARAM_STR );
			$stmt->bindParam( ':Zip', $this->zip, PDO::PARAM_STR );

			//bind phone info
			$stmt->bindParam( ':PrimaryPhone', $this->primaryPhone, PDO::PARAM_STR );
			$stmt->bindParam( ':SecondaryPhone', $this->secondaryPhone, PDO::PARAM_STR );
			$stmt->bindParam( ':PrimaryIsMobile', $this->primaryIsMobile, PDO::PARAM_INT );
			$stmt->bindParam( ':SecondaryIsMobile', $this->secondaryIsMobile, PDO::PARAM_INT );

			//bind high school info
			$stmt->bindParam( ':HighSchool', $this->highSchool, PDO::PARAM_STR );
			$stmt->bindParam( ':GradYear', $this->gradYear, PDO::PARAM_STR );
		///BIND PARAMS

		$stmt->execute();
		return true;
	}

// ========== Static Methods ========== //
	public static function NewStudent ( $params ) {
		$params['StudentID'] = self::NewStudentID();
		return new self( $params );
	}


	private static function NewStudentID () {

		//set UID generator with callback to verify SchedApptID will be unique
		( !self::$UID ) ? self::$UID = new UID( ['Student', 'GetByStudentID'] ) : null;
		return self::$UID->GetUniqueID(); //return uniqure SchedApptID
	}


	public static function GetByStudentID ( $studentID, $extendedInfo = false ) {
		self::InitConnection();

		//If extended info is requested..
		if ( $extendedInfo ) {

			//query DB for all information about student.
			$stmt = self::$conn->prepare('SELECT * FROM `Student`
			                              WHERE `StudentID` = :studentID');
		}
		else { //Else, only query for general information.
			$stmt = self::$conn->prepare('SELECT `StudentID`, `Email`, `LastName`, `FirstName`, `MiddleInitial`
			                              FROM `Student`
			                              WHERE `StudentID` = :studentID');
		}
		$stmt->bindParam(':studentID', $studentID, PDO::PARAM_STR);
		$stmt->execute();

		//If query was successful in retrieving a record, return Student object
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) )
			return new self($r);

		//If query yielded no results:
		else return false;
	}


	public static function ListAllStudents ( $extendedInfo = false ) {
		self::InitConnection();

		//If extended information is requested..
		if ( $extendedInfo ) {

			//query the DB for all information
			$stmt = self::$conn->prepare('SELECT * FROM `Student`');
		}
		else { //Else, only query for general information.
			$stmt = self::$conn->prepare('SELECT `StudentID`, `Email`, `LastName`, `FirstName` FROM `Student`');
		}
		$stmt->execute();

		//If query successfully retrieved records from DB..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create a list to hold students..
			$studentList = [];

			//and populate it with Student objects.
			foreach ($arr as $r) {
				array_push( $studentList, new self($r) );
			}

			return $studentList;
		}

		//If no records were retrieved:
		else return false;
	}


	public static function ListBySchedApptID ( $schedApptID, $extendedInfo = false ) {
		self::InitConnection();

		//Initialize query string depending on value for $extendedInfo
		if ( $extendedInfo ) {
			$qry = 'SELECT `s`.* FROM `Student` AS `s`';	        
		} else {
			$qry = 'SELECT `s`.`StudentID`, `s`.`Email`, `s`.`LastName`, `s`.`FirstName`
			        FROM `Student` AS `s`';
		}

		//Finish query string and prepare query
		$qry .= ' INNER JOIN `StudentAgenda` AS `sa`
		          	ON `s`.`StudentID` = `sa`.`StudentID`
		          INNER JOIN `StudentAgendaItem` AS `sai`
		          	ON `sa`.`AgendaID` = `sai`.`AgendaID`
		          INNER JOIN `ScheduledAppointment` AS `sched`
		          	ON `sai`.`SchedApptID` = `sched`.`SchedApptID`
		          WHERE `sched`.`SchedApptID` = :schedApptID
		          	AND `sa`.`Cancelled` = 0';
		$stmt = self::$conn->prepare($qry);
		$stmt->bindParam(':schedApptID', $schedApptID, PDO::PARAM_STR);
		$stmt->execute();

		//If query successfully retrieved records from DB..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create a list to hold students..
			$studentList = [];

			//and populate it with Student objects.
			foreach ($arr as $r) {
				array_push( $studentList, new self($r) );
			}

			return $studentList;
		}

		//If no records were retrieved:
		else return false;
	}

}

?>