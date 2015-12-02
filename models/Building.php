<?php

/*
	=========================
	Model - Building
	=========================

	Instance Variables
		$buildingAbbrev (string) - Unique abbreviation identifying each building
		$buildingName (string)   - Name of each building
		$isOnAlfredCampus (bool) - TRUE if at Alfred campus, FALSE if at Wellsville


	Instance Methods
		->GetRooms()
			- Returns AND appends to this instance a list of all Room objects
			  related to this building 

	Static Methods
		::GetByBuildingAbbrev( $buildingAbbrev )
			- Returns building matching abbreviation or FALSE

		::ListBuildings( [ $campus = null ] )
			- If passed no arguments, return all buildings
			- If ($campus = 1) return all buildings on Alfred campus
			- If ($campus = 2) return all buildings on Wellsville campus
*/

require_once 'Database.php';

class Building {

// ========== Variables ========== //
	private static $conn;

	public $buildingAbbrev;
	public $buildingName;
	public $isOnAlfredCampus;


// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {

		//Assign variables if they exist in $params array
		$this->buildingAbbrev   = ( array_key_exists('BuildingAbbrev', $params) )   ? $params['BuildingAbbrev'] : null;
		$this->buildingName     = ( array_key_exists('BuildingName', $params) )     ? $params['BuildingName'] : null;
		$this->isOnAlfredCampus = ( array_key_exists('IsOnAlfredCampus', $params) ) ? (bool)$params['IsOnAlfredCampus'] : null;
	}

	private static function construct_multiple( $arr ) {

		$buildingList = [];      //Create new list,
		foreach ( $arr as $r ) { //populate it with Curriculum objects,
			array_push( $buildingList, new self($r) );
		}
		return $buildingList;    //and return it.
	}


// ========== Instance Methods ========== //
	public function GetRooms () {
		require_once 'Room.php';
		return $this->rooms = Room::ListByBuildingAbbrev( $this->buildingAbbrev );
	}


// ========== Static Methods ========== //
	public static function GetByBuildingAbbrev( $buildingAbbrev ) {
		self::InitConnection();

		$stmt = self::$conn->prepare('SELECT * FROM `Building`
		                              WHERE `BuildingAbbrev` = :buildingAbbrev');
		$stmt->bindParam(':buildingAbbrev', $buildingAbbrev, PDO::PARAM_STR);
		$stmt->execute();

		//If building matching abbrev is found, return it
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) )
			return new self( $r );

		//If no match has been found:
		else return false;
	}


	public static function ListBuildings( $campus = null ) {
		self:: InitConnection();

		//Create query string
		$qry = 'SELECT * FROM `Building`';

		//If campus has been selected..
		if ( $campus ) {

			//and campus == 1, return buildings on Alfred campus
			if ( $campus == 1 ) {
				$qry .= ' WHERE `IsOnAlfredCampus` = 1';
			}

			//If $campus == 2, return buildings on Wellsville campus
			if ( $campus == 2 ) {
				$qry .= ' WHERE `IsOnAlfredCampus` = 0';
			}
		}

		//Execute query
		$stmt = self::$conn->prepare($qry);
		$stmt->execute();

		//If the query was successful in fetching results
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

			//Construct and return an array from the data
			return self::construct_multiple( $arr );
		}

		//If query did not find any results:
		else return false;
	}

}

?>