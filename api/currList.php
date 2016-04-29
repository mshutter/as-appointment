<?php
	// Returns a JSON array containing information about the
	// curriculums Alfred State offers. If variable @q is passed, the
	// curriculums will be filtered by DepartmentID == @q

	require_once '../models/Curriculum.php';

	if ( isset( $_REQUEST['q'] ) ) {
		$q = $_REQUEST['q'];
		$curr = new Curriculum($q);
	}
	else {
		$curr = new Curriculum();
	}

	echo json_encode($curr->currList);
?>