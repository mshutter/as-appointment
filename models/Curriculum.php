<?php
	require_once 'Database.php';

	class Curriculum {
		private static $conn;
		public $currList;

		public function __construct( $deptID = -1 ) {
			//if DB connection has not been established, do so.
		( !self::$conn ) ? self::$conn = Database::Connect() : null;
		
			$this->ListCurriculums( $deptID );
		}


		// Populates $this->currList with curriculums
		// pass $deptID to filter by department
		private function ListCurriculums ( $deptID = -1 ) {

			// If $deptID is invalid parameter or default..
			if ( !is_numeric( $deptID ) || $deptID < 0 ) {

				// Get all curriculums.
				$stmt = self::$conn->query('SELECT * FROM `Curriculum`');
			}

			else {
				// Or else, get curriculums by DeptID.
				$stmt = self::$conn->prepare(
					'SELECT `CurriculumID`, `Title`
					 FROM `Curriculum`
					 WHERE DepartmentID = :deptID');

				$stmt->bindParam( ':deptID', $deptID, PDO::PARAM_STR );
				$stmt->execute();
			}

			$this->currList = $stmt->fetchAll(PDO::FETCH_OBJ);
			return;
		}
	}
?>