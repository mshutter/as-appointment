<?php

/*
	=========================
	Model - StudentAgendaItem
	=========================

	Instance Variables
		$agendaID (string)    - Unique identifier of this StudentAgendaItem instance
		$schedApptID (string) - Foreign identifier, linking this instance with a StudentAgenda record


	Static Methods
		::ListByAgendaID( $agendaID [, $extendedInfo ] )
			- Returns array of StudentAgendaItems for the StudentAgenda matching $agendaID
			- If $extendedInfo is true, return ScheduledAppointment objects instead 
*/

require_once 'Database.php';

class StudentAgendaItem {

// ========== Variables ========== //
	private static $conn;

	public $agendaID;
	public $schedApptID;
	public $registrationTime;
	public $cancelled;


// ========== Database Connection ========== //
	private static function InitConnection () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		return false;
	}


// ========== Constructor ========== //
	private function __construct ( $params ) {

		//Assign variables if they exist in $params array
		$this->agendaID         = $params['AgendaID'];
		$this->schedApptID      = $params['SchedApptID'];
		$this->registrationTime = ( array_key_exists('RegistrationTime', $params) ) ? $params['RegistrationTime'] : null;
		$this->cancelled        = ( array_key_exists('Cancelled', $params) )   ? $params['Cancelled'] : '0';
	}


// ========== Static Methods ========== //
	public static function NewAgendaItem ( $params ) {
		if ( array_key_exists('AgendaID', $params) && array_key_exists('SchedApptID', $params) ) {
			return new self( $params );	
		} else return false;
	}


	public static function ListByAgendaID ( $agendaID, $extendedInfo = false ) {
		self::InitConnection();
		
		//Get list of agenda items from DB
		$stmt = self::$conn->prepare('SELECT * FROM `StudentAgendaItem`
		                              WHERE `AgendaID` = :agendaID');
		$stmt->bindParam(':agendaID', $agendaID, PDO::PARAM_STR);
		$stmt->execute();


		//If DB successfully pulled StudentAgendaItem records
		if ( $arr = $stmt->fetchAll(PDO::FETCH_ASSOC) ) {
			$agendaItems = [];

			//If extended information is requested..
			if ( $extendedInfo ) {
				require_once 'ScheduledAppointment.php';

				//populate $agendaItems with ScheduledAppointment objects.
				foreach ( $arr as $r ) {
					array_push( $agendaItems, ScheduledAppointment::GetBySchedApptID( $r['SchedApptID'] ) );
				}
			}

			//If extended information is not requested..
			else {

				//populate $agendaItems with StudentAgendaItems objects.
				foreach ( $arr as $r ) {
					array_push( $agendaItems, new self( $r ) );
				}
			}

			return $agendaItems;
		}

		//DB did not return any records matching this AgendaID
		else return false;
	}
}

?>