<?php
	class Student {

		// ** Variables **
	
		private $UID,

		$lastName,
		$firstName,
		$middleInitial,

		$email,

		// The following need accessors:
		$streetAddress,
		$city,
		$state,
		$zip,

		$highSchool,
		$contactPerson, # Not sure
		$srquik;        # Not sure



		// ** Accessors ( getVariable() // setVariable($value) )**

		// UID - READ ONLY
		public function getUID() {			
			return $this->UID; }

		// Last Name
		public function getLastName() {
			return $this->lastName; }
		public function setLastName($value) {
			$this->lastName = $value; }

		// First Name
		public function getFirstName() {
			return $this->firstName; }
		public function setFirstName($value) {
			$this->firstName = $value; }

		// Middle Initial
		public function getMiddleInitial() {
			return $this->middleInitial; }
		public function setMiddleInitial($value) {
			$this->middleInitial = $value; }

		// Email
		public function getEmail() {
			return $this->email; }
		public function setEmail($value) {
			$this->email = $value; }



		// ** Constructors **

		public function __construct ($UID, $email) {
			$this->UID = $UID;
			$this->setEmail($email);
		}

		public function getFullName () {
			return $this->getFirstName() . " " . $this->getMiddleInitial() . ". " . $this->getLastName();
		}
	}
?>