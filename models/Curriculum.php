<?php

/*
	=========================
	Model - Curriculum
	=========================


	Instance Variables
		$curriculumID (string) - Unique ID (usually ~3-4 numeric digits)
		$departmentID (int)    - Numeric ID indicating what department this curriculum belongs to
		$title (string)        - Concise title given to present curriculum to users
		$description (string)  - Complete description detailing learning outcomes, courses, etc..

	
	Instance Methods
		->GetDepartmentDetails()
			- Return AND add to this instance a Department object matching this->departmentID


	Static Methods
		::GetByCurriculumID( $curriculumID )
			- Returns complete Curriculum object matching $curriculumID

		::ListAllCurriculums()
			- Return array of all Curriculums

		::ListByDepartmentID( [ $departmentID = -1 ] )
			- If $departmentID is not passed, return all curriculums, grouped by department
			- If department is specified, return array of Curriculum objects that match $departmentID
*/

require_once 'Database.php';

class Curriculum {

// ========== Variables ========== //
	private static $conn;

	public $curriculumID;
	public $departmentID;
	public $title;
	public $description;

	
// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Instance Methods ========== //
	public function GetDepartmentDetails ()  {
		require_once 'Department.php';
		return $this->Department = Department::GetByDepartmentID( $this->departmentID );
	}


// ========== Constructor ========== //	
	private function __construct( $params ) {

		//Assign variables if they exist in $params array
		$this->curriculumID = ( array_key_exists('CurriculumID', $params) ) ? $params['CurriculumID'] : null;
		$this->departmentID = ( array_key_exists('DepartmentID', $params) ) ? $params['DepartmentID'] : null;
		$this->title        = ( array_key_exists('Title', $params) )        ? $params['Title'] : null;
		$this->description  = ( array_key_exists('Description', $params) )  ? $params['Description'] : null;
	}


// ========== Static Methods ========== //
	public static function GetByCurriculumID( $curriculumID ) {
		self::InitConnection();

		//Get Curriculum from DB by CurriculumID
		$stmt = self::$conn->prepare('SELECT `CurriculumID`, `DepartmentID`, `Title`, `Description`
		                              FROM `Curriculum`
		                              WHERE `CurriculumID` = :curriculumID');
		$stmt->bindParam(':curriculumID', $curriculumID, PDO::PARAM_STR);
		$stmt->execute();

		//If Curriculum was retrieved successfully..
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) {

			//return a new Curriculum object.
			return new self( $r );
		}

		//If no Curriculum was retrieved from DB:
		else return false;
	}


	public static function ListAllCurriculums () {
		self::InitConnection();

		//Get all Curriculums from database
		$stmt = self::$conn->prepare('SELECT `CurriculumID`, `DepartmentID`, `Title`
		                              FROM `Curriculum`');
		$stmt->execute();

		//If Curriculums were successfully retrieved from DB..	
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create an array that will hold them..
			$curriculumList = [];

			//populate it with Curriculum objects..
			foreach ( $arr as $r ) {
				array_push( $curriculumList, new self($r) );
			}

			//and return it.
			return $curriculumList;
		}

		//If no Curriculums were retrieved from the DB:
		else return false;
	}


	public static function ListByDepartmentID ( $departmentID ) {
		self::InitConnection();

		//Get curriculums from DB that match $departmentID.
		$stmt = self::$conn->prepare('SELECT `CurriculumID`, `DepartmentID`, `Title`
		                              FROM `Curriculum`
		                              WHERE `DepartmentID` = :departmentID');
		$stmt->bindParam(':departmentID', $departmentID, PDO::PARAM_INT);
		$stmt->execute();

		//If Curriculums were successfully retrieved..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create an array to hold them..
			$curriculumList = [];

			//populate it with Curriculum objects..
			foreach ( $arr as $r ) {
				array_push( $curriculumList, new self($r) );
			}

			//and return it.
			return $curriculumList;
		}

		//If no Curriculums were retrieved:
		else return false;
	}

}

?>