<?php

/*
	=========================
	Model - Department
	=========================


	Instance Variables
		$departmentID (string) - Unique ID (10 alphanumeric characters)
		$title (string)        - Title of department displayed to users
		$description (string)  - Short paragraph describing department to users

	
	Instance Methods
		->GetCurriculums()
			- Returns AND appends to this instance a list of Curriculum objects
			  matching $this->departmentID


	Static Methods
		::GetByDepartmentID( $departmentID )
			- Returns Department object matching the given departmentID

		::ListAllDepartments( )
			- Returns array of Department objects containing all departments in DB
*/

require_once 'Database.php';

class Department {

// ========== Variables ========== //
	private static $conn;

	public $departmentID;
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
		$this->departmentID = ( array_key_exists('DepartmentID', $params) ) ? $params['DepartmentID'] : null;
		$this->title        = ( array_key_exists('Title', $params) )        ? $params['Title'] : null;
		$this->description  = ( array_key_exists('Description', $params) )  ? $params['Description'] : null;
	}


// ========== Instance Methods ========== //
	public function GetCurriculums() {
		require_once 'Curriculum.php';
		return $this->Curriculums = Curriculum::ListByDepartmentID( $this->departmentID );
	}


// ========== Static Methods ========== //
	public static function GetByDepartmentID ( $departmentID ) {
		self::InitConnection();

		//Query Department for a matching DepartmentID
		$stmt = self::$conn->prepare('SELECT * FROM `Department`
		                              WHERE `DepartmentID` = :departmentID');
		$stmt->bindParam(':departmentID', $departmentID, PDO::PARAM_INT);
		$stmt->execute();

		//Return matched Department or false
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) ) {
			return new self( $r );
		} else return false;
	}


	public static function ListAllDepartments () {
		self::InitConnection();

		//Query Database for all Departments
		$stmt = self::$conn->prepare('SELECT * FROM `Department`');
		$stmt->execute();

		//If query was successful in retrieving Departments..
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//create an array to hold them..
			$departmentList = [];

			//populate it..
			foreach ( $arr as $r ) {
				array_push( $departmentList, new self( $r ) );
			}

			//and return it.
			return $departmentList;
		}

		//If no departments were retrieved from DB:
		else return false;
	}

}

?>