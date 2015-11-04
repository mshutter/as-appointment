<?php
	require_once 'Database.php';

	class ApptType {
		private static $conn;
		public $apptTypeList;

		public function __construct ( $onlyFacStaff = 0 ) {
			//if DB connection has not been established, do so.
			( !self::$conn ) ? self::$conn = Database::Connect() : null;

			$this->ListApptTypes( $onlyFacStaff );
		}

		// Populates $this->apptTypeList with appointment types.
		// pass $onlyFacStaff if list should only contain faculty/staff types
		private function ListApptTypes ( $onlyFacStaff = 0 ) {

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
			
			$this->apptTypeList = $stmt->fetchAll(PDO::FETCH_OBJ);
			return;
		}
	}
?>