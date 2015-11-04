<?php

require_once 'Database.php';

/* Original appointment types:
	ID  Title
	 0  Other/Unspecified
	 1  Lunch
	 2  Campus Tour
	 3  Department Tour
	 4  Admissions
	 5  Financial Aid
	 6  Alfred State Opportunity Program
	 7  Student Disability
	 8  Athletics
 */

class ScheduledAppointment {
	private static $conn;


	public function __construct () {
		//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
	}


	public static function ListByApptType ( $apptType, $date = null, $endDate = null, $currID = null ) {
		//empty instantiation to ensure DB connection
		new self();

		//create query string 
		$qry = 'SELECT `SchedApptID`, `ApptType`, `TimeStart`, `TimeEnd`
		        FROM `ScheduledAppointment`
		        WHERE `ApptType` = :apptType';


		//if $apptType is 3 (department tour)..
		if ( $apptType == 3 ) {

			//and a curriculum has been passed, append it to the query string
			if ( $currID )
				$qry .= ' AND `CurriculumID` = :currID';

			//or else, break the function
			else return null;
		}


		//if a valid date has been passed
		if ( strtotime( $date ) ) {

			//properly format the date and add it to query string
			$date = date( 'Y-m-d', strtotime( $date ) ).' 00:00:00';
			$qry .= " AND `TimeStart` >= :date";


			//if an end date has also been provided
			if ( strtotime( $endDate ) ) {

				//properly format endDate..
				$endDate = date( 'Y-m-d', strtotime( $endDate ) ).' 23:59:59';

				//and append it to query string
				$qry .= ' AND `TimeEnd` <= :endDate';
			}


			//if an end date has not been explicitly provided
			else {

				//set $endDate to equal the end of day: $date 
				$endDate = date( 'Y-m-d', strtotime( $date ) ).' 23:59:59';

				//and append it to the query
				$qry .= ' AND `TimeEnd` <= :endDate';
			}
		}


		//prepare query for execution
		$stmt = self::$conn->prepare($qry);

		//attach params to query and execute
		$stmt->bindParam( ':apptType', $apptType, PDO::PARAM_INT );
		( $currID )  ? $stmt->bindParam( ':currID', $currID, PDO::PARAM_STR )   : null;
		( $date )    ? $stmt->bindParam( ':date', $date, PDO::PARAM_STR )       : null;
		( $endDate ) ? $stmt->bindParam( ':endDate', $endDate, PDO::PARAM_STR ) : null;
		$stmt->execute();


		//return query result on success..
		if ( $r = $stmt->fetchAll( PDO::FETCH_OBJ ) )
			return $r;

		// or false on failure
		else return null;
	}


	public static function GetByID ( $id ) {
		//create new instance
		$instance = new self();

		//query DB for record matching (SchedApptID = $id)
		$stmt = self::$conn->prepare(
			'SELECT `SchedApptID`, `TimeStart`, `TimeEnd`, `curriculumID`, `ApptType`
		   FROM `ScheduledAppointment`
		   WHERE `SchedApptID` = :id');
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$stmt->execute();

		//if a match was found, populate the instance and return it
		if ( $r = $stmt->fetch(PDO::FETCH_OBJ) )
			return $r;

		else
			return null;
	}
}

?>