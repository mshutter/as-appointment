<?php
/* Dependencies:
	jQuery, jQuery-ui, appt.js
*/

// ========== Get list of departments ========== //
require_once 'models/Curriculum.php';
$curriculums = Curriculum::ListAllCurriculums();

// ========== Get faculty/staff appointments ========== //
require_once 'models/AppointmentType.php';
//$facStaffAppts = AppointmentType::ListAppointmentTypes(true);
?>

<div class="appt-filters-container">
	<p>
		Select which activities may be of interest to you during your visit:	
	</p>

	<!-- input: campus tour -->
	<label id="btn-campusTour" class="btn ui-1 col-xs-12">
		<input id='input-campusTour' type="checkbox" name="apptTypeID[]" value="1" />
		Tour our Campus
	</label>
	<div class='row-space'></div>

	<!-- input: admissions meeting -->
	<label id="btn-admissions" class='btn ui-3 col-xs-12'>
		<input id='input-admissions' type="checkbox" name='apptTypeID[]' value='3'>
		Meet with Admissions
	</label>
	<div class="row-space"></div>

	<!-- input: financial aid -->
	<label id="btn-financialAid" class='btn ui-4 col-xs-12'>
		<input id='input-financialAid' type="checkbox" name='apptTypeID[]' value='4'>
		Meet with Financial Aid
	</label>
	<div class="row"></div>
	<hr />

	
	<!-- input: department tour -->
	<p>
		Select from our list of programs, any that appeal to you:
	</p>
	
	<label id="btn-deptartmentTour" class="btn ui-2 col-xs-12" data-role='dropdown-toggle' data-dropdown="dropdown-deptartmentTours">	
			Explore a Program
			<i class="glyphicon glyphicon-menu-down"></i>
		</label>
		<div class="row"></div>

		<div id="dropdown-deptartmentTours" data-role='dropdown-container' style="max-height:300px;overflow-y:scroll;">
			<?php foreach ( $curriculums as $curr ) : ?>		

			<label class="appt-dropdown-item">
				<input id="option-deptTour-<?php echo $curr->curriculumID; ?>" type="checkbox" name="test[]" value="<?php echo $curr->curriculumID; ?>" />
				<?php echo $curr->title; ?>
				<span style="display:block"></span>
			</label>

			<?php endforeach; ?>
		</div>
		<div class="row"></div>



	<!-- <label id="btn-financialAid" class='btn ui-2 col-xs-12'>
		<input id='input-departmentTour' type="checkbox" name='apptTypeID[]' value='2'>
		Explore a Program
	</label>
	<div class="row"></div> -->




	<!-- <a id="add-dept" href="#">
		<i class="glyphicon glyphicon-plus"></i>
		Explore a Program
	</a> -->

	<hr />
	
	<p>
		Select when, approximately you will be available for a visit:
	</p>
	<input id="input-date" class='hidden' type="text" name="date" value="" />
	<div id='datepicker'></div>


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
		//dpMonthDate is a string representing a date that falls within the month currently being displayed by datepicker.
		//	It is initially assigned to the first of today's month, and changes each time a new DP month is selected.
		var dpMonthDate = (new Date()).getFullYear()+'-'+((new Date()).getMonth() + 1)+'-'+'1';
		var thisMonthOfApptTypes = {};

		function getMonthOfApptTypes () {
			//updates global variable 'thisMonthOfApptTypes' to match currently displayed
			//datepicker month, and user-selected ApptTypeID filters

			//build url
			var url = "api/monthOfAppts.php?d="+dpMonthDate;

			//get jQuery DOM handle for each checked input
			var checkedFilters = $('input[name="apptTypeID[]"]:checked');

			//if any filters are checked, add them to the url
			if ( checkedFilters.length ) {
				checkedFilters.each(function () {
					url += '&apptTypeID[]='+this.value;
				});
			}

			//if no filters are checked, send an empty array as apptTypeID[]'s' value
			else {
				url += '&apptTypeID[]=';
			}

			//call api/monthOfAppts.php
 			$.ajax({
				url: url,
				method: "get"
			})
			.done(updateCalendarIU);
		}

		function updateCalendarIU (json) {
			//accepts json of currently displayed month of apptTypes
			//uses json to display apptTypes available on each day

			//clear previous ui
			$('td[data-handler="selectDay"]').children("span").remove();

			thisMonthOfApptTypes = JSON.parse(json);
			var tcells = []; //context of ui. table cells that can be targeted
			var idate  = ""; //loop index holding string of date
			var uihtml = ""; //string holding html for ui elements

			var numOfDaysWithAppts = Object.keys(thisMonthOfApptTypes).length;

			//populate tcells[] with jQuery DOM handles for each selectable datepicker table cell
			$('td[data-handler]').each(function (i, e) {
				tcells[$(e).children('a').first().html()] = $(e);
			});

			//for each day
			for (var i = 0; i < numOfDaysWithAppts; i++) {

				//get this date
				idatestring = Object.keys(thisMonthOfApptTypes)[i];

				//parse date string to target element
				idate = new Date(idatestring+"T12:00:00");

				//build ui item html
				uihtml = '<span><br /></span>';
				thisMonthOfApptTypes[idatestring].forEach(function (e, i, arr) {
					uihtml += '<span class="ui-dot ui-'+e+'"></span>'
				});

				//add ui html to target element
				if ( tcells[idate.getDate()] ) {
					tcells[idate.getDate()].append(uihtml);
				}
			}
		}
	</script>
	<hr />
</div>