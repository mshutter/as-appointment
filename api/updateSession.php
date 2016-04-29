<?php
//update session variables if they are present in $_REQUEST
//!IMPORTANT: requires the use of session_start() before including

//apptTypeID
if ( isset( $_REQUEST['apptTypeID'] ) )
	$_SESSION['apptTypeID'] = array_unique( $_REQUEST['apptTypeID'] );

//date
if ( isset( $_REQUEST['date'] ) )
	$_SESSION['date'] = strtotime( $_REQUEST['date'] );
if ( !isset( $_SESSION['date'] ) )
	$_SESSION['date'] = time();

//departmentID
if ( isset( $_REQUEST['departmentID'] ) ) 
	$_SESSION['departmentID'] = $_REQUEST['departmentID'];

//curriculumID
if ( isset( $_REQUEST['curriculumID'] ) )
	$_SESSION['curriculumID'] = $_REQUEST['curriculumID'];

?>