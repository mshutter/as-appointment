<?php session_start();

echo "<h3>\$_REQUEST</h3>";
var_dump($_REQUEST);
echo "<hr />";

//1. create student
	require_once "../models/Student.php";

	//param array to create student object
	$studentParams = [];

	//if firstName and lastName are posted and are not whitespace
	if ( isset($_POST['firstName']) && isset($_POST['lastName'])
	&& trim($_POST['firstName']) != "" && trim($_POST['lastName']) != "" ) {

		//add name to params
		$studentParams['FirstName']     = $_POST['firstName']; //*
		$studentParams['MiddleInitial'] = $_POST['middleInitial'];
		$studentParams['LastName']      = $_POST['lastName']; //*	
	}


	//if email is posted and is a valid email address
	if ( isset( $_POST['email'] ) && filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {

		//add email to params
		$studentParams['Email'] = $_POST['email']; //*
	}


	//if addressLine1, city, state, and zip are posted and are not whitespace
	if ( isset( $_POST['addressLine1'] ) && trim($_POST['addressLine1']) != ""
	&&   isset( $_POST['city'] )         && trim($_POST['city']) != ""
	&&   isset( $_POST['state'] )        && trim($_POST['state']) != ""
	&&   isset( $_POST['zip'] )          && trim($_POST['zip']) != "") {

		//add mailing address to params
		$studentParams['AddressLine1'] = $_POST['addressLine1']; //*
		$studentParams['AddressLine2'] = $_POST['addressLine2'];
		$studentParams['City']         = $_POST['city']; //*
		$studentParams['State']        = $_POST['state']; //*
		$studentParams['Zip']          = $_POST['zip']; //*
	}


	//if primaryPhone is posted and is not whitespace
	if ( isset( $_POST['primaryPhone'] ) && trim($_POST['primaryPhone']) != "" ) {

		//add phone information to params
		$studentParams['PrimaryPhone'] = $_POST['primaryPhone']; //*
		(isset($_POST['primaryIsMobile'])) ? $studentParams['PrimaryIsMobile'] = 1 : null;
		$studentParams['SecondaryPhone'] = $_POST['secondaryPhone'];
		(isset($_POST['secondaryIsMobile'])) ? $studentParams['SecondaryIsMobile'] = 1 : null;
	}


	//add highSchool, gradYear and birthDate if they are set
	$studentParams['HighSchool'] = (isset($_POST['highSchool'])) ? $_POST['highSchool'] : "";
	$studentParams['GradYear']   = (isset($_POST['gradYear']))   ? $_POST['gradYear']   : "";
	$studentParams['BirthDate']  = (isset($_POST['birthDate']))  ? $_POST['birthDate']  : "";


	echo "<h3>\$studentParams</h3>";
	var_dump($studentParams);
	echo "<hr />";


	//student object
	$student = Student::NewStudent($studentParams);

	echo "<h3>\$student</h3>";
	var_dump($student);
	echo "<hr />";



//2. create student agenda
	require_once "../models/StudentAgenda.php";

	//param aray to create StudentAgenda object
	$agendaParams = [];
	if ( isset($_POST['numGuests']) && is_numeric($_POST['numGuests']) ) {
		$agendaParams['NumGuests'] = $_POST['numGuests'];
	} else $agendaParams['NumGuests'] = "";
	$agendaParams['StudentID'] = $student->studentID;

	echo "<h3>\$agendaParams</h3>";
	var_dump($agendaParams);
	echo "<hr />";

	$studentAgenda = StudentAgenda::NewStudentAgenda($agendaParams);

	echo "<h3>\$studentAgenda</h3>";
	var_dump($studentAgenda);
	echo "<hr />";



//3. create student agenda items
	require_once "../models/StudentAgendaItem.php";

	//timestamp
	date_default_timezone_set("America/New_York");
	$timestamp = date('Y-m-d H:i:s');

	//list of agenda items
	$studentAgendaItems = [];

	//common params to create agenda items
	$agendaItemParams = [];
	$agendaItemParams['AgendaID'] = $studentAgenda->agendaID;
	$agendaItemParams['RegistrationTime'] = $timestamp;
	$agendaItemParams['Cancelled'] = 0;

	foreach ($_POST['schedApptID'] as $schedApptID) {
		
		//change schedApptID and create a new agendaItem
		$agendaItemParams['SchedApptID'] = $schedApptID;
		array_push( $studentAgendaItems, StudentAgendaItem::NewAgendaItem($agendaItemParams) );
	}


	echo "<h3>\$studentAgendaItems</h3>";
	var_dump($studentAgendaItems);
	echo "<hr />";



//4. push everything to the DB

	//push student

	//push agenda

	//push agenda items


?>