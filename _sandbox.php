<?php
//Prototype for the prototype for the index page

// require_once 'models/AppointmentType.php';
// $facStaffAppts = AppointmentType::ListAppointmentTypes(true);

// require_once 'models/Department.php';
// $dept = Department::GetByDepartmentID(0);
// $dept->GetCurriculums();
?>

<script src="http://code.jquery.com/jquery-1.4.2.min.js"></script>

<!-- first range -->
<input id='x1' type="text" name="x1" />
<input id='x2' type="text" name="x2" />
<br />

<!-- second range -->
<input id='y1' type="text" name="y1" />
<input id='y2' type="text" name="y2" />

<button onclick="return checkDates()">Check</button>
<div id="display"></div>

<script>
	function checkDates (x1, x2, y1, y2) {
		//change time strings into comparable date objects
		x1 = new Date('1/1/2011 ' + x1);
		x2 = new Date('1/1/2011 ' + x2);
		y1 = new Date('1/1/2011 ' + y1);
		y2 = new Date('1/1/2011 ' + y2);

		console.log(x1, x2, y1, y2);

		//return false if there is a conflict
		return !(x1 < y2 && y1 < x2);
	}
</script>