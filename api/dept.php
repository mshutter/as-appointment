<?php

if ( isset( $_GET['deptID'] ) ) {

	$deptID = $_GET['deptID'];

	require_once '../models/Department.php';
	$dept = Department::GetByDepartmentID( $deptID );
	$dept->GetCurriculums();

	echo json_encode($dept);
}


?>