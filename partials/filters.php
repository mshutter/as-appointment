<?php
/* Dependencies:
	jQuery, jQuery-ui, appt.js
*/

// ========== Get list of departments ========== //
require_once 'models/Department.php';
$deptList = Department::ListAllDepartments();

// ========== Get faculty/staff appointments ========== //
require_once 'models/AppointmentType.php';
//$facStaffAppts = AppointmentType::ListAppointmentTypes(true);
?>
	
	<!-- input: campus tour -->
	<label id="btn-campusTour" class="btn btn-primary">
		<input id='input-campusTour' type="checkbox" name="apptTypeID[]" value="1" />
		Campus Tour
	</label>
	<div class='row'></div>

	<!-- input: admissions meeting -->
	<label id="btn-admissions" class='btn btn-primary'>
		<input id='input-admissions' type="checkbox" name='apptTypeID[]' value='3'>
		Admissions Meeting
	</label>
	<div class="row"></div>

	<!-- input: financial aid -->
	<label id="btn-financialAid" class='btn btn-primary'>
		<input id='input-financialAid' type="checkbox" name='apptTypeID[]' value='4'>
		Financial Aid
	</label>
	<div class="row"></div>

	<input id="input-date" type="text" name="date" value="" />
	
	<!-- input: visit date
	<label id="btn-date" class='btn btn-primary col-xs-12'>
		<input id='input-date' class='hidden' type="text" name="date" value="" />
		<i class="glyphicon glyphicon-calendar"></i>
		<span data-role="display-value">Approximate Visit Date</span>
	</label>
	<div class="row-space"></div>
	-->

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
	
	<div>
		Datepicker spot
		<div id='datepicker'></div>
	</div>


	<?php
		//NOTE:
		//#dept-list will be moved to the bottom of the body tag.
		//See appt.js->initDialog() ?>
	
	<div class="debug-display">
		<!-- To display JSON results from getMonthOfApptTypes -->
	</div>

	<div id="dept-list" hidden>
		<div class="appt-dialog-header">
			<h3>Choose a Department</h3>
			<i class="glyphicon glyphicon-remove appt-dialog-close"></i>
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

	<script>
		var dpMonthDate = (new Date()).getFullYear()+'-'+((new Date()).getMonth() + 1)+'-'+'1';
		var thisMonthOfApptTypes = [];

		function getMonthOfApptTypes () {
			//updates global variable 'thisMonthOfApptTypes' to match currently displayed
			//datepicker month, and user-selected ApptTypeID filters

			//build url
			var url = "api/monthOfAppts.php?d="+dpMonthDate;

			var checkedFilters = $('input[name="apptTypeID[]"]:checked');

			//if any filters are checked, add them to the url
			if ( checkedFilters.length ) {
				checkedFilters.each(function () {
					url += '&apptTypeID[]='+this.value;
					console.log(this);
				});
			}

			//if no filters are checked, send an empty array as apptTypeID[]'s' value
			else {
				url += '&apptTypeID[]=';
			}

			/*

			LEFT OFF HERE

			*/

			console.log(url)

 			$.ajax({
				url: url,
				method: "get"
			})
			.done(function (data) {
				console.log(data);
			});

		}

		function createTestBubble (month, date, year) {
			var context = $('td[:contains("24")');
			console.log(context[0]);
			context.css('background-color', 'red');
			return false;

			//html elements (each day: table-cell):
			//td[data-handler="selectDay"][data-month="0-11"][data-year="2016"]
			//  <a>1-30</a> //day is content

			//json element for filters:
			//arr name: 
			//  arr name: dd-mm-yyyy
			//    vals: apptTypes (+3_curr)


		}
	</script>

	<hr />