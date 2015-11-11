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

class Student {

// ========== Variables ========== //
	private static $conn;

	public $studentID;
	public $email;
	public $lastName;
	public $firstName;
	public $middleInitial;
	public $birthDate;
	public $streetAddress;
	public $city;
	public $state;
	public $zip;
	public $primaryPhone;
	public $secondaryPhone;
	public $highSchool;


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
		$this->firstName      = ( array_key_exists('FirstName', $params) )      ? $params['FirstName'] : null;
		$this->middleInitial  = ( array_key_exists('MiddleInitial', $params) )  ? $params['MiddleInitial'] : null;
		$this->birthDate      = ( array_key_exists('BirthDate', $params) )      ? $params['BirthDate'] : null;
		$this->streetAddress  = ( array_key_exists('StreetAddress', $params) )  ? $params['StreetAddress'] : null;
		$this->city           = ( array_key_exists('City', $params) )           ? $params['City'] : null;
		$this->state          = ( array_key_exists('State', $params) )          ? $params['State'] : null;
		$this->zip            = ( array_key_exists('Zip', $params) )            ? $params['Zip'] : null;
		$this->primaryPhone   = ( array_key_exists('PrimaryPhone', $params) )   ? $params['PrimaryPhone'] : null;
		$this->secondaryPhone = ( array_key_exists('SecondaryPhone', $params) ) ? $params['SecondaryPhone'] : null;
		$this->highSchool     = ( array_key_exists('HighSchool', $params) )     ? $params['HighSchool'] : null;
	}


// ========== Static Methods ========== //
	public static function GetByStudentID ( $studentID, $extendedInfo = false ) {
		self::InitConnection();

		//If extended info is requested..
		if ( $extendedInfo ) {

			//query DB for all information about student.
			$stmt = self::$conn->prepare('SELECT * FROM `Student`
			                              WHERE `StudentID` = :studentID');
		}
		else { //Else, only query for general information.
			$stmt = self::$conn->prepare('SELECT `StudentID`, `Email`, `LastName`, `FirstName`
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