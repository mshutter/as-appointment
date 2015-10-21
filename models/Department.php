<?php
	require_once 'Database.php';

	class Department {
		private static $conn;
		public $deptList;

		public function __construct () {
			self::$conn = Database::Connect();
			$this->GetDepartments();
		}

		// Retrieve list of departments from database
		private function GetDepartments () {
			$stmt = self::$conn->query('SELECT * FROM `Department`');
			$this->deptList = $stmt->fetchAll(PDO::FETCH_OBJ);
			return;
		}

	}
?>