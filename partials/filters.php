<?php
/* Dependencies:
	jQuery, jQuery-ui, appt.js
*/

// ========== Get list of departments ========== //
require_once 'models/Department.php';
$deptList = Department::ListAllDepartments();

// ========== Get faculty/staff appointments ========== //
require_once 'models/AppointmentType.php';
$facStaffAppts = AppointmentType::ListAppointmentTypes(true);
?>

<!--
	Static Filters
-->

	<!--
		Input: Date -->

		<label id="btn-date" class='btn btn-primary col-xs-12'>
			<input id='input-date' class='hidden' type="text" name="date" value="" />
			<i class="glyphicon glyphicon-calendar"></i>
			<span data-role="display-value">Approximate Visit Date</span>
		</label>
		<div class="row-space"></div>


	<!--
		Input: Campus Tour -->

		<label id="btn-campusTour" class="btn btn-primary col-xs-12">
			<input class="hidden" id='input-campusTour' type="checkbox" name="apptType[]" value="2" />
			Tour Our Campus
		</label>
		<div class='row-space'></div>


	<!--
		Input: Faculty/Staff -->

		<label id="btn-facStaffAppt" class="btn btn-primary col-xs-12" data-role='dropdown-toggle' data-dropdown="dropdown-facStaffAppts">	
			Meet With Faculty/Staff
			<i class="glyphicon glyphicon-menu-down"></i>
		</label>
		<div class="row"></div>

		<div id="dropdown-facStaffAppts" data-role='dropdown-container'>
			<?php foreach ( $facStaffAppts as $type ) : ?>		

			<label class="btn btn-default appt-dropdown-item">
				<input id="option-facStaff-<?php echo $type->apptTypeID; ?>" type="checkbox" name="apptType[]" value="<?php echo $type->apptTypeID; ?>" />
				<?php echo $type->title; ?>
			</label>

			<?php endforeach; ?>
		</div>
		<div class="row"></div>

	<hr />



<!--
	Dynamic Filters
-->
	<div id="dept-filters">
		<!-- AJAX context for department filters -->
	</div>

	<a id="add-dept" href="#">
		<i class="glyphicon glyphicon-plus"></i>
		Explore a Program
	</a>
	
	<?php
		//NOTE:
		//#dept-list will be moved to the bottom of the body tag.
		//See appt.js->initDialog() ?>

	<div id="dept-list" hidden>
		<div class="appt-dialog-header">
			<h3>Choose a Department</h3>
			<i class="glyphicon glyphicon-remove"></i>
		</div>

		<div class="appt-dialog-content">
			<?php foreach ( $deptList as $dept ) : ?>
			
			<label class="select-dept col-xs-12" data-dept="<?php echo $dept->departmentID; ?>">
				<i class="glyphicon glyphicon-plus"></i>
				<?php echo $dept->title; ?>
			</label>
			<br />

			<?php endforeach; ?>	
		</div>
	</div>

	<hr />