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

	private static function construct_multiple( $arr ) {

		$agendaItemList = array();    //Create new list,
		foreach ( $arr as $r ) { //populate it with studentAgendaItem objects,
			array_push( $agendaItemList, new self($r) );
		}
		return $agendaItemList;  //and return it.
	}

// ========== Instance Methods ========== //
	public function PushToDB() {
		self::InitConnection();

		if ( StudentAgendaItem::GetByAgendaAndSchedApptID( $this->agendaID, $this->schedApptID ) ) {
			$stmt = self::$conn->prepare('UPDATE `StudentAgendaItem`
			        SET `RegistrationTime` = :RegistrationTime,
			            `Cancelled`        = :Cancelled
			        WHERE `AgendaID`       = :AgendaID
			          AND `SchedApptID`    = :SchedApptID');
		}

		else {
			$stmt = self::$conn->prepare('INSERT INTO `StudentAgendaItem`
			        (`AgendaID`, `SchedApptID`, `RegistrationTime`, `Cancelled`)
			        VALUES (:AgendaID, :SchedApptID, :RegistrationTime, :Cancelled)');
		}

		$stmt->bindParam(':AgendaID', $this->agendaID, PDO::PARAM_STR);
		$stmt->bindParam(':SchedApptID', $this->schedApptID, PDO::PARAM_STR);
		$stmt->bindParam(':RegistrationTime', $this->registrationTime, PDO::PARAM_STR);
		$stmt->bindParam(':Cancelled', $this->cancelled, PDO::PARAM_INT);

		return $stmt->execute();
	}


// ========== Static Methods ========== //
	public static function NewAgendaItem ( $params ) {
		if ( array_key_exists('AgendaID', $params) && array_key_exists('SchedApptID', $params) ) {
			return new self( $params );	
		} else return false;
	}


	public static function GetByAgendaAndSchedApptID ( $agendaID, $schedApptID ) {
		self::InitConnection();

		$stmt = self::$conn->prepare('SELECT * FROM `StudentAgendaItem`
		                              WHERE `AgendaID` = :AgendaID
		                                AND `SchedApptID` = :SchedApptID');
		$stmt->bindParam(':AgendaID', $agendaID, PDO::PARAM_STR);
		$stmt->bindParam(':SchedApptID', $schedApptID, PDO::PARAM_STR);
		$stmt->execute();
		if ( $r = $stmt->fetch(PDO::FETCH_ASSOC) )
			return new self($r);
		else return false;
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
			$agendaItems = array();

			//If extended information is requested..
			if ( $extendedInfo ) {
				require_once 'ScheduledAppointment.php';
				require_once 'Building.php';

				//populate $agendaItems with ScheduledAppointment objects.
				foreach ( $arr as $r ) {
					$appt = ScheduledAppointment::GetBySchedApptID( $r['SchedApptID'] );
					$appt->GetAppointmentTypeDetails();
					$appt->Building = Building::GetByBuildingAbbrev( $appt->building );
					$appt->GetCurriculumDetails();
					array_push( $agendaItems, $appt );
				}

				//sort by time
				function timeCompare ($a, $b) {
					$a = strtotime($a->timeStart);
					$b = strtotime($b->timeStart);
					if ($a == $b) {
						return 0;
					}
					return ($a < $b) ? -1 : 1;
				}
				usort($agendaItems, 'timeCompare');
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