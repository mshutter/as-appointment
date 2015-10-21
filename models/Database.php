<?php
	class Database {
		private static $host = 'localhost';
		private static $port = '3306';
		private static $user = 'root';
		private static $pass = 'root';
		private static $name = 'appointment';
		private static $conn;

		// No code may instantiate this class
		private function __construct(){}

		// Sets self::$conn to PDO connection object
		public static function Connect () {
			if ( !isset( self::$conn ) ) {
				try {
					// Create connection string
					$connString = "mysql:host=" .
												self::$host .
												";dbname=" .
												self::$name . ";";
					
					// If a port is specified, append it to connection string
					if (self::$port) {
						$connString .= "port=" . self::$port . ";";
					}

					// Create connection object
					self::$conn = new PDO($connString, self::$user, self::$pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

					// Debug: Successful!
					//echo "Connection successful!";

				} catch(PDOException $e) {

					// Debug: Error!
				  //echo 'ERROR: ' . $e->getMessage();
				  exit();
				}
			}

			return self::$conn;
		}
	}
?>