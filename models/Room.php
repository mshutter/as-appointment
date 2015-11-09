<?php

/*
	=========================
	Model - Room
	=========================


	Instance Variables
		$buildingAbbrev (string) - Abbreviation of building in which room resides
		$roomNum (string)        - Room number or abbreviation of name
		$roomTitle (string)      - Full name of room, if one has been given (e.g. "Admissions Office")


	Static Methods
		::ListRooms( [ $buildingAbbrev = null ] )
			- Lists all rooms, grouped by building
			- Pass building abbreviation to return only rooms in that building 
*/

require_once 'Database.php';

class Room {

// ========== Variables ========== //
	private static $conn;

	public $buildingAbbrev;
	public $roomNum;
	public $roomTitle;


// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {

		//Assign variables if they exist in $params array
		$this->buildingAbbrev = ( array_key_exists('BuildingAbbrev', $params) ) ? $params['BuildingAbbrev'] : null;
		$this->roomNum     = ( array_key_exists('RoomNum', $params) )     ? $params['RoomNum'] : null;
		$this->roomTitle      = ( array_key_exists('RoomTitle', $params) )      ? $params['RoomTitle'] : null;
	}


// ========== Static Methods ========== //
	public static function ListRooms ( $buildingAbbrev = null ) {
		self::InitConnection();


		//If Building has been specified
		if ( $buildingAbbrev ) {

			//Get Rooms in that specific building
			$stmt = self::$conn->prepare('SELECT * FROM `Room`
			                              WHERE `BuildingAbbrev` = :buildingAbbrev');
			$stmt->bindParam(':buildingAbbrev', $buildingAbbrev, PDO::PARAM_STR);
			$stmt->execute();

			//If Rooms were successfully pulled from database..
			if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

				//create a new array..
				$roomList = [];

				//populate it with Room objects..
				foreach ( $arr as $r ) {
					array_push( $roomList, new self( $r ) );
				}

				//and return it.
				return $roomList;
			}

			//If no rooms were pulled from the DB:
			else return false;
		}


		//If building was not specified
		else {

			//Fetch an array of all buildings from DB
			$stmt = self::$conn->prepare('SELECT * FROM `Building`');
			$stmt->execute();

			//If buildings were successfully pulled from DB..
			if ( $buildingList = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {

				//for each building..
				foreach ( $buildingList as &$building ) {

					//get list of rooms within that building..
					$roomList = self::ListRooms( $building['BuildingAbbrev'] );

					//and append the list to the corresponding building in $buildingList.
					$building['roomList'] = $roomList;
				}

				//If $buildingList has been successfully constructed, return it.
				return $buildingList;
			}

			//List of buildings was not retrieved from DB:
			else return false;
		}
	}

}

?>