<?php
	// Pre-header (DOCTYPE, open html/head, meta, etc..)
	include 'partials/preHeader.php';

	// To get list of all departments ($dept->deptList)
	include 'models/Department.php';
	$dept = new Department();

	// To get list of faculty/staff appt types ($apptType->apptTypeList)
	include 'models/ApptType.php';
	$apptType = new ApptType(1);
?>


<!-- Page-specific recouces -->
<link rel="stylesheet" type="text/css" href="css/pikaday.css" />


<?php
	// Append page title
	$title = "Visit";

	// Header (title, close head, open body/div.container, div.header)
	include 'partials/header.php';
?>


<h2>Pay Us a Visit</h2>

<form id='visit' action="#" method="GET">
	<div id='error-display' class="form-group">
		<!-- Validation errors will be displayed here -->
	</div>

	<!-- Select type of tour -->
	<div class='form-group'>
		<input id='rad-tour' type="radio" name='generalApptType' value='2'>
		<label for='rad-tour'>Tour Our Campus</label>
		<br />

		<input id='rad-curriculum' type="radio" name='generalApptType' value='3'>
		<label for='rad-curriculum'>Explore a Program</label>
		<br />

		<input id='rad-faculty' type="radio" name='generalApptType' value='-2'>
		<label for='rad-faculty'>Meet With Faculty/Staff</label>
		<br />
	</div>


	<!-- pikadate.js approximate date selection -->
	<div class='form-group' id='date-dropdown' hidden>
		<input id='select-date' type='text' name='date-pref' value='Approximate visit date' />
	</div>


	<!-- Form elements visible when curriculum visit is selected -->
	<div id='form-curriculum' hidden>
		<!-- Drop-down selection list of departments -->
		<div class="form-group" id="dept-dropdown">
			<select name="department" onchange="getCurrSelect(this.value)">

				<option value='-1'>
					Department (select one)
				</option>
				
				<!-- Generates option for each department -->
				<?php foreach ($dept->deptList as $d) {
					echo "<option value='$d->DepartmentID'>$d->Title</option>";
				} ?>

			</select>
		</div>
		
		<!-- Drop-down selection list of curriculums (majors) -->
		<div class='form-group' id='curr-dropdown'>
			<!-- Populated via getCurrSelect()	with AJAX on department selection -->
		</div>
	</div>


	<!-- Form elements visible when faculty/staff visit is selected -->
	<div id='form-facStaff' hidden>
		<div class='form-group' id='fac-dropdown'>
			<select id='sel-facStaffApptType' name="facStaffApptType">

				<?php foreach ($apptType->apptTypeList as $t)
					// Generates option for each faculty/staff appointment type
					echo "<option value='$t->TypeID'>$t->Title</option>";
				?>

			</select>
		</div>
	</div>

	<?php
		/** NOTE:
		 * #hid-apptType will hold the value of apptType (TypeID) that will end up ultimately making
		 * it into the database. Its value is based on form.generalApptType (radio buttons) unless
		 * the user selects faculty/staff meeting as their general type, in which case apptType will
		 * be set to equal the apptTypevalue of the specific type of staff/faculty meeting. The logic
		 * controlling this functionality can be found in the inline javascript below.
		 */
	?>
	<input id='hid-apptType' type="hidden" name='apptType' value='-1' />

	<div id='form-submit' class='form-group' hidden>
		<input type="submit" value='Next'>
	</div>
</form>


<!-- Calendar script -->
<script src='js/pikaday.js'></script>
<script>
	window.onload = function () {

		// Bind dynamic form changes and hidden apptType form value assignment to related elements
		$("#rad-faculty, #rad-curriculum, #rad-tour, #sel-facStaffApptType").change(function () {

			// Pass value of selected element to function that visually configures form accordingly
			adaptFormToSelection( $(this).val() );

			// If faculty/staff meeting is selected, apptType should be set equal to the value of the
			// specific type of faculty/staff meeting.
			if ( $(this).val() == -2 ) {
				$('#hid-apptType').val( $('#sel-facStaffApptType').val() )
			}

			// If Campus Tour or Curriculum Tour are selected, set value of apptType accordingly
			else {
				$('#hid-apptType').val( $(this).val() );
			}

			// DEBUGGING:
			//console.log( $('#hid-apptType').val() );
		});
	}



	// Create pikaday calendar UI and attach it to #select-date field
	var picker = new Pikaday({
		field: document.getElementById('select-date'),
		position: "bottom left"
	});



	// Generate select+options for curriculums in selected department
	function getCurrSelect (value) {
		// Call actions/getCurrJSON.php with @q = value
		$.ajax({
			url: 'actions/getCurrJSON.php?q=' + value,
			context: $('#curr-dropdown')
		})
		.success(function (data) {
			// Parse JSON and create HTML select element string
			var currList = JSON.parse(data);
			var currSelect = '<select name="curriculum" id="">';
			    currSelect += '<option value="">Undecided</option>';

			// Add each curriculum option item to select HTML
			for (var i = 0; i < currList.length; i++) {
				currSelect += '<option value="' + currList[i].CurriculumID + '">';
				currSelect += currList[i].Title;
				currSelect += '</option>';
			}

			// Close select element
			currSelect += '</select>'

			// Populate #curr-dropdown with curriculum select+options
			$(this).html(currSelect);
		});

		return false;
	}



	// Handles the elements of this form that change depending on user selection
	function adaptFormToSelection (value) {

		// When a selection is made, make date picker visible
		$('#date-dropdown, #form-submit').show();

		// Toggle form elements to match current generalApptType selection 
		switch (value) {
			case '2':
				$('#form-facStaff, #form-curriculum').hide();
				break;

			case '3':
				$('#form-facStaff').hide();
				$('#form-curriculum').show();
				break;

			case '-2':
				$('#form-curriculum').hide();
				$('#form-facStaff').show();
				break;

			default:
				break;
		}
	}
</script>


<?php
	include 'partials/footer.php';
?>