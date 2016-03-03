<?php
//Prototype for the prototype for the index page

// require_once 'models/AppointmentType.php';
// $facStaffAppts = AppointmentType::ListAppointmentTypes(true);

// require_once 'models/Department.php';
// $dept = Department::GetByDepartmentID(0);
// $dept->GetCurriculums();
?>


<script src="http://code.jquery.com/jquery-1.4.2.min.js"></script>

<script>
	$(document).ready(function () {
		var arr = [];
		console.log(arr);
	});
</script>
<div class="target"></div>

<div id="header">
	<h1>DVD Collection</h1>k
</div>

<input type="text" value='filter'>

<ul id="curriculums">
  <li><a data-keywords="">Agriculture Business</a></li>
  <li><a data-keywords="horticulture dairy science animal science">Agriculture Technology</a></li>
  <li><a data-keywords="architecture">Architectural Technology</a></li>
  <li><a data-keywords="">Interior Design</a></li>
  <li><a data-keywords="">Automotive Repair</a></li>
<ul>


<?php

/**
 * GENERATE UID FUNCTION
 */

function UniqueID () {
	//String containing all characters that may be used in unique ID
	$chars = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

	//First character must be alphabetical
	$uid = substr( $chars, mt_rand(0,51), 1 );

	//Shuffle numbers into $chars
	$chars = str_shuffle($chars . '0123456789');

	//Add 9 random alphanumeric characters to $uid
	for ($i = 0; $i < 9; $i++) {
		$x = mt_rand(0,61);
		$uid .= substr( $chars, $x, 1 );
	}

	return $uid;
}

?>