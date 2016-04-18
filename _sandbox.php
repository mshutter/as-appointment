<?php
//Prototype for the prototype for the index page

// require_once 'models/AppointmentType.php';
// $facStaffAppts = AppointmentType::ListAppointmentTypes(true);

// require_once 'models/Department.php';
// $dept = Department::GetByDepartmentID(0);
// $dept->GetCurriculums();
?>

<script src="http://code.jquery.com/jquery-1.4.2.min.js"></script>

<h1>Testing Validation Rules:</h1>
<hr />

<h3>Numeric</h3>
<p>
	0: <?php echo (is_numeric("0"))?"valid":"invalid"; ?>
</p>
<p>
	1: <?php echo (is_numeric("1"))?"valid":"invalid"; ?>
</p>
<p>
	12: <?php echo (is_numeric("12"))?"valid":"invalid"; ?>
</p>
<p>
	f: <?php echo (is_numeric("f"))?"valid":"invalid"; ?>
</p>
<p>
	null: <?php echo (is_numeric(""))?"valid":"invalid"; ?>
</p>
<p>
	0: <?php echo (is_numeric(3))?"valid":"invalid"; ?>
</p>