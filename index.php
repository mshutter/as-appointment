<?php

// ========== Controller ========== //

//Get Faculty/Staff appointment types
require_once 'models/AppointmentType.php';
$apptTypes = AppointmentType::ListAppointmentTypes(1);



// ========== View ========== //

// Pre-header (DOCTYPE, open html/head, meta, etc..)
include 'partials/preHeader.php';
?>

<link rel="stylesheet" type="text/css" href="styles/filters.css" />

<?php
// Append page title
$title = "Visit";

// Header (title, close head, open body/div.container, div.header)
include 'partials/header.php';
?>

<h2>Pay Us a Visit</h2>

<form class="appt-filters" action="step-two.php" method="POST">

	<div id='error-display'>
		<!-- Validation errors will be displayed here -->
	</div>


<!-- Input: Date -->
	<div class="form-group">
		<input class="btn btn-block btn-default" id='date' type='text' name='date' value='Approximate visit date' />	
	</div>


<!-- Input: Campus tour -->
	<div class="form-group">
		<label class="btn btn-block btn-default">
			<input type='checkbox' name="apptTypes" value="2" />
			<span>Tour Our Campus</span>
		</label>
	</div>
	

<!-- Input: Faculty Appointment -->
	<div class="form-group col-xs-12">
		<div class="btn-group btn-group-justified" >
			<div class="btn-group" style="width:20%;">
				<label class="btn btn-default">
					<input type="checkbox" name="includeFacStaff" />	
				</label>	
			</div>
			

			<label class="btn btn-default">
				<div class="btn btn-default">
					Meet With Faculty/Staff
					<i class="glyphicon glyphicon-menu-down"></i>
				</div>			
			</label>
		</div>

		<ul class="dropdown" id="fac-dropdown">
		<?php foreach ( $apptTypes as $type ) : ?>
			<li>
				<label class="btn btn-primary">
					<input type="checkbox" name="apptTypes" value="<?php echo $type->apptTypeID ?>" />
					<?php echo $type->title; ?>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
	

<!-- Input: Department Tour -->
	<div class="form-group">
		<a href="#">+ Add Department of Interest</a>
	</div>
	

<!-- Submit -->
	<input type="submit" value='Get Started' />

</form>


<script>
	window.onload = function () {
		$('#date').datepicker();
	}
</script>


<?php
	include 'partials/footer.php';
?>